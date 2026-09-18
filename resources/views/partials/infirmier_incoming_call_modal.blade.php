<!-- Modal & Système d'écoute d'appels entrants du Médecin pour l'Infirmier -->
<div class="modal fade" id="infirmierIncomingCallModal" tabindex="-1" aria-labelledby="infirmierIncomingCallModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden" style="border: 2px solid #0d9488 !important; box-shadow: 0 20px 50px rgba(13, 148, 136, 0.3) !important;">
            
            <!-- Dégradé vibrant supérieur -->
            <div style="height: 6px; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 50%, #2dd4bf 100%);"></div>

            <div class="modal-body p-4 text-center bg-white">
                <!-- Avatar Médecin animé avec pulsation -->
                <div class="position-relative d-inline-block mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-lg mx-auto" style="width: 86px; height: 86px; background: linear-gradient(135deg, #ccfbf1 0%, #99f6e4 100%); color: #0d9488; border: 3px solid #0d9488;">
                        <i class="fa-solid fa-user-doctor fs-38"></i>
                    </div>
                    <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle">
                        <span class="visually-hidden">Appel en direct</span>
                    </span>
                </div>

                <h5 class="fw-bold text-dark mb-1 fs-18" id="incomingDocName">Dr. Médecin</h5>
                <p class="text-teal fw-semibold fs-13 mb-3" style="color: #0d9488;">
                    <i class="fa-solid fa-video me-1"></i> Appel vidéo de téléconsultation
                </p>

                <!-- Carte d'information Patient -->
                <div class="p-3 rounded-3 mb-4 text-start border shadow-2xs" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="text-muted fs-11 text-uppercase fw-bold">Patient concerné :</span>
                        <span class="badge bg-white text-secondary border fs-10" id="incomingPatientCode"></span>
                    </div>
                    <div class="fw-bold text-dark fs-14" id="incomingPatientName">Nom du Patient</div>
                    <div class="text-muted fs-12 mt-1">
                        <i class="fa-solid fa-stethoscope me-1 text-teal" style="color: #0d9488;"></i>
                        <span id="incomingMotif">Téléconsultation</span>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2.5 fw-bold shadow-xs d-inline-flex align-items-center gap-2" onclick="dismissInfirmierIncomingCall()">
                        <i class="fa-solid fa-xmark fs-14"></i>
                        <span>Ignorer</span>
                    </button>
                    <button type="button" class="btn text-white rounded-pill px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2 pulse-answer-btn" id="btnAcceptIncomingCall" onclick="acceptInfirmierIncomingCall()" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;">
                        <i class="fa-solid fa-phone-volume fs-15"></i>
                        <span>Rejoindre la vidéo</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pulse-answer-btn {
    animation: pulseAnswerBtn 1.5s infinite;
}
@keyframes pulseAnswerBtn {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 148, 136, 0.7); }
    70% { transform: scale(1.04); box-shadow: 0 0 0 12px rgba(13, 148, 136, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 148, 136, 0); }
}
</style>

<script>
    let infIncomingPollingInterval = null;
    let infIncomingAudioCtx = null;
    let infIncomingRingtoneInterval = null;
    let activeIncomingData = null;
    let dismissedCalls = new Set();

    function playInfirmierRingtone() {
        if (infIncomingRingtoneInterval) return;
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            if (!infIncomingAudioCtx) infIncomingAudioCtx = new AudioContext();
            if (infIncomingAudioCtx.state === 'suspended') {
                infIncomingAudioCtx.resume().catch(e => {});
            }

            const triggerBeep = () => {
                try {
                    if (infIncomingAudioCtx.state === 'suspended') {
                        infIncomingAudioCtx.resume().catch(e => {});
                    }
                    const osc1 = infIncomingAudioCtx.createOscillator();
                    const osc2 = infIncomingAudioCtx.createOscillator();
                    const gain = infIncomingAudioCtx.createGain();

                    osc1.type = 'sine';
                    osc2.type = 'sine';
                    osc1.frequency.value = 440;
                    osc2.frequency.value = 480;

                    gain.gain.setValueAtTime(0.3, infIncomingAudioCtx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, infIncomingAudioCtx.currentTime + 1.8);

                    osc1.connect(gain);
                    osc2.connect(gain);
                    gain.connect(infIncomingAudioCtx.destination);

                    osc1.start();
                    osc2.start();
                    osc1.stop(infIncomingAudioCtx.currentTime + 1.8);
                    osc2.stop(infIncomingAudioCtx.currentTime + 1.8);
                } catch(e) {}
            };

            triggerBeep();
            infIncomingRingtoneInterval = setInterval(triggerBeep, 2600);
        } catch(e) {}
    }

    function stopInfirmierRingtone() {
        if (infIncomingRingtoneInterval) {
            clearInterval(infIncomingRingtoneInterval);
            infIncomingRingtoneInterval = null;
        }
    }

    async function checkInfirmierIncomingCalls() {
        // Si l'infirmier est déjà en visioconférence active, ne jamais afficher de modal ni sonner
        if ((typeof currentInfConsultationId !== 'undefined' && currentInfConsultationId) || 
            $('#infirmierVideoCallModal').hasClass('show') || 
            $('#infirmierVideoCallModal').is(':visible')) {
            stopInfirmierRingtone();
            if ($('#infirmierIncomingCallModal').hasClass('show')) {
                $('#infirmierIncomingCallModal').modal('hide');
            }
            return;
        }

        try {
            const res = await fetch('{{ route("infirmier.consultation.teleconsultation.incoming_call") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.status === 401 || res.status === 403) {
                if (infIncomingPollingInterval) clearInterval(infIncomingPollingInterval);
                stopInfirmierRingtone();
                return;
            }

            if (res.ok) {
                const data = await res.json();
                const callKey = data.consultation_id + '_' + (data.call_started_at || data.channel || '');
                
                const isDismissed = dismissedCalls.has(callKey) || dismissedCalls.has(String(data.consultation_id));

                if (data.has_incoming && !isDismissed) {
                    // Double vérification si l'appel n'est pas déjà ouvert
                    if ((typeof currentInfConsultationId !== 'undefined' && currentInfConsultationId == data.consultation_id) ||
                        $('#infirmierVideoCallModal').hasClass('show')) {
                        stopInfirmierRingtone();
                        return;
                    }

                    activeIncomingData = data;
                    document.getElementById('incomingDocName').innerText = data.doctor_name || 'Dr. Médecin';
                    document.getElementById('incomingPatientName').innerText = data.patient_name || 'Patient';
                    document.getElementById('incomingPatientCode').innerText = data.patient_code || '';
                    document.getElementById('incomingMotif').innerText = data.motif || 'Téléconsultation';

                    if (!$('#infirmierIncomingCallModal').hasClass('show')) {
                        $('#infirmierIncomingCallModal').modal('show');
                        playInfirmierRingtone();
                    }
                } else {
                    if ($('#infirmierIncomingCallModal').hasClass('show')) {
                        $('#infirmierIncomingCallModal').modal('hide');
                    }
                    stopInfirmierRingtone();
                }
            }
        } catch(e) {}
    }

    function dismissInfirmierIncomingCall() {
        stopInfirmierRingtone();
        if (activeIncomingData && activeIncomingData.consultation_id) {
            const callKey = activeIncomingData.consultation_id + '_' + (activeIncomingData.call_started_at || activeIncomingData.channel || '');
            dismissedCalls.add(callKey);
            dismissedCalls.add(String(activeIncomingData.consultation_id));
        }
        $('#infirmierIncomingCallModal').modal('hide');
    }

    async function acceptInfirmierIncomingCall() {
        stopInfirmierRingtone();
        if (!activeIncomingData) return;

        const dataToJoin = { ...activeIncomingData };
        currentInfConsultationId = dataToJoin.consultation_id;
        
        const callKey = dataToJoin.consultation_id + '_' + (dataToJoin.call_started_at || dataToJoin.channel || '');
        dismissedCalls.add(callKey);
        dismissedCalls.add(String(dataToJoin.consultation_id));

        $('#infirmierIncomingCallModal').modal('hide');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`/infirmier/consultation/teleconsultation/pickup/${dataToJoin.consultation_id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const result = await res.json();
            if (result.status === 'success') {
                if (typeof openInfirmierVideoCall === 'function') {
                    openInfirmierVideoCall(
                        result.consultation_id,
                        result.token,
                        result.livekit_url,
                        result.doctor_name,
                        result.patient_name,
                        result.channel
                    );
                }
            }
        } catch(err) {
            console.error("Erreur décrochage infirmier:", err);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        infIncomingPollingInterval = setInterval(checkInfirmierIncomingCalls, 3000);
        setTimeout(checkInfirmierIncomingCalls, 1000);
    });
</script>
