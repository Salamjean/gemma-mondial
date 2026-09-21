<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Salle d'Attente - {{ $hospital->label ?? 'Hôpital' }}</title>
    <link rel="icon" href="{{ asset(iconsLoad()['logo'] ?? 'favicon.ico') }}">

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        :root {
            /* THEME LIGHT (PAR DÉFAUT) */
            --bg-main: #f1f5f9;
            --bg-header: #ffffff;
            --bg-card: #ffffff;
            --bg-card-sub: #f8fafc;
            --border-card: #e2e8f0;
            --border-active: #0284c7;
            --primary: #0284c7;
            --primary-light: #e0f2fe;
            --primary-glow: rgba(2, 132, 199, 0.25);
            --success: #059669;
            --success-light: #d1fae5;
            --text-main: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --shadow-card: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            --shadow-active: 0 20px 35px -10px rgba(2, 132, 199, 0.25);
        }

        [data-theme="dark"] {
            /* THEME DARK (OPTIONNEL) */
            --bg-main: #090e17;
            --bg-header: #0f172a;
            --bg-card: #0f172a;
            --bg-card-sub: #1e293b;
            --border-card: #1e293b;
            --border-active: #38bdf8;
            --primary: #38bdf8;
            --primary-light: rgba(56, 189, 248, 0.15);
            --primary-glow: rgba(56, 189, 248, 0.35);
            --success: #10b981;
            --success-light: rgba(16, 185, 129, 0.15);
            --text-main: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --shadow-card: 0 20px 40px -15px rgba(0, 0, 0, 0.7);
            --shadow-active: 0 0 50px -10px rgba(56, 189, 248, 0.35);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            user-select: none;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background-image: 
                radial-gradient(circle at 10% 10%, rgba(2, 132, 199, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(5, 150, 105, 0.05) 0%, transparent 40%);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* HEADER TV */
        .tv-header {
            height: 92px;
            background: var(--bg-header);
            border-bottom: 1px solid var(--border-card);
            padding: 0 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .hospital-brand {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .hospital-logo {
            height: 58px;
            width: auto;
            max-width: 150px;
            object-fit: contain;
        }

        .hospital-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-main);
            margin: 0;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            background: var(--success-light);
            border: 1px solid rgba(5, 150, 105, 0.2);
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--success);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--success);
            box-shadow: 0 0 8px var(--success);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.3); }
        }

        .clock-container {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .digital-clock {
            font-family: 'Outfit', sans-serif;
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: 1px;
            line-height: 1;
        }

        .clock-date {
            font-size: 0.95rem;
            color: var(--text-muted);
            text-align: right;
            font-weight: 600;
            text-transform: capitalize;
            margin-top: 2px;
        }

        /* CONTROLS */
        .tv-controls {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .ctrl-btn {
            background: var(--bg-card-sub);
            border: 1px solid var(--border-card);
            color: var(--text-secondary);
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 1.1rem;
        }

        .ctrl-btn:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        /* MAIN BODY */
        .tv-body {
            flex: 1;
            display: grid;
            grid-template-columns: 1.45fr 1fr;
            gap: 2rem;
            padding: 2rem 2.5rem;
            height: calc(100vh - 92px - 55px);
            overflow: hidden;
        }

        /* HERO CARD (PATIENT EN COURS) */
        .hero-call-card {
            background: var(--bg-card);
            border: 2px solid var(--border-card);
            border-radius: 28px;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            box-shadow: var(--shadow-card);
            transition: all 0.4s ease;
        }

        .hero-call-card.is-calling {
            border-color: var(--border-active);
            box-shadow: var(--shadow-active);
            animation: heroBorderPulse 2.5s infinite alternate;
        }

        @keyframes heroBorderPulse {
            0% { border-color: rgba(2, 132, 199, 0.4); }
            100% { border-color: rgba(2, 132, 199, 1); }
        }

        .hero-badge-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 1.4rem;
            background: var(--primary-light);
            border: 1px solid rgba(2, 132, 199, 0.3);
            border-radius: 16px;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sound-wave {
            display: flex;
            align-items: center;
            gap: 4px;
            height: 24px;
        }

        .sound-bar {
            width: 4px;
            height: 100%;
            background: var(--primary);
            border-radius: 4px;
            animation: soundWave 1.2s infinite ease-in-out;
        }

        .sound-bar:nth-child(1) { animation-delay: 0.1s; height: 35%; }
        .sound-bar:nth-child(2) { animation-delay: 0.3s; height: 70%; }
        .sound-bar:nth-child(3) { animation-delay: 0.5s; height: 100%; }
        .sound-bar:nth-child(4) { animation-delay: 0.2s; height: 50%; }
        .sound-bar:nth-child(5) { animation-delay: 0.4s; height: 80%; }

        @keyframes soundWave {
            0%, 100% { transform: scaleY(0.3); }
            50% { transform: scaleY(1); }
        }

        .patient-main-display {
            text-align: center;
            margin: 1rem 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .empty-call-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            width: 100%;
            height: 100%;
            padding: 1.5rem;
            margin: auto 0;
        }

        .empty-call-icon-box {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--bg-card-sub);
            border: 2px dashed var(--border-card);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: var(--primary);
            font-size: 2.8rem;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.03);
            animation: emptyPulse 3s infinite ease-in-out;
        }

        @keyframes emptyPulse {
            0%, 100% { transform: scale(1); opacity: 0.85; }
            50% { transform: scale(1.06); opacity: 1; border-color: var(--primary); }
        }

        .empty-call-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.65rem;
            letter-spacing: -0.5px;
        }

        .empty-call-desc {
            font-size: 1.15rem;
            color: var(--text-muted);
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.5;
            font-weight: 500;
        }

        .patient-label {
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 0.6rem;
        }

        .patient-name {
            font-family: 'Outfit', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.15;
            color: var(--text-main);
            letter-spacing: -0.5px;
            margin-bottom: 0.75rem;
            word-break: break-word;
        }

        .patient-subcode {
            display: inline-block;
            background: var(--bg-card-sub);
            border: 1px solid var(--border-card);
            padding: 0.45rem 1.25rem;
            border-radius: 12px;
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 700;
        }

        /* DETAILS GRID */
        .call-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .detail-box {
            background: var(--bg-card-sub);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .detail-icon {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        .detail-icon.doctor-icon {
            background: var(--success-light);
            color: var(--success);
        }

        .detail-info-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
        }

        .detail-info-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
        }

        /* SIDE PANEL (HISTORIQUE) */
        .side-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-card);
            border-radius: 28px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-card);
        }

        .panel-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .recent-calls-list {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            padding-right: 0.5rem;
        }

        .recent-calls-list::-webkit-scrollbar {
            width: 4px;
        }
        .recent-calls-list::-webkit-scrollbar-thumb {
            background: var(--border-card);
            border-radius: 4px;
        }

        .recent-item {
            background: var(--bg-card-sub);
            border: 1px solid var(--border-card);
            border-radius: 16px;
            padding: 0.95rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .recent-item:first-child {
            background: var(--primary-light);
            border-color: rgba(2, 132, 199, 0.3);
        }

        .recent-patient-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.15rem;
        }

        .recent-doctor {
            font-size: 0.9rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .recent-badge {
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            padding: 0.35rem 0.85rem;
            border-radius: 10px;
            background: var(--bg-card);
            color: var(--primary);
            border: 1px solid var(--border-card);
            white-space: nowrap;
        }

        /* TICKER TAPE */
        .tv-ticker {
            height: 55px;
            background: var(--bg-header);
            border-top: 1px solid var(--border-card);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .ticker-label {
            background: var(--primary);
            color: white;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            letter-spacing: 1px;
            margin-right: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            z-index: 2;
        }

        .ticker-content-wrapper {
            flex: 1;
            overflow: hidden;
            white-space: nowrap;
        }

        .ticker-content {
            display: inline-block;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-secondary);
            animation: tickerScroll 30s linear infinite;
        }

        @keyframes tickerScroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        /* AUDIO UNLOCK MODAL */
        .audio-unlock-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .audio-unlock-card {
            background: #ffffff;
            border: 1px solid var(--border-card);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 3rem 3.5rem;
            border-radius: 32px;
            max-width: 580px;
        }

        .audio-unlock-btn {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #ffffff;
            font-size: 1.35rem;
            font-weight: 800;
            padding: 1.1rem 3rem;
            border-radius: 18px;
            border: none;
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 1rem;
        }

        .audio-unlock-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(2, 132, 199, 0.6);
        }

        /* MODAL COPIER LIEN TV ANDROID */
        .tv-link-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(8px);
            z-index: 1100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .tv-link-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 2.5rem;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
        }
    </style>
</head>
<body>

    @php
        $tvPublicUrl = route('tv.waiting_screen', $token);
    @endphp

    <!-- MODAL POUR DÉVERROUILLER L'AUDIO AUTOMATIQUE DU NAVIGATEUR -->
    <div id="audioUnlockModal" class="audio-unlock-modal" style="display: none;">
        <div class="audio-unlock-card">
            <div class="mb-4">
                <i class="fa-solid fa-volume-high text-primary" style="font-size: 4.5rem;"></i>
            </div>
            <h2 class="fw-bold text-dark mb-3" style="font-family: 'Outfit';">Écran d'Appel Salle d'Attente</h2>
            <p class="text-muted fs-16 mb-4">
                Cliquez sur le bouton pour activer le carillon sonore et la synthèse vocale automatique en salle d'attente.
            </p>
            <button id="btnUnlockAudio" class="audio-unlock-btn">
                <i class="fa-solid fa-play"></i>
                <span>Activer l'écran & le son</span>
            </button>
        </div>
    </div>

    <!-- MODAL COPIER LE LIEN TV ANDROID -->
    <div id="tvLinkModal" class="tv-link-modal">
        <div class="tv-link-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-brands fa-android text-success fs-24"></i>
                    <h4 class="fw-bold text-dark mb-0 fs-18">Lien direct pour TV Android</h4>
                </div>
                <button type="button" class="btn-close" id="btnCloseTvModal"></button>
            </div>
            <p class="text-muted fs-14 text-start mb-3">
                Ouvrez ce lien dans le navigateur de votre TV Android ou Smart TV. Aucun mot de passe n'est requis pour la TV !
            </p>

            <div class="p-3 bg-light rounded-16 border mb-3 text-center">
                <!-- QR Code direct -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($tvPublicUrl) }}" alt="QR Code TV" class="rounded-12 shadow-sm mb-2" style="width: 160px; height: 160px;">
                <div class="text-muted fs-12 fw-semibold">Scannez avec un smartphone ou ouvrez l'adresse ci-dessous</div>
            </div>

            <div class="input-group mb-4">
                <input type="text" id="inputTvUrl" class="form-control form-control-lg fs-14 fw-bold text-dark bg-white border-primary" value="{{ $tvPublicUrl }}" readonly>
                <button class="btn btn-primary px-3 fw-bold" id="btnCopyTvUrl">
                    <i class="fa-solid fa-copy me-1"></i> Copier
                </button>
            </div>

            <button type="button" class="btn btn-secondary w-100 rounded-12 py-2 fw-bold" id="btnDismissTvModal">
                Fermer
            </button>
        </div>
    </div>

    <!-- HEADER DE LA TV (LIGHT) -->
    <header class="tv-header">
        <div class="hospital-brand">
            @if ($hospital->img_url)
                <img src="{{ asset('assets/uploads/hospital/' . $hospital->img_url) }}" alt="Logo" class="hospital-logo">
            @else
                <img src="{{ asset(iconsLoad()['logo'] ?? 'assets/uploads/hospital.gif') }}" alt="Logo" class="hospital-logo">
            @endif
            <div>
                <h1 class="hospital-title">{{ $hospital->label ?? 'CENTRE HOSPITALIER' }}</h1>
                <div class="d-flex align-items-center gap-3 mt-1">
                    <span class="live-indicator">
                        <span class="pulse-dot"></span>
                        <span>Système d'appel connecté</span>
                    </span>
                    <span class="text-muted fs-13 d-none d-lg-inline">
                        <i class="fa-solid fa-building me-1"></i> Salle d'attente principale
                    </span>
                </div>
            </div>
        </div>

        <div class="clock-container">
            <div class="d-flex flex-column align-items-end">
                <div id="liveClock" class="digital-clock">--:--:--</div>
                <div id="liveDate" class="clock-date">Chargement...</div>
            </div>

            <div class="tv-controls">
                <button id="btnOpenTvModal" class="ctrl-btn" title="Copier le lien pour TV Android / QR Code">
                    <i class="fa-brands fa-android text-success"></i>
                </button>
                <button id="btnToggleTheme" class="ctrl-btn" title="Basculer thème Clair / Sombre">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <button id="btnTestSound" class="ctrl-btn" title="Tester le carillon et la voix">
                    <i class="fa-solid fa-volume-high"></i>
                </button>
                <button id="btnSimulateCall" class="ctrl-btn" title="Simuler un appel patient (Test)">
                    <i class="fa-solid fa-bullhorn"></i>
                </button>
                <button id="btnFullscreen" class="ctrl-btn" title="Plein écran (F11)">
                    <i class="fa-solid fa-expand"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- CORPS DE LA PAGE (LIGHT) -->
    <main class="tv-body">
        <!-- HERO CARD (PATIENT ACTUELLEMENT APPELÉ) -->
        <section id="heroCard" class="hero-call-card {{ $currentCall ? 'is-calling' : '' }}">
            <div class="hero-badge-container">
                <div class="hero-badge">
                    <i class="fa-solid fa-bullhorn fa-bounce"></i>
                    <span>Patient Appelé</span>
                </div>
                <div class="sound-wave" id="soundWaveVisualizer">
                    <div class="sound-bar"></div>
                    <div class="sound-bar"></div>
                    <div class="sound-bar"></div>
                    <div class="sound-bar"></div>
                    <div class="sound-bar"></div>
                </div>
            </div>

            <div id="heroPatientDisplay" class="patient-main-display">
                @if ($currentCall)
                    <div class="patient-label">Veuillez vous présenter en consultation</div>
                    <div id="heroPatientName" class="patient-name">{{ $currentCall->patient_name }}</div>
                    <div id="heroPatientService" class="patient-subcode">
                        <i class="fa-solid fa-stethoscope me-1 text-primary"></i> {{ $currentCall->service_name ?? 'Consultation médicale' }}
                    </div>
                @else
                    <div class="empty-call-state">
                        <div class="empty-call-icon-box">
                            <i class="fa-solid fa-user-clock"></i>
                        </div>
                        <h3 class="empty-call-title">En attente du prochain appel...</h3>
                        <p class="empty-call-desc">Les patients appelés par les médecins s'afficheront ici automatiquement.</p>
                    </div>
                @endif
            </div>

            <div id="heroDetailsGrid" class="call-details-grid" style="{{ $currentCall ? '' : 'display: none;' }}">
                <div class="detail-box">
                    <div class="detail-icon doctor-icon">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <div class="detail-info-label">Médecin traitant</div>
                        <div id="heroDoctorName" class="detail-info-value">{{ $currentCall->doctor_name ?? 'Médecin' }}</div>
                    </div>
                </div>

                <div class="detail-box">
                    <div class="detail-icon">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <div>
                        <div class="detail-info-label">Lieu / Salle</div>
                        <div id="heroCabinet" class="detail-info-value">{{ $currentCall->cabinet ?? 'Cabinet de consultation' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SIDE PANEL (HISTORIQUE DES DERNIERS APPELS) -->
        <aside class="side-panel">
            <div class="panel-header">
                <h3 class="panel-title">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                    <span>6 derniers appels du jour</span>
                </h3>
                <span id="callCountBadge" class="badge bg-primary px-3 py-1.5 rounded-pill fs-12 fw-bold">
                    {{ count($recentCalls) }} appel(s)
                </span>
            </div>

            <div id="recentCallsList" class="recent-calls-list">
                @forelse ($recentCalls as $call)
                    <div class="recent-item">
                        <div>
                            <div class="recent-patient-name">{{ $call->patient_name }}</div>
                            <div class="recent-doctor">
                                <i class="fa-solid fa-user-doctor text-success fs-11"></i>
                                <span>{{ $call->doctor_name }}</span>
                                <span class="text-muted">• {{ $call->cabinet ?? 'Cabinet' }}</span>
                            </div>
                        </div>
                        <div class="recent-badge">
                            <i class="fa-regular fa-clock me-1"></i>
                            <span>{{ $call->called_at ? $call->called_at->format('H:i') : $call->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <div id="noRecentCalls" class="text-center py-5 text-muted">
                        <i class="fa-regular fa-clipboard fs-32 mb-2 opacity-50"></i>
                        <p class="fs-14">Aucun appel enregistré pour aujourd'hui.</p>
                    </div>
                @endforelse
            </div>
        </aside>
    </main>

    <!-- BANDEAU DÉFILANT EN BAS (LIGHT) -->
    <footer class="tv-ticker">
        <div class="ticker-label">
            <i class="fa-solid fa-circle-info"></i>
            <span>Information</span>
        </div>
        <div class="ticker-content-wrapper">
            <div class="ticker-content">
                Bienvenue au {{ $hospital->label ?? 'Centre Hospitalier' }} • Veuillez garder le silence en salle d'attente • Présentez-vous au cabinet dès l'annonce de votre nom par le haut-parleur • Pour toute urgence, adressez-vous immédiatement à l'accueil.
            </div>
        </div>
    </footer>

    <!-- SCRIPTS JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            // Configuration des routes (Public ou Session)
            @if(isset($isPublic) && $isPublic)
                const updatesUrl = "{{ route('tv.waiting_screen.updates', $token) }}";
                const testCallUrl = "{{ route('tv.waiting_screen.test', $token) }}";
                const ttsAudioUrl = "{{ route('tv.waiting_screen.tts', $token) }}";
            @else
                const updatesUrl = "{{ route('hospital.waiting_screen.updates') }}";
                const testCallUrl = "{{ route('hospital.waiting_screen.test') }}";
                const ttsAudioUrl = "{{ route('hospital.waiting_screen.tts') }}";
            @endif

            let lastKnownId = {{ $currentCall ? $currentCall->id : 0 }};
            let announcedCallIds = new Set();
            @if($currentCall)
                announcedCallIds.add({{ $currentCall->id }});
            @endif

            let audioAllowed = false;
            let audioContext = null;
            let speechQueue = [];
            let isSpeaking = false;
            const updateIntervalMs = 3000;

            // 1. HORLOGE NUMÉRIQUE & DATE
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds}`;

                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('liveDate').textContent = now.toLocaleDateString('fr-FR', options);
            }
            setInterval(updateClock, 1000);
            updateClock();

            // 2. SYNTHÈSE SONORE DU CARILLON (Ding-Dong)
            function playHospitalChime() {
                try {
                    const ctx = audioContext || new (window.AudioContext || window.webkitAudioContext)();
                    if (ctx.state === 'suspended') {
                        ctx.resume();
                    }

                    const now = ctx.currentTime;
                    
                    // Note 1 (Mi - 659.25 Hz)
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(659.25, now);
                    gain1.gain.setValueAtTime(0.35, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.8);

                    // Note 2 (Do - 523.25 Hz)
                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(523.25, now + 0.35);
                    gain2.gain.setValueAtTime(0.35, now + 0.35);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.4);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(now + 0.35);
                    osc2.stop(now + 1.4);

                } catch (e) {
                    console.warn('Audio Web API :', e);
                }
            }

            // Système de voix et SpeechSynthesis robuste
            let systemVoices = [];
            function updateVoices() {
                if ('speechSynthesis' in window) {
                    systemVoices = window.speechSynthesis.getVoices() || [];
                }
            }
            if ('speechSynthesis' in window) {
                updateVoices();
                if (window.speechSynthesis.onvoiceschanged !== undefined) {
                    window.speechSynthesis.onvoiceschanged = updateVoices;
                }
                // Keep-alive pour empêcher Chrome de suspendre la synthèse vocale en arrière-plan
                setInterval(() => {
                    if (window.speechSynthesis.paused) {
                        window.speechSynthesis.resume();
                    }
                }, 5000);
            }

            function findBestFrenchVoice() {
                if (!systemVoices.length && 'speechSynthesis' in window) {
                    systemVoices = window.speechSynthesis.getVoices() || [];
                }
                if (!systemVoices.length) return null;

                const frVoices = systemVoices.filter(v => v.lang && (v.lang.toLowerCase().startsWith('fr') || v.lang.toLowerCase().includes('fre')));
                if (!frVoices.length) return null;

                const highQuality = frVoices.find(v => {
                    const name = (v.name || '').toLowerCase();
                    return name.includes('natural') || name.includes('google') || name.includes('premium') || 
                           name.includes('hortense') || name.includes('julie') || name.includes('paul') || 
                           name.includes('thomas') || name.includes('denise') || name.includes('henri') || 
                           name.includes('celine') || name.includes('mathieu') || name.includes('french');
                });
                if (highQuality) return highQuality;

                const frFr = frVoices.find(v => v.lang.toLowerCase().replace('_', '-').startsWith('fr-fr'));
                if (frFr) return frFr;

                return frVoices[0];
            }

            // 3. SYNTHÈSE VOCALE HYBRIDE (Flux Audio MP3 HD + Fallback Web Speech API)
            window._currentSpeechUtterance = null;
            let activeVoiceAudio = null;

            function playNativeWebSpeech(text) {
                if (!('speechSynthesis' in window)) {
                    isSpeaking = false;
                    processSpeechQueue();
                    return;
                }
                try {
                    if (window.speechSynthesis.paused) {
                        window.speechSynthesis.resume();
                    }

                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'fr-FR';
                    utterance.rate = 0.92;
                    utterance.pitch = 1.0;
                    utterance.volume = 1.0;

                    const chosenVoice = findBestFrenchVoice();
                    if (chosenVoice) {
                        utterance.voice = chosenVoice;
                    }

                    window._currentSpeechUtterance = utterance;

                    utterance.onstart = function() {
                        isSpeaking = true;
                    };

                    utterance.onend = function() {
                        isSpeaking = false;
                        window._currentSpeechUtterance = null;
                        setTimeout(processSpeechQueue, 300);
                    };

                    utterance.onerror = function(e) {
                        console.warn('Erreur SpeechSynthesis fallback :', e);
                        isSpeaking = false;
                        window._currentSpeechUtterance = null;
                        setTimeout(processSpeechQueue, 300);
                    };

                    isSpeaking = true;
                    window.speechSynthesis.speak(utterance);

                    setTimeout(() => {
                        if (isSpeaking) {
                            isSpeaking = false;
                            window._currentSpeechUtterance = null;
                            processSpeechQueue();
                        }
                    }, 12000);
                } catch (err) {
                    console.error('Erreur fallback speech :', err);
                    isSpeaking = false;
                    window._currentSpeechUtterance = null;
                    processSpeechQueue();
                }
            }

            // Lecture directe via Web Audio API (le même canal AudioContext que le carillon)
            function playVoiceViaAudioContext(arrayBuffer) {
                return new Promise((resolve, reject) => {
                    try {
                        const ctx = audioContext || new (window.AudioContext || window.webkitAudioContext)();
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }

                        const onDecodeSuccess = function(decodedBuffer) {
                            try {
                                const source = ctx.createBufferSource();
                                source.buffer = decodedBuffer;
                                source.connect(ctx.destination);
                                source.onended = function() {
                                    isSpeaking = false;
                                    resolve();
                                    setTimeout(processSpeechQueue, 300);
                                };
                                source.start(0);
                            } catch (e) {
                                reject(e);
                            }
                        };

                        const onDecodeError = function(err) {
                            reject(err);
                        };

                        // Compatibilité standard et syntaxe callback Android TV WebView
                        const res = ctx.decodeAudioData(arrayBuffer, onDecodeSuccess, onDecodeError);
                        if (res && typeof res.then === 'function') {
                            res.catch(onDecodeError);
                        }
                    } catch (e) {
                        reject(e);
                    }
                });
            }

            function announcePatientVocally(text) {
                // 1. Jouer d'abord le carillon sonore
                playHospitalChime();

                // 2. Lancer la voix après le carillon (850ms)
                setTimeout(() => {
                    isSpeaking = true;
                    const audioUrl = ttsAudioUrl + '?text=' + encodeURIComponent(text);

                    // PRIORITÉ 1 (Spécial Android TV / Smart TV) : Web Audio API directe via AudioContext
                    fetch(audioUrl)
                        .then(response => {
                            if (!response.ok) throw new Error('HTTP status ' + response.status);
                            return response.arrayBuffer();
                        })
                        .then(buffer => {
                            return playVoiceViaAudioContext(buffer);
                        })
                        .catch(err => {
                            console.warn('WebAudio direct échoué, essai lecteur HTML5 Audio :', err);
                            // PRIORITÉ 2 : Lecteur HTML5 Audio standard
                            try {
                                if (activeVoiceAudio) {
                                    try { activeVoiceAudio.pause(); } catch(e) {}
                                }
                                activeVoiceAudio = new Audio(audioUrl);
                                activeVoiceAudio.volume = 1.0;
                                activeVoiceAudio.onended = function() {
                                    isSpeaking = false;
                                    setTimeout(processSpeechQueue, 300);
                                };
                                activeVoiceAudio.onerror = function() {
                                    playNativeWebSpeech(text);
                                };
                                const playPromise = activeVoiceAudio.play();
                                if (playPromise !== undefined) {
                                    playPromise.catch(() => playNativeWebSpeech(text));
                                }
                            } catch (e2) {
                                playNativeWebSpeech(text);
                            }
                        });

                    // Timeout de sécurité si la lecture reste bloquée
                    setTimeout(() => {
                        if (isSpeaking) {
                            isSpeaking = false;
                            processSpeechQueue();
                        }
                    }, 12000);
                }, 850);
            }

            function queueSpeech(text) {
                // Ne garder que le dernier appel si plus de 2 en attente
                if (speechQueue.length > 1) {
                    speechQueue = [text];
                } else {
                    speechQueue.push(text);
                }

                if (!isSpeaking) {
                    processSpeechQueue();
                }
            }

            function processSpeechQueue() {
                if (speechQueue.length > 0 && !isSpeaking) {
                    const nextText = speechQueue.shift();
                    announcePatientVocally(nextText);
                }
            }

            // 4. MISE À JOUR VISUELLE DU PATIENT EN COURS / RÉINITIALISATION
            function resetHeroDisplay() {
                const heroCard = document.getElementById('heroCard');
                if (heroCard) {
                    heroCard.classList.remove('is-calling');
                }
                const soundWave = document.getElementById('soundWaveVisualizer');
                if (soundWave) {
                    soundWave.style.opacity = '0.3';
                }
                const display = document.getElementById('heroPatientDisplay');
                if (display) {
                    display.innerHTML = `
                        <div class="empty-call-state">
                            <div class="empty-call-icon-box">
                                <i class="fa-solid fa-user-clock"></i>
                            </div>
                            <h3 class="empty-call-title">En attente du prochain appel...</h3>
                            <p class="empty-call-desc">Les patients appelés par les médecins s'afficheront ici automatiquement.</p>
                        </div>
                    `;
                }
                const detailsGrid = document.getElementById('heroDetailsGrid');
                if (detailsGrid) {
                    detailsGrid.style.display = 'none';
                }
            }

            function updateHeroDisplay(call) {
                if (!call) {
                    resetHeroDisplay();
                    return;
                }

                const heroCard = document.getElementById('heroCard');
                if (heroCard) {
                    heroCard.classList.add('is-calling');
                }

                const soundWave = document.getElementById('soundWaveVisualizer');
                if (soundWave) {
                    soundWave.style.opacity = '1';
                }

                const display = document.getElementById('heroPatientDisplay');
                if (display) {
                    display.innerHTML = `
                        <div class="patient-label">Veuillez vous présenter en consultation</div>
                        <div id="heroPatientName" class="patient-name">${escapeHtml(call.patient_name)}</div>
                        <div id="heroPatientService" class="patient-subcode">
                            <i class="fa-solid fa-stethoscope me-1 text-primary"></i> ${escapeHtml(call.service_name || 'Consultation médicale')}
                        </div>
                    `;
                }

                const doctorEl = document.getElementById('heroDoctorName');
                if (doctorEl) doctorEl.textContent = call.doctor_name;

                const cabinetEl = document.getElementById('heroCabinet');
                if (cabinetEl) cabinetEl.textContent = call.cabinet || 'Cabinet de consultation';

                const detailsGrid = document.getElementById('heroDetailsGrid');
                if (detailsGrid) detailsGrid.style.display = 'grid';
            }

            // 5. MISE À JOUR DE LA LISTE DES DERNIERS APPELS (6 MAX)
            function updateRecentCallsList(calls) {
                const list = document.getElementById('recentCallsList');
                if (!calls || calls.length === 0) {
                    list.innerHTML = `
                        <div id="noRecentCalls" class="text-center py-5 text-muted">
                            <i class="fa-regular fa-clipboard fs-32 mb-2 opacity-50"></i>
                            <p class="fs-14">Aucun appel enregistré pour aujourd'hui.</p>
                        </div>
                    `;
                    document.getElementById('callCountBadge').textContent = '0 appel';
                    return;
                }

                calls = calls.slice(0, 6);
                document.getElementById('callCountBadge').textContent = `${calls.length} appel(s)`;
                let html = '';
                calls.forEach(c => {
                    html += `
                        <div class="recent-item">
                            <div>
                                <div class="recent-patient-name">${escapeHtml(c.patient_name)}</div>
                                <div class="recent-doctor">
                                    <i class="fa-solid fa-user-doctor text-success fs-11"></i>
                                    <span>${escapeHtml(c.doctor_name)}</span>
                                    <span class="text-muted">• ${escapeHtml(c.cabinet || 'Cabinet')}</span>
                                </div>
                            </div>
                            <div class="recent-badge">
                                <i class="fa-regular fa-clock me-1"></i>
                                <span>${c.time}</span>
                            </div>
                        </div>
                    `;
                });
                list.innerHTML = html;
            }

            function escapeHtml(text) {
                if (!text) return '';
                const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            // 6. POLLING CONTINU
            function pollWaitingScreenUpdates() {
                $.ajax({
                    url: updatesUrl,
                    type: 'GET',
                    data: {
                        last_id: lastKnownId
                    },
                    timeout: 5000,
                    success: function(data) {
                        if (data && data.success) {
                            if (data.latest_id) {
                                lastKnownId = data.latest_id;
                            }

                            if (data.new_calls && data.new_calls.length > 0) {
                                data.new_calls.forEach(call => {
                                    if (!announcedCallIds.has(call.id)) {
                                        announcedCallIds.add(call.id);
                                        if (audioAllowed) {
                                            queueSpeech(call.spoken_text);
                                        }
                                    }
                                });
                            }

                            if (data.current_call) {
                                updateHeroDisplay(data.current_call);
                            } else {
                                resetHeroDisplay();
                            }

                            if (data.recent_calls) {
                                updateRecentCallsList(data.recent_calls);
                            }
                        }
                    },
                    error: function(err) {
                        console.warn('Polling attente...', err);
                    },
                    complete: function() {
                        setTimeout(pollWaitingScreenUpdates, updateIntervalMs);
                    }
                });
            }

            // 7. INITIALISATION DU SON
            function initAudioContext() {
                try {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    if (audioContext.state === 'suspended') {
                        audioContext.resume();
                    }
                    audioAllowed = true;
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.getVoices();
                    }
                } catch (e) {
                    console.error('AudioContext error :', e);
                }
            }

            function performAudioUnlock() {
                initAudioContext();
                const modal = document.getElementById('audioUnlockModal');
                if (modal) {
                    modal.style.display = 'none';
                }
                playHospitalChime(); // Son carillon bref pour débloquer l'audio
                
                // Débloquer également la synthèse vocale sur les navigateurs stricts
                if ('speechSynthesis' in window) {
                    try {
                        window.speechSynthesis.resume();
                        const unlockUtterance = new SpeechSynthesisUtterance('');
                        unlockUtterance.volume = 0.01;
                        window.speechSynthesis.speak(unlockUtterance);
                    } catch(e) {}
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('audioUnlockModal');
                if (modal) {
                    modal.style.display = 'flex';
                }

                const unlockBtn = document.getElementById('btnUnlockAudio');
                if (unlockBtn) {
                    unlockBtn.addEventListener('click', performAudioUnlock);
                    setTimeout(() => unlockBtn.focus(), 300);
                }

                // Déverrouillage automatique au premier bouton pressé sur la télécommande TV
                document.addEventListener('keydown', function(e) {
                    const m = document.getElementById('audioUnlockModal');
                    if (m && m.style.display !== 'none') {
                        performAudioUnlock();
                    }
                }, { once: true });

                setTimeout(pollWaitingScreenUpdates, 1500);
            });

            // 8. CONTRÔLES (TEST, PLEIN ÉCRAN, THEME, COPIER LIEN)
            document.getElementById('btnTestSound').addEventListener('click', function() {
                initAudioContext();
                announcePatientVocally("Ceci est un test sonore du système d'appel en salle d'attente.");
            });

            document.getElementById('btnSimulateCall').addEventListener('click', function() {
                initAudioContext();
                $.ajax({
                    url: testCallUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        patient_name: 'M. KOUASSI YAO JEAN',
                        doctor_name: 'Dr. KOUAME KONAN',
                        cabinet: 'Cabinet 01 - Consultation Générale'
                    },
                    success: function(resp) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Appel test envoyé !',
                            toast: true,
                            position: 'top-end',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            // Plein écran
            document.getElementById('btnFullscreen').addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.warn(`Fullscreen error: ${err.message}`);
                    });
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    }
                }
            });

            // Basculer Thème Clair / Sombre
            document.getElementById('btnToggleTheme').addEventListener('click', function() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                html.setAttribute('data-theme', newTheme);
                this.innerHTML = newTheme === 'light' ? '<i class="fa-solid fa-moon"></i>' : '<i class="fa-solid fa-sun text-warning"></i>';
            });

            // Modal TV Android
            const tvModal = document.getElementById('tvLinkModal');
            document.getElementById('btnOpenTvModal').addEventListener('click', function() {
                tvModal.style.display = 'flex';
            });
            document.getElementById('btnCloseTvModal').addEventListener('click', function() {
                tvModal.style.display = 'none';
            });
            document.getElementById('btnDismissTvModal').addEventListener('click', function() {
                tvModal.style.display = 'none';
            });

            // Copier le lien
            document.getElementById('btnCopyTvUrl').addEventListener('click', function() {
                const input = document.getElementById('inputTvUrl');
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(input.value).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Lien copié !',
                        text: 'Vous pouvez coller ce lien dans le navigateur de votre TV Android.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                });
            });

        })();
    </script>
</body>
</html>
