<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$newsLogoPath = 'images/news-logo.png';

$logoQuery = "SELECT news_logo FROM settings LIMIT 1";
$logoResult = mysqli_query($conn, $logoQuery);

if ($logoResult && mysqli_num_rows($logoResult) > 0) {
    $row = mysqli_fetch_assoc($logoResult);
    if (!empty($row['news_logo'])) {
        $newsLogoPath = $row['news_logo'];
    }
}

$currentDate = strtoupper(date('d F Y'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin News Editor | One World Aviators</title>

    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo+Black&family=Bebas+Neue&family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Noto+Sans+Sinhala:wght@400;500;700;800;900&family=Oswald:wght@400;500;600;700&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <style>
        :root {
            --primary-color: #00205b;
            --secondary-color: #00aaff;
            --accent-color: #ffcc00;
            --text-dark: #333333;
            --text-light: #f4f4f4;
            --bg-light: #f9f9f9;
            --white: #ffffff;

            --navy-dark: #06142e;
            --navy-mid: #0b1f4d;
            --panel-dark: #0f172a;
            --panel-soft: #16213e;
            --line-soft: rgba(255,255,255,0.12);
            --danger-red: #ff1f1f;
            --shadow-dark: rgba(0,0,0,0.45);
            --shadow-soft: rgba(0,0,0,0.20);
            --success: #10b981;
            --card-bg: rgba(255,255,255,0.05);
            --control-bg: rgba(8, 15, 35, 0.75);
            --control-border: rgba(255,255,255,0.10);
            --grid-color: rgba(255,255,255,0.10);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(0,170,255,0.18), transparent 28%),
                radial-gradient(circle at top right, rgba(255,204,0,0.10), transparent 25%),
                linear-gradient(135deg, #020817 0%, #071633 35%, #0b1f4d 100%);
            color: var(--text-light);
            min-height: 100vh;
        }

        .page-wrap {
            width: 100%;
            padding: 20px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            padding: 18px 22px;
            border-radius: 22px;
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.10);
            box-shadow: 0 14px 40px rgba(0,0,0,0.25);
            flex-wrap: wrap;
        }

        .topbar h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        .topbar p {
            margin: 6px 0 0;
            color: rgba(244,244,244,0.75);
            font-size: 14px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .admin-badge {
            background: linear-gradient(135deg, var(--accent-color), #ffde59);
            color: #1d1d1d;
            padding: 10px 16px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 13px;
            box-shadow: 0 10px 25px rgba(255,204,0,0.25);
        }

        .main-grid {
            display: grid;
            grid-template-columns: 430px minmax(0, 1fr);
            gap: 20px;
            align-items: start;
        }

        .controls-card,
        .preview-card {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 26px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.30);
            backdrop-filter: blur(12px);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--line-soft);
            background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.01));
        }

        .card-header h2 {
            margin: 0;
            font-size: 19px;
            font-weight: 900;
            letter-spacing: 0.4px;
        }

        .card-header p {
            margin: 6px 0 0;
            font-size: 13px;
            color: rgba(244,244,244,0.72);
        }

        .controls-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            max-height: calc(100vh - 150px);
            overflow-y: auto;
        }

        .controls-body::-webkit-scrollbar {
            width: 10px;
        }

        .controls-body::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.18);
            border-radius: 999px;
        }

        .control-section {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 16px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.3px;
            color: var(--secondary-color);
            margin-bottom: 12px;
        }

        .form-grid {
            display: grid;
            gap: 12px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 12px;
            font-weight: 800;
            color: rgba(255,255,255,0.92);
            letter-spacing: 0.2px;
        }

        input[type="text"],
        textarea,
        select,
        input[type="number"],
        input[type="color"] {
            width: 100%;
            border: 1px solid var(--control-border);
            background: var(--control-bg);
            color: #fff;
            padding: 12px 13px;
            border-radius: 14px;
            font-size: 14px;
            outline: none;
            transition: 0.25s ease;
        }

        input[type="range"] {
            width: 100%;
        }

        textarea {
            min-height: 92px;
            resize: vertical;
            font-family: 'Noto Sans Sinhala', 'Inter', sans-serif;
            line-height: 1.5;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus,
        input[type="number"]:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(0,170,255,0.14);
        }

        input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 14px;
            border: 1px dashed rgba(255,255,255,0.20);
            background: rgba(255,255,255,0.04);
            color: #fff;
        }

        .inline-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .inline-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .inline-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .switch-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            border: 1px solid rgba(255,255,255,0.09);
            background: rgba(255,255,255,0.04);
            padding: 12px 14px;
            border-radius: 14px;
        }

        .switch-wrap span {
            font-size: 13px;
            font-weight: 700;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 54px;
            height: 28px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider-switch {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: rgba(255,255,255,0.18);
            border-radius: 50px;
            transition: 0.3s;
        }

        .slider-switch:before {
            position: absolute;
            content: "";
            width: 22px;
            height: 22px;
            left: 3px;
            top: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.3s;
        }

        .switch input:checked + .slider-switch {
            background: linear-gradient(135deg, var(--secondary-color), #0077ff);
        }

        .switch input:checked + .slider-switch:before {
            transform: translateX(26px);
        }

        .mini-note {
            font-size: 11px;
            color: rgba(255,255,255,0.62);
            margin-top: 3px;
            line-height: 1.5;
        }

        .range-value {
            margin-top: 4px;
            font-size: 11px;
            color: var(--accent-color);
            font-weight: 800;
        }

        .btn-row,
        .btn-row-3 {
            display: grid;
            gap: 10px;
        }

        .btn-row { grid-template-columns: 1fr 1fr; }
        .btn-row-3 { grid-template-columns: 1fr 1fr 1fr; }

        .btn {
            border: none;
            cursor: pointer;
            padding: 13px 16px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 0.3px;
            transition: 0.25s ease;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), #0b78ff);
            color: white;
            box-shadow: 0 16px 35px rgba(0,170,255,0.25);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--accent-color), #ffdf70);
            color: #121212;
            box-shadow: 0 16px 35px rgba(255,204,0,0.22);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff5b5b, #d7263d);
            color: #fff;
        }

        .btn-dark {
            background: linear-gradient(135deg, #182848, #4b6cb7);
            color: white;
        }

        .btn-soft {
            background: rgba(255,255,255,0.08);
            color: white;
            border: 1px solid rgba(255,255,255,0.08);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .preview-card {
            padding: 16px;
            min-width: 0;
        }

        .preview-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .preview-top h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
        }

        .preview-top .size-badge {
            padding: 9px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            color: #fff;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.10);
        }

        .news-preview-wrap {
            width: 100%;
            overflow: auto;
            border-radius: 24px;
            background: rgba(0,0,0,0.24);
            padding: 14px;
        }

        .news-preview-wrap::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .news-preview-wrap::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.18);
            border-radius: 999px;
        }

        .poster-stage {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-width: max-content;
        }

        .news-poster {
            position: relative;
            width: 1080px;
            height: 1080px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            background: #111;
            box-shadow: 0 20px 55px rgba(0,0,0,0.40);
            transform-origin: top center;
            user-select: none;
        }

        .poster-main-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
            z-index: 0;
            transform: scale(1);
        }

        .poster-overlay {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to top, rgba(0,0,0,0.90) 0%, rgba(0,0,0,0.65) 18%, rgba(0,0,0,0.20) 44%, rgba(0,0,0,0.10) 100%);
            z-index: 1;
            pointer-events: none;
        }

        .poster-overlay-2 {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.06), transparent 22%),
                radial-gradient(circle at center bottom, rgba(0,32,91,0.16), transparent 50%);
            z-index: 1;
            pointer-events: none;
        }

        .poster-grid {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            display: none;
            background-image:
                linear-gradient(var(--grid-color) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-color) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .poster-safe-area {
            position: absolute;
            inset: 28px;
            border: 2px dashed rgba(255,255,255,0.10);
            z-index: 3;
            pointer-events: none;
            display: none;
        }

        .canvas-item {
            position: absolute;
            z-index: 5;
            cursor: move;
            touch-action: none;
        }

        .canvas-item.locked {
            cursor: default;
        }

        .canvas-item.selected {
            outline: 2px dashed rgba(0,170,255,0.95);
            outline-offset: 4px;
            box-shadow: 0 0 0 6px rgba(0,170,255,0.12);
            border-radius: 10px;
        }

        .resize-handle {
            position: absolute;
            width: 14px;
            height: 14px;
            right: -8px;
            bottom: -8px;
            background: #00aaff;
            border: 2px solid #fff;
            border-radius: 50%;
            display: none;
            z-index: 20;
            cursor: nwse-resize;
        }

        .canvas-item.selected .resize-handle {
            display: block;
        }

        .poster-date {
            top: 28px;
            right: 28px;
            color: #f5f5f5;
            font-family: 'Anton', sans-serif;
            font-size: 34px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.55);
            white-space: nowrap;
            transform-origin: center center;
        }

        .poster-sub-image-wrap {
            top: 34px;
            left: 34px;
            width: 250px;
            height: 170px;
            border-radius: 16px;
            overflow: hidden;
            display: none;
            background: rgba(0,0,0,0.20);
            box-shadow: 0 12px 26px rgba(0,0,0,0.35);
        }

        .poster-sub-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transform: scale(1);
            transform-origin: center center;
        }

        .poster-breaking {
            left: 46px;
            bottom: 286px;
            display: none;
            transform-origin: center center;
        }

        .poster-breaking-text {
            color: #ff1f1f;
            font-family: 'Anton', sans-serif;
            font-size: 72px;
            line-height: 0.95;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 5px 18px rgba(0,0,0,0.55);
            white-space: nowrap;
        }

        .poster-breaking-line {
            margin-top: 8px;
            width: 390px;
            height: 7px;
            border-radius: 999px;
            background: #ff1f1f;
            box-shadow: 0 0 15px rgba(255,31,31,0.28);
        }

        .poster-logo {
            left: 50%;
            top: 760px;
            transform: none;
            width: 420px;
            max-width: none;
            object-fit: contain;
            filter: drop-shadow(0 6px 18px rgba(0,0,0,0.45));
            transform-origin: center center;
        }

        .poster-headline {
            left: 50%;
            top: 860px;
            transform: none;
            width: 900px;
            min-height: 70px;
            text-align: center;
            font-family: 'Noto Sans Sinhala', 'Inter', sans-serif;
            font-weight: 900;
            color: #fff;
            text-shadow: 0 4px 18px rgba(0,0,0,0.70);
            line-height: 1.12;
            letter-spacing: 0.2px;
            word-break: break-word;
            white-space: pre-wrap;
            transform-origin: center center;
        }

        .poster-headline .text-segment {
            display: inline;
        }

        .poster-notes {
            left: 50%;
            top: 980px;
            transform: none;
            width: 860px;
            text-align: center;
            font-size: 24px;
            line-height: 1.4;
            color: rgba(255,255,255,0.92);
            font-weight: 600;
            display: none;
            text-shadow: 0 3px 12px rgba(0,0,0,0.52);
            white-space: pre-wrap;
            transform-origin: center center;
        }

        .preview-footer-note {
            margin-top: 12px;
            font-size: 12px;
            color: rgba(244,244,244,0.68);
            line-height: 1.5;
        }

        .active-tool-title {
            font-size: 13px;
            color: var(--accent-color);
            font-weight: 800;
        }

        .selection-info {
            font-size: 12px;
            color: rgba(255,255,255,0.72);
        }

        @media (max-width: 1400px) {
            .main-grid {
                grid-template-columns: 1fr;
            }

            .controls-body {
                max-height: none;
            }
        }

        @media (max-width: 900px) {
            .page-wrap { padding: 12px; }
            .inline-2, .inline-3, .inline-4, .btn-row, .btn-row-3 {
                grid-template-columns: 1fr;
            }
            .topbar {
                padding: 16px;
            }
            .topbar h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

<div class="page-wrap">
    <div class="topbar">
        <div>
            <h1>News Editing Studio</h1>
            <p>Drag, resize, recolor, style, and export premium aviation news posters.</p>
        </div>
        <div class="topbar-actions">
            <div class="admin-badge">ADMIN ACCESS ONLY</div>
            <a href="admin_dashboard.php?tab=news" class="btn btn-soft" style="text-decoration:none;">Back to Dashboard</a>
        </div>
    </div>

    <div class="main-grid">
        <!-- CONTROLS -->
        <div class="controls-card">
            <div class="card-header">
                <h2>Poster Controls</h2>
                <p>Mini Photoshop-style controls for your admins.</p>
            </div>

            <div class="controls-body">
                <div class="control-section">
                    <div class="section-title">Basic Content</div>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="newsDate">Date</label>
                            <input type="text" id="newsDate" value="<?php echo htmlspecialchars($currentDate); ?>" placeholder="29 MARCH 2026">
                        </div>

                        <div class="field-group">
                            <label for="headlineText">Headline</label>
                            <textarea id="headlineText" placeholder="Type headline here..."></textarea>
                            <div class="mini-note">Use highlight tool below to color selected words.</div>
                        </div>

                        <div class="field-group">
                            <label for="notesText">Special Notes</label>
                            <textarea id="notesText" placeholder="Type notes here..."></textarea>
                        </div>

                        <div class="field-group">
                            <label for="breakingTextInput">Breaking Label Text</label>
                            <input type="text" id="breakingTextInput" value="BREAKING NEWS">
                        </div>
                    </div>
                </div>

                <div class="control-section">
                    <div class="section-title">Uploads</div>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="mainImageInput">Main Image</label>
                            <input type="file" id="mainImageInput" accept="image/*">
                        </div>

                        <div class="field-group">
                            <label for="subImageInput">Sub Image</label>
                            <input type="file" id="subImageInput" accept="image/*">
                        </div>
                    </div>
                </div>

                <div class="control-section">
                    <div class="section-title">Canvas Options</div>
                    <div class="form-grid">
                        <div class="inline-2">
                            <div class="field-group">
                                <label for="posterSize">Poster Ratio</label>
                                <select id="posterSize">
                                    <option value="1080x1080" selected>Square 1080 x 1080</option>
                                    <option value="1080x1350">Portrait 1080 x 1350</option>
                                    <option value="1920x1080">Landscape 1920 x 1080</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label for="exportScale">Export Quality</label>
                                <select id="exportScale">
                                    <option value="2">High</option>
                                    <option value="3" selected>Very High</option>
                                    <option value="4">Ultra</option>
                                </select>
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="switch-wrap">
                                <span>Show Safe Area</span>
                                <label class="switch">
                                    <input type="checkbox" id="safeAreaToggle">
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                            <div class="switch-wrap">
                                <span>Show Grid</span>
                                <label class="switch">
                                    <input type="checkbox" id="gridToggle">
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="switch-wrap">
                                <span>Snap to Grid</span>
                                <label class="switch">
                                    <input type="checkbox" id="snapToggle">
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                            <div class="switch-wrap">
                                <span>Lock Selected Item</span>
                                <label class="switch">
                                    <input type="checkbox" id="lockSelectedToggle">
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="switch-wrap">
                                <span>Show Notes</span>
                                <label class="switch">
                                    <input type="checkbox" id="notesToggle" checked>
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                            <div class="switch-wrap">
                                <span>Show Breaking</span>
                                <label class="switch">
                                    <input type="checkbox" id="breakingToggle">
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="overlayStrength">Overlay Darkness</label>
                            <input type="range" id="overlayStrength" min="0" max="100" value="68">
                            <div class="range-value" id="overlayValue">68%</div>
                        </div>

                        <div class="field-group">
                            <label for="backgroundZoom">Background Zoom</label>
                            <input type="range" id="backgroundZoom" min="50" max="180" value="100">
                            <div class="range-value" id="backgroundZoomValue">100%</div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="backgroundPosX">Background Horizontal</label>
                                <input type="range" id="backgroundPosX" min="0" max="100" value="50">
                                <div class="range-value" id="backgroundPosXValue">50%</div>
                            </div>
                            <div class="field-group">
                                <label for="backgroundPosY">Background Vertical</label>
                                <input type="range" id="backgroundPosY" min="0" max="100" value="50">
                                <div class="range-value" id="backgroundPosYValue">50%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="control-section">
                    <div class="section-title">Selection</div>
                    <div class="form-grid">
                        <div class="active-tool-title" id="selectedElementName">Selected: Headline</div>
                        <div class="selection-info" id="selectedElementInfo">Click any item on poster to edit it.</div>

                        <div class="inline-2">
                            <button type="button" class="btn btn-soft" id="bringFrontBtn">Bring Front</button>
                            <button type="button" class="btn btn-soft" id="sendBackBtn">Send Back</button>
                        </div>

                        <div class="inline-3">
                            <button type="button" class="btn btn-soft align-btn" data-align="left">Align Left</button>
                            <button type="button" class="btn btn-soft align-btn" data-align="center">Center</button>
                            <button type="button" class="btn btn-soft align-btn" data-align="right">Align Right</button>
                        </div>

                        <div class="inline-3">
                            <button type="button" class="btn btn-soft" id="centerHorizBtn">Center X</button>
                            <button type="button" class="btn btn-soft" id="centerVertBtn">Center Y</button>
                            <button type="button" class="btn btn-soft" id="duplicateSubImagePosBtn">Reset Pos</button>
                        </div>

                        <div class="field-group">
                            <label for="itemOpacity">Opacity</label>
                            <input type="range" id="itemOpacity" min="10" max="100" value="100">
                            <div class="range-value" id="itemOpacityValue">100%</div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="itemX">X Position</label>
                                <input type="number" id="itemX" value="0">
                            </div>
                            <div class="field-group">
                                <label for="itemY">Y Position</label>
                                <input type="number" id="itemY" value="0">
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="itemWidth">Width</label>
                                <input type="number" id="itemWidth" value="300">
                            </div>
                            <div class="field-group">
                                <label for="itemHeight">Height / 0 for auto</label>
                                <input type="number" id="itemHeight" value="0">
                            </div>
                        </div>

                        <button type="button" class="btn btn-dark" id="applySizePositionBtn">Apply Size / Position</button>
                    </div>
                </div>

                <div class="control-section">
                    <div class="section-title">Text Styling</div>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="fontFamilySelect">Font Family</label>
                            <select id="fontFamilySelect">
                                <option value="'Noto Sans Sinhala', 'Inter', sans-serif">Noto Sans Sinhala</option>
                                <option value="'Anton', sans-serif">Anton</option>
                                <option value="'Bebas Neue', sans-serif">Bebas Neue</option>
                                <option value="'Archivo Black', sans-serif">Archivo Black</option>
                                <option value="'Montserrat', sans-serif">Montserrat</option>
                                <option value="'Poppins', sans-serif">Poppins</option>
                                <option value="'Oswald', sans-serif">Oswald</option>
                                <option value="'Inter', sans-serif">Inter</option>
                            </select>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="fontSizeRange">Font Size</label>
                                <input type="range" id="fontSizeRange" min="12" max="140" value="66">
                                <div class="range-value" id="fontSizeValue">66px</div>
                            </div>
                            <div class="field-group">
                                <label for="fontWeightSelect">Font Weight</label>
                                <select id="fontWeightSelect">
                                    <option value="400">400</option>
                                    <option value="500">500</option>
                                    <option value="600">600</option>
                                    <option value="700">700</option>
                                    <option value="800">800</option>
                                    <option value="900" selected>900</option>
                                </select>
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="lineHeightRange">Line Height</label>
                                <input type="range" id="lineHeightRange" min="80" max="220" value="112">
                                <div class="range-value" id="lineHeightValue">1.12</div>
                            </div>
                            <div class="field-group">
                                <label for="letterSpacingRange">Letter Spacing</label>
                                <input type="range" id="letterSpacingRange" min="-2" max="10" step="0.1" value="0.2">
                                <div class="range-value" id="letterSpacingValue">0.2px</div>
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="textColorPicker">Text Color</label>
                                <input type="color" id="textColorPicker" value="#ffffff">
                            </div>
                            <div class="field-group">
                                <label for="highlightColorPicker">Highlight Color</label>
                                <input type="color" id="highlightColorPicker" value="#00aaff">
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="highlightWordInput">Highlight Selected Words</label>
                            <input type="text" id="highlightWordInput" placeholder="Type word or phrase to highlight">
                            <div class="mini-note">Example: Emirates or ශ්‍රී ලංකා</div>
                        </div>

                        <div class="inline-2">
                            <button type="button" class="btn btn-soft" id="applyHighlightBtn">Apply Highlight</button>
                            <button type="button" class="btn btn-soft" id="clearHighlightBtn">Clear Highlight</button>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="strokeColorPicker">Stroke Color</label>
                                <input type="color" id="strokeColorPicker" value="#000000">
                            </div>
                            <div class="field-group">
                                <label for="strokeSizeRange">Stroke Size</label>
                                <input type="range" id="strokeSizeRange" min="0" max="12" value="0">
                                <div class="range-value" id="strokeSizeValue">0px</div>
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="shadowBlurRange">Shadow Blur</label>
                                <input type="range" id="shadowBlurRange" min="0" max="40" value="18">
                                <div class="range-value" id="shadowBlurValue">18px</div>
                            </div>
                            <div class="field-group">
                                <label for="shadowOpacityRange">Shadow Opacity</label>
                                <input type="range" id="shadowOpacityRange" min="0" max="100" value="70">
                                <div class="range-value" id="shadowOpacityValue">70%</div>
                            </div>
                        </div>

                        <div class="inline-3">
                            <button type="button" class="btn btn-soft align-btn" data-align="left">Text Left</button>
                            <button type="button" class="btn btn-soft align-btn" data-align="center">Text Center</button>
                            <button type="button" class="btn btn-soft align-btn" data-align="right">Text Right</button>
                        </div>

                        <div class="inline-2">
                            <div class="switch-wrap">
                                <span>Uppercase</span>
                                <label class="switch">
                                    <input type="checkbox" id="uppercaseToggle">
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                            <div class="switch-wrap">
                                <span>Italic</span>
                                <label class="switch">
                                    <input type="checkbox" id="italicToggle">
                                    <span class="slider-switch"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="control-section">
                    <div class="section-title">Sub Image Styling</div>
                    <div class="form-grid">
                        <div class="inline-2">
                            <div class="field-group">
                                <label for="subImageBorderSize">Border Size</label>
                                <input type="range" id="subImageBorderSize" min="0" max="20" value="4">
                                <div class="range-value" id="subImageBorderSizeValue">4px</div>
                            </div>
                            <div class="field-group">
                                <label for="subImageRadius">Corner Radius</label>
                                <input type="range" id="subImageRadius" min="0" max="40" value="16">
                                <div class="range-value" id="subImageRadiusValue">16px</div>
                            </div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="subImageBorderColor">Border Color</label>
                                <input type="color" id="subImageBorderColor" value="#ffffff">
                            </div>
                            <div class="field-group">
                                <label for="subImageZoom">Sub Image Zoom</label>
                                <input type="range" id="subImageZoom" min="50" max="200" value="100">
                                <div class="range-value" id="subImageZoomValue">100%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="control-section">
                    <div class="section-title">Logo Styling</div>
                    <div class="form-grid">
                        <div class="field-group">
                            <label for="logoWidth">Logo Width</label>
                            <input type="range" id="logoWidth" min="80" max="900" value="420">
                            <div class="range-value" id="logoWidthValue">420px</div>
                        </div>

                        <div class="inline-2">
                            <div class="field-group">
                                <label for="logoOpacity">Logo Opacity</label>
                                <input type="range" id="logoOpacity" min="10" max="100" value="100">
                                <div class="range-value" id="logoOpacityValue">100%</div>
                            </div>
                            <div class="field-group">
                                <label for="logoShadowBlur">Logo Shadow</label>
                                <input type="range" id="logoShadowBlur" min="0" max="40" value="18">
                                <div class="range-value" id="logoShadowBlurValue">18px</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="control-section">
                    <div class="section-title">Actions</div>
                    <div class="btn-row-3">
                        <button type="button" class="btn btn-primary" id="downloadPngBtn">Download PNG</button>
                        <button type="button" class="btn btn-secondary" id="downloadJpgBtn">Download JPG</button>
                        <button type="button" class="btn btn-danger" id="resetBtn">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- PREVIEW -->
        <div class="preview-card">
            <div class="preview-top">
                <h3>Live Preview</h3>
                <div class="size-badge" id="previewSizeBadge">1080 × 1080</div>
            </div>

            <div class="news-preview-wrap">
                <div class="poster-stage">
                    <div id="newsPoster" class="news-poster">
                        <img id="mainPreviewImage" class="poster-main-image" src="https://via.placeholder.com/1080x1080/0b1f4d/ffffff?text=Upload+News+Image" alt="Main News Image">

                        <div id="posterOverlay" class="poster-overlay"></div>
                        <div class="poster-overlay-2"></div>
                        <div id="posterGrid" class="poster-grid"></div>
                        <div id="posterSafeArea" class="poster-safe-area"></div>

                        <div id="posterDate" class="canvas-item poster-date" data-name="Date">
                            <?php echo htmlspecialchars($currentDate); ?>
                            <span class="resize-handle"></span>
                        </div>

                        <div id="subImageWrap" class="canvas-item poster-sub-image-wrap" data-name="Sub Image">
                            <img id="subPreviewImage" class="poster-sub-image" src="" alt="Sub Image">
                            <span class="resize-handle"></span>
                        </div>

                        <div id="breakingBlock" class="canvas-item poster-breaking" data-name="Breaking Label">
                            <div id="breakingText" class="poster-breaking-text">BREAKING NEWS</div>
                            <div id="breakingLine" class="poster-breaking-line"></div>
                            <span class="resize-handle"></span>
                        </div>

                        <img id="posterLogo" class="canvas-item poster-logo" data-name="Logo" src="<?php echo htmlspecialchars($newsLogoPath); ?>" alt="News Logo">

                        <div id="posterHeadline" class="canvas-item poster-headline selected" data-name="Headline"></div>

                        <div id="posterNotes" class="canvas-item poster-notes" data-name="Special Notes"></div>
                    </div>
                </div>
            </div>

            <div class="preview-footer-note">
                Click any poster item to select it. Drag to move. Use bottom-right blue dot to resize. PNG and JPG export supported.
            </div>
        </div>
    </div>
</div>

<script>
    const newsDate = document.getElementById('newsDate');
    const headlineText = document.getElementById('headlineText');
    const notesText = document.getElementById('notesText');
    const breakingTextInput = document.getElementById('breakingTextInput');

    const mainImageInput = document.getElementById('mainImageInput');
    const subImageInput = document.getElementById('subImageInput');

    const safeAreaToggle = document.getElementById('safeAreaToggle');
    const gridToggle = document.getElementById('gridToggle');
    const snapToggle = document.getElementById('snapToggle');
    const lockSelectedToggle = document.getElementById('lockSelectedToggle');
    const notesToggle = document.getElementById('notesToggle');
    const breakingToggle = document.getElementById('breakingToggle');

    const overlayStrength = document.getElementById('overlayStrength');
    const overlayValue = document.getElementById('overlayValue');

    const backgroundZoom = document.getElementById('backgroundZoom');
    const backgroundZoomValue = document.getElementById('backgroundZoomValue');
    const backgroundPosX = document.getElementById('backgroundPosX');
    const backgroundPosXValue = document.getElementById('backgroundPosXValue');
    const backgroundPosY = document.getElementById('backgroundPosY');
    const backgroundPosYValue = document.getElementById('backgroundPosYValue');

    const posterSize = document.getElementById('posterSize');
    const exportScale = document.getElementById('exportScale');
    const previewSizeBadge = document.getElementById('previewSizeBadge');

    const fontFamilySelect = document.getElementById('fontFamilySelect');
    const fontSizeRange = document.getElementById('fontSizeRange');
    const fontSizeValue = document.getElementById('fontSizeValue');
    const fontWeightSelect = document.getElementById('fontWeightSelect');
    const lineHeightRange = document.getElementById('lineHeightRange');
    const lineHeightValue = document.getElementById('lineHeightValue');
    const letterSpacingRange = document.getElementById('letterSpacingRange');
    const letterSpacingValue = document.getElementById('letterSpacingValue');
    const textColorPicker = document.getElementById('textColorPicker');
    const highlightColorPicker = document.getElementById('highlightColorPicker');
    const highlightWordInput = document.getElementById('highlightWordInput');
    const applyHighlightBtn = document.getElementById('applyHighlightBtn');
    const clearHighlightBtn = document.getElementById('clearHighlightBtn');
    const strokeColorPicker = document.getElementById('strokeColorPicker');
    const strokeSizeRange = document.getElementById('strokeSizeRange');
    const strokeSizeValue = document.getElementById('strokeSizeValue');
    const shadowBlurRange = document.getElementById('shadowBlurRange');
    const shadowBlurValue = document.getElementById('shadowBlurValue');
    const shadowOpacityRange = document.getElementById('shadowOpacityRange');
    const shadowOpacityValue = document.getElementById('shadowOpacityValue');
    const uppercaseToggle = document.getElementById('uppercaseToggle');
    const italicToggle = document.getElementById('italicToggle');

    const selectedElementName = document.getElementById('selectedElementName');
    const selectedElementInfo = document.getElementById('selectedElementInfo');
    const bringFrontBtn = document.getElementById('bringFrontBtn');
    const sendBackBtn = document.getElementById('sendBackBtn');
    const centerHorizBtn = document.getElementById('centerHorizBtn');
    const centerVertBtn = document.getElementById('centerVertBtn');
    const duplicateSubImagePosBtn = document.getElementById('duplicateSubImagePosBtn');
    const itemOpacity = document.getElementById('itemOpacity');
    const itemOpacityValue = document.getElementById('itemOpacityValue');
    const itemX = document.getElementById('itemX');
    const itemY = document.getElementById('itemY');
    const itemWidth = document.getElementById('itemWidth');
    const itemHeight = document.getElementById('itemHeight');
    const applySizePositionBtn = document.getElementById('applySizePositionBtn');

    const subImageBorderSize = document.getElementById('subImageBorderSize');
    const subImageBorderSizeValue = document.getElementById('subImageBorderSizeValue');
    const subImageRadius = document.getElementById('subImageRadius');
    const subImageRadiusValue = document.getElementById('subImageRadiusValue');
    const subImageBorderColor = document.getElementById('subImageBorderColor');
    const subImageZoom = document.getElementById('subImageZoom');
    const subImageZoomValue = document.getElementById('subImageZoomValue');

    const logoWidth = document.getElementById('logoWidth');
    const logoWidthValue = document.getElementById('logoWidthValue');
    const logoOpacity = document.getElementById('logoOpacity');
    const logoOpacityValue = document.getElementById('logoOpacityValue');
    const logoShadowBlur = document.getElementById('logoShadowBlur');
    const logoShadowBlurValue = document.getElementById('logoShadowBlurValue');

    const downloadPngBtn = document.getElementById('downloadPngBtn');
    const downloadJpgBtn = document.getElementById('downloadJpgBtn');
    const resetBtn = document.getElementById('resetBtn');

    const poster = document.getElementById('newsPoster');
    const mainPreviewImage = document.getElementById('mainPreviewImage');
    const posterOverlay = document.getElementById('posterOverlay');
    const posterGrid = document.getElementById('posterGrid');
    const posterSafeArea = document.getElementById('posterSafeArea');

    const posterDate = document.getElementById('posterDate');
    const subImageWrap = document.getElementById('subImageWrap');
    const subPreviewImage = document.getElementById('subPreviewImage');
    const breakingBlock = document.getElementById('breakingBlock');
    const breakingText = document.getElementById('breakingText');
    const breakingLine = document.getElementById('breakingLine');
    const posterLogo = document.getElementById('posterLogo');
    const posterHeadline = document.getElementById('posterHeadline');
    const posterNotes = document.getElementById('posterNotes');

    const alignButtons = document.querySelectorAll('.align-btn');
    const canvasItems = () => Array.from(document.querySelectorAll('.canvas-item'));

    let selectedEl = posterHeadline;
    let posterWidth = 1080;
    let posterHeight = 1080;
    let zCounter = 20;
    let highlightMap = {};
    let currentDrag = null;
    let currentResize = null;
    const GRID_SIZE = 20;

    function readImage(input, target) {
        const file = input.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            target.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function escapeHtml(str) {
        return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    function rgbaFromHex(hex, opacityPercent) {
        const clean = hex.replace('#','');
        const bigint = parseInt(clean, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${opacityPercent/100})`;
    }

    function getBaseTransform(el) {
    return '';
    }

    function applyOpacity(el, opacity) {
        el.style.opacity = opacity / 100;
    }

    function setSelected(el) {
        canvasItems().forEach(item => item.classList.remove('selected'));
        selectedEl = el;
        if (selectedEl) {
            selectedEl.classList.add('selected');
            selectedElementName.textContent = 'Selected: ' + (selectedEl.dataset.name || selectedEl.id);
            selectedElementInfo.textContent = `X: ${parseInt(selectedEl.style.left || selectedEl.offsetLeft)} | Y: ${parseInt(selectedEl.style.top || selectedEl.offsetTop)} | Z: ${selectedEl.style.zIndex || getComputedStyle(selectedEl).zIndex}`;
            lockSelectedToggle.checked = selectedEl.classList.contains('locked');
            syncControlsFromSelection();
        }
    }

    function clamp(value, min, max) {
        return Math.max(min, Math.min(max, value));
    }

    function snap(value) {
        return snapToggle.checked ? Math.round(value / GRID_SIZE) * GRID_SIZE : value;
    }

    function makeSelectableAndMovable(el) {
        el.addEventListener('pointerdown', function(e) {
            if (e.target.classList.contains('resize-handle')) return;
            setSelected(el);

            if (el.classList.contains('locked')) return;

            currentDrag = {
                el: el,
                startX: e.clientX,
                startY: e.clientY,
                origLeft: parseFloat(el.style.left || el.offsetLeft),
                origTop: parseFloat(el.style.top || el.offsetTop)
            };
            el.setPointerCapture(e.pointerId);
        });

        const handle = el.querySelector('.resize-handle');
        if (handle) {
            handle.addEventListener('pointerdown', function(e) {
                e.stopPropagation();
                setSelected(el);
                if (el.classList.contains('locked')) return;

                currentResize = {
                    el: el,
                    startX: e.clientX,
                    startY: e.clientY,
                    origWidth: el.offsetWidth,
                    origHeight: el.offsetHeight
                };
                handle.setPointerCapture(e.pointerId);
            });
        }
    }

    function moveSelectedTo(left, top) {
        if (!selectedEl) return;
        const maxLeft = posterWidth - selectedEl.offsetWidth;
        const maxTop = posterHeight - selectedEl.offsetHeight;
        const finalLeft = clamp(snap(left), 0, Math.max(0, maxLeft));
        const finalTop = clamp(snap(top), 0, Math.max(0, maxTop));

        selectedEl.style.left = finalLeft + 'px';
        selectedEl.style.top = finalTop + 'px';
        updateSelectionInfo();
    }

    function resizeElement(el, width, height) {
        const minW = 40;
        const minH = 20;
        width = clamp(width, minW, posterWidth);
        if (height !== null) height = clamp(height, minH, posterHeight);

        if (el === posterLogo) {
            el.style.width = width + 'px';
        } else if (el === posterHeadline || el === posterNotes || el === posterDate || el === breakingBlock) {
            el.style.width = width + 'px';
            if (height && el !== posterDate) el.style.minHeight = height + 'px';
        } else if (el === subImageWrap) {
            el.style.width = width + 'px';
            if (height) el.style.height = height + 'px';
        } else {
            el.style.width = width + 'px';
            if (height) el.style.height = height + 'px';
        }
        updateSelectionInfo();
    }

    function updateSelectionInfo() {
        if (!selectedEl) return;
        selectedElementInfo.textContent = `X: ${parseInt(selectedEl.style.left || selectedEl.offsetLeft)} | Y: ${parseInt(selectedEl.style.top || selectedEl.offsetTop)} | W: ${selectedEl.offsetWidth} | H: ${selectedEl.offsetHeight}`;
        itemX.value = parseInt(selectedEl.style.left || selectedEl.offsetLeft);
        itemY.value = parseInt(selectedEl.style.top || selectedEl.offsetTop);
        itemWidth.value = selectedEl.offsetWidth;
        itemHeight.value = (selectedEl === posterLogo || selectedEl === posterDate) ? 0 : selectedEl.offsetHeight;
        itemOpacity.value = Math.round(parseFloat(getComputedStyle(selectedEl).opacity) * 100);
        itemOpacityValue.textContent = itemOpacity.value + '%';
    }

    function applyTextStyles(el) {
        if (!el) return;
        const shadowColor = rgbaFromHex('#000000', parseInt(shadowOpacityRange.value, 10));
        const shadowBlur = parseInt(shadowBlurRange.value, 10);
        const strokeSize = parseInt(strokeSizeRange.value, 10);
        const strokeColor = strokeColorPicker.value;

        el.style.fontFamily = fontFamilySelect.value;
        el.style.fontSize = fontSizeRange.value + 'px';
        el.style.fontWeight = fontWeightSelect.value;
        el.style.lineHeight = (lineHeightRange.value / 100).toString();
        el.style.letterSpacing = letterSpacingRange.value + 'px';
        el.style.color = textColorPicker.value;
        el.style.textTransform = uppercaseToggle.checked ? 'uppercase' : 'none';
        el.style.fontStyle = italicToggle.checked ? 'italic' : 'normal';
        el.style.textShadow = `0 4px ${shadowBlur}px ${shadowColor}`;

        if (strokeSize > 0) {
            el.style.webkitTextStroke = `${strokeSize}px ${strokeColor}`;
        } else {
            el.style.webkitTextStroke = '0px transparent';
        }

        if (el === posterHeadline) renderHeadline();
    }

    function renderHeadline() {
        const text = headlineText.value || 'Type your news headline here...';
        let html = escapeHtml(text);

        Object.keys(highlightMap).forEach(key => {
            if (!key.trim()) return;
            const color = highlightMap[key];
            const escapedKey = key.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp(`(${escapedKey})`, 'gi');
            html = html.replace(regex, `<span class="text-segment" style="color:${color};">$1</span>`);
        });

        posterHeadline.innerHTML = html;
        const baseColor = textColorPicker.value;
        posterHeadline.querySelectorAll('.text-segment').forEach(seg => {
            if (!seg.style.color) seg.style.color = baseColor;
        });
    }

    function updateDate() {
        posterDate.textContent = newsDate.value.trim() || 'DATE';
        if (!posterDate.querySelector('.resize-handle')) {
            const handle = document.createElement('span');
            handle.className = 'resize-handle';
            posterDate.appendChild(handle);
            makeSelectableAndMovable(posterDate);
        }
    }

    function updateNotes() {
        const note = notesText.value.trim();
        if (notesToggle.checked && note !== '') {
            posterNotes.style.display = 'block';
            posterNotes.textContent = note;
        } else {
            posterNotes.style.display = 'none';
            posterNotes.textContent = '';
        }
    }

    function updateBreaking() {
        breakingBlock.style.display = breakingToggle.checked ? 'block' : 'none';
        breakingText.textContent = breakingTextInput.value.trim() || 'BREAKING NEWS';
    }

    function updateOverlay() {
        const val = parseInt(overlayStrength.value, 10);
        overlayValue.textContent = val + '%';
        const alphaBottom = Math.min(0.96, val / 100);
        const alphaMid = Math.min(0.85, Math.max(0.1, (val - 10) / 100));
        const alphaUpper = Math.max(0.05, (val - 45) / 100);

        posterOverlay.style.background =
            `linear-gradient(to top,
                rgba(0,0,0,${alphaBottom}) 0%,
                rgba(0,0,0,${alphaMid}) 18%,
                rgba(0,0,0,${alphaUpper}) 44%,
                rgba(0,0,0,0.08) 100%)`;
    }

    function updateBackgroundTransform() {
        mainPreviewImage.style.transform = `scale(${backgroundZoom.value / 100})`;
        mainPreviewImage.style.objectPosition = `${backgroundPosX.value}% ${backgroundPosY.value}%`;
        backgroundZoomValue.textContent = backgroundZoom.value + '%';
        backgroundPosXValue.textContent = backgroundPosX.value + '%';
        backgroundPosYValue.textContent = backgroundPosY.value + '%';
    }

    function updateSubImageStyles() {
        subImageWrap.style.border = `${subImageBorderSize.value}px solid ${subImageBorderColor.value}`;
        subImageWrap.style.borderRadius = subImageRadius.value + 'px';
        subPreviewImage.style.transform = `scale(${subImageZoom.value / 100})`;
        subImageBorderSizeValue.textContent = subImageBorderSize.value + 'px';
        subImageRadiusValue.textContent = subImageRadius.value + 'px';
        subImageZoomValue.textContent = subImageZoom.value + '%';
    }

    function updateLogoStyles() {
        posterLogo.style.width = logoWidth.value + 'px';
        posterLogo.style.opacity = logoOpacity.value / 100;
        posterLogo.style.filter = `drop-shadow(0 6px ${logoShadowBlur.value}px rgba(0,0,0,0.45))`;
        logoWidthValue.textContent = logoWidth.value + 'px';
        logoOpacityValue.textContent = logoOpacity.value + '%';
        logoShadowBlurValue.textContent = logoShadowBlur.value + 'px';
    }

    function updateCanvasHelpers() {
        posterSafeArea.style.display = safeAreaToggle.checked ? 'block' : 'none';
        posterGrid.style.display = gridToggle.checked ? 'block' : 'none';
    }

    function updatePosterSize() {
        const value = posterSize.value;

        if (value === '1080x1080') {
            posterWidth = 1080;
            posterHeight = 1080;
            previewSizeBadge.textContent = '1080 × 1080';

            poster.style.width = '1080px';
            poster.style.height = '1080px';

            posterDate.style.left = '810px';
            posterDate.style.top = '28px';

            subImageWrap.style.left = '34px';
            subImageWrap.style.top = '34px';
            subImageWrap.style.width = '250px';
            subImageWrap.style.height = '170px';

            breakingBlock.style.left = '46px';
            breakingBlock.style.top = '700px';

            posterLogo.style.left = '330px';
            posterLogo.style.top = '760px';

            posterHeadline.style.left = '90px';
            posterHeadline.style.top = '860px';
            posterHeadline.style.width = '900px';

            posterNotes.style.left = '110px';
            posterNotes.style.top = '980px';
            posterNotes.style.width = '860px';
        }
        else if (value === '1080x1350') {
            posterWidth = 1080;
            posterHeight = 1350;
            previewSizeBadge.textContent = '1080 × 1350';

            poster.style.width = '1080px';
            poster.style.height = '1350px';

            posterDate.style.left = '810px';
            posterDate.style.top = '32px';

            subImageWrap.style.left = '34px';
            subImageWrap.style.top = '34px';
            subImageWrap.style.width = '270px';
            subImageWrap.style.height = '190px';

            breakingBlock.style.left = '46px';
            breakingBlock.style.top = '930px';

            posterLogo.style.left = '330px';
            posterLogo.style.top = '1010px';

            posterHeadline.style.left = '75px';
            posterHeadline.style.top = '1120px';
            posterHeadline.style.width = '930px';

            posterNotes.style.left = '90px';
            posterNotes.style.top = '1245px';
            posterNotes.style.width = '900px';
        }
        else if (value === '1920x1080') {
            posterWidth = 1920;
            posterHeight = 1080;
            previewSizeBadge.textContent = '1920 × 1080';

            poster.style.width = '1920px';
            poster.style.height = '1080px';

            posterDate.style.left = '1620px';
            posterDate.style.top = '30px';

            subImageWrap.style.left = '40px';
            subImageWrap.style.top = '34px';
            subImageWrap.style.width = '320px';
            subImageWrap.style.height = '200px';

            breakingBlock.style.left = '62px';
            breakingBlock.style.top = '690px';

            posterLogo.style.left = '660px';
            posterLogo.style.top = '740px';

            posterHeadline.style.left = '260px';
            posterHeadline.style.top = '840px';
            posterHeadline.style.width = '1400px';

            posterNotes.style.left = '235px';
            posterNotes.style.top = '965px';
            posterNotes.style.width = '1450px';
        }

        setSelected(selectedEl || posterHeadline);
        updateSelectionInfo();
        updateLogoStyles();
    }

    function syncControlsFromSelection() {
        if (!selectedEl) return;

        const style = getComputedStyle(selectedEl);

        if ([posterHeadline, posterDate, posterNotes, breakingBlock].includes(selectedEl)) {
            fontFamilySelect.value = style.fontFamily.includes('Anton') ? "'Anton', sans-serif"
                : style.fontFamily.includes('Bebas Neue') ? "'Bebas Neue', sans-serif"
                : style.fontFamily.includes('Archivo Black') ? "'Archivo Black', sans-serif"
                : style.fontFamily.includes('Montserrat') ? "'Montserrat', sans-serif"
                : style.fontFamily.includes('Poppins') ? "'Poppins', sans-serif"
                : style.fontFamily.includes('Oswald') ? "'Oswald', sans-serif"
                : style.fontFamily.includes('Inter') ? "'Inter', sans-serif"
                : "'Noto Sans Sinhala', 'Inter', sans-serif";

            const currentSize = parseInt(style.fontSize) || 66;
            fontSizeRange.value = currentSize;
            fontSizeValue.textContent = currentSize + 'px';

            fontWeightSelect.value = style.fontWeight || '900';

            const lh = parseFloat(style.lineHeight) / parseFloat(style.fontSize);
            if (!isNaN(lh)) {
                lineHeightRange.value = Math.round(lh * 100);
                lineHeightValue.textContent = lh.toFixed(2);
            }

            const ls = parseFloat(style.letterSpacing) || 0;
            letterSpacingRange.value = ls;
            letterSpacingValue.textContent = ls + 'px';

            itemOpacity.value = Math.round(parseFloat(style.opacity || 1) * 100);
            itemOpacityValue.textContent = itemOpacity.value + '%';
        }

        updateSelectionInfo();
    }

    function exportPoster(format = 'png') {
        const oldSafe = posterSafeArea.style.display;
        const oldGrid = posterGrid.style.display;
        const selectedItems = canvasItems();

        posterSafeArea.style.display = 'none';
        posterGrid.style.display = 'none';
        selectedItems.forEach(i => i.classList.remove('selected'));

        const scaleValue = parseInt(exportScale.value, 10);

        html2canvas(poster, {
            useCORS: true,
            allowTaint: true,
            backgroundColor: format === 'jpg' ? '#000000' : null,
            scale: scaleValue
        }).then(canvas => {
            const link = document.createElement('a');
            if (format === 'jpg') {
                link.download = 'one-world-aviators-news.jpg';
                link.href = canvas.toDataURL('image/jpeg', 1.0);
            } else {
                link.download = 'one-world-aviators-news.png';
                link.href = canvas.toDataURL('image/png', 1.0);
            }
            link.click();
        }).catch(err => {
            alert('Failed to export image.');
            console.error(err);
        }).finally(() => {
            posterSafeArea.style.display = oldSafe;
            posterGrid.style.display = oldGrid;
            if (selectedEl) selectedEl.classList.add('selected');
        });
    }

    function resetEditor() {
        newsDate.value = "<?php echo htmlspecialchars($currentDate); ?>";
        headlineText.value = '';
        notesText.value = '';
        breakingTextInput.value = 'BREAKING NEWS';

        mainImageInput.value = '';
        subImageInput.value = '';

        safeAreaToggle.checked = false;
        gridToggle.checked = false;
        snapToggle.checked = false;
        lockSelectedToggle.checked = false;
        notesToggle.checked = true;
        breakingToggle.checked = false;

        overlayStrength.value = 68;
        backgroundZoom.value = 100;
        backgroundPosX.value = 50;
        backgroundPosY.value = 50;

        posterSize.value = '1080x1080';
        exportScale.value = '3';

        fontFamilySelect.value = "'Noto Sans Sinhala', 'Inter', sans-serif";
        fontSizeRange.value = 66;
        fontWeightSelect.value = 900;
        lineHeightRange.value = 112;
        letterSpacingRange.value = 0.2;
        textColorPicker.value = '#ffffff';
        highlightColorPicker.value = '#00aaff';
        highlightWordInput.value = '';
        highlightMap = {};
        strokeColorPicker.value = '#000000';
        strokeSizeRange.value = 0;
        shadowBlurRange.value = 18;
        shadowOpacityRange.value = 70;
        uppercaseToggle.checked = false;
        italicToggle.checked = false;

        subImageBorderSize.value = 4;
        subImageRadius.value = 16;
        subImageBorderColor.value = '#ffffff';
        subImageZoom.value = 100;

        logoWidth.value = 420;
        logoOpacity.value = 100;
        logoShadowBlur.value = 18;

        mainPreviewImage.src = 'https://via.placeholder.com/1080x1080/0b1f4d/ffffff?text=Upload+News+Image';
        subPreviewImage.src = '';
        subImageWrap.style.display = 'none';

        updatePosterSize();
        updateDate();
        renderHeadline();
        updateNotes();
        updateBreaking();
        updateOverlay();
        updateBackgroundTransform();
        updateSubImageStyles();
        updateLogoStyles();
        updateCanvasHelpers();
        applyTextStyles(posterHeadline);

        posterDate.style.fontFamily = "'Anton', sans-serif";
        posterDate.style.fontSize = '34px';
        posterDate.style.fontWeight = '400';
        posterDate.style.color = '#ffffff';
        posterDate.style.webkitTextStroke = '0px transparent';
        posterDate.style.textShadow = '0 4px 10px rgba(0,0,0,0.55)';
        posterDate.style.letterSpacing = '1.2px';
        posterDate.style.lineHeight = '1';

        posterNotes.style.fontFamily = "'Inter', sans-serif";
        posterNotes.style.fontSize = '24px';
        posterNotes.style.fontWeight = '600';
        posterNotes.style.color = '#ffffff';
        posterNotes.style.webkitTextStroke = '0px transparent';
        posterNotes.style.textShadow = '0 3px 12px rgba(0,0,0,0.52)';
        posterNotes.style.letterSpacing = '0px';
        posterNotes.style.lineHeight = '1.4';

        breakingText.style.fontFamily = "'Anton', sans-serif";
        breakingText.style.fontSize = '72px';
        breakingText.style.fontWeight = '400';
        breakingText.style.color = '#ff1f1f';

        canvasItems().forEach(el => {
            el.style.opacity = '1';
            el.classList.remove('locked');
        });

        posterLogo.style.zIndex = 6;
        posterHeadline.style.zIndex = 7;
        posterNotes.style.zIndex = 7;
        breakingBlock.style.zIndex = 6;
        subImageWrap.style.zIndex = 6;
        posterDate.style.zIndex = 6;

        setSelected(posterHeadline);
    }

    // Events
    newsDate.addEventListener('input', updateDate);
    headlineText.addEventListener('input', renderHeadline);
    notesText.addEventListener('input', updateNotes);
    breakingTextInput.addEventListener('input', updateBreaking);

    safeAreaToggle.addEventListener('change', updateCanvasHelpers);
    gridToggle.addEventListener('change', updateCanvasHelpers);

    lockSelectedToggle.addEventListener('change', function() {
        if (!selectedEl) return;
        selectedEl.classList.toggle('locked', this.checked);
    });

    notesToggle.addEventListener('change', updateNotes);
    breakingToggle.addEventListener('change', updateBreaking);

    overlayStrength.addEventListener('input', updateOverlay);
    backgroundZoom.addEventListener('input', updateBackgroundTransform);
    backgroundPosX.addEventListener('input', updateBackgroundTransform);
    backgroundPosY.addEventListener('input', updateBackgroundTransform);

    posterSize.addEventListener('change', updatePosterSize);

    mainImageInput.addEventListener('change', function() {
        if (this.files[0]) readImage(this, mainPreviewImage);
    });

    subImageInput.addEventListener('change', function() {
        if (this.files[0]) {
            subImageWrap.style.display = 'block';
            readImage(this, subPreviewImage);
            setSelected(subImageWrap);
        } else {
            subImageWrap.style.display = 'none';
        }
    });

    fontFamilySelect.addEventListener('change', () => applyTextStyles(selectedEl));
    fontSizeRange.addEventListener('input', () => {
        fontSizeValue.textContent = fontSizeRange.value + 'px';
        applyTextStyles(selectedEl);
    });
    fontWeightSelect.addEventListener('change', () => applyTextStyles(selectedEl));
    lineHeightRange.addEventListener('input', () => {
        lineHeightValue.textContent = (lineHeightRange.value / 100).toFixed(2);
        applyTextStyles(selectedEl);
    });
    letterSpacingRange.addEventListener('input', () => {
        letterSpacingValue.textContent = letterSpacingRange.value + 'px';
        applyTextStyles(selectedEl);
    });
    textColorPicker.addEventListener('input', () => applyTextStyles(selectedEl));
    strokeColorPicker.addEventListener('input', () => applyTextStyles(selectedEl));
    strokeSizeRange.addEventListener('input', () => {
        strokeSizeValue.textContent = strokeSizeRange.value + 'px';
        applyTextStyles(selectedEl);
    });
    shadowBlurRange.addEventListener('input', () => {
        shadowBlurValue.textContent = shadowBlurRange.value + 'px';
        applyTextStyles(selectedEl);
    });
    shadowOpacityRange.addEventListener('input', () => {
        shadowOpacityValue.textContent = shadowOpacityRange.value + '%';
        applyTextStyles(selectedEl);
    });
    uppercaseToggle.addEventListener('change', () => applyTextStyles(selectedEl));
    italicToggle.addEventListener('change', () => applyTextStyles(selectedEl));

    applyHighlightBtn.addEventListener('click', function() {
        const key = highlightWordInput.value.trim();
        if (!key) return;
        highlightMap[key] = highlightColorPicker.value;
        renderHeadline();
    });

    clearHighlightBtn.addEventListener('click', function() {
        highlightMap = {};
        highlightWordInput.value = '';
        renderHeadline();
    });

    subImageBorderSize.addEventListener('input', updateSubImageStyles);
    subImageRadius.addEventListener('input', updateSubImageStyles);
    subImageBorderColor.addEventListener('input', updateSubImageStyles);
    subImageZoom.addEventListener('input', updateSubImageStyles);

    logoWidth.addEventListener('input', updateLogoStyles);
    logoOpacity.addEventListener('input', updateLogoStyles);
    logoShadowBlur.addEventListener('input', updateLogoStyles);

    itemOpacity.addEventListener('input', function() {
        itemOpacityValue.textContent = this.value + '%';
        if (selectedEl) applyOpacity(selectedEl, this.value);
    });

    applySizePositionBtn.addEventListener('click', function() {
        if (!selectedEl) return;
        moveSelectedTo(parseInt(itemX.value || 0, 10), parseInt(itemY.value || 0, 10));
        const w = parseInt(itemWidth.value || selectedEl.offsetWidth, 10);
        const hVal = parseInt(itemHeight.value || 0, 10);
        resizeElement(selectedEl, w, hVal > 0 ? hVal : null);
    });

    bringFrontBtn.addEventListener('click', function() {
        if (!selectedEl) return;
        zCounter++;
        selectedEl.style.zIndex = zCounter;
        updateSelectionInfo();
    });

    sendBackBtn.addEventListener('click', function() {
        if (!selectedEl) return;
        selectedEl.style.zIndex = 4;
        updateSelectionInfo();
    });

    centerHorizBtn.addEventListener('click', function() {
        if (!selectedEl) return;
        const x = (posterWidth - selectedEl.offsetWidth) / 2;
        moveSelectedTo(x, parseInt(selectedEl.style.top || selectedEl.offsetTop, 10));
    });

    centerVertBtn.addEventListener('click', function() {
        if (!selectedEl) return;
        const y = (posterHeight - selectedEl.offsetHeight) / 2;
        moveSelectedTo(parseInt(selectedEl.style.left || selectedEl.offsetLeft, 10), y);
    });

    duplicateSubImagePosBtn.addEventListener('click', function() {
        if (!selectedEl) return;
        updatePosterSize();
    });

    alignButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!selectedEl) return;
            const align = this.dataset.align;
            selectedEl.style.textAlign = align;
        });
    });

    downloadPngBtn.addEventListener('click', () => exportPoster('png'));
    downloadJpgBtn.addEventListener('click', () => exportPoster('jpg'));
    resetBtn.addEventListener('click', resetEditor);

    // Drag / Resize
    document.addEventListener('pointermove', function(e) {
        if (currentDrag) {
            const dx = e.clientX - currentDrag.startX;
            const dy = e.clientY - currentDrag.startY;
            moveSelectedTo(currentDrag.origLeft + dx, currentDrag.origTop + dy);
        }

        if (currentResize) {
            const dx = e.clientX - currentResize.startX;
            const dy = e.clientY - currentResize.startY;
            const el = currentResize.el;

            if (el === posterLogo || el === posterHeadline || el === posterDate || el === posterNotes || el === breakingBlock) {
                resizeElement(el, currentResize.origWidth + dx, currentResize.origHeight + dy);
            } else {
                resizeElement(el, currentResize.origWidth + dx, currentResize.origHeight + dy);
            }
        }
    });

    document.addEventListener('pointerup', function() {
        currentDrag = null;
        currentResize = null;
    });

    // Selection
    canvasItems().forEach(el => makeSelectableAndMovable(el));
    poster.addEventListener('pointerdown', function(e) {
        if (e.target === poster || e.target === mainPreviewImage || e.target === posterOverlay || e.target === posterGrid || e.target === posterSafeArea) {
            canvasItems().forEach(item => item.classList.remove('selected'));
            selectedEl = null;
            selectedElementName.textContent = 'Selected: None';
            selectedElementInfo.textContent = 'Click any item on poster to edit it.';
        }
    });

    // Default styles
    function initDefaultTextStyles() {
        renderHeadline();
        posterHeadline.style.fontFamily = "'Noto Sans Sinhala', 'Inter', sans-serif";
        posterHeadline.style.fontSize = '66px';
        posterHeadline.style.fontWeight = '900';
        posterHeadline.style.lineHeight = '1.12';
        posterHeadline.style.letterSpacing = '0.2px';
        posterHeadline.style.color = '#ffffff';
        posterHeadline.style.textShadow = '0 4px 18px rgba(0,0,0,0.70)';

        posterDate.style.fontFamily = "'Anton', sans-serif";
        posterDate.style.fontSize = '34px';
        posterDate.style.fontWeight = '400';
        posterDate.style.lineHeight = '1';
        posterDate.style.letterSpacing = '1.2px';
        posterDate.style.color = '#ffffff';

        posterNotes.style.fontFamily = "'Inter', sans-serif";
        posterNotes.style.fontSize = '24px';
        posterNotes.style.fontWeight = '600';
        posterNotes.style.lineHeight = '1.4';
        posterNotes.style.letterSpacing = '0px';
        posterNotes.style.color = '#ffffff';
    }

    // Initial
    initDefaultTextStyles();
    updatePosterSize();
    updateDate();
    updateNotes();
    updateBreaking();
    updateOverlay();
    updateBackgroundTransform();
    updateSubImageStyles();
    updateLogoStyles();
    updateCanvasHelpers();
    setSelected(posterHeadline);

    fontSizeValue.textContent = fontSizeRange.value + 'px';
    lineHeightValue.textContent = (lineHeightRange.value / 100).toFixed(2);
    letterSpacingValue.textContent = letterSpacingRange.value + 'px';
    strokeSizeValue.textContent = strokeSizeRange.value + 'px';
    shadowBlurValue.textContent = shadowBlurRange.value + 'px';
    shadowOpacityValue.textContent = shadowOpacityRange.value + '%';
</script>

</body>
</html>