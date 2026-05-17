<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#B4707A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Kate Nails">
    <title>Reservar Cita — Kate Nails</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,600&family=Great+Vibes&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        /* ─── Tokens ──────────────────────────────────────────── */
        :root {
            --rg:          #B4707A;   /* Rose Gold — primary */
            --rg-light:    #C98D95;   /* Rose Gold light */
            --rg-hover:    #9E5E68;   /* Rose Gold dark hover */
            --blush:       #EBC1BA;   /* Rosa Empolvado */
            --blush-soft:  #F5E2DE;   /* Palo Rosa light */
            --sand:        #DFAE9A;   /* Dorado Rosa */
            --taupe:       #E9E1DC;   /* Taupe Claro */
            --bg:          #FAF0ED;   /* Página — blush muy suave */
            --panel:       #FFFFFF;   /* Paneles blancos */
            --panel-tint:  #FDF6F4;   /* Panel ligeramente tintado */
            --text:        #2E1A1A;   /* Texto principal */
            --text-mid:    #7A5252;   /* Texto secundario */
            --text-soft:   #B08888;   /* Texto suave */
            --border:      #EAD5CF;   /* Bordes */
            --border-mid:  #D4B0A8;   /* Bordes medios */

            --serif:   'Cormorant Garamond', Georgia, serif;
            --script:  'Great Vibes', cursive;
            --sans:    'Montserrat', system-ui, sans-serif;
        }

        /* ─── Reset ───────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: var(--sans);
            font-size: 14px;
            line-height: 1.7;
            min-height: 100dvh;
        }

        /* ─── Sparkle SVG helper (decorative) ────────────────── */
        .sparkle {
            display: inline-block;
            width: 12px; height: 12px;
            flex-shrink: 0;
        }

        /* ─── Page shell ──────────────────────────────────────── */
        .shell {
            display: flex;
            min-height: 100dvh;
        }

        /* ─── Sidebar ─────────────────────────────────────────── */
        .sidebar {
            width: 310px;
            flex-shrink: 0;
            background: var(--panel);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2.5rem 2rem 2rem;
            position: sticky;
            top: 0;
            height: 100dvh;
            overflow: hidden;
        }

        /* Logo */
        .sidebar-logo {
            width: 160px;
            height: auto;
            object-fit: contain;
            margin-bottom: 0.5rem;
            filter: drop-shadow(0 2px 12px rgba(180,112,122,0.18));
        }

        /* Step divider */
        .sidebar-divider {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border), transparent);
            margin: 1.5rem 0;
        }

        /* Step tracker */
        .step-eyebrow {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            color: var(--text-soft);
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .step-list {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 0.9rem;
            position: relative;
        }
        .step-item:not(:last-child) {
            padding-bottom: 1.5rem;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 13px; top: 28px; bottom: 0;
            width: 1px;
            background: var(--border);
            transition: background 0.4s;
        }
        .step-item.s-done:not(:last-child)::after { background: var(--blush); }

        .step-circle {
            width: 28px; height: 28px;
            border-radius: 50%;
            border: 1.5px solid var(--border-mid);
            background: var(--panel);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-soft);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            transition: all 0.35s;
        }
        .step-item.s-active .step-circle {
            background: var(--rg);
            border-color: var(--rg);
            color: #fff;
            box-shadow: 0 2px 12px rgba(180,112,122,0.35);
        }
        .step-item.s-done .step-circle {
            background: var(--blush);
            border-color: var(--blush);
            color: #fff;
        }
        .step-item.s-done .step-circle::after {
            content: '✓';
            font-size: 0.65rem;
        }
        .step-item.s-done .step-num { display: none; }

        .step-text { padding-top: 4px; }
        .step-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-soft);
            transition: color 0.3s;
            letter-spacing: 0.02em;
        }
        .step-item.s-active .step-name { color: var(--rg); }
        .step-item.s-done .step-name { color: var(--text-mid); }
        .step-cap {
            font-size: 0.68rem;
            color: var(--text-soft);
            opacity: 0.6;
            margin-top: 1px;
        }
        .step-item.s-active .step-cap { opacity: 0.8; }
        .step-item.s-clickable { cursor: pointer; }
        .step-item.s-clickable:hover .step-circle { border-color: var(--rg); }

        /* Sidebar footer */
        .sidebar-footer {
            margin-top: auto;
            text-align: center;
            font-size: 0.62rem;
            color: var(--text-soft);
            letter-spacing: 0.06em;
            opacity: 0.5;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            width: 100%;
        }
        .sidebar-tagline {
            font-family: var(--script);
            font-size: 1.1rem;
            color: var(--rg-light);
            display: block;
            margin-bottom: 0.4rem;
            opacity: 0.75;
        }

        /* ─── Mobile top bar ──────────────────────────────────── */
        .mobile-bar {
            display: none;
            background: var(--panel);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .mobile-bar-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.9rem 1.25rem;
        }
        .mobile-logo {
            height: 44px;
            width: auto;
            object-fit: contain;
        }
        .mobile-bar-text { flex: 1; }
        .mobile-brand {
            font-family: var(--script);
            font-size: 1.4rem;
            line-height: 1;
            color: var(--rg);
            display: block;
        }
        .mobile-sub {
            font-size: 0.58rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--text-soft);
            display: block;
        }
        .mobile-progress-track {
            height: 3px;
            background: var(--blush-soft);
            position: relative;
        }
        .mobile-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--rg), var(--sand));
            border-radius: 0 2px 2px 0;
            transition: width 0.45s cubic-bezier(.4,0,.2,1);
        }

        /* ─── Main content ────────────────────────────────────── */
        .main {
            flex: 1;
            padding: 3rem 3.5rem;
            max-width: 680px;
        }

        /* ─── Responsive ──────────────────────────────────────── */
        @media (max-width: 767px) {
            .sidebar { display: none; }
            .mobile-bar { display: block; }
            .main { padding: 1.5rem 1.25rem 2rem; max-width: 100%; }
        }
        @media (min-width: 768px) and (max-width: 1099px) {
            .sidebar { width: 260px; padding: 2rem 1.5rem; }
            .sidebar-logo { width: 130px; }
            .main { padding: 2.5rem 2rem; }
        }

        /* ─── Typography ──────────────────────────────────────── */
        .step-script {
            font-family: var(--script);
            font-size: 1.6rem;
            color: var(--rg-light);
            line-height: 1;
            margin-bottom: -0.25rem;
            display: block;
        }
        .step-heading {
            font-family: var(--serif);
            font-size: 2.75rem;
            font-weight: 600;
            color: var(--text);
            line-height: 1.1;
            margin: 0 0 0.5rem;
        }
        .step-desc {
            font-size: 0.8rem;
            font-weight: 400;
            color: var(--text-mid);
            margin: 0 0 2rem;
            letter-spacing: 0.01em;
        }
        .step-desc strong { color: var(--rg); font-weight: 600; }

        /* ─── Alerts ──────────────────────────────────────────── */
        .alert-err {
            background: rgba(180,112,122,0.08);
            border: 1px solid rgba(180,112,122,0.25);
            color: var(--rg-hover);
            padding: 0.6rem 1rem;
            border-radius: 6px;
            font-size: 0.78rem;
            margin-bottom: 1.25rem;
        }
        .field-err {
            font-size: 0.7rem;
            color: var(--rg-hover);
            margin-top: 0.3rem;
            display: block;
        }

        /* ─── Buttons ─────────────────────────────────────────── */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--rg), var(--sand));
            color: #fff;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-family: var(--sans);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 3px 14px rgba(180,112,122,0.3);
        }
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(180,112,122,0.4);
        }
        .btn-primary:disabled { opacity: 0.45; cursor: not-allowed; transform: none; box-shadow: none; }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: transparent;
            border: none;
            color: var(--text-soft);
            font-family: var(--sans);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            cursor: pointer;
            padding: 0.75rem 0;
            transition: color 0.2s;
        }
        .btn-ghost:hover { color: var(--rg); }

        .nav-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }
        .nav-row.end { justify-content: flex-end; }

        /* ─── Service cards ───────────────────────────────────── */
        .service-list { display: flex; flex-direction: column; gap: 0.6rem; }

        .service-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-left-width: 3px;
            border-left-color: transparent;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .service-card:hover {
            border-color: var(--blush);
            background: var(--panel-tint);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(180,112,122,0.1);
        }
        .service-card.is-sel {
            border-color: var(--blush);
            background: var(--panel-tint);
            box-shadow: 0 3px 12px rgba(180,112,122,0.12);
        }
        .svc-dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
            box-shadow: 0 1px 4px rgba(0,0,0,0.12);
        }
        .svc-info { flex: 1; min-width: 0; }
        .svc-name {
            font-family: var(--serif);
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            line-height: 1.15;
        }
        .svc-desc {
            font-size: 0.72rem;
            color: var(--text-mid);
            margin-top: 1px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .svc-meta { text-align: right; flex-shrink: 0; }
        .svc-price {
            font-family: var(--serif);
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--rg);
        }
        .svc-dur {
            font-size: 0.68rem;
            color: var(--text-soft);
            margin-top: 1px;
        }
        .svc-check {
            position: absolute; right: 1rem; top: 50%;
            transform: translateY(-50%);
            color: var(--rg);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .service-card.is-sel .svc-check { opacity: 1; }

        /* ─── Date picker ─────────────────────────────────────── */
        .month-label {
            font-family: var(--script);
            font-size: 1.5rem;
            color: var(--rg-light);
            margin: 0 0 0.75rem;
            display: block;
        }
        .date-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.3rem;
            margin-bottom: 1.5rem;
        }
        .date-cell {
            padding: 0.45rem 0.1rem;
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s;
        }
        .date-cell:hover {
            border-color: var(--blush);
            background: var(--blush-soft);
        }
        .date-cell.is-sel {
            background: linear-gradient(135deg, var(--rg), var(--sand));
            border-color: var(--rg);
            box-shadow: 0 2px 8px rgba(180,112,122,0.3);
        }
        .d-name {
            display: block;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-soft);
            margin-bottom: 2px;
        }
        .d-num {
            font-family: var(--serif);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            line-height: 1;
        }
        .date-cell.is-sel .d-name,
        .date-cell.is-sel .d-num { color: #fff; }

        /* ─── Time slots ──────────────────────────────────────── */
        .time-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.4rem;
        }
        .time-cell {
            padding: 0.6rem 0.2rem;
            background: var(--panel);
            border: 1.5px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--text-mid);
            transition: all 0.15s;
            letter-spacing: 0.03em;
        }
        .time-cell:hover { border-color: var(--blush); background: var(--blush-soft); color: var(--rg); }
        .time-cell.is-sel {
            background: linear-gradient(135deg, var(--rg), var(--sand));
            border-color: var(--rg);
            color: #fff;
            box-shadow: 0 2px 8px rgba(180,112,122,0.3);
        }

        /* ─── Form inputs ─────────────────────────────────────── */
        .form-block { margin-bottom: 1.6rem; }
        .form-label {
            display: block;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--text-soft);
            margin-bottom: 0.4rem;
        }
        .form-label .opt {
            text-transform: none;
            letter-spacing: 0;
            font-weight: 400;
            opacity: 0.6;
            font-size: 0.7rem;
        }
        .form-field {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1.5px solid var(--border-mid);
            border-radius: 0;
            padding: 0.5rem 0;
            color: var(--text);
            font-family: var(--sans);
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-field::placeholder { color: var(--text-soft); opacity: 0.4; }
        .form-field:focus { border-bottom-color: var(--rg); }
        textarea.form-field {
            border: 1.5px solid var(--border-mid);
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
            background: var(--panel-tint);
            resize: vertical;
            min-height: 76px;
        }
        textarea.form-field:focus { border-color: var(--rg); }

        /* ─── Summary chip ────────────────────────────────────── */
        .summary-chip {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--panel-tint);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 2rem;
        }
        .summary-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .summary-body { flex: 1; }
        .summary-svc {
            font-family: var(--serif);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
        }
        .summary-when { font-size: 0.72rem; color: var(--text-mid); margin-top: 1px; }
        .summary-price {
            font-family: var(--serif);
            font-size: 1.35rem;
            font-weight: 600;
            color: var(--rg);
            flex-shrink: 0;
        }

        /* ─── Confirmation ────────────────────────────────────── */
        .confirm-wrap { text-align: center; }
        .confirm-ring {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: var(--blush-soft);
            border: 1.5px solid var(--blush);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .confirm-ring svg { width: 26px; height: 26px; color: var(--rg); }

        .confirm-script {
            font-family: var(--script);
            font-size: 1.8rem;
            color: var(--rg-light);
            display: block;
            margin-bottom: -0.25rem;
        }
        .confirm-heading {
            font-family: var(--serif);
            font-size: 2.4rem;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 0.4rem;
        }
        .confirm-sub { font-size: 0.78rem; color: var(--text-mid); margin: 0 0 1.5rem; }

        .receipt {
            background: var(--panel-tint);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 0.5rem 0;
        }
        .receipt-row + .receipt-row { border-top: 1px solid var(--border); }
        .receipt-key {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            color: var(--text-soft);
        }
        .receipt-val {
            font-family: var(--serif);
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            text-align: right;
            max-width: 65%;
        }
        .receipt-row.is-total .receipt-key { color: var(--text-mid); }
        .receipt-row.is-total .receipt-val { color: var(--rg); font-size: 1.3rem; }

        .pending-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(180,112,122,0.1);
            border: 1px solid rgba(180,112,122,0.2);
            color: var(--rg);
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            margin-bottom: 1rem;
        }
        .pending-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--rg);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        /* ─── Empty state ─────────────────────────────────────── */
        .empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-soft);
        }
        .empty svg { width: 36px; height: 36px; opacity: 0.25; margin: 0 auto 1rem; display: block; }
        .empty p { font-size: 0.82rem; margin: 0; }

        /* ─── Step animation ──────────────────────────────────── */
        @keyframes stepIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .step-wrap { animation: stepIn 0.3s ease both; }

        /* ─── Decorative sparkles in sidebar ──────────────────── */
        .deco-sparkle {
            display: block;
            color: var(--blush);
            text-align: center;
            font-size: 0.9rem;
            letter-spacing: 0.4em;
            margin-bottom: 0.75rem;
            opacity: 0.6;
        }
    </style>
</head>
<body>

    {{-- Mobile bar --}}
    <div class="mobile-bar">
        <div class="mobile-bar-inner">
            <img src="/images/kate-nails-logo.png" class="mobile-logo" alt="Kate Nails">
            <div class="mobile-bar-text">
                <span class="mobile-brand">Kate Nails</span>
                <span class="mobile-sub">Manicure · Pedicure · Beauty</span>
            </div>
        </div>
        <div class="mobile-progress-track">
            <div class="mobile-progress-fill" id="progressFill" style="width:25%"></div>
        </div>
    </div>

    <div class="shell">

        {{-- Desktop sidebar --}}
        <aside class="sidebar">
            <img src="/images/kate-nails-logo.png" class="sidebar-logo" alt="Kate Nails">

            <div class="sidebar-divider"></div>

            <div class="step-eyebrow">Tu proceso de reserva</div>

            <div class="step-list">
                <div class="step-item" id="si-1">
                    <div class="step-circle"><span class="step-num">1</span></div>
                    <div class="step-text">
                        <div class="step-name">Servicio</div>
                        <div class="step-cap" id="sc-1">Elige el tratamiento</div>
                    </div>
                </div>
                <div class="step-item" id="si-2">
                    <div class="step-circle"><span class="step-num">2</span></div>
                    <div class="step-text">
                        <div class="step-name">Fecha</div>
                        <div class="step-cap" id="sc-2">¿Cuándo te vemos?</div>
                    </div>
                </div>
                <div class="step-item" id="si-3">
                    <div class="step-circle"><span class="step-num">3</span></div>
                    <div class="step-text">
                        <div class="step-name">Hora</div>
                        <div class="step-cap" id="sc-3">Elige tu horario</div>
                    </div>
                </div>
                <div class="step-item" id="si-4">
                    <div class="step-circle"><span class="step-num">4</span></div>
                    <div class="step-text">
                        <div class="step-name">Tus datos</div>
                        <div class="step-cap" id="sc-4">Para confirmarte</div>
                    </div>
                </div>
            </div>

            <div class="sidebar-footer">
                <span class="sidebar-tagline">Kate Nails</span>
                <span class="deco-sparkle">✦ &nbsp; ✦ &nbsp; ✦</span>
                Manicure · Pedicure · Beauty<br>
                © {{ date('Y') }} Kate Nails
            </div>
        </aside>

        {{-- Main --}}
        <main class="main">
            {{ $slot }}
        </main>

    </div>

    @livewireScripts
    <script>
        function syncStep(step) {
            step = parseInt(step) || 1;
            for (let n = 1; n <= 4; n++) {
                const el = document.getElementById('si-' + n);
                if (!el) continue;
                el.classList.remove('s-active', 's-done', 's-clickable');
                if (n === step) el.classList.add('s-active');
                else if (n < step) el.classList.add('s-done', 's-clickable');
            }
            const fill = document.getElementById('progressFill');
            if (fill) fill.style.width = Math.min(step / 4 * 100, 100) + '%';
        }

        function readStep() {
            const el = document.querySelector('[data-booking-step]');
            return el ? (parseInt(el.dataset.bookingStep) || 1) : 1;
        }

        // Sync on initial load
        document.addEventListener('DOMContentLoaded', () => syncStep(readStep()));

        // Reliable: listen to the event dispatched directly from the PHP component
        window.addEventListener('stepChanged', (e) => {
            const step = e.detail?.step ?? e.detail?.[0]?.step ?? 1;
            syncStep(step);
        });

        // Fallback: MutationObserver watches the data-booking-step attribute
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new MutationObserver(() => syncStep(readStep()));
            observer.observe(document.body, {
                attributes: true,
                attributeFilter: ['data-booking-step'],
                subtree: true,
            });
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js').catch(() => {});
        }
    </script>
</body>
</html>
