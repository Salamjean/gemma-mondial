@extends('layouts.dashboard', ['title' => 'Programme des Téléconsultations'])

@section('content')
<div class="container-fluid px-15">

    <!-- En-tête de page -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-20 bg-white p-20 rounded-16 border shadow-xs">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-14 bg-teal-subtle text-teal d-flex align-items-center justify-content-center border border-teal-200" style="width: 52px; height: 52px; font-size: 24px; background-color: #ccfbf1; color: #0d9488;">
                <i class="fa-solid fa-headset"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-1">PROGRAMME DES TÉLÉCONSULTATIONS</h4>
                <p class="text-muted fs-13 mb-0">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i>
                    Liste partagée de toutes les téléconsultations programmées de l'établissement. Tout infirmier connecté peut lancer l'appel avec le médecin assigné.
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('infirmier.consultation.today') }}" class="btn btn-sm btn-outline-secondary rounded-10 px-15">
                <i class="fa-solid fa-arrow-left me-1"></i> Consultations du jour
            </a>
            <button type="button" class="btn btn-sm btn-primary rounded-10 px-15 shadow-sm" onclick="window.location.reload();">
                <i class="fa-solid fa-rotate me-1"></i> Actualiser
            </button>
        </div>
    </div>

    <!-- Cartes Statistiques & Filtres d'état -->
    <div class="row g-3 mb-20">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('infirmier.consultation.teleconsultations', ['filter' => 'today']) }}" class="text-decoration-none">
                <div class="p-20 rounded-16 border bg-white shadow-xs transition-all {{ $filter === 'today' ? 'border-primary border-2 shadow-sm' : '' }}">
                    <div class="d-flex align-items-center justify-content-between mb-10">
                        <span class="fs-13 fw-semibold text-muted text-uppercase">Aujourd'hui</span>
                        <div class="p-2 rounded-10 bg-primary-subtle text-primary" style="background-color: #e0e7ff; color: #4338ca;">
                            <i class="fa-solid fa-calendar-day fs-16"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 fs-28">{{ $countToday }}</h3>
                        <span class="fs-12 text-muted">à réaliser ce jour</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('infirmier.consultation.teleconsultations', ['filter' => 'upcoming']) }}" class="text-decoration-none">
                <div class="p-20 rounded-16 border bg-white shadow-xs transition-all {{ $filter === 'upcoming' ? 'border-info border-2 shadow-sm' : '' }}">
                    <div class="d-flex align-items-center justify-content-between mb-10">
                        <span class="fs-13 fw-semibold text-muted text-uppercase">À Venir</span>
                        <div class="p-2 rounded-10 bg-info-subtle text-info" style="background-color: #e0f2fe; color: #0284c7;">
                            <i class="fa-solid fa-calendar-plus fs-16"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 fs-28">{{ $countUpcoming }}</h3>
                        <span class="fs-12 text-muted">programmées</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('infirmier.consultation.teleconsultations', ['filter' => 'all']) }}" class="text-decoration-none">
                <div class="p-20 rounded-16 border bg-white shadow-xs transition-all {{ $filter === 'all' ? 'border-success border-2 shadow-sm' : '' }}">
                    <div class="d-flex align-items-center justify-content-between mb-10">
                        <span class="fs-13 fw-semibold text-muted text-uppercase">Toutes (Sans filtre)</span>
                        <div class="p-2 rounded-10 bg-success-subtle text-success" style="background-color: #dcfce7; color: #15803d;">
                            <i class="fa-solid fa-list-check fs-16"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 fs-28">{{ $countAll }}</h3>
                        <span class="fs-12 text-muted">au total</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('infirmier.consultation.teleconsultations', ['filter' => 'completed']) }}" class="text-decoration-none">
                <div class="p-20 rounded-16 border bg-white shadow-xs transition-all {{ $filter === 'completed' ? 'border-secondary border-2 shadow-sm' : '' }}">
                    <div class="d-flex align-items-center justify-content-between mb-10">
                        <span class="fs-13 fw-semibold text-muted text-uppercase">Terminées</span>
                        <div class="p-2 rounded-10 bg-light text-secondary">
                            <i class="fa-solid fa-circle-check fs-16"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h3 class="fw-bold text-dark mb-0 fs-28">{{ $countCompleted }}</h3>
                        <span class="fs-12 text-muted">consultations clôturées</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tableau Principal -->
    <div class="box rounded-16 border shadow-xs bg-white overflow-hidden">
        <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-video text-teal fs-18" style="color: #0d9488;"></i>
                <h4 class="box-title fw-bold text-dark mb-0">Séances de Téléconsultation</h4>
                <span class="badge bg-teal text-white rounded-pill px-2.5 py-1 fs-11" style="background-color: #0d9488;">{{ $consultations->count() }} résultat(s)</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="fs-12 text-muted"><i class="fa-solid fa-circle text-success fs-9 me-1"></i> Connexion LiveKit HD active</span>
            </div>
        </div>

        <div class="box-body p-20">
            <div class="table-responsive">
                <table id="example" class="table table-hover align-middle display nowrap w-p100">
                    <thead class="bg-light">
                        <tr>
                            <th class="fw-bold text-dark border-0 py-15 ps-15">Patient</th>
                            <th class="fw-bold text-dark border-0 py-15">Date & Heure</th>
                            <th class="fw-bold text-dark border-0 py-15">Médecin Destinataire</th>
                            <th class="fw-bold text-dark border-0 py-15">Constantes Physiques</th>
                            <th class="fw-bold text-dark border-0 py-15">Infirmier Référent</th>
                            <th class="fw-bold text-dark border-0 py-15 text-center">Statut</th>
                            <th class="fw-bold text-dark border-0 py-15 pe-15 text-center">Action Visioconférence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($consultations as $item)
                            @php
                                $patient = $item->patient ?? ($item->admission->patient ?? null);
                                $patientUser = optional($patient)->user;
                                $patientName = trim(($patientUser->name ?? '') . ' ' . ($patientUser->prenom ?? ''));
                                if(empty($patientName)) $patientName = 'Patient #' . $item->id;

                                $docUser = optional(optional($item->doctor)->user);
                                $docName = trim(($docUser->name ?? '') . ' ' . ($docUser->prenom ?? ''));
                                if(empty($docName)) $docName = 'Non assigné';

                                $docHospitalName = optional(optional($item->doctor)->hospital)->nom 
                                    ?? optional(optional($item->doctor)->hospital)->label 
                                    ?? optional($item->teleconsultationHospital)->nom 
                                    ?? optional($item->teleconsultationHospital)->label 
                                    ?? 'Hôpital partenaire';

                                $infUser = optional(optional($item->infirmier)->user);
                                $infName = trim(($infUser->name ?? '') . ' ' . ($infUser->prenom ?? ''));
                                if(empty($infName)) $infName = 'Infirmier de garde';

                                $desiredDateFormatted = $item->desired_date ? date('d/m/Y', strtotime($item->desired_date)) : ($item->date_consultation ? date('d/m/Y', strtotime($item->date_consultation)) : 'N/A');
                                $desiredDateRaw = $item->desired_date ?: $item->date_consultation;
                                $isToday = ($desiredDateRaw === date('Y-m-d'));
                                $isPast = ($desiredDateRaw && $desiredDateRaw < date('Y-m-d'));

                                $isCallLive = (bool)$item->is_call_active || in_array($item->call_status, ['calling', 'in_call', 'accepted']);
                                $isCompleted = ($item->status == 1);
                                $isCallEndedPendingDoc = (!$isCompleted && !$isCallLive && $item->call_status === 'ended');
                            @endphp
                            <tr class="{{ $isCallLive ? 'table-warning-light' : '' }}">
                                <!-- Patient -->
                                <td class="ps-15 py-15">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($patient && !empty($patient->img_url))
                                            <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;" alt="Avatar">
                                        @else
                                            <div class="rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center fw-bold border fs-16" style="width: 44px; height: 44px;">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold text-dark fs-14 mb-0">{{ $patientName }}</div>
                                            <div class="d-flex align-items-center gap-2 fs-12 text-muted">
                                                <span><i class="fa-solid fa-id-card me-1"></i>{{ $patient->code_patient ?? 'N/A' }}</span>
                                                @if(!empty($patient->telephone))
                                                    <span>• <i class="fa-solid fa-phone me-1"></i>{{ $patient->telephone }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Date & Heure -->
                                <td class="py-15">
                                    <div class="d-flex flex-column gap-1">
                                        <span class="fw-bold text-dark fs-13 d-flex align-items-center gap-1">
                                            <i class="fa-regular fa-calendar text-primary"></i>
                                            {{ $desiredDateFormatted }}
                                        </span>
                                        <span class="fs-12 text-muted d-flex align-items-center gap-1">
                                            <i class="fa-regular fa-clock text-muted"></i>
                                            {{ $item->desired_time ? $item->desired_time : 'Non précisée' }}
                                        </span>
                                        @if($isToday)
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5 fs-10 fw-semibold" style="width: fit-content; background-color: #e0e7ff; color: #4338ca;">Aujourd'hui</span>
                                        @elseif($isPast && !$isCompleted)
                                            <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-0.5 fs-10 fw-semibold" style="width: fit-content; background-color: #fee2e2; color: #b91c1c;">En attente</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Médecin Destinataire -->
                                <td class="py-15">
                                    <div class="d-flex flex-column gap-1">
                                        <span class="fw-bold text-dark fs-13 d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-user-doctor text-success me-1"></i>
                                            Dr. {{ $docName }}
                                        </span>
                                        <span class="fs-12 text-muted">
                                            <i class="fa-solid fa-hospital text-muted me-1"></i>
                                            {{ $docHospitalName }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Constantes Physiques -->
                                <td class="py-15">
                                    <div class="d-flex flex-wrap gap-1 fs-11" style="max-width: 200px;">
                                        @if($item->tension_arterielle)
                                            <span class="badge bg-light text-dark border px-2 py-1" title="Tension Artérielle">TA: <b>{{ $item->tension_arterielle }}</b></span>
                                        @endif
                                        @if($item->temperature)
                                            <span class="badge bg-light text-dark border px-2 py-1" title="Température">T°: <b>{{ $item->temperature }}°C</b></span>
                                        @endif
                                        @if($item->pouls)
                                            <span class="badge bg-light text-dark border px-2 py-1" title="Pouls">P: <b>{{ $item->pouls }} bpm</b></span>
                                        @endif
                                        @if($item->poids)
                                            <span class="badge bg-light text-dark border px-2 py-1" title="Poids">Pds: <b>{{ $item->poids }} kg</b></span>
                                        @endif
                                        @if(!$item->tension_arterielle && !$item->temperature && !$item->pouls && !$item->poids)
                                            <span class="text-muted fs-12">Non renseignées</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Infirmier ayant saisi -->
                                <td class="py-15">
                                    <span class="fs-13 text-dark fw-semibold">
                                        <i class="fa-solid fa-user-nurse text-info me-1"></i>
                                        {{ $infName }}
                                    </span>
                                </td>

                                <!-- Statut -->
                                <td class="py-15 text-center">
                                    @if($isCompleted)
                                        <span class="badge bg-success-light text-success border border-success fw-bold px-2.5 py-1.5 rounded-pill fs-11">
                                            <i class="fa-solid fa-circle-check me-1"></i> Clôturée (Médecin)
                                        </span>
                                    @elseif($isCallLive)
                                        <span class="badge bg-warning text-dark border border-warning fw-bold px-2.5 py-1.5 rounded-pill fs-11">
                                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 7px; height: 7px;"></span> En cours d'appel
                                        </span>
                                    @elseif($isCallEndedPendingDoc)
                                        <span class="badge bg-info-light text-info border border-info fw-bold px-2.5 py-1.5 rounded-pill fs-11" title="L'appel est terminé, en attente de validation médicale">
                                            <i class="fa-solid fa-user-doctor me-1"></i> En attente clôture médecin
                                        </span>
                                    @else
                                        <span class="badge bg-light-primary text-primary border border-primary fw-bold px-2.5 py-1.5 rounded-pill fs-11">
                                            <i class="fa-regular fa-clock me-1"></i> Programmée
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Visioconférence -->
                                <td class="pe-15 py-15 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        @if(!$isCompleted)
                                            <button type="button" 
                                                class="btn btn-sm btn-launch-scheduled-call text-white px-15 py-8 rounded-10 fw-bold shadow-sm d-inline-flex align-items-center gap-2"
                                                style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;"
                                                data-id="{{ $item->id }}"
                                                data-patient="{{ addslashes($patientName) }}"
                                                data-doctor="{{ addslashes('Dr. ' . $docName) }}"
                                                title="Lancer l'appel vidéo HD avec le médecin assigné">
                                                <i class="fa-solid fa-video fs-14"></i>
                                                <span>{{ $isCallLive ? "Rejoindre l'appel" : "Lancer l'appel" }}</span>
                                            </button>
                                        @else
                                            <span class="text-muted fs-12"><i class="fa-solid fa-lock me-1"></i> Clôturée</span>
                                        @endif

                                        <a href="{{ route('infirmier.consultation.formulaire', $item->id) }}" class="btn btn-sm btn-outline-secondary rounded-10 px-10 py-8" title="Consulter la fiche / Constantes">
                                            <i class="fa-solid fa-file-medical"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-50 text-muted">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-15" style="width: 64px; height: 64px;">
                                            <i class="fa-solid fa-headset text-muted fs-28"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Aucune téléconsultation trouvée</h5>
                                        <p class="text-muted fs-13 mb-15">Il n'y a actuellement aucune séance de téléconsultation correspondant à ce filtre pour votre établissement.</p>
                                        <a href="{{ route('infirmier.consultation.today') }}" class="btn btn-sm btn-primary rounded-10 px-20">
                                            <i class="fa-solid fa-notes-medical me-1"></i> Voir les consultations du jour
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Scripts pour le déclenchement direct de l'appel LiveKit -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-launch-scheduled-call', async function(e) {
        e.preventDefault();
        var btn = $(this);
        var consultId = btn.data('id');
        var patientName = btn.data('patient') || 'Patient';
        var docName = btn.data('doctor') || 'Médecin';

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Connexion...');

        try {
            const response = await fetch(`/infirmier/consultation/teleconsultation/start-scheduled/${consultId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (response.ok && result.status === 'success') {
                if (typeof openInfirmierVideoCall === 'function') {
                    openInfirmierVideoCall(
                        result.consultation_id,
                        result.token,
                        result.livekit_url,
                        result.doctor_name,
                        result.patient_name,
                        result.channel
                    );
                } else {
                    Swal.fire({
                        title: 'Appel démarré',
                        text: 'Le canal de téléconsultation est ouvert. Veuillez patienter pendant la connexion.',
                        icon: 'info',
                        confirmButtonColor: '#0d9488'
                    });
                }
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: result.message || "Impossible de démarrer l'appel de téléconsultation.",
                    icon: 'error',
                    confirmButtonColor: '#0d9488'
                });
            }
        } catch (error) {
            Swal.fire({
                title: 'Erreur réseau',
                text: "Une erreur est survenue lors de l'initialisation de l'appel vidéo.",
                icon: 'error',
                confirmButtonColor: '#0d9488'
            });
        } finally {
            btn.prop('disabled', false).html('<i class="fa-solid fa-video fs-14 me-1"></i> Lancer l\'appel');
        }
    });
});
</script>
@endsection
