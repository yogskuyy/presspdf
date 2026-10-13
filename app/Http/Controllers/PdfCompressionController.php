<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class PdfCompressionController extends Controller
{
    const MIN_SIZE = 10 * 1024 * 1024; // 10 MB

    /**
     * Halaman utama.
     */
    public function index()
    {
        return view('pdf.index');
    }

    /**
     * Terima upload PDF, cek ukuran, kompres jika perlu.
     */
    public function compress(Request $request): JsonResponse
    {
        @ini_set('max_execution_time', '600');
        @set_time_limit(600);
        @ini_set('memory_limit', '512M');

        $request->validate([
            'file'        => 'required|file|mimes:pdf|max:512000',
            'target_size' => 'nullable|numeric|min:0.5|max:9.9',
        ], [
            'file.required' => 'Pilih file PDF terlebih dahulu.',
            'file.mimes'    => 'File harus berformat PDF.',
            'file.max'      => 'Ukuran file maksimal 500 MB.',
        ]);

        $file     = $request->file('file');
        $size     = $file->getSize();
        $filename = $file->getClientOriginalName();
        $targetMb = (float) $request->input('target_size', 5.5);
        $targetMb = max(0.5, min(9.8, $targetMb));

        // Jika sudah di bawah 10 MB → tolak, suruh tampilkan popup
        if ($size <= self::MIN_SIZE) {
            return response()->json([
                'status'     => 'small',
                'message'    => 'File sudah di bawah 10 MB',
                'size_human' => $this->formatBytes($size),
                'filename'   => $filename,
            ]);
        }

        // Simpan file sementara
        $token     = Str::random(32);
        $uploadDir = "temp/{$token}";
        Storage::disk('public')->makeDirectory($uploadDir);

        $inputPath  = $file->storeAs($uploadDir, 'input.pdf', 'public');
        $inputFull  = Storage::disk('public')->path($inputPath);
        $outputFull = Storage::disk('public')->path("{$uploadDir}/output.pdf");

        // Kompres dengan target ukuran yang dipilih user
        $compressed = $this->runCompression($inputFull, $outputFull, $targetMb);

        if (!$compressed || !file_exists($outputFull) || filesize($outputFull) === 0) {
            // Jika kompresi gagal, kirim file asli sebagai fallback
            copy($inputFull, $outputFull);
        }

        $compressedSize = filesize($outputFull);
        $saved          = $size - $compressedSize;
        $ratio          = round((1 - $compressedSize / $size) * 100, 1);

        // Hapus file input (hemat storage)
        Storage::disk('public')->delete($inputPath);

        // Jika ada batch_token (kompresi multi-file), catat ke sesi agar bisa di-download via ZIP
        $batchToken = $request->input('batch_token');
        if ($batchToken && preg_match('/^[a-zA-Z0-9]{32}$/', $batchToken)) {
            $batchList = session()->get("batch_{$batchToken}", []);
            $batchList[] = [
                'token'    => $token,
                'filename' => $filename,
            ];
            session()->put("batch_{$batchToken}", $batchList);
        }

        return response()->json([
            'status'          => 'compressed',
            'token'           => $token,
            'filename'        => $filename,
            'target_mb'       => $targetMb,
            'original_size'   => $size,
            'original_human'  => $this->formatBytes($size),
            'compressed_size' => $compressedSize,
            'compressed_human'=> $this->formatBytes($compressedSize),
            'saved_human'     => $this->formatBytes(max(0, $saved)),
            'ratio'           => max(0, $ratio),
            'download_url'    => route('pdf.download', $token),
            'batch_download_url' => $batchToken ? route('pdf.downloadBatch', $batchToken) : null,
        ]);
    }

    /**
     * Download hasil kompresi.
     */
    public function download(string $token)
    {
        // Validasi token (hanya huruf/angka, 32 karakter)
        if (!preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
            abort(404);
        }

        $filePath = Storage::disk('public')->path("temp/{$token}/output.pdf");

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan atau sudah kedaluwarsa.');
        }

        $downloadName = 'compressed_' . now()->format('Ymd_His') . '.pdf';

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/pdf',
        ])->deleteFileAfterSend(false); // Biarkan ada, bisa di-download lagi
    }

    /**
     * Download kumpulan hasil kompresi multi-file dalam 1 file ZIP.
     */
    public function downloadBatch(string $batchToken)
    {
        if (!preg_match('/^[a-zA-Z0-9]{32}$/', $batchToken)) {
            abort(404);
        }

        $tokens = session()->get("batch_{$batchToken}", []);
        if (empty($tokens)) {
            abort(404, 'Sesi unduhan batch tidak ditemukan atau sudah kedaluwarsa.');
        }

        $zipFileName = 'PressPDF_Compressed_' . now()->format('Ymd_His') . '.zip';
        $zipPath = Storage::disk('public')->path("temp/{$zipFileName}");

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Gagal membuat file arsip ZIP.');
        }

        $addedCount = 0;
        foreach ($tokens as $index => $item) {
            $token    = is_array($item) ? ($item['token'] ?? '') : $item;
            $origName = is_array($item) ? ($item['filename'] ?? "document_{$index}.pdf") : "document_{$index}.pdf";

            if (!$token || !preg_match('/^[a-zA-Z0-9]{32}$/', $token)) {
                continue;
            }

            $pdfPath = Storage::disk('public')->path("temp/{$token}/output.pdf");
            if (file_exists($pdfPath)) {
                $baseName = pathinfo($origName, PATHINFO_FILENAME);
                $entryName = "compressed_{$baseName}.pdf";
                $zip->addFile($pdfPath, $entryName);
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


    // ── PRIVATE ─────────────────────────────────────────────────────────

    private function runCompression(string $input, string $output, float $targetMb = 5.0): bool
    {
        $input  = str_replace('/', DIRECTORY_SEPARATOR, $input);
        $output = str_replace('/', DIRECTORY_SEPARATOR, $output);

        $targetBytes = (int) ($targetMb * 1024 * 1024);
        $gs = $this->findGhostscript();
        Log::info("runCompression called with input: {$input}, output: {$output}, target: {$targetMb} MB ({$targetBytes} bytes), GS path: " . ($gs ?? 'NULL'));

        if ($gs) {
            $tempDir = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app/temp_gs'));
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0777, true);
            }
            putenv("TEMP={$tempDir}");
            putenv("TMP={$tempDir}");

            $env = [
                'TEMP'       => $tempDir,
                'TMP'        => $tempDir,
                'SYSTEMROOT' => getenv('SYSTEMROOT') ?: 'C:\\Windows',
                'PATH'       => getenv('PATH') ?: '',
            ];

            // Hitung DPI target berdasarkan 3 zona utama:
            // 1) Kompresi Rendah (Maks 3.0 MB) -> ~72-75 DPI (~2 MB)
            // 2) Kompresi Disarankan (Maks 5.5 MB) -> ~125-130 DPI (~4.5 MB)
            // 3) Kompresi Ekstrem (Maks 9.8 MB) -> ~190-195 DPI (~8.8 MB)
            $dpi = $this->calculateTargetDpi($targetMb);

            // Profil kilat utama: Subsample downsampling tanpa -dOptimize (mengurangi overhead linearize hingga 70%)
            $cmd = [
                $gs,
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                '-dNOPAUSE', '-dQUIET', '-dBATCH',
                '-dCompressFonts=true',
                '-dSubsetFonts=true',
                '-dDownsampleColorImages=true',
                '-dDownsampleGrayImages=true',
                '-dColorImageDownsampleThreshold=1.0',
                '-dGrayImageDownsampleThreshold=1.0',
                "-dColorImageResolution={$dpi}",
                "-dGrayImageResolution={$dpi}",
                '-dColorImageDownsampleType=/Subsample',
                '-dGrayImageDownsampleType=/Subsample',
                '-dAutoFilterColorImages=false',
                '-dAutoFilterGrayImages=false',
                '-dColorImageFilter=/DCTEncode',
                '-dGrayImageFilter=/DCTEncode',
                "-sOutputFile={$output}",
                $input,
            ];

            $startTime = microtime(true);
            $process = new Process($cmd, null, $env);
            $process->setTimeout(180);
            $process->run();
            $elapsed = round(microtime(true) - $startTime, 2);

            $isOk = $process->isSuccessful();
            $curSize = (file_exists($output) && $isOk) ? filesize($output) : 0;
            Log::info("GS Fast single-pass finished in {$elapsed}s. Success: " . ($isOk ? 'YES' : 'NO') . ", Size: {$curSize}, TargetMax: {$targetBytes}");

            // Jika hasil kompresi berhasil
            if ($isOk && $curSize > 0) {
                // Pastikan ukuran file TIDAK MELEBIHI batas maksimum yang dipilih (strict max limit)
                if ($curSize > $targetBytes && $dpi > 55) {
                    $retryDpi = max(48, (int) round($dpi * 0.72));
                    $tempOutput = $output . '.retry.pdf';
                    $cmdRetry = $cmd;
                    $cmdRetry[10] = "-dColorImageResolution={$retryDpi}";
                    $cmdRetry[11] = "-dGrayImageResolution={$retryDpi}";
                    $cmdRetry[18] = "-sOutputFile={$tempOutput}";

                    $processRetry = new Process($cmdRetry, null, $env);
                    $processRetry->setTimeout(180);
                    $processRetry->run();

                    if ($processRetry->isSuccessful() && file_exists($tempOutput) && filesize($tempOutput) > 0) {
                        @unlink($output);
                        rename($tempOutput, $output);
                        Log::info("GS Quick retry completed to stay strictly under max size. New size: " . filesize($output));
                    }
                }
                return true;
            }
        }

        Log::warning("GS fallback triggered! Falling back to FPDI/TCPDF.");

        // Fallback: FPDI/TCPDF
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
            return file_exists($output) && filesize($output) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function findGhostscript(): ?string
    {
        // Prioritaskan file konsol (*c.exe) karena Windows GUI binary (*.exe tanpa c) akan membeku jika dijalankan di background
        $specificPaths = [
            'C:\\Program Files\\gs\\gs10.04.0\\bin\\gswin64c.exe',
            'C:\\Program Files (x86)\\gs\\gs10.04.0\\bin\\gswin32c.exe',
        ];

        foreach ($specificPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $candidates = ['gswin64c', 'gswin32c', 'gs', '/usr/bin/gs'];
        foreach (glob('C:\\Program Files\\gs\\*\\bin\\gswin*c.exe') ?: [] as $p) {
            array_unshift($candidates, $p);
        }

        foreach ($candidates as $c) {
            try {
                $p = new Process([$c, '--version']);
                $p->setTimeout(3);
                $p->run();
                if ($p->isSuccessful()) {
                    return $c;
                }
            } catch (\Throwable) {}
        }

        return null;
    }

    private function calculateTargetDpi(float $targetMb): int
    {
        // 3 Zona Target Presisi:
        // - Rendah (<= 3.0 MB): 55 - 75 DPI (~1.5 - 2.5 MB)
        // - Disarankan (3.0 - 5.5 MB): 75 - 130 DPI (~3.5 - 4.8 MB)
        // - Ekstrem/Kualitas Tinggi (5.5 - 9.8 MB): 130 - 195 DPI (~6.5 - 9.2 MB)
        if ($targetMb <= 3.0) {
            $t = max(0.0, ($targetMb - 1.0) / 2.0);
            return (int) round(55 + ($t * 20));
        } elseif ($targetMb <= 5.5) {
            $t = ($targetMb - 3.0) / 2.5;
            return (int) round(75 + ($t * 55));
        } else {
            $t = min(1.0, ($targetMb - 5.5) / 4.3);
            return (int) round(130 + ($t * 65));
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
