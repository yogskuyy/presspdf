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
     * Endpoint diagnostik server untuk memverifikasi kesiapan Ghostscript, commit Git, dan konfigurasi Linux.
     */
    public function checkSystem(Request $request)
    {
        $gs = $this->findGhostscript();
        $gsVersion = null;
        $pdfimage24 = false;

        if ($gs) {
            try {
                $p = new Process([$gs, '--version']);
                $p->setTimeout(3);
                $p->run();
                if ($p->isSuccessful()) {
                    $gsVersion = trim($p->getOutput());
                }
            } catch (\Throwable) {}

            $pdfimage24 = $this->isDeviceSupported($gs, 'pdfimage24');
        }

        $gitCommit = null;
        try {
            $env = $this->getGsEnv();
            $pGit = new Process(['git', 'log', '-1', '--oneline'], null, $env);
            $pGit->setTimeout(3);
            $pGit->run();
            if ($pGit->isSuccessful()) {
                $gitCommit = trim($pGit->getOutput());
            }
        } catch (\Throwable) {}

        $procOpenEnabled = function_exists('proc_open');
        $opcacheEnabled  = function_exists('opcache_get_status') && !empty(opcache_get_status());
        $uploadMax       = ini_get('upload_max_filesize');
        $postMax         = ini_get('post_max_size');
        $memoryLimit     = ini_get('memory_limit');

        $status = 'READY';
        $issues = [];
        if (!$gs) {
            $status = 'WARNING_NO_GHOSTSCRIPT';
            $issues[] = 'Ghostscript belum terpasang di sistem Linux ini. Jalankan: sudo apt update && sudo apt install -y ghostscript';
        }
        if (!$procOpenEnabled) {
            $status = 'ERROR_PROC_OPEN';
            $issues[] = 'Fungsi proc_open dinonaktifkan di php.ini.';
        }

        $data = [
            'status'                => $status,
            'git_commit_server'     => $gitCommit ?? 'unknown (bukan git clone atau git belum terpasang)',
            'server_base_path'      => base_path(),
            'ghostscript_installed' => !empty($gs),
            'ghostscript_path'      => $gs ?? 'TIDAK DITEMUKAN',
            'ghostscript_version'   => $gsVersion ?? 'N/A',
            'pdfimage24_supported'  => $pdfimage24,
            'proc_open_enabled'     => $procOpenEnabled,
            'opcache_enabled'       => $opcacheEnabled,
            'php_version'           => PHP_VERSION,
            'upload_max_filesize'   => $uploadMax,
            'post_max_size'         => $postMax,
            'memory_limit'          => $memoryLimit,
            'issues'                => $issues,
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return response()->view('pdf.check_system', compact('data'));
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
            'file'          => 'required|file|mimes:pdf|max:2097152',
            'target_size'   => 'nullable|numeric|min:1.0|max:100.0',
            'threshold_mb'  => 'nullable|numeric|min:1.0|max:500.0',
            'batch_token'   => 'nullable|string|max:64',
            'relative_path' => 'nullable|string|max:500',
            'is_safe'       => 'nullable|string|in:0,1',
        ], [
            'file.required' => 'Pilih file PDF terlebih dahulu.',
            'file.mimes'    => 'File harus berformat PDF.',
            'file.max'      => 'Ukuran file per-berkas melebihi batas 2 GB.',
        ]);

        $file         = $request->file('file');
        $size         = $file->getSize();
        $filename     = $file->getClientOriginalName();
        $targetMb     = (float) $request->input('target_size', 12.0);
        $targetMb     = max(1.0, min(100.0, $targetMb));
        $batchToken   = $request->input('batch_token');
        $relativePath = $request->input('relative_path') ?: $filename;

        // Aturan Ambang Batas: Skala fleksibel (default 34 MB atau sesuai filter yang dipilih)
        $thresholdMb    = (float) $request->input('threshold_mb', 34.0);
        $thresholdMb    = max(1.0, min(500.0, $thresholdMb));
        $thresholdBytes = (int) ($thresholdMb * 1024 * 1024);

        $isSafeParam    = $request->input('is_safe');
        $shouldCompress = ($isSafeParam === '0' || ($isSafeParam === null && $size >= $thresholdBytes));
        if ($isSafeParam === '1') {
            $shouldCompress = false;
        }

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
                    $compressed = $this->runCompression($inputFull, $outputFull, $targetMb, $thresholdMb);
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
                'threshold_mb'       => $thresholdMb,
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

    private function runCompression(string $input, string $output, float $targetMb = 12.0, float $thresholdMb = 34.0): bool
    {
        $input  = str_replace('/', DIRECTORY_SEPARATOR, $input);
        $output = str_replace('/', DIRECTORY_SEPARATOR, $output);

        $targetBytes    = (int) ($targetMb * 1024 * 1024);
        $minTargetBytes = (int) ($targetBytes * 0.75); // Ambang batas bawah panduan (misal: target 16 MB -> min 12 MB)
        $thresholdBytes = (int) ($thresholdMb * 1024 * 1024);
        $inputSize      = file_exists($input) ? filesize($input) : 0;

        if ($inputSize <= 0) {
            return false;
        }

        $gs = $this->findGhostscript();
        Log::info("runCompression called: input={$input} (" . round($inputSize / 1048576, 2) . " MB), target={$targetMb} MB, GS=" . ($gs ?? 'NULL'));

        if ($gs) {
            $tempDir = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app/temp_gs'));
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0777, true);
            }
            $env = $this->getGsEnv($tempDir);

            // 1. Deteksi jumlah halaman secara akurat
            $pageCount = max(1, $this->getPdfPageCount($input));

            // 2. Adaptive Page-Aware Budgeting:
            // Alokasikan 97% dari targetBytes sebagai budget aman agar ukuran kompresi sedekat mungkin ke target
            // tanpa pernah melampaui batas portal klaim / batasan upload.
            $safeTargetBytes = (int) ($targetBytes * 0.97);
            $bytesPerPage    = $safeTargetBytes / $pageCount;

            // Estimasi DPI awal berbasis budget per halaman
            // Standar scan A4 DCTEncode: ~200 DPI menghasilkan ~410 KB/halaman pada preset /ebook
            $estDpi     = (int) round(200 * sqrt($bytesPerPage / 410000));
            $initialDpi = max(70, min(300, $estDpi));

            Log::info("Engine Adaptive Budget: Pages={$pageCount}, Budget/page=" . round($bytesPerPage / 1024, 1) . " KB, Initial DPI={$initialDpi}");

            $candidates = [];

            // Helper untuk mengeksekusi Ghostscript pdfwrite
            $runGsPass = function(int $dpi, string $preset, string $suffix) use ($gs, $input, $output, $env, &$candidates, $inputSize): int {
                $passOutput = $output . ".{$suffix}.pdf";
                @unlink($passOutput);
                $cmd = $this->buildGsCommand($gs, $input, $passOutput, $dpi, $preset);

                try {
                    $startTime = microtime(true);
                    $p = new Process($cmd, null, $env);
                    $p->setTimeout(300);
                    $p->run();
                    $elapsed = round(microtime(true) - $startTime, 2);
                    clearstatcache(true, $passOutput);

                    if ($p->isSuccessful() && file_exists($passOutput) && filesize($passOutput) > 0) {
                        $sz = filesize($passOutput);
                        Log::info("GS Pass {$suffix} (DPI {$dpi}, {$preset}) finished in {$elapsed}s. Size: {$sz} bytes (" . round($sz / 1048576, 2) . " MB)");
                        $candidates[] = [
                            'path'   => $passOutput,
                            'size'   => $sz,
                            'dpi'    => $dpi,
                            'preset' => $preset,
                        ];
                        return $sz;
                    } else {
                        @unlink($passOutput);
                    }
                } catch (\Throwable $e) {
                    Log::warning("GS Pass {$suffix} error: " . $e->getMessage());
                    @unlink($passOutput);
                }
                return 0;
            };

            // ── PASS 1: Estimasi presisi berbasis halaman ──
            $s1 = $runGsPass($initialDpi, '/ebook', 'p1');
            $pct1 = $s1 > 0 ? ($s1 / $targetBytes) : 0;

            // Evaluasi ketercapaian target:
            // Jika Pass 1 sudah mendarat di zona presisi emas (92% s.d. 100%), langsung anggap Bullseye!
            if ($pct1 >= 0.92 && $pct1 <= 1.00) {
                Log::info("Pass 1 Bullseye! Landed inside golden precision window: " . round($s1 / 1048576, 2) . " MB (" . round($pct1 * 100, 1) . "% of Target {$targetMb} MB)");
            } elseif ($s1 > $targetBytes) {
                // Ukuran melampaui target maksimum -> turunkan DPI secara proporsional kuadratik
                $scale = sqrt(($targetBytes * 0.95) / max(1, $s1));
                $dpi2  = max(55, min($initialDpi - 5, (int) floor($initialDpi * $scale)));
                Log::info("Pass 1 (" . round($s1 / 1048576, 2) . " MB) > Target ({$targetMb} MB). Stepping down DPI: {$initialDpi} -> {$dpi2}...");
                $s2 = $runGsPass($dpi2, '/ebook', 'p2_down');
                $pct2 = $s2 > 0 ? ($s2 / $targetBytes) : 0;

                // Jika Pass 2 masih sedikit di atas target, lakukan Pass 3 yang lebih aman
                if ($s2 > $targetBytes) {
                    $scale3 = sqrt(($targetBytes * 0.92) / max(1, $s2));
                    $dpi3   = max(45, min($dpi2 - 5, (int) floor($dpi2 * $scale3)));
                    Log::info("Pass 2 still over target. Safety Pass 3: {$dpi2} -> {$dpi3}...");
                    $runGsPass($dpi3, '/ebook', 'p3_safe');
                } elseif ($pct2 < 0.88 && $initialDpi > $dpi2 + 4) {
                    // Pass 2 terlalu turun di bawah target (<88%), padahal Pass 1 di atas target
                    // Interpolasi titik optimal antara DPI2 dan DPI1
                    $scale3 = sqrt(($targetBytes * 0.94) / max(1, $s2));
                    $dpi3   = max($dpi2 + 2, min($initialDpi - 2, (int) floor($dpi2 * $scale3)));
                    if ($dpi3 > $dpi2 + 2) {
                        Log::info("Pass 2 stepped down too much (" . round($pct2 * 100, 1) . "%). Interpolating Pass 3: DPI {$dpi3}...");
                        $runGsPass($dpi3, '/ebook', 'p3_interp');
                    }
                }
            } elseif ($s1 > 0 && $pct1 < 0.92) {
                // Ukuran di bawah 92% target -> naikkan DPI untuk meningkatkan kualitas visual & mendekati batas target
                $scale = sqrt(($targetBytes * 0.97) / max(1, $s1));
                $dpi2  = min(320, max($initialDpi + 5, (int) round($initialDpi * $scale)));

                // Jika dokumen memiliki sedikit halaman sehingga di DPI tinggi ukurannya masih sangat kecil (<60%),
                // aktifkan preset /printer dengan tabel kuantisasi JPEG yang lebih tinggi
                $preset2 = '/ebook';
                if ($pct1 < 0.60 && $dpi2 >= 260) {
                    $preset2 = '/printer';
                    $dpi2    = min(220, max(120, (int) round($dpi2 * 0.75)));
                    Log::info("File is small at high DPI. Switching to /printer high-clarity preset (DPI {$dpi2})...");
                } else {
                    Log::info("Pass 1 size (" . round($s1 / 1048576, 2) . " MB) < 92% target. Stepping up DPI: {$initialDpi} -> {$dpi2}...");
                }

                $s2 = $runGsPass($dpi2, $preset2, 'p2_up');
                $pct2 = $s2 > 0 ? ($s2 / $targetBytes) : 0;

                // Evaluasi Pass 3 jika Pass 2 overshot atau masih bisa dimaksimalkan
                if ($s2 > $targetBytes) {
                    // Pass 2 overshot -> interpolasi titik tengah aman
                    $scale3 = sqrt(($targetBytes * 0.94) / max(1, $s2));
                    $dpi3   = max($initialDpi + 2, min($dpi2 - 3, (int) floor($dpi2 * $scale3)));
                    if ($dpi3 < $dpi2 && $dpi3 > $initialDpi) {
                        Log::info("Pass 2 overshot. Interpolating Pass 3: DPI {$dpi3}...");
                        $runGsPass($dpi3, $preset2, 'p3_interp');
                    }
                } elseif ($pct2 < 0.88 && $dpi2 < 300) {
                    // Masih ada ruang headroom untuk mendekatkan lagi ke target
                    $scale3 = sqrt(($targetBytes * 0.98) / max(1, $s2));
                    $dpi3   = min(320, (int) ceil($dpi2 * $scale3));
                    if ($dpi3 > $dpi2 + 4) {
                        Log::info("Pass 2 still has headroom. Final nudge Pass 3: {$dpi2} -> {$dpi3}...");
                        $runGsPass($dpi3, $preset2, 'p3_nudge');
                    }
                }
            }

            // Cek apakah ada kandidat yang valid (<= targetBytes & < inputSize)
            $hasValid = false;
            foreach ($candidates as $c) {
                if ($c['size'] <= $targetBytes && $c['size'] < $inputSize) {
                    $hasValid = true;
                    break;
                }
            }

            // PASS 3: Fallback Smart Rasterizer jika dokumen adalah scan murni / vektor yang tidak mempan pdfwrite
            if (!$hasValid && $this->isDeviceSupported($gs, 'pdfimage24', $env)) {
                [$rasterDpi, $jpegQ] = $this->calculateRasterParams($targetMb, $input);
                Log::info("No valid pdfwrite candidate <= target. Running calibrated pdfimage24 rasterizer ({$rasterDpi} DPI, Q={$jpegQ})...");

                $tempRasterOutput = $output . '.raster.pdf';
                @unlink($tempRasterOutput);

                $cmdRaster = [
                    $gs,
                    '-sDEVICE=pdfimage24',
                    "-r{$rasterDpi}",
                    "-dJPEGQ={$jpegQ}",
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
                        $candidates[] = [
                            'path'   => $tempRasterOutput,
                            'size'   => $rSize,
                            'dpi'    => $rasterDpi,
                            'preset' => 'rasterizer',
                        ];
                    }
                } catch (\Throwable $e) {
                    Log::warning("pdfimage24 error: " . $e->getMessage());
                }
            }

            // ── SELEKSI PEMENANG OPTIMAL (Precision Winner Selection) ──
            // Kriteria:
            // 1. Ukuran HARUS <= targetBytes (TIDAK BOLEH melebihi batas maksimal target pengguna / portal BPJS)
            // 2. Ukuran HARUS < inputSize (berkas harus terkompresi / tidak membesar)
            // 3. Di antara kandidat valid, pilih kandidat TERBESAR yang paling mendekati batas target (kualitas gambar, teks, dan barcode paling jernih)
            $validCandidates = array_filter($candidates, function($c) use ($targetBytes, $inputSize) {
                return $c['size'] <= $targetBytes && $c['size'] < $inputSize && file_exists($c['path']) && $c['size'] > 0;
            });

            if (!empty($validCandidates)) {
                usort($validCandidates, function($a, $b) {
                    return $b['size'] <=> $a['size'];
                });

                $winner = $validCandidates[0];
                $winMb  = round($winner['size'] / 1048576, 2);
                $winPct = round(($winner['size'] / $targetBytes) * 100, 1);
                Log::info("🎯 Selected precision winner: {$winMb} MB ({$winPct}% of Target {$targetMb} MB) at DPI {$winner['dpi']}");

                @unlink($output);
                rename($winner['path'], $output);
                clearstatcache(true, $output);

                // Bersihkan temporary files kandidat lainnya
                foreach ($candidates as $c) {
                    if ($c['path'] !== $output && file_exists($c['path'])) {
                        @unlink($c['path']);
                    }
                }

                return true;
            }

            // Jika semua pass melebihi target, ambil yang paling kecil atau pertahankan file asli jika tidak ada yang lebih kecil
            $smallerThanInput = array_filter($candidates, function($c) use ($inputSize) {
                return $c['size'] < $inputSize && file_exists($c['path']);
            });
            if (!empty($smallerThanInput)) {
                usort($smallerThanInput, fn($a, $b) => $a['size'] <=> $b['size']);
                $bestFallback = $smallerThanInput[0];
                @unlink($output);
                rename($bestFallback['path'], $output);
                clearstatcache(true, $output);
                foreach ($candidates as $c) {
                    if ($c['path'] !== $output && file_exists($c['path'])) @unlink($c['path']);
                }
                return true;
            }

            // Anti-Expansion fallback: jika file asli sudah optimal, copy as-is
            @unlink($output);
            copy($input, $output);
            clearstatcache(true, $output);
            foreach ($candidates as $c) {
                if (file_exists($c['path'])) @unlink($c['path']);
            }
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

    private function isDeviceSupported(string $gs, string $device, ?array $env = null): bool
    {
        static $deviceCache = [];
        $key = "{$gs}_{$device}";
        if (isset($deviceCache[$key])) {
            return $deviceCache[$key];
        }

        try {
            $env = $env ?? $this->getGsEnv();
            $nullOut = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
            $p = new Process([$gs, "-sDEVICE={$device}", '-dNOPAUSE', '-dBATCH', "-sOutputFile={$nullOut}", '-c', 'quit'], null, $env);
            $p->setTimeout(5);
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
            $searchDirs = [
                'C:\\Program Files\\gs',
                'C:\\Program Files (x86)\\gs',
                'C:\\gs',
                'D:\\Program Files\\gs',
                'C:\\laragon\\bin\\gs',
            ];

            foreach ($searchDirs as $dir) {
                if (is_dir($dir)) {
                    foreach (glob($dir . '\\*\\bin\\gswin*c.exe') ?: [] as $p) {
                        $candidates[] = $p;
                    }
                    foreach (glob($dir . '\\*\\bin\\gswin*.exe') ?: [] as $p) {
                        $candidates[] = $p;
                    }
                }
            }

            try {
                $whereOut = trim(shell_exec('where gswin64c 2>NUL') ?? '');
                if ($whereOut !== '') {
                    foreach (explode("\n", str_replace("\r", "", $whereOut)) as $line) {
                        $trimmed = trim($line);
                        if ($trimmed !== '' && file_exists($trimmed)) {
                            $candidates[] = $trimmed;
                        }
                    }
                }
                $whereGs = trim(shell_exec('where gs 2>NUL') ?? '');
                if ($whereGs !== '') {
                    foreach (explode("\n", str_replace("\r", "", $whereGs)) as $line) {
                        $trimmed = trim($line);
                        if ($trimmed !== '' && file_exists($trimmed)) {
                            $candidates[] = $trimmed;
                        }
                    }
                }
            } catch (\Throwable) {}

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

    private function getGsEnv(?string $tempDir = null): array
    {
        if (!$tempDir) {
            $tempDir = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app/temp_gs'));
        }
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }

        putenv("TEMP={$tempDir}");
        putenv("TMP={$tempDir}");
        putenv("TMPDIR={$tempDir}");

        $pathEnv = getenv('PATH') ?: '';
        if (PHP_OS_FAMILY === 'Windows') {
            $winExtras = [
                'C:\\Program Files\\Git\\cmd',
                'C:\\Program Files\\gs\\gs10.04.0\\bin',
            ];
            foreach ($winExtras as $dir) {
                if (is_dir($dir) && !str_contains($pathEnv, $dir)) {
                    $pathEnv = $pathEnv ? ($pathEnv . ';' . $dir) : $dir;
                }
            }
        } else {
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
            $env['WINDIR']     = getenv('WINDIR') ?: 'C:\\Windows';
        }

        return $env;
    }

    private function getPdfPageCount(string $filePath): int
    {
        try {
            $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
            $count = (int) $pdf->setSourceFile($filePath);
            if ($count > 0) return $count;
        } catch (\Throwable) {}

        // Regex fallback untuk membaca /Count dari catalog/pages tree PDF
        try {
            $handle = @fopen($filePath, 'rb');
            if ($handle) {
                $content = fread($handle, min(2097152, filesize($filePath)));
                fclose($handle);
                if (preg_match_all('/\/Count\s+(\d+)/', $content, $matches)) {
                    $maxCount = 0;
                    foreach ($matches[1] as $cVal) {
                        $cInt = (int) $cVal;
                        if ($cInt > $maxCount) $maxCount = $cInt;
                    }
                    if ($maxCount > 0) return $maxCount;
                }
            }
        } catch (\Throwable) {}

        return 1;
    }

    private function calculateTargetDpi(float $targetMb): int
    {
        [$dpi] = $this->calculateRasterParams($targetMb);
        return $dpi;
    }

    /**
     * Parameter resolusi & kompresi JPEG terkalibrasi presisi tinggi untuk fallback rasterizer:
     * Menyesuaikan DPI dan JPEG quality secara dinamis berdasarkan target ukuran dan jumlah halaman berkas.
     */
    private function calculateRasterParams(float $targetMb, ?string $inputPath = null): array
    {
        $pageCount = $inputPath ? $this->getPdfPageCount($inputPath) : 1;
        $targetBytes = $targetMb * 1024 * 1024;
        $safeTargetBytes = (int) ($targetBytes * 0.95);
        $bytesPerPage = $safeTargetBytes / max(1, $pageCount);

        if ($bytesPerPage >= 400000) {
            return [150, 85];
        } elseif ($bytesPerPage >= 250000) {
            return [135, 80];
        } elseif ($bytesPerPage >= 150000) {
            return [120, 75];
        } elseif ($bytesPerPage >= 90000) {
            return [105, 70];
        } elseif ($bytesPerPage >= 55000) {
            return [90, 65];
        } elseif ($bytesPerPage >= 35000) {
            return [80, 58];
        } elseif ($bytesPerPage >= 22000) {
            return [70, 50];
        } else {
            return [60, 42];
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
