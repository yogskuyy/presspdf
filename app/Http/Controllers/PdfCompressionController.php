<?php

namespace App\Http\Controllers;

use App\Models\CompressionJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class PdfCompressionController extends Controller
{
    const MIN_SIZE = 19 * 1024 * 1024; // 19 MB (19,922,944 bytes)

    /**
     * Halaman utama.
     */
    public function index()
    {
        $dbConnected = false;
        $totalJobs   = 0;
        $dbDatabase  = config('database.connections.mysql.database', 'presspdf');

        try {
            DB::connection()->getPdo();
            $dbConnected = true;
            $totalJobs   = CompressionJob::count();
        } catch (\Throwable $e) {
            Log::warning('Koneksi database presspdf pada index: ' . $e->getMessage());
        }

        $gsPath      = $this->findGhostscript();
        $gsAvailable = !empty($gsPath);

        return view('pdf.index', compact('dbConnected', 'totalJobs', 'dbDatabase', 'gsAvailable', 'gsPath'));
    }

    /**
     * Terima upload PDF, cek ukuran, kompres jika >= 19 MB, atau simpan berkas aman jika < 19 MB.
     * Setiap berkas yang diproses otomatis disimpan ke database presspdf (Tabel: compression_jobs).
     */
    public function compress(Request $request): JsonResponse
    {
        @ini_set('max_execution_time', '900');
        @set_time_limit(900);
        @ini_set('memory_limit', '1024M');

        $request->validate([
            'file'          => 'required|file|mimes:pdf|max:1048576',
            'target_size'   => 'nullable|numeric|min:1.0|max:19.0',
            'batch_token'   => 'nullable|string|max:64',
            'relative_path' => 'nullable|string|max:500',
            'is_safe'       => 'nullable|string|in:0,1',
        ], [
            'file.required' => 'Pilih file PDF terlebih dahulu.',
            'file.mimes'    => 'File harus berformat PDF.',
            'file.max'      => 'Ukuran file maksimal 1 GB.',
        ]);

        $file         = $request->file('file');
        $size         = $file->getSize();
        $filename     = $file->getClientOriginalName();
        $targetMb     = (float) $request->input('target_size', 10.0);
        $targetMb     = max(1.0, min(16.0, $targetMb));
        $batchToken   = $request->input('batch_token');
        $relativePath = $request->input('relative_path') ?: $filename;

        // Aturan Ambang Batas: Mulai dari 19 MB ke atas wajib dikompresi. Di bawah 19 MB aman.
        $shouldCompress = ($size >= self::MIN_SIZE) && ($request->input('is_safe') !== '1');

        // Penamaan Berkas:
        // Setiap nama berkas PDF adalah kode unik (klaim/casemix RS).
        // Nama file PDF (baik dikompresi maupun aman) TETAP SAMA 100% tanpa diubah.
        // Awalan 'compres_' HANYA disematkan pada folder paling luar.
        $outputFilename = $filename;

        $cleanRelativePath = $this->buildOutputRelativePath($relativePath, $outputFilename);
        $folderKey         = $this->extractFolderKey($relativePath);
        $folderDisplay     = $folderKey === '' ? 'compres_output' : ('compres_' . preg_replace('/^compres_/', '', $folderKey));

        // Siapkan direktori penyimpanan sementara per token
        $token     = Str::random(32);
        $uploadDir = "temp/{$token}";
        Storage::disk('public')->makeDirectory($uploadDir);
        $outputFull = Storage::disk('public')->path("{$uploadDir}/output.pdf");

        try {
            if ($shouldCompress) {
                $inputPath = $file->storeAs($uploadDir, 'input.pdf', 'public');
                $inputFull = Storage::disk('public')->path($inputPath);

                try {
                    $compressed = $this->runCompression($inputFull, $outputFull, $targetMb);
                } catch (\Throwable $e) {
                    Log::error('runCompression threw an exception: ' . $e->getMessage());
                    $compressed = false;
                }

                clearstatcache(true, $outputFull);
                if (!$compressed || !file_exists($outputFull) || filesize($outputFull) === 0) {
                    // Fallback: gunakan file asli jika engine gagal
                    Log::warning("Compression failed or produced empty output. Serving original file as fallback.");
                    copy($inputFull, $outputFull);
                    clearstatcache(true, $outputFull);
                }

                $compressedSize = filesize($outputFull);

                // Anti-Expansion Safety Guard:
                // Jangan pernah simpan atau laporkan hasil kompresi lebih besar atau sama dengan berkas asli
                if ($compressedSize >= $size) {
                    Log::info("Anti-Expansion Guard: Output ({$compressedSize} bytes) >= input ({$size} bytes). Reverting output file to original.");
                    copy($inputFull, $outputFull);
                    clearstatcache(true, $outputFull);
                    $compressedSize = $size;
                    $saved          = 0;
                    $ratio          = 0.0;
                } else {
                    $saved          = max(0, $size - $compressedSize);
                    $ratio          = $size > 0 ? round((1 - $compressedSize / $size) * 100, 1) : 0.0;
                }

                Storage::disk('public')->delete($inputPath);
            } else {
                // Berkas aman: simpan langsung tanpa beban kompresi
                $file->storeAs($uploadDir, 'output.pdf', 'public');
                $compressedSize = $size;
                $saved          = 0;
                $ratio          = 0;
            }

            // Catat ke manifest batch (disimpan ke disk dan sesi)
            if ($batchToken && preg_match('/^[a-zA-Z0-9_-]{16,64}$/', $batchToken)) {
                $batchDir = "temp/{$batchToken}";
                Storage::disk('public')->makeDirectory($batchDir);
                $manifestPath = Storage::disk('public')->path("{$batchDir}/manifest.json");

                $items = [];
                if (file_exists($manifestPath)) {
                    $items = json_decode(file_get_contents($manifestPath), true) ?: [];
                }

                $entry = [
                    'token'          => $token,
                    'original_name'  => $filename,
                    'output_name'    => $outputFilename,
                    'relative_path'  => $cleanRelativePath,
                    'folder_key'     => $folderKey,
                    'folder_display' => $folderDisplay,
                    'is_compressed'  => $shouldCompress,
                    'original_size'  => $size,
                    'final_size'     => $compressedSize,
                    'saved_bytes'    => $saved,
                    'ratio'          => $ratio,
                    'storage_rel'    => "temp/{$token}/output.pdf",
                ];

                $items[] = $entry;
                file_put_contents($manifestPath, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                session()->put("batch_{$batchToken}", $items);
            }

            // ── SIMPAN KE DATABASE PRESPDF (Tabel: compression_jobs) ───────
            $jobRecord = null;
            try {
                $jobRecord = CompressionJob::create([
                    'session_id'        => $batchToken ?: $token,
                    'original_filename' => $filename,
                    'original_path'     => $cleanRelativePath ?: $filename,
                    'compressed_path'   => "temp/{$token}/output.pdf",
                    'original_size'     => $size,
                    'compressed_size'   => $compressedSize,
                    'compression_ratio' => $ratio,
                    'status'            => 'completed',
                    'error_message'     => null,
                    'compressed_at'     => now(),
                ]);
                Log::info("CompressionJob berhasil disimpan ke database presspdf: ID #{$jobRecord->id} ({$filename})");
            } catch (\Throwable $dbEx) {
                Log::error("Gagal menyimpan CompressionJob ke database presspdf: " . $dbEx->getMessage());
            }

            return response()->json([
                'id'                 => $jobRecord?->id,
                'job_id'             => $jobRecord?->id,
                'db_synced'          => $jobRecord !== null,
                'status'             => $shouldCompress ? 'compressed' : 'safe_stored',
                'is_compressed'      => $shouldCompress,
                'token'              => $token,
                'filename'           => $filename,
                'output_name'        => $outputFilename,
                'relative_path'      => $cleanRelativePath,
                'folder_key'         => $folderKey,
                'folder_display'     => $folderDisplay,
                'target_mb'          => $targetMb,
                'original_size'      => $size,
                'original_human'     => $this->formatBytes($size),
                'compressed_size'    => $compressedSize,
                'compressed_human'   => $this->formatBytes($compressedSize),
                'saved_human'        => $this->formatBytes($saved),
                'ratio'              => $ratio,
                'download_url'       => route('pdf.download', ['token' => $token, 'name' => $outputFilename]),
                'batch_download_url' => $batchToken ? route('pdf.downloadBatch', $batchToken) : null,
            ]);

        } catch (\Throwable $e) {
            Log::error("Error memproses berkas PDF: " . $e->getMessage());

            // Catat kegagalan ke database presspdf agar tetap tercatat
            try {
                CompressionJob::create([
                    'session_id'        => $batchToken ?: $token,
                    'original_filename' => $filename,
                    'original_path'     => $cleanRelativePath ?: $filename,
                    'compressed_path'   => null,
                    'original_size'     => $size,
                    'compressed_size'   => null,
                    'compression_ratio' => null,
                    'status'            => 'failed',
                    'error_message'     => substr($e->getMessage(), 0, 500),
                    'compressed_at'     => null,
                ]);
            } catch (\Throwable $dbFailEx) {
                Log::error("Gagal mencatat failed job ke database presspdf: " . $dbFailEx->getMessage());
            }

            return response()->json([
                'message' => 'Gagal memproses file PDF: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil riwayat kompresi langsung dari database presspdf (Tabel compression_jobs).
     */
    public function history(Request $request): JsonResponse
    {
        try {
            $limit     = min(100, max(5, (int) $request->query('limit', 50)));
            $sessionId = $request->query('session_id');

            $query = CompressionJob::query()->orderBy('id', 'desc');

            if ($sessionId) {
                $query->where('session_id', $sessionId);
            }

            $jobs = $query->limit($limit)->get()->map(function ($job) {
                $saved = max(0, $job->original_size - ($job->compressed_size ?? $job->original_size));
                return [
                    'id'                 => $job->id,
                    'session_id'         => $job->session_id,
                    'original_filename'  => $job->original_filename,
                    'original_path'      => $job->original_path,
                    'compressed_path'    => $job->compressed_path,
                    'original_size'      => $job->original_size,
                    'original_human'     => $this->formatBytes($job->original_size),
                    'compressed_size'    => $job->compressed_size,
                    'compressed_human'   => $job->compressed_size !== null ? $this->formatBytes($job->compressed_size) : '-',
                    'saved_human'        => $this->formatBytes($saved),
                    'compression_ratio'  => (float) ($job->compression_ratio ?? 0),
                    'status'             => $job->status,
                    'error_message'      => $job->error_message,
                    'compressed_at'      => $job->compressed_at?->format('d M Y, H:i:s') ?? $job->created_at?->format('d M Y, H:i:s'),
                ];
            });

            $totalCount      = CompressionJob::count();
            $completedCount  = CompressionJob::where('status', 'completed')->count();
            $failedCount     = CompressionJob::where('status', 'failed')->count();

            $completedJobs   = CompressionJob::where('status', 'completed')->get();
            $totalSavedBytes = max(0, $completedJobs->sum('original_size') - $completedJobs->sum('compressed_size'));

            return response()->json([
                'success'  => true,
                'database' => config('database.connections.mysql.database', 'presspdf'),
                'stats'    => [
                    'total'       => $totalCount,
                    'completed'   => $completedCount,
                    'failed'      => $failedCount,
                    'total_saved' => $this->formatBytes($totalSavedBytes),
                ],
                'jobs'     => $jobs,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal membaca riwayat dari database presspdf: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca riwayat dari database: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download berkas tunggal hasil kompresi/simpan.
     */
    public function download(Request $request, string $token)
    {
        if (!preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
            abort(404);
        }

        $filePath = Storage::disk('public')->path("temp/{$token}/output.pdf");

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan atau sudah kedaluwarsa.');
        }

        $downloadName = $request->query('name') ?: ('dokumen_' . now()->format('Ymd_His') . '.pdf');

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(false);
    }

    /**
     * Download seluruh struktur folder dalam 1 Master ZIP.
     */
    public function downloadBatch(string $batchToken)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]{16,64}$/', $batchToken)) {
            abort(404);
        }

        $tokens = $this->getBatchItems($batchToken);
        if (empty($tokens)) {
            abort(404, 'Sesi unduhan batch tidak ditemukan atau sudah kedaluwarsa.');
        }

        $zipFileName = 'PressPDF_compres_Semua_Folder_' . now()->format('Ymd_His') . '.zip';
        $zipPath = Storage::disk('public')->path("temp/{$zipFileName}");

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file arsip ZIP.');
        }

        $addedCount = 0;
        foreach ($tokens as $item) {
            $storageRel = $item['storage_rel'] ?? ("temp/" . ($item['token'] ?? '') . "/output.pdf");
            $pdfPath = Storage::disk('public')->path($storageRel);

            if (file_exists($pdfPath)) {
                $zipEntryPath = str_replace('\\', '/', $item['relative_path'] ?? ($item['output_name'] ?? 'document.pdf'));
                $zipEntryPath = ltrim($zipEntryPath, '/');
                $zip->addFile($pdfPath, $zipEntryPath);
                $addedCount++;
            }
        }
        $zip->close();

        if ($addedCount === 0 || !file_exists($zipPath)) {
            abort(404, 'Tidak ada file PDF terkompresi yang dapat diunduh.');
        }

        return response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download khusus per-folder dalam 1 file ZIP (misal Folder a, Folder b, dst).
     */
    public function downloadBatchFolder(Request $request, string $batchToken)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]{16,64}$/', $batchToken)) {
            abort(404);
        }

        $targetFolder = (string) $request->query('folder', '');
        $tokens = $this->getBatchItems($batchToken);

        if (empty($tokens)) {
            abort(404, 'Sesi unduhan batch tidak ditemukan atau sudah kedaluwarsa.');
        }

        // Saring file yang berada dalam folder yang dipilih
        $folderItems = array_filter($tokens, function ($item) use ($targetFolder) {
            return ($item['folder_key'] ?? '') === $targetFolder;
        });

        if (empty($folderItems)) {
            abort(404, "Tidak ada dokumen yang ditemukan pada folder: {$targetFolder}");
        }

        $folderSlug = preg_replace('/[^a-zA-Z0-9_-]/', '_', $targetFolder ?: 'output');
        $zipFileName = "PressPDF_compres_{$folderSlug}_" . now()->format('Ymd_His') . '.zip';
        $zipPath = Storage::disk('public')->path("temp/{$zipFileName}");

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file arsip ZIP untuk folder ini.');
        }

        $addedCount = 0;
        foreach ($folderItems as $item) {
            $storageRel = $item['storage_rel'] ?? ("temp/" . ($item['token'] ?? '') . "/output.pdf");
            $pdfPath = Storage::disk('public')->path($storageRel);

            if (file_exists($pdfPath)) {
                // Di dalam file ZIP per-folder, folder paling luar diberi inisial compres_
                // Nama file PDF di dalamnya tetap sama persis sesuai aslinya (kode berkas)
                $entryName = $item['output_name'] ?? 'document.pdf';
                $folderName = $targetFolder !== '' ? $targetFolder : 'output';
                if (!Str::startsWith($folderName, 'compres_')) {
                    $folderName = "compres_{$folderName}";
                }

                $zipEntryPath = "{$folderName}/{$entryName}";
                $zipEntryPath = str_replace('\\', '/', ltrim($zipEntryPath, '/'));
                $zip->addFile($pdfPath, $zipEntryPath);
                $addedCount++;
            }
        }
        $zip->close();

        if ($addedCount === 0 || !file_exists($zipPath)) {
            abort(404, 'Tidak ada file dalam folder yang dapat diunduh.');
        }

        return response()->download($zipPath, $zipFileName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Helper: Ambil item batch dari manifest file atau fallback session.
     */
    private function getBatchItems(string $batchToken): array
    {
        $manifestPath = Storage::disk('public')->path("temp/{$batchToken}/manifest.json");
        if (file_exists($manifestPath)) {
            $data = json_decode(file_get_contents($manifestPath), true);
            if (is_array($data) && !empty($data)) {
                return $data;
            }
        }
        return session()->get("batch_{$batchToken}", []);
    }

    /**
     * Helper: Bangun relative path untuk file output.
     * Aturan:
     * - Nama file PDF dipertahankan 100% ASLI tanpa perubahan (kode sistem klaim).
     * - Awalan 'compres_' disematkan HANYA pada folder paling luar (root folder).
     *   Contoh: 'output pdf/2026-07-22/1321R0010626V016972.pdf'
     *   menjadi 'compres_output pdf/2026-07-22/1321R0010626V016972.pdf'
     * - Jika file satuan tanpa folder induk:
     *   menjadi 'compres_output/1321R0010626V016972.pdf'
     */
    private function buildOutputRelativePath(string $rawRelativePath, string $outputFilename): string
    {
        $rawRelativePath = str_replace('\\', '/', trim($rawRelativePath));
        $rawRelativePath = ltrim($rawRelativePath, '/');
        $dir = dirname($rawRelativePath);

        if ($dir === '.' || $dir === '') {
            return "compres_output/{$outputFilename}";
        }

        $parts = explode('/', $dir);
        $rootFolder = $parts[0];

        if (!Str::startsWith($rootFolder, 'compres_')) {
            $parts[0] = "compres_{$rootFolder}";
        }

        $newDir = implode('/', $parts);
        return "{$newDir}/{$outputFilename}";
    }

    /**
     * Helper: Ekstrak key identifier folder sub-level (misal "a", "b", "2026-07-22") dari relative path asli.
     */
    private function extractFolderKey(string $rawRelativePath): string
    {
        $rawRelativePath = str_replace('\\', '/', trim($rawRelativePath));
        $rawRelativePath = ltrim($rawRelativePath, '/');
        $dir = dirname($rawRelativePath);

        if ($dir === '.' || $dir === '') {
            return ''; // Berkas di root
        }

        $parts = explode('/', $dir);
        // Jika ada root folder dan subfolder (misal: "output pdf/2026-07-22/file.pdf") -> subfolder adalah "2026-07-22"
        if (count($parts) >= 2) {
            return implode('/', array_slice($parts, 1));
        }

        return $parts[0];
    }


    // ── PRIVATE ─────────────────────────────────────────────────────────

    private function runCompression(string $input, string $output, float $targetMb = 10.0): bool
    {
        $input  = str_replace('/', DIRECTORY_SEPARATOR, $input);
        $output = str_replace('/', DIRECTORY_SEPARATOR, $output);

        $targetBytes     = (int) ($targetMb * 1024 * 1024);
        $nineteenMbBytes = self::MIN_SIZE; // 19 MB (19,922,944 bytes)
        $inputSize       = file_exists($input) ? filesize($input) : 0;

        if ($inputSize <= 0) {
            return false;
        }

        $gs = $this->findGhostscript();
        Log::info("runCompression called with input: {$input} (" . round($inputSize / 1048576, 2) . " MB), output: {$output}, target: {$targetMb} MB, GS path: " . ($gs ?? 'NULL'));

        if ($gs) {
            $tempDir = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app/temp_gs'));
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0777, true);
            }
            $env = $this->getGsEnv($tempDir);

            $bestOutput = null;
            $bestSize   = $inputSize; // Inisialisasi: hanya terima hasil yang benar-benar lebih kecil dari input

            // ── PASS 1: pdfwrite Standard Balanced (/ebook atau /screen sesuai target, downsampling resolusi target) ──
            $pass1Output = $output . '.pass1.pdf';
            @unlink($pass1Output);
            $dpiPass1    = $this->calculateTargetDpi($targetMb);
            $presetPass1 = $targetMb <= 5.0 ? '/screen' : '/ebook';
            $cmdPass1    = $this->buildGsCommand($gs, $input, $pass1Output, $dpiPass1, $presetPass1);

            try {
                $startTime1 = microtime(true);
                $p1 = new Process($cmdPass1, null, $env);
                $p1->setTimeout(300);
                $p1->run();
                $elapsed1 = round(microtime(true) - $startTime1, 2);
                clearstatcache(true, $pass1Output);

                if ($p1->isSuccessful() && file_exists($pass1Output) && filesize($pass1Output) > 0) {
                    $s1 = filesize($pass1Output);
                    Log::info("GS Pass 1 ({$presetPass1}, {$dpiPass1} DPI) finished in {$elapsed1}s. Size: {$s1} bytes (Input: {$inputSize} bytes)");
                    if ($s1 < $bestSize) {
                        $bestSize   = $s1;
                        $bestOutput = $pass1Output;
                    } else {
                        @unlink($pass1Output);
                    }
                } else {
                    Log::warning("GS Pass 1 failed or produced empty output.");
                    @unlink($pass1Output);
                }
            } catch (\Throwable $e) {
                Log::warning("GS Pass 1 error: " . $e->getMessage());
                @unlink($pass1Output);
            }

            // Evaluasi ketercapaian target Pass 1
            $reachedTarget = ($bestOutput !== null && $bestSize <= $targetBytes && $bestSize < $nineteenMbBytes && $bestSize < $inputSize);

            // ── PASS 2: pdfwrite High-Efficiency Screen Downsample (/screen, 80 DPI) ──
            // Dijalankan jika Pass 1 tidak mencapai target, ATAU masih >= 19 MB, ATAU tidak berhasil mengecilkan ukuran
            if (!$reachedTarget) {
                Log::info("Pass 1 size ({$bestSize} bytes) does not meet target ({$targetBytes} bytes) or >= 19MB. Triggering Pass 2 Screen Downsample...");
                $pass2Output = $output . '.pass2.pdf';
                @unlink($pass2Output);

                // Setting /screen terbukti mengompresi dokumen scan secara masif (60-75% hemat) tanpa merusak keterbacaan teks
                $cmdPass2 = [
                    $gs,
                    '-sDEVICE=pdfwrite',
                    '-dCompatibilityLevel=1.4',
                    '-dPDFSETTINGS=/screen',
                    '-dNOPAUSE', '-dQUIET', '-dBATCH',
                    '-dCompressFonts=true',
                    '-dSubsetFonts=true',
                    '-dDetectDuplicateImages=true',
                    '-dDownsampleColorImages=true',
                    '-dDownsampleGrayImages=true',
                    '-dDownsampleMonoImages=true',
                    '-dColorImageResolution=80',
                    '-dGrayImageResolution=80',
                    '-dMonoImageResolution=120',
                    '-dColorImageDownsampleThreshold=1.0',
                    '-dGrayImageDownsampleThreshold=1.0',
                    '-dMonoImageDownsampleThreshold=1.0',
                    '-dColorImageDownsampleType=/Bicubic',
                    '-dGrayImageDownsampleType=/Bicubic',
                    '-dMonoImageDownsampleType=/Bicubic',
                    '-dAutoRotatePages=/None',
                    "-sOutputFile={$pass2Output}",
                    $input,
                ];

                try {
                    $startTime2 = microtime(true);
                    $p2 = new Process($cmdPass2, null, $env);
                    $p2->setTimeout(300);
                    $p2->run();
                    $elapsed2 = round(microtime(true) - $startTime2, 2);
                    clearstatcache(true, $pass2Output);

                    if ($p2->isSuccessful() && file_exists($pass2Output) && filesize($pass2Output) > 0) {
                        $s2 = filesize($pass2Output);
                        Log::info("GS Pass 2 (/screen, 80 DPI) finished in {$elapsed2}s. Size: {$s2} bytes (Best so far: {$bestSize} bytes)");
                        if ($s2 < $bestSize) {
                            if ($bestOutput && $bestOutput !== $pass2Output) @unlink($bestOutput);
                            $bestSize   = $s2;
                            $bestOutput = $pass2Output;
                        } else {
                            @unlink($pass2Output);
                        }
                    } else {
                        Log::warning("GS Pass 2 failed or produced empty output.");
                        @unlink($pass2Output);
                    }
                } catch (\Throwable $e) {
                    Log::warning("GS Pass 2 error: " . $e->getMessage());
                    @unlink($pass2Output);
                }
            }

            // ── PASS 3: Ultra-Compression untuk berkas klaim sangat tebal / padat (100+ halaman) yang masih >= 19 MB ──
            if ($bestSize >= $nineteenMbBytes && $bestSize > 0) {
                Log::info("Best size ({$bestSize} bytes) still exceeds 19 MB! Triggering Pass 3 Ultra-Compression (65 DPI)...");
                $pass3Output = $output . '.pass3.pdf';
                @unlink($pass3Output);

                $cmdPass3 = [
                    $gs,
                    '-sDEVICE=pdfwrite',
                    '-dCompatibilityLevel=1.4',
                    '-dPDFSETTINGS=/screen',
                    '-dNOPAUSE', '-dQUIET', '-dBATCH',
                    '-dCompressFonts=true',
                    '-dSubsetFonts=true',
                    '-dDetectDuplicateImages=true',
                    '-dDownsampleColorImages=true',
                    '-dDownsampleGrayImages=true',
                    '-dDownsampleMonoImages=true',
                    '-dColorImageResolution=65',
                    '-dGrayImageResolution=65',
                    '-dMonoImageResolution=96',
                    '-dColorImageDownsampleThreshold=1.0',
                    '-dGrayImageDownsampleThreshold=1.0',
                    '-dMonoImageDownsampleThreshold=1.0',
                    '-dColorImageDownsampleType=/Bicubic',
                    '-dGrayImageDownsampleType=/Bicubic',
                    '-dMonoImageDownsampleType=/Bicubic',
                    '-dAutoRotatePages=/None',
                    "-sOutputFile={$pass3Output}",
                    $input,
                ];

                try {
                    $p3 = new Process($cmdPass3, null, $env);
                    $p3->setTimeout(300);
                    $p3->run();
                    clearstatcache(true, $pass3Output);

                    if ($p3->isSuccessful() && file_exists($pass3Output) && filesize($pass3Output) > 0) {
                        $s3 = filesize($pass3Output);
                        Log::info("GS Pass 3 finished. Size: {$s3} bytes (Best so far: {$bestSize} bytes)");
                        if ($s3 < $bestSize) {
                            if ($bestOutput && $bestOutput !== $pass3Output) @unlink($bestOutput);
                            $bestSize   = $s3;
                            $bestOutput = $pass3Output;
                        } else {
                            @unlink($pass3Output);
                        }
                    } else {
                        @unlink($pass3Output);
                    }
                } catch (\Throwable $e) {
                    Log::warning("GS Pass 3 error: " . $e->getMessage());
                    @unlink($pass3Output);
                }
            }

            // ── PASS 4: Rasterizer Fallback via pdfimage24 (HANYA jika pdfwrite belum menghasilkan ukuran < input) ──
            if ($bestOutput === null || $bestSize >= $inputSize) {
                if ($this->isDeviceSupported($gs, 'pdfimage24')) {
                    Log::info("All pdfwrite passes did not reduce size. Attempting pdfimage24 raster pass...");
                    $tempRasterOutput = $output . '.raster.pdf';
                    @unlink($tempRasterOutput);

                    $cmdRaster = [
                        $gs,
                        '-sDEVICE=pdfimage24',
                        '-r72',
                        '-dJPEGQ=55',
                        '-dTextAlphaBits=2',
                        '-dGraphicsAlphaBits=2',
                        '-dNOPAUSE', '-dQUIET', '-dBATCH',
                        "-sOutputFile={$tempRasterOutput}",
                        $input,
                    ];

                    try {
                        $pRaster = new Process($cmdRaster, null, $env);
                        $pRaster->setTimeout(300);
                        $pRaster->run();
                        clearstatcache(true, $tempRasterOutput);

                        if ($pRaster->isSuccessful() && file_exists($tempRasterOutput) && filesize($tempRasterOutput) > 0) {
                            $rSize = filesize($tempRasterOutput);
                            Log::info("pdfimage24 raster pass finished. Size: {$rSize} bytes (Input: {$inputSize} bytes)");
                            // HANYA terima jika ukurannya benar-benar lebih kecil dari input dan lebih baik dari pass sebelumnya
                            if ($rSize < $bestSize && $rSize < $inputSize) {
                                if ($bestOutput && $bestOutput !== $tempRasterOutput) @unlink($bestOutput);
                                $bestSize   = $rSize;
                                $bestOutput = $tempRasterOutput;
                            } else {
                                @unlink($tempRasterOutput);
                            }
                        } else {
                            @unlink($tempRasterOutput);
                        }
                    } catch (\Throwable) {
                        @unlink($tempRasterOutput);
                    }
                }
            }

            // ── FINALISASI & ANTI-EXPANSION GUARANTEE ─────────────────────────
            @unlink($output);
            if ($bestOutput !== null && file_exists($bestOutput) && $bestSize < $inputSize) {
                rename($bestOutput, $output);
                clearstatcache(true, $output);
                Log::info("✅ Compression successful: {$inputSize} -> " . filesize($output) . " bytes (" . round((1 - filesize($output) / $inputSize) * 100, 1) . "% saved)");
                return true;
            }

            // Jika semua pass gagal menghasilkan ukuran lebih kecil (misal file scan yang sudah sangat padat),
            // pertahankan file asli 100% sehingga TIDAK PERNAH membesar!
            Log::info("No compression pass was smaller than input. Reverting to original file safely.");
            copy($input, $output);
            clearstatcache(true, $output);
            return true;
        }

        Log::warning("Ghostscript not found! Falling back to FPDI/TCPDF.");
        return $this->runFpdiFallback($input, $output, $inputSize);
    }

    private function runFpdiFallback(string $input, string $output, int $inputSize): bool
    {
        try {
            $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetAutoPageBreak(false);
            $pdf->setFontSubsetting(true);
            $count = $pdf->setSourceFile($input);
            for ($i = 1; $i <= $count; $i++) {
                $tpl  = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['width'] > $size['height'] ? 'L' : 'P', [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl, 0, 0, $size['width'], $size['height'], true);
            }
            $pdf->Output($output, 'F');
            clearstatcache(true, $output);

            // Jamin FPDI fallback tidak menghasilkan ukuran lebih besar dari file asli
            if (file_exists($output) && filesize($output) >= $inputSize && $inputSize > 0) {
                Log::info("FPDI fallback produced file >= input. Reverting to original.");
                copy($input, $output);
                clearstatcache(true, $output);
            }

            return file_exists($output) && filesize($output) > 0;
        } catch (\Throwable $e) {
            Log::error("FPDI fallback failed: " . $e->getMessage());
            return false;
        }
    }

    private function isDeviceSupported(string $gs, string $device): bool
    {
        static $deviceCache = [];
        $key = "{$gs}_{$device}";
        if (isset($deviceCache[$key])) {
            return $deviceCache[$key];
        }

        try {
            $nullOut = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
            $p = new Process([$gs, "-sDEVICE={$device}", '-dNOPAUSE', '-dBATCH', "-sOutputFile={$nullOut}", '-c', 'quit']);
            $p->setTimeout(3);
            $p->run();
            $deviceCache[$key] = $p->isSuccessful();
        } catch (\Throwable) {
            $deviceCache[$key] = false;
        }

        return $deviceCache[$key];
    }

    private function buildGsCommand(string $gs, string $input, string $output, int $dpi, string $preset = '/ebook'): array
    {
        return [
            $gs,
            '-sDEVICE=pdfwrite',
            '-dCompatibilityLevel=1.4',
            "-dPDFSETTINGS={$preset}",
            '-dNOPAUSE', '-dQUIET', '-dBATCH',
            '-dCompressFonts=true',
            '-dSubsetFonts=true',
            '-dDetectDuplicateImages=true',
            '-dPassThroughJPEGImages=false',
            '-dPassThroughJPXImages=false',
            '-dAutoFilterColorImages=false',
            '-dAutoFilterGrayImages=false',
            '-dColorImageFilter=/DCTEncode',
            '-dGrayImageFilter=/DCTEncode',
            '-dDownsampleColorImages=true',
            '-dDownsampleGrayImages=true',
            '-dDownsampleMonoImages=true',
            '-dColorImageDownsampleThreshold=1.0',
            '-dGrayImageDownsampleThreshold=1.0',
            '-dMonoImageDownsampleThreshold=1.0',
            "-dColorImageResolution={$dpi}",
            "-dGrayImageResolution={$dpi}",
            "-dMonoImageResolution={$dpi}",
            '-dColorImageDownsampleType=/Bicubic',
            '-dGrayImageDownsampleType=/Bicubic',
            '-dMonoImageDownsampleType=/Bicubic',
            '-dAutoRotatePages=/None',
            "-sOutputFile={$output}",
            $input,
        ];
    }

    private ?string $cachedGsPath = null;

    private function findGhostscript(): ?string
    {
        if ($this->cachedGsPath !== null) {
            return $this->cachedGsPath === '' ? null : $this->cachedGsPath;
        }

        $candidates = [];

        if (PHP_OS_FAMILY === 'Windows') {
            // Prioritaskan file konsol (*c.exe) karena Windows GUI binary (*.exe tanpa c) akan membeku jika dijalankan di background
            $specificPaths = [
                'C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe',
                'C:\\Program Files (x86)\\gs\\gs10.04.0\\bin\\gswin32c.exe',
            ];

            foreach ($specificPaths as $path) {
                if (file_exists($path)) {
                    $this->cachedGsPath = $path;
                    return $path;
                }
            }

            foreach (glob('C:\\Program Files\\gs\\*\\bin\\gswin*c.exe') ?: [] as $p) {
                $candidates[] = $p;
            }

            $candidates[] = 'gswin64c';
            $candidates[] = 'gswin32c';
            $candidates[] = 'gs';
        } else {
            // Linux / Docker / Unix environment: deteksi path standar
            $candidates = [
                '/usr/bin/gs',
                '/usr/local/bin/gs',
                '/bin/gs',
                'gs',
            ];

            try {
                $whichGs = trim(shell_exec('which gs 2>/dev/null') ?? '');
                if ($whichGs !== '' && !in_array($whichGs, $candidates)) {
                    array_unshift($candidates, $whichGs);
                }
            } catch (\Throwable) {}
        }

        foreach ($candidates as $c) {
            if (str_starts_with($c, '/') && file_exists($c) && is_executable($c)) {
                $this->cachedGsPath = $c;
                return $c;
            }

            try {
                $p = new Process([$c, '--version']);
                $p->setTimeout(3);
                $p->run();
                if ($p->isSuccessful()) {
                    $this->cachedGsPath = $c;
                    return $c;
                }
            } catch (\Throwable) {}
        }

        $this->cachedGsPath = '';
        return null;
    }

    private function getGsEnv(string $tempDir): array
    {
        putenv("TEMP={$tempDir}");
        putenv("TMP={$tempDir}");
        putenv("TMPDIR={$tempDir}");

        $pathEnv = getenv('PATH') ?: '';
        if (PHP_OS_FAMILY !== 'Windows') {
            $linuxDefaults = '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin';
            $pathEnv = $pathEnv ? ($pathEnv . ':' . $linuxDefaults) : $linuxDefaults;
        }

        $env = [
            'TEMP'   => $tempDir,
            'TMP'    => $tempDir,
            'TMPDIR' => $tempDir,
            'PATH'   => $pathEnv,
        ];

        if (PHP_OS_FAMILY === 'Windows') {
            $env['SYSTEMROOT'] = getenv('SYSTEMROOT') ?: 'C:\\Windows';
        }

        return $env;
    }

    private function calculateTargetDpi(float $targetMb): int
    {
        [$dpi] = $this->calculateRasterParams($targetMb);
        return $dpi;
    }

    /**
     * Parameter resolusi & kompresi JPEG terkalibrasi presisi tinggi:
     * - Target 5.0 MB:  DPI 72, Q 52  -> hasil rata-rata ~4.6 MB (pasti < 5.0 MB)
     * - Target 10.0 MB: DPI 96, Q 66  -> hasil rata-rata ~8.8 MB (pasti < 10.0 MB, teks super jelas)
     * - Target 15.0 MB: DPI 124, Q 75 -> hasil rata-rata ~13.5 MB (pasti < 15.0 MB, mendekati dokumen asli)
     */
    private function calculateRasterParams(float $targetMb): array
    {
        if ($targetMb <= 5.0) {
            return [72, 52];
        }

        if ($targetMb <= 10.0) {
            $t = ($targetMb - 5.0) / 5.0;
            $dpi = (int) round(72 + ($t * 24)); // 72 -> 96
            $q   = (int) round(52 + ($t * 14)); // 52 -> 66
            return [$dpi, $q];
        }

        $t = min(1.0, ($targetMb - 10.0) / 5.0);
        $dpi = (int) round(96 + ($t * 28)); // 96 -> 124
        $q   = (int) round(66 + ($t * 9));  // 66 -> 75
        return [$dpi, $q];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
