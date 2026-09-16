@php
    // S'assurer que les consultations du patient sont chargées avec toutes les relations nécessaires de TOUS LES HÔPITAUX
    $patientInterventions = \App\Models\Consultation::where('patient_id', $patient->id)
        ->orderByDESC('created_at')
        ->with([
            'hospital.user',
            'doctor.user',
            'infirmier.user',
            'admission.infirmier.user',
            'admission.doctor.user',
            'admission.cashier.user',
            'admission.secretariat.user',
            'prestationHospital.prestationService.service',
            'registre.registreConsultationCurative',
            'registre.registreAccouchement',
            'registre.registreConsultationPreNatale',
            'registre.registreConsultationPostNatale',
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
                <i class="fa-solid fa-timeline text-primary me-2"></i><b>HISTORIQUE ET DOSSIER MÉDICAL COMPLET DU PATIENT</b>
            </h4>
            <p class="text-muted mb-0 fs-13 mt-1">
                Toutes les constatations, prises de constantes, diagnostics et soins reçus par le patient dans <b>tous les établissements hôpitaux</b>.
            </p>
        </div>
        <div>
            <span class="badge bg-primary text-white fw-bold px-3 py-2 fs-13 rounded-8 shadow-xs">
                <i class="fa-solid fa-hospital-user me-1"></i> {{ count($patientInterventions) }} Consultation(s) enregistrée(s)
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
                            <th class="fw-bold text-dark">Hôpital / Établissement</th>
                            <th class="fw-bold text-dark">Service / Prestation</th>
                            <th class="fw-bold text-dark">Motif / Diagnostic</th>
                            <th class="fw-bold text-dark">Personnels Intervenants</th>
                            <th class="fw-bold text-dark text-center">Issue / Statut</th>
                            <th class="fw-bold text-dark text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patientInterventions as $index => $item)
                            @php
                                $dateStr = \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') . ' à ' . \Carbon\Carbon::parse($item->created_at)->format('H:i');
                                $hospitalName = optional($item->hospital)->label 
                                    ?: (optional(optional($item->hospital)->user)->name 
                                    ?: (optional($item->hospital)->name 
                                    ?: 'Hôpital Général'));
                                
                                $serviceName = optional(optional($item->prestationHospital)->prestationService)->libelle 
                                    ?? optional(optional(optional(optional($item->prestationHospital)->prestationService)->service))->libelle 
                                    ?? 'Consultation générale';
                                
                                $doctorName = trim(optional(optional($item->doctor)->user)->name . ' ' . optional(optional($item->doctor)->user)->prenom) 
                                    ?: trim(optional(optional(optional($item->admission)->doctor)->user)->name . ' ' . optional(optional(optional($item->admission)->doctor)->user)->prenom);
                                
                                $infirmierName = trim(optional(optional($item->infirmier)->user)->name . ' ' . optional(optional($item->infirmier)->user)->prenom)
                                    ?: trim(optional(optional(optional($item->admission)->infirmier)->user)->name . ' ' . optional(optional(optional($item->admission)->infirmier)->user)->prenom);

                                $caissiereName = trim(optional(optional(optional($item->admission)->cashier)->user)->name . ' ' . optional(optional(optional($item->admission)->cashier)->user)->prenom)
                                    ?: trim(optional(optional(optional($item->admission)->secretariat)->user)->name . ' ' . optional(optional(optional($item->admission)->secretariat)->user)->prenom);

                                $reg = $item->registre;
                                $regCur = optional($reg)->registreConsultationCurative;

                                $motifStr = $item->motif_consultation ?? optional($item->admission)->motif_consultation ?? (optional($regCur)->motif_consultation ?? 'Non renseigné');
                                $diagStr = optional($regCur)->diagnostic_retenu ?? optional($item->hospitalisation)->diagnostic ?? null;

                                $issueStr = optional($reg)->issue_consultation ?? 'En cours';
                                $hasJustif = !empty(optional($reg)->issue_consultation_justification);
                                $hasOrdo = ($item->ordonnances_count > 0 || count($item->ordonnances ?? []) > 0);
                                $isDone = ($item->status == 1 || $item->status == '1') 
                                    || in_array($item->call_status, ['completed', 'ended', 'complete'])
                                    || (!empty(optional($reg)->issue_consultation) && optional($reg)->issue_consultation !== 'En cours')
                                    || $hasJustif 
                                    || $hasOrdo 
                                    || ($item->examen_count > 0 || !empty($item->examen)) 
                                    || ($item->arret_count > 0 || !empty($item->arret)) 
                                    || !empty($item->hospitalisation)
                                    || ($item->declaration_count > 0 || !empty($item->declaration));

                                // Constantes
                                $valPoids = $item->poids ?: (optional($regCur)->poids ?? 'N/A');
                                $valTemp = $item->temperature ?: (optional($regCur)->temperature ?? 'N/A');
                                $valTA = $item->tension_arterielle ?: (optional($regCur)->ta ?? 'N/A');
                                $valPouls = $item->pouls ?: (optional($regCur)->pouls ?? 'N/A');
                                $valSat = $item->saturation_oxygene ?: (optional($regCur)->saturation_oxygene ?? 'N/A');
                                $valTaille = $item->taille ?: (optional($regCur)->taille ?? 'N/A');
                                $valImc = $item->imc ?: (optional($regCur)->imc ?? 'N/A');
                                $valGlyA = $item->gly_a_jeun ?: (optional($regCur)->glycemie_a_jeun ?? 'N/A');
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

                            <!-- Ligne principale -->
                            <tr class="align-middle">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:38px; height:38px;">
                                            <i class="fa-solid fa-stethoscope fs-15"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block fs-13">{{ $dateStr }}</span>
                                            <span class="text-muted fs-11">N° {{ $item->code_consultation ?? ('CONS-' . $item->id) }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 fs-12 fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                                        <i class="fa-solid fa-hospital text-danger fs-13"></i> {{ $hospitalName }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary fs-13">{{ $serviceName }}</span>
                                    @if(!empty($item->call_channel))
                                        <br><span class="badge bg-teal-subtle text-teal-800 fs-10 px-2 py-0.5 rounded-pill mt-1" style="background: rgba(13, 148, 136, 0.12); color: #0f766e;"><i class="fa-solid fa-video me-1"></i> En ligne</span>
                                    @else
                                        <br><span class="badge bg-secondary-subtle text-secondary fs-10 px-2 py-0.5 rounded-pill mt-1"><i class="fa-solid fa-user-check me-1"></i> Présentiel</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <span class="text-dark fw-semibold fs-13 d-block text-truncate" style="max-width: 220px;" title="{{ $motifStr }}">
                                            <i class="fa-solid fa-notes-medical text-muted me-1"></i> {{ $motifStr }}
                                        </span>
                                        @if($diagStr)
                                            <span class="text-success fw-bold fs-12 d-block text-truncate" style="max-width: 220px;" title="{{ $diagStr }}">
                                                <i class="fa-solid fa-microscope me-1"></i> {{ $diagStr }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($doctorName)
                                            <span class="badge bg-soft-info text-info border border-info-subtle fw-semibold text-start fs-12">
                                                <i class="fa-solid fa-user-md me-1"></i> Dr. {{ $doctorName }}
                                            </span>
                                        @endif
                                        @if($infirmierName)
                                            <span class="badge bg-soft-success text-success border border-success-subtle fw-semibold text-start fs-12">
                                                <i class="fa-solid fa-user-nurse me-1"></i> Inf. {{ $infirmierName }}
                                            </span>
                                        @endif
                                        @if(!$doctorName && !$infirmierName)
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
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-info text-white rounded-8 px-2.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1" data-bs-toggle="collapse" data-bs-target="#details-row-{{ $item->id }}" aria-expanded="false" aria-controls="details-row-{{ $item->id }}" title="Voir toutes les constatations et détails">
                                            <i class="fa-solid fa-eye fs-12"></i>
                                            <span>Voir les détails</span>
                                        </button>
                                        <a href="{{ $parcoursUrl }}" class="btn btn-sm btn-outline-primary rounded-8 px-2.5 fw-bold shadow-xs d-inline-flex align-items-center gap-1" title="Voir la fiche parcours dédiée">
                                            <i class="fa-solid fa-arrow-up-right-from-square fs-12"></i>
                                            <span>Parcours</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Drawer Collapsible : Détails complets de la consultation -->
                            <tr class="collapse bg-light-subtle" id="details-row-{{ $item->id }}">
                                <td colspan="7" class="p-0 border-top border-bottom" style="background-color: #f8fafc;">
                                    <div class="p-20 m-2 bg-white rounded-14 border shadow-sm">
                                        
                                        <!-- En-tête des détails -->
                                        <div class="d-flex align-items-center justify-content-between pb-15 mb-15 border-bottom flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary text-white fs-13 px-3 py-1.5 fw-bold rounded-8">
                                                    <i class="fa-solid fa-hospital me-1"></i> {{ $hospitalName }}
                                                </span>
                                                <h6 class="fw-bold text-dark mb-0 fs-15">
                                                    Constatations & Prises de soins du {{ $dateStr }} (Réf: {{ $item->code_consultation ?? ('CONS-' . $item->id) }})
                                                </h6>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="{{ $parcoursUrl }}" class="btn btn-sm btn-outline-info rounded-8 px-3 fw-bold">
                                                    <i class="fa-solid fa-file-medical me-1"></i> Ouvrir la fiche complète
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Grille des Constatations & Constantes -->
                                        <div class="row g-3 mb-20">
                                            <!-- 1. PRISES DE CONSTANTES -->
                                            <div class="col-12">
                                                <h6 class="fw-bold text-dark fs-13 mb-2 text-uppercase text-muted">
                                                    <i class="fa-solid fa-heart-pulse text-danger me-1"></i> Prise de Constantes Physiques
                                                </h6>
                                                <div class="row g-2">
                                                    <div class="col-6 col-sm-3 col-md-2">
                                                        <div class="p-2 text-center bg-light rounded-10 border">
                                                            <span class="text-muted fs-11 fw-semibold d-block">Tension Artérielle</span>
                                                            <span class="fs-13 fw-bold text-dark">{{ $valTA }} {{ $valTA != 'N/A' ? 'mmHg' : '' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-sm-3 col-md-2">
                                                        <div class="p-2 text-center bg-light rounded-10 border">
                                                            <span class="text-muted fs-11 fw-semibold d-block">Température</span>
                                                            <span class="fs-13 fw-bold text-dark">{{ $valTemp }} {{ $valTemp != 'N/A' ? '°C' : '' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-sm-3 col-md-2">
                                                        <div class="p-2 text-center bg-light rounded-10 border">
                                                            <span class="text-muted fs-11 fw-semibold d-block">Poids</span>
                                                            <span class="fs-13 fw-bold text-dark">{{ $valPoids }} {{ $valPoids != 'N/A' ? 'kg' : '' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-sm-3 col-md-2">
                                                        <div class="p-2 text-center bg-light rounded-10 border">
                                                            <span class="text-muted fs-11 fw-semibold d-block">Pouls</span>
                                                            <span class="fs-13 fw-bold text-dark">{{ $valPouls }} {{ $valPouls != 'N/A' ? 'bpm' : '' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-sm-3 col-md-2">
                                                        <div class="p-2 text-center bg-light rounded-10 border">
                                                            <span class="text-muted fs-11 fw-semibold d-block">Saturation O₂</span>
                                                            <span class="fs-13 fw-bold text-dark">{{ $valSat }} {{ $valSat != 'N/A' ? '%' : '' }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-sm-3 col-md-2">
                                                        <div class="p-2 text-center bg-light rounded-10 border">
                                                            <span class="text-muted fs-11 fw-semibold d-block">Glycémie a jeûn</span>
                                                            <span class="fs-13 fw-bold text-dark">{{ $valGlyA }} {{ $valGlyA != 'N/A' ? 'g/l' : '' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. DIAGNOSTIC & EXAMEN -->
                                            <div class="col-md-6">
                                                <div class="p-15 bg-light rounded-12 border h-100">
                                                    <h6 class="fw-bold text-dark fs-13 mb-2 text-uppercase text-muted">
                                                        <i class="fa-solid fa-stethoscope text-info me-1"></i> Bilan Médical & Diagnostic
                                                    </h6>
                                                    <div class="mb-2">
                                                        <span class="fw-semibold text-dark fs-12">Motif :</span>
                                                        <span class="text-secondary fs-12">{{ $motifStr }}</span>
                                                    </div>
                                                    @if(optional($regCur)->examen_physique)
                                                        <div class="mb-2">
                                                            <span class="fw-semibold text-dark fs-12">Examen physique :</span>
                                                            <span class="text-secondary fs-12">{{ $regCur->examen_physique }}</span>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <span class="fw-semibold text-dark fs-12">Diagnostic retenu :</span>
                                                        <span class="badge bg-primary-light text-primary fw-bold fs-12 px-2 py-1 ms-1 border border-primary-subtle">
                                                            {{ $diagStr ?? 'Aucun diagnostic spécifique enregistré' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 3. ORDONNANCES & PRESCRIPTIONS -->
                                            <div class="col-md-6">
                                                <div class="p-15 bg-light rounded-12 border h-100">
                                                    <h6 class="fw-bold text-dark fs-13 mb-2 text-uppercase text-muted">
                                                        <i class="fa-solid fa-prescription-bottle-medical text-warning me-1"></i> Prescriptions Médicamenteuses
                                                    </h6>
                                                    @if(count($item->ordonnances ?? []) > 0)
                                                        @foreach($item->ordonnances as $ord)
                                                            <div class="mb-2">
                                                                <span class="badge bg-warning text-dark fs-11 mb-1">Ordonnance {{ ucfirst($ord->type) }}</span>
                                                                <ul class="list-unstyled mb-0 ps-2">
                                                                    @forelse($ord->prescriptions ?? [] as $p)
                                                                        @php
                                                                            $drugName = optional($p->drug)->name ?? optional(optional($p->drugHospital)->drug)->name ?? 'Médicament';
                                                                        @endphp
                                                                        <li class="fs-12 text-dark fw-medium">
                                                                            <i class="fa-solid fa-pills text-primary me-1"></i> <b>{{ $drugName }}</b> - Posologie: {{ $p->dosage ?? 'Selon ordonnance' }} (Qté: {{ $p->quantity }})
                                                                        </li>
                                                                    @empty
                                                                        <li class="fs-12 text-muted">Aucun médicament listé.</li>
                                                                    @endforelse
                                                                </ul>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted fs-12 italic">Aucune ordonnance émise lors de cette intervention.</span>
                                                    @endif

                                                    <div class="mt-3 pt-2 border-top d-flex flex-wrap gap-2">
                                                        @if($item->examen)
                                                            <span class="badge bg-info text-white fs-11 px-2 py-1"><i class="fa-solid fa-x-ray me-1"></i> Bulletin d'examen demandé</span>
                                                        @endif
                                                        @if($item->arret)
                                                            <span class="badge bg-secondary text-white fs-11 px-2 py-1"><i class="fa-solid fa-file-contract me-1"></i> Certificat d'arrêt délivré</span>
                                                        @endif
                                                        @if($item->hospitalisation)
                                                            <span class="badge bg-danger text-white fs-11 px-2 py-1"><i class="fa-solid fa-bed-pulse me-1"></i> Hospitalisation requise</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 4. REMARQUES & ANTÉCÉDENTS REGISTRE -->
                                            @if($regCur)
                                                <div class="col-12">
                                                    <div class="p-12 bg-light rounded-10 border">
                                                        <span class="fw-bold text-dark fs-12 d-block mb-1"><i class="fa-solid fa-book-medical text-primary me-1"></i> Synthèse & Antécédents du Registre :</span>
                                                        <div class="d-flex flex-wrap gap-2 fs-11">
                                                            <span class="badge {{ str_contains($regCur->hta ?? '', 'Oui') ? 'bg-danger' : 'bg-secondary' }}">HTA: {{ $regCur->hta ?? 'Non' }}</span>
                                                            <span class="badge {{ str_contains($regCur->diabete ?? '', 'Oui') ? 'bg-danger' : 'bg-secondary' }}">Diabète: {{ $regCur->diabete ?? 'Non' }}</span>
                                                            <span class="badge {{ ($regCur->alcool ?? '') == 'Oui' ? 'bg-warning text-dark' : 'bg-secondary' }}">Alcool: {{ $regCur->alcool ?? 'Non' }}</span>
                                                            <span class="badge {{ ($regCur->tabac ?? '') == 'Oui' ? 'bg-warning text-dark' : 'bg-secondary' }}">Tabac: {{ $regCur->tabac ?? 'Non' }}</span>
                                                            <span class="badge {{ ($regCur->drepanocytaire ?? '') == 'Oui' ? 'bg-danger' : 'bg-secondary' }}">Drépanocytose: {{ $regCur->drepanocytaire ?? 'Non' }}</span>
                                                            @if(!empty($regCur->traitement_medicamenteux_anterieur))
                                                                <span class="text-muted ms-2">Traitements antérieurs : {{ $regCur->traitement_medicamenteux_anterieur }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
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
                <p class="text-muted fs-13 mb-0">Ce patient n'a encore aucun dossier ou constatation enregistrée dans le système.</p>
            </div>
        @endif
    </div>
</div>
