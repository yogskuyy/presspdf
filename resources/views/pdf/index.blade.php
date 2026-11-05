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
        --font-main: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;

        /* Light Theme - Clean Modern Slate */
        --bg-base: #f8fafc;
        --bg-card: #ffffff;
        --bg-card-solid: #ffffff;
        --bg-elevated: #f1f5f9;
        --border-subtle: #e2e8f0;
        --border-hover: #cbd5e1;
        --border-strong: #94a3b8;

        --text-primary: #0f172a;
        --text-secondary: #334155;
        --text-muted: #64748b;

        --primary: #2563eb;
        --primary-hover: #1d4ed8;
        --primary-light: #eff6ff;
        --primary-gradient: #2563eb;

        --accent-red: #dc2626;
        --accent-red-light: #fef2f2;
        --accent-green: #16a34a;
        --accent-green-light: #f0fdf4;
        --accent-amber: #d97706;
        --accent-amber-light: #fffbeb;
        --accent-blue: #0284c7;
        --accent-blue-light: #f0f9ff;

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 2px 4px -1px rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);

        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 10px;
        --radius-pill: 9999px;
    }

    [data-theme="dark-titanium"] {
        /* Dark Theme - Sleek Modern Zinc */
        --bg-base: #09090b;
        --bg-card: #121215;
        --bg-card-solid: #121215;
        --bg-elevated: #1a1a1f;
        --border-subtle: #27272a;
        --border-hover: #3f3f46;
        --border-strong: #52525b;

        --text-primary: #f4f4f5;
        --text-secondary: #a1a1aa;
        --text-muted: #71717a;

        --primary: #3b82f6;
        --primary-hover: #60a5fa;
        --primary-light: rgba(59, 130, 246, 0.12);
        --primary-gradient: #3b82f6;

        --accent-red: #ef4444;
        --accent-red-light: rgba(239, 68, 68, 0.12);
        --accent-green: #22c55e;
        --accent-green-light: rgba(34, 197, 94, 0.12);
        --accent-amber: #f59e0b;
        --accent-amber-light: rgba(245, 158, 11, 0.12);
        --accent-blue: #38bdf8;
        --accent-blue-light: rgba(56, 189, 248, 0.12);

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
        --shadow-md: 0 2px 4px -1px rgba(0, 0, 0, 0.5);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.6);
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    body {
        font-family: var(--font-main);
        background-color: var(--bg-base);
        color: var(--text-primary);
        line-height: 1.5;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .app-viewport {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px 20px 48px;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 20px;
        flex: 1;
    }

    /* TOPBAR */
    .app-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
    }

    .brand-cluster {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-icon-box {
        width: 36px;
        height: 36px;
        border-radius: var(--radius-sm);
        background: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 17px;
    }

    .brand-title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -0.3px;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .brand-badge {
        font-size: 11px;
        font-family: var(--font-mono);
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        background: var(--primary-light);
        color: var(--primary);
    }

    .brand-subline {
        font-size: 12px;
        color: var(--text-muted);
    }

    .theme-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        font-family: var(--font-main);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .theme-toggle-btn:hover {
        color: var(--text-primary);
        background: var(--bg-card);
        border-color: var(--border-hover);
    }

    /* STEPPER BAR */
    .workflow-stepper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
    }

    .step-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
    }

    .step-pill.active {
        color: var(--text-primary);
        border-color: var(--border-hover);
        background: var(--bg-card);
    }

    .step-num {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--bg-elevated);
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .step-pill.active .step-num {
        background: var(--primary);
        color: #ffffff;
    }

    /* MAIN TWO-COLUMN GRID */
    .main-workspace-grid {
        display: grid;
        grid-template-columns: 1.18fr 0.82fr;
        gap: 18px;
        align-items: start;
    }

    .modern-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-subtle);
        background: var(--bg-card);
    }

    .card-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 4px;
        background: var(--bg-elevated);
        color: var(--text-secondary);
        border: 1px solid var(--border-subtle);
    }

    .card-body {
        padding: 18px;
    }

    /* THRESHOLD FILTER SCALE CARD */
    .threshold-control-card {
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        padding: 14px 16px;
        margin-bottom: 16px;
    }

    .threshold-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .threshold-title-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .threshold-icon-circle {
        width: 30px;
        height: 30px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--accent-red);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }

    .threshold-title-text {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .threshold-active-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        background: var(--accent-red-light);
        color: var(--accent-red);
        border: 1px solid rgba(220, 38, 38, 0.2);
    }

    .threshold-desc-text {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .threshold-val-counter {
        display: flex;
        align-items: center;
        gap: 3px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        padding: 4px 10px;
        border-radius: var(--radius-sm);
        flex-shrink: 0;
    }

    .threshold-val-counter:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px var(--primary-light);
    }

    .threshold-val-prefix {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .threshold-number-input {
        width: 44px;
        border: none;
        background: transparent;
        font-family: var(--font-mono);
        font-size: 16px;
        font-weight: 700;
        color: var(--primary);
        text-align: center;
        outline: none;
        padding: 0;
        margin: 0;
        -moz-appearance: textfield;
    }

    .threshold-number-input::-webkit-inner-spin-button,
    .threshold-number-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .threshold-unit {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .threshold-slider-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .filter-range-slider {
        accent-color: var(--primary);
    }

    .threshold-ticks {
        position: relative;
        width: 100%;
        height: 22px;
        margin-top: 4px;
    }

    .threshold-ticks .threshold-tick-item {
        position: absolute;
        top: 0;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 4px;
        white-space: nowrap;
        font-size: 11px;
        font-weight: 500;
        color: var(--text-muted);
        user-select: none;
        transition: color 0.15s ease;
    }

    .threshold-ticks .threshold-tick-item:hover {
        color: var(--primary);
    }

    .threshold-ticks .threshold-tick-item.klaim-highlight {
        color: var(--accent-red);
        font-weight: 700;
    }

    .threshold-ticks .threshold-tick-item.preset-active {
        font-weight: 700 !important;
        color: var(--accent-red) !important;
        background: var(--accent-red-light) !important;
    }

    /* DROPZONE */
    .dropzone-view {
        border: 1.5px dashed var(--border-strong);
        border-radius: var(--radius-md);
        padding: 38px 20px;
        text-align: center;
        cursor: pointer;
        background: var(--bg-card);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        transition: all 0.15s ease;
    }

    .dropzone-view:hover, .dropzone-view.drag-active {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .dz-icon-circle {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 20px;
    }

    .dz-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .dz-desc {
        font-size: 12.5px;
        color: var(--text-secondary);
        max-width: 440px;
        line-height: 1.5;
    }

    .dz-buttons-row {
        display: flex;
        gap: 10px;
        margin-top: 4px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn-primary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: var(--radius-sm);
        background: var(--primary);
        color: #ffffff;
        font-family: var(--font-main);
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-primary-pill:hover {
        background: var(--primary-hover);
    }

    .btn-secondary-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        color: var(--text-primary);
        border: 1px solid var(--border-hover);
        font-family: var(--font-main);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-secondary-pill:hover {
        background: var(--bg-elevated);
        border-color: var(--text-muted);
    }

    .dz-chips-ribbon {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 6px;
    }

    .dz-chip {
        font-size: 11px;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: 4px;
        background: var(--bg-elevated);
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* WORKSPACE QUEUE */
    .workspace-view {
        display: none;
        flex-direction: column;
        gap: 14px;
    }

    .workspace-view.show {
        display: flex;
    }

    .queue-summary-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        flex-wrap: wrap;
        gap: 10px;
    }

    .queue-folder-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .folder-icon-circle {
        width: 30px;
        height: 30px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .queue-stat-badges {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .stat-badge-pill {
        font-size: 11.5px;
        font-weight: 500;
        padding: 3px 9px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
    }

    .stat-badge-pill.needs {
        background: var(--accent-red-light);
        border-color: rgba(220, 38, 38, 0.2);
        color: var(--accent-red);
        font-weight: 600;
    }

    .stat-badge-pill.safe {
        background: var(--accent-green-light);
        border-color: rgba(22, 163, 74, 0.2);
        color: var(--accent-green);
    }

    .filter-tabs-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .active-filter-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .active-filter-count-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        background: var(--accent-red-light);
        color: var(--accent-red);
    }

    .queue-action-btns {
        display: flex;
        gap: 6px;
    }

    .btn-add-mini {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        font-family: var(--font-main);
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-add-mini:hover {
        background: var(--bg-elevated);
        color: var(--text-primary);
        border-color: var(--border-hover);
    }

    .filter-notice-strip {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: var(--primary-light);
        border: 1px solid rgba(37, 99, 235, 0.15);
        border-radius: var(--radius-sm);
        font-size: 12px;
        color: var(--primary);
    }

    .files-list-tray {
        display: flex;
        flex-direction: column;
        gap: 6px;
        max-height: 420px;
        overflow-y: auto;
        padding-right: 2px;
    }

    .empty-queue-notice {
        text-align: center;
        padding: 32px 16px;
        background: var(--bg-elevated);
        border: 1px dashed var(--border-subtle);
        border-radius: var(--radius-sm);
    }

    .file-item-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        gap: 10px;
        transition: border-color 0.15s ease;
    }

    .file-item-card:hover {
        border-color: var(--border-hover);
    }

    .file-item-card.needs-comp {
        border-left: 3px solid var(--accent-red);
    }

    .file-main-info {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }

    .file-doc-icon {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        background: var(--bg-elevated);
        color: var(--accent-red);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }

    .file-text-col {
        min-width: 0;
        flex: 1;
    }

    .file-name-text {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-path-text {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .file-pills-col {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .size-pill-tag {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 4px;
        background: var(--bg-elevated);
        color: var(--text-secondary);
        border: 1px solid var(--border-subtle);
    }

    .status-pill-chip {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 4px;
        white-space: nowrap;
    }

    .status-pill-chip.needs-comp {
        background: var(--accent-red-light);
        color: var(--accent-red);
    }

    .status-pill-chip.compressing {
        background: var(--accent-amber-light);
        color: var(--accent-amber);
    }

    .status-pill-chip.success {
        background: var(--accent-green-light);
        color: var(--accent-green);
    }

    .status-pill-chip.error {
        background: var(--accent-red-light);
        color: var(--accent-red);
    }

    .btn-file-del {
        background: transparent;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 4px;
        font-size: 13px;
        transition: all 0.15s ease;
    }

    .btn-file-del:hover {
        background: var(--accent-red-light);
        color: var(--accent-red);
    }

    /* RIGHT MODULE - TARGET PRESETS */
    .presets-container {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .preset-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        cursor: pointer;
        transition: all 0.15s ease;
        gap: 12px;
    }

    .preset-card:hover {
        border-color: var(--border-hover);
        background: var(--bg-elevated);
    }

    .preset-card.active {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .preset-card-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .preset-radio-indicator {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1.5px solid var(--border-hover);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.15s ease;
    }

    .preset-card.active .preset-radio-indicator {
        border-color: var(--primary);
        background: var(--primary);
    }

    .preset-radio-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ffffff;
        opacity: 0;
        transition: opacity 0.15s ease;
    }

    .preset-card.active .preset-radio-dot {
        opacity: 1;
    }

    .preset-title-wrap {
        display: flex;
        flex-direction: column;
    }

    .preset-name {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .preset-desc {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 1px;
    }

    .preset-badge-rec {
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 3px;
        background: var(--primary);
        color: #ffffff;
    }

    .preset-size-num {
        font-size: 15px;
        font-family: var(--font-mono);
        font-weight: 700;
        color: var(--primary);
        flex-shrink: 0;
    }

    /* SLIDER DRAWER */
    .slider-accordion-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        font-size: 12px;
        font-weight: 500;
        color: var(--text-secondary);
        cursor: pointer;
        margin-top: 4px;
        transition: all 0.15s ease;
    }

    .slider-accordion-toggle:hover {
        background: var(--bg-card);
        color: var(--text-primary);
        border-color: var(--border-hover);
    }

    .slider-drawer {
        display: none;
        flex-direction: column;
        gap: 8px;
        padding: 12px 14px;
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
        height: 5px;
        border-radius: 4px;
        cursor: pointer;
    }

    .range-ticks-text {
        display: flex;
        justify-content: space-between;
        font-size: 10.5px;
        font-family: var(--font-mono);
        color: var(--text-muted);
    }

    /* HINT BANNER */
    .mode-hint-banner {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        background: var(--bg-elevated);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        font-size: 12px;
        margin-top: 12px;
    }

    /* MASTER ACTIONS */
    .master-action-row {
        display: flex;
        gap: 8px;
        margin-top: 16px;
    }

    .btn-compress-master {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: var(--radius-sm);
        background: var(--primary);
        color: #ffffff;
        font-family: var(--font-main);
        font-size: 13.5px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-compress-master:hover:not(:disabled) {
        background: var(--primary-hover);
    }

    .btn-compress-master:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-cancel-clean {
        padding: 11px 16px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        font-family: var(--font-main);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-cancel-clean:hover {
        background: var(--bg-elevated);
        color: var(--text-primary);
        border-color: var(--border-hover);
    }

    /* PROGRESS BAY */
    .progress-bay {
        display: none;
        flex-direction: column;
        gap: 6px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: var(--radius-sm);
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
        font-size: 12px;
        font-weight: 600;
    }

    .progress-track {
        height: 6px;
        border-radius: 99px;
        background: var(--border-subtle);
        overflow: hidden;
    }

    .progress-fill-bar {
        height: 100%;
        width: 0%;
        background: var(--primary);
        border-radius: 99px;
        transition: width 0.2s ease;
    }

    /* SPINNER */
    .mini-spinner {
        width: 14px;
        height: 14px;
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
        gap: 12px;
    }

    .spec-card {
        padding: 14px 16px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .spec-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .spec-icon-box.red { background: var(--accent-red-light); color: var(--accent-red); }
    .spec-icon-box.amber { background: var(--accent-amber-light); color: var(--accent-amber); }
    .spec-icon-box.blue { background: var(--accent-blue-light); color: var(--accent-blue); }
    .spec-icon-box.green { background: var(--accent-green-light); color: var(--accent-green); }

    .spec-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .spec-desc {
        font-size: 11.5px;
        color: var(--text-secondary);
        line-height: 1.45;
    }

    .spec-tag-sub {
        font-size: 10px;
        font-family: var(--font-mono);
        font-weight: 600;
        color: var(--text-muted);
        margin-top: auto;
    }

    /* RESULTS SECTION */
    .result-section {
        display: none;
        flex-direction: column;
        gap: 16px;
    }

    .result-section.show {
        display: flex;
    }

    .results-banner {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: var(--bg-card);
        border-bottom: 1px solid var(--border-subtle);
    }

    .success-check-badge {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-sm);
        background: var(--accent-green-light);
        color: var(--accent-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .results-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 14px 20px;
        background: var(--bg-elevated);
        border-bottom: 1px solid var(--border-subtle);
    }

    .res-stat-box {
        background: var(--bg-card);
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-subtle);
    }

    .res-stat-label {
        font-size: 11px;
        font-weight: 500;
        color: var(--text-muted);
    }

    .res-stat-num {
        font-size: 17px;
        font-family: var(--font-mono);
        font-weight: 700;
        color: var(--text-primary);
        margin-top: 1px;
    }

    .res-stat-num.highlight {
        color: var(--accent-green);
    }

    .folder-structure-guarantee-box {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 16px;
        background: var(--accent-green-light);
        border: 1px solid rgba(22, 163, 74, 0.2);
        border-radius: var(--radius-sm);
        margin-bottom: 14px;
        color: #064e3b;
        font-size: 12px;
    }

    [data-theme="dark-titanium"] .folder-structure-guarantee-box {
        color: #a7f3d0;
    }

    .folder-result-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        margin-bottom: 10px;
        overflow: hidden;
    }

    .folder-result-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        background: var(--bg-elevated);
        border-bottom: 1px solid var(--border-subtle);
        flex-wrap: wrap;
        gap: 8px;
    }

    .folder-result-title {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .folder-count-badge {
        font-size: 11px;
        color: var(--text-muted);
    }

    .btn-dl-folder-zip {
        padding: 5px 12px;
        border-radius: var(--radius-sm);
        background: var(--primary);
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background 0.15s ease;
    }

    .btn-dl-folder-zip:hover {
        background: var(--primary-hover);
    }

    .folder-result-files-list {
        display: flex;
        flex-direction: column;
    }

    .result-row-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-bottom: 1px solid var(--border-subtle);
        gap: 10px;
    }

    .result-row-card:last-child {
        border-bottom: none;
    }

    .btn-dl-individual {
        padding: 5px 10px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--text-primary);
        font-size: 11.5px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s ease;
    }

    .btn-dl-individual:hover {
        background: var(--primary-light);
        color: var(--primary);
        border-color: var(--primary);
    }

    .results-cta-deck {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        background: var(--bg-card);
        border-top: 1px solid var(--border-subtle);
        flex-wrap: wrap;
    }

    .btn-dl-all-zip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: var(--radius-sm);
        background: var(--primary);
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .btn-dl-all-zip:hover {
        background: var(--primary-hover);
    }

    /* FOOTER */
    .app-footer {
        text-align: center;
        padding: 20px;
        font-size: 12px;
        color: var(--text-muted);
        margin-top: auto;
    }

    /* TOAST */
    .toast-tray {
        position: fixed;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        z-index: 9999;
    }

    .toast-card {
        padding: 10px 14px;
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-md);
        font-size: 12.5px;
        font-weight: 500;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* MODAL */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        z-index: 9999;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-card {
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        max-width: 440px;
        width: 100%;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 12px;
    }

    .modal-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-sm);
        background: var(--accent-green-light);
        color: var(--accent-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .modal-card-wide {
        max-width: 900px !important;
        width: 95% !important;
        text-align: left !important;
        align-items: stretch !important;
    }

    /* DATABASE HISTORY & MONITOR STYLES */
    .db-history-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: var(--radius-sm);
        background: var(--bg-card);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        font-family: var(--font-main);
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .db-history-btn:hover {
        background: var(--bg-elevated);
        border-color: var(--primary);
        color: var(--text-primary);
    }

    .db-live-indicator {
        position: relative;
        width: 7px;
        height: 7px;
        background: #16a34a;
        border-radius: 50%;
        display: inline-block;
    }

    .db-live-pulse {
        position: absolute;
        inset: -2px;
        border-radius: 50%;
        background: rgba(22, 163, 74, 0.4);
    }

    .db-badge {
        font-size: 11px;
        font-weight: 600;
        background: var(--primary-light);
        color: var(--primary);
        padding: 1px 6px;
        border-radius: 4px;
    }

    .db-history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .db-history-table tr {
        border-bottom: 1px solid var(--border-subtle);
    }

    .db-history-table tbody tr:hover {
        background: var(--bg-elevated);
    }

    .db-history-table td, .db-history-table th {
        padding: 8px 10px;
        color: var(--text-secondary);
        vertical-align: middle;
        text-align: left;
    }

    .db-history-table th {
        color: var(--text-muted);
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* RESPONSIVE */
    @media (max-width: 980px) {
        .main-workspace-grid {
            grid-template-columns: 1fr;
        }
        .specs-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .app-viewport {
            padding: 12px 10px 36px;
            gap: 14px;
        }
        .workflow-stepper {
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }
        .step-pill {
            padding: 6px 8px;
            font-size: 11px;
        }
        .dz-buttons-row {
            flex-direction: column;
            width: 100%;
        }
        .btn-primary-pill, .btn-secondary-pill {
            width: 100%;
            justify-content: center;
        }
        .master-action-row {
            flex-direction: column;
        }
        .btn-compress-master, .btn-cancel-clean {
            width: 100%;
            text-align: center;
        }
        .results-stats-row {
            grid-template-columns: 1fr;
        }
        .specs-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
<body>



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
                    <span class="brand-badge">v2.5</span>
                </div>
                <div class="brand-subline">Kompresi &amp; Optimasi Dokumen PDF</div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
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
            <span>Input File / Folder (Bebas Ukuran)</span>
        </div>
        <div class="step-pill active">
            <span class="step-num">2</span>
            <span>Filter Ambang Batas (&ge; <span class="threshold-label-val">34</span> MB)</span>
        </div>
        <div class="step-pill active">
            <span class="step-num">3</span>
            <span>Target Presisi (8 / 12 / 16 MB)</span>
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
                <span class="card-badge">Bebas Ukuran (Folder / File Berapa Pun)</span>
            </div>

            <div class="card-body">
                <!-- THRESHOLD FILTER SCALE CARD (Fleksibel, Default: 34 MB) -->
                <div class="threshold-control-card" id="thresholdControlCard">
                    <div class="threshold-top-row">
                        <div class="threshold-title-left">
                            <div class="threshold-icon-circle">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div>
                                <div class="threshold-title-text">
                                    <span>Skala Ambang Batas Filter</span>
                                    <span class="threshold-active-badge" id="thresholdBadgeVal">&ge; 34 MB</span>
                                </div>
                                <div class="threshold-desc-text">Hanya berkas &ge; batas ini yang dimunculkan &amp; dieksekusi. Berkas lainnya otomatis dilewati.</div>
                            </div>
                        </div>
                        <div class="threshold-val-counter" title="Ketik angka ambang batas (MB) atau geser slider di bawah">
                            <span class="threshold-val-prefix">&ge;</span>
                            <input type="number" id="thresholdNumberInput" min="1" max="500" value="34" class="threshold-number-input"
                                   oninput="handleThresholdDirectInput(this.value)"
                                   onblur="handleThresholdBlur(this)">
                            <span class="threshold-unit">MB</span>
                        </div>
                    </div>

                    <div class="threshold-slider-container">
                        <input type="range" class="modern-range-input filter-range-slider" id="thresholdSlider"
                               min="0" max="700" step="1" value="400"
                               oninput="handleThresholdSliderStep(this.value)">
                        <div class="threshold-ticks">
                            <span class="threshold-tick-item" data-val="5" style="left: 0%; transform: translateX(0);" onclick="setThresholdVal(5)">5 MB</span>
                            <span class="threshold-tick-item" data-val="10" style="left: 14.286%; transform: translateX(-50%);" onclick="setThresholdVal(10)">10 MB</span>
                            <span class="threshold-tick-item" data-val="19" style="left: 28.571%; transform: translateX(-50%);" onclick="setThresholdVal(19)">19 MB (BPJS)</span>
                            <span class="threshold-tick-item" data-val="25" style="left: 42.857%; transform: translateX(-50%);" onclick="setThresholdVal(25)">25 MB</span>
                            <span class="threshold-tick-item klaim-highlight" data-val="34" style="left: 57.143%; transform: translateX(-50%);" onclick="setThresholdVal(34)">34 MB (Klaim)</span>
                            <span class="threshold-tick-item" data-val="50" style="left: 71.429%; transform: translateX(-50%);" onclick="setThresholdVal(50)">50 MB</span>
                            <span class="threshold-tick-item" data-val="75" style="left: 85.714%; transform: translateX(-50%);" onclick="setThresholdVal(75)">75 MB</span>
                            <span class="threshold-tick-item" data-val="100" style="left: 100%; transform: translateX(-100%);" onclick="setThresholdVal(100)">100 MB</span>
                        </div>
                    </div>
                </div>

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
                        Sistem memindai folder berukuran bebas (2 GB+). Hanya berkas <strong>&ge; <span class="threshold-label-val">34</span> MB</strong> yang dimunculkan untuk dieksekusi. Berkas lainnya di bawah filter tidak ditampilkan. Nama file PDF kode klaim <strong>tetap 100% utuh</strong>.
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
                        <span class="dz-chip"><i class="fa-solid fa-check" style="color: var(--accent-green);"></i> Bebas Ukuran (Folder 2 GB+ Didukung)</span>
                        <span class="dz-chip"><i class="fa-solid fa-filter" style="color: var(--accent-red);"></i> Filter Ambang Batas &ge; <span class="threshold-label-val">34</span> MB</span>
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
                            <span class="stat-badge-pill" title="Total seluruh berkas PDF di dalam folder"><span id="metricTotalCount">0</span> Total di Folder</span>
                            <span class="stat-badge-pill needs">🔴 <span id="metricNeedsCount">0</span> Terbaca (&ge; <span class="threshold-label-val">34</span> MB)</span>
                            <span class="stat-badge-pill safe" title="Berkas di bawah ambang batas otomatis disembunyikan">🟢 <span id="metricSafeCount">0</span> Lainnya Dilewati</span>
                        </div>
                    </div>

                    <div class="filter-tabs-row">
                        <div class="active-filter-header">
                            <i class="fa-solid fa-filter" style="color: var(--accent-red);"></i>
                            <span>Daftar Berkas Terbaca untuk Eksekusi:</span>
                            <span class="active-filter-count-badge"><span id="tabCountNeeds">0</span> File (&ge; <span class="threshold-label-val">34</span> MB)</span>
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

                    <div class="filter-notice-strip" id="filterNoticeStrip">
                        <i class="fa-solid fa-circle-info"></i>
                        <span id="filterNoticeText">Menampilkan hanya berkas yang berukuran &ge; 34 MB sesuai filter. Berkas lainnya tidak ditampilkan &amp; tidak dieksekusi.</span>
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
                <span class="card-badge" id="targetSizeLabel" style="color: var(--primary); font-weight: 800;">12.0 MB</span>
            </div>

            <div class="card-body">
                <!-- PRESET OPTIONS -->
                <div class="presets-container">
                    <!-- 8.0 MB -->
                    <div class="preset-card" id="presetLow" onclick="selectCompressionPreset(8.0, 'low')">
                        <div class="preset-card-left">
                            <div class="preset-radio-indicator">
                                <div class="preset-radio-dot"></div>
                            </div>
                            <div class="preset-title-wrap">
                                <div class="preset-name">Kompak &amp; Ringan</div>
                                <div class="preset-desc">Rentang Presisi 6.0 – 8.0 MB · Ukuran berkas ekstra hemat, teks &amp; tabel tetap jelas.</div>
                            </div>
                        </div>
                        <div class="preset-size-num">8.0 MB</div>
                    </div>

                    <!-- 12.0 MB (RECOMMENDED) -->
                    <div class="preset-card active" id="presetRec" onclick="selectCompressionPreset(12.0, 'recommended')">
                        <div class="preset-card-left">
                            <div class="preset-radio-indicator">
                                <div class="preset-radio-dot"></div>
                            </div>
                            <div class="preset-title-wrap">
                                <div class="preset-name">
                                    Standar Rekomendasi
                                    <span class="preset-badge-rec">Pilihan Utama</span>
                                </div>
                                <div class="preset-desc">Rentang Presisi 9.0 – 12.0 MB · Keseimbangan optimal, sangat jernih &amp; barcode tajam.</div>
                            </div>
                        </div>
                        <div class="preset-size-num">12.0 MB</div>
                    </div>

                    <!-- 16.0 MB -->
                    <div class="preset-card" id="presetHigh" onclick="selectCompressionPreset(16.0, 'high')">
                        <div class="preset-card-left">
                            <div class="preset-radio-indicator">
                                <div class="preset-radio-dot"></div>
                            </div>
                            <div class="preset-title-wrap">
                                <div class="preset-name">
                                    Ketajaman Maksimal
                                    <span class="preset-badge-rec" style="background: rgba(16, 185, 129, 0.15); color: var(--accent-green); border-color: rgba(16, 185, 129, 0.3);">Kualitas Arsip</span>
                                </div>
                                <div class="preset-desc">Rentang Presisi 12.0 – 16.0 MB · Kualitas tinggi mendekati scan asli tanpa over-kompresi.</div>
                            </div>
                        </div>
                        <div class="preset-size-num">16.0 MB</div>
                    </div>
                </div>

                <!-- OPTIONAL SLIDER -->
                <div class="slider-accordion-toggle" onclick="toggleCustomSlider()">
                    <span><i class="fa-solid fa-sliders" style="margin-right: 6px;"></i> Atur Ukuran Kustom (5.0 – 18.0 MB)</span>
                    <i class="fa-solid fa-chevron-down" id="customSliderChevron"></i>
                </div>

                <div class="slider-drawer" id="customSliderDrawer">
                    <input type="range" class="modern-range-input" id="targetSizeSlider"
                           min="5.0" max="18.0" step="0.5" value="12.0"
                           oninput="handleSliderChange(this.value)">
                    <div class="range-ticks-text">
                        <span>5.0 MB</span>
                        <span>8.0 MB</span>
                        <span>12.0 MB</span>
                        <span>16.0 MB</span>
                        <span>18.0 MB</span>
                    </div>
                </div>

                <div class="mode-hint-banner">
                    <i class="fa-solid fa-circle-info"></i>
                    <span id="qualityHintText">Target 12.0 MB: Standar rekomendasi, teks dokumen &amp; tabel super jelas, barcode 100% terbaca.</span>
                </div>

                <div class="master-action-row">
                    <button type="button" class="btn-compress-master" id="compressBtn" onclick="startBatchCompress()">
                        <i class="fa-solid fa-bolt"></i>
                        <span id="compressBtnText">Kompres File Terfilter (&ge; <span class="threshold-label-val">34</span> MB)</span>
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
            <div class="spec-title">Filter Ambang Batas Fleksibel</div>
            <p class="spec-desc">
                Folder ukuran berapa pun bebas dimasukkan (2 GB+). Hanya berkas &ge; batas filter (misal &ge; 34 MB) yang dimunculkan &amp; dikompresi. Berkas di bawahnya otomatis dilewati.
            </p>
            <div class="spec-tag-sub">FILTER BEBAS KAPASITAS</div>
        </div>

        <div class="spec-card">
            <div class="spec-icon-box amber">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="spec-title">3 Kualitas Jernih &amp; Terbaca</div>
            <p class="spec-desc">
                Preset 8 MB, 12 MB, dan 16 MB terkalibrasi khusus agar tulisan kecil, tabel medis, tanda tangan, dan barcode selalu tajam dan terbaca jelas.
            </p>
            <div class="spec-tag-sub">8 / 12 / 16 MB TERKALIBRASI</div>
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
            Berkas Aman di Bawah Filter!
        </h2>
        <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5;" id="modalDesc">
            Semua file PDF sudah berukuran aman (&lt; <span class="threshold-label-val">34</span> MB) sehingga tidak memerlukan kompresi dan siap digunakan.
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
let rawScannedPdfFiles = [];
let activeExecutableQueue = [];
let activeTheme = localStorage.getItem('presspdf_theme') || 'light';
let isCompressing = false;
let currentSourceInfo = { type: 'files', name: '' };

// Skala Ambang Batas Dinamis (Bebas diatur, Default: 34 MB sesuai instruksi)
let currentThresholdMb = parseFloat(localStorage.getItem('presspdf_threshold_mb')) || 34;
let currentThresholdBytes = currentThresholdMb * 1024 * 1024;
let currentTargetMb = 12.0;

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

const THRESHOLD_PRESETS = [5, 10, 19, 25, 34, 50, 75, 100];

function sliderStepToMb(stepVal) {
    const val = Math.max(0, Math.min(700, parseFloat(stepVal) || 0));
    if (val >= 700) return 100;
    if (val <= 0) return 5;

    const intervalIdx = Math.min(THRESHOLD_PRESETS.length - 2, Math.floor(val / 100));
    const subPct = (val % 100) / 100;

    const startMb = THRESHOLD_PRESETS[intervalIdx];
    const endMb = THRESHOLD_PRESETS[intervalIdx + 1];
    const calcMb = startMb + subPct * (endMb - startMb);
    return Math.round(calcMb);
}

function mbToSliderStep(mb) {
    const num = parseFloat(mb) || 34;
    if (num <= THRESHOLD_PRESETS[0]) return 0;
    if (num >= THRESHOLD_PRESETS[THRESHOLD_PRESETS.length - 1]) return 700;

    for (let i = 0; i < THRESHOLD_PRESETS.length - 1; i++) {
        if (num >= THRESHOLD_PRESETS[i] && num <= THRESHOLD_PRESETS[i + 1]) {
            const fraction = (num - THRESHOLD_PRESETS[i]) / (THRESHOLD_PRESETS[i + 1] - THRESHOLD_PRESETS[i]);
            return Math.round((i + fraction) * 100);
        }
    }
    return 400; // default (34 MB)
}

function handleThresholdSliderStep(val) {
    const mb = sliderStepToMb(val);
    setThresholdVal(mb, false);
}

function handleThresholdDirectInput(val) {
    if (val === '' || isNaN(val)) return;
    const num = Math.min(500, Math.max(1, parseFloat(val)));
    setThresholdVal(num, true);
}

function handleThresholdBlur(input) {
    if (!input.value || isNaN(input.value)) {
        input.value = currentThresholdMb;
    } else {
        const num = Math.min(500, Math.max(1, parseFloat(input.value)));
        setThresholdVal(num, true);
    }
}

function setThresholdVal(val, updateSlider = true) {
    const num = Math.min(500, Math.max(1, parseFloat(val) || 34));
    currentThresholdMb = num;
    currentThresholdBytes = num * 1024 * 1024;
    localStorage.setItem('presspdf_threshold_mb', num);

    if (updateSlider) {
        const slider = document.getElementById('thresholdSlider');
        if (slider) {
            slider.value = mbToSliderStep(num);
        }
    }

    const numberInput = document.getElementById('thresholdNumberInput');
    if (numberInput && parseFloat(numberInput.value) !== num) {
        numberInput.value = num;
    }

    const bigNum = document.getElementById('thresholdBigNum');
    if (bigNum) bigNum.textContent = num;

    const badgeVal = document.getElementById('thresholdBadgeVal');
    if (badgeVal) badgeVal.innerHTML = `&ge; ${num} MB`;

    document.querySelectorAll('.threshold-label-val').forEach(el => {
        el.textContent = num;
    });

    // Update active highlight pada ticks preset
    document.querySelectorAll('.threshold-ticks span').forEach(span => {
        const spanVal = parseInt(span.getAttribute('data-val'), 10);
        if (spanVal === num) {
            span.classList.add('preset-active');
        } else {
            span.classList.remove('preset-active');
        }
    });

    recalculateQueueThreshold();
}

function getFilteredQueue() {
    return rawScannedPdfFiles.filter(item => item.size >= currentThresholdBytes);
}

function recalculateQueueThreshold() {
    activeExecutableQueue = getFilteredQueue();

    if (rawScannedPdfFiles.length > 0) {
        updateInsightsStrip();
        renderFilesQueue();
    }
    resetCompressBtn();
}

// Initial set threshold from saved preference or default (34 MB)
setThresholdVal(currentThresholdMb);

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

        // Ukuran BEBAS: Tidak ada batasan 1 GB, mendukung folder 2 GB, 5 GB, dst.
        const relPath = file.customRelativePath || file.webkitRelativePath || file.name;
        const exists = rawScannedPdfFiles.some(item => item.name === file.name && item.size === file.size && item.relativePath === relPath);
        if (!exists) {
            rawScannedPdfFiles.push({
                id: 'f_' + Math.random().toString(36).substr(2, 9),
                file: file,
                name: file.name,
                relativePath: relPath,
                size: file.size,
                status: 'pending',
                statusText: `Perlu Kompres (≥ ${currentThresholdMb} MB)`,
                result: null
            });
        }
    });

    if (nonPdfCount > 0 && isFolder) {
        showToast(`Folder dipindai: ${nonPdfCount} file non-PDF otomatis dilewati.`);
    }

    if (rawScannedPdfFiles.length === 0) {
        showToast('Tidak ada file PDF yang valid di dalam folder/file yang dipilih.');
        return;
    }

    document.getElementById('dropzoneView').style.display = 'none';
    document.getElementById('workspaceView').classList.add('show');
    document.getElementById('resultSection').classList.remove('show');

    const currentVal = document.getElementById('targetSizeSlider').value;
    handleSliderChange(currentVal);

    recalculateQueueThreshold();

    if (activeExecutableQueue.length === 0 && rawScannedPdfFiles.length > 0) {
        showSmallPopup(
            currentSourceInfo.name || 'Folder Dokumen',
            `Semua ${rawScannedPdfFiles.length} file PDF berukuran < ${currentThresholdMb} MB. Berkas Anda sudah aman di bawah filter!`
        );
    }
}

function updateInsightsStrip() {
    const totalPdfs = rawScannedPdfFiles.length;
    let totalFolderBytes = 0;
    rawScannedPdfFiles.forEach(f => { totalFolderBytes += f.size; });

    const executableCount = activeExecutableQueue.length;
    const skippedCount = totalPdfs - executableCount;

    document.getElementById('metricTotalCount').textContent = totalPdfs;
    document.getElementById('metricNeedsCount').textContent = executableCount;
    document.getElementById('metricSafeCount').textContent = skippedCount;

    document.getElementById('insightsFolderName').textContent = currentSourceInfo.name || `${totalPdfs} Dokumen Terdeteksi`;
    document.getElementById('insightsSourceDesc').textContent = currentSourceInfo.type === 'folder'
        ? `Total ${totalPdfs} berkas (${formatBytes(totalFolderBytes)}) dipindai dari folder`
        : `Total ${totalPdfs} berkas PDF (${formatBytes(totalFolderBytes)})`;

    const tabCount = document.getElementById('tabCountNeeds');
    if (tabCount) tabCount.textContent = executableCount;

    const noticeEl = document.getElementById('filterNoticeText');
    if (noticeEl) {
        if (executableCount > 0) {
            noticeEl.textContent = `Menampilkan hanya ${executableCount} berkas berukuran ≥ ${currentThresholdMb} MB untuk dieksekusi. Sebanyak ${skippedCount} berkas lainnya (< ${currentThresholdMb} MB) otomatis dilewati dan tidak ditampilkan.`;
        } else {
            noticeEl.textContent = `Semua ${totalPdfs} berkas berukuran di bawah ${currentThresholdMb} MB sehingga tidak ada berkas yang dimunculkan untuk kompresi.`;
        }
    }
}

function renderFilesQueue() {
    const container = document.getElementById('filesQueueList');
    container.innerHTML = '';

    const list = activeExecutableQueue;

    if (list.length === 0) {
        const emptyDiv = document.createElement('div');
        emptyDiv.className = 'empty-queue-notice';
        emptyDiv.innerHTML = `
            <i class="fa-solid fa-circle-check" style="font-size: 32px; color: var(--accent-green); margin-bottom: 8px;"></i>
            <div style="font-weight: 700; font-size: 14px; color: var(--text-primary);">
                Tidak Ada Berkas Berukuran &ge; <span class="threshold-label-val">${currentThresholdMb}</span> MB
            </div>
            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                Semua ${rawScannedPdfFiles.length} berkas di dalam folder ini berukuran aman di bawah filter, sehingga tidak ditampilkan.
            </div>
            <div style="font-size: 11.5px; color: var(--primary); margin-top: 8px;">
                💡 Geser slider atau turunkan ambang batas filter di atas jika ingin memproses file dengan ukuran lebih kecil.
            </div>
        `;
        container.appendChild(emptyDiv);
        return;
    }

    list.forEach(item => {
        const row = document.createElement('div');
        row.className = 'file-item-card needs-comp';
        row.id = `queueItem_${item.id}`;

        let statusClass = 'status-pill-chip needs-comp';
        let statusText = item.statusText || `Perlu Kompres (≥ ${currentThresholdMb} MB)`;

        if (item.status === 'done') {
            statusClass = 'status-pill-chip success';
        } else if (item.status === 'compressing') {
            statusClass = 'status-pill-chip compressing';
            statusText = 'Mengompresi...';
        } else if (item.status === 'error') {
            statusClass = 'status-pill-chip error';
            statusText = 'Gagal';
        }

        const pathHint = (item.relativePath && item.relativePath !== item.name)
            ? `<div class="file-path-text"><i class="fa-regular fa-folder" style="color:#d97706; margin-right:4px;"></i>${escapeHtml(item.relativePath)}</div>`
            : '';

        row.innerHTML = `
            <div class="file-main-info">
                <div class="file-doc-icon">
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
                <button type="button" class="btn-file-del" onclick="removeFileFromQueue('${item.id}')" title="Keluarkan dari antrean eksekusi">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    });

    resetCompressBtn();
}

function removeFileFromQueue(id) {
    rawScannedPdfFiles = rawScannedPdfFiles.filter(f => f.id !== id);
    if (rawScannedPdfFiles.length === 0) {
        clearSelectedFiles();
        return;
    }
    recalculateQueueThreshold();
}

function clearSelectedFiles() {
    rawScannedPdfFiles = [];
    activeExecutableQueue = [];
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
    currentTargetMb = parseFloat(val);
    document.getElementById('targetSizeSlider').value = currentTargetMb.toFixed(1);
    updatePresetCardsUI(modeName);
    updateTargetLabelUI(currentTargetMb);
    updateQualityHintUI(modeName, currentTargetMb);
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
    const minVal = (val * 0.75).toFixed(1);

    if (modeName === 'low' || val <= 8.5) {
        hintEl.textContent = `Target ${val.toFixed(1)} MB: Hasil terkalibrasi presisi pada rentang ${minVal} – ${val.toFixed(1)} MB. Kompak & ringan, sangat hemat penyimpanan.`;
    } else if (modeName === 'high' || val >= 14.5) {
        hintEl.textContent = `Target ${val.toFixed(1)} MB: Hasil terkalibrasi presisi pada rentang ${minVal} – ${val.toFixed(1)} MB. Resolusi tajam kualitas scan asli tanpa over-kompresi.`;
    } else {
        hintEl.textContent = `Target ${val.toFixed(1)} MB: Hasil terkalibrasi presisi pada rentang ${minVal} – ${val.toFixed(1)} MB. Rekomendasi optimal dokumen klaim rumah sakit.`;
    }
}

function handleSliderChange(val) {
    const num = parseFloat(val);
    currentTargetMb = num;
    updateTargetLabelUI(num);

    let mode = 'custom';
    if (Math.abs(num - 8.0) < 0.25) mode = 'low';
    else if (Math.abs(num - 12.0) < 0.25) mode = 'recommended';
    else if (Math.abs(num - 16.0) < 0.25) mode = 'high';

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
    activeExecutableQueue = getFilteredQueue();
    const totalToProcess = activeExecutableQueue.length;

    if (totalToProcess === 0 || isCompressing) {
        showSmallPopup(
            currentSourceInfo.name || 'Dokumen Pilihan',
            `Tidak ada file berukuran ≥ ${currentThresholdMb} MB yang dapat dieksekusi.`
        );
        return;
    }

    isCompressing = true;
    const targetSize = document.getElementById('targetSizeSlider').value;
    const btn = document.getElementById('compressBtn');
    btn.disabled = true;
    btn.innerHTML = '<div class="mini-spinner"></div><span>Memproses Kompresi Berkas...</span>';

    const progBox = document.getElementById('progressBox');
    progBox.classList.add('show');

    const batchToken = generateBatchToken();
    const results = [];

    for (let i = 0; i < totalToProcess; i++) {
        const item = activeExecutableQueue[i];
        item.status = 'compressing';
        const statusTag = document.getElementById(`statusTag_${item.id}`);

        const overallPct = Math.round((i / totalToProcess) * 100);
        setProgress(overallPct);
        document.getElementById('progressPctText').textContent = `${overallPct}%`;

        if (statusTag) {
            statusTag.className = 'status-pill-chip compressing';
            statusTag.textContent = 'Mengompresi...';
        }
        document.getElementById('progressStageText').textContent = `Mengompresi (${i + 1}/${totalToProcess}): ${item.name} (≥ ${currentThresholdMb} MB)`;

        const form = new FormData();
        form.append('file', item.file);
        form.append('target_size', targetSize);
        form.append('threshold_mb', currentThresholdMb.toString());
        form.append('batch_token', batchToken);
        form.append('relative_path', item.relativePath || item.name);
        form.append('is_safe', '0');
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

            item.status = 'done';
            item.statusText = `Selesai (${data.compressed_human})`;
            if (statusTag) {
                statusTag.className = 'status-pill-chip success';
                statusTag.textContent = `Selesai (${data.compressed_human})`;
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
    document.getElementById('progressStageText').textContent = `Selesai memproses ${results.length} berkas terfilter!`;

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
        const count = activeExecutableQueue.length;
        if (count > 0) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-bolt"></i><span>Kompres ${count} File Terfilter (&ge; <span class="threshold-label-val">${currentThresholdMb}</span> MB)</span>`;
        } else {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-check"></i><span>Tidak Ada Berkas (&ge; <span class="threshold-label-val">${currentThresholdMb}</span> MB)</span>`;
        }
    }
    const progBox = document.getElementById('progressBox');
    if (progBox && !isCompressing) progBox.classList.remove('show');
    if (!isCompressing) setProgress(0);
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
                Hasil Kompresi Berkas Terfilter Selesai &bull; Nama File Utuh 100%
            </div>
            <div>
                Hanya ${resultsList.length} berkas yang memenuhi filter (&ge; ${currentThresholdMb} MB) yang diproses &amp; dikompresi. Nama file PDF tetap 100% sama persis sesuai aslinya (kode klaim). Berkas lainnya di folder komputer Anda tetap aman dan utuh.
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

            const card = document.createElement('div');
            card.className = 'folder-result-card';
            card.innerHTML = `
                <div class="folder-result-head">
                    <div class="folder-result-title">
                        <i class="fa-solid fa-folder-open" style="color: #d97706; font-size: 16px;"></i>
                        <span>${escapeHtml(group.display)}</span>
                        <span class="folder-count-badge">(${group.files.length} Berkas Terkompresi)</span>
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
            : `<span class="status-pill-chip safe-skip" style="font-size:10.5px;">🟢 Berkas Aman (&lt; ${currentThresholdMb} MB)</span>`);

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
</script>
</body>
</html>
