<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PressPDF — Diagnostik Server Linux</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0b0f19;
            color: #e2e8f0;
            padding: 40px 20px;
            margin: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 850px;
            margin: 0 auto;
        }
        .card {
            background: #111827;
            border: 1px solid #1f2937;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
            margin-bottom: 24px;
        }
        h1 {
            font-size: 24px;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #f8fafc;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }
        .badge-success { background: rgba(5, 150, 105, 0.2); color: #34d399; border: 1px solid rgba(5, 150, 105, 0.4); }
        .badge-danger { background: rgba(225, 29, 72, 0.2); color: #fb7185; border: 1px solid rgba(225, 29, 72, 0.4); }
        .badge-warning { background: rgba(217, 119, 6, 0.2); color: #fbbf24; border: 1px solid rgba(217, 119, 6, 0.4); }

        .table-diag {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        .table-diag th, .table-diag td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #1e293b;
        }
        .table-diag th {
            color: #94a3b8;
            font-weight: 600;
            width: 35%;
        }
        .table-diag td {
            font-family: 'JetBrains Mono', monospace;
            color: #f1f5f9;
        }
        .alert-box {
            padding: 18px 20px;
            border-radius: 12px;
            margin-top: 20px;
            background: rgba(225, 29, 72, 0.12);
            border: 1px solid rgba(225, 29, 72, 0.35);
            color: #fda4af;
        }
        .code-block {
            background: #050811;
            border: 1px solid #1e293b;
            padding: 14px;
            border-radius: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: #38bdf8;
            margin-top: 10px;
            overflow-x: auto;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #4f46e5;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>
                <i class="fa-solid fa-microchip" style="color: #6366f1;"></i>
                Diagnostik Sistem Linux PressPDF
            </h1>
            <p style="color: #94a3b8; font-size: 14px;">
                Halaman ini memverifikasi environment server Linux dan kesiapan Ghostscript secara transparan.
            </p>

            <table class="table-diag">
                <tr>
                    <th>Status Server</th>
                    <td>
                        @if($data['status'] === 'READY')
                            <span class="badge badge-success"><i class="fa-solid fa-check"></i> SIAP &amp; AKTIF</span>
                        @else
                            <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> PERLU PERBAIKAN</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Folder Web di Server (Base Path)</th>
                    <td style="color: #38bdf8;">{{ $data['server_base_path'] }}</td>
                </tr>
                <tr>
                    <th>Commit Git di Server</th>
                    <td>{{ $data['git_commit_server'] }}</td>
                </tr>
                <tr>
                    <th>Ghostscript Terpasang</th>
                    <td>
                        @if($data['ghostscript_installed'])
                            <span class="badge badge-success"><i class="fa-solid fa-check"></i> YA ({{ $data['ghostscript_version'] }})</span>
                        @else
                            <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> TIDAK TERPASANG</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Path Ghostscript</th>
                    <td>{{ $data['ghostscript_path'] }}</td>
                </tr>
                <tr>
                    <th>Engine pdfimage24 (Akselerasi)</th>
                    <td>
                        @if($data['pdfimage24_supported'])
                            <span class="badge badge-success"><i class="fa-solid fa-bolt"></i> DIDUKUNG (SUPER CEPAT)</span>
                        @else
                            <span class="badge badge-warning"><i class="fa-solid fa-info"></i> Standard Fallback (pdfwrite)</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Fungsi proc_open (Eksekusi Engine)</th>
                    <td>
                        @if($data['proc_open_enabled'])
                            <span class="badge badge-success">AKTIF</span>
                        @else
                            <span class="badge badge-danger">TERBLOKIR DI PHP.INI</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>OPcache Aktif di PHP</th>
                    <td>
                        @if($data['opcache_enabled'])
                            <span class="badge badge-warning">AKTIF (Perlu restart PHP-FPM saat update kode)</span>
                        @else
                            <span class="badge badge-success">OFF / DYNAMIC</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Limit Upload &amp; Memori PHP</th>
                    <td>Upload: {{ $data['upload_max_filesize'] }} | Memory: {{ $data['memory_limit'] }}</td>
                </tr>
            </table>

            @if(!empty($data['issues']))
                <div class="alert-box">
                    <strong style="color: #f43f5e;"><i class="fa-solid fa-triangle-exclamation"></i> Hal yang Perlu Diperbaiki di Server Linux:</strong>
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        @foreach($data['issues'] as $issue)
                            <li>{{ $issue }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="margin-top: 30px;">
                <div style="font-size: 14px; font-weight: 700; color: #f8fafc; margin-bottom: 6px;">
                    Perintah Sinkronisasi &amp; Refresh Lengkap (Salin &amp; Tempel di Terminal Server Linux):
                </div>
                <div class="code-block">
cd {{ $data['server_base_path'] }}<br>
sudo git fetch origin<br>
sudo git reset --hard origin/main<br>
sudo php artisan optimize:clear<br>
sudo systemctl restart php8.2-fpm || sudo systemctl restart apache2
                </div>
            </div>

            <a href="{{ route('pdf.index') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Studio PressPDF
            </a>
        </div>
    </div>
</body>
</html>
