@extends('layouts.app')

@section('title', 'PressPDF — Upload & Kompres PDF')

@push('styles')
<style>
/* ===========================
   HERO SECTION
   =========================== */
.hero {
    text-align: center;
    padding: 40px 0 56px;
}
.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    background: rgba(108, 99, 255, 0.12);
    border: 1px solid rgba(108, 99, 255, 0.25);
    border-radius: var(--radius-pill);
    font-size: 12px;
    font-weight: 600;
    color: #a78bfa;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 20px;
}
.hero-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: clamp(36px, 6vw, 64px);
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 16px;
    letter-spacing: -1px;
}
.hero-subtitle {
    font-size: 18px;
    color: var(--text-secondary);
    max-width: 560px;
    margin: 0 auto 32px;
    line-height: 1.7;
}
.hero-stats {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
    margin-bottom: 0;
}
.hero-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}
.hero-stat-value {
    font-size: 28px;
    font-weight: 800;
    font-family: 'Space Grotesk', sans-serif;
    background: linear-gradient(135deg, var(--primary-start), var(--accent-cyan));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.hero-stat-label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.hero-stat-divider {
    width: 1px;
    height: 40px;
    background: var(--border-subtle);
}

/* ===========================
   WORKFLOW STEPS
   =========================== */
.workflow-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 40px;
    flex-wrap: wrap;
}
.workflow-step {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    background: var(--bg-glass);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    font-size: 13px;
    color: var(--text-secondary);
    transition: var(--transition);
}
.workflow-step.active {
    background: rgba(108, 99, 255, 0.12);
    border-color: rgba(108, 99, 255, 0.3);
    color: #a78bfa;
}
.workflow-step.completed {
    background: rgba(16, 185, 129, 0.10);
    border-color: rgba(16, 185, 129, 0.25);
    color: #10b981;
}
.workflow-step-num {
    width: 24px; height: 24px;
    border-radius: 50%;
    background: var(--bg-surface2);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid var(--border-subtle);
    flex-shrink: 0;
    transition: var(--transition);
}
.workflow-step.active .workflow-step-num {
    background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
    border-color: transparent;
    color: white;
}
.workflow-step.completed .workflow-step-num {
    background: linear-gradient(135deg, #059669, #10b981);
    border-color: transparent;
    color: white;
}
.workflow-arrow {
    color: var(--text-muted);
    font-size: 12px;
    padding: 0 8px;
    flex-shrink: 0;
}

/* ===========================
   UPLOAD ZONE
   =========================== */
.upload-section {
    margin-bottom: 32px;
}
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 12px;
}
.section-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}
.section-title-icon {
    width: 36px; height: 36px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
}
.section-title-icon.purple {
    background: rgba(108, 99, 255, 0.15);
    color: #a78bfa;
}
.section-title-icon.cyan {
    background: rgba(6, 182, 212, 0.12);
    color: var(--accent-cyan);
}
.section-title-icon.emerald {
    background: rgba(16, 185, 129, 0.12);
    color: var(--accent-emerald);
}

.upload-tabs {
    display: flex;
    gap: 8px;
    background: var(--bg-surface);
    padding: 4px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-subtle);
    margin-bottom: 20px;
}
.upload-tab {
    flex: 1;
    padding: 10px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: var(--transition-fast);
    font-family: 'Inter', sans-serif;
}
.upload-tab.active {
    background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
    color: white;
    box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
}

.drop-zone {
    border: 2px dashed rgba(108, 99, 255, 0.25);
    border-radius: var(--radius-xl);
    padding: 60px 40px;
    text-align: center;
    background: rgba(108, 99, 255, 0.03);
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}
.drop-zone::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center, rgba(108,99,255,0.06) 0%, transparent 70%);
    opacity: 0;
    transition: var(--transition);
}
.drop-zone:hover,
.drop-zone.dragging {
    border-color: rgba(108, 99, 255, 0.5);
    background: rgba(108, 99, 255, 0.06);
    box-shadow: 0 0 40px rgba(108, 99, 255, 0.1);
}
.drop-zone:hover::before,
.drop-zone.dragging::before {
    opacity: 1;
}
.drop-zone.dragging {
    border-style: solid;
    animation: borderPulse 1s ease-in-out infinite;
}
@keyframes borderPulse {
    0%, 100% { border-color: rgba(108, 99, 255, 0.5); }
    50%       { border-color: rgba(168, 85, 247, 0.8); }
}

.drop-icon-wrap {
    position: relative;
    display: inline-block;
    margin-bottom: 20px;
}
.drop-icon {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, rgba(108,99,255,0.15), rgba(168,85,247,0.15));
    border: 1px solid rgba(108, 99, 255, 0.25);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #a78bfa;
    margin: 0 auto;
    transition: var(--transition);
    animation: iconFloat 3s ease-in-out infinite;
}
.drop-zone:hover .drop-icon {
    background: linear-gradient(135deg, rgba(108,99,255,0.25), rgba(168,85,247,0.25));
    transform: scale(1.05);
}
@keyframes iconFloat {
    0%, 100% { transform: translateY(0); }
    50%       { transform: translateY(-6px); }
}
.drop-icon-ring {
    position: absolute;
    inset: -8px;
    border-radius: calc(var(--radius-lg) + 8px);
    border: 1px solid rgba(108, 99, 255, 0.15);
    animation: ringPulse 3s ease-in-out infinite;
}
@keyframes ringPulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50%       { transform: scale(1.05); opacity: 1; }
}

.drop-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 8px;
    font-family: 'Space Grotesk', sans-serif;
}
.drop-subtitle {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 20px;
}
.drop-hint {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}
.drop-hint-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
    padding: 5px 12px;
    background: var(--bg-glass);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-pill);
}
.drop-hint-item i {
    color: #a78bfa;
    font-size: 11px;
}

/* Hidden file inputs */
#fileInput, #folderInput {
    display: none;
}

/* ===========================
   SCAN SUMMARY BANNER
   =========================== */
.scan-summary {
    display: none;
    padding: 16px 20px;
    background: rgba(108, 99, 255, 0.08);
    border: 1px solid rgba(108, 99, 255, 0.2);
    border-radius: var(--radius-md);
    margin-bottom: 20px;
    animation: fadeSlideIn 0.4s ease;
}
.scan-summary.show {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.scan-stats {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}
.scan-stat {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.scan-stat-value {
    font-size: 20px;
    font-weight: 800;
    font-family: 'Space Grotesk', sans-serif;
}
.scan-stat-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.scan-stat-value.eligible { color: #a78bfa; }
.scan-stat-value.skipped  { color: #f59e0b; }
.scan-stat-value.total    { color: var(--accent-cyan); }

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ===========================
   FILE TABLE
   =========================== */
.file-table-section {
    display: none;
    animation: fadeSlideIn 0.5s ease;
}
.file-table-section.show {
    display: block;
}

.file-table-container {
    overflow: hidden;
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-subtle);
    background: var(--bg-glass);
    backdrop-filter: blur(12px);
    margin-bottom: 20px;
}

.file-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.file-table thead {
    background: rgba(255,255,255,0.03);
    border-bottom: 1px solid var(--border-subtle);
}
.file-table th {
    padding: 14px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    white-space: nowrap;
}
.file-table th:first-child {
    padding-left: 20px;
    width: 40px;
}
.file-table td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    vertical-align: middle;
    color: var(--text-secondary);
}
.file-table td:first-child { padding-left: 20px; }
.file-table tbody tr {
    transition: var(--transition-fast);
}
.file-table tbody tr:hover {
    background: rgba(108, 99, 255, 0.05);
}
.file-table tbody tr:last-child td {
    border-bottom: none;
}

.file-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.file-icon {
    width: 32px; height: 32px;
    background: rgba(244, 63, 94, 0.12);
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    color: #f43f5e;
    flex-shrink: 0;
}
.file-filename {
    font-weight: 500;
    color: var(--text-primary);
    word-break: break-all;
}
.file-size-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: var(--radius-pill);
    font-size: 12px;
    font-weight: 600;
    color: #f59e0b;
    font-family: 'Space Grotesk', sans-serif;
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: var(--radius-pill);
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.status-pending    { background: rgba(100,116,139,0.15); color: #94a3b8; border: 1px solid rgba(100,116,139,0.2); }
.status-processing { background: rgba(6,182,212,0.12); color: var(--accent-cyan); border: 1px solid rgba(6,182,212,0.25); }
.status-completed  { background: rgba(16,185,129,0.12); color: #10b981; border: 1px solid rgba(16,185,129,0.25); }
.status-failed     { background: rgba(244,63,94,0.12); color: #f43f5e; border: 1px solid rgba(244,63,94,0.25); }

/* Compression ratio */
.ratio-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'Space Grotesk', sans-serif;
}
.ratio-badge.good  { color: #10b981; }
.ratio-badge.fair  { color: #f59e0b; }
.ratio-badge.none  { color: var(--text-muted); }

/* Checkbox */
.checkbox-cell input[type="checkbox"] {
    width: 16px; height: 16px;
    cursor: pointer;
    accent-color: var(--primary-mid);
}

/* ===========================
   QUALITY SELECTOR
   =========================== */
.quality-selector {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.quality-option {
    position: relative;
}
.quality-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}
.quality-label {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 10px 16px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-subtle);
    background: var(--bg-glass);
    cursor: pointer;
    transition: var(--transition-fast);
    min-width: 100px;
}
.quality-label-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    text-transform: capitalize;
}
.quality-label-desc {
    font-size: 11px;
    color: var(--text-muted);
}
.quality-option input:checked + .quality-label {
    background: rgba(108, 99, 255, 0.12);
    border-color: rgba(108, 99, 255, 0.4);
    box-shadow: 0 0 20px rgba(108, 99, 255, 0.15);
}
.quality-option input:checked + .quality-label .quality-label-name {
    color: #a78bfa;
}

/* ===========================
   ACTION TOOLBAR
   =========================== */
.action-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding: 16px 20px;
    background: var(--bg-glass);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    margin-top: 8px;
}
.action-toolbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.action-toolbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.select-all-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: var(--bg-glass);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition-fast);
    font-family: 'Inter', sans-serif;
}
.select-all-btn:hover {
    background: var(--bg-glass-hover);
    color: var(--text-primary);
}
.select-all-btn input[type="checkbox"] {
    accent-color: var(--primary-mid);
    cursor: pointer;
}

/* ===========================
   PROGRESS SECTION
   =========================== */
.compress-progress {
    display: none;
    padding: 20px;
    background: rgba(108, 99, 255, 0.06);
    border: 1px solid rgba(108, 99, 255, 0.2);
    border-radius: var(--radius-lg);
    margin-bottom: 20px;
    animation: fadeSlideIn 0.3s ease;
}
.compress-progress.show {
    display: block;
}
.compress-progress-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.compress-progress-title {
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #a78bfa;
}
.compress-progress-pct {
    font-size: 13px;
    font-weight: 700;
    color: #a78bfa;
    font-family: 'Space Grotesk', sans-serif;
}

/* ===========================
   RESULTS SECTION
   =========================== */
.results-section {
    display: none;
    animation: fadeSlideIn 0.5s ease;
}
.results-section.show { display: block; }

.result-summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.result-summary-card {
    padding: 20px 24px;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-subtle);
    background: var(--bg-glass);
    backdrop-filter: blur(12px);
    transition: var(--transition);
}
.result-summary-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-hover);
}
.result-summary-card-icon {
    width: 40px; height: 40px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    margin-bottom: 12px;
}
.result-summary-card-value {
    font-size: 28px;
    font-weight: 800;
    font-family: 'Space Grotesk', sans-serif;
    margin-bottom: 4px;
}
.result-summary-card-label {
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ===========================
   SKIPPED FILES SECTION
   =========================== */
.skipped-section {
    display: none;
    margin-top: 20px;
}
.skipped-section.show { display: block; }
.skipped-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: var(--radius-md);
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #f59e0b;
    width: 100%;
    text-align: left;
    font-family: 'Inter', sans-serif;
}
.skipped-list {
    display: none;
    margin-top: 8px;
    padding: 12px;
    background: rgba(245, 158, 11, 0.04);
    border: 1px solid rgba(245, 158, 11, 0.15);
    border-radius: var(--radius-md);
}
.skipped-list.show { display: block; }
.skipped-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 4px;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    font-size: 13px;
    color: var(--text-secondary);
}
.skipped-item:last-child { border-bottom: none; }

/* ===========================
   UPLOAD PROGRESS OVERLAY
   =========================== */
.upload-overlay {
    display: none;
    position: absolute;
    inset: 0;
    background: rgba(10, 10, 15, 0.85);
    backdrop-filter: blur(4px);
    border-radius: var(--radius-xl);
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    z-index: 10;
    animation: fadeIn 0.3s ease;
}
.upload-overlay.show {
    display: flex;
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.upload-spinner {
    width: 48px; height: 48px;
    border: 3px solid rgba(108, 99, 255, 0.2);
    border-top-color: #a78bfa;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
.upload-overlay-text {
    font-size: 15px;
    font-weight: 600;
    color: #a78bfa;
}
.upload-overlay-sub {
    font-size: 12px;
    color: var(--text-muted);
}

/* ===========================
   EMPTY STATE
   =========================== */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}
.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    color: rgba(108,99,255,0.3);
}
.empty-state p {
    font-size: 15px;
}

/* ===========================
   ENGINE WARNING
   =========================== */
.engine-warning {
    padding: 16px 20px;
    background: rgba(245, 158, 11, 0.08);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: var(--radius-md);
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 28px;
    font-size: 13px;
    color: #d97706;
    line-height: 1.6;
}
.engine-warning i { margin-top: 2px; flex-shrink: 0; }
.engine-warning strong { display: block; margin-bottom: 4px; }
.engine-warning code {
    background: rgba(245,158,11,0.12);
    padding: 1px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-family: monospace;
}

/* ===========================
   CONFETTI ANIMATION
   =========================== */
.confetti-piece {
    position: fixed;
    width: 8px; height: 8px;
    border-radius: 2px;
    pointer-events: none;
    z-index: 9999;
    animation: confettiFall 3s ease-in forwards;
}
@keyframes confettiFall {
    0%   { transform: translateY(-20px) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}

/* ===========================
   RESPONSIVE
   =========================== */
@media (max-width: 768px) {
    .hero-title { font-size: 36px; }
    .file-table th:nth-child(4),
    .file-table td:nth-child(4) { display: none; }
    .workflow-steps { gap: 4px; }
    .workflow-arrow { display: none; }
}
@media (max-width: 480px) {
    .hero-stats { gap: 16px; }
    .hero-stat-divider { display: none; }
    .quality-selector { flex-direction: column; }
    .action-toolbar { flex-direction: column; align-items: stretch; }
}
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="hero" aria-labelledby="heroTitle">
    <div class="hero-eyebrow">
        <i class="fa-solid fa-wand-magic-sparkles"></i>
        Smart PDF Compression Engine
    </div>
    <h1 class="hero-title" id="heroTitle">
        Kompres PDF
        <span class="gradient-text">Tanpa Batas,</span><br>
        Dengan Presisi
    </h1>
    <p class="hero-subtitle">
        Upload file atau folder PDF, sistem kami otomatis menyaring file > 10 MB dan mengompresi dengan kualitas terbaik menggunakan engine Ghostscript atau FPDI.
    </p>

    <div class="hero-stats">
        <div class="hero-stat">
            <span class="hero-stat-value">10 MB</span>
            <span class="hero-stat-label">Threshold Filter</span>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat">
            <span class="hero-stat-value">500 MB</span>
            <span class="hero-stat-label">Max Per File</span>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat">
            <span class="hero-stat-value">4 Level</span>
            <span class="hero-stat-label">Kualitas Kompresi</span>
        </div>
        <div class="hero-stat-divider"></div>
        <div class="hero-stat">
            <span class="hero-stat-value">ZIP</span>
            <span class="hero-stat-label">Batch Download</span>
        </div>
    </div>
</section>

<!-- Workflow Steps -->
<div class="workflow-steps" id="workflowSteps" role="list" aria-label="Alur kerja kompresi">
    <div class="workflow-step active" id="step1" role="listitem">
        <span class="workflow-step-num">1</span>
        <i class="fa-solid fa-upload" style="font-size:12px;"></i>
        Upload File
    </div>
    <span class="workflow-arrow" aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
    <div class="workflow-step" id="step2" role="listitem">
        <span class="workflow-step-num">2</span>
        <i class="fa-solid fa-filter" style="font-size:12px;"></i>
        Filter & Scan
    </div>
    <span class="workflow-arrow" aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
    <div class="workflow-step" id="step3" role="listitem">
        <span class="workflow-step-num">3</span>
        <i class="fa-solid fa-list-check" style="font-size:12px;"></i>
        Konfirmasi
    </div>
    <span class="workflow-arrow" aria-hidden="true"><i class="fa-solid fa-chevron-right"></i></span>
    <div class="workflow-step" id="step4" role="listitem">
        <span class="workflow-step-num">4</span>
        <i class="fa-solid fa-download" style="font-size:12px;"></i>
        Download
    </div>
</div>

<!-- Engine Warning (if not Ghostscript) -->
@if($engine !== 'ghostscript')
<div class="engine-warning" role="alert">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <div>
        <strong>Mode FPDI (Fallback) Aktif</strong>
        Ghostscript tidak ditemukan di sistem. Kompresi akan menggunakan FPDI/TCPDF (re-stream PDF, tanpa downsampling gambar).
        Untuk hasil optimal, install <a href="https://www.ghostscript.com/releases/gsdnld.html" target="_blank" rel="noopener noreferrer" style="color:inherit;">Ghostscript</a>
        dan pastikan <code>gswin64c</code> tersedia di PATH.
    </div>
</div>
@endif

<!-- ============================================
     SECTION 1: UPLOAD
     ============================================ -->
<section class="upload-section" id="uploadSection" aria-labelledby="uploadTitle">
    <div class="section-header">
        <h2 class="section-title" id="uploadTitle">
            <span class="section-title-icon purple"><i class="fa-solid fa-file-arrow-up"></i></span>
            Upload File PDF
        </h2>
        <button class="btn btn-secondary btn-sm" id="resetBtn" onclick="resetSession()" style="display:none;" aria-label="Reset dan mulai ulang">
            <i class="fa-solid fa-rotate-left"></i> Reset Ulang
        </button>
    </div>

    <!-- Upload Type Tabs -->
    <div class="upload-tabs" role="tablist" aria-label="Pilih metode upload">
        <button class="upload-tab active" id="tabFiles" role="tab" aria-selected="true" aria-controls="dropzone"
                onclick="switchTab('files')">
            <i class="fa-solid fa-files"></i> File Satuan
        </button>
        <button class="upload-tab" id="tabFolder" role="tab" aria-selected="false" aria-controls="dropzone"
                onclick="switchTab('folder')">
            <i class="fa-solid fa-folder-open"></i> Upload Folder
        </button>
    </div>

    <!-- Drop Zone -->
    <div class="drop-zone" id="dropZone" role="button" tabindex="0"
         aria-label="Area drag & drop untuk upload file PDF"
         onclick="triggerFileInput()"
         onkeydown="if(event.key==='Enter'||event.key===' ') triggerFileInput()"
         ondragover="handleDragOver(event)"
         ondragleave="handleDragLeave(event)"
         ondrop="handleDrop(event)">

        <!-- Upload Loading Overlay -->
        <div class="upload-overlay" id="uploadOverlay" aria-live="polite">
            <div class="upload-spinner"></div>
            <div class="upload-overlay-text">Menganalisis File...</div>
            <div class="upload-overlay-sub" id="overlayFileCount">Menyaring PDF & mengecek ukuran</div>
        </div>

        <div class="drop-icon-wrap" aria-hidden="true">
            <div class="drop-icon" id="dropIcon">
                <i class="fa-solid fa-file-pdf" id="dropIconFa"></i>
            </div>
            <div class="drop-icon-ring"></div>
        </div>

        <h3 class="drop-title" id="dropTitle">Drag & Drop File PDF Anda di Sini</h3>
        <p class="drop-subtitle" id="dropSubtitle">atau klik untuk memilih file dari komputer Anda</p>

        <div class="drop-hint">
            <span class="drop-hint-item"><i class="fa-solid fa-check-circle"></i> Format PDF</span>
            <span class="drop-hint-item"><i class="fa-solid fa-check-circle"></i> Filter otomatis > 10 MB</span>
            <span class="drop-hint-item"><i class="fa-solid fa-check-circle"></i> Multiple file</span>
            <span class="drop-hint-item" id="folderHint" style="display:none;">
                <i class="fa-solid fa-check-circle"></i> Semua sub-folder discan
            </span>
        </div>
    </div>

    <!-- Hidden Inputs -->
    <input type="file" id="fileInput" name="files[]" multiple accept=".pdf"
           aria-label="Input file PDF" onchange="handleFileSelect(this.files)">
    <input type="file" id="folderInput" name="files[]" multiple
           webkitdirectory mozdirectory
           aria-label="Input folder" onchange="handleFileSelect(this.files)">
</section>

<!-- ============================================
     SCAN SUMMARY
     ============================================ -->
<div class="scan-summary" id="scanSummary" aria-live="polite" role="status">
    <div class="scan-stats">
        <div class="scan-stat">
            <span class="scan-stat-value total" id="statTotal">0</span>
            <span class="scan-stat-label">Total PDF</span>
        </div>
        <div class="scan-stat">
            <span class="scan-stat-value eligible" id="statEligible">0</span>
            <span class="scan-stat-label">Akan Diproses (&gt;10 MB)</span>
        </div>
        <div class="scan-stat">
            <span class="scan-stat-value skipped" id="statSkipped">0</span>
            <span class="scan-stat-label">Dilewati (≤10 MB)</span>
        </div>
    </div>
    <button class="btn btn-secondary btn-sm" onclick="triggerFileInput()" aria-label="Tambah lebih banyak file">
        <i class="fa-solid fa-plus"></i> Tambah File
    </button>
</div>

<!-- ============================================
     SECTION 2: FILE LIST & CONFIRMATION
     ============================================ -->
<section class="file-table-section" id="fileTableSection" aria-labelledby="fileListTitle">
    <div class="section-header">
        <h2 class="section-title" id="fileListTitle">
            <span class="section-title-icon cyan"><i class="fa-solid fa-list-check"></i></span>
            Konfirmasi File untuk Dikompresi
        </h2>
    </div>

    <!-- Quality Selector -->
    <div style="margin-bottom: 16px;">
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:10px; font-weight:500;">
            <i class="fa-solid fa-sliders"></i> Pilih Kualitas Kompresi:
        </p>
        <div class="quality-selector" role="radiogroup" aria-label="Pilih level kualitas kompresi">
            <div class="quality-option">
                <input type="radio" name="quality" id="qScreen" value="screen" aria-label="Screen - ukuran terkecil">
                <label class="quality-label" for="qScreen">
                    <span class="quality-label-name">🗜️ Screen</span>
                    <span class="quality-label-desc">72 dpi · Ukuran terkecil</span>
                </label>
            </div>
            <div class="quality-option">
                <input type="radio" name="quality" id="qEbook" value="ebook" checked aria-label="Ebook - keseimbangan optimal">
                <label class="quality-label" for="qEbook">
                    <span class="quality-label-name">📖 eBook</span>
                    <span class="quality-label-desc">150 dpi · Optimal ✓</span>
                </label>
            </div>
            <div class="quality-option">
                <input type="radio" name="quality" id="qPrinter" value="printer" aria-label="Printer - kualitas cetak">
                <label class="quality-label" for="qPrinter">
                    <span class="quality-label-name">🖨️ Printer</span>
                    <span class="quality-label-desc">300 dpi · Kualitas cetak</span>
                </label>
            </div>
            <div class="quality-option">
                <input type="radio" name="quality" id="qPrepress" value="prepress" aria-label="Prepress - warna terjaga">
                <label class="quality-label" for="qPrepress">
                    <span class="quality-label-name">🎨 Prepress</span>
                    <span class="quality-label-desc">300 dpi · Warna terjaga</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Compress Progress -->
    <div class="compress-progress" id="compressProgress" aria-live="polite" role="status">
        <div class="compress-progress-header">
            <span class="compress-progress-title">
                <div class="spinner"></div>
                Memproses Kompresi...
            </span>
            <span class="compress-progress-pct" id="progressPct">0%</span>
        </div>
        <div class="progress-bar-wrap">
            <div class="progress-bar-fill" id="progressFill" style="width:0%"></div>
        </div>
        <p style="font-size:12px; color:var(--text-muted); margin-top:8px;" id="progressStatus">
            Menginisialisasi...
        </p>
    </div>

    <!-- File Table -->
    <div class="file-table-container">
        <table class="file-table" aria-label="Daftar file PDF yang akan dikompresi" id="fileTable">
            <thead>
                <tr>
                    <th class="checkbox-cell" scope="col">
                        <input type="checkbox" id="selectAllChk" aria-label="Pilih semua file"
                               onchange="toggleAllCheckboxes(this)">
                    </th>
                    <th scope="col">Nama File</th>
                    <th scope="col">Ukuran Asli</th>
                    <th scope="col">Ukuran Terkompresi</th>
                    <th scope="col">Rasio</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody id="fileTableBody">
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fa-regular fa-folder-open"></i>
                            <p>Belum ada file. Upload PDF di atas.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Action Toolbar -->
    <div class="action-toolbar" id="actionToolbar">
        <div class="action-toolbar-left">
            <label class="select-all-btn" for="selectAllChk2">
                <input type="checkbox" id="selectAllChk2" aria-label="Pilih semua"
                       onchange="toggleAllCheckboxes(this)">
                Pilih Semua
            </label>
            <span id="selectedCount" style="font-size:13px; color:var(--text-muted);">
                0 file dipilih
            </span>
        </div>
        <div class="action-toolbar-right">
            <button class="btn btn-primary btn-lg" id="compressBtn" onclick="startCompression()"
                    disabled aria-label="Mulai kompres file terpilih">
                <i class="fa-solid fa-compress"></i>
                Kompres Sekarang
            </button>
        </div>
    </div>

    <!-- Skipped Files -->
    <div class="skipped-section" id="skippedSection">
        <button class="skipped-toggle" id="skippedToggle" onclick="toggleSkipped()"
                aria-expanded="false" aria-controls="skippedList">
            <i class="fa-solid fa-chevron-right" id="skippedChevron"></i>
            <i class="fa-solid fa-ban"></i>
            <span id="skippedToggleText">File Dilewati (≤10 MB)</span>
        </button>
        <div class="skipped-list" id="skippedList" role="list" aria-label="File yang dilewati">
        </div>
    </div>
</section>

<!-- ============================================
     SECTION 3: RESULTS
     ============================================ -->
<section class="results-section" id="resultsSection" aria-labelledby="resultsTitle">
    <div class="section-header">
        <h2 class="section-title" id="resultsTitle">
            <span class="section-title-icon emerald"><i class="fa-solid fa-chart-bar"></i></span>
            Hasil Kompresi
        </h2>
        <div style="display:flex; gap:8px;">
            <button class="btn btn-cyan" id="downloadZipBtn" onclick="downloadAllZip()"
                    aria-label="Unduh semua file terkompresi dalam format ZIP">
                <i class="fa-solid fa-file-zipper"></i> Download Semua (.zip)
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="result-summary-grid" id="resultSummaryGrid" role="list" aria-label="Ringkasan hasil kompresi">
        <!-- Filled by JS -->
    </div>
</section>
@endsection

@push('scripts')
<script>
// =========================================================
//  STATE
// =========================================================
let currentTab   = 'files';
let uploadedJobs = [];   // { id, name, size, size_human, status }
let skippedFiles = [];   // { name, size, size_human }
let completedJobs = [];  // CompressionJob objects from server

const ROUTES = {
    upload:         '{{ route("pdf.upload") }}',
    compress:       '{{ route("pdf.compress") }}',
    compressStream: '{{ route("pdf.compress.stream") }}',
    reset:          '{{ route("pdf.reset") }}',
    download:       '{{ url("/download") }}',
    downloadZip:    '{{ route("pdf.download.zip") }}',
};

// =========================================================
//  TAB SWITCHING
// =========================================================
function switchTab(tab) {
    currentTab = tab;
    document.getElementById('tabFiles').classList.toggle('active', tab === 'files');
    document.getElementById('tabFolder').classList.toggle('active', tab === 'folder');

    const folderHint  = document.getElementById('folderHint');
    const dropTitle   = document.getElementById('dropTitle');
    const dropSubtitle = document.getElementById('dropSubtitle');
    const dropIconFa  = document.getElementById('dropIconFa');

    if (tab === 'folder') {
        folderHint.style.display = 'flex';
        dropTitle.textContent    = 'Drag & Drop Folder PDF Anda di Sini';
        dropSubtitle.textContent = 'atau klik untuk memilih folder dari komputer Anda';
        dropIconFa.className     = 'fa-solid fa-folder-open';
    } else {
        folderHint.style.display = 'none';
        dropTitle.textContent    = 'Drag & Drop File PDF Anda di Sini';
        dropSubtitle.textContent = 'atau klik untuk memilih file dari komputer Anda';
        dropIconFa.className     = 'fa-solid fa-file-pdf';
    }
}

function triggerFileInput() {
    if (currentTab === 'folder') {
        document.getElementById('folderInput').click();
    } else {
        document.getElementById('fileInput').click();
    }
}

// =========================================================
//  DRAG & DROP
// =========================================================
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropZone').classList.add('dragging');
}
function handleDragLeave(e) {
    e.stopPropagation();
    document.getElementById('dropZone').classList.remove('dragging');
}
function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('dropZone').classList.remove('dragging');
    const items = e.dataTransfer.items;
    const files = e.dataTransfer.files;

    if (items && items.length > 0 && items[0].webkitGetAsEntry) {
        // Handle folder drop via FileSystem API
        processDroppedItems(items);
    } else {
        handleFileSelect(files);
    }
}

function processDroppedItems(items) {
    const filePromises = [];
    for (let i = 0; i < items.length; i++) {
        const entry = items[i].webkitGetAsEntry();
        if (entry) {
            filePromises.push(readEntry(entry));
        }
    }
    Promise.all(filePromises).then(results => {
        const allFiles = results.flat();
        const dt = new DataTransfer();
        allFiles.forEach(f => dt.items.add(f));
        handleFileSelect(dt.files);
    });
}

function readEntry(entry) {
    return new Promise((resolve) => {
        if (entry.isFile) {
            entry.file(f => resolve([f]));
        } else if (entry.isDirectory) {
            const reader = entry.createReader();
            const readAll = (allEntries) => {
                reader.readEntries(entries => {
                    if (entries.length === 0) {
                        Promise.all(allEntries.map(e => readEntry(e))).then(r => resolve(r.flat()));
                    } else {
                        readAll(allEntries.concat(Array.from(entries)));
                    }
                });
            };
            readAll([]);
        } else {
            resolve([]);
        }
    });
}

// =========================================================
//  FILE SELECT & UPLOAD
// =========================================================
async function handleFileSelect(fileList) {
    if (!fileList || fileList.length === 0) return;

    const overlay = document.getElementById('uploadOverlay');
    const overlayCount = document.getElementById('overlayFileCount');
    overlay.classList.add('show');
    overlayCount.textContent = `Memproses ${fileList.length} file...`;

    const formData = new FormData();
    let pdfCount = 0;

    for (let i = 0; i < fileList.length; i++) {
        const f = fileList[i];
        if (f.name.toLowerCase().endsWith('.pdf')) {
            formData.append('files[]', f);
            pdfCount++;
        }
    }

    if (pdfCount === 0) {
        overlay.classList.remove('show');
        Toast.show('Tidak ada file PDF yang ditemukan dalam pilihan Anda.', 'warning');
        return;
    }

    overlayCount.textContent = `Mengupload ${pdfCount} file PDF...`;

    try {
        const response = await fetch(ROUTES.upload, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Upload gagal.');
        }

        overlay.classList.remove('show');
        processUploadResult(data, fileList.length);
        document.getElementById('resetBtn').style.display = 'flex';

    } catch (err) {
        overlay.classList.remove('show');
        Toast.show('Error: ' + err.message, 'error', 6000);
        console.error(err);
    }
}

function processUploadResult(data, totalSelected) {
    uploadedJobs = uploadedJobs.concat(data.eligible || []);
    skippedFiles = skippedFiles.concat(data.skipped  || []);

    const totalPdf  = uploadedJobs.length + skippedFiles.length;
    const eligible  = uploadedJobs.length;
    const skipped   = skippedFiles.length;

    // Update stats
    document.getElementById('statTotal').textContent    = totalPdf;
    document.getElementById('statEligible').textContent = eligible;
    document.getElementById('statSkipped').textContent  = skipped;

    // Show scan summary
    document.getElementById('scanSummary').classList.add('show');

    // Render file table
    if (eligible > 0) {
        renderFileTable();
        document.getElementById('fileTableSection').classList.add('show');
        setWorkflowStep(3);
    }

    // Render skipped files
    if (skipped > 0) {
        renderSkippedFiles();
        document.getElementById('skippedSection').classList.add('show');
        document.getElementById('skippedToggleText').textContent =
            `${skipped} file dilewati (≤10 MB)`;
    }

    // Toast
    if (eligible > 0) {
        Toast.show(`${eligible} file PDF siap dikompresi!`, 'success');
    } else {
        Toast.show('Semua file PDF ≤10 MB — tidak ada yang perlu dikompresi.', 'info');
    }
}

// =========================================================
//  RENDER FILE TABLE
// =========================================================
function renderFileTable() {
    const tbody = document.getElementById('fileTableBody');
    tbody.innerHTML = '';

    uploadedJobs.forEach(job => {
        const row = createJobRow(job);
        tbody.appendChild(row);
    });

    updateSelectedCount();
}

function createJobRow(job) {
    const tr = document.createElement('tr');
    tr.id = `row-${job.id}`;
    tr.setAttribute('data-job-id', job.id);

    const statusHtml  = renderStatusBadge(job);
    const downloadHtml = job.download_url
        ? `<a href="${job.download_url}" class="btn btn-success btn-sm" target="_blank" aria-label="Unduh ${job.name}">
               <i class="fa-solid fa-download"></i> Unduh
           </a>`
        : '—';

    const ratioHtml   = renderRatioBadge(job);
    const compSizeHtml = job.compressed_size_human || '—';

    tr.innerHTML = `
        <td class="checkbox-cell">
            ${job.status === 'pending' ? `<input type="checkbox" class="job-checkbox" data-job-id="${job.id}"
                   aria-label="Pilih file ${job.name}"
                   onchange="updateSelectedCount()" checked>` : ''}
        </td>
        <td>
            <div class="file-name-cell">
                <div class="file-icon"><i class="fa-solid fa-file-pdf"></i></div>
                <span class="file-filename" title="${job.name}">${truncateName(job.name, 45)}</span>
            </div>
        </td>
        <td>
            <span class="file-size-badge">
                <i class="fa-solid fa-weight-hanging" style="font-size:10px;"></i>
                ${job.original_size_human || job.size_human}
            </span>
        </td>
        <td id="cell-compsize-${job.id}">${compSizeHtml}</td>
        <td id="cell-ratio-${job.id}">${ratioHtml}</td>
        <td id="cell-status-${job.id}">${statusHtml}</td>
        <td id="cell-action-${job.id}">${downloadHtml}</td>
    `;
    return tr;
}

function updateJobRow(job) {
    const row = document.getElementById(`row-${job.id}`);
    if (!row) return;

    document.getElementById(`cell-status-${job.id}`).innerHTML   = renderStatusBadge(job);
    document.getElementById(`cell-ratio-${job.id}`).innerHTML    = renderRatioBadge(job);
    document.getElementById(`cell-compsize-${job.id}`).innerHTML = job.compressed_size_human || '—';

    const actionCell = document.getElementById(`cell-action-${job.id}`);
    if (job.download_url) {
        actionCell.innerHTML = `
            <a href="${job.download_url}" class="btn btn-success btn-sm" target="_blank" aria-label="Unduh ${job.name}">
                <i class="fa-solid fa-download"></i> Unduh
            </a>`;
    } else if (job.status === 'failed') {
        actionCell.innerHTML = `<span style="color:#f43f5e; font-size:12px;" title="${job.error_message}">
            <i class="fa-solid fa-circle-xmark"></i> Gagal</span>`;
    }

    // Remove checkbox for non-pending
    const chkCell = row.querySelector('.checkbox-cell');
    if (job.status !== 'pending' && chkCell) {
        chkCell.innerHTML = '';
    }
}

function renderStatusBadge(job) {
    const icons = {
        pending:    'fa-clock',
        processing: 'fa-spinner fa-spin',
        completed:  'fa-circle-check',
        failed:     'fa-circle-xmark',
    };
    const labels = {
        pending:    'Menunggu',
        processing: 'Memproses...',
        completed:  'Selesai',
        failed:     'Gagal',
    };
    return `<span class="status-badge status-${job.status}">
        <i class="fa-solid ${icons[job.status] || 'fa-clock'}"></i>
        ${labels[job.status] || job.status}
    </span>`;
}

function renderRatioBadge(job) {
    if (!job.compression_ratio && job.compression_ratio !== 0) return '—';
    if (job.status !== 'completed') return '—';
    const ratio = job.compression_ratio;
    const cls   = ratio >= 30 ? 'good' : ratio > 0 ? 'fair' : 'none';
    const icon  = ratio > 0 ? 'fa-arrow-trend-down' : 'fa-minus';
    return `<span class="ratio-badge ${cls}">
        <i class="fa-solid ${icon}"></i>
        ${ratio > 0 ? '-' + ratio + '%' : 'No change'}
    </span>`;
}

// =========================================================
//  RENDER SKIPPED
// =========================================================
function renderSkippedFiles() {
    const list = document.getElementById('skippedList');
    list.innerHTML = '';
    skippedFiles.forEach(f => {
        const item = document.createElement('div');
        item.className = 'skipped-item';
        item.setAttribute('role', 'listitem');
        item.innerHTML = `
            <span style="display:flex; align-items:center; gap:8px;">
                <i class="fa-solid fa-file-pdf" style="color:rgba(245,158,11,0.5);"></i>
                ${truncateName(f.name, 50)}
            </span>
            <span style="font-size:12px; color:var(--text-muted);">${f.size_human}</span>
        `;
        list.appendChild(item);
    });
}

function toggleSkipped() {
    const list    = document.getElementById('skippedList');
    const chevron = document.getElementById('skippedChevron');
    const btn     = document.getElementById('skippedToggle');
    const isOpen  = list.classList.contains('show');
    list.classList.toggle('show', !isOpen);
    chevron.style.transform = isOpen ? '' : 'rotate(90deg)';
    btn.setAttribute('aria-expanded', !isOpen);
}

// =========================================================
//  CHECKBOX LOGIC
// =========================================================
function toggleAllCheckboxes(masterChk) {
    const checkboxes = document.querySelectorAll('.job-checkbox');
    checkboxes.forEach(c => c.checked = masterChk.checked);

    // Sync both master checkboxes
    document.getElementById('selectAllChk').checked  = masterChk.checked;
    document.getElementById('selectAllChk2').checked = masterChk.checked;

    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.job-checkbox');
    const checked    = document.querySelectorAll('.job-checkbox:checked');

    document.getElementById('selectedCount').textContent = `${checked.length} file dipilih`;

    const btn = document.getElementById('compressBtn');
    btn.disabled = checked.length === 0;

    // Sync master checkbox state
    const allChk  = document.getElementById('selectAllChk');
    const allChk2 = document.getElementById('selectAllChk2');
    if (checkboxes.length > 0) {
        allChk.indeterminate  = checked.length > 0 && checked.length < checkboxes.length;
        allChk2.indeterminate = allChk.indeterminate;
        allChk.checked  = checked.length === checkboxes.length;
        allChk2.checked = allChk.checked;
    }
}

function getSelectedJobIds() {
    return Array.from(document.querySelectorAll('.job-checkbox:checked'))
        .map(c => parseInt(c.dataset.jobId));
}

// =========================================================
//  COMPRESSION  — SSE Real-time Parallel
// =========================================================

let sseSource = null;
let compressStats = { success: 0, failed: 0, jobs: [] };

async function startCompression() {
    const selectedIds = getSelectedJobIds();
    if (selectedIds.length === 0) {
        Toast.show('Pilih minimal 1 file untuk dikompresi.', 'warning');
        return;
    }

    const quality = document.querySelector('input[name="quality"]:checked').value;
    const btn     = document.getElementById('compressBtn');
    btn.disabled  = true;
    btn.innerHTML = '<div class="spinner"></div> Memulai...';

    const progressEl = document.getElementById('compressProgress');
    progressEl.classList.add('show');
    setWorkflowStep(4);
    document.querySelectorAll('.job-checkbox').forEach(c => c.disabled = true);

    compressStats = { success: 0, failed: 0, jobs: [] };
    const total = selectedIds.length;

    // Mark selected rows as "queued" in UI immediately
    selectedIds.forEach(id => {
        const row = document.getElementById(`row-${id}`);
        if (row) {
            document.getElementById(`cell-status-${id}`).innerHTML =
                `<span class="status-badge status-processing">
                    <i class="fa-solid fa-spinner fa-spin"></i> Antre...
                </span>`;
            // Add per-row mini progress bar
            addRowProgressBar(id);
        }
    });

    // Try SSE first (real-time updates as each file finishes)
    const sseSupported = typeof EventSource !== 'undefined';

    if (sseSupported) {
        await compressWithSSE(selectedIds, quality, total);
    } else {
        // Fallback: regular POST
        await compressWithPost(selectedIds, quality);
    }
}

/**
 * 🚀 FAST PATH: SSE streaming — each file update arrives instantly as it finishes.
 * On the server, files are compressed in parallel (MAX_PARALLEL at a time),
 * and each finished file is pushed immediately via SSE.
 */
async function compressWithSSE(selectedIds, quality, total) {
    const btn        = document.getElementById('compressBtn');
    const progressEl = document.getElementById('compressProgress');

    const url = `${ROUTES.compressStream}?job_ids=${selectedIds.join(',')}&quality=${quality}`;

    return new Promise((resolve) => {
        sseSource = new EventSource(url);
        let done = 0;
        let startTime = Date.now();

        sseSource.addEventListener('start', (e) => {
            const d = JSON.parse(e.data);
            document.getElementById('progressStatus').textContent = d.message;
            btn.innerHTML = `<div class="spinner"></div> Memproses ${d.total} file paralel...`;
        });

        sseSource.addEventListener('progress', (e) => {
            const d   = JSON.parse(e.data);
            const job = d.job;
            done = d.done;

            // Real-time progress bar
            const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
            setProgressReal(d.percent, done, total, elapsed);

            // Update table row immediately
            const idx = uploadedJobs.findIndex(j => j.id === job.id);
            if (idx !== -1) uploadedJobs[idx] = { ...uploadedJobs[idx], ...job };
            updateJobRow(job);
            removeRowProgressBar(job.id);

            compressStats.jobs.push(job);
            if (job.status === 'completed') {
                compressStats.success++;
                completedJobs.push(job);
                // Flash green on row
                flashRow(job.id, 'success');
            } else {
                compressStats.failed++;
                flashRow(job.id, 'error');
            }
        });

        sseSource.addEventListener('done', (e) => {
            sseSource.close();
            sseSource = null;

            setProgressReal(100, total, total, ((Date.now() - startTime) / 1000).toFixed(1));

            setTimeout(() => {
                progressEl.classList.remove('show');
                showResults({
                    success_count: compressStats.success,
                    failed_count:  compressStats.failed,
                    jobs:          compressStats.jobs,
                });
                if (compressStats.success > 0) {
                    triggerConfetti();
                    Toast.show(`✅ ${compressStats.success} file selesai dikompresi!`, 'success', 6000);
                }
                if (compressStats.failed > 0) {
                    Toast.show(`❌ ${compressStats.failed} file gagal.`, 'error');
                }
            }, 600);

            resolve();
        });

        sseSource.onerror = () => {
            sseSource.close();
            sseSource = null;
            // SSE failed mid-way — fall back to POST for remaining
            Toast.show('Koneksi SSE terputus. Melanjutkan dengan mode standar...', 'warning');
            const remaining = selectedIds.filter(id =>
                !compressStats.jobs.find(j => j.id === id)
            );
            if (remaining.length > 0) {
                compressWithPost(remaining, quality).then(resolve);
            } else {
                resolve();
            }
        };
    });
}

/**
 * Fallback POST-based compression (no SSE).
 */
async function compressWithPost(selectedIds, quality) {
    const btn        = document.getElementById('compressBtn');
    const progressEl = document.getElementById('compressProgress');

    try {
        let pct = 0;
        const interval = setInterval(() => {
            pct = Math.min(pct + 1, 90);
            setProgressReal(pct, 0, selectedIds.length, '');
        }, 300);

        const response = await postJson(ROUTES.compress, {
            job_ids: selectedIds,
            quality: quality,
        });

        clearInterval(interval);
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Kompresi gagal.');

        setProgressReal(100, selectedIds.length, selectedIds.length, '');
        const results = data.results;

        results.jobs.forEach(job => {
            const idx = uploadedJobs.findIndex(j => j.id === job.id);
            if (idx !== -1) uploadedJobs[idx] = { ...uploadedJobs[idx], ...job };
            updateJobRow(job);
            removeRowProgressBar(job.id);
        });

        completedJobs = results.jobs.filter(j => j.status === 'completed');

        setTimeout(() => {
            progressEl.classList.remove('show');
            showResults(results);
            if (results.success_count > 0) {
                triggerConfetti();
                Toast.show(`${results.success_count} file berhasil dikompresi!`, 'success', 6000);
            }
        }, 600);

    } catch (err) {
        progressEl.classList.remove('show');
        btn.disabled  = false;
        btn.innerHTML = '<i class="fa-solid fa-compress"></i> Kompres Sekarang';
        Toast.show('Error: ' + err.message, 'error', 6000);
        selectedIds.forEach(id => removeRowProgressBar(id));
    }
}

// ---- Per-row mini progress bar ----
function addRowProgressBar(jobId) {
    const cell = document.getElementById(`cell-status-${jobId}`);
    if (!cell) return;
    const bar = document.createElement('div');
    bar.id = `row-bar-${jobId}`;
    bar.style.cssText = `
        height:3px; border-radius:99px; margin-top:6px;
        background:rgba(255,255,255,0.08); overflow:hidden;
    `;
    bar.innerHTML = `<div style="height:100%;width:0%;background:linear-gradient(90deg,#6c63ff,#06b6d4);
        border-radius:99px; animation:rowBarAnim 1.5s ease-in-out infinite;"></div>`;
    cell.appendChild(bar);

    // CSS animation
    if (!document.getElementById('row-bar-style')) {
        const s = document.createElement('style');
        s.id = 'row-bar-style';
        s.textContent = `@keyframes rowBarAnim{
            0%{width:5%;} 50%{width:80%;} 100%{width:5%;}
        }`;
        document.head.appendChild(s);
    }
}

function removeRowProgressBar(jobId) {
    const bar = document.getElementById(`row-bar-${jobId}`);
    if (bar) bar.remove();
}

function flashRow(jobId, type) {
    const row = document.getElementById(`row-${jobId}`);
    if (!row) return;
    const color = type === 'success'
        ? 'rgba(16,185,129,0.12)'
        : 'rgba(244,63,94,0.1)';
    row.style.transition = 'background 0.3s';
    row.style.background = color;
    setTimeout(() => { row.style.background = ''; }, 1500);
}

// ---- Global progress bar ----
function setProgressReal(pct, done, total, elapsed) {
    const fill     = document.getElementById('progressFill');
    const pctEl    = document.getElementById('progressPct');
    const statusEl = document.getElementById('progressStatus');

    fill.style.width      = pct + '%';
    pctEl.textContent     = Math.round(pct) + '%';

    const elapsedStr = elapsed ? ` · ${elapsed}s` : '';
    if (pct === 100) {
        statusEl.textContent = `✅ Semua selesai${elapsedStr}`;
    } else if (done > 0) {
        statusEl.textContent = `⚡ ${done}/${total} file selesai (paralel)${elapsedStr}`;
    } else {
        statusEl.textContent = 'Menginisialisasi engine kompresi paralel...';
    }
}

// =========================================================
//  SHOW RESULTS
// =========================================================
function showResults(results) {
    const section = document.getElementById('resultsSection');
    section.classList.add('show');
    section.scrollIntoView({ behavior: 'smooth', block: 'start' });

    const totalOriginal   = completedJobs.reduce((s, j) => s + (j.original_size || 0), 0);
    const totalCompressed = completedJobs.reduce((s, j) => s + (j.compressed_size || 0), 0);
    const saved           = totalOriginal - totalCompressed;
    const avgRatio        = completedJobs.length > 0
        ? (completedJobs.reduce((s, j) => s + (j.compression_ratio || 0), 0) / completedJobs.length).toFixed(1)
        : 0;

    const grid = document.getElementById('resultSummaryGrid');
    grid.innerHTML = `
        <div class="result-summary-card" role="listitem">
            <div class="result-summary-card-icon" style="background:rgba(108,99,255,0.15); color:#a78bfa;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="result-summary-card-value" style="color:#a78bfa;">${results.success_count}</div>
            <div class="result-summary-card-label">File Berhasil</div>
        </div>
        <div class="result-summary-card" role="listitem">
            <div class="result-summary-card-icon" style="background:rgba(16,185,129,0.12); color:#10b981;">
                <i class="fa-solid fa-hard-drive"></i>
            </div>
            <div class="result-summary-card-value" style="color:#10b981;">${formatBytes(saved)}</div>
            <div class="result-summary-card-label">Ruang Tersimpan</div>
        </div>
        <div class="result-summary-card" role="listitem">
            <div class="result-summary-card-icon" style="background:rgba(6,182,212,0.12); color:var(--accent-cyan);">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div class="result-summary-card-value" style="color:var(--accent-cyan);">${avgRatio}%</div>
            <div class="result-summary-card-label">Rata-rata Kompresi</div>
        </div>
        <div class="result-summary-card" role="listitem">
            <div class="result-summary-card-icon" style="background:rgba(244,63,94,0.12); color:#f43f5e;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="result-summary-card-value" style="color:#f43f5e;">${results.failed_count}</div>
            <div class="result-summary-card-label">File Gagal</div>
        </div>
    `;

    // Show/hide ZIP button based on completed count
    document.getElementById('downloadZipBtn').style.display =
        completedJobs.length > 1 ? 'flex' : 'none';
}

// =========================================================
//  DOWNLOAD ZIP
// =========================================================
async function downloadAllZip() {
    const completedIds = completedJobs.map(j => j.id);
    if (completedIds.length === 0) {
        Toast.show('Tidak ada file yang bisa diunduh.', 'warning');
        return;
    }

    const btn = document.getElementById('downloadZipBtn');
    btn.disabled  = true;
    btn.innerHTML = '<div class="spinner"></div> Membuat ZIP...';

    try {
        const response = await postJson(ROUTES.downloadZip, { job_ids: completedIds });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.message || 'Gagal membuat ZIP.');
        }

        // Trigger download via blob
        const blob = await response.blob();
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = `PressPDF_Compressed_${Date.now()}.zip`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        Toast.show('File ZIP berhasil diunduh!', 'success');

    } catch (err) {
        Toast.show('Error: ' + err.message, 'error');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fa-solid fa-file-zipper"></i> Download Semua (.zip)';
    }
}

// =========================================================
//  SESSION RESET
// =========================================================
async function resetSession() {
    if (!confirm('Reset semua file dan mulai dari awal?')) return;

    try {
        await postJson(ROUTES.reset, {});

        // Reset state
        uploadedJobs  = [];
        skippedFiles  = [];
        completedJobs = [];

        // Reset UI
        document.getElementById('scanSummary').classList.remove('show');
        document.getElementById('fileTableSection').classList.remove('show');
        document.getElementById('resultsSection').classList.remove('show');
        document.getElementById('skippedSection').classList.remove('show');
        document.getElementById('fileTableBody').innerHTML = `
            <tr><td colspan="7">
                <div class="empty-state">
                    <i class="fa-regular fa-folder-open"></i>
                    <p>Belum ada file. Upload PDF di atas.</p>
                </div>
            </td></tr>`;
        document.getElementById('resultSummaryGrid').innerHTML = '';
        document.getElementById('compressBtn').disabled = true;
        document.getElementById('resetBtn').style.display = 'none';
        document.getElementById('selectAllChk').checked  = false;
        document.getElementById('selectAllChk2').checked = false;
        document.getElementById('selectedCount').textContent = '0 file dipilih';
        document.getElementById('fileInput').value   = '';
        document.getElementById('folderInput').value = '';

        setWorkflowStep(1);
        Toast.show('Session direset. Siap upload file baru!', 'info');

    } catch (err) {
        Toast.show('Gagal reset: ' + err.message, 'error');
    }
}

// =========================================================
//  WORKFLOW STEPS
// =========================================================
function setWorkflowStep(active) {
    for (let i = 1; i <= 4; i++) {
        const el = document.getElementById(`step${i}`);
        el.classList.remove('active', 'completed');
        if (i < active)      el.classList.add('completed');
        else if (i === active) el.classList.add('active');
    }
}

// =========================================================
//  CONFETTI
// =========================================================
function triggerConfetti() {
    const colors = ['#6c63ff', '#a855f7', '#06b6d4', '#10b981', '#f59e0b', '#f43f5e'];
    for (let i = 0; i < 80; i++) {
        setTimeout(() => {
            const el = document.createElement('div');
            el.className = 'confetti-piece';
            el.style.cssText = `
                left: ${Math.random() * 100}vw;
                top: -10px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                width: ${4 + Math.random() * 8}px;
                height: ${4 + Math.random() * 8}px;
                border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
                animation-duration: ${2 + Math.random() * 2}s;
                animation-delay: ${Math.random() * 0.5}s;
            `;
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 3500);
        }, i * 20);
    }
}

// =========================================================
//  UTILITIES
// =========================================================
function truncateName(name, maxLen) {
    if (name.length <= maxLen) return name;
    const ext  = name.lastIndexOf('.');
    const base = ext !== -1 ? name.substring(0, ext) : name;
    const extStr = ext !== -1 ? name.substring(ext) : '';
    return base.substring(0, maxLen - 5 - extStr.length) + '...' + extStr;
}

// =========================================================
//  INIT
// =========================================================
document.addEventListener('DOMContentLoaded', () => {
    // Check for existing jobs in session
    fetch('{{ route("pdf.jobs") }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            if (data.jobs && data.jobs.length > 0) {
                const pending   = data.jobs.filter(j => j.status === 'pending');
                const completed = data.jobs.filter(j => j.status === 'completed');
                if (pending.length > 0 || completed.length > 0) {
                    uploadedJobs  = data.jobs;
                    completedJobs = completed;
                    renderFileTable();
                    document.getElementById('fileTableSection').classList.add('show');
                    document.getElementById('statEligible').textContent = data.jobs.length;
                    document.getElementById('statTotal').textContent    = data.jobs.length;
                    document.getElementById('scanSummary').classList.add('show');
                    document.getElementById('resetBtn').style.display = 'flex';
                    setWorkflowStep(completed.length > 0 ? 4 : 3);
                }
            }
        })
        .catch(() => {});
});
</script>
@endpush
