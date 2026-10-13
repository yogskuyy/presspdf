<?php

namespace App\Services;

use App\Models\CompressionJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class PdfCompressionService
{
    /**
     * Minimum file size to process (10 MB in bytes).
     */
    const MIN_SIZE_BYTES = 10 * 1024 * 1024; // 10 MB

    /**
     * Max concurrent Ghostscript processes (parallel compression).
     * Tune this based on server CPU cores (recommended: cores - 1).
     */
    const MAX_PARALLEL = 4;

    /**
     * Ghostscript compression settings per quality level.
     */
    const GS_SETTINGS = [
        'screen'  => '/screen',   // ~72 dpi - smallest size
        'ebook'   => '/ebook',    // ~150 dpi - good balance
        'printer' => '/printer',  // ~300 dpi - high quality
        'prepress'=> '/prepress', // ~300 dpi + color preservation
    ];

    /**
     * Detect available compression engine.
     */
    public function detectEngine(): string
    {
        return $this->findGhostscript() ? 'ghostscript' : 'fpdi';
    }

    /**
     * Find Ghostscript executable path (cached per request).
     */
    private ?string $cachedGsPath = null;

    public function findGhostscript(): ?string
    {
        if ($this->cachedGsPath !== null) {
            return $this->cachedGsPath === '' ? null : $this->cachedGsPath;
        }

        $candidates = [
            'gswin64c',
            'gswin32c',
            'gs',
            '/usr/bin/gs',
            '/usr/local/bin/gs',
        ];

        // Auto-discover from Program Files
        foreach (['C:\\Program Files\\gs', 'C:\\Program Files (x86)\\gs'] as $dir) {
            if (is_dir($dir)) {
                foreach (glob($dir . '\\*\\bin\\gswin*.exe') ?: [] as $exe) {
                    array_unshift($candidates, $exe);
                }
            }
        }

        foreach ($candidates as $candidate) {
            try {
                $p = new Process([$candidate, '--version']);
                $p->setTimeout(5);
                $p->run();
                if ($p->isSuccessful()) {
                    $this->cachedGsPath = $candidate;
                    return $candidate;
                }
            } catch (\Exception) {
                continue;
            }
        }

        $this->cachedGsPath = '';
        return null;
    }

    // =========================================================
    //  SINGLE FILE COMPRESSION
    // =========================================================

    /**
     * Compress a single PDF file (synchronous).
     */
    public function compress(CompressionJob $job, string $quality = 'ebook'): bool
    {
        try {
            $job->update(['status' => 'processing']);

            $inputPath  = Storage::disk('public')->path($job->original_path);
            $outputRel  = $this->resolveOutputPath($job);
            $outputPath = Storage::disk('public')->path($outputRel);

            Storage::disk('public')->makeDirectory(dirname($outputRel));

            $gsPath = $this->findGhostscript();
            $success = $gsPath
                ? $this->compressWithGhostscript($inputPath, $outputPath, $quality)
                : $this->compressWithFpdi($inputPath, $outputPath);

            return $this->finalizeJob($job, $outputRel, $outputPath, $success);

        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error_message' => substr($e->getMessage(), 0, 250)]);
            Log::error("Compress failed job#{$job->id}: " . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    //  PARALLEL BATCH COMPRESSION  ← KEY OPTIMIZATION
    // =========================================================

    /**
     * Compress multiple jobs in PARALLEL using async Symfony Process.
     * Multiple Ghostscript instances run simultaneously — dramatically
     * faster than sequential processing for batch uploads.
     *
     * @param  int[]    $jobIds
     * @param  string   $quality
     * @param  callable $onProgress  fn(CompressionJob $job) — called after each file finishes
     * @return array    ['success' => int, 'failed' => int, 'jobs' => CompressionJob[]]
     */
    public function compressBatchParallel(
        array $jobIds,
        string $quality = 'ebook',
        ?callable $onProgress = null
    ): array {
        $results = ['success' => 0, 'failed' => 0, 'jobs' => []];
        $jobs    = CompressionJob::whereIn('id', $jobIds)->get();
        $gsPath  = $this->findGhostscript();

        if (!$gsPath) {
            // FPDI is not parallelizable (PHP single-threaded), run sequentially
            foreach ($jobs as $job) {
                $ok = $this->compress($job, $quality);
                $ok ? $results['success']++ : $results['failed']++;
                $results['jobs'][] = $job->fresh();
                if ($onProgress) ($onProgress)($job->fresh());
            }
            return $results;
        }

        // ── PARALLEL GHOSTSCRIPT ──────────────────────────────────────────
        // Split into chunks of MAX_PARALLEL, run each chunk concurrently.
        $chunks  = $jobs->chunk(self::MAX_PARALLEL);

        foreach ($chunks as $chunk) {
            /** @var array<int, array{job: CompressionJob, process: Process, outputRel: string, outputPath: string}> $running */
            $running = [];

            // Launch all processes in this chunk simultaneously
            foreach ($chunk as $job) {
                $job->update(['status' => 'processing']);

                $inputPath  = Storage::disk('public')->path($job->original_path);
                $outputRel  = $this->resolveOutputPath($job);
                $outputPath = Storage::disk('public')->path($outputRel);

                Storage::disk('public')->makeDirectory(dirname($outputRel));

                $setting = self::GS_SETTINGS[$quality] ?? '/ebook';
                $env = $this->getGsEnv();

                $process = new Process([
                    $gsPath,
                    '-sDEVICE=pdfwrite',
                    '-dCompatibilityLevel=1.4',
                    "-dPDFSETTINGS={$setting}",
                    '-dNOPAUSE',
                    '-dQUIET',
                    '-dBATCH',
                    '-dDetectDuplicateImages=true',
                    '-dCompressFonts=true',
                    '-dSubsetFonts=true',
                    '-r72',             // Speed-up: limit rasterization resolution
                    "-sOutputFile={$outputPath}",
                    $inputPath,
                ], null, $env);
                $process->setTimeout(600);

                // 🚀 START asynchronously (non-blocking)
                $process->start();

                $running[] = [
                    'job'        => $job,
                    'process'    => $process,
                    'outputRel'  => $outputRel,
                    'outputPath' => $outputPath,
                ];
            }

            // Poll until ALL processes in this chunk are done
            while (!empty($running)) {
                foreach ($running as $idx => $item) {
                    if ($item['process']->isRunning()) {
                        continue; // Still going
                    }

                    // Process finished — finalize
                    $job = $item['job'];
                    $ok  = $item['process']->isSuccessful();

                    $success = $this->finalizeJob(
                        $job,
                        $item['outputRel'],
                        $item['outputPath'],
                        $ok
                    );

                    if ($success) $results['success']++;
                    else          $results['failed']++;

                    $results['jobs'][] = $job->fresh();

                    if ($onProgress) ($onProgress)($job->fresh());

                    unset($running[$idx]);
                }
                if (!empty($running)) {
                    usleep(50_000); // 50ms poll interval
                }
            }
        }

        return $results;
    }

    /**
     * Backward-compatible alias (used by controller).
     */
    public function compressBatch(
        array $jobIds,
        string $quality = 'ebook',
        ?callable $onProgress = null
    ): array {
        return $this->compressBatchParallel($jobIds, $quality, $onProgress);
    }

    // =========================================================
    //  GHOSTSCRIPT (SINGLE, for direct call)
    // =========================================================

    protected function compressWithGhostscript(
        string $input,
        string $output,
        string $quality = 'ebook'
    ): bool {
        $gsPath  = $this->findGhostscript();
        $setting = self::GS_SETTINGS[$quality] ?? '/ebook';
        $env     = $this->getGsEnv();

        $process = new Process([
            $gsPath,
            '-sDEVICE=pdfwrite',
            '-dCompatibilityLevel=1.4',
            "-dPDFSETTINGS={$setting}",
            '-dNOPAUSE',
            '-dQUIET',
            '-dBATCH',
            '-dDetectDuplicateImages=true',
            '-dCompressFonts=true',
            '-dSubsetFonts=true',
            "-sOutputFile={$output}",
            $input,
        ], null, $env);
        $process->setTimeout(600);
        $process->run();

        return $process->isSuccessful();
    }

    private function getGsEnv(): array
    {
        $tempDir = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app/temp_gs'));
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }
        putenv("TEMP={$tempDir}");
        putenv("TMP={$tempDir}");

        return [
            'TEMP'       => $tempDir,
            'TMP'        => $tempDir,
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
            'PATH'       => getenv('PATH') ?: '',
        ];
    }

    // =========================================================
    //  FPDI FALLBACK
    // =========================================================

    protected function compressWithFpdi(string $input, string $output): bool
    {
        try {
            $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetAutoPageBreak(false);
            $pdf->setFontSubsetting(true);

            $pageCount = $pdf->setSourceFile($input);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplId = $pdf->importPage($i);
                $size  = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['width'] > $size['height'] ? 'L' : 'P', [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height'], true);
            }

            $pdf->Output($output, 'F');
            return file_exists($output) && filesize($output) > 0;

        } catch (\Throwable $e) {
            Log::warning('FPDI failed: ' . $e->getMessage());
            return copy($input, $output); // last resort
        }
    }

    // =========================================================
    //  HELPERS
    // =========================================================

    /**
     * Determine output path for a job.
     */
    private function resolveOutputPath(CompressionJob $job): string
    {
        return "compressed/{$job->session_id}/compressed_{$job->original_filename}";
    }

    /**
     * Finalize job record after compression attempt.
     */
    private function finalizeJob(
        CompressionJob $job,
        string $outputRel,
        string $outputPath,
        bool $processOk
    ): bool {
        if (!$processOk || !file_exists($outputPath) || filesize($outputPath) === 0) {
            $job->update(['status' => 'failed', 'error_message' => 'Output file tidak terbentuk.']);
            return false;
        }

        $compressedSize = filesize($outputPath);
        $ratio = round((1 - ($compressedSize / $job->original_size)) * 100, 2);

        $job->update([
            'status'            => 'completed',
            'compressed_path'   => $outputRel,
            'compressed_size'   => $compressedSize,
            'compression_ratio' => max(0, $ratio),
            'compressed_at'     => now(),
        ]);

        Log::info("✅ Job #{$job->id} done — ratio: {$ratio}%");
        return true;
    }

    // =========================================================
    //  ZIP & CLEANUP
    // =========================================================

    public function createZipArchive(array $jobIds, string $sessionId): ?string
    {
        $jobs = CompressionJob::whereIn('id', $jobIds)
            ->where('status', 'completed')
            ->whereNotNull('compressed_path')
            ->get();

        if ($jobs->isEmpty()) return null;

        $zipDir  = Storage::disk('public')->path("zips/{$sessionId}");
        if (!is_dir($zipDir)) mkdir($zipDir, 0755, true);

        $zipPath    = $zipDir . '/compressed_pdfs_' . time() . '.zip';
        $zipRelPath = "zips/{$sessionId}/" . basename($zipPath);

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("Cannot create ZIP.");
        }

        foreach ($jobs as $job) {
            $fp = Storage::disk('public')->path($job->compressed_path);
            if (file_exists($fp)) {
                $zip->addFile($fp, 'compressed_' . $job->original_filename);
            }
        }

        $zip->close();
        return $zipRelPath;
    }

    public function cleanupSession(string $sessionId): void
    {
        Storage::disk('public')->deleteDirectory("uploads/{$sessionId}");
        Storage::disk('public')->deleteDirectory("compressed/{$sessionId}");
        Storage::disk('public')->deleteDirectory("zips/{$sessionId}");
    }
}
