<!-- Modal Notification Appel Entrant Consultation en Ligne pour Médecin (Design Premium Light) -->
<div class="modal fade" id="doctorIncomingCallModal" tabindex="-1" aria-labelledby="doctorIncomingCallModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-2xl rounded-4" style="overflow: hidden; background: #ffffff !important; border: 1px solid #0d9488 !important; border-radius: 24px !important;">
            
            <!-- Dynamic Top Header Accent -->
            <div style="height: 6px; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 50%, #2dd4bf 100%);"></div>

            <div class="modal-body text-center p-4">
                <!-- Icon Ringing Animation -->
                <div class="position-relative d-inline-block my-3">
                    <div class="spinner-grow text-teal" style="width: 4.5rem; height: 4.5rem; color: #0d9488;" role="status">
                        <span class="visually-hidden">Appel entrant...</span>
                    </div>
                    <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 d-flex align-items-center justify-content-center">
                        <div class="bg-teal text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg" style="width: 54px; height: 54px; background-color: #0d9488 !important;">
                            <i class="fa-solid fa-phone-volume fs-22 animate__animated animate__headShake animate__infinite"></i>
                        </div>
                    </div>
                </div>

                <h4 class="fw-bold text-dark mb-1">Appel de Téléconsultation</h4>
                <p class="text-muted fs-13 mb-3">Un patient sollicite une consultation en ligne immédiate</p>

                <!-- Patient Card Details -->
                <div class="p-3 mb-4 rounded-3 text-start shadow-sm" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-teal-subtle text-teal me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #ccfbf1; color: #0f766e;">
                            <i class="fa-solid fa-user-injured fs-18"></i>
                        </div>
                        <div>
                            <div class="text-xs text-uppercase fw-semibold text-muted">Patient</div>
                            <h5 class="fw-bold text-dark mb-0" id="incomingPatientName">Patient</h5>
                        </div>
                    </div>
                </div>

                <!-- Call Action Buttons -->
                <div class="d-flex align-items-center justify-content-center gap-3">
                    <button type="button" id="btnRejectIncomingCall" onclick="dismissIncomingCallModal()" class="btn btn-light border border-secondary-subtle rounded-pill px-4 py-2.5 fw-semibold text-secondary shadow-sm" style="min-width: 130px;">
                        <i class="fa-solid fa-xmark me-1"></i> Ignorer
                    </button>
                    <button type="button" id="btnAcceptIncomingCall" onclick="acceptIncomingPatientCall()" class="btn btn-success rounded-pill px-4 py-2.5 fw-bold text-white shadow-lg d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none; min-width: 160px;">
                        <i class="fa-solid fa-phone me-2 fs-16"></i> Décrocher
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentIncomingConsultationId = null;
    let doctorIncomingPollInterval = null;
    let ringtoneAudioContext = null;
    let ringtoneOscillatorInterval = null;
    let ignoredIncomingConsultationIds = new Set();

    function playRingtoneSound() {
        try {
            if (!ringtoneAudioContext) {
                ringtoneAudioContext = new (window.AudioContext || window.webkitAudioContext)();
            }
            if (ringtoneAudioContext.state === 'suspended') {
                ringtoneAudioContext.resume();
            }

            if (ringtoneOscillatorInterval) clearInterval(ringtoneOscillatorInterval);

            const triggerBeep = () => {
                if (!ringtoneAudioContext) return;
                const osc = ringtoneAudioContext.createOscillator();
                const gain = ringtoneAudioContext.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(440, ringtoneAudioContext.currentTime); // Note A4
                gain.gain.setValueAtTime(0.15, ringtoneAudioContext.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ringtoneAudioContext.currentTime + 0.8);
                osc.connect(gain);
                gain.connect(ringtoneAudioContext.destination);
                osc.start();
                osc.stop(ringtoneAudioContext.currentTime + 0.8);
            };

            triggerBeep();
            ringtoneOscillatorInterval = setInterval(triggerBeep, 2000);
        } catch (e) {
            console.warn("Ringtone playback error:", e);
        }
    }

    function stopRingtoneSound() {
        if (ringtoneOscillatorInterval) {
            clearInterval(ringtoneOscillatorInterval);
            ringtoneOscillatorInterval = null;
        }
    }

    async function checkDoctorIncomingCalls() {
        // Si le médecin est déjà en visioconférence ou si le modal d'appel vidéo est actif, ne pas afficher d'appel entrant
        if ($('#doctorVideoCallModal').hasClass('show') || $('#doctorVideoCallModal').is(':visible')) {
            stopRingtoneSound();
            $('#doctorIncomingCallModal').modal('hide');
            return;
        }

        try {
            const res = await fetch('/doctor/consultation/incoming-requests', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.status === 401 || res.status === 403) {
                if (doctorIncomingPollInterval) {
                    clearInterval(doctorIncomingPollInterval);
                    doctorIncomingPollInterval = null;
                }
                return;
            }

            if (res.ok) {
                const data = await res.json();
                if (data.has_incoming) {
                    if (ignoredIncomingConsultationIds.has(data.consultation_id)) {
                        stopRingtoneSound();
                        return;
                    }
                    if (currentIncomingConsultationId !== data.consultation_id) {
                        currentIncomingConsultationId = data.consultation_id;
                        document.getElementById('incomingPatientName').innerText = data.patient_name || 'Patient';
                        
                        const titleEl = document.querySelector('#doctorIncomingCallModal h4');
                        const subtitleEl = document.querySelector('#doctorIncomingCallModal .modal-body > p');
                        const accentEl = document.querySelector('#doctorIncomingCallModal .modal-content > div:first-child');
                        
                        if (data.is_emergency) {
                            if (titleEl) titleEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Appel d\'Urgence Médicale';
                            if (subtitleEl) subtitleEl.innerHTML = '<span class="text-danger fw-bold">Priorité Haute</span> &bull; Service : <strong>' + (data.service_name || 'Général') + '</strong>';
                            if (accentEl) accentEl.style.background = 'linear-gradient(90deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%)';
                        } else {
                            if (titleEl) titleEl.innerHTML = 'Appel de Téléconsultation';
                            if (subtitleEl) subtitleEl.innerText = 'Un patient sollicite une consultation en ligne immédiate';
                            if (accentEl) accentEl.style.background = 'linear-gradient(90deg, #0d9488 0%, #14b8a6 50%, #2dd4bf 100%)';
                        }

                        $('#doctorIncomingCallModal').modal('show');
                        playRingtoneSound();
                    }
                } else {
                    // Si plus d'appel en cours (ex: pris par un autre médecin, annulé par le patient ou 30s écoulées)
                    currentIncomingConsultationId = null;
                    stopRingtoneSound();
                    $('#doctorIncomingCallModal').modal('hide');
                }
            }
        } catch (err) {
            console.warn("Erreur polling incoming calls:", err);
        }
    }

    async function acceptIncomingPatientCall() {
        if (!currentIncomingConsultationId) return;

        const consultationId = currentIncomingConsultationId;
        ignoredIncomingConsultationIds.add(consultationId);
        currentIncomingConsultationId = null;
        stopRingtoneSound();
        $('#doctorIncomingCallModal').modal('hide');

        const btnAccept = document.getElementById('btnAcceptIncomingCall');
        if (btnAccept) btnAccept.disabled = true;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`/doctor/consultation/call/accept-patient-request/${consultationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();

            if (data.status === 'success') {
                if (typeof window.openDoctorVideoCall === 'function') {
                    window.openDoctorVideoCall(data.consultation_id, data.token, data.livekit_url, data.patient_name, data.channel);
                } else {
                    alert('Erreur: Module vidéo non initialisé.');
                }
            } else if (data.status === 'taken') {
                alert(data.message || 'Un autre médecin a déjà décroché cet appel.');
            } else {
                alert(data.message || 'Impossible de décrocher l\'appel.');
            }
        } catch (err) {
            console.error("Erreur lors du décrochage de l'appel:", err);
            alert("Erreur de connexion lors du décrochage.");
        } finally {
            if (btnAccept) btnAccept.disabled = false;
        }
    }

    function dismissIncomingCallModal() {
        stopRingtoneSound();
        const rejectedId = currentIncomingConsultationId;
        if (rejectedId) {
            ignoredIncomingConsultationIds.add(rejectedId);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch(`/doctor/consultation/call/reject/${rejectedId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(() => {
                if (typeof loadPendingOnlineRequests === 'function') {
                    loadPendingOnlineRequests();
                }
            }).catch(e => console.warn("Erreur lors du rejet de l'appel:", e));
        }
        currentIncomingConsultationId = null;
        $('#doctorIncomingCallModal').modal('hide');
    }

    $(document).ready(function() {
        // Démarrer le polling toutes les 3 secondes pour les médecins
        if (doctorIncomingPollInterval) clearInterval(doctorIncomingPollInterval);
        checkDoctorIncomingCalls();
        doctorIncomingPollInterval = setInterval(checkDoctorIncomingCalls, 3000);
    });
</script>
