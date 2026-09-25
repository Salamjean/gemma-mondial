<div class="row" id="infirmierDashboardContainer">
    @if (\Illuminate\Support\Facades\Auth::user()->infirmier->serviceHospital->service->id == 5)
        {{-- =========================================================================
             SECTION SOINS INFIRMIERS (SERVICE ID == 5)
             ========================================================================= --}}
        <!-- 4 KPI Cards -->
        <div class="col-12 mb-3">
            <div class="row g-2 g-md-3">
                <div class="col-6 col-lg-3">
                    <div class="box bg-success-light mb-0 h-100 shadow-sm border-0 rounded-12">
                        <div class="box-body p-2 p-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-icon-wrapper p-1 p-md-2 me-2">
                                    <img src="{{ asset('assets/icons/admission_history.png') }}" alt="icon" style="max-width: 40px; height: auto;">
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0 fw-bold text-success fs-20 fs-md-26">
                                        {{ $count['pending_count'] }}
                                    </h3>
                                    <p class="text-fade mt-1 mb-0 text-success fs-11 fs-md-13 fw-semibold">Soins en attente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="box bg-success-light mb-0 h-100 shadow-sm border-0 rounded-12">
                        <div class="box-body p-2 p-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-icon-wrapper p-1 p-md-2 me-2">
                                    <img src="{{ asset('assets/icons/admission_valid.png') }}" alt="icon" style="max-width: 40px; height: auto;">
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0 fw-bold text-success fs-20 fs-md-26">
                                        {{ $count['success_count'] }}
                                    </h3>
                                    <p class="text-fade mt-1 mb-0 text-success fs-11 fs-md-13 fw-semibold">Soins effectués</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="box bg-success-light mb-0 h-100 shadow-sm border-0 rounded-12">
                        <div class="box-body p-2 p-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-icon-wrapper p-1 p-md-2 me-2">
                                    <img src="{{ asset('assets/icons/admission_money.png') }}" alt="icon" style="max-width: 40px; height: auto;">
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0 fw-bold text-success fs-20 fs-md-26">
                                        {{ $count['payment_pending_count'] }}
                                    </h3>
                                    <p class="text-fade mt-1 mb-0 text-success fs-11 fs-md-13 fw-semibold">Paiements en attente</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-3">
                    <div class="box bg-success-light mb-0 h-100 shadow-sm border-0 rounded-12">
                        <div class="box-body p-2 p-md-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="kpi-icon-wrapper p-1 p-md-2 me-2">
                                    <img src="{{ asset('assets/icons/admission_money.png') }}" alt="icon" style="max-width: 40px; height: auto;">
                                </div>
                                <div class="text-end">
                                    <h3 class="mb-0 fw-bold text-success fs-20 fs-md-26">
                                        {{ $count['payment_success_count'] }}
                                    </h3>
                                    <p class="text-fade mt-1 mb-0 text-success fs-11 fs-md-13 fw-semibold">Paiements effectués</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Nouveaux Soins -->
        <div class="col-12 mb-3">
            <div class="box shadow-sm border-0 rounded-12">
                <div class="box-header py-3 px-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 border-bottom">
                    <div class="badge badge-warning fs-14 py-2 px-3 fw-bold">
                        <i class="fa-solid fa-notes-medical me-1"></i> LISTE DE VOS NOUVEAUX SOINS
                    </div>
                    @php
                        $infirmierId = \Illuminate\Support\Facades\Auth::user()->infirmier->id;
                        $countAllInfCares = \App\Models\CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
                            $query->where('infirmier_id', $infirmierId);
                        })->where('status', '!=', 'success')->count();
                    @endphp
                    <a href="{{ route('infirmier.care.all') }}" class="btn btn-sm btn-primary rounded-8 fw-bold shadow-sm d-inline-flex align-items-center">
                        <i class="fa-solid fa-list-check me-1"></i> Tous les soins (Sans filtre)
                        <span class="badge bg-white text-primary ms-1 fs-11">{{ $countAllInfCares }}</span>
                    </a>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="ps-3 py-2">Code patient</th>
                                    <th class="py-2">Nom du Patient</th>
                                    <th class="py-2">Acte médical</th>
                                    <th class="py-2">Motif</th>
                                    <th class="py-2">Statut</th>
                                    <th class="pe-3 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cares as $key => $item)
                                    @if ($item->status === 'pending')
                                        <tr>
                                            <td class="ps-3 fw-bold">{{ $item->admission->patient->code_patient }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->admission->patient->user->name }} {{ $item->admission->patient->user->prenom }}</div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->admission->prestationHospital->prestationService->libelle }}</span></td>
                                            <td><small class="text-muted">{{ $item->admission->motif_consultation }}</small></td>
                                            <td>
                                                <span class="badge badge-warning">En attente</span>
                                            </td>
                                            <td class="pe-3 text-center">
                                                <a href="{{ route('infirmier.care.formulaire', $item->id) }}" class="btn btn-sm btn-info rounded-8 px-2 py-1" title="Commencer le soin">
                                                    <i class="fa-solid fa-play me-1"></i> Commencer
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Aucun nouveau soin en attente.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Soins en cours -->
        <div class="col-12 mb-3">
            <div class="box shadow-sm border-0 rounded-12">
                <div class="box-header py-3 px-3 border-bottom">
                    <div class="badge badge-warning fs-14 py-2 px-3 fw-bold">
                        <i class="fa-solid fa-spinner me-1"></i> LISTE DE VOS SOINS EN COURS
                    </div>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th class="ps-3 py-2">Code patient</th>
                                    <th class="py-2">Nom du Patient</th>
                                    <th class="py-2">Acte médical</th>
                                    <th class="py-2">Motif</th>
                                    <th class="py-2">Statut</th>
                                    <th class="pe-3 py-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cares as $key => $item)
                                    @if ($item->status !== 'pending')
                                        <tr>
                                            <td class="ps-3 fw-bold">{{ $item->admission->patient->code_patient }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->admission->patient->user->name }} {{ $item->admission->patient->user->prenom }}</div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->admission->prestationHospital->prestationService->libelle }}</span></td>
                                            <td><small class="text-muted">{{ $item->admission->motif_consultation }}</small></td>
                                            <td>
                                                @if ($item->status === 'payment_pending')
                                                    <span class="badge badge-info">En attente de paiement</span>
                                                @elseif ($item->status === 'payment_success')
                                                    <span class="badge badge-success">Paiement effectué</span>
                                                @endif
                                            </td>
                                            <td class="pe-3 text-center">
                                                @if ($item->status === 'payment_pending')
                                                    <a href="{{ route('infirmier.care.detail', $item->id) }}" class="btn btn-sm btn-info rounded-8 px-2 py-1" title="Voir détail">
                                                        <i class="fa-solid fa-eye me-1"></i> Détail
                                                    </a>
                                                @elseif ($item->status === 'payment_success')
                                                    <a href="{{ route('infirmier.care.payment_success', $item->id) }}" class="btn btn-sm btn-info rounded-8 px-2 py-1" title="Continuer">
                                                        <i class="fa-solid fa-arrow-right me-1"></i> Continuer
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Aucun soin en cours.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- =========================================================================
             SECTION AUTRES SERVICES / CONSULTATIONS INFIRMIÈRES
             ========================================================================= --}}
        
        <!-- Bloc Patients à consulter ce jour -->
        <div class="col-12 mb-3">
            <div class="box shadow-sm border-0 rounded-12 overflow-hidden" style="background: rgba(9, 187, 134, 0.288); padding : 10px 20px;">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
                    <div class="badge badge-dark" style="font-size: 20px;">
                        PATIENTS A CONSULTER POUR CE JOUR
                    </div>
                    @php
                        $infirmierId = \Illuminate\Support\Facades\Auth::user()->infirmier->id;
                        $countAllInfConsultations = \App\Models\Consultation::where(function ($q) use ($infirmierId) {
                            $q->where('infirmier_id', $infirmierId)
                              ->orWhereNull('infirmier_id');
                        })->where('status_inf', 0)->count();
                    @endphp
                    <a href="{{ route('infirmier.consultation.all') }}" class="btn btn-sm btn-primary fw-bold shadow-sm">
                        <i class="fa-solid fa-users me-1"></i> Toutes les consultations
                        <span class="badge bg-white text-primary ms-1 fs-12">{{ $countAllInfConsultations }}</span>
                    </a>
                </div>
                <div class="box-body p-0 text-center">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="bb-2" style="text-align: left;">Heure</th>
                                    <th class="bb-2">Code</th>
                                    <th class="bb-2">N° dossier médical</th>
                                    <th class="bb-2">Nom & prénom(s)</th>
                                    <th class="bb-2">Type de la visite</th>
                                    <th class="bb-2">Motif de la visite</th>
                                    <th class="bb-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $item)
                                    <tr>
                                        <td style="text-align: left;">{{ $item->created_at->format('H:i:s') }}</td>
                                        <td style="text-align: center;"><b>{{ $item->code_consultation }}</b></td>
                                        <td style="text-align: center;"><span class="fw-bold">{{ $item->patient->code_patient }}</span></td>
                                        <td style="text-align: center; text-transform: capitalize; width: 300px;">
                                            <span class="badge text-dark fw-900 fs-14">{{ $item->patient->user->name }} {{ $item->patient->user->prenom }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-primary">{{ $item->prestationHospital->prestationService->libelle }}</span>
                                        </td>
                                        <td style="width: 300px;">
                                            <span class="badge text-dark fw-900 fs-14">{{ $item->admission->motif_consultation }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('infirmier.consultation.formulaire', $item->id) }}"
                                                class="btn btn-sm" style="background: rgba(214, 110, 62, 0.452);"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Faire la consultation">
                                                <span>Commencer</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Aucun patient à consulter pour aujourd'hui.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Téléconsultations assignées aux médecins (Infirmier) -->
        @php
            $infTeleconsultations = \App\Models\Consultation::where(function ($q) {
                    $q->whereIn('orientation_infirmier', ['teleconsultation', 'urgence'])
                      ->orWhere('is_urgence', 1);
                })
                ->where(function ($q) use ($infirmierId) {
                    $q->where('infirmier_id', $infirmierId)
                      ->orWhereNull('infirmier_id');
                })
                ->where('status', 0)
                ->whereNotIn('call_status', ['completed', 'cancelled'])
                ->with(['doctor.user', 'patient.user', 'prestationHospital.prestationService'])
                ->latest('created_at')
                ->get();
        @endphp
        <div class="col-12 mb-3" id="infirmierTeleconsultSection" style="{{ $infTeleconsultations->count() > 0 ? '' : 'display: none;' }}">
            <div class="box border-0 shadow-sm rounded-12 bg-white overflow-hidden" style="border: 1px solid rgba(13, 148, 136, 0.25) !important;">
                <div style="height: 4px; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 50%, #2dd4bf 100%);"></div>
                <div class="box-header border-0 bg-white p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(13, 148, 136, 0.12); color: #0d9488;">
                            <i class="fa-solid fa-headset fs-16"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0 fs-15 d-flex align-items-center gap-2 flex-wrap">
                                <span>TÉLÉCONSULTATIONS AVEC LES MÉDECINS</span>
                                <span id="infTeleconsultCountBadge" class="badge bg-teal-subtle text-teal-800 rounded-pill fs-11" style="background: rgba(13,148,136,0.12); color: #0f766e;">
                                    {{ $infTeleconsultations->count() }} en cours / programmée(s)
                                </span>
                            </h5>
                            <p class="text-muted fs-12 mb-0">Appels vidéo médicaux assignés pour vos patients</p>
                        </div>
                    </div>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <tr class="fs-11 text-uppercase text-secondary fw-bold">
                                    <th class="ps-3 py-2">Patient</th>
                                    <th class="py-2">Médecin</th>
                                    <th class="py-2">Date & Heure</th>
                                    <th class="py-2">Motif</th>
                                    <th class="py-2 text-center">Statut</th>
                                    <th class="pe-3 py-2 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody id="infTeleconsultTableBody">
                                @foreach ($infTeleconsultations as $t)
                                    @php
                                        $tPatUser = optional(optional($t->patient)->user);
                                        $tPatName = trim(($tPatUser->name ?? '') . ' ' . ($tPatUser->prenom ?? '')) ?: 'Patient';
                                        $tDocUser = optional(optional($t->doctor)->user);
                                        $hasAssignedDoc = !empty($t->doctor_id) && !empty($tDocUser->name);
                                        $tDocName = $hasAssignedDoc ? ('Dr. ' . trim($tDocUser->name . ' ' . ($tDocUser->prenom ?? ''))) : 'Médecins de garde';
                                        $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
                                        $infName = trim((auth()->user()->name ?? '') . ' ' . (auth()->user()->prenom ?? ''));
                                        $token = !empty($t->call_channel) ? \App\Services\LiveKitTokenService::generateToken($t->call_channel, 'infirmier_' . auth()->user()->id, 'Inf. ' . $infName) : '';
                                        $isEmergency = ($t->orientation_infirmier === 'urgence' || $t->is_urgence == 1);
                                    @endphp
                                    <tr id="inf-teleconsult-row-{{ $t->id }}">
                                        <td class="ps-3 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold fs-12 flex-shrink-0" style="width: 32px; height: 32px; background: {{ $isEmergency ? '#fee2e2' : '#ccfbf1' }}; color: {{ $isEmergency ? '#b91c1c' : '#0f766e' }}; border: 1.5px solid {{ $isEmergency ? '#ef4444' : '#0d9488' }};">
                                                    {{ $isEmergency ? 'URG' : 'PT' }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-13 d-flex align-items-center gap-1">
                                                        <span>{{ $tPatName }}</span>
                                                        @if($isEmergency)
                                                            <span class="badge bg-danger text-white rounded-pill fs-10 px-1.5 py-0.5">Urgence</span>
                                                        @endif
                                                    </div>
                                                    <span class="badge bg-light text-secondary border fs-10">{{ optional($t->patient)->code_patient }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-2 fw-semibold text-dark fs-12">
                                            <i class="fa-solid fa-user-doctor me-1" style="color: #0d9488;"></i> {{ $tDocName }}
                                        </td>
                                        <td class="py-2 text-dark fs-12 fw-semibold">
                                            <i class="fa-regular fa-calendar-check me-1" style="color: #0d9488;"></i> {{ $t->desired_date ? date('d/m/Y', strtotime($t->desired_date)) : ($t->date_consultation ? date('d/m/Y', strtotime($t->date_consultation)) : 'Aujourd\'hui') }} à {{ $t->desired_time ?: ($t->created_at ? $t->created_at->format('H:i') : '') }}
                                        </td>
                                        <td class="py-2">
                                            <span class="badge bg-light text-dark border fs-11">
                                                {{ optional(optional($t->prestationHospital)->prestationService)->libelle ?? $t->motif_consultation ?? ($isEmergency ? 'Urgence médicale' : 'Téléconsultation') }}
                                            </span>
                                        </td>
                                        <td class="py-2 text-center">
                                            @if($t->is_call_active && in_array($t->call_status, ['calling', 'accepted', 'in_call']))
                                                @if($hasAssignedDoc || in_array($t->call_status, ['accepted', 'in_call']))
                                                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1 fs-10 fw-semibold">
                                                        <i class="fa-solid fa-circle-check me-1"></i> Médecin connecté
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fs-10 fw-semibold">
                                                        <span class="spinner-grow spinner-grow-sm me-1" style="width: 6px; height: 6px;"></span> Appel...
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 fs-10 fw-semibold" style="background: rgba(13,148,136,0.12); color: #0d9488;">
                                                    En attente médecin
                                                </span>
                                            @endif
                                        </td>
                                        <td class="pe-3 py-2 text-end">
                                            <button type="button" class="btn text-white btn-xs btn-sm rounded-pill px-3 py-1 fw-bold shadow-sm d-inline-flex align-items-center gap-1" onclick="openInfirmierVideoCall({{ $t->id }}, '{{ $token }}', '{{ $livekitUrl }}', '{{ addslashes($tDocName) }}', '{{ addslashes($tPatName) }}', '{{ $t->call_channel }}')" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;">
                                                <i class="fa-solid fa-video fs-12"></i>
                                                <span class="fs-12">Rejoindre</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            let lastKnownTeleconsultIds = @json($infTeleconsultations->pluck('id'));

            setInterval(async () => {
                try {
                    const res = await fetch('/infirmier/consultation/teleconsultation/active-list', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        const list = data.teleconsultations || [];
                        const currentIds = list.map(item => item.id);

                        const removedIds = lastKnownTeleconsultIds.filter(id => !currentIds.includes(id));
                        if (removedIds.length > 0) {
                            removedIds.forEach(id => {
                                const row = document.getElementById(`inf-teleconsult-row-${id}`);
                                if (row) {
                                    row.style.transition = 'all 0.4s ease';
                                    row.style.opacity = '0';
                                    row.style.transform = 'scale(0.95)';
                                    setTimeout(() => row.remove(), 400);
                                }
                            });
                        }

                        lastKnownTeleconsultIds = currentIds;

                        const badge = document.getElementById('infTeleconsultCountBadge');
                        if (badge) {
                            badge.innerText = `${list.length} en cours / programmée(s)`;
                        }

                        const section = document.getElementById('infirmierTeleconsultSection');
                        if (section) {
                            if (list.length === 0) {
                                setTimeout(() => {
                                    if (lastKnownTeleconsultIds.length === 0) section.style.display = 'none';
                                }, 500);
                            } else {
                                section.style.display = 'block';
                            }
                        }
                    }
                } catch(e) {}
            }, 3500);
        });
        </script>

        <!-- Deux colonnes : Historique Consultations (col-md-9) & Suivi Hospitalisation (col-md-3) -->
        <div class="col-12">
            <div class="row">
                <!-- Colonne Gauche : Liste des Consultations -->
                <div class="col-xl-9 col-lg-9 col-md-9 col-12">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-xs-12 col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                    <div class="badge badge-primary" style="font-size: 20px;">LISTE DE VOS CONSULTATIONS</div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th class="bb-2">Date et heure</th>
                                            <th class="bb-2">Reference</th>
                                            <th class="bb-2">Motif</th>
                                            <th class="bb-2">Statut</th>
                                            <th class="bb-2 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (\App\Models\Consultation::orderByDESC('created_at')->withCount('ordonnances', 'arret', 'examen')->where('infirmier_id', \Illuminate\Support\Facades\Auth::user()->infirmier->id)->where('status_inf', 1)->get() as $item)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($item->date_consultation)->format('d/m/Y') }} -
                                                    {{ $item->created_at->format('H:i:s') }}
                                                </td>
                                                <td><b>{{ $item->patient->code_patient }}</b></td>

                                                <td class="" style="width: 200px;">
                                                    {{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? ($item->prestationHospital->serviceHospital->service->libelle ?? 'Consultation') }}
                                                </td>

                                                <td class="">
                                                    @if ($item->status_inf == 0)
                                                        <span class="badge badge-warning">En attente</span>
                                                    @else
                                                        <span class="badge badge-success">Terminée</span> <br>
                                                        <div style="padding-top: 5px;">
                                                            @if ($item->ordonnances_count > 0)
                                                                @foreach ($item->ordonnances as $ordonnan)
                                                                    <a target="_blank"
                                                                        href="{{ route('impression', ['ordonnance', $ordonnan->id]) }}"><span
                                                                            class="badge badge-warning">Ordonnance
                                                                            {{ $ordonnan->type }}</span></a>
                                                                @endforeach
                                                            @endif
                                                            @if ($item->arret_count > 0)
                                                                <a style="padding-bottom: 5px;" target="_blank"
                                                                    href="{{ route('consultation.imprimer.post', ['arret', $item->arret->id]) }}"><span
                                                                        class="badge badge-primary">Arrêt de travail</span></a>
                                                            @endif
                                                            @if ($item->examen_count > 0)
                                                                <a target="_blank"
                                                                    href="{{ route('consultation.imprimer.post', ['examen', $item->examen->id]) }}"><span
                                                                        class="badge badge-secondary">Bulletin d'examen</span></a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->status == 0)
                                                        <a href="{{ route('doctor.consultation.info', $item->id) }}"
                                                            class="btn btn-sm btn-info" title="info">
                                                            <span class="">Info patient</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('infirmier.consultation.detail', $item->id) }}"
                                                            class="btn btn-sm btn-info" title="detail consultation">
                                                            <span class="">Détail</span>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne Droite : Suivi Hospitalisation -->
                <div class="col-xl-3 col-lg-3 col-md-3 col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title"><i class="fa-solid fa-bed-pulse"></i> Patient(s) Hospitalisé(s)</h4>
                            <h4 class="mb-0 pull-right box-title"><i class="fa-solid fa-user-nurse"></i> Suivi</h4>
                        </div>
                        <div class="box-body">
                            <div class="hospitalises-scroll-container" style="max-height: 420px; overflow-y: auto; -webkit-overflow-scrolling: touch;">
                                @php
                                    $hospList = \App\Models\DayHospitalisation::orderByDESC('created_at')->where('infirmier_id', \Illuminate\Support\Facades\Auth::user()->infirmier->id)->where('status', 'en_cours')->whereHas('hospitalisation', function($q) { $q->where('status', 'in_progress'); })->get();
                                @endphp
                                @forelse ($hospList as $item)
                                    <div class="d-flex align-items-center mb-10 pb-10 border-bottom">
                                        <div class="me-15 flex-shrink-0">
                                            @if ($item->hospitalisation->consultation->patient->img_url != null)
                                                <img src="{{ asset('assets/uploads/patient/'. $item->hospitalisation->consultation->patient->img_url) }}"
                                                    class="avatar avatar-lg rounded10 bg-primary-light" alt="Photo de profil" style="width:48px; height:48px; object-fit: cover;" />
                                            @else
                                                @if ($item->hospitalisation->consultation->patient->gender == 'masculin')
                                                    <img src="{{ asset('assets/images/avatar/6.png') }}" class="avatar avatar-lg rounded10 bg-primary-light"
                                                        alt="Photo de profil" style="width:48px; height:48px; object-fit: cover;" />
                                                @else
                                                    <img src="{{ asset('assets/images/avatar/2.png') }}" class="avatar avatar-lg rounded10 bg-primary-light"
                                                        alt="Photo de profil" style="width:48px; height:48px; object-fit: cover;" />
                                                @endif
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column flex-grow-1 fw-500">
                                            <a href="#" class="text-dark hover-primary mb-1 fs-14 fw-bold text-truncate" style="max-width: 140px;">
                                                {{ $item->hospitalisation->consultation->patient->user->name }} {{ $item->hospitalisation->consultation->patient->user->prenom }}
                                            </a>
                                            <span class="text-fade fs-12">{{ $item->doctor->user->name }}</span>
                                            <div class="d-flex align-items-center gap-2 mt-1 fs-11 text-muted">
                                                <span><i class="fa fa-clock-o me-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}</span>
                                                <span class="badge badge-info-light">{{ $item->hospitalisation->type }}</span>
                                            </div>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            <a href="{{ route('infirmier.suivi.hospitalisation',['id' => $item->hospitalisation->id]) }}" class="waves-effect waves-circle btn btn-circle btn-primary-light btn-sm" title="Suivi"><i class="fa-solid fa-stethoscope"></i></a>
                                            <br/>
                                            <span class="badge badge-warning mt-5">{{ $item->status }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted fs-12">
                                        <i class="fa-solid fa-bed text-muted fs-24 d-block mb-2"></i>
                                        Aucun patient hospitalisé en cours.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
