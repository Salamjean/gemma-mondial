<!-- Modal Consultation Vidéo en Ligne (LiveKit Cloud) Design Premium Light pour Infirmier(ère) -->
<style>
    #infirmier-remote-video-container video {
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
    #infirmier-local-video-container video {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        min-width: 100% !important;
        min-height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 12px !important;
    }
</style>
<div class="modal fade" id="infirmierVideoCallModal" tabindex="-1" aria-labelledby="infirmierVideoCallModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered my-1" style="max-width: 95vw !important; width: 95vw !important; transition: all 0.3s ease-in-out;">
        <div class="modal-content border-0 shadow-2xl rounded-4" style="overflow: hidden !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important; border-radius: 16px !important; box-shadow: 0 25px 60px rgba(0,0,0,0.3) !important;">
            
            <!-- Header Modal Light -->
            <div class="modal-header border-bottom border-light-subtle bg-white px-4 py-2 d-flex align-items-center justify-content-between" style="background: #ffffff !important; border-bottom: 1px solid #f1f5f9 !important; height: 56px !important; min-height: 56px !important;">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="position-relative me-2">
                        <span class="spinner-grow text-teal" style="width: 1rem; height: 1rem; color: #0d9488;" role="status"></span>
                        <span class="position-absolute top-50 start-50 translate-middle p-1 rounded-circle" style="background-color: #0d9488;"></span>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center fs-15" id="infirmierVideoCallModalLabel">
                            <i class="fa-solid fa-video me-2" style="color: #0d9488;"></i> Téléconsultation Médicale Directe
                        </h5>
                    </div>
                    <span id="infCallDoctorName" class="badge bg-teal-subtle text-teal-800 border border-teal-200 px-3 py-1.5 fs-12 rounded-pill shadow-xs" style="background-color: #ccfbf1; color: #115e59;">
                        <i class="fa-solid fa-user-doctor me-1"></i> Médecin
                    </span>
                    <span id="infCallPatientName" class="badge bg-light text-secondary border px-3 py-1.5 fs-12 rounded-pill shadow-xs">
                        <i class="fa-solid fa-user me-1"></i> Patient
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div id="infCallDurationBadge" class="badge bg-light text-dark border border-teal-500 px-3 py-1.5 fs-13 rounded-pill font-monospace shadow-xs" style="border-color: #0d9488 !important;">
                        <i class="fa-regular fa-clock me-1 text-teal" style="color: #0d9488;"></i> <span id="infCallDuration">00:00</span>
                    </div>
                    <button type="button" class="btn-close" onclick="closeInfirmierVideoCall()" title="Fermer"></button>
                </div>
            </div>

            <!-- Viewport Vidéo Plein Écran Absolu -->
            <div class="modal-body p-0 position-relative" style="height: 75vh !important; min-height: 520px !important; max-height: 80vh !important; position: relative !important; background: #0f172a !important; overflow: hidden !important;">
                
                <!-- Conteneur Vidéo Distante (Médecin) -->
                <div id="infirmier-remote-video-container" style="position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; background: #0f172a !important; display: flex !important; align-items: center !important; justify-content: center !important; overflow: hidden !important;">
                    <div id="infirmierDoctorWaitingState" style="position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; width: 100% !important; z-index: 10 !important; pointer-events: none !important; display: flex !important; flex-direction: column !important; align-items: center !important; justify-content: center !important;">
                        <div class="p-4 rounded-4 shadow-lg text-center" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); border: 1px solid #e2e8f0; width: 85% !important; max-width: 340px !important; flex-shrink: 0; pointer-events: auto;">
                            <div class="spinner-grow mb-2" style="width: 2.5rem; height: 2.5rem; color: #0d9488;" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;">Connexion au médecin...</h5>
                            <p class="text-secondary mb-0 fs-12">En attente de la réception du flux vidéo et audio du médecin.</p>
                        </div>
                    </div>
                </div>

                <!-- Miniature Vidéo Locale (Infirmier Picture-in-Picture) Fixée en Bas à Droite -->
                <div id="infirmier-local-video-container" style="position: absolute !important; bottom: 20px !important; right: 20px !important; width: 180px !important; height: 120px !important; z-index: 40 !important; background: #0f172a !important; border: 2px solid #0d9488 !important; border-radius: 14px !important; overflow: hidden !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6) !important; display: block !important;">
                    <span class="badge position-absolute top-0 start-0 m-2 fs-11 px-2.5 py-1 rounded-pill" style="z-index: 50 !important; background: rgba(15, 23, 42, 0.85) !important; backdrop-filter: blur(6px) !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.25) !important; pointer-events: none !important;">
                        <i class="fa-solid fa-user-nurse me-1" style="color: #2dd4bf;"></i> Vous (Infirmier)
                    </span>
                </div>
            </div>

            <!-- Barres de contrôles flottantes en bas Light -->
            <div class="modal-footer border-top border-light-subtle bg-white justify-content-center py-2" style="background: #ffffff !important; border-top: 1px solid #f1f5f9 !important; height: 66px !important; min-height: 66px !important;">
                <div class="d-flex align-items-center gap-3 px-4 py-1.5 rounded-pill shadow-sm" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <button type="button" id="btnInfToggleMic" onclick="toggleInfirmierMic()" class="btn btn-light border border-secondary-subtle rounded-circle shadow-sm" style="width: 46px; height: 46px; transition: all 0.2s;" title="Activer / Désactiver le micro">
                        <i class="fa-solid fa-microphone fs-16 text-dark"></i>
                    </button>
                    <button type="button" id="btnInfToggleCam" onclick="toggleInfirmierCam()" class="btn btn-light border border-secondary-subtle rounded-circle shadow-sm" style="width: 46px; height: 46px; transition: all 0.2s;" title="Activer / Désactiver la caméra">
                        <i class="fa-solid fa-video fs-16 text-dark"></i>
                    </button>

                    <div class="vr bg-secondary-subtle my-2 mx-1" style="height: 24px;"></div>
                    <button type="button" onclick="closeInfirmierVideoCall()" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm text-white d-flex align-items-center" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none; height: 42px;">
                        <i class="fa-solid fa-phone-slash me-2"></i> Raccrocher
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/livekit-client/dist/livekit-client.umd.min.js"></script>
<script>
    let infLivekitRoom = null;
    let infAudioTrack = null;
    let infVideoTrack = null;
    let currentInfConsultationId = null;
    let isInfMicMuted = false;
    let isInfCamOff = false;
    let infCallTimer = null;
    let infStatusPollInterval = null;
    let isInfirmierEndingCall = false;

    // Débloquer l'audio au premier clic de l'utilisateur
    const unlockInfAudio = () => {
        if (infLivekitRoom) {
            try { infLivekitRoom.startAudio(); } catch(e){}
        }
        const a = document.getElementById("remote-doctor-audio-stream");
        if (a) {
            a.muted = false;
            a.volume = 1.0;
            if (a.paused) a.play().catch(e => {});
        }
    };
    document.addEventListener('click', unlockInfAudio, { passive: true });
    document.addEventListener('touchstart', unlockInfAudio, { passive: true });

    async function getInfirmierLiveKitSDK() {
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

    function openInfirmierVideoCall(consultationId, token, livekitUrl, doctorName, patientName, channel) {
        currentInfConsultationId = consultationId;
        isInfirmierEndingCall = false;
        isInfMicMuted = false;
        isInfCamOff = false;

        // Nettoyage complet des conteneurs précédents
        const initRemoteContainer = document.getElementById('infirmier-remote-video-container');
        if (initRemoteContainer) {
            initRemoteContainer.querySelectorAll('.inf-remote-participant-card').forEach(c => c.remove());
            initRemoteContainer.querySelectorAll('video').forEach(v => v.remove());
        }
        document.querySelectorAll('[id^="inf-remote-audio-"]').forEach(a => a.remove());

        const docBadge = document.getElementById("infCallDoctorName");
        if (docBadge) {
            docBadge.innerHTML = `<i class="fa-solid fa-user-doctor me-1"></i> ${doctorName || 'Médecin'}`;
        }
        const patBadge = document.getElementById("infCallPatientName");
        if (patBadge) {
            patBadge.innerHTML = `<i class="fa-solid fa-user me-1"></i> ${patientName || 'Patient'}`;
        }

        const micBtn = document.getElementById('btnInfToggleMic');
        if (micBtn) {
            micBtn.className = 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
            micBtn.innerHTML = '<i class="fa-solid fa-microphone fs-16 text-dark"></i>';
        }

        // Réinitialiser le timer
        let seconds = 0;
        const durationEl = document.getElementById("infCallDuration");
        if (durationEl) durationEl.innerText = "00:00";
        if (infCallTimer) clearInterval(infCallTimer);
        infCallTimer = setInterval(() => {
            seconds++;
            const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
            const secs = String(seconds % 60).padStart(2, '0');
            if (durationEl) durationEl.innerText = `${mins}:${secs}`;
        }, 1000);

        // Polling de statut pour détecter si le médecin a terminé la consultation
        if (infStatusPollInterval) clearInterval(infStatusPollInterval);
        infStatusPollInterval = setInterval(async () => {
            if (!currentInfConsultationId || isInfirmierEndingCall) return;
            try {
                const targetId = currentInfConsultationId;
                const res = await fetch(`/infirmier/consultation/teleconsultation/call-status/${targetId}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.status === 'success') {
                        if (data.is_call_active || data.call_status === 'in_call' || data.call_status === 'calling') {
                            const waitingState = document.getElementById('infirmierDoctorWaitingState');
                            if (waitingState && infLivekitRoom && infLivekitRoom.remoteParticipants && infLivekitRoom.remoteParticipants.size > 0) {
                                waitingState.style.setProperty("display", "none", "important");
                            }
                        } else if (data.is_call_active === false && ['ended', 'doctor_ended', 'completed', 'cancelled', 'rejected'].includes(data.call_status)) {
                            if (infStatusPollInterval) clearInterval(infStatusPollInterval);
                            infStatusPollInterval = null;
                            if (!isInfirmierEndingCall) {
                                isInfirmierEndingCall = true;
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        text: "Le médecin a terminé la consultation.",
                                        icon: "info",
                                        timer: 3000,
                                        showConfirmButton: false
                                    });
                                }
                                closeInfirmierVideoCall(false);
                            }
                        }
                    }
                }
            } catch(e) {}
        }, 3000);

        // Ouvrir la modale de manière robuste
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modalEl = document.getElementById('infirmierVideoCallModal');
                if (modalEl) {
                    const inst = bootstrap.Modal.getOrCreateInstance(modalEl);
                    inst.show();
                }
            } else if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $('#infirmierVideoCallModal').modal('show');
            }
        } catch(e) {
            try { $('#infirmierVideoCallModal').modal('show'); } catch(err) {}
        }
        setTimeout(() => {
            $('.modal-backdrop').css('z-index', 1050);
            $('#infirmierVideoCallModal').css('z-index', 1055);
        }, 150);

        // Connexion LiveKit
        if (token && livekitUrl) {
            joinLiveKitInfirmierCall(livekitUrl, token, channel);
        }
    }

    function createInfirmierFallbackVideoTrack(label = "Infirmier(ère)") {
        const canvas = document.createElement("canvas");
        canvas.width = 640;
        canvas.height = 480;
        const ctx = canvas.getContext("2d");

        let angle = 0;
        const draw = () => {
            angle += 0.05;
            const grad = ctx.createLinearGradient(0, 0, 640, 480);
            grad.addColorStop(0, "#0f172a");
            grad.addColorStop(0.5, "#134e4a");
            grad.addColorStop(1, "#0d9488");
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, 640, 480);

            ctx.beginPath();
            ctx.arc(320, 200, 60 + Math.sin(angle) * 6, 0, Math.PI * 2);
            ctx.fillStyle = "rgba(13, 148, 136, 0.35)";
            ctx.fill();

            ctx.beginPath();
            ctx.arc(320, 200, 48, 0, Math.PI * 2);
            ctx.fillStyle = "#0d9488";
            ctx.fill();
            ctx.lineWidth = 3;
            ctx.strokeStyle = "#ffffff";
            ctx.stroke();

            ctx.font = "bold 28px sans-serif";
            ctx.fillStyle = "#ffffff";
            ctx.textAlign = "center";
            ctx.fillText("INF", 320, 210);

            ctx.font = "bold 18px sans-serif";
            ctx.fillStyle = "#e2e8f0";
            ctx.fillText(label, 320, 300);

            ctx.font = "13px sans-serif";
            ctx.fillStyle = "#94a3b8";
            ctx.fillText("Poste de téléconsultation actif", 320, 325);

            requestAnimationFrame(draw);
        };
        draw();

        const stream = canvas.captureStream(15);
        return stream.getVideoTracks()[0];
    }

    function attachInfirmierRemoteTrack(track, publication, participant) {
        if (!track) return;
        const remoteContainer = document.getElementById("infirmier-remote-video-container");
        if (!remoteContainer) return;

        const participantId = participant?.identity || (publication?.participantIdentity) || (track?.participant?.identity);
        if (!participantId) return;

        const isColleague = participantId.startsWith('colleague_');
        const defaultDocName = document.getElementById("infCallDoctorName")?.innerText.trim() || 'Dr. Médecin';
        const participantLabel = isColleague ? (participant?.name || 'Dr. Invité (Expert)') : (participant?.name || defaultDocName);

        if (track.kind === 'video') {
            let card = document.getElementById(`inf-remote-card-${participantId}`);
            if (!card) {
                card = document.createElement('div');
                card.id = `inf-remote-card-${participantId}`;
                card.className = 'inf-remote-participant-card position-relative overflow-hidden';
                card.style.cssText = 'position: relative; flex: 1 1 50%; min-width: 280px; height: 100%; min-height: 100%; background: #0f172a; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;';

                const badge = document.createElement('span');
                badge.className = 'badge position-absolute top-0 start-0 m-2 fs-11 px-2.5 py-1 rounded-pill shadow-sm';
                badge.style.cssText = 'z-index: 25; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); color: #ffffff; border: 1px solid rgba(255,255,255,0.2);';
                badge.innerHTML = `<i class="fa-solid fa-user-doctor text-teal me-1" style="color:#2dd4bf;"></i> ${participantLabel}`;
                card.appendChild(badge);

                remoteContainer.appendChild(card);
            }

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
            videoEl.play().catch(e => console.warn("Infirmier remote video play error:", e));

            updateInfirmierRemoteLayout(remoteContainer);
        } else if (track.kind === 'audio') {
            const audioId = `inf-remote-audio-${participantId}`;
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
                    console.warn("Lecture audio docteur bloquée, appel startAudio:", e);
                    if (infLivekitRoom) infLivekitRoom.startAudio();
                });
            }
        }
    }

    function removeInfirmierRemoteParticipant(participantId) {
        const card = document.getElementById(`inf-remote-card-${participantId}`);
        if (card) card.remove();
        const audio = document.getElementById(`inf-remote-audio-${participantId}`);
        if (audio) audio.remove();
        const container = document.getElementById("infirmier-remote-video-container");
        if (container) updateInfirmierRemoteLayout(container);
    }

    function updateInfirmierRemoteLayout(container) {
        if (!container) return;
        const cards = container.querySelectorAll('.inf-remote-participant-card');
        if (cards.length === 0) {
            const waitingState = document.getElementById('infirmierDoctorWaitingState');
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

    async function joinLiveKitInfirmierCall(url, token, channel) {
        try {
            const LiveKit = await getInfirmierLiveKitSDK();
            if (!LiveKit) {
                console.error("LiveKit SDK non disponible");
                return;
            }

            const SDK = window.LiveKitClient || window.LiveKit || LiveKit;
            const RoomClass = SDK?.Room || SDK?.default?.Room || LiveKit?.Room;
            const createLocalAudioTrackFn = SDK?.createLocalAudioTrack || SDK?.default?.createLocalAudioTrack || LiveKit?.createLocalAudioTrack;
            const LocalVideoTrackClass = SDK?.LocalVideoTrack || SDK?.default?.LocalVideoTrack || LiveKit?.LocalVideoTrack;
            const RoomEventEnum = SDK?.RoomEvent || SDK?.default?.RoomEvent || LiveKit?.RoomEvent || {};

            if (!RoomClass) return;

            if (infLivekitRoom) {
                try { await infLivekitRoom.disconnect(); } catch (e) {}
                infLivekitRoom = null;
            }

            infLivekitRoom = new RoomClass({
                adaptiveStream: true,
                dynacast: true,
            });

            const audioStatusEvent = RoomEventEnum.AudioPlaybackStatusChanged || "audioPlaybackChanged";
            infLivekitRoom.on(audioStatusEvent, () => {
                if (!infLivekitRoom.canPlaybackAudio) {
                    infLivekitRoom.startAudio();
                }
            });

            const muteEvent = RoomEventEnum.TrackMuted || 'trackMuted';
            const unmuteEvent = RoomEventEnum.TrackUnmuted || 'trackUnmuted';

            infLivekitRoom.on(muteEvent, (publication, participant) => {
                if (participant && (participant === infLivekitRoom.localParticipant || participant.identity === infLivekitRoom.localParticipant?.identity)) {
                    return;
                }
                if (publication.kind === 'video' && participant) {
                    const card = document.getElementById(`inf-remote-card-${participant.identity}`);
                    if (card) {
                        card.querySelectorAll('video').forEach(v => v.style.setProperty('display', 'none', 'important'));
                    }
                }
            });

            infLivekitRoom.on(unmuteEvent, (publication, participant) => {
                if (participant && (participant === infLivekitRoom.localParticipant || participant.identity === infLivekitRoom.localParticipant?.identity)) {
                    return;
                }
                if (publication.kind === 'video' && participant) {
                    const card = document.getElementById(`inf-remote-card-${participant.identity}`);
                    if (card) {
                        card.querySelectorAll('video').forEach(v => v.style.setProperty('display', 'block', 'important'));
                    }
                }
            });

            const pConnEvent = RoomEventEnum.ParticipantConnected || "participantConnected";
            infLivekitRoom.on(pConnEvent, (participant) => {
                console.log("Participant connecté au salon LiveKit:", participant.identity);
                const waitingState = document.getElementById('infirmierDoctorWaitingState');
                if (waitingState) waitingState.style.setProperty("display", "none", "important");
                if (participant.trackPublications) {
                    participant.trackPublications.forEach(publication => {
                        if (publication.track) {
                            attachInfirmierRemoteTrack(publication.track, publication, participant);
                        }
                    });
                }
            });

            const pDisconnEvent = RoomEventEnum.ParticipantDisconnected || "participantDisconnected";
            infLivekitRoom.on(pDisconnEvent, (participant) => {
                console.log("Participant déconnecté du salon LiveKit:", participant?.identity);
                if (participant?.identity) {
                    removeInfirmierRemoteParticipant(participant.identity);
                }
                // Si tous les participants distants sont partis, réafficher l'état d'attente
                if (infLivekitRoom && (!infLivekitRoom.remoteParticipants || infLivekitRoom.remoteParticipants.size === 0)) {
                    const waitingState = document.getElementById('infirmierDoctorWaitingState');
                    if (waitingState) waitingState.style.setProperty("display", "flex", "important");
                }
            });

            const subEvent = RoomEventEnum.TrackSubscribed || "trackSubscribed";
            infLivekitRoom.on(subEvent, (track, publication, participant) => {
                console.log("Infirmier: track subscribed:", track.kind, participant?.identity);
                const waitingState = document.getElementById('infirmierDoctorWaitingState');
                if (waitingState) waitingState.style.setProperty("display", "none", "important");
                attachInfirmierRemoteTrack(track, publication, participant);
            });

            const pubEvent = RoomEventEnum.TrackPublished || "trackPublished";
            infLivekitRoom.on(pubEvent, (publication, participant) => {
                const waitingState = document.getElementById('infirmierDoctorWaitingState');
                if (waitingState) waitingState.style.setProperty("display", "none", "important");
                if (publication.track) {
                    attachInfirmierRemoteTrack(publication.track, publication, participant);
                }
            });

            const unsubEvent = RoomEventEnum.TrackUnsubscribed || "trackUnsubscribed";
            infLivekitRoom.on(unsubEvent, (track, publication, participant) => {
                track.detach().forEach(el => el.remove());
                if (participant) removeInfirmierRemoteParticipant(participant.identity);
            });

            await infLivekitRoom.connect(url, token);
            try { await infLivekitRoom.startAudio(); } catch(e){}

            let engineWait = 0;
            while (infLivekitRoom.engine && !infLivekitRoom.engine.isEngineConnected && engineWait < 25) {
                await new Promise(r => setTimeout(r, 100));
                engineWait++;
            }

            // Traitement et détection immédiate des participants distants déjà dans la room
            const processRemoteDoctor = () => {
                if (infLivekitRoom && infLivekitRoom.remoteParticipants && infLivekitRoom.remoteParticipants.size > 0) {
                    const waitingState = document.getElementById('infirmierDoctorWaitingState');
                    if (waitingState) waitingState.style.setProperty("display", "none", "important");
                    infLivekitRoom.remoteParticipants.forEach(participant => {
                        if (participant.trackPublications) {
                            participant.trackPublications.forEach(publication => {
                                if (publication.track) {
                                    attachInfirmierRemoteTrack(publication.track, publication, participant);
                                }
                            });
                        }
                    });
                }
            };
            processRemoteDoctor();
            const remoteRetryInterval = setInterval(processRemoteDoctor, 400);
            setTimeout(() => clearInterval(remoteRetryInterval), 6000);

            // 1. Activer micro de l'infirmier avec fallbacks robustes
            const acquireInfMicrophone = async () => {
                try {
                    const micPub = await infLivekitRoom.localParticipant.setMicrophoneEnabled(true);
                    if (micPub) {
                        infAudioTrack = micPub.track || micPub;
                        console.log("Microphone infirmier activé !");
                        return;
                    }
                } catch(e1) {
                    console.warn("setMicrophoneEnabled infirmier retry:", e1);
                }

                try {
                    if (createLocalAudioTrackFn) {
                        const localAudio = await createLocalAudioTrackFn();
                        const pub = await infLivekitRoom.localParticipant.publishTrack(localAudio, { name: "audio" });
                        infAudioTrack = (pub && pub.track) ? pub.track : localAudio;
                        console.log("Microphone infirmier publié via createLocalAudioTrack !");
                        return;
                    }
                } catch(e2) {}

                try {
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        const rawStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                        const rawTrack = rawStream.getAudioTracks()[0];
                        if (rawTrack) {
                            const LocalAudioTrackClass = SDK?.LocalAudioTrack || SDK?.default?.LocalAudioTrack || LiveKit?.LocalAudioTrack;
                            const lTrack = LocalAudioTrackClass ? new LocalAudioTrackClass(rawTrack) : rawTrack;
                            const pub = await infLivekitRoom.localParticipant.publishTrack(lTrack, { name: "audio" });
                            infAudioTrack = (pub && pub.track) ? pub.track : lTrack;
                            console.log("Microphone infirmier activé via getUserMedia direct !");
                        }
                    }
                } catch(e3) {
                    console.error("Microphone infirmier indisponible:", e3);
                }
            };
            await acquireInfMicrophone();

            // 2. Activer caméra infirmier avec fallback visuel
            const localContainer = document.getElementById('infirmier-local-video-container');
            const renderInfLocalTrack = (vTrack) => {
                if (localContainer && vTrack) {
                    localContainer.querySelectorAll('video').forEach(v => v.remove());
                    localContainer.querySelectorAll('.local-cam-off-avatar').forEach(el => el.remove());
                    infVideoTrack = vTrack;
                    const localEl = vTrack.attach();
                    localEl.muted = true;
                    localEl.autoplay = true;
                    localEl.playsInline = true;
                    localEl.setAttribute('playsinline', 'true');
                    localEl.style.position = 'absolute';
                    localEl.style.top = '0';
                    localEl.style.left = '0';
                    localEl.style.width = '100%';
                    localEl.style.height = '100%';
                    localEl.style.minWidth = '100%';
                    localEl.style.minHeight = '100%';
                    localEl.style.objectFit = 'cover';
                    localEl.style.display = 'block';
                    localEl.style.borderRadius = '12px';
                    localContainer.appendChild(localEl);
                }
            };

            const acquireInfCamera = async () => {
                try {
                    const camPub = await infLivekitRoom.localParticipant.setCameraEnabled(true);
                    let vTrack = (camPub && camPub.track) ? camPub.track : null;

                    let waitCount = 0;
                    while (!vTrack && waitCount < 10) {
                        await new Promise(r => setTimeout(r, 150));
                        if (camPub && camPub.track) {
                            vTrack = camPub.track;
                        } else if (infLivekitRoom.localParticipant.videoTrackPublications) {
                            infLivekitRoom.localParticipant.videoTrackPublications.forEach(pub => {
                                if (pub.track && pub.kind === 'video') vTrack = pub.track;
                            });
                        }
                        waitCount++;
                    }

                    if (vTrack) return vTrack;
                } catch (e1) {
                    console.warn("setCameraEnabled infirmier échoué:", e1);
                }

                console.warn("Caméra physique infirmier indisponible, fallback visuel");
                const fallbackRaw = createInfirmierFallbackVideoTrack("Infirmier(ère)");
                if (fallbackRaw) {
                    let fallbackTrack = fallbackRaw;
                    if (LocalVideoTrackClass) {
                        try { fallbackTrack = new LocalVideoTrackClass(fallbackRaw); } catch(e){}
                    }
                    const pub = await infLivekitRoom.localParticipant.publishTrack(fallbackTrack, { name: "camera" });
                    return (pub && pub.track) ? pub.track : fallbackTrack;
                }
                return null;
            };

            try {
                const vTrack = await acquireInfCamera();
                if (vTrack) renderInfLocalTrack(vTrack);
            } catch (vErr) {
                console.error("Erreur acquisition caméra infirmier:", vErr);
            }

        } catch (error) {
            console.error("Erreur LiveKit Infirmier:", error);
        }
    }

    async function toggleInfirmierMic() {
        if (!infLivekitRoom || !infLivekitRoom.localParticipant) return;
        isInfMicMuted = !isInfMicMuted;
        try {
            if (infAudioTrack && typeof infAudioTrack.mute === 'function' && typeof infAudioTrack.unmute === 'function') {
                if (isInfMicMuted) {
                    await infAudioTrack.mute();
                } else {
                    await infAudioTrack.unmute();
                }
            } else {
                await infLivekitRoom.localParticipant.setMicrophoneEnabled(!isInfMicMuted);
            }
        } catch(e) {
            console.warn("Erreur bascule micro:", e);
        }
        const btn = document.getElementById('btnInfToggleMic');
        if (btn) {
            btn.className = isInfMicMuted ? 'btn btn-danger rounded-circle shadow-sm' : 'btn btn-light border border-secondary-subtle rounded-circle shadow-sm';
            btn.innerHTML = `<i class="fa-solid fa-microphone${isInfMicMuted ? '-slash' : ''} fs-16 ${isInfMicMuted ? 'text-white' : 'text-dark'}"></i>`;
        }
    }

    async function toggleInfirmierCam() {
        if (!infLivekitRoom || !infLivekitRoom.localParticipant) return;
        isInfCamOff = !isInfCamOff;
        const btn = document.getElementById('btnInfToggleCam');
        const localContainer = document.getElementById('infirmier-local-video-container');

        if (isInfCamOff) {
            try {
                if (infVideoTrack && typeof infVideoTrack.mute === 'function') {
                    await infVideoTrack.mute();
                }
                await infLivekitRoom.localParticipant.setCameraEnabled(false);
            } catch(e) {
                console.warn("Erreur désactivation caméra:", e);
            }
            if (localContainer) {
                localContainer.querySelectorAll('video').forEach(v => v.style.setProperty('display', 'none', 'important'));
                localContainer.querySelectorAll('.local-cam-off-avatar').forEach(el => el.remove());
                const offDiv = document.createElement('div');
                offDiv.className = 'local-cam-off-avatar w-100 h-100 position-absolute top-0 start-0 d-flex flex-column align-items-center justify-content-center';
                offDiv.style.zIndex = '32';
                offDiv.style.background = '#0f172a';
                offDiv.style.pointerEvents = 'none';
                offDiv.innerHTML = `
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-1" style="width: 44px; height: 44px; background: linear-gradient(135deg, #0d9488 0%, #047857 100%); color: #ffffff; font-weight: bold; font-size: 15px; border: 2px solid rgba(255,255,255,0.25);">
                        INF
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
                localContainer.querySelectorAll('.local-cam-off-avatar').forEach(el => el.remove());
            }

            try {
                if (infVideoTrack) {
                    if (typeof infVideoTrack.unmute === 'function') {
                        await infVideoTrack.unmute();
                    }
                    if (typeof infVideoTrack.attach === 'function') {
                        renderInfLocalTrack(infVideoTrack);
                    }
                }
                const vTrack = await acquireInfCamera();
                if (vTrack) renderInfLocalTrack(vTrack);
            } catch(e) {
                console.warn("Erreur réactivation caméra:", e);
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

    async function closeInfirmierVideoCall(notifyBackend = true) {
        if (isInfirmierEndingCall) return;
        isInfirmierEndingCall = true;

        if (typeof stopInfirmierRingtone === 'function') {
            stopInfirmierRingtone();
        }

        if (infCallTimer) {
            clearInterval(infCallTimer);
            infCallTimer = null;
        }

        if (infStatusPollInterval) {
            clearInterval(infStatusPollInterval);
            infStatusPollInterval = null;
        }

        const targetConsultationId = currentInfConsultationId;
        if (targetConsultationId && typeof dismissedCalls !== 'undefined') {
            dismissedCalls.add(String(targetConsultationId));
            if (typeof activeIncomingData !== 'undefined' && activeIncomingData && activeIncomingData.consultation_id == targetConsultationId) {
                const callKey = activeIncomingData.consultation_id + '_' + (activeIncomingData.call_started_at || activeIncomingData.channel || '');
                dismissedCalls.add(callKey);
            }
        }
        currentInfConsultationId = null;

        if (infLivekitRoom && infLivekitRoom.localParticipant) {
            try {
                infLivekitRoom.localParticipant.videoTrackPublications.forEach(pub => {
                    if (pub.track) try { pub.track.stop(); } catch(e){}
                });
                infLivekitRoom.localParticipant.audioTrackPublications.forEach(pub => {
                    if (pub.track) try { pub.track.stop(); } catch(e){}
                });
            } catch (e) {}
        }

        if (infAudioTrack) {
            try { infAudioTrack.stop(); } catch(e){}
            infAudioTrack = null;
        }
        if (infVideoTrack) {
            try { infVideoTrack.stop(); } catch(e){}
            infVideoTrack = null;
        }

        if (infLivekitRoom) {
            try { await infLivekitRoom.disconnect(); } catch (e) {}
            infLivekitRoom = null;
        }

        const remoteContainer = document.getElementById('infirmier-remote-video-container');
        if (remoteContainer) {
            remoteContainer.querySelectorAll('.inf-remote-participant-card').forEach(c => c.remove());
            remoteContainer.querySelectorAll('video').forEach(v => v.remove());
        }
        document.querySelectorAll('[id^="inf-remote-audio-"]').forEach(a => a.remove());
        const localContainer = document.getElementById('infirmier-local-video-container');
        if (localContainer) {
            localContainer.innerHTML = `
                <span class="badge position-absolute top-0 start-0 m-2 fs-11 px-2.5 py-1 rounded-pill" style="z-index: 50 !important; background: rgba(15, 23, 42, 0.85) !important; backdrop-filter: blur(6px) !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.25) !important; pointer-events: none !important;">
                    <i class="fa-solid fa-user-nurse me-1" style="color: #2dd4bf;"></i> Vous (Infirmier)
                </span>
            `;
        }
        const waitingState = document.getElementById('infirmierDoctorWaitingState');
        if (waitingState) waitingState.style.display = 'flex';

        $('#infirmierVideoCallModal').modal('hide');

        if (targetConsultationId && notifyBackend) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                await fetch(`/infirmier/consultation/teleconsultation/end/${targetConsultationId}`, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    }
                });
            } catch(e) {}
        }

        setTimeout(() => {
            if (window.location.pathname.includes('/dashboard') || window.location.pathname.includes('/consultation')) {
                window.location.reload();
            }
        }, 300);
    }
</script>

