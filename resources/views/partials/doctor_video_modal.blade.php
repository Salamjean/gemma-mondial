<!-- Modal Consultation Vidéo en Ligne (LiveKit Cloud) Design Premium Light pour Médecin -->
<style>
    #remote-video-container {
        position: relative !important;
        width: 100% !important;
        height: 100% !important;
        min-width: 100% !important;
        min-height: 100% !important;
        flex: 1 1 auto !important;
        overflow: hidden !important;
        background: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    #remote-video-container video {
        width: 100% !important;
        height: 100% !important;
        min-width: 100% !important;
        min-height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
        display: block !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
    }
    #local-video-container video {
        width: 100% !important;
        height: 100% !important;
        min-width: 100% !important;
        min-height: 100% !important;
        object-fit: cover !important;
        display: block !important;
    }
</style>
<div class="modal fade" id="doctorVideoCallModal" tabindex="-1" aria-labelledby="doctorVideoCallModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered my-1" style="max-width: 98vw !important; width: 98vw !important; transition: all 0.3s ease-in-out;">
        <div class="modal-content border-0 shadow-2xl rounded-4" style="overflow: hidden !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important; border-radius: 16px !important; box-shadow: 0 25px 60px rgba(0,0,0,0.3) !important;">
            
            <!-- Header Modal Light -->
            <div class="modal-header border-bottom border-light-subtle bg-white px-4 py-2 d-flex align-items-center justify-content-between" style="background: #ffffff !important; border-bottom: 1px solid #f1f5f9 !important; height: 56px !important; min-height: 56px !important;">
                <div class="d-flex align-items-center">
                    <div class="position-relative me-3">
                        <span class="spinner-grow text-teal" style="width: 1rem; height: 1rem; color: #0d9488;" role="status"></span>
                        <span class="position-absolute top-50 start-50 translate-middle p-1 rounded-circle" style="background-color: #0d9488;"></span>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center" id="doctorVideoCallModalLabel">
                            <i class="fa-solid fa-video me-2" style="color: #0d9488;"></i> Téléconsultation Médicale HD
                        </h5>
                        <small class="text-muted fs-12">Salon vidéo sécurisé et crypté</small>
                    </div>
                    <span id="callPatientName" class="badge bg-teal-subtle text-teal-800 border border-teal-200 ms-3 px-3 py-2 fs-13 rounded-pill shadow-sm" style="background-color: #ccfbf1; color: #115e59;">
                        <i class="fa-solid fa-user me-1"></i> Patient
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn text-white rounded-pill px-3 py-1.5 fs-12 fw-bold d-flex align-items-center shadow-sm" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;" onclick="openDoctorInviteColleagueModal()" title="Inviter un confrère pour un 2ème avis">
                        <i class="fa-solid fa-user-plus me-1.5"></i> <span>Inviter un confrère</span>
                    </button>
                    <div id="callDurationBadge" class="badge bg-light text-dark border border-teal-500 px-3 py-2 fs-13 rounded-pill font-monospace shadow-sm" style="border-color: #0d9488 !important;">
                        <i class="fa-regular fa-clock me-1 text-teal" style="color: #0d9488;"></i> <span id="callDuration">00:00</span>
                    </div>
                    <button type="button" class="btn-close ms-1" onclick="closeDoctorVideoCall()" title="Fermer"></button>
                </div>
            </div>

            <!-- Viewport Vidéo Principal avec Split-Screen Flex Grid Grande Taille -->
            <div class="modal-body p-0 position-relative" id="callModalBody" style="height: 84vh !important; min-height: 600px !important; max-height: 88vh !important; display: flex !important; flex-direction: row !important; align-items: stretch !important; background: #0f172a !important; overflow: hidden !important; transition: all 0.3s ease-in-out;">
                
                <!-- Colonne Gauche : Visioconférence (Agrandie 100% ou Réduite 38%) -->
                <div id="videoSplitCol" class="position-relative" style="width: 100%; height: 100% !important; flex-shrink: 0 !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important; background: #0f172a !important; overflow: hidden !important; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                    <!-- Conteneur Vidéo Distante (Patient) -->
                    <div id="remote-video-container" class="w-100 h-100 position-relative" style="display: flex !important; align-items: center !important; justify-content: center !important; background: #0f172a !important; overflow: hidden !important;">
                        <div id="patientWaitingState" class="position-absolute" style="top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; width: 100% !important; z-index: 10 !important; pointer-events: none !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important;">
                            <div class="p-3 rounded-4 shadow-lg text-center" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border: 1px solid #e2e8f0; width: 85% !important; max-width: 320px !important; flex-shrink: 0; pointer-events: auto;">
                                <div class="spinner-grow mb-2" style="width: 2.5rem; height: 2.5rem; color: #0d9488;" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">Appel en cours...</h5>
                                <p class="text-secondary mb-0 fs-12">En attente de la connexion vidéo et audio de l'infirmier(ère)...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Miniature Vidéo Locale (Médecin Picture-in-Picture) -->
                    <div id="local-video-container" class="position-absolute rounded-4 border border-2 shadow-2xl overflow-hidden" style="bottom: 16px !important; right: 16px !important; width: 140px !important; height: 95px !important; z-index: 20 !important; background: #1e293b !important; border-color: #0d9488 !important; box-shadow: 0 8px 20px rgba(0,0,0,0.4) !important; transition: all 0.3s ease-in-out;">
                        <span class="badge bg-white text-dark shadow-sm position-absolute top-0 start-0 m-1 fs-10 px-2 py-0.5 rounded-pill" style="z-index: 30; border: 1px solid #cbd5e1; opacity: 0.9;">
                            <i class="fa-solid fa-user-doctor me-1" style="color: #0d9488;"></i> Vous
                        </span>
                    </div>
                </div>

                <!-- Colonne Droite : Formulaire de Consultation Complet Intégré Directement -->
                <div id="formSplitCol" class="h-100 bg-white d-none flex-column" style="width: 0%; height: 100% !important; flex-shrink: 0 !important; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 2px solid #0d9488 !important; background: #f4f6f9 !important; overflow: hidden !important;">
                    @include('partials.online_consultation_form')
                </div>
            </div>

            <!-- Barres de contrôles flottantes en bas Light -->
            <div class="modal-footer border-top border-light-subtle bg-white justify-content-center py-2" style="background: #ffffff !important; border-top: 1px solid #f1f5f9 !important; height: 66px !important; min-height: 66px !important;">
                <div class="d-flex align-items-center gap-3 px-4 py-1.5 rounded-pill shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <button type="button" id="btnToggleMic" onclick="toggleDoctorMic()" class="btn btn-light border border-secondary-subtle rounded-circle shadow-sm" style="width: 46px; height: 46px; transition: all 0.2s;" title="Activer / Désactiver le micro">
                        <i class="fa-solid fa-microphone fs-16 text-dark"></i>
                    </button>
                    <button type="button" id="btnToggleCam" onclick="toggleDoctorCam()" class="btn btn-light border border-secondary-subtle rounded-circle shadow-sm" style="width: 46px; height: 46px; transition: all 0.2s;" title="Activer / Désactiver la caméra">
                        <i class="fa-solid fa-video fs-16 text-dark"></i>
                    </button>
                    
                    <button type="button" id="btnToggleForm" onclick="toggleConsultationFormSplit()" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow-sm d-none align-items-center" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none; height: 42px; transition: all 0.3s ease;">
                        <i class="fa-solid fa-file-medical me-2"></i> <span id="btnToggleFormText">Formulaire de consultation</span>
                    </button>

                    <div class="vr bg-secondary-subtle my-2 mx-1" style="height: 24px;"></div>
                    <button type="button" onclick="closeDoctorVideoCall()" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm text-white d-flex align-items-center" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; height: 42px;">
                        <i class="fa-solid fa-phone-slash me-2"></i> Raccrocher
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>
<script>
    let livekitRoom = null;
    let doctorAudioTrack = null;
    let doctorVideoTrack = null;
    let currentConsultationId = null;
    let isMicMuted = false;
    let isCamOff = false;
    let callTimer = null;
    let statusPollInterval = null;
    let isFormSplitActive = false;

    async function getLiveKitSDK() {
        if (window.LivekitClient) return window.LivekitClient;
        if (window.LiveKit) return window.LiveKit;

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = "https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js";
            script.onload = () => resolve(window.LivekitClient || window.LiveKit);
            script.onerror = () => reject(new Error("Échec du chargement du SDK LiveKit."));
            document.head.appendChild(script);
        });
    }

    function openDoctorVideoCall(consultationId, arg2, arg3, arg4, arg5) {
        currentConsultationId = consultationId;
        isDoctorEndingCall = false;

        let patientName = 'Patient';
        let preToken = null;
        let preLivekitUrl = null;
        let preChannel = null;

        if (typeof arg2 === 'string' && (arg2.startsWith('ey') || arg2.length > 50)) {
            preToken = arg2;
            preLivekitUrl = arg3;
            patientName = arg4 || 'Patient';
            preChannel = arg5 || null;
        } else if (typeof arg3 === 'string' && (arg3.startsWith('ey') || arg3.length > 50)) {
            patientName = arg2 || 'Patient';
            preToken = arg3;
            preLivekitUrl = arg4;
            preChannel = arg5 || null;
        } else {
            patientName = arg2 || 'Patient';
        }

        const nameBadge = document.getElementById("callPatientName");
        if (nameBadge) {
            nameBadge.innerHTML = `<i class="fa-solid fa-user me-1"></i> ${patientName}`;
        }

        isMicMuted = false;
        isCamOff = false;

        const micBtn = document.getElementById('btnToggleMic');
        if (micBtn) {
            micBtn.className = 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
            micBtn.innerHTML = '<i class="fa-solid fa-microphone fs-16 text-dark"></i>';
        }
        const camBtn = document.getElementById('btnToggleCam');
        if (camBtn) {
            camBtn.className = 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
            camBtn.innerHTML = '<i class="fa-solid fa-video fs-16 text-dark"></i>';
        }

        // Cacher le bouton formulaire au départ tant que l'infirmier n'a pas décroché
        const formBtn = document.getElementById('btnToggleForm');
        if (formBtn) {
            formBtn.classList.remove('d-flex');
            formBtn.classList.add('d-none');
        }

        // Pré-charger la consultation dans le formulaire du modal
        if (typeof loadDoctorModalConsultation === 'function') {
            loadDoctorModalConsultation(consultationId);
        }

        // Réinitialiser le timer
        let seconds = 0;
        const durationEl = document.getElementById("callDuration");
        if (durationEl) durationEl.innerText = "00:00";
        if (callTimer) clearInterval(callTimer);
        callTimer = setInterval(() => {
            seconds++;
            const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
            const secs = String(seconds % 60).padStart(2, '0');
            if (durationEl) durationEl.innerText = `${mins}:${secs}`;
        }, 1000);

        // Polling de statut pour détecter si le patient décroche ou raccroche
        if (statusPollInterval) clearInterval(statusPollInterval);
        statusPollInterval = setInterval(async () => {
            if (!currentConsultationId || isDoctorEndingCall) return;
            try {
                const targetId = currentConsultationId;
                const res = await fetch(`/doctor/consultation/call/status/${targetId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (res.status === 401 || res.status === 403) {
                    if (statusPollInterval) clearInterval(statusPollInterval);
                    statusPollInterval = null;
                    return;
                }
                const data = await res.json();
                if (isDoctorEndingCall) return;

                if (data.call_status === 'in_call' || (data.is_call_active && data.call_status !== 'calling')) {
                    showDoctorConsultationButton();
                }

                if (!data.is_call_active || ['patient_left', 'cancelled', 'rejected', 'ended', 'completed'].includes(data.call_status)) {
                    if (statusPollInterval) clearInterval(statusPollInterval);
                    statusPollInterval = null;
                    if (!isDoctorEndingCall && data.call_status !== 'doctor_ended') {
                        isDoctorEndingCall = true;
                        alert("L'appel a été terminé.");
                        closeDoctorVideoCall(false);
                    }
                }
            } catch(e) {}
        }, 3000);

        // Ouvrir la modale Bootstrap
        $('#doctorVideoCallModal').modal('show');
        setTimeout(() => {
            $('.modal-backdrop').css('z-index', 1050);
            $('#doctorVideoCallModal').css('z-index', 1055);
        }, 100);

        // Si le token et l'URL sont déjà fournis (ex: clic Décrocher)
        if (preToken && preLivekitUrl) {
            const channel = preChannel || ('consultation_room_' + consultationId);
            joinLiveKitDoctorCall(preLivekitUrl, preToken, channel);
            return;
        }

        // Sinon, requête POST vers /doctor/consultation/call/start/{id}
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch(`/doctor/consultation/call/start/${consultationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.token) {
                if (data.patient_name) {
                    const nb = document.getElementById("callPatientName");
                    if (nb) nb.innerHTML = `<i class="fa-solid fa-user me-1"></i> ${data.patient_name}`;
                }
                joinLiveKitDoctorCall(data.livekit_url, data.token, data.channel);
            } else {
                alert(data.message || data.error || "Impossible d'accéder au salon vidéo.");
            }
        })
        .catch(err => {
            console.error("Erreur serveur lors du démarrage de l'appel:", err);
            alert("Erreur de connexion avec le serveur.");
        });
    }

    function createDoctorFallbackVideoTrack(label = "Médecin") {
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
            ctx.fillText("DR", 320, 212);

            ctx.font = "bold 20px sans-serif";
            ctx.fillStyle = "#e2e8f0";
            ctx.fillText(label, 320, 310);

            ctx.font = "14px sans-serif";
            ctx.fillStyle = "#94a3b8";
            ctx.fillText("Flux vidéo médecin actif", 320, 335);

            requestAnimationFrame(draw);
        };
        draw();

        const stream = canvas.captureStream(15);
        return stream.getVideoTracks()[0];
    }

    function showDoctorConsultationButton() {
        const btn = document.getElementById('btnToggleForm');
        if (btn) {
            btn.classList.remove('d-none');
            btn.classList.add('d-flex');
        }
        const waitingState = document.getElementById("patientWaitingState");
        if (waitingState) waitingState.style.setProperty("display", "none", "important");
    }

    function attachDoctorRemoteTrack(track, publication, participant) {
        if (!track) return;
        const container = document.getElementById("remote-video-container");
        if (!container) return;

        showDoctorConsultationButton();

        const participantId = participant?.identity || (publication?.participantIdentity) || 'remote-main';
        const isColleague = participantId.startsWith('colleague_') || (participant?.name && participant.name.includes('Dr'));
        const participantLabel = isColleague ? (participant?.name || 'Confrère Expert') : 'Patient & Infirmier';

        if (track.kind === 'video') {
            let card = document.getElementById(`remote-card-${participantId}`);
            if (!card) {
                card = document.createElement('div');
                card.id = `remote-card-${participantId}`;
                card.className = 'remote-participant-card position-relative overflow-hidden';
                card.style.cssText = 'position: relative; flex: 1 1 50%; min-width: 280px; height: 100%; min-height: 100%; background: #0f172a; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;';
                
                // Badge
                const badge = document.createElement('span');
                badge.className = 'badge position-absolute top-0 start-0 m-2 fs-11 px-2.5 py-1 rounded-pill shadow-sm';
                badge.style.cssText = 'z-index: 25; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); color: #ffffff; border: 1px solid rgba(255,255,255,0.2);';
                badge.innerHTML = isColleague 
                    ? `<i class="fa-solid fa-user-doctor text-teal me-1" style="color:#2dd4bf;"></i> ${participantLabel}`
                    : `<i class="fa-solid fa-user-nurse text-teal me-1" style="color:#2dd4bf;"></i> ${participantLabel}`;
                card.appendChild(badge);

                container.appendChild(card);
            }

            // Supprimer d'anciennes vidéos pour CE participant
            card.querySelectorAll('video').forEach(v => v.remove());

            const videoEl = track.attach();
            videoEl.autoplay = true;
            videoEl.playsInline = true;
            videoEl.setAttribute('playsinline', 'true');
            videoEl.style.setProperty('width', '100%', 'important');
            videoEl.style.setProperty('height', '100%', 'important');
            videoEl.style.setProperty('object-fit', 'cover', 'important');
            videoEl.style.setProperty('display', 'block', 'important');
            videoEl.style.setProperty('position', 'absolute', 'important');
            videoEl.style.setProperty('top', '0', 'important');
            videoEl.style.setProperty('left', '0', 'important');
            card.appendChild(videoEl);
            videoEl.play().catch(e => console.warn("Doctor remote video play error:", e));

            // Ajuster le layout flex du conteneur
            updateRemoteLayout(container);
        } else if (track.kind === 'audio') {
            const audioId = `remote-audio-${participantId}`;
            const oldAudio = document.getElementById(audioId);
            if (oldAudio) oldAudio.remove();

            const audioEl = track.attach();
            audioEl.id = audioId;
            audioEl.autoplay = true;
            audioEl.muted = false;
            audioEl.volume = 1.0;
            audioEl.setAttribute('autoplay', 'true');
            audioEl.setAttribute('playsinline', 'true');
            document.body.appendChild(audioEl);

            const p = audioEl.play();
            if (p !== undefined) {
                p.catch(e => {
                    console.warn("Doctor remote audio play error:", e);
                    if (livekitRoom) livekitRoom.startAudio();
                });
            }
        }
    }

    function removeDoctorRemoteParticipant(participantId) {
        const card = document.getElementById(`remote-card-${participantId}`);
        if (card) card.remove();
        const audio = document.getElementById(`remote-audio-${participantId}`);
        if (audio) audio.remove();
        const container = document.getElementById("remote-video-container");
        if (container) updateRemoteLayout(container);
    }

    function updateRemoteLayout(container) {
        if (!container) return;
        const cards = container.querySelectorAll('.remote-participant-card');
        if (cards.length === 0) {
            // Re-afficher l'état d'attente s'il existe
            const waitingState = document.getElementById("patientWaitingState");
            if (waitingState) waitingState.style.setProperty("display", "flex", "important");
        } else if (cards.length === 1) {
            cards[0].style.width = '100%';
            cards[0].style.flex = '1 1 100%';
            cards[0].style.height = '100%';
        } else {
            cards.forEach(c => {
                c.style.width = `${Math.floor(100 / Math.min(cards.length, 3))}%`;
                c.style.flex = '1 1 48%';
                c.style.height = '100%';
            });
        }
    }

    async function joinLiveKitDoctorCall(url, token, channel) {
        try {
            const LiveKit = await getLiveKitSDK();
            if (!LiveKit) {
                console.error("LiveKit SDK non disponible");
                alert("Le SDK vidéo n'a pas pu être chargé. Veuillez rafraîchir la page.");
                return;
            }

            const SDK = window.LiveKitClient || window.LiveKit || LiveKit;
            const RoomClass = SDK?.Room || SDK?.default?.Room || LiveKit?.Room;
            const createLocalAudioTrackFn = SDK?.createLocalAudioTrack || SDK?.default?.createLocalAudioTrack || LiveKit?.createLocalAudioTrack;
            const createLocalVideoTrackFn = SDK?.createLocalVideoTrack || SDK?.default?.createLocalVideoTrack || LiveKit?.createLocalVideoTrack;
            const LocalVideoTrackClass = SDK?.LocalVideoTrack || SDK?.default?.LocalVideoTrack || LiveKit?.LocalVideoTrack;
            const RoomEventEnum = SDK?.RoomEvent || SDK?.default?.RoomEvent || LiveKit?.RoomEvent || {};

            if (!RoomClass) {
                console.error("LiveKit SDK RoomClass non disponible");
                return;
            }

            livekitRoom = new RoomClass({
                adaptiveStream: true,
                dynacast: true,
            });

            const localContainer = document.getElementById('local-video-container');

            const audioStatusEvent = RoomEventEnum.AudioPlaybackStatusChanged || "audioPlaybackChanged";
            livekitRoom.on(audioStatusEvent, () => {
                if (!livekitRoom.canPlaybackAudio) {
                    livekitRoom.startAudio();
                }
            });

            const muteEvent = RoomEventEnum.TrackMuted || 'trackMuted';
            const unmuteEvent = RoomEventEnum.TrackUnmuted || 'trackUnmuted';

            livekitRoom.on(muteEvent, (publication, participant) => {
                if (participant && (participant === livekitRoom.localParticipant || participant.identity === livekitRoom.localParticipant?.identity)) {
                    return;
                }
                if (publication.kind === 'video' && participant) {
                    const card = document.getElementById(`remote-card-${participant.identity}`);
                    if (card) {
                        card.querySelectorAll('video').forEach(v => v.style.setProperty('display', 'none', 'important'));
                    }
                }
            });

            livekitRoom.on(unmuteEvent, (publication, participant) => {
                if (participant && (participant === livekitRoom.localParticipant || participant.identity === livekitRoom.localParticipant?.identity)) {
                    return;
                }
                if (publication.kind === 'video' && participant) {
                    const card = document.getElementById(`remote-card-${participant.identity}`);
                    if (card) {
                        card.querySelectorAll('video').forEach(v => v.style.setProperty('display', 'block', 'important'));
                    }
                }
            });

            const pConnEvent = RoomEventEnum.ParticipantConnected || "participantConnected";
            livekitRoom.on(pConnEvent, (participant) => {
                console.log("Participant connecté au salon LiveKit:", participant.identity);
                showDoctorConsultationButton();
                if (participant.trackPublications) {
                    participant.trackPublications.forEach(publication => {
                        if (publication.track) {
                            attachDoctorRemoteTrack(publication.track, publication, participant);
                        }
                    });
                }
            });

            const pDisconnEvent = RoomEventEnum.ParticipantDisconnected || "participantDisconnected";
            livekitRoom.on(pDisconnEvent, (participant) => {
                console.log("Participant déconnecté du salon LiveKit:", participant.identity);
                removeDoctorRemoteParticipant(participant.identity);

                // Si le participant principal (infirmier ou patient) raccroche, raccrocher directement chez le médecin
                if (!isDoctorEndingCall && participant && !participant.identity.startsWith('colleague_')) {
                    isDoctorEndingCall = true;
                    if (statusPollInterval) {
                        clearInterval(statusPollInterval);
                        statusPollInterval = null;
                    }
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: "L'infirmier(ère) / patient a raccroché la téléconsultation vidéo.",
                            icon: "info",
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        alert("L'infirmier(ère) / patient a raccroché la téléconsultation vidéo.");
                    }
                    closeDoctorVideoCall(false);
                }
            });

            const subEvent = RoomEventEnum.TrackSubscribed || "trackSubscribed";
            livekitRoom.on(subEvent, (track, publication, participant) => {
                console.log("Doctor: track subscribed:", track.kind, participant?.identity);
                attachDoctorRemoteTrack(track, publication, participant);
            });

            const pubEvent = RoomEventEnum.TrackPublished || "trackPublished";
            livekitRoom.on(pubEvent, (publication, participant) => {
                showDoctorConsultationButton();
                if (publication.track) {
                    attachDoctorRemoteTrack(publication.track, publication, participant);
                }
            });

            const unsubEvent = RoomEventEnum.TrackUnsubscribed || "trackUnsubscribed";
            livekitRoom.on(unsubEvent, (track, publication, participant) => {
                track.detach().forEach(el => el.remove());
                if (participant) removeDoctorRemoteParticipant(participant.identity);
            });

            await livekitRoom.connect(url, token);
            try { await livekitRoom.startAudio(); } catch(e){}

            let engineWait = 0;
            while (livekitRoom.engine && !livekitRoom.engine.isEngineConnected && engineWait < 25) {
                await new Promise(r => setTimeout(r, 100));
                engineWait++;
            }

            if (livekitRoom.remoteParticipants && livekitRoom.remoteParticipants.size > 0) {
                showDoctorConsultationButton();
                livekitRoom.remoteParticipants.forEach(participant => {
                    if (participant.trackPublications) {
                        participant.trackPublications.forEach(publication => {
                            if (publication.track) {
                                attachDoctorRemoteTrack(publication.track, publication, participant);
                            }
                        });
                    }
                });
            }

            // 1. Activer micro du docteur avec fallbacks multiples robustes
            const acquireDoctorMicrophone = async () => {
                try {
                    const micPub = await livekitRoom.localParticipant.setMicrophoneEnabled(true);
                    if (micPub) {
                        doctorAudioTrack = micPub.track || micPub;
                        if (doctorAudioTrack && typeof doctorAudioTrack.unmute === 'function') {
                            try { await doctorAudioTrack.unmute(); } catch(e){}
                        }
                        console.log("Microphone docteur activé via setMicrophoneEnabled !");
                        return;
                    }
                } catch (e1) {
                    console.warn("setMicrophoneEnabled docteur simple échoué:", e1);
                }

                try {
                    if (createLocalAudioTrackFn) {
                        const localAudio = await createLocalAudioTrackFn({ echoCancellation: true, noiseSuppression: true, autoGainControl: true });
                        const pub = await livekitRoom.localParticipant.publishTrack(localAudio, { name: "audio" });
                        doctorAudioTrack = (pub && pub.track) ? pub.track : localAudio;
                        if (doctorAudioTrack && typeof doctorAudioTrack.unmute === 'function') {
                            try { await doctorAudioTrack.unmute(); } catch(e){}
                        }
                        console.log("Microphone docteur publié via createLocalAudioTrack !");
                        return;
                    }
                } catch (e2) {
                    console.warn("createLocalAudioTrack docteur échoué:", e2);
                }

                try {
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        const rawStream = await navigator.mediaDevices.getUserMedia({ audio: { echoCancellation: true, noiseSuppression: true } });
                        const rawTrack = rawStream.getAudioTracks()[0];
                        if (rawTrack) {
                            const LocalAudioTrackClass = SDK?.LocalAudioTrack || SDK?.default?.LocalAudioTrack || LiveKit?.LocalAudioTrack;
                            const lTrack = LocalAudioTrackClass ? new LocalAudioTrackClass(rawTrack) : rawTrack;
                            const pub = await livekitRoom.localParticipant.publishTrack(lTrack, { name: "audio" });
                            doctorAudioTrack = (pub && pub.track) ? pub.track : lTrack;
                            console.log("Microphone docteur activé via getUserMedia direct !");
                        }
                    }
                } catch (e3) {
                    console.error("Microphone docteur définitivement inaccessible:", e3);
                }
            };
            await acquireDoctorMicrophone();

            // 2. Activer caméra du docteur
            const renderDoctorLocalTrack = (vTrack) => {
                if (localContainer && vTrack) {
                    localContainer.querySelectorAll('video').forEach(v => v.remove());
                    localContainer.querySelectorAll('.local-cam-off-avatar, #docCamOffPlaceholder').forEach(el => el.remove());
                    doctorVideoTrack = vTrack;
                    const localEl = vTrack.attach();
                    localEl.muted = true;
                    localEl.autoplay = true;
                    localEl.playsInline = true;
                    localEl.setAttribute('playsinline', 'true');
                    localEl.style.setProperty('width', '100%', 'important');
                    localEl.style.setProperty('height', '100%', 'important');
                    localEl.style.setProperty('min-width', '100%', 'important');
                    localEl.style.setProperty('min-height', '100%', 'important');
                    localEl.style.setProperty('object-fit', 'cover', 'important');
                    localEl.style.setProperty('display', 'block', 'important');
                    localContainer.appendChild(localEl);
                    localEl.play().catch(e => console.warn("Doctor local video play error:", e));
                }
            };

            const acquireDoctorCamera = async () => {
                try {
                    const camPub = await livekitRoom.localParticipant.setCameraEnabled(true);
                    let vTrack = (camPub && camPub.track) ? camPub.track : null;

                    let waitCount = 0;
                    while (!vTrack && waitCount < 10) {
                        await new Promise(r => setTimeout(r, 150));
                        if (camPub && camPub.track) {
                            vTrack = camPub.track;
                        } else if (livekitRoom.localParticipant.videoTrackPublications) {
                            livekitRoom.localParticipant.videoTrackPublications.forEach(pub => {
                                if (pub.track && pub.kind === 'video') vTrack = pub.track;
                            });
                        }
                        waitCount++;
                    }

                    if (vTrack) {
                        console.log("Caméra physique docteur réelle obtenue !");
                        return vTrack;
                    }
                } catch (e1) {
                    console.warn("setCameraEnabled docteur échoué:", e1);
                }

                console.warn("Caméra docteur physique indisponible, fallback virtuel");
                const fallbackRaw = createDoctorFallbackVideoTrack("Médecin HD");
                if (fallbackRaw) {
                    let fallbackTrack = fallbackRaw;
                    if (LocalVideoTrackClass) {
                        try { fallbackTrack = new LocalVideoTrackClass(fallbackRaw); } catch(e){}
                    }
                    const pub = await livekitRoom.localParticipant.publishTrack(fallbackTrack, { name: "camera" });
                    return (pub && pub.track) ? pub.track : fallbackTrack;
                }
                return null;
            };

            try {
                const vTrack = await acquireDoctorCamera();
                if (vTrack) renderDoctorLocalTrack(vTrack);
            } catch (vErr) {
                console.error("Erreur caméra docteur:", vErr);
            }

        } catch (error) {
            console.error("Erreur LiveKit Doctor:", error);
        }
    }

    async function toggleDoctorMic() {
        if (!livekitRoom || !livekitRoom.localParticipant) return;
        isMicMuted = !isMicMuted;
        try {
            if (doctorAudioTrack && typeof doctorAudioTrack.mute === 'function' && typeof doctorAudioTrack.unmute === 'function') {
                if (isMicMuted) {
                    await doctorAudioTrack.mute();
                } else {
                    await doctorAudioTrack.unmute();
                }
            } else {
                await livekitRoom.localParticipant.setMicrophoneEnabled(!isMicMuted);
            }
        } catch (e) {
            console.warn("Erreur bascule micro docteur:", e);
        }
        const btn = document.getElementById('btnToggleMic');
        if (btn) {
            btn.className = isMicMuted ? 'btn btn-danger rounded-circle shadow-sm' : 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
            btn.innerHTML = isMicMuted ? '<i class="fa-solid fa-microphone-slash fs-16 text-white"></i>' : '<i class="fa-solid fa-microphone fs-16 text-dark"></i>';
        }
    }

    async function toggleDoctorCam() {
        if (!livekitRoom || !livekitRoom.localParticipant) return;
        isCamOff = !isCamOff;
        const btn = document.getElementById('btnToggleCam');
        const localContainer = document.getElementById('local-video-container');

        if (isCamOff) {
            try {
                if (doctorVideoTrack && typeof doctorVideoTrack.mute === 'function') {
                    await doctorVideoTrack.mute();
                }
                await livekitRoom.localParticipant.setCameraEnabled(false);
            } catch (e) {
                console.warn("Erreur désactivation caméra docteur:", e);
            }
            if (localContainer) {
                localContainer.querySelectorAll('video').forEach(v => v.style.setProperty('display', 'none', 'important'));
                localContainer.querySelectorAll('.local-cam-off-avatar, #docCamOffPlaceholder').forEach(el => el.remove());
                const offDiv = document.createElement('div');
                offDiv.id = 'docCamOffPlaceholder';
                offDiv.className = 'local-cam-off-avatar w-100 h-100 position-absolute top-0 start-0 d-flex flex-column align-items-center justify-content-center';
                offDiv.style.zIndex = '25';
                offDiv.style.background = '#0f172a';
                offDiv.style.pointerEvents = 'none';
                offDiv.innerHTML = `
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-1" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0d9488 0%, #047857 100%); color: #ffffff; font-weight: bold; font-size: 15px; border: 2px solid rgba(255,255,255,0.25);">
                        DR
                    </div>
                    <span class="text-white-50 fs-10 fw-semibold">Caméra coupée</span>
                `;
                localContainer.appendChild(offDiv);
            }
            if (btn) {
                btn.className = 'btn btn-danger rounded-circle shadow-sm';
                btn.innerHTML = '<i class="fa-solid fa-video-slash fs-16 text-white"></i>';
            }
        } else {
            if (localContainer) {
                localContainer.querySelectorAll('.local-cam-off-avatar, #docCamOffPlaceholder').forEach(el => el.remove());
            }

            try {
                if (doctorVideoTrack) {
                    if (typeof doctorVideoTrack.unmute === 'function') {
                        await doctorVideoTrack.unmute();
                    }
                    if (typeof doctorVideoTrack.attach === 'function') {
                        renderDoctorLocalTrack(doctorVideoTrack);
                    }
                }
                const vTrack = await acquireDoctorCamera();
                if (vTrack) renderDoctorLocalTrack(vTrack);
            } catch (e) {
                console.warn("Erreur réactivation caméra docteur:", e);
            }
            if (localContainer) {
                localContainer.querySelectorAll('video').forEach(v => {
                    v.style.setProperty('display', 'block', 'important');
                    v.play().catch(err => {});
                });
            }
            if (btn) {
                btn.className = 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
                btn.innerHTML = '<i class="fa-solid fa-video fs-16 text-dark"></i>';
            }
        }
    }

    async function closeDoctorVideoCall(notifyBackend = true) {
        isDoctorEndingCall = true;

        if (callTimer) clearInterval(callTimer);
        callTimer = null;
        if (statusPollInterval) clearInterval(statusPollInterval);
        statusPollInterval = null;

        const targetConsultationId = currentConsultationId;
        currentConsultationId = null;

        if (livekitRoom && livekitRoom.localParticipant) {
            try {
                livekitRoom.localParticipant.videoTrackPublications.forEach(pub => {
                    if (pub.track) try { pub.track.stop(); } catch(e){}
                });
                livekitRoom.localParticipant.audioTrackPublications.forEach(pub => {
                    if (pub.track) try { pub.track.stop(); } catch(e){}
                });
            } catch (e) {}
        }

        if (doctorAudioTrack) {
            try { doctorAudioTrack.stop(); } catch(e){}
            doctorAudioTrack = null;
        }
        if (doctorVideoTrack) {
            try { doctorVideoTrack.stop(); } catch(e){}
            doctorVideoTrack = null;
        }
        if (livekitRoom) {
            try { livekitRoom.disconnect(); } catch (e) {}
            livekitRoom = null;
        }

        if (targetConsultationId && notifyBackend) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch(`/doctor/consultation/call/end/${targetConsultationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            } catch (e) {
                console.error("Erreur fin d'appel:", e);
            }
        }

        if (isFormSplitActive) {
            toggleConsultationFormSplit();
        }

        $('#doctorVideoCallModal').modal('hide');
        
        if (typeof loadPendingOnlineRequests === 'function') {
            loadPendingOnlineRequests();
        }
        
        const remoteContainer = document.getElementById("remote-video-container");
        if (remoteContainer) {
            remoteContainer.innerHTML = `
                <div id="patientWaitingState" class="position-absolute" style="top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; width: 100% !important; z-index: 10 !important; pointer-events: none !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important;">
                    <div class="p-4 rounded-4 shadow-lg text-center" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border: 1px solid #e2e8f0; width: 380px; max-width: 90%; flex-shrink: 0; pointer-events: auto;">
                        <div class="spinner-grow mb-3" style="width: 3.2rem; height: 3.2rem; color: #0d9488;" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <h4 class="fw-bold text-dark mb-1" style="white-space: nowrap; font-size: 1.35rem;">Appel en cours...</h4>
                        <p class="text-secondary mb-0 fs-13">En attente de la connexion vidéo et audio du patient.</p>
                    </div>
                </div>
            `;
        }

        const localContainer = document.getElementById("local-video-container");
        if (localContainer) {
            localContainer.innerHTML = `
                <span class="badge bg-white text-dark shadow-sm position-absolute top-0 start-0 m-1 fs-10 px-2 py-0.5 rounded-pill" style="z-index: 30; border: 1px solid #cbd5e1; opacity: 0.9;">
                    <i class="fa-solid fa-user-doctor me-1" style="color: #0d9488;"></i> Vous
                </span>
            `;
        }

    }

    // --- GESTION DE L'INVITATION DE CONFRÈRE (2ème avis médical) ---
    async function openDoctorInviteColleagueModal() {
        if (!currentConsultationId) {
            alert("Veuillez d'abord démarrer ou rejoindre une consultation.");
            return;
        }

        const input = document.getElementById('colleagueInviteLinkInput');
        const loading = document.getElementById('colleagueInviteLoading');
        const content = document.getElementById('colleagueInviteContent');
        const whatsappBtn = document.getElementById('btnColleagueWhatsapp');

        if (input) input.value = '';
        if (loading) loading.classList.remove('d-none');
        if (content) content.classList.add('d-none');

        $('#modalDoctorInviteColleague').modal('show');
        setTimeout(() => {
            $('#modalDoctorInviteColleague').css('z-index', 1060);
            $('.modal-backdrop').last().css('z-index', 1058);
        }, 150);

        try {
            const res = await fetch(`/doctor/consultation/call/invite-link/${currentConsultationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();

            if (data.status === 'success' && data.invite_url) {
                if (input) input.value = data.invite_url;
                if (whatsappBtn) {
                    const patName = document.getElementById('callPatientName')?.innerText?.trim() || 'un patient';
                    const waText = encodeURIComponent(`Bonjour Docteur, je vous invite à rejoindre ma téléconsultation en direct pour le patient ${patName} afin d'apporter votre expertise médicale : ${data.invite_url}`);
                    whatsappBtn.href = `https://wa.me/?text=${waText}`;
                }
                if (loading) loading.classList.add('d-none');
                if (content) content.classList.remove('d-none');
            } else {
                alert(data.message || "Impossible de générer le lien d'invitation.");
                $('#modalDoctorInviteColleague').modal('hide');
            }
        } catch (e) {
            console.error("Erreur génération lien confrère:", e);
            alert("Erreur lors de la génération du lien d'invitation.");
            $('#modalDoctorInviteColleague').modal('hide');
        }
    }

    function copyColleagueInviteLink() {
        const input = document.getElementById('colleagueInviteLinkInput');
        if (!input || !input.value) return;

        input.select();
        input.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(input.value).then(() => {
            const btn = document.getElementById('btnCopyColleagueLink');
            if (btn) {
                const origHtml = btn.innerHTML;
                btn.className = 'btn btn-success px-3 fw-bold';
                btn.innerHTML = '<i class="fa-solid fa-check me-1"></i> Copié !';
                setTimeout(() => {
                    btn.className = 'btn btn-teal text-white px-3 fw-bold';
                    btn.style.background = '#0d9488';
                    btn.innerHTML = origHtml;
                }, 2500);
            }
        }).catch(err => {
            document.execCommand('copy');
            alert("Lien copié dans le presse-papier !");
        });
    }

    function toggleConsultationFormSplit() {
        if (!currentConsultationId) {
            alert("Aucune consultation active.");
            return;
        }

        isFormSplitActive = !isFormSplitActive;
        const dialog = document.querySelector('#doctorVideoCallModal .modal-dialog');
        const videoCol = document.getElementById('videoSplitCol');
        const formCol = document.getElementById('formSplitCol');
        const btnText = document.getElementById('btnToggleFormText');
        const btnIcon = document.querySelector('#btnToggleForm i');

        if (isFormSplitActive) {
            if (dialog) {
                dialog.style.setProperty('max-width', '99vw', 'important');
                dialog.style.setProperty('width', '99vw', 'important');
            }
            if (videoCol) videoCol.style.width = '25%';
            if (formCol) {
                formCol.style.width = '75%';
                formCol.classList.remove('d-none');
                formCol.classList.add('d-flex');
            }

            if (typeof loadDoctorModalConsultation === 'function') {
                loadDoctorModalConsultation(currentConsultationId);
            }

            if (btnText) btnText.innerText = "Agrandir vidéo";
            if (btnIcon) btnIcon.className = "fa-solid fa-expand me-2";
        } else {
            if (dialog) dialog.style.setProperty('max-width', '95vw', 'important');
            if (videoCol) videoCol.style.width = '100%';
            if (formCol) {
                formCol.style.width = '0%';
                formCol.classList.remove('d-flex');
                formCol.classList.add('d-none');
            }
            if (btnText) btnText.innerText = "Formulaire de consultation";
            if (btnIcon) btnIcon.className = "fa-solid fa-file-medical me-2";
        }
    }
</script>

<!-- Modal d'invitation d'un confrère (Deuxième avis médical) -->
<div class="modal fade" id="modalDoctorInviteColleague" tabindex="-1" aria-labelledby="modalDoctorInviteColleagueLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-white px-4 pt-4 pb-2 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); color: #0d9488; font-size: 20px;">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-16" id="modalDoctorInviteColleagueLabel">
                            Inviter un confrère médecin
                        </h5>
                        <small class="text-muted fs-12">Assistance & Expertise médicale en direct</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <!-- Loader -->
                <div id="colleagueInviteLoading" class="text-center py-4">
                    <div class="spinner-border text-teal mb-2" style="color: #0d9488;" role="status"></div>
                    <p class="text-muted fs-13 mb-0">Génération sécurisée du lien d'expertise...</p>
                </div>

                <!-- Contenu principal -->
                <div id="colleagueInviteContent" class="d-none">
                    <div class="alert alert-light border border-teal-subtle rounded-3 p-3 mb-3 d-flex align-items-start gap-2.5" style="background: #f0fdfa;">
                        <i class="fa-solid fa-circle-info text-teal mt-0.5" style="color: #0d9488;"></i>
                        <div class="fs-12 text-secondary leading-relaxed">
                            Partagez ce lien sécurisé avec un médecin confrère. En cliquant dessus, il rejoindra immédiatement la téléconsultation avec accès au dossier patient et aux constantes physiques.
                        </div>
                    </div>

                    <label class="form-label fw-bold text-dark fs-12 mb-1.5">Lien d'accès direct pour le confrère :</label>
                    <div class="input-group mb-3">
                        <input type="text" id="colleagueInviteLinkInput" class="form-control bg-light text-dark font-monospace fs-12" readonly onclick="this.select()">
                        <button type="button" id="btnCopyColleagueLink" onclick="copyColleagueInviteLink()" class="btn text-white px-3 fw-bold" style="background: #0d9488;">
                            <i class="fa-regular fa-copy me-1"></i> Copier
                        </button>
                    </div>

                    <div class="d-flex flex-column gap-2 mt-3 pt-2 border-top border-light-subtle">
                        <span class="fs-11 fw-bold text-uppercase text-muted letter-spacing-1">Partage rapide</span>
                        <div class="d-flex gap-2">
                            <a id="btnColleagueWhatsapp" href="#" target="_blank" class="btn btn-success text-white w-100 rounded-pill fs-13 py-2 fw-semibold d-flex align-items-center justify-content-center shadow-xs">
                                <i class="fa-brands fa-whatsapp me-2 fs-16"></i> Envoyer sur WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light px-4 py-2.5 justify-content-end">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fs-13" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
