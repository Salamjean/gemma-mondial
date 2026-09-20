<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Avis Médical Expert &bull; Téléconsultation GEMMA</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- LiveKit SDK -->
    <script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>

    <style>
        :root {
            --header-height: 60px;
            --controls-height: 70px;
            --teal-color: #0d9488;
            --teal-hover: #0f766e;
            --teal-light: rgba(13, 148, 136, 0.15);
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #090d16;
            color: #f8fafc;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* En-tête Responsive */
        .expert-header {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 10px 16px;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            z-index: 20;
            flex-shrink: 0;
        }

        .header-title-box {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .header-brand {
            font-weight: 700;
            font-size: 15px;
            color: #ffffff;
            white-space: nowrap;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-badges-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow: hidden;
        }

        .badge-patient-info {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid #475569;
            color: #e2e8f0;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            max-width: 220px;
        }

        .badge-doc-ref {
            background: rgba(13, 148, 136, 0.15);
            border: 1px solid #0d9488;
            color: #2dd4bf;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            white-space: nowrap;
        }

        /* Conteneur principal */
        .main-call-container {
            flex: 1;
            display: flex;
            position: relative;
            height: calc(100vh - var(--header-height));
            height: calc(100dvh - var(--header-height));
            overflow: hidden;
        }

        /* Espace Vidéo */
        .video-stage {
            flex: 1;
            background: #090d16;
            display: flex;
            flex-direction: column;
            position: relative;
            min-width: 0;
            height: 100%;
            overflow: hidden;
        }

        .video-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 12px;
            padding: 14px;
            overflow-y: auto;
            align-content: center;
            justify-content: center;
        }

        /* Grille adaptée selon le nombre d'éléments */
        .video-grid:has(.video-card:only-child) {
            grid-template-columns: 1fr;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
        }

        .video-card {
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 16 / 9;
            min-height: 180px;
            max-height: calc(100vh - 220px);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            margin: 0 auto;
            width: 100%;
        }

        .video-card video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .participant-badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.15);
            z-index: 10;
            max-width: calc(100% - 20px);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Barre de Contrôles */
        .call-controls {
            background: #1e293b;
            border-top: 1px solid #334155;
            padding: 10px 16px;
            height: var(--controls-height);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-shrink: 0;
            z-index: 15;
        }

        .control-btn {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            border: none;
            transition: transform 0.15s ease, background 0.2s ease;
            color: #ffffff;
            flex-shrink: 0;
        }

        .control-btn:active {
            transform: scale(0.92);
        }

        .control-btn-light {
            background: #334155;
        }

        .control-btn-light:hover {
            background: #475569;
        }

        .control-btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .control-btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        /* Sidebar Dossier Clinique */
        .clinical-sidebar {
            width: 360px;
            background: #1e293b;
            border-left: 1px solid #334155;
            overflow-y: auto;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 30;
        }

        .sidebar-header-mobile {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px solid #334155;
            margin-bottom: 8px;
        }

        .patient-card-header {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            padding: 16px;
            border-radius: 12px;
            color: #ffffff;
        }

        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .vital-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 10px;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 25;
        }

        /* Overlay Pre-Join */
        .prejoin-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.98);
            backdrop-filter: blur(16px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            overflow-y: auto;
        }

        .prejoin-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            width: 100%;
            max-width: 480px;
            padding: 28px 22px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            text-align: center;
            margin: auto;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991.98px) {
            .badge-doc-ref {
                display: none;
            }

            .badge-patient-info {
                max-width: 150px;
            }

            /* La sidebar devient un drawer offcanvas rétractable sur tablette et mobile */
            .clinical-sidebar {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                width: min(380px, 85vw);
                height: 100%;
                height: 100dvh;
                transform: translateX(100%);
                box-shadow: -10px 0 30px rgba(0,0,0,0.6);
            }

            .clinical-sidebar.active {
                transform: translateX(0);
            }

            .sidebar-header-mobile {
                display: flex;
            }

            .sidebar-backdrop.active {
                display: block;
            }
        }

        @media (max-width: 575.98px) {
            :root {
                --header-height: 54px;
                --controls-height: 64px;
            }

            .expert-header {
                padding: 8px 12px;
            }

            .header-brand {
                font-size: 13px;
            }

            .badge-patient-info {
                display: none;
            }

            .video-grid {
                grid-template-columns: 1fr;
                padding: 8px;
                gap: 8px;
            }

            .video-card {
                aspect-ratio: 16 / 10;
                min-height: 160px;
            }

            .call-controls {
                padding: 8px 12px;
                gap: 8px;
            }

            .control-btn {
                width: 42px;
                height: 42px;
                font-size: 15px;
            }

            .prejoin-card {
                padding: 22px 16px;
                border-radius: 16px;
            }

            .prejoin-card h3 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

    @php
        $patientUser = optional(optional($consultation->patient)->user);
        $patientName = trim(($patientUser->name ?? '') . ' ' . ($patientUser->prenom ?? '')) ?: 'Patient';
        $docUser = optional(optional($consultation->doctor)->user);
        $docName = trim(($docUser->name ?? '') . ' ' . ($docUser->prenom ?? '')) ?: 'Médecin traitant';
        $infUser = optional(optional($consultation->infirmier)->user);
        $infName = trim(($infUser->name ?? '') . ' ' . ($infUser->prenom ?? '')) ?: 'Infirmier';
        $serviceName = optional(optional(optional($consultation->prestationHospital)->serviceHospital)->service)->libelle 
            ?? optional(optional(optional($consultation->prestationHospital)->prestationService)->service)->libelle 
            ?? 'Consultation générale';
        
        $myDoctorName = $currentUser ? trim($currentUser->name . ' ' . ($currentUser->prenom ?? '')) : '';
        $mySpecialty = optional(optional($currentUser)->doctor)->type_name ?: '';
    @endphp

    <!-- Overlay de Connexion / Pré-appel -->
    <div id="prejoinOverlay" class="prejoin-overlay">
        <div class="prejoin-card">
            <div class="avatar text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 60px; height: 60px; background: #0d9488; font-size: 24px;">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <h3 class="fw-bold text-white mb-1">Avis d'Expert Médical</h3>
            <p class="text-secondary fs-13 mb-3">Invitation du <strong>Dr. {{ $docName }}</strong> pour apporter votre avis sur la téléconsultation.</p>

            <div class="p-3 rounded-12 bg-dark border border-secondary mb-3 text-start">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge rounded-pill px-2.5 py-1 fs-11" style="background: rgba(13, 148, 136, 0.2); color: #2dd4bf; border: 1px solid #0d9488;">
                        <i class="fa-solid fa-user-injured me-1"></i> {{ $patientName }}
                    </span>
                    <span class="badge bg-light text-dark fs-11 rounded-pill px-2.5 py-1">{{ $serviceName }}</span>
                </div>
                <div class="text-secondary fs-12">
                    <i class="fa-solid fa-user-nurse text-warning me-1"></i> Infirmier au chevet : <strong>{{ $infName }}</strong>
                </div>
            </div>

            <form id="prejoinForm" onsubmit="event.preventDefault(); startColleagueCall();">
                <div class="mb-3 text-start">
                    <label class="form-label text-light fs-12 fw-semibold">Votre Nom & Prénom <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-user-md"></i></span>
                        <input type="text" id="inputColleagueName" class="form-control bg-dark border-secondary text-white" value="{{ $myDoctorName }}" placeholder="Ex: Dr. KOUASSI Jean" required>
                    </div>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label text-light fs-12 fw-semibold">Votre Spécialité (Optionnel)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-stethoscope"></i></span>
                        <input type="text" id="inputColleagueSpecialty" class="form-control bg-dark border-secondary text-white" value="{{ $mySpecialty }}" placeholder="Ex: Cardiologue, Pédiatre...">
                    </div>
                </div>

                <button type="submit" id="btnJoinRoom" class="btn w-100 py-2.5 rounded-pill fw-bold text-white shadow fs-14 d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;">
                    <i class="fa-solid fa-video"></i>
                    <span>Entrer dans la Téléconsultation</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Header Navigation -->
    <header class="expert-header">
        <div class="header-title-box">
            <h5 class="header-brand">
                <span class="spinner-grow spinner-grow-sm text-teal" style="color: #2dd4bf; width: 8px; height: 8px;" role="status"></span>
                <span>GEMMA</span>
            </h5>
            <div class="header-badges-wrap">
                <span class="badge-patient-info" title="Patient : {{ $patientName }}">
                    <i class="fa-solid fa-user-injured me-1 text-teal" style="color: #2dd4bf;"></i> {{ $patientName }}
                </span>
                <span class="badge-doc-ref" title="Médecin Référent : Dr. {{ $docName }}">
                    <i class="fa-solid fa-user-doctor me-1"></i> Dr. {{ $docName }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="badge bg-dark border border-secondary px-2.5 py-1.5 rounded-pill font-monospace fs-12 text-light">
                <i class="fa-regular fa-clock text-warning me-1"></i> <span id="callDurationTimer">00:00</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-2.5 py-1 fs-12 d-flex align-items-center gap-1" onclick="toggleSidebar()" title="Afficher/Masquer le dossier clinique">
                <i class="fa-solid fa-clipboard-user"></i>
                <span class="d-none d-sm-inline">Dossier</span>
            </button>
        </div>
    </header>

    <!-- Backdrop pour mobile/tablette -->
    <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="closeSidebar()"></div>

    <!-- Conteneur Principal -->
    <div class="main-call-container">
        <!-- Espace Vidéo Flex Grid -->
        <div class="video-stage">
            <div id="videoGrid" class="video-grid">
                <!-- Les vidéos distantes et locales sont injectées ici dynamiquement -->
                <div id="waitingPlaceholder" class="video-card text-center p-4">
                    <div>
                        <div class="spinner-border text-teal mb-3" style="width: 2.5rem; height: 2.5rem; color: #2dd4bf;" role="status"></div>
                        <h6 class="fw-bold text-white mb-1">Connexion à la téléconsultation...</h6>
                        <p class="text-secondary fs-12 mb-0">En attente de la réception des flux vidéo et audio...</p>
                    </div>
                </div>
            </div>

            <!-- Barre de Contrôles Inférieure -->
            <div class="call-controls">
                <button type="button" id="btnToggleMic" onclick="toggleMic()" class="control-btn control-btn-light" title="Micro">
                    <i class="fa-solid fa-microphone"></i>
                </button>
                <button type="button" id="btnToggleCam" onclick="toggleCam()" class="control-btn control-btn-light" title="Caméra">
                    <i class="fa-solid fa-video"></i>
                </button>
                <button type="button" id="btnToggleScreen" onclick="toggleScreenShare()" class="control-btn control-btn-light d-none d-md-flex" title="Partager l'écran">
                    <i class="fa-solid fa-desktop"></i>
                </button>
                <button type="button" class="control-btn control-btn-light d-flex d-lg-none" onclick="toggleSidebar()" title="Voir Dossier Patient">
                    <i class="fa-solid fa-clipboard-user"></i>
                </button>
                <div class="vr bg-secondary my-1 mx-1" style="height: 24px;"></div>
                <button type="button" onclick="leaveCall()" class="control-btn control-btn-danger" title="Quitter la consultation">
                    <i class="fa-solid fa-phone-slash"></i>
                </button>
            </div>
        </div>

        <!-- Sidebar Données Cliniques & Constantes -->
        <aside id="clinicalSidebar" class="clinical-sidebar">
            <div class="sidebar-header-mobile">
                <h6 class="fw-bold text-white mb-0"><i class="fa-solid fa-clipboard-user text-teal me-2" style="color: #2dd4bf;"></i> Dossier Patient</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary text-white rounded-circle" onclick="closeSidebar()" style="width: 30px; height: 30px; padding: 0;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="patient-card-header">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white text-dark fs-10 text-uppercase fw-bold rounded-pill px-2 py-0.5">Dossier Patient</span>
                </div>
                <h5 class="fw-bold mb-1 fs-16">{{ $patientName }}</h5>
                <div class="fs-12 opacity-85">
                    @if(optional($consultation->patient)->age)
                        <span>{{ $consultation->patient->age }} ans</span> &bull;
                    @endif
                    @if(optional($consultation->patient)->genre)
                        <span>{{ $consultation->patient->genre == 'M' ? 'Masculin' : 'Féminin' }}</span> &bull;
                    @endif
                    <span>Code : <strong>{{ optional($consultation->patient)->code_patient ?? 'N/A' }}</strong></span>
                </div>
            </div>

            <!-- Constantes physiques -->
            <div>
                <h6 class="fw-bold text-light mb-2 fs-12 text-uppercase"><i class="fa-solid fa-heart-pulse text-danger me-1"></i> Constantes Physiques</h6>
                <div class="vitals-grid">
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Température</small>
                        <span class="fw-bold fs-13 text-white">{{ $consultation->temperature ? $consultation->temperature . ' °C' : 'N/A' }}</span>
                    </div>
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Tension Artérielle</small>
                        <span class="fw-bold fs-13 text-white">{{ $consultation->tension_arterielle ? $consultation->tension_arterielle . ' mmHg' : 'N/A' }}</span>
                    </div>
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Pouls</small>
                        <span class="fw-bold fs-13 text-white">{{ $consultation->pouls ? $consultation->pouls . ' bpm' : 'N/A' }}</span>
                    </div>
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Poids & Taille</small>
                        <span class="fw-bold fs-13 text-white">
                            {{ $consultation->poids ? $consultation->poids . ' kg' : '-' }} / {{ $consultation->taille ? $consultation->taille . ' cm' : '-' }}
                        </span>
                    </div>
                    @if($consultation->imc)
                    <div class="vital-box" style="grid-column: span 2;">
                        <small class="text-secondary d-block fs-11">Indice de Masse Corporelle (IMC)</small>
                        <span class="fw-bold fs-13" style="color: #2dd4bf;">{{ $consultation->imc }} kg/m²</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Motif de consultation -->
            <div>
                <h6 class="fw-bold text-light mb-2 fs-12 text-uppercase"><i class="fa-solid fa-notes-medical text-warning me-1"></i> Motif de la Demande</h6>
                <div class="p-2.5 bg-dark border border-secondary rounded-10 fs-12 text-light">
                    {{ $consultation->motif_consultation ?: 'Téléconsultation médicale demandée pour avis spécialisé.' }}
                </div>
            </div>

            <!-- Soignants présents -->
            <div>
                <h6 class="fw-bold text-light mb-2 fs-12 text-uppercase"><i class="fa-solid fa-user-group text-info me-1"></i> Équipe en Séance</h6>
                <div class="d-flex flex-column gap-2 fs-12">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-8 bg-dark border border-secondary">
                        <i class="fa-solid fa-user-doctor" style="color: #2dd4bf;"></i>
                        <div>
                            <strong class="text-white">Dr. {{ $docName }}</strong>
                            <small class="text-secondary d-block fs-11">Médecin traitant</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 p-2 rounded-8 bg-dark border border-secondary">
                        <i class="fa-solid fa-user-nurse text-warning"></i>
                        <div>
                            <strong class="text-white">Inf. {{ $infName }}</strong>
                            <small class="text-secondary d-block fs-11">Infirmier(ère) au chevet</small>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        let livekitRoom = null;
        let localAudioTrack = null;
        let localVideoTrack = null;
        let localScreenTrack = null;
        let isMicMuted = false;
        let isCamOff = false;
        let callSeconds = 0;
        let callTimer = null;

        const consultationId = "{{ $consultation->id }}";
        const inviteHash = "{{ $hash }}";

        function toggleSidebar() {
            const sidebar = document.getElementById('clinicalSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const isMobile = window.innerWidth < 992;

            if (isMobile) {
                sidebar.classList.toggle('active');
                backdrop.classList.toggle('active');
            } else {
                if (sidebar.style.display === 'none') {
                    sidebar.style.display = 'flex';
                } else {
                    sidebar.style.display = 'none';
                }
            }
        }

        function closeSidebar() {
            document.getElementById('clinicalSidebar').classList.remove('active');
            document.getElementById('sidebarBackdrop').classList.remove('active');
        }

        window.addEventListener('resize', () => {
            const sidebar = document.getElementById('clinicalSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
                sidebar.style.display = 'flex';
            }
        });

        async function startColleagueCall() {
            const colleagueName = $('#inputColleagueName').val().trim();
            const colleagueSpecialty = $('#inputColleagueSpecialty').val().trim();

            if (!colleagueName) {
                Swal.fire({ text: "Veuillez renseigner votre nom.", icon: "warning", confirmButtonColor: '#0d9488' });
                return;
            }

            $('#btnJoinRoom').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Connexion...');

            try {
                const res = await fetch(`/teleconsultation/expert/${consultationId}/${inviteHash}/token`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        colleague_name: colleagueName,
                        colleague_specialty: colleagueSpecialty
                    })
                });

                const data = await res.json();
                if (data.status === 'success') {
                    $('#prejoinOverlay').fadeOut(300);
                    connectToLiveKit(data.livekit_url, data.token, data.display_name);
                } else {
                    Swal.fire({ text: data.message || "Erreur de connexion.", icon: "error", confirmButtonColor: '#ef4444' });
                    $('#btnJoinRoom').prop('disabled', false).html('<i class="fa-solid fa-video"></i> Entrer dans la Téléconsultation');
                }
            } catch (err) {
                console.error("Erreur token expert:", err);
                Swal.fire({ text: "Impossible de joindre le serveur.", icon: "error", confirmButtonColor: '#ef4444' });
                $('#btnJoinRoom').prop('disabled', false).html('<i class="fa-solid fa-video"></i> Entrer dans la Téléconsultation');
            }
        }

        async function connectToLiveKit(url, token, displayName) {
            try {
                const LiveKit = window.LivekitClient || window.LiveKit;
                const RoomClass = LiveKit?.Room || LiveKit?.default?.Room;

                livekitRoom = new RoomClass({
                    adaptiveStream: true,
                    dynacast: true,
                });

                // Démarrer le timer
                callTimer = setInterval(() => {
                    callSeconds++;
                    const mins = String(Math.floor(callSeconds / 60)).padStart(2, '0');
                    const secs = String(callSeconds % 60).padStart(2, '0');
                    document.getElementById('callDurationTimer').innerText = `${mins}:${secs}`;
                }, 1000);

                // Événements de pistes distantes
                livekitRoom.on(LiveKit.RoomEvent.TrackSubscribed, (track, publication, participant) => {
                    $('#waitingPlaceholder').remove();
                    attachParticipantTrack(track, participant);
                });

                livekitRoom.on(LiveKit.RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
                    detachParticipantTrack(track, participant);
                });

                livekitRoom.on(LiveKit.RoomEvent.ParticipantDisconnected, (participant) => {
                    $(`#card-${participant?.identity}`).remove();
                    if (participant && !participant.identity.startsWith('colleague_')) {
                        // Raccrochage direct chez le confrère
                        if (livekitRoom) {
                            try { livekitRoom.disconnect(); } catch(e) {}
                        }
                        $('#videoGrid').html(`
                            <div class="video-card text-center p-4">
                                <div>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 2px solid #ef4444;">
                                        <i class="fa-solid fa-phone-slash fs-24"></i>
                                    </div>
                                    <h5 class="fw-bold text-white mb-1">Téléconsultation clôturée</h5>
                                    <p class="text-secondary fs-13 mb-3">Le médecin traitant a terminé la session de téléconsultation.</p>
                                    <a href="{{ url('/') }}" class="btn px-4 py-2 rounded-pill fw-bold" style="background: #0d9488; color: #ffffff;">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Retour à l'accueil
                                    </a>
                                </div>
                            </div>
                        `);
                    }
                });

                livekitRoom.on(LiveKit.RoomEvent.Disconnected, () => {
                    $('#videoGrid').html(`
                        <div class="video-card text-center p-4">
                            <div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 2px solid #ef4444;">
                                    <i class="fa-solid fa-phone-slash fs-24"></i>
                                </div>
                                <h5 class="fw-bold text-white mb-1">Téléconsultation terminée</h5>
                                <p class="text-secondary fs-13 mb-3">La session vidéo a pris fin.</p>
                                <a href="{{ url('/') }}" class="btn px-4 py-2 rounded-pill fw-bold" style="background: #0d9488; color: #ffffff;">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    `);
                });

                // Rejoindre la room
                await livekitRoom.connect(url, token);
                console.log("Connecté à la room LiveKit en tant qu'expert !");

                // Créer et publier les pistes locales (Micro + Caméra)
                try {
                    localAudioTrack = await LiveKit.createLocalAudioTrack({ echoCancellation: true, noiseSuppression: true });
                    await livekitRoom.localParticipant.publishTrack(localAudioTrack);
                } catch(e) { console.warn("Erreur audio local:", e); }

                try {
                    localVideoTrack = await LiveKit.createLocalVideoTrack({ resolution: LiveKit.VideoPresets.h720 });
                } catch(e) {
                    console.warn("Caméra physique non accessible, utilisation du flux de secours animé:", e);
                    try {
                        const fallbackMediaStreamTrack = createColleagueFallbackVideoTrack(displayName);
                        localVideoTrack = new LiveKit.LocalVideoTrack(fallbackMediaStreamTrack);
                    } catch(err2) {
                        console.warn("Erreur fallback vidéo:", err2);
                    }
                }

                if (localVideoTrack) {
                    try {
                        await livekitRoom.localParticipant.publishTrack(localVideoTrack);
                        addLocalVideoCard(localVideoTrack, displayName + " (Vous)");
                    } catch(e) {
                        console.warn("Erreur publication vidéo locale:", e);
                    }
                }

            } catch (e) {
                console.error("Erreur LiveKit:", e);
                Swal.fire({ text: "Erreur lors de la connexion LiveKit: " + e.message, icon: "error" });
            }
        }

        function createColleagueFallbackVideoTrack(label = "Dr. Expert") {
            const canvas = document.createElement("canvas");
            canvas.width = 640;
            canvas.height = 480;
            const ctx = canvas.getContext("2d");

            let angle = 0;
            const draw = () => {
                angle += 0.05;
                const grad = ctx.createLinearGradient(0, 0, 640, 480);
                grad.addColorStop(0, "#0f172a");
                grad.addColorStop(0.5, "#1e293b");
                grad.addColorStop(1, "#0d9488");
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, 640, 480);

                ctx.beginPath();
                ctx.arc(320, 200, 65 + Math.sin(angle) * 8, 0, Math.PI * 2);
                ctx.fillStyle = "rgba(13, 148, 136, 0.35)";
                ctx.fill();

                ctx.beginPath();
                ctx.arc(320, 200, 52, 0, Math.PI * 2);
                ctx.fillStyle = "#0d9488";
                ctx.fill();
                ctx.lineWidth = 3;
                ctx.strokeStyle = "#ffffff";
                ctx.stroke();

                ctx.font = "bold 32px sans-serif";
                ctx.fillStyle = "#ffffff";
                ctx.textAlign = "center";
                ctx.fillText("EXP", 320, 212);

                ctx.font = "bold 20px sans-serif";
                ctx.fillStyle = "#e2e8f0";
                ctx.fillText(label, 320, 305);

                ctx.font = "14px sans-serif";
                ctx.fillStyle = "#94a3b8";
                ctx.fillText("Médecin Expert Invité", 320, 332);

                requestAnimationFrame(draw);
            };
            draw();

            const stream = canvas.captureStream(15);
            return stream.getVideoTracks()[0];
        }

        function addLocalVideoCard(track, name) {
            $('#waitingPlaceholder').remove();
            const cardId = 'card-local-expert';
            if (document.getElementById(cardId)) return;

            const card = document.createElement('div');
            card.id = cardId;
            card.className = 'video-card';

            const videoEl = track.attach();
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            videoEl.style.objectFit = 'cover';
            card.appendChild(videoEl);

            const badge = document.createElement('div');
            badge.className = 'participant-badge';
            badge.innerHTML = `<i class="fa-solid fa-user-doctor me-1 text-teal" style="color:#2dd4bf;"></i> ${name}`;
            card.appendChild(badge);

            document.getElementById('videoGrid').appendChild(card);
        }

        function attachParticipantTrack(track, participant) {
            const grid = document.getElementById('videoGrid');
            const cardId = `card-${participant.identity}`;

            if (track.kind === 'video') {
                let card = document.getElementById(cardId);
                if (!card) {
                    card = document.createElement('div');
                    card.id = cardId;
                    card.className = 'video-card';

                    const badge = document.createElement('div');
                    badge.className = 'participant-badge';
                    badge.innerHTML = `<i class="fa-solid fa-video me-1 text-teal" style="color:#2dd4bf;"></i> ${participant.name || participant.identity}`;
                    card.appendChild(badge);
                    grid.appendChild(card);
                }

                const videoEl = track.attach();
                videoEl.autoplay = true;
                videoEl.playsInline = true;
                videoEl.style.width = '100%';
                videoEl.style.height = '100%';
                videoEl.style.objectFit = 'cover';
                card.insertBefore(videoEl, card.firstChild);
            } else if (track.kind === 'audio') {
                const audioEl = track.attach();
                audioEl.id = `audio-${participant.identity}`;
                document.body.appendChild(audioEl);
                audioEl.play().catch(e => {
                    if (livekitRoom) livekitRoom.startAudio();
                });
            }
        }

        function detachParticipantTrack(track, participant) {
            if (track.kind === 'video') {
                $(`#card-${participant.identity} video`).remove();
            } else if (track.kind === 'audio') {
                $(`#audio-${participant.identity}`).remove();
            }
        }

        async function toggleMic() {
            if (!localAudioTrack) return;
            isMicMuted = !isMicMuted;
            if (isMicMuted) {
                await localAudioTrack.mute();
                $('#btnToggleMic').removeClass('control-btn-light').addClass('control-btn-danger').html('<i class="fa-solid fa-microphone-slash"></i>');
            } else {
                await localAudioTrack.unmute();
                $('#btnToggleMic').removeClass('control-btn-danger').addClass('control-btn-light').html('<i class="fa-solid fa-microphone"></i>');
            }
        }

        async function toggleCam() {
            if (!localVideoTrack) return;
            isCamOff = !isCamOff;
            if (isCamOff) {
                await localVideoTrack.mute();
                $('#btnToggleCam').removeClass('control-btn-light').addClass('control-btn-danger').html('<i class="fa-solid fa-video-slash"></i>');
            } else {
                await localVideoTrack.unmute();
                $('#btnToggleCam').removeClass('control-btn-danger').addClass('control-btn-light').html('<i class="fa-solid fa-video"></i>');
            }
        }

        async function toggleScreenShare() {
            try {
                if (!localScreenTrack) {
                    localScreenTrack = await navigator.mediaDevices.getDisplayMedia({ video: true });
                    const screenTrack = new LiveKit.LocalVideoTrack(localScreenTrack.getVideoTracks()[0]);
                    await livekitRoom.localParticipant.publishTrack(screenTrack);
                    $('#btnToggleScreen').removeClass('control-btn-light').addClass('btn-success text-white');
                    screenTrack.mediaStreamTrack.onended = () => { toggleScreenShare(); };
                } else {
                    localScreenTrack.getTracks().forEach(t => t.stop());
                    localScreenTrack = null;
                    $('#btnToggleScreen').removeClass('btn-success text-white').addClass('control-btn-light');
                }
            } catch (e) {
                console.warn("Screen share error:", e);
            }
        }

        function leaveCall() {
            Swal.fire({
                title: 'Quitter la téléconsultation ?',
                text: 'Vous allez vous déconnecter du salon médical.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#334155',
                confirmButtonText: 'Oui, quitter',
                cancelButtonText: 'Annuler'
            }).then((res) => {
                if (res.isConfirmed) {
                    if (livekitRoom) {
                        livekitRoom.disconnect();
                    }
                    window.location.href = "{{ url('/') }}";
                }
            });
        }
    </script>
</body>
</html>
