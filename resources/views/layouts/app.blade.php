<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PressPDF — Kompresi file PDF besar dengan cepat, aman, dan efisien. Upload file atau folder, filter otomatis > 10MB, unduh hasil kompresi.">
    <title>@yield('title', 'PressPDF — Smart PDF Compressor')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ===========================
           CSS VARIABLES & DESIGN TOKENS
           =========================== */
        :root {
            --primary-start: #6c63ff;
            --primary-end:   #a855f7;
            --primary-mid:   #8b5cf6;
            --accent-cyan:   #06b6d4;
            --accent-rose:   #f43f5e;
            --accent-amber:  #f59e0b;
            --accent-emerald:#10b981;

            --bg-dark:       #0a0a0f;
            --bg-card:       #111118;
            --bg-surface:    #16161f;
            --bg-surface2:   #1d1d2a;
            --bg-glass:      rgba(255, 255, 255, 0.04);
            --bg-glass-hover:rgba(255, 255, 255, 0.07);

            --border-subtle: rgba(255, 255, 255, 0.07);
            --border-accent: rgba(108, 99, 255, 0.3);

            --text-primary:  #f0f0ff;
            --text-secondary:#a0a0c0;
            --text-muted:    #60607a;

            --shadow-glow:   0 0 40px rgba(108, 99, 255, 0.15);
            --shadow-card:   0 8px 32px rgba(0, 0, 0, 0.4);
            --shadow-hover:  0 16px 48px rgba(108, 99, 255, 0.25);

            --radius-sm:     8px;
            --radius-md:     12px;
            --radius-lg:     16px;
            --radius-xl:     24px;
            --radius-pill:   999px;

            --transition:    all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.15s ease;
            --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ===========================
           RESET & BASE
           =========================== */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===========================
           ANIMATED BACKGROUND
           =========================== */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background:
                radial-gradient(ellipse 800px 600px at 20% -10%, rgba(108, 99, 255, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 600px 500px at 80% 110%, rgba(168, 85, 247, 0.10) 0%, transparent 60%),
                radial-gradient(ellipse 400px 400px at 50% 50%, rgba(6, 182, 212, 0.04) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        /* Animated particles/orbs */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
            animation: orbFloat 20s ease-in-out infinite;
        }
        .bg-orb-1 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(108,99,255,0.08), transparent);
            top: -100px; left: -100px;
            animation-duration: 25s;
        }
        .bg-orb-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(168,85,247,0.06), transparent);
            bottom: -150px; right: -150px;
            animation-duration: 30s;
            animation-delay: -10s;
        }
        .bg-orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(6,182,212,0.05), transparent);
            top: 50%; left: 60%;
            animation-duration: 20s;
            animation-delay: -5s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -20px) scale(1.05); }
            66%       { transform: translate(-20px, 30px) scale(0.95); }
        }

        /* ===========================
           LAYOUT
           =========================== */
        .app-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ===========================
           NAVBAR
           =========================== */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 40px;
            background: rgba(10, 10, 15, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-subtle);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .navbar-logo {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: white;
            box-shadow: 0 4px 15px rgba(108, 99, 255, 0.4);
        }
        .navbar-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #f0f0ff, var(--primary-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .navbar-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            border-radius: var(--radius-pill);
            color: white;
            letter-spacing: 0.5px;
        }

        .navbar-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .engine-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 500;
            border: 1px solid var(--border-subtle);
            background: var(--bg-glass);
        }
        .engine-badge.gs {
            border-color: rgba(16, 185, 129, 0.3);
            color: #10b981;
        }
        .engine-badge.fpdi {
            border-color: rgba(245, 158, 11, 0.3);
            color: #f59e0b;
        }
        .engine-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 6px currentColor;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* ===========================
           MAIN CONTENT
           =========================== */
        .main-content {
            flex: 1;
            padding: 48px 40px;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* ===========================
           TYPOGRAPHY HELPERS
           =========================== */
        .gradient-text {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end), var(--accent-cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ===========================
           GLASS CARD
           =========================== */
        .glass-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: var(--transition);
        }
        .glass-card:hover {
            border-color: var(--border-accent);
            box-shadow: var(--shadow-hover);
        }

        /* ===========================
           BUTTONS
           =========================== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            color: white;
            box-shadow: 0 4px 20px rgba(108, 99, 255, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(108, 99, 255, 0.6);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-secondary {
            background: var(--bg-glass);
            color: var(--text-primary);
            border: 1px solid var(--border-subtle);
        }
        .btn-secondary:hover {
            background: var(--bg-glass-hover);
            border-color: var(--border-accent);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #f43f5e);
            color: white;
            box-shadow: 0 4px 20px rgba(244, 63, 94, 0.3);
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(244, 63, 94, 0.5);
        }

        .btn-cyan {
            background: linear-gradient(135deg, #0891b2, #06b6d4);
            color: white;
            box-shadow: 0 4px 20px rgba(6, 182, 212, 0.3);
        }
        .btn-cyan:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.5);
        }

        .btn-sm {
            padding: 7px 14px;
            font-size: 12px;
            border-radius: var(--radius-sm);
        }
        .btn-lg {
            padding: 16px 32px;
            font-size: 16px;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ===========================
           LOADING SPINNER
           =========================== */
        .spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.2);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ===========================
           ALERTS / TOASTS
           =========================== */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .toast {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 500;
            max-width: 380px;
            pointer-events: all;
            backdrop-filter: blur(16px);
            border: 1px solid;
            animation: toastIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            box-shadow: var(--shadow-card);
        }
        .toast.hiding {
            animation: toastOut 0.3s ease forwards;
        }
        .toast-success {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        .toast-error {
            background: rgba(244, 63, 94, 0.15);
            border-color: rgba(244, 63, 94, 0.3);
            color: #fb7185;
        }
        .toast-info {
            background: rgba(108, 99, 255, 0.15);
            border-color: rgba(108, 99, 255, 0.3);
            color: #a78bfa;
        }
        .toast-warning {
            background: rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fbbf24;
        }
        @keyframes toastIn {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        @keyframes toastOut {
            from { transform: translateX(0); opacity: 1; }
            to   { transform: translateX(120%); opacity: 0; }
        }

        /* ===========================
           PROGRESS BAR
           =========================== */
        .progress-bar-wrap {
            height: 4px;
            background: rgba(255,255,255,0.08);
            border-radius: var(--radius-pill);
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: var(--radius-pill);
            background: linear-gradient(90deg, var(--primary-start), var(--accent-cyan));
            transition: width 0.5s ease;
            position: relative;
        }
        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 1.5s infinite;
        }
        @keyframes shimmer {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* ===========================
           FOOTER
           =========================== */
        .footer {
            text-align: center;
            padding: 24px 40px;
            border-top: 1px solid var(--border-subtle);
            color: var(--text-muted);
            font-size: 13px;
        }
        .footer a {
            color: var(--primary-mid);
            text-decoration: none;
        }
        .footer a:hover { text-decoration: underline; }

        /* ===========================
           RESPONSIVE
           =========================== */
        @media (max-width: 768px) {
            .navbar { padding: 14px 20px; }
            .main-content { padding: 32px 20px; }
        }
        @media (max-width: 480px) {
            .navbar-info { display: none; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Background Orbs -->
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-orb bg-orb-3"></div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="app-wrapper">
        <!-- Navbar -->
        <nav class="navbar" role="navigation" aria-label="Main navigation">
            <a href="{{ route('pdf.index') }}" class="navbar-brand" aria-label="PressPDF Home">
                <div class="navbar-logo" aria-hidden="true">
                    <i class="fa-solid fa-compress"></i>
                </div>
                <span class="navbar-name">PressPDF</span>
                <span class="navbar-badge">v1.0</span>
            </a>

            <div class="navbar-info" id="navbarInfo">
                @if(isset($engine))
                <div class="engine-badge {{ $engine === 'ghostscript' ? 'gs' : 'fpdi' }}">
                    <span class="engine-dot"></span>
                    @if($engine === 'ghostscript')
                        Ghostscript Engine
                    @else
                        FPDI Engine
                    @endif
                </div>
                @endif
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>Built with ❤️ using <strong>Laravel</strong> &bull; PressPDF &copy; {{ date('Y') }} &bull; Smart PDF Compression</p>
        </footer>
    </div>

    <!-- Global JS Utilities -->
    <script>
        // =====================
        // TOAST NOTIFICATION
        // =====================
        const Toast = {
            show(message, type = 'info', duration = 4000) {
                const icons = {
                    success: 'fa-circle-check',
                    error:   'fa-circle-xmark',
                    info:    'fa-circle-info',
                    warning: 'fa-triangle-exclamation',
                };
                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                toast.className = `toast toast-${type}`;
                toast.innerHTML = `
                    <i class="fa-solid ${icons[type] || icons.info}"></i>
                    <span>${message}</span>
                `;
                container.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('hiding');
                    setTimeout(() => toast.remove(), 300);
                }, duration);
            }
        };

        // =====================
        // FORMAT BYTES
        // =====================
        function formatBytes(bytes) {
            if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
            if (bytes >= 1048576)    return (bytes / 1048576).toFixed(2) + ' MB';
            if (bytes >= 1024)       return (bytes / 1024).toFixed(2) + ' KB';
            return bytes + ' B';
        }

        // =====================
        // CSRF TOKEN
        // =====================
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // =====================
        // FETCH WITH CSRF
        // =====================
        async function postJson(url, data) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });
            return response;
        }
    </script>

    @stack('scripts')
</body>
</html>
