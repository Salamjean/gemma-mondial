<!-- Section Demandes de Téléconsultation en ligne en attente (Format Tableau Premium) -->
<div class="card border-0 shadow-sm rounded-20 bg-white mb-4 overflow-hidden" id="pendingOnlineRequestsBox" style="border: 1px solid rgba(13, 148, 136, 0.25) !important;">
    <!-- Barre d'accentuation supérieure dégradée -->
    <div style="height: 4px; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 50%, #2dd4bf 100%);"></div>

    <div class="card-header border-0 bg-white p-3 p-md-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm flex-shrink-0" style="width: 48px; height: 48px; background: rgba(13, 148, 136, 0.12); color: #0d9488;">
                <i class="fa-solid fa-headset fs-20"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-0 fs-16 d-flex align-items-center gap-2 flex-wrap">
                    <span>DEMANDES DE TÉLÉCONSULTATION EN LIGNE EN ATTENTE</span>
                    <span class="d-inline-flex align-items-center px-2.5 py-1 rounded-pill fs-11 fw-semibold" style="background: rgba(13, 148, 136, 0.12); color: #0f766e;">
                        <span class="pulse-live-dot me-1.5"></span> EN DIRECT
                    </span>
                </h5>
                <p class="text-muted fs-12 mb-0">Liste des appels vidéo en attente de prise en charge médicale</p>
            </div>
        </div>
        <div>
            <span class="badge rounded-pill px-3 py-2 fs-13 fw-bold shadow-sm" id="pendingCountBadge" style="background: #0d9488; color: #ffffff;">
                0 demande(s) en attente
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="pendingOnlineTable">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr class="fs-12 text-uppercase text-secondary fw-bold">
                        <th class="ps-4 py-3" style="min-width: 200px;">Patient</th>
                        <th class="py-3" style="min-width: 170px;">Motif / Prestation</th>
                        <th class="py-3" style="min-width: 140px;">Heure de la demande</th>
                        <th class="py-3 text-center" style="min-width: 150px;">Appels en absence</th>
                        <th class="py-3 text-center" style="min-width: 130px;">Statut</th>
                        <th class="pe-4 py-3 text-end" style="min-width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody id="pendingOnlineList">
                    <!-- État initial sans demandes -->
                    <tr id="noPendingRow">
                        <td colspan="6" class="text-center py-4 px-3">
                            <div class="py-3">
                                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 54px; height: 54px; background: rgba(13, 148, 136, 0.1); color: #0d9488;">
                                    <i class="fa-solid fa-video-slash fs-22"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1 fs-14">Aucune demande de téléconsultation en attente</h6>
                                <p class="text-muted fs-12 mb-3 max-w-400 mx-auto">Les appels vidéo lancés par les patients apparaîtront ici automatiquement avec notifications.</p>
                                <span class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill fs-11 text-muted bg-white border shadow-sm">
                                    <span class="spinner-grow spinner-grow-sm text-teal" style="width: 8px; height: 8px; color: #0d9488;" role="status"></span>
                                    Écoute des demandes en temps réel...
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.pulse-live-dot {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: pulse-live-anim 1.8s infinite;
}
@keyframes pulse-live-anim {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}
#pendingOnlineTable tbody tr {
    transition: background-color 0.2s ease-in-out;
}
#pendingOnlineTable tbody tr:hover {
    background-color: #f0fdf4 !important;
}
.btn-pickup-call {
    background: linear-gradient(135deg, #0d9488 0%, #059669 100%) !important;
    border: none !important;
    color: #ffffff !important;
    transition: all 0.2s ease-in-out;
}
.btn-pickup-call:hover {
    background: linear-gradient(135deg, #0f766e 0%, #047857 100%) !important;
    transform: scale(1.03);
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35) !important;
    color: #ffffff !important;
}
</style>

<script>
let doctorAudioCtx = null;
let doctorRingtoneInterval = null;

// Débloquer l'AudioContext dès la première interaction sur la page du médecin
document.addEventListener('click', function unlockDoctorAudio() {
    if (doctorAudioCtx && doctorAudioCtx.state === 'suspended') {
        doctorAudioCtx.resume();
    }
}, { passive: true });

function playDoctorRingtone() {
    if (doctorRingtoneInterval) return;
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        if (!doctorAudioCtx) {
            doctorAudioCtx = new AudioContext();
        }
        if (doctorAudioCtx.state === 'suspended') {
            doctorAudioCtx.resume();
        }
        
        const triggerBeep = () => {
            try {
                if (doctorAudioCtx.state === 'suspended') {
                    doctorAudioCtx.resume();
                }
                const osc1 = doctorAudioCtx.createOscillator();
                const osc2 = doctorAudioCtx.createOscillator();
                const gain = doctorAudioCtx.createGain();

                osc1.type = 'sine';
                osc2.type = 'sine';
                osc1.frequency.value = 440;
                osc2.frequency.value = 480;

                gain.gain.setValueAtTime(0.25, doctorAudioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, doctorAudioCtx.currentTime + 1.8);

                osc1.connect(gain);
                osc2.connect(gain);
                gain.connect(doctorAudioCtx.destination);

                osc1.start();
                osc2.start();
                osc1.stop(doctorAudioCtx.currentTime + 1.8);
                osc2.stop(doctorAudioCtx.currentTime + 1.8);
            } catch (e) {}
        };

        triggerBeep();
        doctorRingtoneInterval = setInterval(triggerBeep, 2500);
    } catch (e) {
        console.warn("Erreur démarrage sonnerie médecin:", e);
    }
}

function stopDoctorRingtone() {
    if (doctorRingtoneInterval) {
        clearInterval(doctorRingtoneInterval);
        doctorRingtoneInterval = null;
    }
}

async function loadPendingOnlineRequests() {
    try {
        const res = await fetch('{{ route("doctor.consultation.pending_online_requests") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (res.status === 401 || res.status === 403) {
            if (pendingPollingInterval) {
                clearInterval(pendingPollingInterval);
                pendingPollingInterval = null;
            }
            stopDoctorRingtone();
            return;
        }
        if (res.ok) {
            const data = await res.json();
            if (data.status === 'success') {
                const list = data.requests || [];
                const container = document.getElementById('pendingOnlineList');
                const countBadge = document.getElementById('pendingCountBadge');
                if (countBadge) countBadge.innerText = `${list.length} demande(s) en attente`;

                // Déclencher la sonnerie uniquement pour les appels en direct (live ringing)
                const hasActiveRingingRequest = list.some(req => req.is_call_active && req.call_status === 'calling' && !req.is_taken_by_me);
                if (hasActiveRingingRequest) {
                    playDoctorRingtone();
                } else {
                    stopDoctorRingtone();
                }

                if (!container) return;

                if (list.length === 0) {
                    container.innerHTML = `
                        <tr id="noPendingRow">
                            <td colspan="6" class="text-center py-4 px-3">
                                <div class="py-3">
                                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 54px; height: 54px; background: rgba(13, 148, 136, 0.1); color: #0d9488;">
                                        <i class="fa-solid fa-video-slash fs-22"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1 fs-14">Aucune demande de téléconsultation en attente</h6>
                                    <p class="text-muted fs-12 mb-3 max-w-400 mx-auto">Les appels vidéo lancés par les patients apparaîtront ici automatiquement avec notifications.</p>
                                    <span class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill fs-11 text-muted bg-white border shadow-sm">
                                        <span class="spinner-grow spinner-grow-sm text-teal" style="width: 8px; height: 8px; color: #0d9488;" role="status"></span>
                                        Écoute des demandes en temps réel...
                                    </span>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                let html = '';
                list.forEach(req => {
                    const avatarHtml = req.patient_photo
                        ? `<img src="${req.patient_photo}" class="rounded-circle object-fit-cover me-3 shadow-sm flex-shrink-0" style="width: 42px; height: 42px; border: 2px solid #0d9488;" alt="Avatar">`
                        : `<div class="rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold fs-14 shadow-sm flex-shrink-0" style="width: 42px; height: 42px; background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); color: #0f766e; border: 2px solid #0d9488;">
                            ${req.patient_initials || 'PT'}
                           </div>`;

                    const statusHtml = req.is_taken_by_me
                        ? `<span class="badge bg-success text-white px-3 py-1.5 rounded-pill fs-11 fw-semibold shadow-xs">
                            <i class="fa-solid fa-headset me-1"></i> En cours
                           </span>`
                        : (req.call_status === 'missed' || !req.is_call_active)
                            ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fs-11 fw-semibold shadow-xs" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                                <i class="fa-solid fa-phone-slash me-1"></i> Appel en absence
                               </span>`
                            : `<span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill fs-11 fw-semibold shadow-xs" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                <i class="fa-solid fa-phone-volume animate__animated animate__headShake animate__infinite me-1"></i> En direct
                               </span>`;

                    const btnHtml = req.is_taken_by_me
                        ? `<button type="button" onclick="pickupPendingCall(${req.id})" class="btn btn-pickup-call btn-sm rounded-pill px-3.5 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                            <i class="fa-solid fa-video fs-13"></i>
                            <span>Rejoindre l'appel</span>
                           </button>`
                        : `<button type="button" onclick="pickupPendingCall(${req.id})" class="btn btn-pickup-call btn-sm rounded-pill px-3.5 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                            <i class="fa-solid fa-video fs-13"></i>
                            <span>Prendre en charge</span>
                           </button>`;

                    const missedCellHtml = (req.patient_missed_count && req.patient_missed_count > 0)
                        ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fs-12 fw-bold shadow-xs">
                            <i class="fa-solid fa-phone-slash me-1"></i> ${req.patient_missed_count} manqué(s)
                           </span>`
                        : `<span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill fs-11">
                            Aucun
                           </span>`;

                    html += `
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    ${avatarHtml}
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 fs-14">${req.patient_name}</h6>
                                        ${req.patient_code ? `<span class="badge bg-light text-secondary border fs-11 mt-0.5">${req.patient_code}</span>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-teal-subtle text-teal-800 fw-medium px-2.5 py-1.5 rounded-8 fs-12" style="background: rgba(13, 148, 136, 0.1); color: #0f766e;">
                                    <i class="fa-solid fa-stethoscope me-1"></i> ${req.motif || 'Téléconsultation'}
                                </span>
                            </td>
                            <td class="py-3 text-muted fs-13">
                                <i class="fa-regular fa-clock me-1 text-teal" style="color: #0d9488;"></i> ${req.time_formatted || req.created_at}
                            </td>
                            <td class="py-3 text-center">
                                ${missedCellHtml}
                            </td>
                            <td class="py-3 text-center">
                                ${statusHtml}
                            </td>
                            <td class="pe-4 py-3 text-end">
                                ${btnHtml}
                            </td>
                        </tr>
                    `;
                });
                container.innerHTML = html;
            }
        }
    } catch (err) {
        console.warn("Erreur chargement demandes en attente:", err);
    }
}

async function pickupPendingCall(consultationId) {
    stopDoctorRingtone();
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch(`/doctor/consultation/pickup-pending/${consultationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const data = await res.json();
        if (data.status === 'success') {
            if (typeof openDoctorVideoCall === 'function') {
                openDoctorVideoCall(data.consultation_id, data.token, data.livekit_url, data.patient_name, data.channel);
            }
            loadPendingOnlineRequests();
        } else {
            alert(data.message || 'Demande déjà prise en charge.');
            loadPendingOnlineRequests();
        }
    } catch (e) {
        console.error("Erreur prise en charge:", e);
    }
}

if (typeof window.pendingOnlineInterval !== 'undefined') {
    clearInterval(window.pendingOnlineInterval);
}
$(document).ready(function() {
    loadPendingOnlineRequests();
    window.pendingOnlineInterval = setInterval(loadPendingOnlineRequests, 4000);
});
</script>
