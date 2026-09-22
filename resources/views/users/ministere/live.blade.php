<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OBSERVATOIRE NATIONAL - STATISTIQUES NAISSANCES & DÉCÈS (LIVE 24/7)</title>

    <!-- Fonts Google & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --bg-card-hover: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --accent-blue: #0284c7;
            --accent-green: #059669;
            --accent-red: #dc2626;
            --accent-amber: #d97706;
            --accent-purple: #7c3aed;
            --header-bg: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        /* En-tête Salle de Contrôle */
        .live-header {
            background-color: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 28px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .republique-badge {
            font-size: 0.75rem;
            letter-spacing: 2px;
            color: var(--text-secondary);
            font-weight: 700;
            text-transform: uppercase;
        }

        .header-title {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(220, 38, 38, 0.08);
            border: 1px solid rgba(220, 38, 38, 0.25);
            color: #dc2626;
            padding: 5px 14px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .live-dot {
            width: 9px;
            height: 9px;
            background-color: #dc2626;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #dc2626;
            animation: pulseDot 1.4s infinite ease-in-out;
        }

        @keyframes pulseDot {
            0% {
                transform: scale(0.9);
                opacity: 0.4;
            }

            50% {
                transform: scale(1.3);
                opacity: 1;
            }

            100% {
                transform: scale(0.9);
                opacity: 0.4;
            }
        }

        .clock-display {
            font-family: 'Outfit', sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--accent-blue);
            letter-spacing: 1px;
            line-height: 1;
        }

        .date-display {
            font-size: 0.82rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* Cartes & Widgets */
        .wall-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .kpi-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 130px;
        }

        .kpi-title {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kpi-value {
            font-size: 2.35rem;
            font-weight: 800;
            line-height: 1;
            margin: 8px 0;
            font-family: 'Outfit', sans-serif;
        }

        .kpi-subtext {
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Barres de progression genre */
        .gender-progress {
            height: 7px;
            border-radius: 10px;
            background-color: #e2e8f0;
            overflow: hidden;
            display: flex;
            margin: 6px 0;
        }

        /* Titres de section graphes */
        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .chart-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Ticker Défilant 24/7 en bas */
        .live-ticker-bar {
            background-color: var(--header-bg);
            border-top: 1px solid var(--border-color);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            overflow: hidden;
            white-space: nowrap;
            position: sticky;
            bottom: 0;
            z-index: 999;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.03);
        }

        .ticker-label {
            background: var(--accent-blue);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.75rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 6px;
            margin-right: 15px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ticker-content {
            display: inline-block;
            white-space: nowrap;
            animation: tickerScroll 40s linear infinite;
            color: var(--text-secondary);
            font-size: 0.88rem;
            font-weight: 600;
        }

        .ticker-content:hover {
            animation-play-state: paused;
        }

        @keyframes tickerScroll {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .ticker-item {
            display: inline-flex;
            align-items: center;
            margin-right: 35px;
            gap: 6px;
        }

        .ticker-item-badge {
            font-size: 0.7rem;
            padding: 2px 7px;
            border-radius: 4px;
            font-weight: 700;
        }

        /* Boutons de commande d'affichage */
        .btn-screen-control {
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.82rem;
            font-weight: 600;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .btn-screen-control:hover {
            background: #f8fafc;
            color: var(--text-primary);
            border-color: #cbd5e1;
        }

        /* Animation pulsation synchronisation */
        .sync-active {
            animation: rotateSync 1s ease-in-out;
        }

        @keyframes rotateSync {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ==========================================================
           CINÉMATIQUE DIRECTIONNELLE :
           - NAISSANCE : Quitte en HAUT -> Rentre en BAS
           - DÉCÈS     : Quitte en BAS  -> S'envole en HAUT
           ========================================================== */
        .celebration-overlay {
            position: fixed;
            inset: 0;
            background: transparent !important;
            backdrop-filter: none !important;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            opacity: 0;
            pointer-events: none !important;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }

        .celebration-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        /* Conteneur circulaire */
        .celebration-circle-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
        }

        /* 1. NAISSANCE : Descend du HAUT vers le centre */
        #birthCelebrationOverlay.active .celebration-circle-container {
            animation: birthDescendFromTop 1.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* 1. NAISSANCE : Sortie vers le BAS */
        #birthCelebrationOverlay.exiting .celebration-circle-container {
            animation: birthExitToBottom 0.9s cubic-bezier(0.7, 0, 0.84, 0) forwards !important;
        }

        /* 2. DÉCÈS : Monte du BAS vers le centre */
        #deathCelebrationOverlay.active .celebration-circle-container {
            animation: deathAscendFromBottom 1.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* 2. DÉCÈS : Sortie vers le HAUT */
        #deathCelebrationOverlay.exiting .celebration-circle-container {
            animation: deathFlyToTop 0.9s cubic-bezier(0.7, 0, 0.84, 0) forwards !important;
        }

        /* KEYFRAMES NAISSANCE : Quitte en haut (petit) -> Rentre au milieu (plus grand scale 1.25) -> Sort en bas (rétrécit) */
        @keyframes birthDescendFromTop {
            0% {
                transform: translateY(-120vh) scale(0.12) rotate(6deg);
                opacity: 0;
            }

            55% {
                transform: translateY(18px) scale(1.35) rotate(-1deg);
                opacity: 1;
            }

            80% {
                transform: translateY(-8px) scale(1.22) rotate(0.5deg);
                opacity: 1;
            }

            100% {
                transform: translateY(0) scale(1.25) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes birthExitToBottom {
            0% {
                transform: translateY(0) scale(1.25) rotate(0deg);
                opacity: 1;
            }

            20% {
                transform: translateY(-20px) scale(1.28) rotate(1deg);
                opacity: 1;
            }

            100% {
                transform: translateY(125vh) scale(0.15) rotate(-8deg);
                opacity: 0;
            }
        }

        /* KEYFRAMES DÉCÈS : Quitte en bas (petit) -> Monte au milieu (plus grand scale 1.25) -> S'envole en haut (rétrécit) */
        @keyframes deathAscendFromBottom {
            0% {
                transform: translateY(120vh) scale(0.12) rotate(-6deg);
                opacity: 0;
            }

            55% {
                transform: translateY(-18px) scale(1.35) rotate(1deg);
                opacity: 1;
            }

            80% {
                transform: translateY(8px) scale(1.22) rotate(-0.5deg);
                opacity: 1;
            }

            100% {
                transform: translateY(0) scale(1.25) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes deathFlyToTop {
            0% {
                transform: translateY(0) scale(1.25) rotate(0deg);
                opacity: 1;
            }

            20% {
                transform: translateY(20px) scale(1.28) rotate(-1deg);
                opacity: 1;
            }

            100% {
                transform: translateY(-125vh) scale(0.15) rotate(8deg);
                opacity: 0;
            }
        }

        .celebration-img-circle {
            width: 380px;
            height: 380px;
            object-fit: cover;
            border-radius: 50%;
            position: relative;
            z-index: 2;
        }

        /* Nom en bas en NOIR GRAND FORMAT */
        .celebration-circle-name {
            color: #000000 !important;
            font-weight: 900;
            font-size: 2.25rem;
            text-align: center;
            margin-top: 18px;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 12px rgba(255, 255, 255, 0.95), 0 0 20px rgba(255, 255, 255, 0.9);
            position: relative;
            z-index: 3;
            max-width: 550px;
            line-height: 1.2;
        }

        /* Effets Naissance (Cercle Bébé) */
        .birth-circle-glow {
            box-shadow: 0 0 70px rgba(251, 191, 36, 0.95), 0 0 130px rgba(2, 132, 199, 0.8), 0 0 220px rgba(251, 191, 36, 0.5);
            border: 6px solid #fde047;
            animation: birthCircleFloat 3.5s infinite ease-in-out;
        }

        .birth-circle-sunburst {
            position: absolute;
            top: -45px;
            left: 50%;
            transform: translateX(-50%);
            width: 470px;
            height: 470px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(251, 191, 36, 0.5) 0%, rgba(2, 132, 199, 0.3) 50%, transparent 70%);
            animation: sunburstCircleRotate 12s linear infinite;
            z-index: 1;
        }

        /* Effets Décès (Cercle Colombe) */
        .death-circle-glow {
            box-shadow: 0 0 80px rgba(255, 255, 255, 0.95), 0 0 140px rgba(220, 38, 38, 0.6), 0 0 220px rgba(100, 116, 139, 0.4);
            border: 6px solid #ffffff;
            animation: deathCircleAscend 3.5s infinite ease-in-out;
        }

        .death-circle-halo {
            position: absolute;
            top: -45px;
            left: 50%;
            transform: translateX(-50%);
            width: 470px;
            height: 470px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.45) 0%, rgba(220, 38, 38, 0.25) 50%, transparent 70%);
            animation: haloCirclePulse 3s infinite ease-in-out;
            z-index: 1;
        }

        @keyframes birthCircleFloat {
            0% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-12px) scale(1.02);
            }

            100% {
                transform: translateY(0) scale(1);
            }
        }

        @keyframes deathCircleAscend {
            0% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-12px) scale(1.02);
            }

            100% {
                transform: translateY(0) scale(1);
            }
        }

        @keyframes sunburstCircleRotate {
            0% {
                transform: translateX(-50%) rotate(0deg) scale(1);
            }

            50% {
                transform: translateX(-50%) rotate(180deg) scale(1.1);
            }

            100% {
                transform: translateX(-50%) rotate(360deg) scale(1);
            }
        }

        @keyframes haloCirclePulse {
            0% {
                transform: translateX(-50%) scale(0.92);
                opacity: 0.5;
            }

            50% {
                transform: translateX(-50%) scale(1.18);
                opacity: 0.95;
            }

            100% {
                transform: translateX(-50%) scale(0.92);
                opacity: 0.5;
            }
        }
    </style>
</head>

<body>

    <!-- OVERLAY D'ANIMATION : CERCLE DU BÉBÉ + NOM EN NOIR -->
    <div id="birthCelebrationOverlay" class="celebration-overlay">
        <div class="celebration-circle-container">
            <div class="birth-circle-sunburst"></div>
            <img src="{{ asset('assets/live/baby_birth_live.jpg') }}" alt="Naissance"
                class="celebration-img-circle birth-circle-glow">
            <div class="celebration-circle-name" id="celebrationBirthName">Bienvenue au Nouveau-né</div>
        </div>
    </div>

    <!-- OVERLAY D'ANIMATION : CERCLE DE LA COLOMBE + NOM EN NOIR -->
    <div id="deathCelebrationOverlay" class="celebration-overlay">
        <div class="celebration-circle-container">
            <div class="death-circle-halo"></div>
            <img src="{{ asset('assets/live/dove_death_live.jpg') }}" alt="Décès"
                class="celebration-img-circle death-circle-glow">
            <div class="celebration-circle-name" id="celebrationDeathName">Hommage & Déclaration</div>
        </div>
    </div>

    <!-- EN-TÊTE SUPÉRIEURE SALLE DE CONTRÔLE -->
    <header class="live-header">
        <div class="row align-items-center g-3">
            <!-- Logo & Titre Institutionnel -->
            <div class="col-lg-5 col-md-6 col-12 d-flex align-items-center gap-3">
                <div
                    style="background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(2, 132, 199, 0.25); border-radius: 12px; width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fa fa-heart-pulse fa-xl" style="color: var(--accent-blue);"></i>
                </div>
                <div>
                    <div class="republique-badge">République de Côte d'Ivoire &bull; Ministère de la Santé</div>
                    <div class="header-title">Observatoire National : Naissances & Décès</div>
                </div>
            </div>

            <!-- Indicateur En Direct & Statut -->
            <div
                class="col-lg-4 col-md-3 col-12 text-center d-flex align-items-center justify-content-lg-center justify-content-start gap-3">
                <div class="live-indicator">
                    <span class="live-dot"></span>
                    <span>Direct 24/7</span>
                </div>
                <div class="d-none d-xl-flex align-items-center gap-2 text-muted" style="font-size: 0.78rem;">
                    <i class="fa fa-arrows-rotate" id="syncIcon"></i>
                    <span id="syncText">Sync auto 20s</span>
                </div>
            </div>

            <!-- Horloge Grand Format & Contrôles -->
            <div
                class="col-lg-3 col-md-3 col-12 text-end d-flex align-items-center justify-content-end gap-2 flex-wrap">
                <div class="text-end me-2">
                    <div class="clock-display" id="liveClock">00:00:00</div>
                    <div class="date-display" id="liveDate">Chargement...</div>
                </div>

                <!-- Boutons de Test / Démonstration d'Animations -->
                <button class="btn-screen-control" id="btnTestBirth" title="Tester l'animation Naissance">
                    <i class="fa fa-baby text-warning"></i> <span class="d-none d-xxl-inline">Test Bébé</span>
                </button>
                <button class="btn-screen-control" id="btnTestDeath" title="Tester l'animation Décès">
                    <i class="fa fa-dove text-danger"></i> <span class="d-none d-xxl-inline">Test Décès</span>
                </button>

                <button class="btn-screen-control" id="btnFullscreen" title="Plein Écran (F11)">
                    <i class="fa fa-expand" id="fullscreenIcon"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- CONTENU PRINCIPAL DU WALLBOARD -->
    <main class="container-fluid px-4 py-3 flex-grow-1">

        <!-- LIGNE 1 : CARTES DES GRANDS INDICATEURS (KPIS) -->
        <div class="row g-3 mb-3">
            <!-- 1. Naissances Année -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="wall-card kpi-card" style="border-left: 4px solid var(--accent-blue);">
                    <div class="kpi-title">
                        <span>Naissances ({{ $year }})</span>
                        <i class="fa fa-baby" style="color: var(--accent-blue); font-size: 1.2rem;"></i>
                    </div>
                    <div class="kpi-value" style="color: var(--accent-blue);" id="kpiBirthsYear">
                        {{ number_format($totalBirthsYear, 0, ',', ' ') }}</div>
                    <div>
                        <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                            <span><i class="fa fa-mars text-primary"></i> <strong
                                    id="kpiMaleCount">{{ $birthsMale }}</strong> Garçons (<span
                                    id="kpiMalePct">{{ $totalBirthsYear > 0 ? round(($birthsMale / $totalBirthsYear) * 100, 1) : 0 }}%</span>)</span>
                            <span><i class="fa fa-venus text-danger"></i> <strong
                                    id="kpiFemaleCount">{{ $birthsFemale }}</strong> Filles (<span
                                    id="kpiFemalePct">{{ $totalBirthsYear > 0 ? round(($birthsFemale / $totalBirthsYear) * 100, 1) : 0 }}%</span>)</span>
                        </div>
                        <div class="gender-progress">
                            <div id="barMale"
                                style="width: {{ $totalBirthsYear > 0 ? ($birthsMale / $totalBirthsYear) * 100 : 50 }}%; background: #0284c7;">
                            </div>
                            <div id="barFemale"
                                style="width: {{ $totalBirthsYear > 0 ? ($birthsFemale / $totalBirthsYear) * 100 : 50 }}%; background: #e11d48;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Décès Année -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="wall-card kpi-card" style="border-left: 4px solid var(--accent-red);">
                    <div class="kpi-title">
                        <span>Décès ({{ $year }})</span>
                        <i class="fa fa-cross" style="color: var(--accent-red); font-size: 1.2rem;"></i>
                    </div>
                    <div class="kpi-value text-danger" id="kpiDeathsYear">
                        {{ number_format($totalDeathsYear, 0, ',', ' ') }}</div>
                    <div class="d-flex justify-content-between align-items-center kpi-subtext">
                        <span>Hommes : <strong id="kpiDeathsMaleCount">{{ $deathsMale }}</strong> | Femmes : <strong
                                id="kpiDeathsFemaleCount">{{ $deathsFemale }}</strong></span>
                        <span class="badge bg-danger-subtle text-danger">Mortalité</span>
                    </div>
                </div>
            </div>

            <!-- 3. Mortalité Maternelle & Taux -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="wall-card kpi-card" style="border-left: 4px solid var(--accent-amber);">
                    <div class="kpi-title">
                        <span>Mortalité Maternelle</span>
                        <i class="fa fa-female" style="color: var(--accent-amber); font-size: 1.2rem;"></i>
                    </div>
                    <div class="kpi-value text-warning" id="kpiMaternalYear">
                        {{ number_format($maternalDeathsYear, 0, ',', ' ') }}</div>
                    <div class="d-flex justify-content-between align-items-center kpi-subtext">
                        <span>Taux estimé : <strong
                                id="kpiMaternalRate">{{ $totalBirthsYear > 0 ? round(($maternalDeathsYear / $totalBirthsYear) * 1000, 2) : 0 }}
                                ‰</strong></span>
                        <span class="badge bg-warning-subtle text-warning">Surveillance</span>
                    </div>
                </div>
            </div>

            <!-- 4. Solde Naturel (Naissances - Décès) -->
            <div class="col-xl-3 col-md-6 col-12">
                <div class="wall-card kpi-card" style="border-left: 4px solid var(--accent-green);">
                    <div class="kpi-title">
                        <span>Accroissement Naturel</span>
                        <i class="fa fa-scale-balanced" style="color: var(--accent-green); font-size: 1.2rem;"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-baseline">
                        <div class="kpi-value text-success" id="kpiNaturalBalance">
                            {{ ($totalBirthsYear - $totalDeathsYear) >= 0 ? '+' . number_format($totalBirthsYear - $totalDeathsYear, 0, ',', ' ') : number_format($totalBirthsYear - $totalDeathsYear, 0, ',', ' ') }}
                        </div>
                        <div class="text-end">
                            <span class="text-muted" style="font-size: 0.72rem; text-transform: uppercase;">Ratio
                                Vital</span>
                            <div class="fw-bold" style="font-size: 1.15rem; color: var(--accent-blue);"
                                id="kpiVitalRatio">
                                {{ $totalDeathsYear > 0 ? round($totalBirthsYear / $totalDeathsYear, 2) : $totalBirthsYear }}
                                N/D
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center kpi-subtext">
                        <span>Cumul historique : <strong
                                id="kpiTotalAllText">{{ number_format($totalBirthsAll, 0, ',', ' ') }} N /
                                {{ number_format($totalDeathsAll, 0, ',', ' ') }} D</strong></span>
                        <span class="badge bg-success-subtle text-success">Démographie</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIGNE 2 : GRAPHIQUES CENTRAUX HAUTE VISIBILITÉ -->
        <div class="row g-3 mb-3">
            <!-- Évolution Mensuelle (Naissances vs Décès) -->
            <div class="col-xl-8 col-12">
                <div class="wall-card">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fa fa-chart-line" style="color: var(--accent-blue);"></i>
                            <span>Dynamique Mensuelle Nationale : Naissances vs Décès ({{ $year }})</span>
                        </div>
                        <div class="d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                            <span class="d-flex align-items-center gap-1"><span
                                    style="display:inline-block; width:12px; height:3px; background:#0284c7; border-radius:2px;"></span>
                                Naissances</span>
                            <span class="d-flex align-items-center gap-1"><span
                                    style="display:inline-block; width:12px; height:3px; background:#dc2626; border-radius:2px;"></span>
                                Décès</span>
                        </div>
                    </div>
                    <div style="height: 310px; position: relative;">
                        <canvas id="liveMonthlyTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Répartition par Genre des Naissances -->
            <div class="col-xl-4 col-12">
                <div class="wall-card">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fa fa-venus-mars" style="color: var(--accent-purple);"></i>
                            <span>Naissances par Genre ({{ $year }})</span>
                        </div>
                    </div>
                    <div style="height: 235px; position: relative;">
                        <canvas id="liveGenderBirthChart"></canvas>
                    </div>
                    <div class="row text-center mt-2 pt-2 border-top"
                        style="border-color: var(--border-color) !important;">
                        <div class="col-6 border-end" style="border-color: var(--border-color) !important;">
                            <span class="text-muted"
                                style="font-size: 0.75rem; text-transform: uppercase;">Garçons</span>
                            <div class="fw-bold" style="color: #0284c7; font-size: 1.15rem;" id="genderMaleLiveCount">
                                {{ $birthsMale }}</div>
                        </div>
                        <div class="col-6">
                            <span class="text-muted"
                                style="font-size: 0.75rem; text-transform: uppercase;">Filles</span>
                            <div class="fw-bold" style="color: #e11d48; font-size: 1.15rem;" id="genderFemaleLiveCount">
                                {{ $birthsFemale }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIGNE 3 : MORTALITÉ PAR ÂGE & COMPARAISON TRIMESTRIELLE -->
        <div class="row g-3">
            <!-- Mortalité par Tranches d'Âge -->
            <div class="col-xl-6 col-12">
                <div class="wall-card">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fa fa-user-clock" style="color: var(--accent-red);"></i>
                            <span>Mortalité par Tranches d'Âge ({{ $year }})</span>
                        </div>
                    </div>
                    <div style="height: 250px; position: relative;">
                        <canvas id="liveAgeDeathChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Comparatif Trimestriel : Naissances vs Décès -->
            <div class="col-xl-6 col-12">
                <div class="wall-card">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fa fa-chart-column" style="color: var(--accent-green);"></i>
                            <span>Bilan Trimestriel : Naissances vs Décès ({{ $year }})</span>
                        </div>
                        <div class="d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                            <span class="d-flex align-items-center gap-1"><span
                                    style="display:inline-block; width:10px; height:10px; background:#0284c7; border-radius:2px;"></span>
                                Naissances</span>
                            <span class="d-flex align-items-center gap-1"><span
                                    style="display:inline-block; width:10px; height:10px; background:#dc2626; border-radius:2px;"></span>
                                Décès</span>
                        </div>
                    </div>
                    <div style="height: 250px; position: relative;">
                        <canvas id="liveQuarterlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- BANDEAU DÉFILANT EN CONTINU (LIVE TICKER 24/7) -->
    <footer class="live-ticker-bar">
        <div class="ticker-label">
            <i class="fa fa-bolt"></i> DERNIÈRES DÉCLARATIONS
        </div>
        <div class="overflow-hidden w-100 position-relative">
            <div class="ticker-content" id="liveTickerContent">
                @foreach($recentBirths as $b)
                    <span class="ticker-item">
                        <span class="ticker-item-badge bg-primary text-white">NAISSANCE</span>
                        <span>{{ $b->enfant->user->name ?? 'Enfant' }} {{ $b->enfant->user->prenom ?? '' }}
                            ({{ $b->genre == 'M' || strtolower($b->genre) == 'masculin' ? 'Garçon' : 'Fille' }}) &bull;
                            {{ $b->date ? \Carbon\Carbon::parse($b->date)->format('d/m/Y') : '' }} &bull; Réf:
                            {{ $b->numero_declaration ?: ($b->reference ?: '#' . $b->id) }}</span>
                    </span>
                @endforeach
                @foreach($recentDeaths as $d)
                    <span class="ticker-item">
                        <span class="ticker-item-badge bg-danger text-white">DÉCÈS</span>
                        <span>{{ $d->person == 'enfant' ? 'Nouveau-né' : (($d->declaration->patient->user->name ?? 'Patient') . ' ' . ($d->declaration->patient->user->prenom ?? '')) }}
                            &bull; Cause: {{ $d->cause_initiale ?: ($d->cause_directe ?: 'Non précisée') }} &bull;
                            {{ $d->date ? \Carbon\Carbon::parse($d->date)->format('d/m/Y') : '' }}</span>
                    </span>
                @endforeach
            </div>
        </div>
    </footer>

    <!-- SCRIPTS CHART.JS & MOTEUR CINÉMATIQUE 24/7 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            // --- 1. HORLOGE EN DIRECT & DATE (FR) ---
            function updateClock() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('liveClock').textContent = `${hours}:${minutes}:${seconds}`;

                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateStr = now.toLocaleDateString('fr-FR', options);
                document.getElementById('liveDate').textContent = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
            }
            setInterval(updateClock, 1000);
            updateClock();

            // --- 2. GESTION DU MODE PLEIN ÉCRAN ---
            const btnFullscreen = document.getElementById('btnFullscreen');
            const fullscreenIcon = document.getElementById('fullscreenIcon');
            btnFullscreen.addEventListener('click', function () {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log(`Erreur plein écran: ${err.message}`);
                    });
                    fullscreenIcon.classList.remove('fa-expand');
                    fullscreenIcon.classList.add('fa-compress');
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                        fullscreenIcon.classList.remove('fa-compress');
                        fullscreenIcon.classList.add('fa-expand');
                    }
                }
            });

            // --- 3. INITIALISATION DES GRAPHIQUES CHART.JS EN MODE CLAIR ---
            const monthsLabels = @json($monthsLabels);
            let birthsMonthlyData = @json(array_values($birthsMonthly));
            let deathsMonthlyData = @json(array_values($deathsMonthly));

            function calculateQuarters(bArr, dArr) {
                const qBirths = [
                    (bArr[0] || 0) + (bArr[1] || 0) + (bArr[2] || 0),
                    (bArr[3] || 0) + (bArr[4] || 0) + (bArr[5] || 0),
                    (bArr[6] || 0) + (bArr[7] || 0) + (bArr[8] || 0),
                    (bArr[9] || 0) + (bArr[10] || 0) + (bArr[11] || 0)
                ];
                const qDeaths = [
                    (dArr[0] || 0) + (dArr[1] || 0) + (dArr[2] || 0),
                    (dArr[3] || 0) + (dArr[4] || 0) + (dArr[5] || 0),
                    (dArr[6] || 0) + (dArr[7] || 0) + (dArr[8] || 0),
                    (dArr[9] || 0) + (dArr[10] || 0) + (dArr[11] || 0)
                ];
                return { qBirths, qDeaths };
            }

            const quartersInitial = calculateQuarters(birthsMonthlyData, deathsMonthlyData);

            // 3.1 Graphe Évolution Mensuelle
            const ctxMonthly = document.getElementById('liveMonthlyTrendChart').getContext('2d');
            const chartMonthly = new Chart(ctxMonthly, {
                type: 'line',
                data: {
                    labels: monthsLabels,
                    datasets: [
                        {
                            label: 'Naissances',
                            data: birthsMonthlyData,
                            borderColor: '#0284c7',
                            backgroundColor: 'rgba(2, 132, 199, 0.08)',
                            borderWidth: 3.5,
                            tension: 0.35,
                            fill: true,
                            pointBackgroundColor: '#0284c7',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8
                        },
                        {
                            label: 'Décès',
                            data: deathsMonthlyData,
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.08)',
                            borderWidth: 3.5,
                            tension: 0.35,
                            fill: true,
                            pointBackgroundColor: '#dc2626',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 8
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: '#475569' },
                            grid: { color: 'rgba(0, 0, 0, 0.05)' }
                        },
                        x: {
                            ticks: { color: '#475569' },
                            grid: { display: false }
                        }
                    }
                }
            });

            // 3.2 Graphe Genre Donut
            const ctxGender = document.getElementById('liveGenderBirthChart').getContext('2d');
            const chartGender = new Chart(ctxGender, {
                type: 'doughnut',
                data: {
                    labels: ['Garçons', 'Filles', 'Non précisé'],
                    datasets: [{
                        data: [{{ $birthsMale }}, {{ $birthsFemale }}, {{ $birthsOther }}],
                        backgroundColor: ['#0284c7', '#e11d48', '#94a3b8'],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#475569', font: { size: 11 } }
                        }
                    }
                }
            });

            // 3.3 Graphe Tranches d'Âge
            let ageCategories = @json($ageCategories);
            const ctxAge = document.getElementById('liveAgeDeathChart').getContext('2d');
            const chartAge = new Chart(ctxAge, {
                type: 'bar',
                data: {
                    labels: Object.keys(ageCategories),
                    datasets: [{
                        label: 'Décès',
                        data: Object.values(ageCategories),
                        backgroundColor: [
                            '#ea580c',
                            '#d97706',
                            '#65a30d',
                            '#dc2626',
                            '#9333ea',
                            '#64748b'
                        ],
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: '#475569' },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        y: {
                            ticks: { color: '#475569' },
                            grid: { display: false }
                        }
                    }
                }
            });

            // 3.4 Graphe Trimestriel (Naissances vs Décès)
            const ctxQuarter = document.getElementById('liveQuarterlyChart').getContext('2d');
            const chartQuarter = new Chart(ctxQuarter, {
                type: 'bar',
                data: {
                    labels: ['Trimestre 1 (T1)', 'Trimestre 2 (T2)', 'Trimestre 3 (T3)', 'Trimestre 4 (T4)'],
                    datasets: [
                        {
                            label: 'Naissances',
                            data: quartersInitial.qBirths,
                            backgroundColor: '#0284c7',
                            borderRadius: 6
                        },
                        {
                            label: 'Décès',
                            data: quartersInitial.qDeaths,
                            backgroundColor: '#dc2626',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: '#475569' },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            ticks: { color: '#475569' },
                            grid: { display: false }
                        }
                    }
                }
            });

            // ==========================================================
            // 4. MOTEUR D'ANIMATION DU CERCLE FLOTTANT + NOM EN NOIR
            // ==========================================================
            let animationQueue = [];
            let isAnimating = false;

            function showBirthAnimation(data) {
                const overlay = document.getElementById('birthCelebrationOverlay');
                if (!overlay) return;

                const nameElem = document.getElementById('celebrationBirthName');
                if (nameElem) {
                    nameElem.textContent = data.nom || 'Bienvenue au Nouveau-né';
                }

                overlay.classList.remove('exiting');
                overlay.classList.add('active');
                isAnimating = true;

                // Reste 6.5 secondes puis s'envole vers le haut
                setTimeout(() => {
                    overlay.classList.add('exiting');
                    setTimeout(() => {
                        overlay.classList.remove('active', 'exiting');
                        isAnimating = false;
                        processNextAnimation();
                    }, 850);
                }, 6000);
            }

            function showDeathAnimation(data) {
                const overlay = document.getElementById('deathCelebrationOverlay');
                if (!overlay) return;

                const nameElem = document.getElementById('celebrationDeathName');
                if (nameElem) {
                    nameElem.textContent = data.nom || 'Hommage & Déclaration';
                }

                overlay.classList.remove('exiting');
                overlay.classList.add('active');
                isAnimating = true;

                // Reste 6.5 secondes puis s'envole vers le haut
                setTimeout(() => {
                    overlay.classList.add('exiting');
                    setTimeout(() => {
                        overlay.classList.remove('active', 'exiting');
                        isAnimating = false;
                        processNextAnimation();
                    }, 850);
                }, 6000);
            }

            function triggerCelebration(type, payload) {
                animationQueue.push({ type, payload });
                if (!isAnimating) {
                    processNextAnimation();
                }
            }

            function processNextAnimation() {
                if (animationQueue.length === 0 || isAnimating) return;
                const next = animationQueue.shift();
                if (next.type === 'birth') {
                    showBirthAnimation(next.payload);
                } else if (next.type === 'death') {
                    showDeathAnimation(next.payload);
                }
            }

            // Boutons de test manuels pour l'administrateur
            document.getElementById('btnTestBirth').addEventListener('click', function () {
                triggerCelebration('birth', {
                    nom: 'Enfant KOUASSI Amani',
                    genre: 'masculin',
                    date: 'Aujourd\'hui à ' + new Date().toLocaleTimeString('fr-FR')
                });
            });

            document.getElementById('btnTestDeath').addEventListener('click', function () {
                triggerCelebration('death', {
                    nom: 'Hommage à KONE Mamadou',
                    cause: 'Arrêt cardio-respiratoire',
                    date: 'Aujourd\'hui à ' + new Date().toLocaleTimeString('fr-FR')
                });
            });

            // --- 5. SUIVI EN TEMPS RÉEL DU COMPTEUR ET DÉCLENCHEMENT AUTO ---
            let lastKnownTotalBirths = parseInt("{{ $totalBirthsYear }}") || 0;
            let lastKnownTotalDeaths = parseInt("{{ $totalDeathsYear }}") || 0;
            let isInitialLoad = true;

            const syncIcon = document.getElementById('syncIcon');
            const syncText = document.getElementById('syncText');

            function fetchLiveData() {
                syncIcon.classList.add('sync-active');

                fetch("{{ route('ministere.live.data') }}?year={{ $year }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Erreur réseau');
                        return res.json();
                    })
                    .then(data => {
                        const currentBirths = parseInt(String(data.totalBirthsYear).replace(/\s+/g, '')) || 0;
                        const currentDeaths = parseInt(String(data.totalDeathsYear).replace(/\s+/g, '')) || 0;

                        // Si une nouvelle naissance est détectée en direct
                        if (!isInitialLoad && currentBirths > lastKnownTotalBirths) {
                            const latestBirth = (data.recentBirths && data.recentBirths.length > 0) ? data.recentBirths[0] : {
                                nom: 'Nouveau-né enregistré',
                                genre: 'M',
                                date: 'À l\'instant'
                            };
                            triggerCelebration('birth', latestBirth);
                        }

                        // Si un nouveau décès est détecté en direct
                        if (!isInitialLoad && currentDeaths > lastKnownTotalDeaths) {
                            const latestDeath = (data.recentDeaths && data.recentDeaths.length > 0) ? data.recentDeaths[0] : {
                                nom: 'Déclaration enregistrée',
                                cause: 'Cause médicale',
                                date: 'À l\'instant'
                            };
                            triggerCelebration('death', latestDeath);
                        }

                        lastKnownTotalBirths = currentBirths;
                        lastKnownTotalDeaths = currentDeaths;
                        isInitialLoad = false;

                        // Mise à jour des KPIs
                        document.getElementById('kpiBirthsYear').textContent = data.totalBirthsYear;
                        document.getElementById('kpiDeathsYear').textContent = data.totalDeathsYear;
                        document.getElementById('kpiMaternalYear').textContent = data.maternalDeathsYear;
                        document.getElementById('kpiMaternalRate').textContent = `${data.maternalRate} ‰`;

                        document.getElementById('kpiMaleCount').textContent = data.birthsMale;
                        document.getElementById('kpiFemaleCount').textContent = data.birthsFemale;
                        document.getElementById('kpiMalePct').textContent = `${data.birthsMalePct}%`;
                        document.getElementById('kpiFemalePct').textContent = `${data.birthsFemalePct}%`;

                        document.getElementById('genderMaleLiveCount').textContent = data.birthsMale;
                        document.getElementById('genderFemaleLiveCount').textContent = data.birthsFemale;

                        document.getElementById('barMale').style.width = `${data.birthsMalePct}%`;
                        document.getElementById('barFemale').style.width = `${data.birthsFemalePct}%`;

                        // Solde naturel & Ratio vital
                        const balance = currentBirths - currentDeaths;
                        document.getElementById('kpiNaturalBalance').textContent = (balance >= 0 ? '+' : '') + new Intl.NumberFormat('fr-FR').format(balance);

                        const vitalRatio = currentDeaths > 0 ? (currentBirths / currentDeaths).toFixed(2) : currentBirths;
                        document.getElementById('kpiVitalRatio').textContent = `${vitalRatio} N/D`;

                        document.getElementById('kpiTotalAllText').textContent = `${data.totalBirthsAll} N / ${data.totalDeathsAll} D`;

                        // Graphe Mensuel
                        chartMonthly.data.datasets[0].data = data.birthsMonthly;
                        chartMonthly.data.datasets[1].data = data.deathsMonthly;
                        chartMonthly.update();

                        // Graphe Genre
                        chartGender.data.datasets[0].data = [data.birthsMale, data.birthsFemale, data.birthsOther];
                        chartGender.update();

                        // Graphe Tranches d'Âge
                        chartAge.data.labels = Object.keys(data.ageCategories);
                        chartAge.data.datasets[0].data = Object.values(data.ageCategories);
                        chartAge.update();

                        // Graphe Trimestriel
                        const updatedQuarters = calculateQuarters(data.birthsMonthly, data.deathsMonthly);
                        chartQuarter.data.datasets[0].data = updatedQuarters.qBirths;
                        chartQuarter.data.datasets[1].data = updatedQuarters.qDeaths;
                        chartQuarter.update();

                        // Ticker Content
                        if (data.recentBirths || data.recentDeaths) {
                            let tickerHtml = '';
                            if (data.recentBirths) {
                                data.recentBirths.forEach(b => {
                                    let gText = b.genre && ['m', 'masculin'].includes(b.genre.toLowerCase()) ? 'Garçon' : 'Fille';
                                    tickerHtml += `
                                <span class="ticker-item">
                                    <span class="ticker-item-badge bg-primary text-white">NAISSANCE</span>
                                    <span>${b.nom} (${gText}) &bull; ${b.date} &bull; Réf: ${b.numero}</span>
                                </span>
                            `;
                                });
                            }
                            if (data.recentDeaths) {
                                data.recentDeaths.forEach(d => {
                                    let mat = d.deces_maternel ? ' [Décès Maternel]' : '';
                                    tickerHtml += `
                                <span class="ticker-item">
                                    <span class="ticker-item-badge bg-danger text-white">DÉCÈS</span>
                                    <span>${d.nom}${mat} &bull; Cause: ${d.cause} &bull; ${d.date}</span>
                                </span>
                            `;
                                });
                            }
                            if (tickerHtml.trim().length > 0) {
                                document.getElementById('liveTickerContent').innerHTML = tickerHtml;
                            }
                        }

                        syncText.textContent = `Sync : ${data.updated_at}`;
                    })
                    .catch(err => {
                        console.warn('Tentative de reconnexion au serveur...', err);
                        syncText.textContent = `Reconnexion...`;
                    })
                    .finally(() => {
                        setTimeout(() => {
                            syncIcon.classList.remove('sync-active');
                        }, 1000);
                    });
            }

            // Intervalle automatique chaque 20 secondes
            setInterval(fetchLiveData, 20000);

            // Auto-rechargement propre de sécurité toutes les 6 heures pour pérennité 24/7 sur Smart TV
            setTimeout(function () {
                window.location.reload();
            }, 6 * 60 * 60 * 1000);

        })();
    </script>
</body>

</html>