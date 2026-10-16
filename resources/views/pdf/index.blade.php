<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PressPDF — YGSKY Studio kompresi dokumen PDF modern, cepat, dan presisi. Otomatis deteksi berkas besar dengan struktur folder utuh.">
    <title>PressPDF — Modern Document Studio &amp; Compressor</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    :root {
        --font-main: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        --font-mono: 'JetBrains Mono', monospace;

        /* Smooth Modern Palette */
        --bg-base: #f8fafc;
        --bg-card: rgba(255, 255, 255, 0.94);
        --bg-card-solid: #ffffff;
        --bg-elevated: #f1f5f9;
        --border-subtle: #e2e8f0;
        --border-hover: #cbd5e1;

        --text-primary: #0f172a;
        --text-secondary: #475569;
        --text-muted: #64748b;

        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --primary-light: #eef2ff;
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);

        --accent-red: #e11d48;
        --accent-red-light: #ffe4e6;
        --accent-green: #059669;
        --accent-green-light: #d1fae5;
        --accent-amber: #d97706;
        --accent-amber-light: #fef3c7;
        --accent-blue: #0284c7;
        --accent-blue-light: #e0f2fe;

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
        --shadow-md: 0 4px 16px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
        --shadow-lg: 0 12px 32px -4px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
        --shadow-hover: 0 16px 36px -6px rgba(79, 70, 229, 0.12), 0 6px 16px -3px rgba(15, 23, 42, 0.06);

        --radius-lg: 20px;
        --radius-md: 14px;
        --radius-sm: 10px;
    }

    [data-theme="dark-titanium"] {
        --bg-base: #0b0f19;
        --bg-card: rgba(17, 24, 39, 0.88);
        --bg-card-solid: #111827;
        --bg-elevated: #1e293b;
        --border-subtle: #242f42;
        --border-hover: #374151;

        --text-primary: #f8fafc;
        --text-secondary: #cbd5e1;
        --text-muted: #94a3b8;

        --primary: #6366f1;
        --primary-hover: #4f46e5;
        --primary-light: rgba(99, 102, 241, 0.15);
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);

        --accent-red: #fb7185;
        --accent-red-light: rgba(244, 63, 94, 0.16);
        --accent-green: #34d399;
        --accent-green-light: rgba(16, 185, 129, 0.16);
        --accent-amber: #fbbf24;
        --accent-amber-light: rgba(245, 158, 11, 0.16);
        --accent-blue: #38bdf8;
        --accent-blue-light: rgba(14, 165, 233, 0.16);

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.4);
        --shadow-md: 0 6px 20px rgba(0, 0, 0, 0.35);
        --shadow-lg: 0 16px 36px rgba(0, 0, 0, 0.5);
        --shadow-hover: 0 20px 40px rgba(99, 102, 241, 0.2);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
    }

    body {
        font-family: var(--font-main);
        background-color: var(--bg-base);
        background-image:
            radial-gradient(at 10% 10%, rgba(99, 102, 241, 0.06) 0px, transparent 50%),
            radial-gradient(at 90% 15%, rgba(244, 63, 94, 0.04) 0px, transparent 45%),
            radial-gradient(at 50% 90%, rgba(14, 165, 233, 0.05) 0px, transparent 55%);
        background-attachment: fixed;
        color: var(--text-primary);
        line-height: 1.55;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* AMBIENT FLOATING BACKGROUND CANVAS */
    #ambientFloatingCanvas {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 0;
        opacity: 0.9;
    }

    .app-viewport {
        position: relative;
        z-index: 1;
        max-width: 1280px;
        margin: 0 auto;
        padding: 24px 20px 60px;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 24px;
        flex: 1;
    }

    /* TOPBAR */
    .app-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        background: var(--bg-card);
        backdrop-filter: blur(16px);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .brand-cluster {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .brand-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 20px;
        box-shadow: 0 6px 14px rgba(79, 70, 229, 0.25);
    }

    .brand-title {
        font-size: 19px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .brand-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 99px;
        background: var(--primary-light);
        color: var(--primary);
    }

    .brand-subline {
        font-size: 12.5px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .theme-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        font-family: var(--font-main);
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .theme-toggle-btn:hover {
        color: var(--text-primary);
        border-color: var(--border-hover);
        background: var(--bg-card-solid);
    }

    /* STEPPER BAR */
    .workflow-stepper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    @media (max-width: 860px) {
        .workflow-stepper {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .step-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        background: var(--bg-card);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        box-shadow: var(--shadow-sm);
    }

    .step-pill.active {
        border-color: rgba(99, 102, 241, 0.3);
        background: var(--bg-card-solid);
    }

    .step-num {
        width: 24px;
        height: 24px;
        border-radius: 99px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* MAIN TWO-COLUMN GRID */
    .main-workspace-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 980px) {
        .main-workspace-grid {
            grid-template-columns: 1fr;
        }
    }

    /* CARD CONTAINER */
    .modern-card {
        background: var(--bg-card);
        backdrop-filter: blur(16px);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-subtle);
        background: var(--bg-card-solid);
    }

    .card-title {
        font-size: 14.5px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-badge {
        font-size: 11.5px;
        font-family: var(--font-mono);
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 99px;
        background: var(--bg-elevated);
        color: var(--text-muted);
    }

    .card-body {
        padding: 24px;
    }

    /* DROPZONE */
    .dropzone-view {
        border: 2px dashed var(--border-hover);
        border-radius: var(--radius-md);
        padding: 44px 24px;
        text-align: center;
        cursor: pointer;
        background: rgba(248, 250, 252, 0.4);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }

    [data-theme="dark-titanium"] .dropzone-view {
        background: rgba(17, 24, 39, 0.4);
    }

    .dropzone-view:hover, .dropzone-view.drag-active {
        border-color: var(--primary);
        background: var(--primary-light);
        transform: translateY(-2px);
    }

    .dz-icon-circle {
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 28px;
        transition: transform 0.2s ease;
    }

    .dropzone-view:hover .dz-icon-circle {
        transform: scale(1.06);
    }

    .dz-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .dz-desc {
        font-size: 13px;
        color: var(--text-secondary);
        max-width: 480px;
        line-height: 1.5;
    }

    .dz-buttons-row {
        display: flex;
        gap: 12px;
        margin-top: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn-primary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        background: var(--primary-gradient);
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-primary-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
    }

    .btn-secondary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: var(--radius-sm);
        background: var(--bg-card-solid);
        color: var(--text-primary);
        border: 1px solid var(--border-subtle);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-secondary-pill:hover {
        background: var(--bg-elevated);
        border-color: var(--border-hover);
    }

    .dz-chips-ribbon {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 8px;
    }

    .dz-chip {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 99px;
        background: var(--bg-elevated);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* WORKSPACE QUEUE */
    .workspace-view {
        display: none;
        flex-direction: column;
        gap: 16px;
    }

    .workspace-view.show {
        display: flex;
    }

    .queue-summary-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-radius: var(--radius-md);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        flex-wrap: wrap;
        gap: 12px;
    }

    .queue-folder-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .folder-icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .queue-stat-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .stat-badge-pill {
        font-size: 12px;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 99px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-card-solid);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
    }

    .stat-badge-pill.needs {
        background: var(--accent-red-light);
        border-color: rgba(225, 29, 72, 0.2);
        color: var(--accent-red);
    }

    .stat-badge-pill.safe {
        background: var(--accent-green-light);
        border-color: rgba(5, 150, 105, 0.2);
        color: var(--accent-green);
    }

    /* TABS */
    .filter-tabs-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .tab-pills-wrap {
        display: flex;
        gap: 6px;
        background: var(--bg-elevated);
        padding: 4px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-subtle);
    }

    .filter-tab-btn {
        padding: 6px 14px;
        border-radius: 8px;
        font-family: var(--font-main);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-tab-btn.active {
        background: var(--bg-card-solid);
        color: var(--text-primary);
        box-shadow: var(--shadow-sm);
    }

    .queue-action-btns {
        display: flex;
        gap: 8px;
    }

    .btn-add-mini {
        padding: 6px 12px;
        border-radius: 8px;
        font-family: var(--font-main);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--bg-card-solid);
        border: 1px solid var(--border-subtle);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .btn-add-mini:hover {
        background: var(--bg-elevated);
        border-color: var(--border-hover);
        color: var(--text-primary);
    }

    /* QUEUE SCROLL TRAY */
    .files-list-tray {
        max-height: 480px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-right: 4px;
    }

    .files-list-tray::-webkit-scrollbar {
        width: 6px;
    }
    .files-list-tray::-webkit-scrollbar-thumb {
        background: var(--border-hover);
        border-radius: 99px;
    }

    .file-item-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        background: var(--bg-card-solid);
        border: 1px solid var(--border-subtle);
        gap: 14px;
        transition: border-color 0.15s ease;
    }

    .file-item-card:hover {
        border-color: var(--border-hover);
    }

    .file-item-card.needs-comp {
        border-left: 3px solid var(--accent-red);
    }

    .file-item-card.safe-file {
        border-left: 3px solid var(--accent-green);
    }

    .file-main-info {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1;
    }

    .file-doc-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: var(--bg-elevated);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
        color: var(--accent-red);
    }

    .file-doc-icon.safe {
        color: var(--accent-green);
    }

    .file-text-col {
        min-width: 0;
        flex: 1;
    }

    .file-name-text {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-path-text {
        font-size: 11px;
        font-family: var(--font-mono);
        color: var(--text-muted);
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-pills-col {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .size-pill-tag {
        font-size: 11.5px;
        font-family: var(--font-mono);
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: var(--bg-elevated);
        color: var(--text-secondary);
    }

    .status-pill-chip {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 99px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-pill-chip.needs-comp {
        background: var(--accent-red-light);
        color: var(--accent-red);
    }

    .status-pill-chip.safe-skip {
        background: var(--accent-green-light);
        color: var(--accent-green);
    }

    .status-pill-chip.compressing {
        background: var(--accent-blue-light);
        color: var(--accent-blue);
    }

    .status-pill-chip.success {
        background: var(--accent-green-light);
        color: var(--accent-green);
    }

    .status-pill-chip.error {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-file-del {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        transition: all 0.15s ease;
    }

    .btn-file-del:hover {
        background: var(--accent-red-light);
        color: var(--accent-red);
        border-color: rgba(225, 29, 72, 0.2);
    }

    /* RIGHT MODULE - TARGET PRESETS */
    .presets-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .preset-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-radius: var(--radius-md);
        background: var(--bg-card-solid);
        border: 2px solid var(--border-subtle);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        gap: 14px;
        position: relative;
    }

    .preset-card:hover {
        border-color: var(--border-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .preset-card.active {
        border-color: var(--primary);
        background: var(--primary-light);
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.12);
    }

    .preset-card-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .preset-radio-indicator {
        width: 20px;
        height: 20px;
        border-radius: 99px;
        border: 2px solid var(--border-hover);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .preset-card.active .preset-radio-indicator {
        border-color: var(--primary);
        background: var(--primary);
    }

    .preset-radio-dot {
        width: 8px;
        height: 8px;
        border-radius: 99px;
        background: #ffffff;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s ease;
    }

    .preset-card.active .preset-radio-dot {
        opacity: 1;
        transform: scale(1);
    }

    .preset-title-wrap {
        display: flex;
        flex-direction: column;
    }

    .preset-name {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .preset-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .preset-badge-rec {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 99px;
        background: var(--primary);
        color: #ffffff;
    }

    .preset-size-num {
        font-size: 18px;
        font-family: var(--font-mono);
        font-weight: 800;
        color: var(--primary);
        flex-shrink: 0;
    }

    /* SLIDER DRAWER */
    .slider-accordion-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        margin-top: 4px;
        transition: all 0.15s ease;
    }

    .slider-accordion-toggle:hover {
        background: var(--bg-card-solid);
        color: var(--text-primary);
    }

    .slider-drawer {
        display: none;
        flex-direction: column;
        gap: 10px;
        padding: 16px;
        background: var(--bg-elevated);
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-subtle);
        margin-top: 6px;
    }

    .slider-drawer.show {
        display: flex;
    }

    .modern-range-input {
        width: 100%;
        accent-color: var(--primary);
        height: 6px;
        border-radius: 99px;
        cursor: pointer;
    }

    .range-ticks-text {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
        font-family: var(--font-mono);
        color: var(--text-muted);
    }

    /* HINT BANNER */
    .mode-hint-banner {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        background: var(--primary-light);
        border: 1px solid rgba(99, 102, 241, 0.2);
        color: var(--primary);
        font-size: 12.5px;
        margin-top: 14px;
    }

    /* MASTER ACTIONS */
    .master-action-row {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-compress-master {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 24px;
        border-radius: var(--radius-md);
        background: var(--primary-gradient);
        color: #ffffff;
        font-family: var(--font-main);
        font-size: 14.5px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(79, 70, 229, 0.3);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-compress-master:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
    }

    .btn-compress-master:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    .btn-cancel-clean {
        padding: 14px 20px;
        border-radius: var(--radius-md);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        font-family: var(--font-main);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-cancel-clean:hover {
        background: var(--bg-card-solid);
        color: var(--text-primary);
        border-color: var(--border-hover);
    }

    /* PROGRESS BAY */
    .progress-bay {
        display: none;
        flex-direction: column;
        gap: 8px;
        margin-top: 16px;
        padding: 16px;
        border-radius: var(--radius-md);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
    }

    .progress-bay.show {
        display: flex;
    }

    .progress-labels {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12.5px;
        font-weight: 600;
    }

    .progress-track {
        height: 8px;
        border-radius: 99px;
        background: var(--border-subtle);
        overflow: hidden;
    }

    .progress-fill-bar {
        height: 100%;
        width: 0%;
        background: var(--primary-gradient);
        border-radius: 99px;
        transition: width 0.3s ease;
    }

    /* SPINNER */
    .mini-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* SPECIFICATIONS GRID */
    .specs-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }

    @media (max-width: 900px) {
        .specs-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 500px) {
        .specs-grid {
            grid-template-columns: 1fr;
        }
    }

    .spec-card {
        padding: 20px;
        border-radius: var(--radius-lg);
        background: var(--bg-card);
        backdrop-filter: blur(12px);
        border: 1px solid var(--border-subtle);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .spec-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .spec-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .spec-icon-box.red { background: var(--accent-red-light); color: var(--accent-red); }
    .spec-icon-box.amber { background: var(--accent-amber-light); color: var(--accent-amber); }
    .spec-icon-box.blue { background: var(--accent-blue-light); color: var(--accent-blue); }
    .spec-icon-box.green { background: var(--accent-green-light); color: var(--accent-green); }

    .spec-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
    }

    .spec-desc {
        font-size: 12px;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .spec-tag-sub {
        font-size: 10.5px;
        font-family: var(--font-mono);
        font-weight: 700;
        color: var(--text-muted);
        margin-top: auto;
    }

    /* RESULTS SECTION */
    .result-section {
        display: none;
        flex-direction: column;
        gap: 20px;
    }

    .result-section.show {
        display: flex;
    }

    .results-banner {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
        background: var(--bg-card-solid);
        border-bottom: 1px solid var(--border-subtle);
    }

    .success-check-badge {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--accent-green-light);
        color: var(--accent-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .results-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        padding: 20px 24px;
        background: var(--bg-elevated);
        border-bottom: 1px solid var(--border-subtle);
    }

    @media (max-width: 640px) {
        .results-stats-row {
            grid-template-columns: 1fr;
        }
    }

    .res-stat-box {
        background: var(--bg-card-solid);
        padding: 14px 18px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-subtle);
    }

    .res-stat-label {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .res-stat-num {
        font-size: 20px;
        font-family: var(--font-mono);
        font-weight: 800;
        color: var(--text-primary);
        margin-top: 2px;
    }

    .res-stat-num.highlight {
        color: var(--accent-green);
    }

    .folder-structure-guarantee-box {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px 20px;
        background: var(--accent-green-light);
        border: 1px solid rgba(5, 150, 105, 0.2);
        border-radius: var(--radius-md);
        margin-bottom: 16px;
        color: #064e3b;
    }

    [data-theme="dark-titanium"] .folder-structure-guarantee-box {
        color: #a7f3d0;
    }

    .folder-result-card {
        background: var(--bg-card-solid);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        margin-bottom: 14px;
        overflow: hidden;
    }

    .folder-result-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        background: var(--bg-elevated);
        border-bottom: 1px solid var(--border-subtle);
        flex-wrap: wrap;
        gap: 10px;
    }

    .folder-result-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: 700;
    }

    .folder-count-badge {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .btn-dl-folder-zip {
        padding: 6px 14px;
        border-radius: 8px;
        background: var(--primary-gradient);
        color: #ffffff;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform 0.15s ease;
    }

    .btn-dl-folder-zip:hover {
        transform: translateY(-1px);
    }

    .folder-result-files-list {
        display: flex;
        flex-direction: column;
    }

    .result-row-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        border-bottom: 1px solid var(--border-subtle);
        gap: 12px;
    }

    .result-row-card:last-child {
        border-bottom: none;
    }

    .btn-dl-individual {
        padding: 6px 14px;
        border-radius: 8px;
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        color: var(--text-primary);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }

    .btn-dl-individual:hover {
        background: var(--primary-light);
        color: var(--primary);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .results-cta-deck {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 20px 24px;
        background: var(--bg-card-solid);
        border-top: 1px solid var(--border-subtle);
        flex-wrap: wrap;
    }

    .btn-dl-all-zip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: var(--radius-md);
        background: var(--primary-gradient);
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        transition: all 0.2s ease;
    }

    .btn-dl-all-zip:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4);
    }

    /* FOOTER */
    .app-footer {
        text-align: center;
        padding: 24px;
        font-size: 12px;
        color: var(--text-muted);
        margin-top: auto;
    }

    /* TOAST */
    .toast-tray {
        position: fixed;
        bottom: 24px;
        right: 24px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 9999;
    }

    .toast-card {
        padding: 12px 18px;
        background: var(--bg-card-solid);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
        animation: slideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* MODAL */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        z-index: 9999;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-card {
        background: var(--bg-card-solid);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        max-width: 440px;
        width: 100%;
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 14px;
        animation: popUp 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes popUp {
        from { transform: scale(0.94); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .modal-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: var(--accent-green-light);
        color: var(--accent-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    /* ============================================================
       LANDING PAGE ENTRANCE & ELEGANT MICRO-ANIMATIONS
       ============================================================ */
    @keyframes heroFadeDown {
        from {
            opacity: 0;
            transform: translateY(-14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes heroFadeUp {
        from {
            opacity: 0;
            transform: translateY(18px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .app-topbar {
        animation: heroFadeDown 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
    }


    .workflow-stepper {
        animation: heroFadeUp 0.7s 0.16s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .main-workspace-grid {
        animation: heroFadeUp 0.7s 0.24s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .specs-grid {
        animation: heroFadeUp 0.7s 0.32s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    /* GENTLE FLOATING & BREATHING AURA FOR DROPZONE */
    @keyframes floatGentle {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    @keyframes auraPulse {
        0%, 100% {
            transform: scale(0.95);
            opacity: 0.25;
        }
        50% {
            transform: scale(1.15);
            opacity: 0.65;
        }
    }

    .dz-icon-circle {
        position: relative;
    }

    .dz-icon-circle i {
        display: inline-block;
        animation: floatGentle 4s ease-in-out infinite;
    }

    .dz-icon-circle::before {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 26px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.35) 0%, rgba(99, 102, 241, 0) 70%);
        animation: auraPulse 3.5s ease-in-out infinite;
        pointer-events: none;
        z-index: 0;
    }

    /* SHIMMER TEXT GRADIENT ACCENT */
    .shimmer-text {
        background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 35%, #8b5cf6 70%, #4f46e5 100%);
        background-size: 250% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: textShimmer 8s linear infinite;
    }

    [data-theme="dark-titanium"] .shimmer-text {
        background: linear-gradient(135deg, #818cf8 0%, #38bdf8 35%, #c084fc 70%, #818cf8 100%);
        background-size: 250% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    @keyframes textShimmer {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }


    /* ============================================================
       DEDICATED MOBILE RESPONSIVE OPTIMIZATION (<= 640px)
       ============================================================ */
    @media (max-width: 640px) {
        .app-viewport {
            padding: 12px 10px 48px;
            gap: 14px;
        }

        /* Topbar Mobile */
        .app-topbar {
            padding: 10px 14px;
            border-radius: var(--radius-md);
        }
        .brand-icon-box {
            width: 36px;
            height: 36px;
            font-size: 16px;
            border-radius: 10px;
        }
        .brand-title {
            font-size: 16px;
        }
        .brand-subline {
            display: none; /* Sembunyikan subline panjang agar topbar mobile tetap rapi */
        }
        .theme-toggle-btn {
            padding: 6px 10px;
            font-size: 12px;
        }
        .theme-toggle-btn span {
            display: none; /* Icon-only toggle di mobile */
        }

        /* Stepper Mobile */
        .workflow-stepper {
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }
        .step-pill {
            padding: 8px 10px;
            font-size: 11px;
            gap: 6px;
            border-radius: var(--radius-sm);
        }
        .step-num {
            width: 18px;
            height: 18px;
            font-size: 9.5px;
        }

        /* Card Content Mobile */
        .card-header {
            padding: 12px 16px;
        }
        .card-title {
            font-size: 13.5px;
        }
        .card-badge {
            font-size: 10.5px;
            padding: 2px 8px;
        }
        .card-body {
            padding: 14px;
        }

        /* Dropzone Mobile */
        .dropzone-view {
            padding: 28px 14px;
            gap: 12px;
        }
        .dz-icon-circle {
            width: 52px;
            height: 52px;
            font-size: 22px;
            border-radius: 14px;
        }
        .dz-title {
            font-size: 15px;
        }
        .dz-desc {
            font-size: 12px;
            line-height: 1.45;
        }
        .dz-buttons-row {
            flex-direction: column;
            width: 100%;
            gap: 8px;
        }
        .btn-primary-pill, .btn-secondary-pill {
            width: 100%;
            justify-content: center;
            padding: 12px 16px;
            font-size: 13px;
        }
        .dz-chips-ribbon {
            gap: 6px;
        }
        .dz-chip {
            font-size: 10.5px;
            padding: 3px 8px;
        }

        /* Presets Mobile */
        .presets-container {
            gap: 8px;
        }
        .preset-card {
            padding: 12px 14px;
            gap: 10px;
        }
        .preset-card-left {
            gap: 10px;
        }
        .preset-name {
            font-size: 13px;
        }
        .preset-desc {
            font-size: 11px;
        }
        .preset-badge-rec {
            display: none; /* Sembunyikan badge agar tidak memotong judul preset */
        }
        .preset-size-num {
            font-size: 15px;
        }

        /* Action Row Mobile */
        .master-action-row {
            flex-direction: column;
            gap: 8px;
        }
        .btn-compress-master {
            width: 100%;
            padding: 13px 16px;
            font-size: 13px;
        }
        .btn-cancel-clean {
            width: 100%;
            text-align: center;
            padding: 10px 16px;
        }

        /* Queue Mobile */
        .queue-summary-strip {
            padding: 10px 12px;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .queue-stat-badges {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .stat-badge-pill {
            font-size: 11px;
            padding: 4px 8px;
        }
        .filter-tabs-row {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }
        .tab-pills-wrap {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            text-align: center;
        }
        .filter-tab-btn {
            padding: 6px 2px;
            font-size: 11px;
        }
        .queue-action-btns {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }
        .btn-add-mini {
            justify-content: center;
            font-size: 11.5px;
        }
        .file-item-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 12px;
        }
        .file-main-info {
            width: 100%;
        }
        .file-pills-col {
            width: 100%;
            justify-content: space-between;
            border-top: 1px dashed var(--border-subtle);
            padding-top: 8px;
        }

        /* Results Mobile */
        .results-banner {
            padding: 14px 16px;
        }
        .results-stats-row {
            padding: 12px 14px;
            gap: 8px;
        }
        .res-stat-box {
            padding: 10px 12px;
        }
        .res-stat-num {
            font-size: 17px;
        }
        .results-cta-deck {
            padding: 14px;
            flex-direction: column;
            gap: 8px;
        }
        .btn-dl-all-zip, #btnDownloadSingleLink, .results-cta-deck .btn-secondary-pill {
            width: 100%;
            justify-content: center;
            text-align: center;
            padding: 12px 14px;
            font-size: 13px;
        }
        .folder-result-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .btn-dl-folder-zip {
            width: 100%;
            justify-content: center;
        }
        .result-row-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            padding: 10px 12px;
        }
        .btn-dl-individual {
            width: 100%;
            justify-content: center;
        }

    }

    /* ── DATABASE HISTORY & MONITOR STYLES ── */
    .db-history-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: var(--radius-pill);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        font-family: var(--font-main);
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: var(--shadow-sm);
    }
    .db-history-btn:hover {
        background: var(--bg-elevated);
        border-color: var(--primary);
        color: var(--text-primary);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    .db-live-indicator {
        position: relative;
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
    }
    .db-live-pulse {
        position: absolute;
        inset: -3px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.4);
        animation: pulseDot 2s infinite ease-out;
    }
    @keyframes pulseDot {
        0% { transform: scale(0.9); opacity: 0.9; }
        100% { transform: scale(2.2); opacity: 0; }
    }
    .db-badge {
        font-size: 11px;
        font-weight: 700;
        background: var(--primary-light);
        color: var(--primary);
        padding: 2px 8px;
        border-radius: 999px;
    }
    [data-theme="dark-titanium"] .db-badge {
        background: rgba(99, 102, 241, 0.25);
        color: #a5b4fc;
    }
    .db-history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }
    .db-history-table tr {
        border-bottom: 1px solid var(--border-subtle);
        transition: background 0.15s ease;
    }
    .db-history-table tbody tr:hover {
        background: var(--bg-elevated);
    }
    .db-history-table td {
        padding: 10px 8px;
        color: var(--text-secondary);
        vertical-align: middle;
    }
    .modal-card-wide {
        max-width: 920px !important;
        width: 95% !important;
    }
    </style>
<body>

<!-- AMBIENT FLOATING ELEMENTS (Buku & Pesawat Kertas Melayang Halus dari Bawah ke Atas) -->
<canvas id="ambientFloatingCanvas" aria-hidden="true"></canvas>

<div class="app-viewport">

    <!-- TOPBAR -->
    <header class="app-topbar">
        <div class="brand-cluster">
            <div class="brand-icon-box">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <div>
                <div class="brand-title">
                    PressPDF
                    <span class="brand-badge">Studio v2.4</span>
                </div>
                <div class="brand-subline">Kompresi Dokumen Cepat &amp; Kualitas terjaga</div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @if(!empty($gsAvailable))
                <div class="db-history-btn" style="cursor: default; border-color: rgba(5, 150, 105, 0.3); background: rgba(5, 150, 105, 0.08);" title="Ghostscript terdeteksi dan aktif: {{ $gsPath }}">
                    <span class="db-live-indicator" style="background: rgba(5, 150, 105, 0.2);"><span class="db-live-pulse" style="background: var(--accent-green);"></span></span>
                    <i class="fa-solid fa-bolt" style="color: var(--accent-green);"></i>
                    <span class="db-label-text" style="color: var(--text-primary);">Engine: <strong style="color: var(--accent-green);">Ghostscript Aktif</strong></span>
                </div>
            @else
                <div class="db-history-btn" style="cursor: default; border-color: rgba(225, 29, 72, 0.3); background: rgba(225, 29, 72, 0.08);" title="Ghostscript belum terpasang di Linux. Jalankan: sudo apt install -y ghostscript">
                    <span class="db-live-indicator" style="background: rgba(225, 29, 72, 0.2);"><span class="db-live-pulse" style="background: var(--accent-red);"></span></span>
                    <i class="fa-solid fa-triangle-exclamation" style="color: var(--accent-red);"></i>
                    <span class="db-label-text" style="color: var(--text-primary);">Engine: <strong style="color: var(--accent-red);">GS Belum Terpasang</strong></span>
                </div>
            @endif

            <button type="button" class="db-history-btn" id="dbHistoryBtn" onclick="openDbHistoryModal()" title="Cek riwayat tersimpan di database presspdf">
                <span class="db-live-indicator"><span class="db-live-pulse"></span></span>
                <i class="fa-solid fa-database" style="color: #6366f1;"></i>
                <span class="db-label-text">Database: <strong>{{ $dbDatabase ?? 'presspdf' }}</strong></span>
                <span class="db-badge" id="topbarDbBadge">{{ $totalJobs ?? 1 }} Data</span>
            </button>

            <button type="button" class="theme-toggle-btn" id="themeSwitchBtn" onclick="toggleThemeMode()">
                <i class="fa-solid fa-circle-half-stroke"></i>
                <span id="themeSwitchText">Mode Gelap</span>
            </button>
        </div>
    </header>

    <!-- STEPPER -->
    <div class="workflow-stepper">
        <div class="step-pill active">
            <span class="step-num">1</span>
            <span>Input File / Folder (Maks. 1 GB)</span>
        </div>
        <div class="step-pill active">
            <span class="step-num">2</span>
            <span>Saring Otomatis (&ge; 19 MB)</span>
        </div>
        <div class="step-pill active">
            <span class="step-num">3</span>
            <span>Target Presisi (5 / 10 / 15 MB)</span>
        </div>
        <div class="step-pill active">
            <span class="step-num">4</span>
            <span>Unduh Per-Folder &amp; ZIP</span>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN WORKSPACE -->
    <main class="main-workspace-grid">

        <!-- LEFT COLUMN: DROPZONE & QUEUE -->
        <div class="modern-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-folder-tree" style="color: var(--primary);"></i>
                    <span>Area Unggah &amp; Daftar Dokumen</span>
                </div>
                <span class="card-badge">Maks. 1 GB / Folder</span>
            </div>

            <div class="card-body">
                <!-- STATE 1: DROPZONE -->
                <div class="dropzone-view" id="dropzoneView"
                     onclick="triggerFileInput()"
                     ondragover="onDragOver(event)"
                     ondragleave="onDragLeave(event)"
                     ondrop="onDrop(event)">

                    <div class="dz-icon-circle">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>

                    <div class="dz-title">Tarik &amp; Letakkan File atau Folder PDF di Sini</div>
                    <p class="dz-desc">
                        Sistem memindai seluruh folder &amp; sub-folder. Berkas berukuran <strong>&ge; 19 MB</strong> otomatis dikompresi. Nama file PDF kode klaim <strong>tetap 100% utuh</strong>, dan folder terluar otomatis diberi inisial <code>compres_</code>.
                    </p>

                    <div class="dz-buttons-row">
                        <button class="btn-primary-pill" type="button" onclick="event.stopPropagation(); triggerFolderInput()">
                            <i class="fa-solid fa-folder-open"></i>
                            Pilih Folder Dokumen
                        </button>
                        <button class="btn-secondary-pill" type="button" onclick="event.stopPropagation(); triggerFileInput()">
                            <i class="fa-solid fa-file-pdf"></i>
                            Pilih Berkas Satuan
                        </button>
                    </div>

                    <div class="dz-chips-ribbon">
                        <span class="dz-chip"><i class="fa-solid fa-check" style="color: var(--accent-green);"></i> Folder Penuh (Maks. 1 GB)</span>
                        <span class="dz-chip"><i class="fa-solid fa-filter" style="color: var(--accent-red);"></i> Filter Otomatis &ge; 19 MB</span>
                        <span class="dz-chip"><i class="fa-solid fa-shield-halved" style="color: var(--accent-blue);"></i> Nama Kode Utuh 100%</span>
                    </div>
                </div>

                <!-- Hidden inputs -->
                <input type="file" id="fileInput" accept="application/pdf" multiple onchange="onFilesChosen(this.files, 'files')" style="display: none;">
                <input type="file" id="folderInput" webkitdirectory directory multiple onchange="onFolderChosen(this.files)" style="display: none;">

                <!-- STATE 2: ACTIVE QUEUE -->
                <div class="workspace-view" id="workspaceView">
                    <div class="queue-summary-strip">
                        <div class="queue-folder-info">
                            <div class="folder-icon-circle">
                                <i class="fa-solid fa-folder-open" id="insightsSourceIcon"></i>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 700;" id="insightsFolderName">Dokumen Terdeteksi</div>
                                <div style="font-size: 11.5px; color: var(--text-muted);" id="insightsSourceDesc">Pemindaian berkas selesai</div>
                            </div>
                        </div>

                        <div class="queue-stat-badges">
                            <span class="stat-badge-pill"><span id="metricTotalCount">0</span> Total Berkas</span>
                            <span class="stat-badge-pill needs">🔴 <span id="metricNeedsCount">0</span> Perlu Kompres (&ge; 19 MB)</span>
                            <span class="stat-badge-pill safe">🟢 <span id="metricSafeCount">0</span> Aman (&lt; 19 MB)</span>
                        </div>
                    </div>

                    <div class="filter-tabs-row">
                        <div class="tab-pills-wrap">
                            <button type="button" class="filter-tab-btn active" id="tabFilterAll" onclick="setQueueFilter('all')">
                                Semua (<span id="tabCountAll">0</span>)
                            </button>
                            <button type="button" class="filter-tab-btn" id="tabFilterNeeds" onclick="setQueueFilter('needs_compress')">
                                Perlu Kompres (<span id="tabCountNeeds">0</span>)
                            </button>
                            <button type="button" class="filter-tab-btn" id="tabFilterSafe" onclick="setQueueFilter('safe')">
                                Berkas Aman (<span id="tabCountSafe">0</span>)
                            </button>
                        </div>

                        <div class="queue-action-btns">
                            <button type="button" class="btn-add-mini" onclick="triggerFolderInput()">
                                <i class="fa-solid fa-folder-plus"></i> + Folder
                            </button>
                            <button type="button" class="btn-add-mini" onclick="triggerFileInput()">
                                <i class="fa-solid fa-file-circle-plus"></i> + File
                            </button>
                        </div>
                    </div>

                    <div class="files-list-tray" id="filesQueueList">
                        <!-- Filled by JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: CONTROL & EXECUTION -->
        <div class="modern-card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fa-solid fa-sliders" style="color: var(--primary);"></i>
                    <span>Target Ukuran &amp; Eksekusi</span>
                </div>
                <span class="card-badge" id="targetSizeLabel" style="color: var(--primary); font-weight: 800;">10.0 MB</span>
            </div>

            <div class="card-body">
                <!-- PRESET OPTIONS -->
                <div class="presets-container">
                    <!-- 5.0 MB -->
                    <div class="preset-card" id="presetLow" onclick="selectCompressionPreset(5.0, 'low')">
                        <div class="preset-card-left">
                            <div class="preset-radio-indicator">
                                <div class="preset-radio-dot"></div>
                            </div>
                            <div class="preset-title-wrap">
                                <div class="preset-name">Kompak &amp; Ringan</div>
                                <div class="preset-desc">Hasil pasti &lt; 5 MB · Cocok untuk batas unggah ketat.</div>
                            </div>
                        </div>
                        <div class="preset-size-num">5.0 MB</div>
                    </div>

                    <!-- 10.0 MB (RECOMMENDED) -->
                    <div class="preset-card active" id="presetRec" onclick="selectCompressionPreset(10.0, 'recommended')">
                        <div class="preset-card-left">
                            <div class="preset-radio-indicator">
                                <div class="preset-radio-dot"></div>
                            </div>
                            <div class="preset-title-wrap">
                                <div class="preset-name">
                                    Standar Rekomendasi
                                    <span class="preset-badge-rec">Pilihan Utama</span>
                                </div>
                                <div class="preset-desc">Hasil dijamin &lt; 10 MB · Teks &amp; barcode sangat jernih.</div>
                            </div>
                        </div>
                        <div class="preset-size-num">10.0 MB</div>
                    </div>

                    <!-- 15.0 MB -->
                    <div class="preset-card" id="presetHigh" onclick="selectCompressionPreset(15.0, 'high')">
                        <div class="preset-card-left">
                            <div class="preset-radio-indicator">
                                <div class="preset-radio-dot"></div>
                            </div>
                            <div class="preset-title-wrap">
                                <div class="preset-name">Ketajaman Maksimal</div>
                                <div class="preset-desc">Hasil pasti &lt; 15 MB · Kualitas tinggi mendekati berkas asli.</div>
                            </div>
                        </div>
                        <div class="preset-size-num">15.0 MB</div>
                    </div>
                </div>

                <!-- OPTIONAL SLIDER -->
                <div class="slider-accordion-toggle" onclick="toggleCustomSlider()">
                    <span><i class="fa-solid fa-sliders" style="margin-right: 6px;"></i> Atur Ukuran Kustom (5.0 – 15.0 MB)</span>
                    <i class="fa-solid fa-chevron-down" id="customSliderChevron"></i>
                </div>

                <div class="slider-drawer" id="customSliderDrawer">
                    <input type="range" class="modern-range-input" id="targetSizeSlider"
                           min="5.0" max="15.0" step="0.5" value="10.0"
                           oninput="handleSliderChange(this.value)">
                    <div class="range-ticks-text">
                        <span>5.0 MB</span>
                        <span>10.0 MB</span>
                        <span>15.0 MB</span>
                    </div>
                </div>

                <div class="mode-hint-banner">
                    <i class="fa-solid fa-circle-info"></i>
                    <span id="qualityHintText">Target 10.0 MB: Teks &amp; barcode sangat jernih, proses kilat ~5-8 detik.</span>
                </div>

                <div class="master-action-row">
                    <button type="button" class="btn-compress-master" id="compressBtn" onclick="startBatchCompress()">
                        <i class="fa-solid fa-bolt"></i>
                        <span id="compressBtnText">Kompres File Terdeteksi (&ge; 19 MB)</span>
                    </button>
                    <button type="button" class="btn-cancel-clean" onclick="clearSelectedFiles()">
                        Batal
                    </button>
                </div>

                <div class="progress-bay" id="progressBox">
                    <div class="progress-labels">
                        <span id="progressStageText">Memproses kompresi berkas...</span>
                        <span id="progressPctText" style="color: var(--primary);">0%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill-bar" id="progressFill"></div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- SPECIFICATIONS SECTION -->
    <section class="specs-grid">
        <div class="spec-card">
            <div class="spec-icon-box red">
                <i class="fa-solid fa-filter"></i>
            </div>
            <div class="spec-title">Filter Ambang Batas 19 MB</div>
            <p class="spec-desc">
                File PDF &ge; 19 MB otomatis terdeteksi &amp; dikompresi. Berkas &lt; 19 MB tetap disertakan utuh di foldernya tanpa perubahan.
            </p>
            <div class="spec-tag-sub">&ge; 19 MB OTOMATIS</div>
        </div>

        <div class="spec-card">
            <div class="spec-icon-box amber">
                <i class="fa-solid fa-folder-tree"></i>
            </div>
            <div class="spec-title">Struktur Folder Rapi</div>
            <p class="spec-desc">
                Mendukung 1 folder penuh (maks 1 GB). Output terbungkus rapi dengan awalan <code>compres_</code> pada folder terluar.
            </p>
            <div class="spec-tag-sub">MAKS. 1 GB PER FOLDER</div>
        </div>

        <div class="spec-card">
            <div class="spec-icon-box blue">
                <i class="fa-solid fa-microchip"></i>
            </div>
            <div class="spec-title">Mesin Super Cepat</div>
            <p class="spec-desc">
                Didukung Ghostscript 10 multi-threaded 8 core &amp; dedicated memory buffer. Selesai rata-rata dalam 5–8 detik per berkas.
            </p>
            <div class="spec-tag-sub">GS ACCELERATED</div>
        </div>

        <div class="spec-card">
            <div class="spec-icon-box green">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="spec-title">Nama Berkas Utuh 100%</div>
            <p class="spec-desc">
                Seluruh kode berkas PDF klaim rumah sakit tetap sama persis tanpa diubah. Data sementara otomatis dihapus dari server.
            </p>
            <div class="spec-tag-sub">100% AMAN &amp; PRIVAT</div>
        </div>
    </section>

    <!-- RESULTS SECTION -->
    <section class="modern-card result-section" id="resultSection">
        <div class="results-banner">
            <div class="success-check-badge">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <h3 style="font-size: 18px; font-weight: 800;" id="resultHeading">Kompresi Dokumen Selesai!</h3>
                <p style="font-size: 13px; color: var(--text-muted);" id="resultFileName">Semua berkas telah berhasil diproses dengan presisi.</p>
            </div>
        </div>

        <div class="results-stats-row">
            <div class="res-stat-box">
                <div class="res-stat-label">Total Ukuran Awal</div>
                <div class="res-stat-num" id="statOriginal">—</div>
            </div>
            <div class="res-stat-box">
                <div class="res-stat-label">Total Ukuran Baru</div>
                <div class="res-stat-num" id="statCompressed">—</div>
            </div>
            <div class="res-stat-box">
                <div class="res-stat-label">Total Penghematan</div>
                <div class="res-stat-num highlight" id="statSavings">—</div>
            </div>
        </div>

        <div style="padding: 24px;" id="batchResultList">
            <!-- Filled by JS -->
        </div>

        <div class="results-cta-deck">
            <a class="btn-dl-all-zip" id="btnDownloadZipLink" href="#" target="_blank" style="display: none;">
                <i class="fa-solid fa-file-zipper"></i>
                Unduh Semua Folder (.ZIP Master)
            </a>
            <a class="btn-dl-all-zip" id="btnDownloadSingleLink" href="#" target="_blank">
                <i class="fa-solid fa-download"></i>
                Unduh Dokumen PDF Terkompresi
            </a>
            <button type="button" class="btn-secondary-pill" onclick="resetForm()">
                <i class="fa-solid fa-arrow-rotate-left"></i>
                Proses Dokumen Baru
            </button>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="app-footer">
        PressPDF &copy; {{ date('Y') }} &mdash; YGSKY Studio Kompresi PDF Cepat, Bersih &amp; Presisi
    </footer>

</div>

<!-- MODAL POPUP -->
<div class="modal-backdrop" id="modalSmall" role="dialog" aria-modal="true">
    <div class="modal-card">
        <div class="modal-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h2 style="font-size: 17px; font-weight: 800; color: var(--text-primary);" id="modalTitle">
            Berkas Sudah di Bawah 19 MB!
        </h2>
        <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;">
            File PDF ini sudah berukuran aman (&lt; 19 MB) sehingga tidak memerlukan kompresi dan siap digunakan.
        </p>
        <div style="padding: 10px 16px; border-radius: 8px; background: var(--bg-elevated); width: 100%; font-size: 12.5px; font-family: var(--font-mono);">
            <span id="modalFileName" style="font-weight: 700;">—</span>:
            <span id="modalFileSize" style="color: var(--primary); font-weight: 700;">—</span>
        </div>
        <button class="btn-primary-pill" style="width: 100%; justify-content: center; margin-top: 6px;" id="modalOkBtn" onclick="closeModal()">
            Oke, Saya Mengerti
        </button>
    </div>
</div>

<!-- MODAL RIWAYAT DATABASE PRESPDF -->
<div class="modal-backdrop" id="modalDbHistory" role="dialog" aria-modal="true">
    <div class="modal-card modal-card-wide" style="display: flex; flex-direction: column; padding: 0; overflow: hidden; max-height: 85vh;">
        <!-- Header -->
        <div style="padding: 18px 24px; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; background: var(--bg-card-solid);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(79, 70, 229, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-database"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                        Database Monitor: <span style="font-family: var(--font-mono); color: var(--primary);">{{ $dbDatabase ?? 'presspdf' }}</span>
                        <span style="font-size: 11px; padding: 2px 8px; border-radius: 999px; background: rgba(16, 185, 129, 0.14); color: #059669; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.25);">
                            <i class="fa-solid fa-circle" style="font-size: 7px; margin-right: 4px;"></i>MySQL Aktif
                        </span>
                    </h3>
                    <p style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                        Tabel <code>compression_jobs</code> &bull; Setiap inputan baru otomatis terbaca dan tersimpan
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeDbHistoryModal()" style="background: none; border: none; font-size: 20px; color: var(--text-muted); cursor: pointer; padding: 6px 10px; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.color='var(--text-primary)'" onmouseout="this.style.color='var(--text-muted)'">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Metrics Strip -->
        <div style="padding: 14px 24px; background: var(--bg-elevated); border-bottom: 1px solid var(--border-subtle); display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
            <div style="background: var(--bg-card-solid); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Total Input Tercatat</div>
                <div style="font-size: 20px; font-weight: 800; color: var(--text-primary); font-family: var(--font-mono);" id="dbMetricTotal">0</div>
            </div>
            <div style="background: var(--bg-card-solid); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Status Selesai</div>
                <div style="font-size: 20px; font-weight: 800; color: #059669; font-family: var(--font-mono);" id="dbMetricCompleted">0</div>
            </div>
            <div style="background: var(--bg-card-solid); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Total Penghematan</div>
                <div style="font-size: 20px; font-weight: 800; color: var(--primary); font-family: var(--font-mono);" id="dbMetricSaved">0 B</div>
            </div>
            <div style="background: var(--bg-card-solid); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: flex-end;">
                <button type="button" class="btn-secondary-pill" onclick="loadDbHistory(true)" style="padding: 8px 14px; font-size: 12px; width: 100%; justify-content: center;" id="btnRefreshDb">
                    <i class="fa-solid fa-arrows-rotate"></i> Segarkan Data
                </button>
            </div>
        </div>

        <!-- Search / Filter -->
        <div style="padding: 12px 24px 8px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div style="position: relative; flex: 1; max-width: 320px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; color: var(--text-muted);"></i>
                <input type="text" id="dbSearchInput" placeholder="Cari nama berkas..." oninput="filterDbTable()" style="width: 100%; padding: 8px 12px 8px 34px; border-radius: 8px; border: 1px solid var(--border-subtle); background: var(--bg-base); color: var(--text-primary); font-size: 13px; font-family: var(--font-main); outline: none;">
            </div>
            <div style="font-size: 12px; color: var(--text-muted);" id="dbRecordCounter">Menampilkan 0 data</div>
        </div>

        <!-- Table Container -->
        <div style="flex: 1; overflow-y: auto; padding: 10px 24px 20px;" id="dbTableWrapper">
            <div id="dbLoadingIndicator" style="text-align: center; padding: 40px; color: var(--text-muted);">
                <div class="mini-spinner" style="margin: 0 auto 12px; width: 24px; height: 24px; border-width: 3px;"></div>
                <span>Mengambil data dari database presspdf...</span>
            </div>
            <table class="db-history-table" id="dbHistoryTable" style="display: none;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-subtle); text-align: left; color: var(--text-muted); font-size: 11px; text-transform: uppercase;">
                        <th style="padding: 10px 8px; width: 50px;">ID</th>
                        <th style="padding: 10px 8px;">Waktu</th>
                        <th style="padding: 10px 8px;">Nama Berkas</th>
                        <th style="padding: 10px 8px;">Ukuran Asli</th>
                        <th style="padding: 10px 8px;">Ukuran Akhir</th>
                        <th style="padding: 10px 8px;">Hemat</th>
                        <th style="padding: 10px 8px;">Rasio</th>
                        <th style="padding: 10px 8px; width: 90px;">Status</th>
                    </tr>
                </thead>
                <tbody id="dbHistoryTableBody">
                    <!-- Populated dynamically -->
                </tbody>
            </table>
            <div id="dbEmptyNotice" style="display: none; text-align: center; padding: 48px 16px; color: var(--text-muted);">
                <i class="fa-solid fa-folder-open" style="font-size: 32px; opacity: 0.4; margin-bottom: 8px;"></i>
                <div>Belum ada input berkas yang tercatat di database.</div>
            </div>
        </div>

        <!-- Footer -->
        <div style="padding: 12px 24px; border-top: 1px solid var(--border-subtle); background: var(--bg-card-solid); display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 11.5px; color: var(--text-muted); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                Setiap inputan baru di-sinkronisasi langsung ke MySQL tabel <code>compression_jobs</code>.
            </div>
            <button type="button" class="btn-primary-pill" onclick="closeDbHistoryModal()" style="padding: 6px 18px; font-size: 12.5px;">
                Tutup
            </button>
        </div>
    </div>
</div>

<div class="toast-tray" id="toastBox"></div>

<script>
let selectedFilesQueue = [];
let activeTheme = localStorage.getItem('presspdf_theme') || 'light';
let isCompressing = false;
let currentQueueFilter = 'all';
let currentSourceInfo = { type: 'files', name: '' };
const NINETEEN_MB_BYTES = 19 * 1024 * 1024;

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const URL_COMPRESS_ENDPOINT = '{{ route("pdf.compress") }}';
const URL_HISTORY_ENDPOINT  = '{{ route("pdf.history") }}';
let cachedDbJobs = [];

// Init theme
if (activeTheme === 'dark-titanium') {
    document.documentElement.setAttribute('data-theme', 'dark-titanium');
    document.getElementById('themeSwitchText').textContent = 'Mode Terang';
} else {
    document.documentElement.removeAttribute('data-theme');
    document.getElementById('themeSwitchText').textContent = 'Mode Gelap';
}

function toggleThemeMode() {
    if (activeTheme === 'dark-titanium') {
        activeTheme = 'light';
        document.documentElement.removeAttribute('data-theme');
        document.getElementById('themeSwitchText').textContent = 'Mode Gelap';
    } else {
        activeTheme = 'dark-titanium';
        document.documentElement.setAttribute('data-theme', 'dark-titanium');
        document.getElementById('themeSwitchText').textContent = 'Mode Terang';
    }
    localStorage.setItem('presspdf_theme', activeTheme);
}


function triggerFileInput() {
    document.getElementById('fileInput').click();
}

function triggerFolderInput() {
    document.getElementById('folderInput').click();
}

function generateBatchToken() {
    const chars = 'abcdef0123456789';
    let token = '';
    for (let i = 0; i < 32; i++) {
        token += chars[Math.floor(Math.random() * chars.length)];
    }
    return token;
}

function onFolderChosen(fileList) {
    if (!fileList || fileList.length === 0) return;
    let folderName = 'Folder Pilihan';
    if (fileList[0] && fileList[0].webkitRelativePath) {
        folderName = fileList[0].webkitRelativePath.split('/')[0] || folderName;
    }
    currentSourceInfo = { type: 'folder', name: folderName };
    processIncomingFiles(fileList, true);
    document.getElementById('folderInput').value = '';
}

function onFilesChosen(fileList, source = 'files') {
    if (!fileList || fileList.length === 0) return;
    if (source === 'folder') {
        onFolderChosen(fileList);
        return;
    }
    currentSourceInfo = { type: 'files', name: `${fileList.length} Dokumen PDF` };
    processIncomingFiles(fileList, false);
    document.getElementById('fileInput').value = '';
}

function processIncomingFiles(fileList, isFolder = false) {
    let nonPdfCount = 0;

    Array.from(fileList).forEach(file => {
        if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
            nonPdfCount++;
            return;
        }
        if (file.size > 1073741824) {
            showToast(`"${file.name}" dilewati: Melebihi batas maksimal 1 GB.`);
            return;
        }

        const relPath = file.customRelativePath || file.webkitRelativePath || file.name;
        const exists = selectedFilesQueue.some(item => item.name === file.name && item.size === file.size && item.relativePath === relPath);
        if (!exists) {
            const isOver19 = file.size >= NINETEEN_MB_BYTES;
            selectedFilesQueue.push({
                id: 'f_' + Math.random().toString(36).substr(2, 9),
                file: file,
                name: file.name,
                relativePath: relPath,
                size: file.size,
                isOver19Mb: isOver19,
                shouldCompress: isOver19,
                status: 'pending',
                statusText: isOver19 ? 'Perlu Kompres (≥ 19 MB)' : 'Aman (Disertakan Utuh)',
                result: null
            });
        }
    });

    if (nonPdfCount > 0 && isFolder) {
        showToast(`Folder dipindai: ${nonPdfCount} file non-PDF otomatis dilewati.`);
    }

    if (selectedFilesQueue.length === 0) {
        showToast('Tidak ada file PDF yang valid untuk diproses.');
        return;
    }

    updateInsightsStrip();
    renderFilesQueue();

    document.getElementById('dropzoneView').style.display = 'none';
    document.getElementById('workspaceView').classList.add('show');
    document.getElementById('resultSection').classList.remove('show');

    const currentVal = document.getElementById('targetSizeSlider').value;
    handleSliderChange(currentVal);

    const totalNeeds = selectedFilesQueue.filter(f => f.isOver19Mb).length;
    if (totalNeeds === 0 && selectedFilesQueue.length > 0) {
        showSmallPopup(
            currentSourceInfo.name || 'Folder Dokumen',
            `Semua ${selectedFilesQueue.length} file PDF sudah berukuran < 19 MB. Berkas Anda aman tanpa perlu kompresi!`
        );
    }
}

function updateInsightsStrip() {
    const totalPdfs = selectedFilesQueue.length;
    const over19Mb = selectedFilesQueue.filter(f => f.isOver19Mb).length;
    const under19Mb = totalPdfs - over19Mb;

    document.getElementById('metricTotalCount').textContent = totalPdfs;
    document.getElementById('metricNeedsCount').textContent = over19Mb;
    document.getElementById('metricSafeCount').textContent = under19Mb;

    document.getElementById('insightsFolderName').textContent = currentSourceInfo.name || `${totalPdfs} Dokumen Terdeteksi`;
    document.getElementById('insightsSourceDesc').textContent = currentSourceInfo.type === 'folder'
        ? 'Struktur sub-folder terbaca otomatis (Nama kode berkas utuh)'
        : 'Berkas PDF satuan siap diproses';

    document.getElementById('tabCountAll').textContent = totalPdfs;
    document.getElementById('tabCountNeeds').textContent = over19Mb;
    document.getElementById('tabCountSafe').textContent = under19Mb;
}

function setQueueFilter(filterType) {
    currentQueueFilter = filterType;
    document.getElementById('tabFilterAll').classList.toggle('active', filterType === 'all');
    document.getElementById('tabFilterNeeds').classList.toggle('active', filterType === 'needs_compress');
    document.getElementById('tabFilterSafe').classList.toggle('active', filterType === 'safe');
    renderFilesQueue();
}

function renderFilesQueue() {
    const container = document.getElementById('filesQueueList');
    container.innerHTML = '';

    let filtered = selectedFilesQueue;
    if (currentQueueFilter === 'needs_compress') {
        filtered = selectedFilesQueue.filter(f => f.isOver19Mb);
    } else if (currentQueueFilter === 'safe') {
        filtered = selectedFilesQueue.filter(f => !f.isOver19Mb);
    }

    if (filtered.length === 0) {
        const emptyDiv = document.createElement('div');
        emptyDiv.style.cssText = 'padding: 32px; text-align: center; color: var(--text-muted); font-size: 13px;';
        emptyDiv.textContent = 'Tidak ada file dalam kategori ini.';
        container.appendChild(emptyDiv);
        return;
    }

    filtered.forEach(item => {
        const row = document.createElement('div');
        row.className = `file-item-card ${item.isOver19Mb ? 'needs-comp' : 'safe-file'}`;
        row.id = `queueItem_${item.id}`;

        let statusClass = 'status-pill-chip';
        let statusText = item.statusText;

        if (item.status === 'done') {
            statusClass += ' success';
        } else if (item.status === 'compressing') {
            statusClass += ' compressing';
            statusText = 'Mengompresi...';
        } else if (item.status === 'error') {
            statusClass += ' error';
            statusText = 'Gagal';
        } else if (item.isOver19Mb) {
            statusClass += ' needs-comp';
            statusText = 'Perlu Kompres (≥ 19 MB)';
        } else {
            statusClass += ' safe-skip';
            statusText = 'Aman (< 19 MB)';
        }

        const pathHint = (item.relativePath && item.relativePath !== item.name)
            ? `<div class="file-path-text"><i class="fa-regular fa-folder" style="color:#d97706; margin-right:4px;"></i>${escapeHtml(item.relativePath)}</div>`
            : '';

        row.innerHTML = `
            <div class="file-main-info">
                <div class="file-doc-icon ${item.isOver19Mb ? '' : 'safe'}">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <div class="file-text-col">
                    <div class="file-name-text" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
                    ${pathHint}
                </div>
            </div>
            <div class="file-pills-col">
                <span class="size-pill-tag">${formatBytes(item.size)}</span>
                <span class="${statusClass}" id="statusTag_${item.id}">${statusText}</span>
                <button type="button" class="btn-file-del" onclick="removeFileFromQueue('${item.id}')" title="Hapus dari antrean">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });

    resetCompressBtn();
}

function removeFileFromQueue(id) {
    selectedFilesQueue = selectedFilesQueue.filter(f => f.id !== id);
    if (selectedFilesQueue.length === 0) {
        clearSelectedFiles();
        return;
    }
    updateInsightsStrip();
    renderFilesQueue();
}

function clearSelectedFiles() {
    selectedFilesQueue = [];
    currentSourceInfo = { type: 'files', name: '' };
    document.getElementById('dropzoneView').style.display = 'flex';
    document.getElementById('workspaceView').classList.remove('show');
    document.getElementById('filesQueueList').innerHTML = '';
    document.getElementById('fileInput').value = '';
    document.getElementById('folderInput').value = '';
    resetCompressBtn();
}

function onDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropzoneView').classList.add('drag-active');
}

function onDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropzoneView').classList.remove('drag-active');
}

async function onDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropzoneView').classList.remove('drag-active');

    const dt = e.dataTransfer;
    if (!dt) return;

    if (dt.items && dt.items.length > 0 && typeof dt.items[0].webkitGetAsEntry === 'function') {
        const filePromises = [];
        let hasFolder = false;

        for (let i = 0; i < dt.items.length; i++) {
            const entry = dt.items[i].webkitGetAsEntry();
            if (entry) {
                if (entry.isDirectory) hasFolder = true;
                filePromises.push(scanFileSystemEntry(entry, ''));
            }
        }

        const nestedResults = await Promise.all(filePromises);
        const flattened = nestedResults.flat();

        if (hasFolder) {
            currentSourceInfo = { type: 'folder', name: 'Folder Drop' };
            processIncomingFiles(flattened, true);
        } else {
            currentSourceInfo = { type: 'files', name: `${flattened.length} Dokumen PDF` };
            processIncomingFiles(flattened, false);
        }
    } else if (dt.files && dt.files.length > 0) {
        onFilesChosen(dt.files, 'files');
    }
}

function scanFileSystemEntry(entry, path = '') {
    return new Promise(resolve => {
        if (entry.isFile) {
            entry.file(f => {
                const fullRel = path ? (path + '/' + f.name) : f.name;
                Object.defineProperty(f, 'customRelativePath', {
                    value: fullRel,
                    writable: false
                });
                resolve([f]);
            }, () => resolve([]));
        } else if (entry.isDirectory) {
            const dirReader = entry.createReader();
            const allEntries = [];
            const readBatch = () => {
                dirReader.readEntries(async entries => {
                    if (entries.length === 0) {
                        const subPromises = allEntries.map(e =>
                            scanFileSystemEntry(e, path ? (path + '/' + entry.name) : entry.name)
                        );
                        const subArrays = await Promise.all(subPromises);
                        resolve(subArrays.flat());
                    } else {
                        allEntries.push(...entries);
                        readBatch();
                    }
                }, () => resolve([]));
            };
            readBatch();
        } else {
            resolve([]);
        }
    });
}

function selectCompressionPreset(val, modeName) {
    document.getElementById('targetSizeSlider').value = val.toFixed(1);
    updatePresetCardsUI(modeName);
    updateTargetLabelUI(val);
    updateQualityHintUI(modeName, val);
}

function updatePresetCardsUI(modeName) {
    document.getElementById('presetLow').classList.toggle('active', modeName === 'low');
    document.getElementById('presetRec').classList.toggle('active', modeName === 'recommended');
    document.getElementById('presetHigh').classList.toggle('active', modeName === 'high');
}

function updateTargetLabelUI(num) {
    const val = parseFloat(num);
    document.getElementById('targetSizeLabel').textContent = val.toFixed(1) + ' MB';
}

function updateQualityHintUI(modeName, num) {
    const hintEl = document.getElementById('qualityHintText');
    const val = parseFloat(num);

    if (modeName === 'low' || val <= 6.0) {
        hintEl.textContent = `Target ${val.toFixed(1)} MB: Sangat hemat & ringan, anti-alias halus untuk batas ketat.`;
    } else if (modeName === 'high' || val >= 13.0) {
        hintEl.textContent = `Target ${val.toFixed(1)} MB: Ketajaman tinggi 124 DPI mendekati dokumen asli.`;
    } else {
        hintEl.textContent = `Target ${val.toFixed(1)} MB: Standar rekomendasi, teks dokumen & tabel super jelas.`;
    }
}

function handleSliderChange(val) {
    const num = parseFloat(val);
    updateTargetLabelUI(num);

    let mode = 'custom';
    if (Math.abs(num - 5.0) < 0.25) mode = 'low';
    else if (Math.abs(num - 10.0) < 0.25) mode = 'recommended';
    else if (Math.abs(num - 15.0) < 0.25) mode = 'high';

    updatePresetCardsUI(mode);
    updateQualityHintUI(mode, num);
}

function toggleCustomSlider() {
    const drawer = document.getElementById('customSliderDrawer');
    const chevron = document.getElementById('customSliderChevron');
    drawer.classList.toggle('show');
    if (drawer.classList.contains('show')) {
        chevron.style.transform = 'rotate(180deg)';
    } else {
        chevron.style.transform = 'rotate(0deg)';
    }
}

async function startBatchCompress() {
    const over19MbCount = selectedFilesQueue.filter(f => f.isOver19Mb).length;
    if (over19MbCount === 0 || isCompressing) {
        showSmallPopup(
            currentSourceInfo.name || 'Dokumen Pilihan',
            'Tidak ada file berukuran mulai dari 19 MB ke atas. Semua berkas sudah aman tanpa perlu kompresi!'
        );
        return;
    }

    isCompressing = true;
    const targetSize = document.getElementById('targetSizeSlider').value;
    const btn = document.getElementById('compressBtn');
    btn.disabled = true;
    btn.innerHTML = '<div class="mini-spinner"></div><span>Memproses &amp; Menyusun Folder...</span>';

    const progBox = document.getElementById('progressBox');
    progBox.classList.add('show');

    const totalToProcess = selectedFilesQueue.length;
    const batchToken = generateBatchToken();
    const results = [];

    for (let i = 0; i < totalToProcess; i++) {
        const item = selectedFilesQueue[i];
        item.status = 'compressing';
        const statusTag = document.getElementById(`statusTag_${item.id}`);

        const overallPct = Math.round((i / totalToProcess) * 100);
        setProgress(overallPct);
        document.getElementById('progressPctText').textContent = `${overallPct}%`;

        if (item.isOver19Mb) {
            if (statusTag) {
                statusTag.className = 'status-pill-chip compressing';
                statusTag.textContent = 'Mengompresi...';
            }
            document.getElementById('progressStageText').textContent = `Mengompresi (${i + 1}/${totalToProcess}): ${item.name} (≥ 19 MB)`;
        } else {
            if (statusTag) {
                statusTag.className = 'status-pill-chip safe-skip';
                statusTag.textContent = 'Menyimpan...';
            }
            document.getElementById('progressStageText').textContent = `Menyimpan berkas aman (${i + 1}/${totalToProcess}): ${item.name} (< 19 MB)`;
        }

        const form = new FormData();
        form.append('file', item.file);
        form.append('target_size', targetSize);
        form.append('batch_token', batchToken);
        form.append('relative_path', item.relativePath || item.name);
        form.append('is_safe', item.isOver19Mb ? '0' : '1');
        form.append('_token', CSRF_TOKEN);

        try {
            const res = await fetch(URL_COMPRESS_ENDPOINT, { method: 'POST', body: form });
            let data = null;
            const resText = await res.text();
            try {
                data = JSON.parse(resText);
            } catch (jsonErr) {
                if (!res.ok) {
                    throw new Error(`Server Error (${res.status}): Batas ukuran server terlampaui atau terjadi galat internal.`);
                }
                throw new Error('Respon server bukan format JSON yang valid.');
            }

            if (!res.ok) {
                throw new Error(data.message || data.errors?.file?.[0] || 'Gagal memproses file.');
            }

            if (data.is_compressed) {
                item.status = 'done';
                item.statusText = `Selesai (${data.compressed_human})`;
                if (statusTag) {
                    statusTag.className = 'status-pill-chip success';
                    statusTag.textContent = `Selesai (${data.compressed_human})`;
                }
            } else {
                item.status = 'done';
                item.statusText = 'Aman (Disertakan)';
                if (statusTag) {
                    statusTag.className = 'status-pill-chip safe-skip';
                    statusTag.textContent = 'Aman (Disertakan)';
                }
            }

            results.push(data);
            refreshTopbarDbCount();
        } catch (err) {
            item.status = 'error';
            item.statusText = 'Gagal';
            if (statusTag) {
                statusTag.className = 'status-pill-chip error';
                statusTag.textContent = 'Gagal';
            }
            showToast(`Gagal pada "${item.name}": ${err.message}`);
        }
    }

    setProgress(100);
    document.getElementById('progressPctText').textContent = '100%';
    document.getElementById('progressStageText').textContent = 'Semua berkas dan struktur folder selesai dipaketkan!';

    isCompressing = false;
    resetCompressBtn();

    if (results.length > 0) {
        showBatchResults(results, batchToken);
        refreshTopbarDbCount();
    } else {
        showToast('Tidak ada file yang berhasil diproses.');
    }
}

function resetCompressBtn() {
    const btn = document.getElementById('compressBtn');
    if (btn) {
        const over19MbCount = selectedFilesQueue.filter(f => f.isOver19Mb).length;
        if (over19MbCount > 0) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-bolt"></i><span>Kompres ${over19MbCount} File (≥ 19 MB) &amp; Susun Folder</span>`;
        } else {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-check"></i><span>Semua File Sudah Aman (< 19 MB)</span>`;
        }
    }
    const progBox = document.getElementById('progressBox');
    if (progBox) progBox.classList.remove('show');
    setProgress(0);
}

function setProgress(pct) {
    document.getElementById('progressFill').style.width = pct + '%';
}

function showBatchResults(resultsList, batchToken) {
    let totalOrig = 0;
    let totalComp = 0;
    let totalCompressedCount = 0;
    let totalSafeCount = 0;

    const listContainer = document.getElementById('batchResultList');
    listContainer.innerHTML = '';

    const folderGroups = {};

    resultsList.forEach(item => {
        const orig = item.original_size || 0;
        const comp = Math.min(orig, (item.compressed_size || item.final_size || orig));
        totalOrig += orig;
        totalComp += comp;
        if (item.is_compressed) totalCompressedCount++;
        else totalSafeCount++;

        const fKey = item.folder_key || '';
        if (!folderGroups[fKey]) {
            folderGroups[fKey] = {
                key: fKey,
                display: item.folder_display || (fKey ? `Folder ${fKey}` : 'Berkas Utama / Root'),
                files: []
            };
        }
        folderGroups[fKey].files.push(item);
    });

    const totalSaved = Math.max(0, totalOrig - totalComp);
    const totalRatio = (totalOrig > 0 && totalSaved > 0) ? Math.max(0, Math.round((totalSaved / totalOrig) * 100)) : 0;

    document.getElementById('statOriginal').textContent = formatBytes(totalOrig);
    document.getElementById('statCompressed').textContent = formatBytes(totalComp);
    document.getElementById('statSavings').textContent = totalRatio > 0 ? `-${totalRatio}% (${formatBytes(totalSaved)})` : `0% (0 B)`;

    const guaranteeBox = document.createElement('div');
    guaranteeBox.className = 'folder-structure-guarantee-box';
    guaranteeBox.innerHTML = `
        <div style="font-size: 22px; flex-shrink: 0;"><i class="fa-solid fa-folder-tree"></i></div>
        <div style="flex: 1; font-size: 13px; line-height: 1.5;">
            <div style="font-weight: 700; margin-bottom: 2px;">
                Nama File PDF Utuh 100% &bull; Folder Terluar Otomatis Diatur "compres_"
            </div>
            <div>
                Setiap nama file kode klaim <strong>dipertahankan tetap sama tanpa diubah</strong>. Folder paling luar otomatis diberi awalan <strong>compres_</strong> dan file aman (&lt; 19 MB) tersusun rapi di sub-foldernya. Anda tidak perlu menyortir berkas secara manual!
            </div>
        </div>
    `;
    listContainer.appendChild(guaranteeBox);

    const folderKeys = Object.keys(folderGroups);
    const hasMultipleFolders = folderKeys.length > 1 || (folderKeys.length === 1 && folderKeys[0] !== '');

    if (hasMultipleFolders) {
        folderKeys.forEach(fKey => {
            const group = folderGroups[fKey];
            const compCount = group.files.filter(f => f.is_compressed).length;
            const safeCount = group.files.length - compCount;

            const card = document.createElement('div');
            card.className = 'folder-result-card';
            card.innerHTML = `
                <div class="folder-result-head">
                    <div class="folder-result-title">
                        <i class="fa-solid fa-folder-open" style="color: #d97706; font-size: 16px;"></i>
                        <span>${escapeHtml(group.display)}</span>
                        <span class="folder-count-badge">(${group.files.length} Berkas • ${compCount} Terkompresi, ${safeCount} Aman)</span>
                    </div>
                    <a class="btn-dl-folder-zip" href="/download-batch-folder/${batchToken}?folder=${encodeURIComponent(group.key)}" target="_blank">
                        <i class="fa-solid fa-file-zipper"></i>
                        <span>Unduh Folder Ini (.ZIP)</span>
                    </a>
                </div>
                <div class="folder-result-files-list"></div>
            `;
            listContainer.appendChild(card);

            const fileListEl = card.querySelector('.folder-result-files-list');
            group.files.forEach(f => {
                fileListEl.appendChild(createResultRowElement(f));
            });
        });
    } else {
        resultsList.forEach(item => {
            listContainer.appendChild(createResultRowElement(item));
        });
    }

    const zipBtn = document.getElementById('btnDownloadZipLink');
    const singleBtn = document.getElementById('btnDownloadSingleLink');

    if (resultsList.length > 1 || hasMultipleFolders) {
        document.getElementById('resultHeading').textContent = `Berhasil Memproses ${resultsList.length} Berkas!`;
        document.getElementById('resultFileName').textContent = `Seluruh folder & sub-folder tersusun rapi. Siap diunduh per-folder di atas atau via Master .ZIP di bawah.`;
        zipBtn.style.display = 'inline-flex';
        zipBtn.href = `/download-batch/${batchToken}`;
        singleBtn.style.display = 'none';
    } else {
        document.getElementById('resultHeading').textContent = 'Kompresi Dokumen Berhasil!';
        document.getElementById('resultFileName').textContent = resultsList[0].output_name || resultsList[0].filename;
        zipBtn.style.display = 'none';
        singleBtn.style.display = 'inline-flex';
        singleBtn.href = resultsList[0].download_url;
        singleBtn.innerHTML = `<i class="fa-solid fa-download"></i><span>Unduh Berkas (${escapeHtml(resultsList[0].output_name || resultsList[0].filename)})</span>`;
    }

    document.getElementById('resultSection').classList.add('show');
    document.getElementById('resultSection').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function createResultRowElement(item) {
    const row = document.createElement('div');
    row.className = 'result-row-card';

    const isComp = item.is_compressed;
    const displayName = item.filename || item.output_name;
    const origSize = item.original_size || 0;
    const finalSize = Math.min(origSize, (item.compressed_size || item.final_size || origSize));
    const ratioNum = parseFloat(item.ratio) || 0;
    const isActuallyReduced = isComp && ratioNum > 0 && finalSize < origSize;

    const badgeHtml = isActuallyReduced
        ? `<span class="status-pill-chip needs-comp" style="font-size:10.5px;">🔴 Terkompresi (-${ratioNum}%)</span>`
        : (isComp
            ? `<span class="status-pill-chip safe-skip" style="font-size:10.5px;">🟢 Berkas Optimum (0%)</span>`
            : `<span class="status-pill-chip safe-skip" style="font-size:10.5px;">🟢 Berkas Aman (&lt; 19 MB)</span>`);

    const sizeHtml = isActuallyReduced
        ? `<span>${formatBytes(origSize)} &rarr; <strong>${formatBytes(finalSize)}</strong></span>`
        : (isComp
            ? `<span>Ukuran: <strong>${formatBytes(origSize)}</strong> (Sudah Optimum)</span>`
            : `<span>Ukuran: <strong>${formatBytes(origSize)}</strong> (Disertakan Utuh)</span>`);

    const relPathHint = item.relative_path && item.relative_path !== displayName
        ? `<div style="font-size:11px; font-family:var(--font-mono); color:var(--text-muted); margin-top:2px;">
             <i class="fa-regular fa-folder" style="color:#d97706; margin-right:4px;"></i>${escapeHtml(item.relative_path)}
           </div>`
        : '';

    row.innerHTML = `
        <div style="min-width:0; flex:1;">
            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                <span style="font-size:13.5px; font-weight:700; color:var(--text-primary);" title="${escapeHtml(displayName)}">
                    <i class="fa-solid fa-file-pdf" style="color: ${isComp ? 'var(--accent-red)' : 'var(--accent-green)'}; margin-right: 4px;"></i>
                    ${escapeHtml(displayName)}
                </span>
                ${badgeHtml}
            </div>
            ${relPathHint}
            <div style="font-size:11.5px; font-family:var(--font-mono); color:var(--text-muted); margin-top:3px;">
                ${sizeHtml}
            </div>
        </div>
        <a class="btn-dl-individual" href="${item.download_url}" target="_blank">
            <i class="fa-solid fa-download"></i>
            <span>Unduh</span>
        </a>
    `;
    return row;
}

function resetForm() {
    clearSelectedFiles();
    document.getElementById('resultSection').classList.remove('show');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showSmallPopup(filename, sizeHuman) {
    document.getElementById('modalFileName').textContent = filename;
    document.getElementById('modalFileSize').textContent = sizeHuman;
    document.getElementById('modalSmall').classList.add('show');
    document.getElementById('modalOkBtn').focus();
}

function closeModal() {
    document.getElementById('modalSmall').classList.remove('show');
}

// ── DATABASE HISTORY MODAL FUNCTIONS ──
function openDbHistoryModal() {
    document.getElementById('modalDbHistory').classList.add('show');
    loadDbHistory(false);
}

function closeDbHistoryModal() {
    document.getElementById('modalDbHistory').classList.remove('show');
}

async function loadDbHistory(forceRefresh = false) {
    const loading = document.getElementById('dbLoadingIndicator');
    const table = document.getElementById('dbHistoryTable');
    const emptyNotice = document.getElementById('dbEmptyNotice');
    const btnRefresh = document.getElementById('btnRefreshDb');

    if (btnRefresh) btnRefresh.disabled = true;
    loading.style.display = 'block';
    table.style.display = 'none';
    emptyNotice.style.display = 'none';

    try {
        const res = await fetch(URL_HISTORY_ENDPOINT);
        const data = await res.json();

        if (!data.success) {
            throw new Error(data.message || 'Gagal memuat database.');
        }

        cachedDbJobs = data.jobs || [];

        // Update topbar badge
        const badgeEl = document.getElementById('topbarDbBadge');
        if (badgeEl) {
            badgeEl.textContent = `${data.stats.total} Data`;
        }

        // Update modal metrics
        document.getElementById('dbMetricTotal').textContent = data.stats.total;
        document.getElementById('dbMetricCompleted').textContent = data.stats.completed;
        document.getElementById('dbMetricSaved').textContent = data.stats.total_saved;

        renderDbTable(cachedDbJobs);

    } catch (err) {
        console.error('Error fetching db history:', err);
        loading.innerHTML = `<span style="color:#ef4444;"><i class="fa-solid fa-triangle-exclamation"></i> Gagal terhubung ke database: ${escapeHtml(err.message)}</span>`;
    } finally {
        if (btnRefresh) btnRefresh.disabled = false;
    }
}

async function refreshTopbarDbCount() {
    try {
        const res = await fetch(URL_HISTORY_ENDPOINT + '?limit=1');
        const data = await res.json();
        if (data.success && data.stats) {
            const badgeEl = document.getElementById('topbarDbBadge');
            if (badgeEl) {
                badgeEl.textContent = `${data.stats.total} Data`;
            }
        }
    } catch (e) {}
}

function renderDbTable(jobs) {
    const loading = document.getElementById('dbLoadingIndicator');
    const table = document.getElementById('dbHistoryTable');
    const tbody = document.getElementById('dbHistoryTableBody');
    const emptyNotice = document.getElementById('dbEmptyNotice');
    const counter = document.getElementById('dbRecordCounter');

    loading.style.display = 'none';

    if (!jobs || jobs.length === 0) {
        table.style.display = 'none';
        emptyNotice.style.display = 'block';
        counter.textContent = '0 data';
        return;
    }

    tbody.innerHTML = '';
    jobs.forEach(job => {
        const tr = document.createElement('tr');
        const statusBadge = job.status === 'completed'
            ? `<span class="status-pill-chip success" style="font-size: 10px; padding: 2px 7px;">Selesai</span>`
            : `<span class="status-pill-chip error" style="font-size: 10px; padding: 2px 7px;" title="${escapeHtml(job.error_message || '')}">Gagal</span>`;

        tr.innerHTML = `
            <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted);">#${job.id}</td>
            <td style="white-space: nowrap; font-size: 11px; color: var(--text-muted);">${escapeHtml(job.compressed_at || '-')}</td>
            <td>
                <div style="font-weight: 600; color: var(--text-primary); max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(job.original_filename)}">
                    ${escapeHtml(job.original_filename)}
                </div>
                ${job.original_path && job.original_path !== job.original_filename ? `<div style="font-size: 11px; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 280px;">${escapeHtml(job.original_path)}</div>` : ''}
            </td>
            <td style="font-family: var(--font-mono);">${job.original_human}</td>
            <td style="font-family: var(--font-mono); font-weight: 600;">${job.compressed_human}</td>
            <td style="font-family: var(--font-mono); color: #059669;">${job.saved_human}</td>
            <td>
                <span style="font-family: var(--font-mono); font-weight: 700; color: ${job.compression_ratio > 0 ? 'var(--primary)' : 'var(--text-muted)'};">
                    ${job.compression_ratio > 0 ? (job.compression_ratio + '%') : '—'}
                </span>
            </td>
            <td>${statusBadge}</td>
        `;
        tbody.appendChild(tr);
    });

    counter.textContent = `Menampilkan ${jobs.length} data`;
    table.style.display = 'table';
    emptyNotice.style.display = 'none';
}

function filterDbTable() {
    const q = (document.getElementById('dbSearchInput').value || '').toLowerCase().trim();
    if (!q) {
        renderDbTable(cachedDbJobs);
        return;
    }
    const filtered = cachedDbJobs.filter(j =>
        (j.original_filename && j.original_filename.toLowerCase().includes(q)) ||
        (j.original_path && j.original_path.toLowerCase().includes(q)) ||
        (j.session_id && j.session_id.toLowerCase().includes(q)) ||
        String(j.id).includes(q)
    );
    renderDbTable(filtered);
}

document.getElementById('modalSmall').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

const modalDbHist = document.getElementById('modalDbHistory');
if (modalDbHist) {
    modalDbHist.addEventListener('click', function(e) {
        if (e.target === this) closeDbHistoryModal();
    });
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal();
        closeDbHistoryModal();
    }
});

function showToast(msg) {
    const el = document.createElement('div');
    el.className = 'toast-card';
    el.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="color: var(--accent-red);"></i> ${msg}`;
    document.getElementById('toastBox').appendChild(el);
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(100%)';
        el.style.transition = 'all 0.3s ease';
        setTimeout(() => el.remove(), 300);
    }, 4000);
}

function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' B';
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/* ============================================================
   AMBIENT FLOATING PARTICLES (Pesawat & Buku Melayang Lembut)
   ============================================================ */
(function initAmbientFloatingElements() {
    const canvas = document.getElementById('ambientFloatingCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let width = 0;
    let height = 0;
    let dpr = window.devicePixelRatio || 1;

    function resize() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.scale(dpr, dpr);
    }
    window.addEventListener('resize', resize);
    resize();

    // Tipe elemen melayang: 'plane' (pesawat kertas), 'book' (buku terbuka), 'doc' (lembar berkas)
    const types = ['plane', 'book', 'doc', 'plane', 'book'];
    const itemCount = Math.max(16, Math.min(26, Math.floor(window.innerWidth / 75)));
    const items = [];

    class FloatingItem {
        constructor(isInitial = false) {
            this.reset(isInitial);
        }

        reset(isInitial = false) {
            this.type = types[Math.floor(Math.random() * types.length)];
            this.x = Math.random() * width;
            this.y = isInitial ? Math.random() * height : height + 30 + Math.random() * 50;
            this.baseX = this.x;
            this.size = 20 + Math.random() * 16; // 20px - 36px
            this.speedY = 0.38 + Math.random() * 0.45; // Melayang halus ke atas (0.38 - 0.83 px/frame)
            this.swayAngle = Math.random() * Math.PI * 2;
            this.swaySpeed = 0.01 + Math.random() * 0.015;
            this.swayRadius = 24 + Math.random() * 32;
            this.angle = (Math.random() - 0.5) * 0.35;
            this.rotSpeed = (Math.random() - 0.5) * 0.003;
            this.baseAlpha = 0.18 + Math.random() * 0.16; // Transparansi halus & nyaman di mata
        }

        update() {
            this.y -= this.speedY;
            this.swayAngle += this.swaySpeed;
            this.x = this.baseX + Math.sin(this.swayAngle) * this.swayRadius;
            this.angle += this.rotSpeed;

            if (this.y < -60) {
                this.reset(false);
            }
        }

        draw(ctx, isDark) {
            // Fade-in halus di bagian bawah dan fade-out di bagian atas
            let fade = 1;
            if (this.y > height - 120) {
                fade = Math.max(0, (height - this.y) / 120);
            } else if (this.y < 140) {
                fade = Math.max(0, this.y / 140);
            }

            const alpha = this.baseAlpha * fade * (isDark ? 0.9 : 1.0);
            if (alpha <= 0.01) return;

            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.angle);

            const strokeColor = isDark
                ? `rgba(165, 180, 252, ${alpha * 1.3})`
                : `rgba(99, 102, 241, ${alpha * 1.2})`;
            const fillColor = isDark
                ? `rgba(99, 102, 241, ${alpha * 0.3})`
                : `rgba(99, 102, 241, ${alpha * 0.16})`;
            const shadowFill = isDark
                ? `rgba(129, 140, 248, ${alpha * 0.45})`
                : `rgba(79, 70, 229, ${alpha * 0.25})`;

            ctx.strokeStyle = strokeColor;
            ctx.lineWidth = 1.3;
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';

            const s = this.size;

            if (this.type === 'plane') {
                // Pesawat kertas origami
                ctx.beginPath();
                ctx.moveTo(0, -s * 0.75); // Hidung pesawat
                ctx.lineTo(-s * 0.6, s * 0.45); // Sayap kiri
                ctx.lineTo(0, s * 0.25); // Lipatan tengah
                ctx.closePath();
                ctx.fillStyle = fillColor;
                ctx.fill();
                ctx.stroke();

                ctx.beginPath();
                ctx.moveTo(0, -s * 0.75);
                ctx.lineTo(s * 0.6, s * 0.45); // Sayap kanan
                ctx.lineTo(0, s * 0.25);
                ctx.closePath();
                ctx.fillStyle = shadowFill;
                ctx.fill();
                ctx.stroke();

                // Garis lipatan tengah
                ctx.beginPath();
                ctx.moveTo(0, -s * 0.75);
                ctx.lineTo(0, s * 0.5);
                ctx.stroke();

            } else if (this.type === 'book') {
                // Buku terbuka melayang
                const hw = s * 0.58;
                const hh = s * 0.42;
                const curl = s * 0.14;

                // Halaman kiri
                ctx.beginPath();
                ctx.moveTo(0, -hh * 0.6);
                ctx.quadraticCurveTo(-hw * 0.5, -hh - curl, -hw, -hh * 0.75);
                ctx.lineTo(-hw, hh * 0.72);
                ctx.quadraticCurveTo(-hw * 0.5, hh + curl, 0, hh * 0.88);
                ctx.closePath();
                ctx.fillStyle = fillColor;
                ctx.fill();
                ctx.stroke();

                // Halaman kanan
                ctx.beginPath();
                ctx.moveTo(0, -hh * 0.6);
                ctx.quadraticCurveTo(hw * 0.5, -hh - curl, hw, -hh * 0.75);
                ctx.lineTo(hw, hh * 0.72);
                ctx.quadraticCurveTo(hw * 0.5, hh + curl, 0, hh * 0.88);
                ctx.closePath();
                ctx.fillStyle = shadowFill;
                ctx.fill();
                ctx.stroke();

                // Punggung buku
                ctx.beginPath();
                ctx.moveTo(0, -hh * 0.6);
                ctx.lineTo(0, hh * 0.88);
                ctx.stroke();

                // Garis teks dekoratif
                ctx.beginPath();
                ctx.moveTo(-hw * 0.25, -hh * 0.1);
                ctx.lineTo(-hw * 0.75, -hh * 0.1);
                ctx.moveTo(-hw * 0.25, hh * 0.22);
                ctx.lineTo(-hw * 0.65, hh * 0.22);
                ctx.moveTo(hw * 0.25, -hh * 0.1);
                ctx.lineTo(hw * 0.75, -hh * 0.1);
                ctx.moveTo(hw * 0.25, hh * 0.22);
                ctx.lineTo(hw * 0.65, hh * 0.22);
                ctx.lineWidth = 1;
                ctx.stroke();

            } else {
                // Lembaran dokumen PDF dengan sudut terlipat
                const w = s * 0.48;
                const h = s * 0.66;
                const f = s * 0.18;

                ctx.beginPath();
                ctx.moveTo(-w, -h);
                ctx.lineTo(w - f, -h);
                ctx.lineTo(w, -h + f);
                ctx.lineTo(w, h);
                ctx.lineTo(-w, h);
                ctx.closePath();
                ctx.fillStyle = fillColor;
                ctx.fill();
                ctx.stroke();

                // Lipatan sudut dokumen
                ctx.beginPath();
                ctx.moveTo(w - f, -h);
                ctx.lineTo(w - f, -h + f);
                ctx.lineTo(w, -h + f);
                ctx.fillStyle = shadowFill;
                ctx.fill();
                ctx.stroke();

                // Garis teks dokumen
                ctx.beginPath();
                ctx.moveTo(-w * 0.6, -h * 0.3);
                ctx.lineTo(w * 0.4, -h * 0.3);
                ctx.moveTo(-w * 0.6, 0);
                ctx.lineTo(w * 0.55, 0);
                ctx.moveTo(-w * 0.6, h * 0.3);
                ctx.lineTo(w * 0.2, h * 0.3);
                ctx.lineWidth = 1;
                ctx.stroke();
            }

            ctx.restore();
        }
    }

    for (let i = 0; i < itemCount; i++) {
        items.push(new FloatingItem(true));
    }

    let isTabVisible = !document.hidden;
    document.addEventListener('visibilitychange', () => {
        isTabVisible = !document.hidden;
    });

    function loop() {
        requestAnimationFrame(loop);
        if (!isTabVisible) return;

        ctx.clearRect(0, 0, width, height);

        const isDark = document.documentElement.getAttribute('data-theme') === 'dark-titanium';

        for (let i = 0; i < items.length; i++) {
            items[i].update();
            items[i].draw(ctx, isDark);
        }
    }

    requestAnimationFrame(loop);
})();
</script>
</body>
</html>
