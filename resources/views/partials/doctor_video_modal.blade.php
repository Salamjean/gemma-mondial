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
                <div class="d-flex align-items-center gap-3">
                    <div id="callDurationBadge" class="badge bg-light text-dark border border-teal-500 px-3 py-2 fs-14 rounded-pill font-monospace shadow-sm" style="border-color: #0d9488 !important;">
                        <i class="fa-regular fa-clock me-1 text-teal" style="color: #0d9488;"></i> <span id="callDuration">00:00</span>
                    </div>
                    <button type="button" class="btn-close" onclick="closeDoctorVideoCall()" title="Fermer"></button>
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
                                <p class="text-secondary mb-0 fs-12">En attente de la connexion vidéo et audio du patient.</p>
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
                    
                    <button type="button" id="btnToggleForm" onclick="toggleConsultationFormSplit()" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none; height: 42px;">
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

        // Polling de statut pour détecter si le patient raccroche
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

                if (!data.is_call_active || ['patient_left', 'cancelled', 'rejected'].includes(data.call_status)) {
                    if (statusPollInterval) clearInterval(statusPollInterval);
                    statusPollInterval = null;
                    if (!isDoctorEndingCall && data.call_status !== 'doctor_ended') {
                        isDoctorEndingCall = true;
                        alert("L'appel a été terminé par le patient.");
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

    function attachDoctorRemoteTrack(track) {
        if (!track) return;
        const container = document.getElementById("remote-video-container");
        if (!container) return;

        if (track.kind === 'video') {
            const waitingState = document.getElementById("patientWaitingState");
            if (waitingState) waitingState.style.setProperty("display", "none", "important");

            const oldVideos = container.querySelectorAll('video');
            oldVideos.forEach(v => v.remove());

            const videoEl = track.attach();
            videoEl.autoplay = true;
            videoEl.playsInline = true;
            videoEl.setAttribute('playsinline', 'true');
            videoEl.style.setProperty('width', '100%', 'important');
            videoEl.style.setProperty('height', '100%', 'important');
            videoEl.style.setProperty('min-width', '100%', 'important');
            videoEl.style.setProperty('min-height', '100%', 'important');
            videoEl.style.setProperty('max-width', '100%', 'important');
            videoEl.style.setProperty('max-height', '100%', 'important');
            videoEl.style.setProperty('object-fit', 'cover', 'important');
            videoEl.style.setProperty('display', 'block', 'important');
            videoEl.style.setProperty('position', 'absolute', 'important');
            videoEl.style.setProperty('top', '0', 'important');
            videoEl.style.setProperty('left', '0', 'important');
            container.appendChild(videoEl);
            videoEl.play().catch(e => console.warn("Doctor remote video play error:", e));
        } else if (track.kind === 'audio') {
            const existingAudio = document.getElementById("remote-doctor-audio-el");
            if (existingAudio) existingAudio.remove();

            const audioEl = track.attach();
            audioEl.id = "remote-doctor-audio-el";
            audioEl.autoplay = true;
            document.body.appendChild(audioEl);
            audioEl.play().catch(e => console.warn("Doctor remote audio play error:", e));
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

            // 0. Demande d'autorisation média préalable au navigateur
            if (typeof navigator !== "undefined" && navigator.mediaDevices?.getUserMedia) {
                try {
                    const preStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: true });
                    preStream.getTracks().forEach((t) => t.stop());
                } catch (pErr) {
                    console.warn("Pré-demande d'autorisation média docteur:", pErr);
                }
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

            const pConnEvent = RoomEventEnum.ParticipantConnected || "participantConnected";
            livekitRoom.on(pConnEvent, (participant) => {
                console.log("Patient connecté au salon LiveKit:", participant.identity);
            });

            const pDisconnEvent = RoomEventEnum.ParticipantDisconnected || "participantDisconnected";
            livekitRoom.on(pDisconnEvent, (participant) => {
                console.log("Patient déconnecté du salon LiveKit:", participant.identity);
                alert("Le patient a raccroché l'appel.");
                closeDoctorVideoCall(false);
            });

            const subEvent = RoomEventEnum.TrackSubscribed || "trackSubscribed";
            livekitRoom.on(subEvent, (track) => {
                attachDoctorRemoteTrack(track);
            });

            const unsubEvent = RoomEventEnum.TrackUnsubscribed || "trackUnsubscribed";
            livekitRoom.on(unsubEvent, (track) => {
                track.detach().forEach(el => el.remove());
            });

            await livekitRoom.connect(url, token);
            try { await livekitRoom.startAudio(); } catch(e){}

            let engineWait = 0;
            while (livekitRoom.engine && !livekitRoom.engine.isEngineConnected && engineWait < 25) {
                await new Promise(r => setTimeout(r, 100));
                engineWait++;
            }

            if (livekitRoom.remoteParticipants && livekitRoom.remoteParticipants.size > 0) {
                livekitRoom.remoteParticipants.forEach(participant => {
                    if (participant.trackPublications) {
                        participant.trackPublications.forEach(publication => {
                            if (publication.isSubscribed && publication.track) {
                                attachDoctorRemoteTrack(publication.track);
                            }
                        });
                    }
                });
            }

            // 1. Activer micro du docteur avec annulation d'écho (AEC)
            try {
                const micPub = await livekitRoom.localParticipant.setMicrophoneEnabled(true, {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                });
                if (micPub && micPub.track) doctorAudioTrack = micPub.track;
                console.log("Microphone docteur activé avec annulation d'écho");
            } catch (aErr) {
                console.warn("Microphone docteur non accessible:", aErr.message || aErr);
            }

            // 2. Activer caméra du docteur
            const renderDoctorLocalTrack = (vTrack) => {
                if (localContainer && vTrack) {
                    const oldVideos = localContainer.querySelectorAll('video');
                    oldVideos.forEach(v => v.remove());
                    doctorVideoTrack = vTrack;
                    const localEl = vTrack.attach();
                    localEl.muted = true;
                    localEl.autoplay = true;
                    localEl.playsInline = true;
                    localEl.setAttribute('playsinline', 'true');
                    localEl.style.width = '100%';
                    localEl.style.height = '100%';
                    localEl.style.minWidth = '100%';
                    localEl.style.minHeight = '100%';
                    localEl.style.objectFit = 'cover';
                    localEl.style.display = 'block';
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
        if (livekitRoom && livekitRoom.localParticipant) {
            isMicMuted = !isMicMuted;
            try {
                await livekitRoom.localParticipant.setMicrophoneEnabled(!isMicMuted);
            } catch (e) {
                console.warn("Erreur bascule micro:", e);
            }
            const btn = document.getElementById('btnToggleMic');
            if (btn) {
                btn.className = isMicMuted ? 'btn btn-danger rounded-circle shadow-sm' : 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
                btn.innerHTML = isMicMuted ? '<i class="fa-solid fa-microphone-slash fs-16 text-white"></i>' : '<i class="fa-solid fa-microphone fs-16 text-dark"></i>';
            }
        }
    }

    async function toggleDoctorCam() {
        if (livekitRoom && livekitRoom.localParticipant) {
            isCamOff = !isCamOff;
            try {
                await livekitRoom.localParticipant.setCameraEnabled(!isCamOff);
            } catch (e) {
                console.warn("Erreur bascule caméra:", e);
            }
            const btn = document.getElementById('btnToggleCam');
            if (btn) {
                btn.className = isCamOff ? 'btn btn-danger rounded-circle shadow-sm' : 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
                btn.innerHTML = isCamOff ? '<i class="fa-solid fa-video-slash fs-16 text-white"></i>' : '<i class="fa-solid fa-video fs-16 text-dark"></i>';
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
