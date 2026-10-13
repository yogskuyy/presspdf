<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PressPDF — Studio kompresi dokumen PDF bergaya skeuomorphism dengan tombol fisik taktil, mesin Ghostscript native, dan panduan lengkap.">
    <title>PressPDF — Skeuomorphic Document Studio & Compressor</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700;800&family=JetBrains+Mono:wght@500;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    /* ============================================================
       SKEUOMORPHIC DESIGN SYSTEM
       (Machined Aluminum, Physical Bevels, Realistic Insets & LEDs)
       ============================================================ */
    :root {
        --font-heading: 'Space Grotesk', system-ui, sans-serif;
        --font-mono: 'JetBrains Mono', monospace;
        --font-body: 'Plus Jakarta Sans', system-ui, sans-serif;

        /* Skeuomorphic Machined Aluminum & Warm Console Palette */
        --skeuo-desk: #dce3eb;
        --skeuo-metal-base: #e3e8ee;
        --skeuo-metal-light: #f7f9fb;
        --skeuo-metal-dark: #cdd5df;
        --skeuo-panel-rim: #b0bccb;

        --skeuo-inset-bg: #d0d7e2;
        --skeuo-inset-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.16), inset -2px -2px 5px rgba(255, 255, 255, 0.85);

        --skeuo-plate-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.14), 0 4px 10px -2px rgba(15, 23, 42, 0.08);
        --skeuo-button-raised: linear-gradient(180deg, #ffffff 0%, #e2e8f0 100%);
        --skeuo-button-shadow: 0 4px 8px rgba(15, 23, 42, 0.16), 0 1px 2px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.9);

        --skeuo-ink: #1e293b;
        --skeuo-ink-muted: #475569;
        --skeuo-ink-dim: #64748b;

        /* Physical Status LEDs & Accents */
        --led-green: #10b981;
        --led-green-glow: 0 0 8px #10b981, 0 0 14px rgba(16, 185, 129, 0.6);
        --led-amber: #f59e0b;
        --led-amber-glow: 0 0 8px #f59e0b, 0 0 14px rgba(245, 158, 11, 0.6);
        --led-red: #ef4444;
        --led-red-glow: 0 0 8px #ef4444, 0 0 14px rgba(239, 68, 68, 0.6);
        --led-blue: #0284c7;

        --primary-red: #d92d20;
        --primary-red-gradient: linear-gradient(180deg, #ef4444 0%, #b91c1c 100%);
        --primary-red-shadow: 0 4px 12px rgba(220, 38, 38, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.35);

        --radius-panel: 16px;
        --radius-button: 10px;
    }

    [data-theme="dark-titanium"] {
        --skeuo-desk: #121417;
        --skeuo-metal-base: #22252a;
        --skeuo-metal-light: #2b2f36;
        --skeuo-metal-dark: #191b1f;
        --skeuo-panel-rim: #383c44;

        --skeuo-inset-bg: #141619;
        --skeuo-inset-shadow: inset 2px 2px 6px rgba(0, 0, 0, 0.7), inset -1px -1px 3px rgba(255, 255, 255, 0.06);

        --skeuo-plate-shadow: 0 16px 36px rgba(0, 0, 0, 0.6), 0 4px 12px rgba(0, 0, 0, 0.4);
        --skeuo-button-raised: linear-gradient(180deg, #353940 0%, #23262b 100%);
        --skeuo-button-shadow: 0 4px 10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.12);

        --skeuo-ink: #f8fafc;
        --skeuo-ink-muted: #cbd5e1;
        --skeuo-ink-dim: #94a3b8;
    }

    [data-theme="dark-titanium"] body {
        background-color: var(--skeuo-desk);
        background-image:
            radial-gradient(rgba(255, 255, 255, 0.07) 1.2px, transparent 1.2px),
            linear-gradient(180deg, rgba(0, 0, 0, 0.4) 0%, transparent 100%);
    }

    [data-theme="dark-titanium"] .skeuo-header-plate,
    [data-theme="dark-titanium"] .skeuo-chassis-box,
    [data-theme="dark-titanium"] .system-guide-panel,
    [data-theme="dark-titanium"] .results-chassis-box {
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: var(--skeuo-plate-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    [data-theme="dark-titanium"] .chassis-header-trim {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.04) 0%, rgba(0, 0, 0, 0.25) 100%);
        border-bottom-color: #383c44;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
    }

    [data-theme="dark-titanium"] .brand-title-text {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.8);
    }

    [data-theme="dark-titanium"] .analog-preset-switch {
        background: linear-gradient(180deg, #2a2d33 0%, #1f2125 100%);
        border-color: #3f444d;
        border-top-color: rgba(255, 255, 255, 0.12);
    }

    [data-theme="dark-titanium"] .analog-preset-switch:hover {
        background: linear-gradient(180deg, #32363e 0%, #25282e 100%);
    }

    [data-theme="dark-titanium"] .analog-preset-switch.active {
        background: linear-gradient(180deg, #1c1e22 0%, #16181b 100%);
        border-color: var(--primary-red);
        box-shadow: inset 1px 2px 5px rgba(0, 0, 0, 0.6), 0 0 0 1px var(--primary-red);
    }

    [data-theme="dark-titanium"] .dropzone-view {
        background: #141619;
        border-color: #2e333a;
    }

    [data-theme="dark-titanium"] .dropzone-view::after {
        border-color: rgba(148, 163, 184, 0.25);
    }

    [data-theme="dark-titanium"] .physical-doc-seal {
        background: linear-gradient(180deg, #2e3238 0%, #1e2025 100%);
        border-color: #3f444d;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    [data-theme="dark-titanium"] .doc-seal-tag {
        background: #dc2626;
        color: #ffffff;
    }

    [data-theme="dark-titanium"] .physical-doc-seal i {
        color: #f1f5f9 !important;
    }

    [data-theme="dark-titanium"] .spec-tag-pill {
        background: #1e2126;
        border-color: #383c44;
        color: #cbd5e1;
    }

    [data-theme="dark-titanium"] .skeuo-hud-btn {
        background: linear-gradient(180deg, #2e3238 0%, #202327 100%);
        border-color: #3f444d;
        color: #f1f5f9;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.4);
    }

    [data-theme="dark-titanium"] .skeuo-hud-hint {
        background: #181a1d;
        border-color: #33373f;
        color: #94a3b8;
    }

    [data-theme="dark-titanium"] .btn-card-del {
        background: #2a2d33;
        border-color: #3f444d;
        color: #94a3b8;
    }

    [data-theme="dark-titanium"] .btn-card-del:hover {
        background: #7f1d1d;
        color: #fecaca;
    }

    [data-theme="dark-titanium"] .file-physical-card {
        background: linear-gradient(180deg, #272a2f 0%, #1e2024 100%);
        border-color: #383c44;
        border-top-color: rgba(255, 255, 255, 0.1);
    }

    [data-theme="dark-titanium"] .file-physical-card.needs-comp {
        border-left-color: #ef4444;
    }

    [data-theme="dark-titanium"] .file-physical-card.safe-skip {
        border-left-color: #10b981;
    }

    [data-theme="dark-titanium"] .tab-filter-btn {
        background: #1e2126;
        border-color: #383d46;
        color: #94a3b8;
    }

    [data-theme="dark-titanium"] .tab-filter-btn.active {
        background: #2b2f36;
        color: #f8fafc;
        border-color: #4b525d;
    }

    [data-theme="dark-titanium"] .btn-add-more-skeuo {
        background: #1e2126;
        border-color: #383d46;
        color: #cbd5e1;
    }

    [data-theme="dark-titanium"] .specs-widget-rack {
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: var(--skeuo-plate-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    [data-theme="dark-titanium"] .spec-widget-card {
        background: linear-gradient(180deg, #272a2f 0%, #1e2024 100%);
        border-color: #383c44;
        border-top-color: rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    [data-theme="dark-titanium"] .spec-widget-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border-color: #4b525d;
    }

    [data-theme="dark-titanium"] .widget-icon-capsule.red {
        background: #450a0a; color: #fca5a5; border-color: #7f1d1d;
    }
    [data-theme="dark-titanium"] .widget-icon-capsule.amber {
        background: #451a03; color: #fcd34d; border-color: #78350f;
    }
    [data-theme="dark-titanium"] .widget-icon-capsule.blue {
        background: #082f49; color: #7dd3fc; border-color: #075985;
    }
    [data-theme="dark-titanium"] .widget-icon-capsule.green {
        background: #052e16; color: #86efac; border-color: #14532d;
    }

    [data-theme="dark-titanium"] .telem-val.highlight-red { color: #fca5a5; }
    [data-theme="dark-titanium"] .telem-val.highlight-amber { color: #fcd34d; }
    [data-theme="dark-titanium"] .telem-val.highlight-blue { color: #7dd3fc; }
    [data-theme="dark-titanium"] .telem-val.highlight-green { color: #86efac; }

    [data-theme="dark-titanium"] .result-row-card {
        background: linear-gradient(180deg, #272a2f 0%, #1e2024 100%);
        border-color: #383c44;
    }

    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: var(--font-body);
        background-color: var(--skeuo-desk);
        color: var(--skeuo-ink);
        min-height: 100vh;
        overflow-x: hidden;
        position: relative;
        transition: background-color 0.3s ease, color 0.3s ease;
        -webkit-font-smoothing: antialiased;
        background-image:
            radial-gradient(rgba(0, 0, 0, 0.06) 1.2px, transparent 1.2px),
            linear-gradient(180deg, rgba(255, 255, 255, 0.25) 0%, transparent 100%);
        background-size: 24px 24px, 100% 100%;
    }

    /* ============================================================
       PARALLAX CANVAS BACKGROUND
       ============================================================ */
    .parallax-canvas-layer {
        position: fixed;
        inset: 0;
        z-index: 0;
        pointer-events: all;
        overflow: hidden;
    }

    #bgInteractiveCanvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: block;
        cursor: crosshair;
    }

    /* Interactive HUD at bottom left */
    .skeuo-hud-bar {
        position: fixed;
        bottom: 18px;
        left: 20px;
        z-index: 20;
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--skeuo-metal-base);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        border-radius: 999px;
        padding: 5px 12px;
    }

    .skeuo-hud-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: var(--skeuo-button-raised);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 999px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        color: var(--skeuo-ink);
        box-shadow: var(--skeuo-button-shadow);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .skeuo-hud-btn:active {
        box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.25);
        transform: translateY(1px);
    }

    .skeuo-hud-hint {
        font-size: 11px;
        font-family: var(--font-mono);
        color: var(--skeuo-ink-dim);
        display: flex;
        align-items: center;
        gap: 5px;
        padding-left: 4px;
    }

    @media (max-width: 800px) {
        .skeuo-hud-bar { display: none; }
    }

    /* ============================================================
       APPLICATION CONSOLE VIEWPORT (MAX-WIDTH 1340PX)
       ============================================================ */
    .app-viewport {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 1340px;
        margin: 0 auto;
        padding: 16px 20px 96px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        pointer-events: none;
        perspective: 1400px;
    }

    .app-viewport > * {
        pointer-events: auto;
    }

    /* ============================================================
       TOP HEADER: MACHINED ALUMINUM FACEPLATE
       ============================================================ */
    .skeuo-header-plate {
        background: linear-gradient(180deg, var(--skeuo-metal-light) 0%, var(--skeuo-metal-base) 60%, var(--skeuo-metal-dark) 100%);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: var(--radius-panel);
        box-shadow: var(--skeuo-plate-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.85);
        padding: 14px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        position: relative;
    }

    /* Metallic Screws on Plate Corners */
    .plate-screw {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: radial-gradient(circle, #cbd5e1 30%, #64748b 90%);
        box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.8), 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    .plate-screw::after {
        content: '';
        position: absolute;
        top: 3.5px;
        left: 1px;
        width: 6px;
        height: 1px;
        background: #475569;
    }
    .screw-tl { top: 6px; left: 8px; }
    .screw-tr { top: 6px; right: 8px; }
    .screw-bl { bottom: 6px; left: 8px; }
    .screw-br { bottom: 6px; right: 8px; }

    .brand-group {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .brand-embossed-seal {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--primary-red-gradient);
        box-shadow: 0 4px 10px rgba(185, 28, 28, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.4);
        border: 1px solid #991b1b;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
    }

    .brand-title-wrap {
        display: flex;
        flex-direction: column;
    }

    .brand-title-text {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 19px;
        color: var(--skeuo-ink);
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
        letter-spacing: -0.4px;
        line-height: 1.15;
    }

    .brand-sub-badge {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        color: var(--skeuo-ink-dim);
    }

    /* Live Telemetry Inset Screen */
    .telemetry-inset-screen {
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 8px;
        box-shadow: var(--skeuo-inset-shadow);
        padding: 6px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-family: var(--font-mono);
        font-size: 11.5px;
        font-weight: 700;
    }

    .led-indicator-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--led-green);
        box-shadow: var(--led-green-glow);
        display: inline-block;
        flex-shrink: 0;
    }

    .skeuo-btn-tactile {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: var(--skeuo-button-raised);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: var(--radius-button);
        box-shadow: var(--skeuo-button-shadow);
        font-family: var(--font-mono);
        font-size: 12px;
        font-weight: 700;
        color: var(--skeuo-ink);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .skeuo-btn-tactile:active {
        box-shadow: inset 1px 2px 4px rgba(0, 0, 0, 0.2);
        transform: translateY(1px);
    }

    /* ============================================================
       WORKFLOW STEPPER (4 TAHAPAN PROSES)
       ============================================================ */
    .workflow-bezel-bar {
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 12px;
        box-shadow: var(--skeuo-inset-shadow);
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .workflow-step-node {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: var(--font-mono);
        font-size: 11.5px;
        font-weight: 700;
        color: var(--skeuo-ink-muted);
    }

    .workflow-step-node.active {
        color: var(--skeuo-ink);
    }

    .step-led-num {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: var(--skeuo-metal-base);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
        color: var(--skeuo-ink);
    }

    .workflow-step-node.active .step-led-num {
        background: var(--primary-red);
        color: #ffffff;
        box-shadow: 0 0 8px rgba(220, 38, 38, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.4);
        border-color: #991b1b;
    }

    .workflow-step-arrow {
        color: var(--skeuo-ink-dim);
        font-size: 10px;
    }

    /* ============================================================
       TWO-COLUMN CONSOLE HARDWARE LAYOUT
       ============================================================ */
    .console-module-grid {
        display: grid;
        grid-template-columns: 1.22fr 0.88fr;
        gap: 20px;
        align-items: start;
        width: 100%;
    }

    @media (max-width: 1024px) {
        .console-module-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }
    }

    .parallax-tilt-shell {
        transform-style: preserve-3d;
        will-change: transform;
        transition: transform 0.12s cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    /* Skeuomorphic Chassis Card */
    .skeuo-chassis-box {
        background: linear-gradient(180deg, var(--skeuo-metal-light) 0%, var(--skeuo-metal-base) 45%, var(--skeuo-metal-dark) 100%);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: var(--radius-panel);
        box-shadow: var(--skeuo-plate-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.9);
        position: relative;
        overflow: hidden;
    }

    /* Chassis Header Trim */
    .chassis-header-trim {
        padding: 9px 18px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.4) 0%, rgba(0, 0, 0, 0.04) 100%);
        border-bottom: 1px solid var(--skeuo-panel-rim);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        color: var(--skeuo-ink-muted);
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .chassis-title {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--skeuo-ink);
    }

    /* ============================================================
       PANEL 01: DROPZONE & QUEUE (LEFT MODULE)
       ============================================================ */
    .dropzone-view {
        margin: 16px;
        padding: 34px 24px;
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 12px;
        box-shadow: var(--skeuo-inset-shadow);
        text-align: center;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 440px;
        transition: background 0.2s ease, border-color 0.2s ease;
        position: relative;
    }

    .dropzone-view::after {
        content: '';
        position: absolute;
        inset: 10px;
        border: 2px dashed rgba(71, 85, 105, 0.35);
        border-radius: 8px;
        pointer-events: none;
        transition: all 0.2s ease;
    }

    .dropzone-view:hover::after,
    .dropzone-view.drag-over::after {
        border-color: var(--primary-red);
        border-style: solid;
        background: rgba(220, 38, 38, 0.04);
    }

    /* Physical 3D Document Stamp Graphic */
    .physical-doc-seal {
        width: 70px;
        height: 84px;
        background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.12), inset 0 1px 0 #ffffff;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .physical-doc-seal::before {
        content: '';
        position: absolute;
        top: -1px;
        right: -1px;
        width: 18px;
        height: 18px;
        background: #e2e8f0;
        border-left: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        box-shadow: -1px 1px 2px rgba(0, 0, 0, 0.08);
    }

    .doc-seal-tag {
        padding: 2px 6px;
        background: var(--primary-red);
        color: #ffffff;
        font-family: var(--font-mono);
        font-weight: 800;
        font-size: 10.5px;
        border-radius: 3px;
        margin-bottom: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .dropzone-view:hover .physical-doc-seal {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.16);
    }

    .dz-headline-main {
        font-family: var(--font-heading);
        font-size: 19px;
        font-weight: 800;
        color: var(--skeuo-ink);
        margin-bottom: 6px;
        letter-spacing: -0.3px;
    }

    .dz-subline-main {
        font-size: 13px;
        color: var(--skeuo-ink-muted);
        max-width: 440px;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    /* Tactile Buttons */
    .dz-tactile-buttons {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }

    .btn-skeuo-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 22px;
        background: var(--primary-red-gradient);
        color: #ffffff;
        border: 1px solid #991b1b;
        border-top: 1px solid #fca5a5;
        border-radius: var(--radius-button);
        font-family: var(--font-heading);
        font-size: 14px;
        font-weight: 700;
        box-shadow: var(--primary-red-shadow);
        cursor: pointer;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
        transition: all 0.15s ease;
    }

    .btn-skeuo-primary:hover {
        filter: brightness(1.05);
        transform: translateY(-1px);
    }

    .btn-skeuo-primary:active {
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.4);
        transform: translateY(1px);
    }

    .btn-skeuo-metal {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        background: var(--skeuo-button-raised);
        color: var(--skeuo-ink);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: var(--radius-button);
        font-family: var(--font-heading);
        font-size: 14px;
        font-weight: 700;
        box-shadow: var(--skeuo-button-shadow);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-skeuo-metal:active {
        box-shadow: inset 1px 2px 4px rgba(0, 0, 0, 0.2);
        transform: translateY(1px);
    }

    .dz-specs-ribbon {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .spec-tag-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: var(--skeuo-metal-base);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 999px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        color: var(--skeuo-ink-muted);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
    }

    #fileInput, #folderInput { display: none; }

    /* Workspace Queue View */
    .workspace-view {
        display: none;
        padding: 16px;
        min-height: 440px;
    }

    .workspace-view.show { display: block; }

    .insights-lcd-strip {
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 10px;
        box-shadow: var(--skeuo-inset-shadow);
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .lcd-source-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .lcd-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: var(--skeuo-metal-base);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: var(--skeuo-button-shadow);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-red);
        font-size: 14px;
    }

    .lcd-metrics-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .lcd-metric-badge {
        padding: 3px 8px;
        background: var(--skeuo-metal-base);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 6px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
    }

    .lcd-metric-badge.needs-comp {
        color: var(--primary-red);
        background: #fee2e2;
        border-color: #fca5a5;
    }

    .lcd-metric-badge.safe-skip {
        color: var(--led-green);
        background: #dcfce7;
        border-color: #86efac;
    }

    /* Queue Filter Tabs */
    .queue-tabs-bezel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tab-pills {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .tab-filter-btn {
        padding: 5px 11px;
        border-radius: 6px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        background: var(--skeuo-button-raised);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: var(--skeuo-button-shadow);
        color: var(--skeuo-ink-muted);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .tab-filter-btn.active {
        background: var(--skeuo-inset-bg);
        box-shadow: inset 1px 1px 3px rgba(0, 0, 0, 0.25);
        color: var(--skeuo-ink);
        border-color: #94a3b8;
    }

    .btn-add-more-skeuo {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: var(--skeuo-button-raised);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 6px;
        box-shadow: var(--skeuo-button-shadow);
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        color: var(--skeuo-ink);
        cursor: pointer;
    }

    /* Queue Scroll List */
    .files-recessed-tray {
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 10px;
        box-shadow: var(--skeuo-inset-shadow);
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 330px;
        overflow-y: auto;
    }

    .file-physical-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06), inset 0 1px 0 #ffffff;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .file-physical-card.needs-comp {
        border-left: 4px solid var(--primary-red);
    }

    .file-physical-card.safe-skip {
        border-left: 4px solid var(--led-green);
    }

    .file-card-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }

    .file-card-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        background: var(--skeuo-inset-bg);
        border: 1px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-red);
        font-size: 13px;
        flex-shrink: 0;
    }

    .file-card-info { min-width: 0; flex: 1; }

    .file-card-name {
        font-family: var(--font-heading);
        font-size: 12.5px;
        font-weight: 700;
        color: var(--skeuo-ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }

    .file-card-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-family: var(--font-mono);
        color: var(--skeuo-ink-dim);
        flex-wrap: wrap;
    }

    .status-pill-chip {
        font-size: 9.5px;
        font-weight: 700;
        padding: 1px 5px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
    }

    .status-pill-chip.needs-comp { background: #fee2e2; color: #b91c1c; border-color: #fca5a5; }
    .status-pill-chip.safe-skip { background: #dcfce7; color: #15803d; border-color: #86efac; }
    .status-pill-chip.compressing { background: #fef3c7; color: #b45309; border-color: #fde68a; }
    .status-pill-chip.success { background: #dcfce7; color: #15803d; }
    .status-pill-chip.error { background: #fee2e2; color: #b91c1c; }

    .btn-card-del {
        background: var(--skeuo-metal-base);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        border-radius: 6px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--skeuo-ink-dim);
        cursor: pointer;
    }

    .btn-card-del:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* ============================================================
       PANEL 02: CONTROL DECK & SYSTEM GUIDE (RIGHT MODULE)
       ============================================================ */
    .control-deck-module {
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .deck-sub-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .deck-target-lcd {
        padding: 3px 10px;
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 6px;
        box-shadow: var(--skeuo-inset-shadow);
        font-family: var(--font-mono);
        font-size: 14px;
        font-weight: 800;
        color: var(--primary-red);
    }

    /* 3 Tactile Switches */
    .analog-presets-trio {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .analog-preset-switch {
        background: var(--skeuo-button-raised);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: 10px;
        box-shadow: var(--skeuo-button-shadow);
        padding: 10px 8px;
        cursor: pointer;
        text-align: left;
        position: relative;
        transition: all 0.15s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .analog-preset-switch:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
    }

    .analog-preset-switch.active {
        background: var(--skeuo-inset-bg);
        border-color: #94a3b8;
        box-shadow: inset 1px 2px 5px rgba(0, 0, 0, 0.22);
        transform: translateY(1px);
    }

    .switch-led-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #94a3b8;
        margin-bottom: 5px;
    }

    .analog-preset-switch.active .switch-led-dot {
        background: var(--led-green);
        box-shadow: var(--led-green-glow);
    }

    .switch-title-label {
        font-family: var(--font-heading);
        font-size: 12.5px;
        font-weight: 800;
        color: var(--skeuo-ink);
        margin-bottom: 2px;
    }

    .switch-target-num {
        font-family: var(--font-mono);
        font-size: 15px;
        font-weight: 800;
        color: var(--primary-red);
        margin-bottom: 2px;
    }

    .switch-desc-text {
        font-size: 10px;
        color: var(--skeuo-ink-dim);
        line-height: 1.3;
    }

    /* Slider Drawer */
    .slider-bezel-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 7px 12px;
        background: var(--skeuo-metal-base);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 6px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        cursor: pointer;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        color: var(--skeuo-ink);
    }

    .slider-recessed-drawer {
        display: none;
        padding: 10px;
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 8px;
        box-shadow: var(--skeuo-inset-shadow);
        margin-top: 8px;
    }

    .slider-recessed-drawer.show { display: block; }

    .skeuo-range-input {
        -webkit-appearance: none;
        appearance: none;
        width: 100%;
        height: 8px;
        border-radius: 999px;
        background: #cbd5e1;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.25);
        outline: none;
        cursor: pointer;
    }

    .skeuo-range-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: linear-gradient(180deg, #ffffff 0%, #cbd5e1 100%);
        border: 1px solid #94a3b8;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.25), inset 0 1px 0 #ffffff;
        cursor: pointer;
        transition: transform 0.1s ease;
    }

    .skeuo-range-input::-webkit-slider-thumb:hover { transform: scale(1.15); }

    .range-ticks-lcd {
        display: flex;
        justify-content: space-between;
        margin-top: 4px;
        font-size: 10px;
        font-family: var(--font-mono);
        color: var(--skeuo-ink-dim);
    }

    .mode-hint-inset {
        padding: 8px 12px;
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 6px;
        box-shadow: var(--skeuo-inset-shadow);
        font-size: 11.5px;
        color: var(--skeuo-ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Physical Master CTA Button */
    .master-action-row {
        display: flex;
        gap: 8px;
    }

    .btn-compress-master {
        flex: 1;
        padding: 13px 18px;
        background: var(--primary-red-gradient);
        color: #ffffff;
        border: 1px solid #991b1b;
        border-top: 1px solid #fca5a5;
        border-radius: var(--radius-button);
        box-shadow: 0 5px 12px rgba(185, 28, 28, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.4);
        font-family: var(--font-heading);
        font-size: 14.5px;
        font-weight: 800;
        cursor: pointer;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.15s ease;
    }

    .btn-compress-master:hover:not(:disabled) {
        filter: brightness(1.06);
        transform: translateY(-1px);
    }

    .btn-compress-master:active:not(:disabled) {
        box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.5);
        transform: translateY(1px);
    }

    .btn-compress-master:disabled {
        opacity: 0.55;
        cursor: not-allowed;
    }

    .btn-cancel-tactile {
        padding: 13px 16px;
        background: var(--skeuo-button-raised);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: var(--radius-button);
        box-shadow: var(--skeuo-button-shadow);
        font-family: var(--font-mono);
        font-size: 12.5px;
        font-weight: 700;
        color: var(--skeuo-ink);
        cursor: pointer;
    }

    .btn-cancel-tactile:active {
        box-shadow: inset 1px 2px 4px rgba(0, 0, 0, 0.2);
        transform: translateY(1px);
    }

    /* Progress Inset Bar */
    .progress-inset-bay {
        display: none;
        padding: 10px 14px;
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 8px;
        box-shadow: var(--skeuo-inset-shadow);
    }

    .progress-inset-bay.show { display: block; }

    .progress-hud-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
    }

    .progress-track-groove {
        height: 10px;
        background: #cbd5e1;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3);
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill-led {
        height: 100%;
        width: 0%;
        background: var(--primary-red-gradient);
        box-shadow: 0 0 6px rgba(220, 38, 38, 0.6);
        border-radius: 999px;
        transition: width 0.3s ease;
    }

    /* ============================================================
       SPECS & RULES HORIZONTAL WIDGET RACK (BERJEJER KE SAMPING)
       ============================================================ */
    .specs-widget-rack {
        width: 100%;
        margin-top: 4px;
    }

    .rack-spec-limit {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .telemetry-chip-tag {
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: var(--skeuo-inset-shadow);
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 5px;
        color: var(--primary-red);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .specs-widget-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        padding: 16px;
    }

    @media (max-width: 1120px) {
        .specs-widget-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .specs-widget-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Individual Physical Widget Card */
    .spec-widget-card {
        background: linear-gradient(180deg, #ffffff 0%, #f6f8fb 100%);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08), inset 0 1px 0 #ffffff;
        padding: 16px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 12px;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        position: relative;
    }

    .spec-widget-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12), inset 0 1px 0 #ffffff;
        border-color: rgba(220, 38, 38, 0.35);
    }

    .widget-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .widget-icon-capsule {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        box-shadow: inset 1px 1px 2px rgba(0, 0, 0, 0.08), 0 2px 4px rgba(0, 0, 0, 0.05);
        flex-shrink: 0;
    }

    .widget-icon-capsule.red {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
    }
    .widget-icon-capsule.amber {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .widget-icon-capsule.blue {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .widget-icon-capsule.green {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .widget-tag-pill {
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: var(--skeuo-inset-shadow);
        padding: 3px 8px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .widget-tag-text {
        font-family: var(--font-mono);
        font-size: 9.5px;
        font-weight: 800;
        color: var(--skeuo-ink-dim);
        letter-spacing: 0.3px;
    }

    .widget-led-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .widget-led-dot.red { background: var(--led-red); box-shadow: var(--led-red-glow); }
    .widget-led-dot.amber { background: var(--led-amber); box-shadow: var(--led-amber-glow); }
    .widget-led-dot.blue { background: var(--led-blue); box-shadow: 0 0 6px #0284c7; }
    .widget-led-dot.green { background: var(--led-green); box-shadow: var(--led-green-glow); }

    .widget-card-body {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .widget-card-title {
        font-family: var(--font-heading);
        font-size: 13.5px;
        font-weight: 800;
        color: var(--skeuo-ink);
        line-height: 1.25;
        letter-spacing: -0.2px;
    }

    .widget-card-desc {
        font-size: 11.5px;
        line-height: 1.5;
        color: var(--skeuo-ink-muted);
    }

    .widget-card-desc strong {
        color: var(--skeuo-ink);
    }

    .widget-telemetry-strip {
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        box-shadow: var(--skeuo-inset-shadow);
        padding: 4px 8px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-family: var(--font-mono);
        font-size: 9.5px;
        font-weight: 700;
    }

    .telem-label {
        color: var(--skeuo-ink-dim);
        text-transform: uppercase;
        font-size: 9px;
    }

    .telem-val {
        font-weight: 800;
    }

    .telem-val.highlight-red { color: #b91c1c; }
    .telem-val.highlight-amber { color: #b45309; }
    .telem-val.highlight-blue { color: #0369a1; }
    .telem-val.highlight-green { color: #15803d; }

    /* ============================================================
       RESULTS SECTION (FULL WIDTH PHYSICAL CONSOLE)
       ============================================================ */
    .result-section {
        display: none;
        width: 100%;
        animation: fadeIn 0.25s ease-out;
    }

    .result-section.show { display: block; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .results-chassis-box {
        background: linear-gradient(180deg, var(--skeuo-metal-light) 0%, var(--skeuo-metal-base) 100%);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: var(--radius-panel);
        box-shadow: var(--skeuo-plate-shadow);
        overflow: hidden;
    }

    .results-banner-strip {
        padding: 14px 20px;
        background: linear-gradient(180deg, #dcfce7 0%, #bbf7d0 100%);
        border-bottom: 1px solid #86efac;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .seal-check-badge {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: linear-gradient(180deg, #22c55e 0%, #15803d 100%);
        border: 1px solid #166534;
        box-shadow: 0 3px 8px rgba(21, 128, 61, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.4);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .results-header-text h3 {
        font-family: var(--font-heading);
        font-size: 17px;
        font-weight: 800;
        color: #14532d;
        margin-bottom: 2px;
    }

    .results-header-text p {
        font-size: 12.5px;
        color: #166534;
    }

    /* Comparative Meters */
    .results-meters-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        border-bottom: 1px solid var(--skeuo-panel-rim);
        background: var(--skeuo-metal-base);
    }

    .meter-stat-cell {
        padding: 12px;
        text-align: center;
        border-right: 1px solid var(--skeuo-panel-rim);
    }

    .meter-stat-cell:last-child { border-right: none; }

    .meter-stat-label {
        font-size: 10px;
        font-weight: 700;
        font-family: var(--font-mono);
        text-transform: uppercase;
        color: var(--skeuo-ink-dim);
        margin-bottom: 4px;
    }

    .meter-stat-val {
        font-family: var(--font-mono);
        font-size: 17px;
        font-weight: 800;
        color: var(--skeuo-ink);
    }

    .meter-stat-val.highlight { color: #15803d; }

    /* Results file list */
    .results-file-queue {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--skeuo-panel-rim);
        max-height: 250px;
        overflow-y: auto;
        background: var(--skeuo-inset-bg);
        box-shadow: var(--skeuo-inset-shadow);
    }

    .result-row-card {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        padding: 8px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .btn-dl-individual {
        padding: 4px 10px;
        background: var(--skeuo-button-raised);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 6px;
        box-shadow: var(--skeuo-button-shadow);
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 700;
        color: var(--skeuo-ink);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .results-cta-deck {
        padding: 14px 18px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-dl-all-zip {
        flex: 1;
        min-width: 200px;
        padding: 11px 18px;
        background: linear-gradient(180deg, #22c55e 0%, #15803d 100%);
        color: #ffffff;
        border: 1px solid #166534;
        border-top: 1px solid #86efac;
        border-radius: var(--radius-button);
        font-family: var(--font-heading);
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(21, 128, 61, 0.35);
    }

    /* Modal */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1000;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-backdrop.show { display: flex; }

    .skeuo-modal-chassis {
        background: linear-gradient(180deg, var(--skeuo-metal-light) 0%, var(--skeuo-metal-base) 100%);
        border: 1px solid var(--skeuo-panel-rim);
        border-top: 1px solid #ffffff;
        border-radius: var(--radius-panel);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        max-width: 420px;
        width: 100%;
        text-align: center;
        overflow: hidden;
    }

    .modal-inner-padding { padding: 24px 20px 18px; }

    .modal-badge-seal {
        width: 46px;
        height: 46px;
        margin: 0 auto 12px;
        background: linear-gradient(180deg, #fbbf24 0%, #d97706 100%);
        border: 1px solid #b45309;
        box-shadow: 0 4px 10px rgba(217, 119, 6, 0.35);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ffffff;
    }

    .modal-file-recessed {
        margin: 12px 0;
        padding: 8px 12px;
        background: var(--skeuo-inset-bg);
        border: 1px solid var(--skeuo-panel-rim);
        border-radius: 6px;
        box-shadow: var(--skeuo-inset-shadow);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-family: var(--font-mono);
        font-size: 11.5px;
    }

    /* Toast */
    .toast-tray {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        display: flex;
        flex-direction: column;
        gap: 8px;
        pointer-events: none;
    }

    .toast-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);
        border: 1px solid var(--skeuo-panel-rim);
        border-left: 4px solid var(--primary-red);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        color: var(--skeuo-ink);
        pointer-events: all;
    }

    /* Micro Spinner */
    .mini-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Footer */
    .app-footer {
        text-align: center;
        font-family: var(--font-mono);
        font-size: 11px;
        color: var(--skeuo-ink-dim);
        padding-top: 2px;
    }
    </style>
</head>
<body>

<!-- INTERACTIVE BACKGROUND CANVAS -->
<div class="parallax-canvas-layer" aria-hidden="true">
    <canvas id="bgInteractiveCanvas"></canvas>
</div>


<!-- MAIN WORKBENCH VIEWPORT (WIDE DUAL CONSOLE) -->
<div class="app-viewport">

    <!-- TOP HEADER: MACHINED ALUMINUM FACEPLATE -->
    <header class="skeuo-header-plate">
        <span class="plate-screw screw-tl"></span>
        <span class="plate-screw screw-tr"></span>
        <span class="plate-screw screw-bl"></span>
        <span class="plate-screw screw-br"></span>

        <a href="/" class="brand-group" title="PressPDF Beranda">
            <div class="brand-embossed-seal">
                <i class="fa-solid fa-file-pdf"></i>
            </div>
            <div class="brand-title-wrap">
                <span class="brand-title-text">PressPDF Studio</span>
                <span class="brand-sub-badge">SMART GHOSTSCRIPT ENGINE v2.4</span>
            </div>
        </a>

        <!-- System Telemetry Status -->
        <div class="telemetry-inset-screen">
            <span class="led-indicator-dot"></span>
            <span>SYSTEM: ONLINE // ENGINE: GS_NATIVE // FILTER: &gt;10MB</span>
        </div>

        <!-- Mode Visual Toggle -->
        <button type="button" class="skeuo-btn-tactile" id="themeSwitchBtn" onclick="toggleThemeMode()" title="Ganti mode visual">
            <i class="fa-solid fa-circle-half-stroke"></i>
            <span id="themeSwitchText">Mode Titanium</span>
        </button>
    </header>

    <!-- ALUR KERJA / WORKFLOW STEPPER BAR (4 TAHAPAN PROSES RESMI) -->
    <div class="workflow-bezel-bar" role="list" aria-label="Alur kerja kompresi PDF">
        <div class="workflow-step-node active">
            <span class="step-led-num">1</span>
            <span>Upload File / Folder PDF</span>
        </div>
        <i class="fa-solid fa-chevron-right workflow-step-arrow"></i>

        <div class="workflow-step-node active">
            <span class="step-led-num">2</span>
            <span>Saring Otomatis (&gt; 10 MB)</span>
        </div>
        <i class="fa-solid fa-chevron-right workflow-step-arrow"></i>

        <div class="workflow-step-node active">
            <span class="step-led-num">3</span>
            <span>Tentukan Target Presisi (MB)</span>
        </div>
        <i class="fa-solid fa-chevron-right workflow-step-arrow"></i>

        <div class="workflow-step-node active">
            <span class="step-led-num">4</span>
            <span>Unduh Arsip .ZIP / Satuan</span>
        </div>
    </div>

    <!-- MAIN CONSOLE MODULE GRID (TWO-COLUMN SKEUOMORPHIC CONSOLE) -->
    <div class="console-module-grid">

        <!-- LEFT MODULE: INPUT & STAGING CHASSIS -->
        <div class="parallax-tilt-shell" id="tiltLeftPanel">
            <div class="skeuo-chassis-box">
                <span class="plate-screw screw-tl"></span>
                <span class="plate-screw screw-tr"></span>
                <span class="plate-screw screw-bl"></span>
                <span class="plate-screw screw-br"></span>

                <div class="chassis-header-trim">
                    <span class="chassis-title"><i class="fa-solid fa-hard-drive"></i> MODUL 01 // AREA UNGGAH &amp; ANTREAN FILE</span>
                    <span>BAY_01 // READY</span>
                </div>

                <!-- STATE 1: Dropzone View -->
                <div class="dropzone-view" id="dropzoneView"
                     role="button" tabindex="0"
                     aria-label="Area unggah file atau folder PDF"
                     onclick="triggerFileInput()"
                     onkeydown="if(event.key==='Enter'||event.key===' ')this.click()"
                     ondragover="onDragOver(event)"
                     ondragleave="onDragLeave(event)"
                     ondrop="onDrop(event)">

                    <div class="physical-doc-seal">
                        <span class="doc-seal-tag">PDF</span>
                        <i class="fa-solid fa-arrow-down-long" style="font-size: 20px; color: var(--skeuo-ink);"></i>
                    </div>

                    <h2 class="dz-headline-main">Tarik &amp; Letakkan File atau Folder PDF di Sini</h2>
                    <p class="dz-subline-main">
                        Sistem otomatis memindai seluruh file PDF dan menyortir dokumen berukuran <strong>&gt; 10 MB</strong> untuk dikompresi.
                    </p>

                    <div class="dz-tactile-buttons">
                        <button class="btn-skeuo-primary" type="button" onclick="event.stopPropagation(); triggerFolderInput()">
                            <i class="fa-solid fa-folder-open"></i>
                            Pilih Folder Berisi PDF
                        </button>
                        <button class="btn-skeuo-metal" type="button" onclick="event.stopPropagation(); triggerFileInput()">
                            <i class="fa-solid fa-file-pdf"></i>
                            Pilih File PDF Satuan
                        </button>
                    </div>

                    <div class="dz-specs-ribbon">
                        <span class="spec-tag-pill"><i class="fa-solid fa-check" style="color: var(--led-green);"></i> Input 1 Folder Penuh</span>
                        <span class="spec-tag-pill"><i class="fa-solid fa-filter" style="color: var(--primary-red);"></i> Otomatis Deteksi &gt; 10 MB</span>
                        <span class="spec-tag-pill"><i class="fa-solid fa-file-zipper" style="color: var(--led-blue);"></i> Unduh .ZIP Sekaligus</span>
                    </div>
                </div>

                <!-- Hidden Native File Inputs -->
                <input type="file" id="fileInput" accept="application/pdf" multiple onchange="onFilesChosen(this.files, 'files')">
                <input type="file" id="folderInput" webkitdirectory directory multiple onchange="onFolderChosen(this.files)">

                <!-- STATE 2: Active Workspace Queue -->
                <div class="workspace-view" id="workspaceView">
                    <!-- LCD Status Strip -->
                    <div class="insights-lcd-strip">
                        <div class="lcd-source-info">
                            <div class="lcd-icon-box">
                                <i class="fa-solid fa-folder-tree" id="insightsSourceIcon"></i>
                            </div>
                            <div>
                                <div style="font-family: var(--font-heading); font-size: 13.5px; font-weight: 800;" id="insightsFolderName">Dokumen Terdeteksi</div>
                                <div style="font-size: 11px; font-family: var(--font-mono); color: var(--skeuo-ink-dim);" id="insightsSourceDesc">Pemindaian file PDF selesai</div>
                            </div>
                        </div>
                        <div class="lcd-metrics-group">
                            <div class="lcd-metric-badge" title="Total file PDF yang ditemukan">
                                <span id="metricTotalCount">0</span> PDF
                            </div>
                            <div class="lcd-metric-badge needs-comp" title="File > 10 MB yang akan dikompresi">
                                🔴 <span id="metricNeedsCount">0</span> Perlu Kompres
                            </div>
                            <div class="lcd-metric-badge safe-skip" title="File <= 10 MB yang dilewati">
                                🟢 <span id="metricSafeCount">0</span> Aman
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="queue-tabs-bezel">
                        <div class="tab-pills">
                            <button type="button" class="tab-filter-btn active" id="tabFilterAll" onclick="setQueueFilter('all')">
                                Semua (<span id="tabCountAll">0</span>)
                            </button>
                            <button type="button" class="tab-filter-btn" id="tabFilterNeeds" onclick="setQueueFilter('needs_compress')">
                                🔴 Perlu Kompres (<span id="tabCountNeeds">0</span>)
                            </button>
                            <button type="button" class="tab-filter-btn" id="tabFilterSafe" onclick="setQueueFilter('safe')">
                                🟢 Aman (<span id="tabCountSafe">0</span>)
                            </button>
                        </div>
                        <div style="display:flex; gap:6px;">
                            <button type="button" class="btn-add-more-skeuo" onclick="triggerFolderInput()" title="Tambah folder lain">
                                <i class="fa-solid fa-folder-plus"></i> + Folder
                            </button>
                            <button type="button" class="btn-add-more-skeuo" onclick="triggerFileInput()" title="Tambah file satuan">
                                <i class="fa-solid fa-file-circle-plus"></i> + File
                            </button>
                        </div>
                    </div>

                    <!-- Queue List Recessed Tray -->
                    <div class="files-recessed-tray" id="filesQueueList">
                        <!-- Dinamis terisi -->
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT MODULE: CONTROL DECK & INFORMATION MANUAL -->
        <div class="parallax-tilt-shell" id="tiltRightPanel">
            <!-- Parameter Control Chassis -->
            <div class="skeuo-chassis-box">
                <span class="plate-screw screw-tl"></span>
                <span class="plate-screw screw-tr"></span>
                <span class="plate-screw screw-bl"></span>
                <span class="plate-screw screw-br"></span>

                <div class="chassis-header-trim">
                    <span class="chassis-title"><i class="fa-solid fa-sliders"></i> MODUL 02 // KONTROL TARGET &amp; EKSEKUSI</span>
                    <span>PRECISION_RACK</span>
                </div>

                <div class="control-deck-module">
                    <div class="deck-sub-header">
                        <span style="font-family: var(--font-heading); font-size: 14px; font-weight: 800;">Target Batas Ukuran Maksimal</span>
                        <span class="deck-target-lcd" id="targetSizeLabel">5.5 MB</span>
                    </div>

                    <!-- 3 Tactile Switches -->
                    <div class="analog-presets-trio">
                        <div class="analog-preset-switch" id="presetLow" onclick="selectCompressionPreset(3.0, 'low')">
                            <div>
                                <div class="switch-led-dot"></div>
                                <div class="switch-title-label">Ekonomis</div>
                                <div class="switch-target-num">3.0 MB</div>
                            </div>
                            <div class="switch-desc-text">72 DPI · Hemat kuota untuk portal web limit ketat.</div>
                        </div>

                        <div class="analog-preset-switch active" id="presetRec" onclick="selectCompressionPreset(5.5, 'recommended')">
                            <div>
                                <div class="switch-led-dot"></div>
                                <div class="switch-title-label">Standar Ideal</div>
                                <div class="switch-target-num">5.5 MB</div>
                            </div>
                            <div class="switch-desc-text">150 DPI · Keseimbangan optimal kejernihan teks.</div>
                        </div>

                        <div class="analog-preset-switch" id="presetHigh" onclick="selectCompressionPreset(9.8, 'extreme')">
                            <div>
                                <div class="switch-led-dot"></div>
                                <div class="switch-title-label">Presisi Tinggi</div>
                                <div class="switch-target-num">9.8 MB</div>
                            </div>
                            <div class="switch-desc-text">300 DPI · Kualitas tajam mendekati dokumen aslinya.</div>
                        </div>
                    </div>

                    <!-- Manual Slider Drawer Accordion -->
                    <div class="slider-bezel-toggle" onclick="toggleCustomSlider()">
                        <span><i class="fa-solid fa-sliders" style="margin-right: 6px;"></i> Pengaturan Skala Manual (1.0 – 9.8 MB)</span>
                        <i class="fa-solid fa-chevron-down" id="customSliderChevron"></i>
                    </div>

                    <div class="slider-recessed-drawer" id="customSliderDrawer">
                        <input type="range" class="skeuo-range-input" id="targetSizeSlider"
                               min="1.0" max="9.8" step="0.5" value="5.5"
                               oninput="handleSliderChange(this.value)">
                        <div class="range-ticks-lcd">
                            <span>1.0 MB</span>
                            <span>3.0 MB (Ekonomis)</span>
                            <span>5.5 MB (Standar)</span>
                            <span>8.0 MB</span>
                            <span>9.8 MB (Presisi)</span>
                        </div>
                    </div>

                    <div class="mode-hint-inset">
                        <i class="fa-solid fa-circle-info" style="color: var(--primary-red);"></i>
                        <span style="font-size: 11.5px;">Status: <strong id="qualityHintText" style="color: var(--primary-red);">Standar Ideal (Maksimal 5.5 MB — Seimbang &amp; Jernih)</strong></span>
                    </div>

                    <!-- Master Action CTA -->
                    <div class="master-action-row">
                        <button type="button" class="btn-compress-master" id="compressBtn" onclick="startBatchCompress()">
                            <i class="fa-solid fa-bolt"></i>
                            <span id="compressBtnText">Kompres File Terdeteksi</span>
                        </button>
                        <button type="button" class="btn-cancel-tactile" onclick="clearSelectedFiles()">
                            Batal
                        </button>
                    </div>

                    <!-- Progress Bar Inset Bay -->
                    <div class="progress-inset-bay" id="progressBox">
                        <div class="progress-hud-line">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div class="mini-spinner" style="border-top-color: var(--primary-red); border-color: rgba(0,0,0,0.2);"></div>
                                <span id="progressStageText">Memproses kompresi dokumen...</span>
                            </div>
                            <span id="progressPctText" style="font-weight: 800; color: var(--primary-red);">0%</span>
                        </div>
                        <div class="progress-track-groove">
                            <div class="progress-fill-led" id="progressFill"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- WIDGET RACK: KETENTUAN & SPESIFIKASI SISTEM RESMI (4 WIDGET BERJEJER KE SAMPING) -->
    <div class="skeuo-chassis-box specs-widget-rack">
        <span class="plate-screw screw-tl"></span>
        <span class="plate-screw screw-tr"></span>
        <span class="plate-screw screw-bl"></span>
        <span class="plate-screw screw-br"></span>

        <div class="chassis-header-trim">
            <span class="chassis-title"><i class="fa-solid fa-microchip"></i> MODUL SPESIFIKASI // KETENTUAN SISTEM RESMI</span>
            <div class="rack-spec-limit">
                <span class="telemetry-chip-tag"><i class="fa-solid fa-hard-drive"></i> MAKS. 500 MB / BERKAS</span>
            </div>
        </div>

        <div class="specs-widget-grid">
            <!-- WIDGET 1: FILTER AMBANG BATAS 10 MB -->
            <div class="spec-widget-card accent-red">
                <div class="widget-card-head">
                    <div class="widget-icon-capsule red">
                        <i class="fa-solid fa-filter"></i>
                    </div>
                    <div class="widget-tag-pill">
                        <span class="widget-led-dot red"></span>
                        <span class="widget-tag-text">FILTER OTOMATIS</span>
                    </div>
                </div>
                <div class="widget-card-body">
                    <h3 class="widget-card-title">Ambang Batas 10 MB</h3>
                    <p class="widget-card-desc">
                        Dokumen PDF berukuran <strong>&le; 10 MB</strong> tidak perlu dikompresi dan otomatis dilewati demi menjaga keaslian mutu. Hanya dokumen <strong>&gt; 10 MB</strong> yang dioptimasi.
                    </p>
                </div>
                <div class="widget-telemetry-strip">
                    <span class="telem-label">KRITERIA</span>
                    <span class="telem-val highlight-red">&gt; 10 MB DIPROSES</span>
                </div>
            </div>

            <!-- WIDGET 2: REKURSIF FOLDER & SUB-FOLDER -->
            <div class="spec-widget-card accent-amber">
                <div class="widget-card-head">
                    <div class="widget-icon-capsule amber">
                        <i class="fa-solid fa-folder-tree"></i>
                    </div>
                    <div class="widget-tag-pill">
                        <span class="widget-led-dot amber"></span>
                        <span class="widget-tag-text">REKURSIF PENUH</span>
                    </div>
                </div>
                <div class="widget-card-body">
                    <h3 class="widget-card-title">Folder &amp; Sub-Folder</h3>
                    <p class="widget-card-desc">
                        Dapat memilih <strong>1 folder dokumen utuh</strong>. Sistem otomatis memindai seluruh sub-folder di dalamnya secara mendalam untuk mencari berkas PDF tanpa perlu sortir manual satu per satu.
                    </p>
                </div>
                <div class="widget-telemetry-strip">
                    <span class="telem-label">PEMINDAIAN</span>
                    <span class="telem-val highlight-amber">REKURSIF OTOMATIS</span>
                </div>
            </div>

            <!-- WIDGET 3: ENGINE GHOSTSCRIPT & FPDI -->
            <div class="spec-widget-card accent-blue">
                <div class="widget-card-head">
                    <div class="widget-icon-capsule blue">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <div class="widget-tag-pill">
                        <span class="widget-led-dot blue"></span>
                        <span class="widget-tag-text">NATIVE ENGINE</span>
                    </div>
                </div>
                <div class="widget-card-body">
                    <h3 class="widget-card-title">Ghostscript &amp; FPDI</h3>
                    <p class="widget-card-desc">
                        Proses didukung engine <strong>Ghostscript native</strong> dengan downsampling resolusi gambar terkalibrasi dan kompresi font presisi tinggi tanpa antrean server cloud pihak ketiga.
                    </p>
                </div>
                <div class="widget-telemetry-strip">
                    <span class="telem-label">CORE ENGINE</span>
                    <span class="telem-val highlight-blue">GS v2.4 + FPDI</span>
                </div>
            </div>

            <!-- WIDGET 4: KEAMANAN & PRIVASI SANDBOX -->
            <div class="spec-widget-card accent-green">
                <div class="widget-card-head">
                    <div class="widget-icon-capsule green">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="widget-tag-pill">
                        <span class="widget-led-dot green"></span>
                        <span class="widget-tag-text">100% PRIVAT</span>
                    </div>
                </div>
                <div class="widget-card-body">
                    <h3 class="widget-card-title">Privasi &amp; Sandbox Aman</h3>
                    <p class="widget-card-desc">
                        Dokumen diproses di lingkungan <strong>sandbox lokal yang aman</strong>. Berkas sementara otomatis dihapus secara permanen dari server setelah proses kompresi selesai.
                    </p>
                </div>
                <div class="widget-telemetry-strip">
                    <span class="telem-label">KEAMANAN</span>
                    <span class="telem-val highlight-green">AUTO-WIPE PERMANEN</span>
                </div>
            </div>
        </div>
    </div>

    <!-- STATE 3: RESULTS SECTION (FULL-WIDTH SKEUOMORPHIC TERMINAL) -->
    <section class="result-section" id="resultSection">
        <div class="results-chassis-box">
            <span class="plate-screw screw-tl"></span>
            <span class="plate-screw screw-tr"></span>
            <span class="plate-screw screw-bl"></span>
            <span class="plate-screw screw-br"></span>

            <div class="results-banner-strip">
                <div class="seal-check-badge">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div class="results-header-text">
                    <h3 id="resultHeading">Kompresi Selesai!</h3>
                    <p id="resultFileName">Dokumen Anda telah berhasil dioptimasi dengan presisi</p>
                </div>
            </div>

            <div class="results-meters-row">
                <div class="meter-stat-cell">
                    <div class="meter-stat-label">Total Ukuran Awal</div>
                    <div class="meter-stat-val" id="statOriginal">—</div>
                </div>
                <div class="meter-stat-cell">
                    <div class="meter-stat-label">Total Ukuran Baru</div>
                    <div class="meter-stat-val" id="statCompressed">—</div>
                </div>
                <div class="meter-stat-cell">
                    <div class="meter-stat-label">Total Penghematan</div>
                    <div class="meter-stat-val highlight" id="statSavings">—</div>
                </div>
            </div>

            <!-- List per-file results -->
            <div class="results-file-queue" id="batchResultList">
                <!-- Dinamis terisi hasil tiap file -->
            </div>

            <div class="results-cta-deck">
                <a class="btn-dl-all-zip" id="btnDownloadZipLink" href="#" target="_blank" style="display: none;">
                    <i class="fa-solid fa-file-zipper"></i>
                    Unduh Semua Dokumen (.ZIP)
                </a>
                <a class="btn-dl-all-zip" id="btnDownloadSingleLink" href="#" target="_blank">
                    <i class="fa-solid fa-download"></i>
                    Unduh Dokumen PDF Terkompresi
                </a>
                <button type="button" class="btn-skeuo-metal" onclick="resetForm()">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                    Kompres Dokumen Baru
                </button>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="app-footer">
        PressPDF &copy; {{ date('Y') }} &mdash; SKEUOMORPHIC PRECISION PDF STUDIO // GHOSTSCRIPT NATIVE ENGINE // 100% PRIVATE
    </footer>

</div>

<!-- SKEUOMORPHIC MODAL (< 10 MB NOTIFICATION) -->
<div class="modal-backdrop" id="modalSmall" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="skeuo-modal-chassis">
        <span class="plate-screw screw-tl"></span>
        <span class="plate-screw screw-tr"></span>
        <span class="plate-screw screw-bl"></span>
        <span class="plate-screw screw-br"></span>

        <div class="modal-inner-padding">
            <div class="modal-badge-seal">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 style="font-family: var(--font-heading); font-size: 18px; font-weight: 800; color: var(--skeuo-ink);" id="modalTitle">
                File Sudah Di Bawah 10 MB!
            </h2>
            <p style="font-size: 13px; line-height: 1.5; color: var(--skeuo-ink-muted); margin-top: 6px;">
                File PDF ini sudah berukuran ringan sehingga <strong>tidak memerlukan kompresi</strong> lebih lanjut.
            </p>
            <div class="modal-file-recessed">
                <span id="modalFileName" style="font-weight: 700; color: var(--skeuo-ink);">—</span>
                <span id="modalFileSize" style="color: var(--primary-red); font-weight: 800;">—</span>
            </div>
        </div>
        <div style="padding: 0 20px 20px;">
            <button class="btn-skeuo-primary" style="width: 100%; justify-content: center;" id="modalOkBtn" onclick="closeModal()">
                Oke, Saya Mengerti
            </button>
        </div>
    </div>
</div>

<!-- TOAST TRAY -->
<div class="toast-tray" id="toastBox"></div>

<!-- ====================================================
     INTERACTIVE CANVAS & PARALLAX SCRIPT
     ==================================================== -->
<script>
(function initSkeuoParallaxCanvas() {
    const canvas = document.getElementById('bgInteractiveCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let width = 0;
    let height = 0;
    let dpr = window.devicePixelRatio || 1;

    const mouse = {
        x: window.innerWidth / 2,
        y: window.innerHeight / 2,
        currentX: window.innerWidth / 2,
        currentY: window.innerHeight / 2,
        active: false
    };

    const tiltLeft = document.getElementById('tiltLeftPanel');
    const tiltRight = document.getElementById('tiltRightPanel');

    function resizeCanvas() {
        width = window.innerWidth;
        height = window.innerHeight;
        dpr = window.devicePixelRatio || 1;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.scale(dpr, dpr);
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
        mouse.active = true;
    });

    window.addEventListener('mouseleave', () => {
        mouse.active = false;
        mouse.x = width / 2;
        mouse.y = height / 2;
    });

    window.addEventListener('touchmove', (e) => {
        if (e.touches && e.touches[0]) {
            mouse.x = e.touches[0].clientX;
            mouse.y = e.touches[0].clientY;
            mouse.active = true;
        }
    }, { passive: true });

    // Click Spawns & Ripples
    const ripples = [];
    window.addEventListener('click', (e) => {
        const tag = e.target.tagName.toLowerCase();
        if (tag === 'button' || tag === 'input' || tag === 'a' || e.target.closest('.skeuo-chassis-box') || e.target.closest('.system-guide-panel')) {
            return;
        }
        createRipple(e.clientX, e.clientY);
        spawnPaperPlane(e.clientX, e.clientY, (Math.random() - 0.5) * 6, -3 - Math.random() * 4);
    });

    function createRipple(x, y) {
        ripples.push({
            x: x,
            y: y,
            radius: 5,
            maxRadius: 180 + Math.random() * 80,
            alpha: 0.45,
            speed: 4.5
        });
    }

    const planes = [];
    const docSheets = [];

    class SkeuoPaperPlane {
        constructor(x, y, vx, vy) {
            this.x = x ?? Math.random() * width;
            this.y = y ?? Math.random() * height;
            this.vx = vx ?? (Math.random() * 1.5 + 0.6) * (Math.random() > 0.5 ? 1 : -1);
            this.vy = vy ?? (Math.random() * 1.2 - 0.6);
            this.scale = 0.9 + Math.random() * 0.4;
            this.angle = Math.atan2(this.vy, this.vx);
            this.wobble = Math.random() * Math.PI * 2;
            this.wobbleSpeed = 0.03 + Math.random() * 0.02;
        }

        update() {
            this.wobble += this.wobbleSpeed;
            const flutter = Math.sin(this.wobble) * 0.4;

            if (mouse.active) {
                const dx = this.x - mouse.currentX;
                const dy = this.y - mouse.currentY;
                const dist = Math.hypot(dx, dy);
                const repelRadius = 150;

                if (dist < repelRadius && dist > 1) {
                    const force = (1 - dist / repelRadius) * 2.4;
                    this.vx += (dx / dist) * force;
                    this.vy += (dy / dist) * force;
                }
            }

            const speed = Math.hypot(this.vx, this.vy);
            const maxSpeed = 4.2;
            if (speed > maxSpeed) {
                this.vx = (this.vx / speed) * maxSpeed;
                this.vy = (this.vy / speed) * maxSpeed;
            }
            this.vx *= 0.99;
            this.vy *= 0.99;
            this.vy += Math.cos(this.wobble) * 0.05;

            this.x += this.vx;
            this.y += this.vy;

            const targetAngle = Math.atan2(this.vy, this.vx) + flutter * 0.2;
            this.angle += (targetAngle - this.angle) * 0.1;

            const pad = 60;
            if (this.x < -pad) this.x = width + pad;
            if (this.x > width + pad) this.x = -pad;
            if (this.y < -pad) this.y = height + pad;
            if (this.y > height + pad) this.y = -pad;
        }

        draw(ctx, shiftX, shiftY) {
            ctx.save();
            ctx.translate(this.x + shiftX, this.y + shiftY);
            ctx.rotate(this.angle);
            ctx.scale(this.scale, this.scale);

            // Realistic Soft Ambient Drop Shadow
            ctx.beginPath();
            ctx.moveTo(27, 6);
            ctx.lineTo(-15, -6);
            ctx.lineTo(-5, 6);
            ctx.lineTo(-15, 18);
            ctx.closePath();
            ctx.fillStyle = 'rgba(15, 23, 42, 0.14)';
            ctx.fill();

            // Left wing with bevel highlight
            ctx.beginPath();
            ctx.moveTo(24, 0);
            ctx.lineTo(-18, -12);
            ctx.lineTo(-8, 0);
            ctx.closePath();
            ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
            ctx.fill();
            ctx.strokeStyle = 'rgba(100, 116, 139, 0.45)';
            ctx.lineWidth = 1.2;
            ctx.stroke();

            // Right wing
            ctx.beginPath();
            ctx.moveTo(24, 0);
            ctx.lineTo(-8, 0);
            ctx.lineTo(-18, 12);
            ctx.closePath();
            ctx.fillStyle = 'rgba(241, 245, 249, 0.75)';
            ctx.fill();
            ctx.strokeStyle = 'rgba(100, 116, 139, 0.45)';
            ctx.lineWidth = 1.2;
            ctx.stroke();

            // Keel crease line
            ctx.beginPath();
            ctx.moveTo(24, 0);
            ctx.lineTo(-8, 0);
            ctx.strokeStyle = '#ef4444';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            ctx.restore();
        }
    }

    class SkeuoDocSheet {
        constructor() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * 0.35;
            this.vy = -0.3 - Math.random() * 0.35;
            this.rot = Math.random() * Math.PI * 2;
            this.rotSpeed = (Math.random() - 0.5) * 0.008;
            this.w = 26 + Math.random() * 12;
            this.h = this.w * 1.35;
        }

        update() {
            this.rot += this.rotSpeed;

            if (mouse.active) {
                const dx = this.x - mouse.currentX;
                const dy = this.y - mouse.currentY;
                const dist = Math.hypot(dx, dy);
                if (dist < 130 && dist > 1) {
                    const force = (1 - dist / 130) * 1.3;
                    this.vx += (dx / dist) * force;
                    this.vy += (dy / dist) * force;
                }
            }

            this.vx *= 0.98;
            this.vy *= 0.98;
            if (this.vy > -0.2) this.vy -= 0.01;

            this.x += this.vx;
            this.y += this.vy;

            if (this.y < -50) {
                this.y = height + 50;
                this.x = Math.random() * width;
            }
            if (this.x < -50) this.x = width + 50;
            if (this.x > width + 50) this.x = -50;
        }

        draw(ctx, shiftX, shiftY) {
            ctx.save();
            ctx.translate(this.x + shiftX, this.y + shiftY);
            ctx.rotate(this.rot);

            // Ambient shadow
            ctx.fillStyle = 'rgba(15, 23, 42, 0.08)';
            ctx.fillRect(-this.w / 2 + 3, -this.h / 2 + 3, this.w, this.h);

            // Paper sheet
            ctx.fillStyle = 'rgba(255, 255, 255, 0.85)';
            ctx.strokeStyle = 'rgba(148, 163, 184, 0.4)';
            ctx.lineWidth = 1.2;

            ctx.beginPath();
            ctx.rect(-this.w / 2, -this.h / 2, this.w, this.h);
            ctx.fill();
            ctx.stroke();

            // Fold corner
            ctx.beginPath();
            ctx.moveTo(this.w / 2 - 7, -this.h / 2);
            ctx.lineTo(this.w / 2, -this.h / 2 + 7);
            ctx.fillStyle = '#ef4444';
            ctx.fill();

            ctx.restore();
        }
    }

    const planeCount = Math.max(6, Math.min(10, Math.floor(window.innerWidth / 170)));
    for (let i = 0; i < planeCount; i++) planes.push(new SkeuoPaperPlane());

    const sheetCount = Math.max(6, Math.min(10, Math.floor(window.innerWidth / 160)));
    for (let i = 0; i < sheetCount; i++) docSheets.push(new SkeuoDocSheet());

    function spawnPaperPlane(x, y, vx, vy) {
        planes.push(new SkeuoPaperPlane(x, y, vx, vy));
        if (planes.length > 20) planes.shift();
    }

    // HUD buttons
    document.getElementById('hudBtnLaunch')?.addEventListener('click', (e) => {
        e.stopPropagation();
        spawnPaperPlane(width * 0.1, height * 0.7, 4.5, -2.8);
        createRipple(width * 0.1, height * 0.7);
    });

    document.getElementById('hudBtnBreeze')?.addEventListener('click', (e) => {
        e.stopPropagation();
        planes.forEach(p => {
            p.vx += 3.5 + Math.random() * 2;
            p.vy -= 1.5 + Math.random() * 1.5;
        });
        docSheets.forEach(s => {
            s.vx += 2.0;
            s.vy -= 1.0;
        });
        createRipple(width * 0.5, height * 0.5);
    });

    // 60FPS PARALLAX TICK LOOP
    let isTabVisible = true;
    document.addEventListener('visibilitychange', () => { isTabVisible = !document.hidden; });

    function animateParallax() {
        requestAnimationFrame(animateParallax);
        if (!isTabVisible) return;

        mouse.currentX += (mouse.x - mouse.currentX) * 0.06;
        mouse.currentY += (mouse.y - mouse.currentY) * 0.06;

        const centerX = width / 2;
        const centerY = height / 2;
        const normOffsetX = (mouse.currentX - centerX);
        const normOffsetY = (mouse.currentY - centerY);

        // 2.5D Tilt on Left & Right Panels
        if (tiltLeft && tiltRight) {
            const rotX = (-normOffsetY / centerY) * 2.5;
            const rotY = (normOffsetX / centerX) * 2.8;
            tiltLeft.style.transform = `rotateX(${rotX.toFixed(2)}deg) rotateY(${rotY.toFixed(2)}deg) translate3d(${(normOffsetX * 0.01).toFixed(1)}px, ${(normOffsetY * 0.01).toFixed(1)}px, 0)`;
            tiltRight.style.transform = `rotateX(${(rotX * 0.9).toFixed(2)}deg) rotateY(${(rotY * 0.9).toFixed(2)}deg) translate3d(${(normOffsetX * 0.015).toFixed(1)}px, ${(normOffsetY * 0.015).toFixed(1)}px, 0)`;
        }

        ctx.clearRect(0, 0, width, height);

        const canvasShiftX = normOffsetX * 0.022;
        const canvasShiftY = normOffsetY * 0.022;

        for (let i = ripples.length - 1; i >= 0; i--) {
            const r = ripples[i];
            r.radius += r.speed;
            r.alpha -= 0.01;

            if (r.alpha <= 0 || r.radius >= r.maxRadius) {
                ripples.splice(i, 1);
                continue;
            }

            ctx.save();
            ctx.beginPath();
            ctx.arc(r.x + canvasShiftX, r.y + canvasShiftY, r.radius, 0, Math.PI * 2);
            ctx.strokeStyle = `rgba(239, 68, 68, ${r.alpha})`;
            ctx.lineWidth = 1.8;
            ctx.stroke();
            ctx.restore();
        }

        docSheets.forEach(sheet => {
            sheet.update();
            sheet.draw(ctx, canvasShiftX * 0.8, canvasShiftY * 0.8);
        });

        planes.forEach(plane => {
            plane.update();
            plane.draw(ctx, canvasShiftX, canvasShiftY);
        });
    }

    requestAnimationFrame(animateParallax);
})();
</script>

<!-- ====================================================
     APPLICATION LOGIC & STATE PIPELINE
     ==================================================== -->
<script>
let selectedFilesQueue = [];
let activeTheme = localStorage.getItem('presspdf_skeuo_theme') || 'aluminum';
if (activeTheme !== 'dark-titanium') activeTheme = 'aluminum';
let isCompressing = false;
let currentQueueFilter = 'all'; // 'all' | 'needs_compress' | 'safe'
let currentSourceInfo = { type: 'files', name: '' };
const TEN_MB_BYTES = 10 * 1024 * 1024; // 10,485,760 bytes

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const URL_COMPRESS_ENDPOINT = '{{ route("pdf.compress") }}';

// Apply saved theme
if (activeTheme === 'dark-titanium') {
    document.documentElement.setAttribute('data-theme', 'dark-titanium');
    document.getElementById('themeSwitchText').textContent = 'Mode Aluminum';
} else {
    document.documentElement.removeAttribute('data-theme');
    document.getElementById('themeSwitchText').textContent = 'Mode Titanium';
}

function toggleThemeMode() {
    if (activeTheme === 'aluminum') {
        activeTheme = 'dark-titanium';
        document.documentElement.setAttribute('data-theme', 'dark-titanium');
        document.getElementById('themeSwitchText').textContent = 'Mode Aluminum';
    } else {
        activeTheme = 'aluminum';
        document.documentElement.removeAttribute('data-theme');
        document.getElementById('themeSwitchText').textContent = 'Mode Titanium';
    }
    localStorage.setItem('presspdf_skeuo_theme', activeTheme);
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
        if (file.size > 524288000) { // 500 MB
            showToast(`"${file.name}" dilewati: Ukuran melebihi batas 500 MB.`);
            return;
        }

        const relPath = file.customRelativePath || file.webkitRelativePath || file.name;
        const exists = selectedFilesQueue.some(item => item.name === file.name && item.size === file.size && item.relativePath === relPath);
        if (!exists) {
            const isOver10 = file.size > TEN_MB_BYTES;
            selectedFilesQueue.push({
                id: 'f_' + Math.random().toString(36).substr(2, 9),
                file: file,
                name: file.name,
                relativePath: relPath,
                size: file.size,
                isOver10Mb: isOver10,
                shouldCompress: isOver10,
                status: isOver10 ? 'pending' : 'safe_skip',
                statusText: isOver10 ? 'Menunggu' : 'Aman (Dilewati)',
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

    // Switch view states
    document.getElementById('dropzoneView').style.display = 'none';
    document.getElementById('workspaceView').classList.add('show');
    document.getElementById('resultSection').classList.remove('show');

    const currentVal = document.getElementById('targetSizeSlider').value;
    handleSliderChange(currentVal);

    const totalNeeds = selectedFilesQueue.filter(f => f.isOver10Mb).length;
    if (totalNeeds === 0 && selectedFilesQueue.length > 0) {
        showSmallPopup(
            currentSourceInfo.name || 'Folder Dokumen',
            `Semua ${selectedFilesQueue.length} file PDF sudah berukuran ≤ 10 MB. Dokumen Anda aman tanpa perlu kompresi!`
        );
    }
}

function updateInsightsStrip() {
    const totalPdfs = selectedFilesQueue.length;
    const over10Mb = selectedFilesQueue.filter(f => f.isOver10Mb).length;
    const under10Mb = totalPdfs - over10Mb;

    document.getElementById('metricTotalCount').textContent = totalPdfs;
    document.getElementById('metricNeedsCount').textContent = over10Mb;
    document.getElementById('metricSafeCount').textContent = under10Mb;

    document.getElementById('tabCountAll').textContent = totalPdfs;
    document.getElementById('tabCountNeeds').textContent = over10Mb;
    document.getElementById('tabCountSafe').textContent = under10Mb;

    const folderNameEl = document.getElementById('insightsFolderName');
    const sourceDescEl = document.getElementById('insightsSourceDesc');
    const sourceIcon = document.getElementById('insightsSourceIcon');

    if (currentSourceInfo.type === 'folder') {
        folderNameEl.textContent = currentSourceInfo.name || 'Folder Dokumen';
        sourceDescEl.textContent = `Pemeriksaan struktur folder selesai • ${totalPdfs} PDF ditemukan`;
        sourceIcon.className = 'fa-solid fa-folder-tree';
    } else {
        folderNameEl.textContent = `${totalPdfs} Dokumen Terpilih`;
        sourceDescEl.textContent = `Batch file PDF satuan siap diproses`;
        sourceIcon.className = 'fa-solid fa-file-pdf';
    }
}

function setQueueFilter(filterType) {
    currentQueueFilter = filterType;
    document.querySelectorAll('.tab-filter-btn').forEach(btn => btn.classList.remove('active'));
    if (filterType === 'all') document.getElementById('tabFilterAll').classList.add('active');
    if (filterType === 'needs_compress') document.getElementById('tabFilterNeeds').classList.add('active');
    if (filterType === 'safe') document.getElementById('tabFilterSafe').classList.add('active');
    renderFilesQueue();
}

function renderFilesQueue() {
    const queueList = document.getElementById('filesQueueList');
    const btnText = document.getElementById('compressBtnText');
    const compressBtn = document.getElementById('compressBtn');

    const over10MbCount = selectedFilesQueue.filter(f => f.isOver10Mb).length;

    if (over10MbCount > 0) {
        compressBtn.disabled = false;
        btnText.textContent = `Kompres ${over10MbCount} File Terdeteksi (> 10 MB)`;
    } else {
        compressBtn.disabled = true;
        btnText.textContent = `Semua File Sudah Aman (≤ 10 MB)`;
    }

    queueList.innerHTML = '';

    let filesToDisplay = selectedFilesQueue;
    if (currentQueueFilter === 'needs_compress') {
        filesToDisplay = selectedFilesQueue.filter(f => f.isOver10Mb);
    } else if (currentQueueFilter === 'safe') {
        filesToDisplay = selectedFilesQueue.filter(f => !f.isOver10Mb);
    }

    if (filesToDisplay.length === 0) {
        const emptyNotice = document.createElement('div');
        emptyNotice.style.cssText = 'padding: 20px; text-align: center; color: var(--skeuo-ink-dim); font-family: var(--font-mono); font-size: 12px; font-weight: 700;';
        emptyNotice.innerHTML = currentQueueFilter === 'needs_compress'
            ? '🎉 Tidak ada file di atas 10 MB. Semua file PDF sudah aman!'
            : 'Tidak ada file dalam kategori ini.';
        queueList.appendChild(emptyNotice);
        return;
    }

    filesToDisplay.forEach((item) => {
        const card = document.createElement('div');
        card.className = `file-physical-card ${item.isOver10Mb ? 'needs-comp' : 'safe-skip'}`;
        card.id = `queueItem_${item.id}`;

        let statusClass = 'status-pill-chip';
        let statusText = item.statusText;

        if (item.status === 'compressing') {
            statusClass += ' compressing';
            statusText = 'Mengompresi...';
        } else if (item.status === 'done') {
            statusClass += ' success';
            statusText = item.statusText;
        } else if (item.status === 'error') {
            statusClass += ' error';
            statusText = 'Gagal';
        } else if (item.isOver10Mb) {
            statusClass += ' needs-comp';
            statusText = '🔴 > 10 MB';
        } else {
            statusClass += ' safe-skip';
            statusText = '🟢 Aman';
        }

        const relPathHtml = item.relativePath && item.relativePath !== item.name
            ? `<span title="${escapeHtml(item.relativePath)}"><i class="fa-regular fa-folder" style="margin-right: 3px;"></i>${escapeHtml(item.relativePath)}</span>`
            : '';

        card.innerHTML = `
            <div class="file-card-left">
                <div class="file-card-icon">
                    <i class="fa-solid ${item.isOver10Mb ? 'fa-file-pdf' : 'fa-file-shield'}"></i>
                </div>
                <div class="file-card-info">
                    <div class="file-card-name" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
                    <div class="file-card-meta">
                        ${relPathHtml}
                        <span>Ukuran: <strong style="color:var(--skeuo-ink);">${formatBytes(item.size)}</strong></span>
                        <span class="${statusClass}" id="statusTag_${item.id}">${statusText}</span>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-card-del" onclick="removeFileFromQueue('${item.id}')" title="Hapus dari antrean">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;
        queueList.appendChild(card);
    });
}

function removeFileFromQueue(id) {
    if (isCompressing) return;
    selectedFilesQueue = selectedFilesQueue.filter(f => f.id !== id);
    if (selectedFilesQueue.length === 0) {
        clearSelectedFiles();
    } else {
        updateInsightsStrip();
        renderFilesQueue();
    }
}

function clearSelectedFiles() {
    if (isCompressing) return;
    selectedFilesQueue = [];
    document.getElementById('fileInput').value = '';
    document.getElementById('folderInput').value = '';
    document.getElementById('dropzoneView').style.display = 'flex';
    document.getElementById('workspaceView').classList.remove('show');
    document.getElementById('progressBox').classList.remove('show');
    resetCompressBtn();
}

function onDragOver(e) {
    e.preventDefault();
    document.getElementById('dropzoneView').classList.add('drag-over');
}

function onDragLeave(e) {
    document.getElementById('dropzoneView').classList.remove('drag-over');
}

async function onDrop(e) {
    e.preventDefault();
    document.getElementById('dropzoneView').classList.remove('drag-over');

    let droppedFiles = [];
    let detectedFolderName = '';

    if (e.dataTransfer && e.dataTransfer.items && e.dataTransfer.items.length > 0) {
        const items = Array.from(e.dataTransfer.items);
        const entries = [];
        for (const item of items) {
            const entry = item.webkitGetAsEntry ? item.webkitGetAsEntry() : null;
            if (entry) {
                if (entry.isDirectory && !detectedFolderName) {
                    detectedFolderName = entry.name;
                }
                entries.push(scanFileSystemEntry(entry));
            } else if (item.getAsFile) {
                const f = item.getAsFile();
                if (f) droppedFiles.push(f);
            }
        }
        if (entries.length > 0) {
            const scanned = await Promise.all(entries);
            droppedFiles = droppedFiles.concat(scanned.flat());
        }
    } else if (e.dataTransfer && e.dataTransfer.files) {
        droppedFiles = Array.from(e.dataTransfer.files);
    }

    if (droppedFiles.length === 0) return;

    if (detectedFolderName) {
        currentSourceInfo = { type: 'folder', name: detectedFolderName };
    } else {
        currentSourceInfo = { type: 'files', name: `${droppedFiles.length} Dokumen PDF` };
    }

    processIncomingFiles(droppedFiles, !!detectedFolderName);
}

function scanFileSystemEntry(entry, path = '') {
    return new Promise((resolve) => {
        if (entry.isFile) {
            entry.file(f => {
                const subPath = path ? `${path}/${f.name}` : f.name;
                Object.defineProperty(f, 'customRelativePath', { value: subPath, writable: true });
                resolve([f]);
            }, () => resolve([]));
        } else if (entry.isDirectory) {
            const dirReader = entry.createReader();
            const collected = [];
            const dirPath = path ? `${path}/${entry.name}` : entry.name;
            const readBatch = () => {
                dirReader.readEntries(async (entries) => {
                    if (!entries || entries.length === 0) {
                        const subArrays = await Promise.all(collected.map(e => scanFileSystemEntry(e, dirPath)));
                        resolve(subArrays.flat());
                    } else {
                        collected.push(...entries);
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

window.handleFiles = function(files, folderName = null) {
    if (folderName) {
        currentSourceInfo = { type: 'folder', name: folderName };
    } else {
        currentSourceInfo = { type: 'files', name: `${files.length} Dokumen PDF` };
    }
    processIncomingFiles(files, !!folderName);
};

// Preset Controls
let currentCompressionMode = 'recommended';

function selectCompressionPreset(val, modeName) {
    currentCompressionMode = modeName;
    const num = parseFloat(val);
    const slider = document.getElementById('targetSizeSlider');
    if (slider) slider.value = num;

    updatePresetCardsUI(modeName);
    updateTargetLabelUI(num);
    updateQualityHintUI(modeName, num);
}

function updatePresetCardsUI(modeName) {
    document.getElementById('presetLow')?.classList.toggle('active', modeName === 'low');
    document.getElementById('presetRec')?.classList.toggle('active', modeName === 'recommended');
    document.getElementById('presetHigh')?.classList.toggle('active', modeName === 'extreme');
}

function updateTargetLabelUI(num) {
    const lbl = document.getElementById('targetSizeLabel');
    if (lbl) lbl.textContent = `${num.toFixed(1)} MB`;
}

function updateQualityHintUI(modeName, num) {
    const hint = document.getElementById('qualityHintText');
    if (!hint) return;

    if (modeName === 'low' || num <= 3.0) {
        hint.textContent = `Ekonomis (Maksimal ${num.toFixed(1)} MB — Super Hemat & Ringan)`;
        hint.style.color = 'var(--skeuo-ink)';
    } else if (modeName === 'recommended' || (num > 3.0 && num <= 6.0)) {
        hint.textContent = `Standar Ideal (Maksimal ${num.toFixed(1)} MB — Seimbang & Jernih)`;
        hint.style.color = 'var(--primary-red)';
    } else {
        hint.textContent = `Presisi Tinggi (Maksimal ${num.toFixed(1)} MB — Ketajaman Maksimal)`;
        hint.style.color = 'var(--led-amber)';
    }
}

function handleSliderChange(val) {
    const num = parseFloat(val);
    if (Math.abs(num - 3.0) < 0.2) currentCompressionMode = 'low';
    else if (Math.abs(num - 5.5) < 0.2) currentCompressionMode = 'recommended';
    else if (Math.abs(num - 9.8) < 0.2) currentCompressionMode = 'extreme';
    else currentCompressionMode = 'custom';

    updatePresetCardsUI(currentCompressionMode);
    updateTargetLabelUI(num);
    updateQualityHintUI(currentCompressionMode, num);
}

function toggleCustomSlider() {
    const drawer = document.getElementById('customSliderDrawer');
    const chevron = document.getElementById('customSliderChevron');
    if (drawer) {
        drawer.classList.toggle('show');
        const isShown = drawer.classList.contains('show');
        if (chevron) {
            chevron.className = isShown ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down';
        }
    }
}

// Batch Compression Pipeline
async function startBatchCompress() {
    const filesToCompress = selectedFilesQueue.filter(f => f.isOver10Mb);
    if (filesToCompress.length === 0 || isCompressing) {
        showSmallPopup(
            currentSourceInfo.name || 'Dokumen Pilihan',
            'Tidak ada file berukuran di atas 10 MB. Semua file sudah aman tanpa perlu kompresi!'
        );
        return;
    }

    isCompressing = true;
    const targetSize = document.getElementById('targetSizeSlider').value;
    const btn = document.getElementById('compressBtn');
    btn.disabled = true;
    btn.innerHTML = '<div class="mini-spinner"></div><span>Mengompresi...</span>';

    const progBox = document.getElementById('progressBox');
    progBox.classList.add('show');

    const totalToCompress = filesToCompress.length;
    const batchToken = generateBatchToken();
    const results = [];

    for (let i = 0; i < totalToCompress; i++) {
        const item = filesToCompress[i];
        item.status = 'compressing';
        const statusTag = document.getElementById(`statusTag_${item.id}`);
        if (statusTag) {
            statusTag.className = 'status-pill-chip compressing';
            statusTag.textContent = 'Mengompresi...';
        }

        const overallPct = Math.round((i / totalToCompress) * 100);
        setProgress(overallPct);
        document.getElementById('progressPctText').textContent = `${overallPct}%`;
        document.getElementById('progressStageText').textContent = `Memproses (${i + 1}/${totalToCompress}): ${item.name}`;

        const form = new FormData();
        form.append('file', item.file);
        form.append('target_size', targetSize);
        form.append('batch_token', batchToken);
        form.append('_token', CSRF_TOKEN);

        try {
            const res = await fetch(URL_COMPRESS_ENDPOINT, { method: 'POST', body: form });
            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || data.errors?.file?.[0] || 'Gagal memproses file.');
            }

            if (data.status === 'small') {
                item.status = 'done';
                item.statusText = 'Sudah < 10 MB';
                if (statusTag) {
                    statusTag.className = 'status-pill-chip safe-skip';
                    statusTag.textContent = 'Sudah < 10 MB';
                }
            } else {
                item.status = 'done';
                item.statusText = `Selesai (${data.compressed_human})`;
                if (statusTag) {
                    statusTag.className = 'status-pill-chip success';
                    statusTag.textContent = `Selesai (${data.compressed_human})`;
                }
                results.push({
                    file: item.file,
                    relativePath: item.relativePath,
                    status: 'compressed',
                    original_size: data.original_size,
                    compressed_size: data.compressed_size,
                    filename: data.filename,
                    download_url: data.download_url,
                    token: data.token,
                    batch_download_url: data.batch_download_url,
                    ratio: data.ratio
                });
            }
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
    document.getElementById('progressStageText').textContent = 'Semua file selesai diproses!';

    isCompressing = false;
    resetCompressBtn();

    const successfulResults = results.filter(r => r.status === 'compressed');
    if (successfulResults.length > 0) {
        showBatchResults(successfulResults, batchToken);
    } else {
        showToast('Tidak ada file yang berhasil dikompresi.');
    }
}

function resetCompressBtn() {
    const btn = document.getElementById('compressBtn');
    if (btn) {
        const over10MbCount = selectedFilesQueue.filter(f => f.isOver10Mb).length;
        if (over10MbCount > 0) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-bolt"></i><span>Kompres ${over10MbCount} File Terdeteksi (> 10 MB)</span>`;
        } else {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-check"></i><span>Semua File Sudah Aman (≤ 10 MB)</span>`;
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

    const listContainer = document.getElementById('batchResultList');
    listContainer.innerHTML = '';

    resultsList.forEach(item => {
        totalOrig += item.original_size;
        totalComp += item.compressed_size;

        const row = document.createElement('div');
        row.className = 'result-row-card';
        row.innerHTML = `
            <div style="min-width:0; flex:1;">
                <div style="font-family:var(--font-heading); font-size:13px; font-weight:800; color:var(--skeuo-ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${escapeHtml(item.filename)}">
                    <i class="fa-solid fa-file-pdf" style="color: var(--primary-red); margin-right: 6px;"></i>
                    ${escapeHtml(item.filename)}
                </div>
                <div style="font-size:11px; font-family:var(--font-mono); color:var(--skeuo-ink-dim); display:flex; gap:8px;">
                    <span>${formatBytes(item.original_size)} &rarr; <strong>${formatBytes(item.compressed_size)}</strong></span>
                    <span style="color: var(--led-green); font-weight: 800;">(-${item.ratio}%)</span>
                </div>
            </div>
            <a class="btn-dl-individual" href="${item.download_url}" target="_blank" title="Unduh dokumen ini">
                <i class="fa-solid fa-download"></i>
                <span>Unduh</span>
            </a>
        `;
        listContainer.appendChild(row);
    });

    const totalSaved = Math.max(0, totalOrig - totalComp);
    const totalRatio = totalOrig > 0 ? Math.round((1 - totalComp / totalOrig) * 100) : 0;

    document.getElementById('statOriginal').textContent = formatBytes(totalOrig);
    document.getElementById('statCompressed').textContent = formatBytes(totalComp);
    document.getElementById('statSavings').textContent = `-${totalRatio}% (${formatBytes(totalSaved)})`;

    const zipBtn = document.getElementById('btnDownloadZipLink');
    const singleBtn = document.getElementById('btnDownloadSingleLink');

    const skippedSafeCount = selectedFilesQueue.filter(f => !f.isOver10Mb).length;
    const skippedHint = skippedSafeCount > 0 ? ` (${skippedSafeCount} file ≤ 10 MB aman & dilewati)` : '';

    if (resultsList.length > 1) {
        document.getElementById('resultHeading').textContent = `Berhasil Mengompresi ${resultsList.length} Dokumen!`;
        document.getElementById('resultFileName').textContent = `Seluruh dokumen terkompresi siap diunduh dalam 1 arsip .ZIP atau satuan${skippedHint}`;
        zipBtn.style.display = 'inline-flex';
        zipBtn.href = `/download-batch/${batchToken}`;
        singleBtn.style.display = 'none';
    } else {
        document.getElementById('resultHeading').textContent = 'Kompresi Dokumen Berhasil!';
        document.getElementById('resultFileName').textContent = `${resultsList[0].filename}${skippedHint}`;
        zipBtn.style.display = 'none';
        singleBtn.style.display = 'inline-flex';
        singleBtn.href = resultsList[0].download_url;
    }

    document.getElementById('resultSection').classList.add('show');
    document.getElementById('resultSection').scrollIntoView({ behavior: 'smooth', block: 'center' });
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

document.getElementById('modalSmall').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
});

function showToast(msg) {
    const el = document.createElement('div');
    el.className = 'toast-card';
    el.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="color: var(--primary-red);"></i> ${msg}`;
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
