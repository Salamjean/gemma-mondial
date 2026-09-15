@php
    // S'assurer que les consultations du patient sont chargées avec toutes les relations nécessaires
    $patientInterventions = \App\Models\Consultation::where('patient_id', $patient->id)
        ->orderByDESC('created_at')
        ->with([
            'doctor.user',
            'infirmier.user',
            'admission.infirmier.user',
            'admission.doctor.user',
            'admission.cashier.user',
            'admission.secretariat.user',
            'prestationHospital.prestationService.service',
            'registre.registreConsultationCurative',
            'ordonnances.prescriptions.drug',
            'ordonnances.prescriptions.drugHospital.drug',
            'examen',
            'arret',
            'declaration',
            'hospitalisation'
        ])
        ->get();
@endphp

<div class="box border-0 shadow-sm rounded-16 bg-white mb-30">
    <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="box-title mb-0 fw-bold text-dark fs-18">
                <i class="fa-solid fa-timeline text-primary me-2"></i><b>HISTORIQUE DES INTERVENTIONS ET PARCOURS DE SOINS</b>
            </h4>
            <p class="text-muted mb-0 fs-13 mt-1">
                Consultez le parcours complet de chaque intervention (médecin, infirmier, constantes, prescriptions, issue).
            </p>
        </div>
        <div>
            <span class="badge bg-primary-light text-primary fw-bold px-3 py-2 fs-13 rounded-8">
                <i class="fa-solid fa-list-check me-1"></i> {{ count($patientInterventions) }} Intervention(s)
            </span>
        </div>
    </div>

    <div class="box-body p-20">
        @if(count($patientInterventions) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle display nowrap margin-top-10 w-p100 border">
                    <thead class="bg-light">
                        <tr>
                            <th class="fw-bold text-dark"># Date & Heure</th>
                            <th class="fw-bold text-dark">Service / Prestation</th>
                            <th class="fw-bold text-dark">Motif</th>
                            <th class="fw-bold text-dark">Personnels Intervenants</th>
                            <th class="fw-bold text-dark text-center">Issue / Statut</th>
                            <th class="fw-bold text-dark text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patientInterventions as $index => $item)
                            @php
                                $dateStr = \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') . ' à ' . \Carbon\Carbon::parse($item->created_at)->format('H:i');
                                $serviceName = optional(optional($item->prestationHospital)->prestationService)->libelle 
                                    ?? optional(optional(optional(optional($item->prestationHospital)->prestationService)->service))->libelle 
                                    ?? 'Consultation générale';
                                
                                $doctorName = trim(optional(optional($item->doctor)->user)->name . ' ' . optional(optional($item->doctor)->user)->prenom) 
                                    ?: trim(optional(optional(optional($item->admission)->doctor)->user)->name . ' ' . optional(optional(optional($item->admission)->doctor)->user)->prenom);
                                
                                $infirmierName = trim(optional(optional($item->infirmier)->user)->name . ' ' . optional(optional($item->infirmier)->user)->prenom)
                                    ?: trim(optional(optional(optional($item->admission)->infirmier)->user)->name . ' ' . optional(optional(optional($item->admission)->infirmier)->user)->prenom);

                                $caissiereName = trim(optional(optional(optional($item->admission)->cashier)->user)->name . ' ' . optional(optional(optional($item->admission)->cashier)->user)->prenom)
                                    ?: trim(optional(optional(optional($item->admission)->secretariat)->user)->name . ' ' . optional(optional(optional($item->admission)->secretariat)->user)->prenom);

                                $issueStr = optional($item->registre)->issue_consultation ?? 'En cours';
                                $hasJustif = !empty(optional($item->registre)->issue_consultation_justification);
                                $hasOrdo = ($item->ordonnances_count > 0 || count($item->ordonnances ?? []) > 0);
                                $isDone = ($item->status == 1) && ($hasJustif || $hasOrdo || $item->examen_count > 0 || $item->arret_count > 0 || $item->hospitalisation);
                            @endphp
                            @php
                                $parcoursUrl = '#';
                                if (request()->routeIs('secretariat.*') && \Illuminate\Support\Facades\Route::has('secretariat.patient.parcours')) {
                                    $parcoursUrl = route('secretariat.patient.parcours', $item->id);
                                } elseif (request()->routeIs('hospital.*') && \Illuminate\Support\Facades\Route::has('hospital.patient.parcours')) {
                                    $parcoursUrl = route('hospital.patient.parcours', $item->id);
                                } elseif (\Illuminate\Support\Facades\Route::has('doctor.patient.parcours')) {
                                    $parcoursUrl = route('doctor.patient.parcours', $item->id);
                                } else {
                                    $parcoursUrl = url('parcours/' . $item->id);
                                }
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                            <i class="fa-solid fa-stethoscope fs-14"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block fs-13">{{ $dateStr }}</span>
                                            <span class="text-muted fs-11">N° {{ $item->code_consultation ?? ('CONS-' . $item->id) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary fs-13">{{ $serviceName }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary fs-13 d-inline-block text-truncate" style="max-width: 200px;" title="{{ $item->motif_consultation ?? optional($item->admission)->motif_consultation ?? 'Non renseigné' }}">
                                        {{ $item->motif_consultation ?? optional($item->admission)->motif_consultation ?? 'Non renseigné' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($doctorName)
                                            <span class="badge bg-soft-info text-info border border-info-subtle fw-semibold text-start fs-12">
                                                <i class="fa-solid fa-user-md me-1"></i> Médecin : {{ $doctorName }}
                                            </span>
                                        @endif
                                        @if($infirmierName)
                                            <span class="badge bg-soft-success text-success border border-success-subtle fw-semibold text-start fs-12">
                                                <i class="fa-solid fa-user-nurse me-1"></i> Infirmier(ère) : {{ $infirmierName }}
                                            </span>
                                        @endif
                                        @if($caissiereName)
                                            <span class="badge bg-soft-secondary text-secondary border border-secondary-subtle fw-semibold text-start fs-12">
                                                <i class="fa-solid fa-user me-1"></i> Accueil/Caisse : {{ $caissiereName }}
                                            </span>
                                        @endif
                                        @if(!$doctorName && !$infirmierName && !$caissiereName)
                                            <span class="text-muted fs-12">Non spécifié</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        @if($isDone)
                                            <span class="badge bg-success-light text-success fw-bold px-2 py-1 fs-12">
                                                <i class="fa-solid fa-check-circle me-1"></i> Terminée
                                            </span>
                                        @else
                                            <span class="badge bg-warning-light text-warning fw-bold px-2 py-1 fs-12">
                                                <i class="fa-solid fa-clock me-1"></i> Non terminée
                                            </span>
                                        @endif

                                        @if($issueStr != 'En cours')
                                            <span class="badge bg-primary text-white fw-semibold fs-11 text-uppercase">
                                                {{ $issueStr }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $parcoursUrl }}" class="btn btn-sm btn-primary rounded-8 px-3 fw-bold shadow-sm">
                                        <i class="fa-solid fa-eye me-1"></i> Voir le parcours
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-40">
                <i class="fa-solid fa-folder-open text-muted fs-40 mb-3 d-block"></i>
                <h5 class="fw-bold text-muted mb-1">Aucune intervention enregistrée</h5>
                <p class="text-muted fs-13 mb-0">Ce patient n'a pas encore de parcours médical dans le système.</p>
            </div>
        @endif
    </div>
</div>
