<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .expert-header {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 12px 24px;
        }
        .main-call-container {
            flex: 1;
            display: flex;
            height: calc(100vh - 65px);
            overflow: hidden;
        }
        .video-stage {
            flex: 1;
            background: #090d16;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .video-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 12px;
            padding: 16px;
            overflow-y: auto;
            align-content: center;
        }
        .video-card {
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            min-height: 240px;
            max-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .video-card video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .participant-badge {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.15);
            z-index: 10;
        }
        .call-controls {
            background: #1e293b;
            border-top: 1px solid #334155;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }
        .control-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            border: none;
            transition: all 0.2s;
            color: #ffffff;
        }
        .control-btn-light {
            background: #334155;
        }
        .control-btn-light:hover {
            background: #475569;
            transform: scale(1.05);
        }
        .control-btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        .control-btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            transform: scale(1.05);
        }
        .clinical-sidebar {
            width: 380px;
            background: #1e293b;
            border-left: 1px solid #334155;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .patient-card-header {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            padding: 18px;
            border-radius: 14px;
            color: #ffffff;
        }
        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .vital-box {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 12px;
        }
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
            padding: 20px;
        }
        .prejoin-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 24px;
            width: 100%;
            max-width: 520px;
            padding: 35px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            text-align: center;
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
            <div class="avatar bg-teal text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; background: #0d9488; font-size: 28px;">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <h3 class="fw-bold text-white mb-1">Invitation Médicale &bull; Avis d'Expert</h3>
            <p class="text-secondary fs-14 mb-4">Vous êtes invité(e) par le <strong>Dr. {{ $docName }}</strong> pour apporter votre expertise sur une téléconsultation en direct.</p>

            <div class="p-3 rounded-12 bg-dark border border-secondary mb-4 text-start">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-teal-subtle text-teal-300 border border-teal-500 rounded-pill px-2.5 py-1 fs-11" style="background: rgba(13, 148, 136, 0.2); color: #2dd4bf;">
                        <i class="fa-solid fa-hospital me-1"></i> Patient : {{ $patientName }}
                    </span>
                    <span class="badge bg-light text-dark fs-11 rounded-pill px-2.5 py-1">{{ $serviceName }}</span>
                </div>
                <div class="text-secondary fs-12">
                    <i class="fa-solid fa-user-nurse text-warning me-1"></i> Infirmier(ère) présent(e) : <strong>{{ $infName }}</strong>
                </div>
            </div>

            <form id="prejoinForm" onsubmit="event.preventDefault(); startColleagueCall();">
                <div class="mb-3 text-start">
                    <label class="form-label text-light fs-13 fw-semibold">Votre Nom & Prénom <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-user-md"></i></span>
                        <input type="text" id="inputColleagueName" class="form-control bg-dark border-secondary text-white" value="{{ $myDoctorName }}" placeholder="Ex: Dr. KOUASSI Jean" required>
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label text-light fs-13 fw-semibold">Votre Spécialité médicale (Optionnel)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-secondary text-secondary"><i class="fa-solid fa-stethoscope"></i></span>
                        <input type="text" id="inputColleagueSpecialty" class="form-control bg-dark border-secondary text-white" value="{{ $mySpecialty }}" placeholder="Ex: Cardiologue, Pédiatre, Généraliste...">
                    </div>
                </div>

                <button type="submit" id="btnJoinRoom" class="btn btn-teal w-100 py-3 rounded-pill fw-bold text-white shadow-lg fs-15 d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;">
                    <i class="fa-solid fa-video"></i>
                    <span>Entrer dans la Téléconsultation</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Header Navigation -->
    <header class="expert-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="spinner-grow spinner-grow-sm text-teal" style="color: #2dd4bf;" role="status"></span>
                <h5 class="fw-bold text-white mb-0 fs-16"><i class="fa-solid fa-hospital-user text-teal me-2" style="color: #2dd4bf;"></i> GEMMA Téléconsultation</h5>
            </div>
            <span class="badge bg-dark border border-secondary text-light px-3 py-1.5 rounded-pill fs-12">
                <i class="fa-solid fa-user-injured me-1 text-teal" style="color: #2dd4bf;"></i> Patient : <strong>{{ $patientName }}</strong>
            </span>
            <span class="badge bg-teal-subtle border border-teal-500 rounded-pill px-3 py-1.5 fs-12" style="background: rgba(13, 148, 136, 0.2); color: #2dd4bf;">
                <i class="fa-solid fa-user-doctor me-1"></i> Médecin Référent : Dr. {{ $docName }}
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="badge bg-dark border border-secondary px-3 py-1.5 rounded-pill font-monospace fs-13 text-light">
                <i class="fa-regular fa-clock text-warning me-1"></i> <span id="callDurationTimer">00:00</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" onclick="toggleSidebar()" title="Afficher/Masquer le dossier clinique">
                <i class="fa-solid fa-clipboard-user me-1"></i> Dossier Patient
            </button>
        </div>
    </header>

    <!-- Conteneur Principal -->
    <div class="main-call-container">
        <!-- Espace Vidéo Flex Grid -->
        <div class="video-stage">
            <div id="videoGrid" class="video-grid">
                <!-- Les vidéos distantes et locales sont injectées ici dynamiquement -->
                <div id="waitingPlaceholder" class="video-card w-100 h-100 text-center p-4">
                    <div>
                        <div class="spinner-border text-teal mb-3" style="width: 3rem; height: 3rem; color: #2dd4bf;" role="status"></div>
                        <h5 class="fw-bold text-white mb-1">Connexion au salon médical en cours...</h5>
                        <p class="text-secondary fs-13 mb-0">En attente de la réception des flux vidéo et audio...</p>
                    </div>
                </div>
            </div>

            <!-- Barre de Contrôles Inférieure -->
            <div class="call-controls">
                <button type="button" id="btnToggleMic" onclick="toggleMic()" class="control-btn control-btn-light" title="Couper/Activer le micro">
                    <i class="fa-solid fa-microphone"></i>
                </button>
                <button type="button" id="btnToggleCam" onclick="toggleCam()" class="control-btn control-btn-light" title="Couper/Activer la caméra">
                    <i class="fa-solid fa-video"></i>
                </button>
                <button type="button" id="btnToggleScreen" onclick="toggleScreenShare()" class="control-btn control-btn-light" title="Partager l'écran">
                    <i class="fa-solid fa-desktop"></i>
                </button>
                <div class="vr bg-secondary my-1 mx-2" style="height: 30px;"></div>
                <button type="button" onclick="leaveCall()" class="control-btn control-btn-danger" title="Quitter la consultation">
                    <i class="fa-solid fa-phone-slash"></i>
                </button>
            </div>
        </div>

        <!-- Sidebar Données Cliniques & Constantes -->
        <div id="clinicalSidebar" class="clinical-sidebar">
            <div class="patient-card-header">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-white text-dark fs-10 text-uppercase fw-bold rounded-pill px-2 py-0.5">Dossier Patient</span>
                </div>
                <h5 class="fw-bold mb-1 fs-17">{{ $patientName }}</h5>
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
                <h6 class="fw-bold text-light mb-2 fs-13 text-uppercase"><i class="fa-solid fa-heart-pulse text-danger me-1"></i> Constantes Physiques</h6>
                <div class="vitals-grid">
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Température</small>
                        <span class="fw-bold fs-14 text-white">{{ $consultation->temperature ? $consultation->temperature . ' °C' : 'N/A' }}</span>
                    </div>
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Tension Artérielle</small>
                        <span class="fw-bold fs-14 text-white">{{ $consultation->tension_arterielle ? $consultation->tension_arterielle . ' mmHg' : 'N/A' }}</span>
                    </div>
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Pouls</small>
                        <span class="fw-bold fs-14 text-white">{{ $consultation->pouls ? $consultation->pouls . ' bpm' : 'N/A' }}</span>
                    </div>
                    <div class="vital-box">
                        <small class="text-secondary d-block fs-11">Poids & Taille</small>
                        <span class="fw-bold fs-14 text-white">
                            {{ $consultation->poids ? $consultation->poids . ' kg' : '-' }} / {{ $consultation->taille ? $consultation->taille . ' cm' : '-' }}
                        </span>
                    </div>
                    @if($consultation->imc)
                    <div class="vital-box col-span-2" style="grid-column: span 2;">
                        <small class="text-secondary d-block fs-11">Indice de Masse Corporelle (IMC)</small>
                        <span class="fw-bold fs-14 text-teal" style="color: #2dd4bf;">{{ $consultation->imc }} kg/m²</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Motif de consultation -->
            <div>
                <h6 class="fw-bold text-light mb-2 fs-13 text-uppercase"><i class="fa-solid fa-notes-medical text-warning me-1"></i> Motif de la Demande</h6>
                <div class="p-3 bg-dark border border-secondary rounded-12 fs-13 text-light">
                    {{ $consultation->motif_consultation ?: 'Téléconsultation médicale demandée pour avis spécialisé.' }}
                </div>
            </div>

            <!-- Soignants présents -->
            <div>
                <h6 class="fw-bold text-light mb-2 fs-13 text-uppercase"><i class="fa-solid fa-user-group text-info me-1"></i> Équipe en Séance</h6>
                <div class="d-flex flex-column gap-2 fs-13">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-8 bg-dark border border-secondary">
                        <i class="fa-solid fa-user-doctor text-teal" style="color: #2dd4bf;"></i>
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
        </div>
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
            if (sidebar.style.display === 'none') {
                sidebar.style.display = 'flex';
            } else {
                sidebar.style.display = 'none';
            }
        }

        async function startColleagueCall() {
            const colleagueName = $('#inputColleagueName').val().trim();
            const colleagueSpecialty = $('#inputColleagueSpecialty').val().trim();

            if (!colleagueName) {
                Swal.fire({ text: "Veuillez renseigner votre nom.", icon: "warning", confirmButtonColor: '#0d9488' });
                return;
            }

            $('#btnJoinRoom').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Connexion sécurisée...');

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
                    $(`#card-${participant.identity}`).remove();
                    if (participant && !participant.identity.startsWith('colleague_')) {
                        Swal.fire({
                            text: "L'appel de téléconsultation a été terminé par le poste principal.",
                            icon: "info",
                            confirmButtonColor: '#0d9488'
                        });
                    }
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
