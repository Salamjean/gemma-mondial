@extends('layouts.dashboard', ['title' => 'Détail de la consultation'])

@section('content')
@php
    $patient = $consultation->patient ?? optional($consultation->admission)->patient;
    $patientUser = optional($patient)->user;
    $doctor = $consultation->doctor;
    $doctorUser = optional($doctor)->user;
    $infirmier = $consultation->infirmier;
    $infirmierUser = optional($infirmier)->user;
    
    $hospital = $consultation->hospital 
        ?? optional($doctor)->hospital 
        ?? optional(Auth::user())->hospital;
    $hospitalName = optional($hospital)->name ?? 'Établissement Hospitalier';
    
    $bulletin = $consultation->bulletinExamen ?? $consultation->examen;
    $registre = $consultation->registre;
    $arret = $consultation->arret ?? \App\Models\ArretTravail::where('consultation_id', $consultation->id)->first();

    $patientFullName = trim((optional($patientUser)->name ?? '') . ' ' . (optional($patientUser)->prenom ?? '')) ?: 'Patient';
    $patientCode = optional($patient)->code_patient ?? ('#' . $consultation->id);
    
    $practitionerName = '';
    if ($doctorUser && !empty(trim($doctorUser->name ?? ''))) {
        $practitionerName = 'Dr. ' . trim(($doctorUser->name ?? '') . ' ' . ($doctorUser->prenom ?? ''));
    } elseif ($infirmierUser && !empty(trim($infirmierUser->name ?? ''))) {
        $practitionerName = trim(($infirmierUser->name ?? '') . ' ' . ($infirmierUser->prenom ?? '')) . ' (Infirmier)';
    } else {
        $practitionerName = 'Praticien référent';
    }

    $prestationService = optional($consultation->prestationHospital)->prestationService 
        ?? optional(optional($consultation->admission)->prestationHospital)->prestationService;
    $typeVisite = optional($prestationService)->libelle 
        ?? optional(optional(optional($doctor)->serviceHospital)->service)->libelle 
        ?? 'Consultation Médicale';

    $issue = optional($registre)->issue_consultation ?? ($consultation->status == 1 ? 'sortie' : 'En attente');
    $justif = optional($registre)->issue_consultation_justification 
        ?: ($consultation->observation_infirmiere ?: ($consultation->observation_soins ?: 'Aucune consigne particulière enregistrée.'));

    // Calcul de l'âge
    $patientAge = '';
    if (!empty(optional($patient)->birth_date)) {
        try {
            $bDate = \Carbon\Carbon::parse($patient->birth_date);
            $patientAge = $bDate->age . ' ans';
        } catch (\Throwable $e) {
            $patientAge = $patient->birth_date;
        }
    }

    $residenceActuelle = optional(optional($patient)->residenceActuelle)->name 
        ?? optional(optional($patient)->currentResidence)->name 
        ?? (optional($patient)->address ?: (optional($patient)->adresse ?: 'Non renseignée'));

    $residenceHabituelle = optional(optional($patient)->habitualResidence)->name;
    
    $lieuNaissance = optional(optional($patient)->birthPlace)->name 
        ?: (optional($patient)->lieu_naissance ?: 'Non renseigné');

    $hasVitals = !empty($consultation->tension_arterielle) || !empty($consultation->temperature) 
        || !empty($consultation->poids) || !empty($consultation->pouls) 
        || !empty($consultation->saturation_oxygene) || !empty($consultation->taille)
        || !empty(optional($patient)->temperature) || !empty(optional($patient)->poids);

    $tempVal = $consultation->temperature ?: optional($patient)->temperature;
    $poidsVal = $consultation->poids ?: optional($patient)->poids;
    $tailleVal = $consultation->taille ?: optional($patient)->taille;
@endphp

<!-- EN-TETE DE PAGE -->
<div class="content-header mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="page-title text-dark fw-bold mb-0">
                    Consultation : <span class="text-primary">{{ $consultation->code_consultation ?? ('#' . $consultation->id) }}</span>
                </h4>
                @if($consultation->status == 1)
                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold fs-12 px-2 py-1">
                        <i class="fa-solid fa-check-circle me-1"></i> Clôturée / Effectuée
                    </span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fw-bold fs-12 px-2 py-1">
                        <i class="fa-solid fa-clock me-1"></i> En attente
                    </span>
                @endif

                @if(!empty($consultation->is_urgence) && $consultation->is_urgence == 1)
                    <span class="badge bg-danger text-white fw-bold fs-12 px-2 py-1">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Urgence
                    </span>
                @endif
            </div>
            <div class="text-muted fs-13 mt-1">
                Dossier Patient : <strong>{{ $patientCode }}</strong> &bull; <strong>{{ $patientFullName }}</strong>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour aux consultations
            </a>
            @if(optional($patient)->id)
                <a href="{{ route('doctor.consultation.patient.card', $patient->id) }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                    <i class="fa-solid fa-folder-open me-1"></i> Dossier Médical
                </a>
            @endif
            @if($bulletin)
                <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-primary btn-sm px-3 fw-bold">
                    <i class="fa-solid fa-file-pdf me-1"></i> Fiche Laboratoire PDF
                </a>
            @endif
        </div>
    </div>
</div>

<!-- CONTENU PRINCIPAL -->
<section class="content px-0">
    <div class="row g-3">
        
        <!-- COLONNE GAUCHE : PROFIL ET INFORMATIONS DU PATIENT -->
        <div class="col-lg-4 col-md-5 col-12">
            
            <!-- CARTE IDENTITÉ PATIENT -->
            <div class="box shadow-none border mb-3">
                <div class="box-body p-4 text-center border-bottom bg-light-subtle">
                    @if(!empty(optional($patient)->img_url) && file_exists(public_path('assets/uploads/patient/' . $patient->img_url)))
                        <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}" class="rounded-circle mx-auto mb-3 border" style="width: 80px; height: 80px; object-fit: cover;" alt="{{ $patientFullName }}">
                    @else
                        <div class="avatar avatar-xxl bg-primary-subtle text-primary rounded-circle mx-auto mb-3 fw-bold fs-24 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            {{ strtoupper(substr($patientFullName, 0, 2)) }}
                        </div>
                    @endif
                    <h5 class="fw-bold text-dark mb-1">{{ $patientFullName }}</h5>
                    <div class="badge bg-light text-primary border font-monospace fs-12 mb-2">{{ $patientCode }}</div>
                    <div>
                        <span class="badge bg-secondary-subtle text-secondary fs-11 text-capitalize">
                            {{ optional($patient)->gender ?? 'Genre non précisé' }}
                        </span>
                        @if($patientAge)
                            <span class="badge bg-secondary-subtle text-secondary fs-11 ms-1">
                                {{ $patientAge }}
                            </span>
                        @endif
                        @if(!empty(optional($patient)->group_sanguin))
                            <span class="badge bg-danger-subtle text-danger fs-11 ms-1 fw-bold">
                                {{ $patient->group_sanguin }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="box-body p-3 fs-13">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="fa-solid fa-briefcase text-secondary me-1"></i> Profession :</span>
                        <strong class="text-dark">{{ optional($patient)->profession ?: 'Non renseigné' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="fa-solid fa-phone text-secondary me-1"></i> Téléphone :</span>
                        <strong class="text-dark">{{ optional($patient)->telephone ?: (optional($patientUser)->contact ?? (optional($patient)->contact2 ?: 'Non renseigné')) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="fa-solid fa-envelope text-secondary me-1"></i> Email :</span>
                        <strong class="text-dark text-break">{{ optional($patientUser)->email ?: 'Non renseigné' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="fa-solid fa-cake-candles text-secondary me-1"></i> Date Naissance :</span>
                        <strong class="text-dark">
                            @if(!empty(optional($patient)->birth_date))
                                {{ date('d/m/Y', strtotime($patient->birth_date)) }}
                            @else
                                Non renseigné
                            @endif
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="fa-solid fa-map-pin text-secondary me-1"></i> Lieu Naissance :</span>
                        <strong class="text-dark">{{ $lieuNaissance }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="fa-solid fa-house text-secondary me-1"></i> Résidence Actuelle :</span>
                        <strong class="text-dark text-end">{{ $residenceActuelle }}</strong>
                    </div>
                    @if($residenceHabituelle && $residenceHabituelle !== $residenceActuelle)
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="fa-solid fa-location-dot text-secondary me-1"></i> Résidence Habituelle :</span>
                            <strong class="text-dark text-end">{{ $residenceHabituelle }}</strong>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted"><i class="fa-solid fa-id-card text-secondary me-1"></i> Pièce d'identité :</span>
                        <strong class="text-dark text-end">
                            {{ optional($patient)->type_piece ?: 'CNI' }} {{ optional($patient)->numero_identite ? '(' . $patient->numero_identite . ')' : '' }}
                        </strong>
                    </div>
                    @if(!empty(optional($patient)->num_cmu) || !empty(optional($patient)->no_assurance))
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted"><i class="fa-solid fa-shield-halved text-secondary me-1"></i> Assurance / CMU :</span>
                            <strong class="text-dark text-end">
                                {{ optional($patient)->num_cmu ?: optional($patient)->no_assurance }}
                            </strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- CARTE ETABLISSEMENT DE SOINS -->
            <div class="box shadow-none border mb-3">
                <div class="box-header bg-light py-2 px-3">
                    <h6 class="box-title fs-13 fw-bold text-dark mb-0">
                        <i class="fa-solid fa-hospital text-primary me-2"></i> Établissement &amp; Praticien
                    </h6>
                </div>
                <div class="box-body p-3 fs-13">
                    <div class="fw-bold text-primary mb-1">{{ $hospitalName }}</div>
                    <div class="text-muted fs-12 mb-2">{{ optional($hospital)->adresse ?: 'Structure sanitaire conventionnée' }}</div>
                    <div class="d-flex justify-content-between py-1 border-top">
                        <span class="text-muted">Praticien :</span>
                        <strong class="text-dark">{{ $practitionerName }}</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-top">
                        <span class="text-muted">Service :</span>
                        <strong class="text-dark">{{ $typeVisite }}</strong>
                    </div>
                </div>
            </div>

            <!-- CARTE ANTECEDENTS & ALLERGIES SI DISPONIBLES -->
            @if(!empty(optional($patient)->allergie_medicamenteuse) || !empty(optional($patient)->type_allergie_medicamenteuse) || !empty(optional($patient)->antecedent_churgical) || !empty(optional($patient)->type_antecedent_medical))
                <div class="box shadow-none border mb-3">
                    <div class="box-header bg-light py-2 px-3">
                        <h6 class="box-title fs-13 fw-bold text-danger mb-0">
                            <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Allergies &amp; Antécédents
                        </h6>
                    </div>
                    <div class="box-body p-3 fs-13">
                        @if(!empty(optional($patient)->allergie_medicamenteuse) || !empty(optional($patient)->type_allergie_medicamenteuse))
                            <div class="mb-2">
                                <span class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i> Allergies :</span>
                                <div class="text-dark">{{ optional($patient)->type_allergie_medicamenteuse ?: $patient->allergie_medicamenteuse }}</div>
                            </div>
                        @endif
                        @if(!empty(optional($patient)->antecedent_churgical) || !empty(optional($patient)->type_antecedent_medical))
                            <div>
                                <span class="text-muted fw-bold"><i class="fa-solid fa-file-waveform me-1"></i> Antécédents :</span>
                                <div class="text-dark">{{ optional($patient)->type_antecedent_medical ?: $patient->antecedent_churgical }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        <!-- COLONNE DROITE : DETAILS DE LA CONSULTATION & ACTES -->
        <div class="col-lg-8 col-md-7 col-12">
            
            <!-- BANDEAU DE 4 METRIQUES SYNTHETIQUES -->
            <div class="row g-2 mb-3">
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 bg-white rounded border">
                        <div class="text-muted fs-11 text-uppercase fw-bold">Code Consultation</div>
                        <div class="fw-bold text-primary fs-14 text-truncate">{{ $consultation->code_consultation ?? ('#' . $consultation->id) }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 bg-white rounded border">
                        <div class="text-muted fs-11 text-uppercase fw-bold">Date de Visite</div>
                        <div class="fw-bold text-dark fs-14 text-truncate">
                            {{ date('d/m/Y', strtotime($consultation->date_consultation ?? $consultation->created_at)) }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 bg-white rounded border">
                        <div class="text-muted fs-11 text-uppercase fw-bold">Type de Visite</div>
                        <div class="fw-bold text-dark fs-14 text-truncate">{{ $typeVisite }}</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="p-3 bg-white rounded border">
                        <div class="text-muted fs-11 text-uppercase fw-bold">Praticien</div>
                        <div class="fw-bold text-dark fs-14 text-truncate">{{ $practitionerName }}</div>
                    </div>
                </div>
            </div>

            <!-- BANDEAU PARAMETRES CLINIQUES / CONSTANTES VITALES -->
            @if($hasVitals)
                <div class="box shadow-none border mb-3">
                    <div class="box-header bg-light py-2 px-3">
                        <h6 class="box-title fs-13 fw-bold text-dark mb-0">
                            <i class="fa-solid fa-heart-pulse text-danger me-2"></i> Constantes &amp; Paramètres Vitaux
                        </h6>
                    </div>
                    <div class="box-body p-3 fs-13">
                        <div class="row g-2 text-center">
                            @if(!empty($consultation->tension_arterielle))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">Tension Artérielle</div>
                                        <strong class="text-dark fs-14">{{ $consultation->tension_arterielle }} <span class="fs-11 fw-normal text-muted">mmHg</span></strong>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($tempVal))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">Température</div>
                                        <strong class="text-dark fs-14">{{ $tempVal }} <span class="fs-11 fw-normal text-muted">°C</span></strong>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($poidsVal))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">Poids</div>
                                        <strong class="text-dark fs-14">{{ $poidsVal }} <span class="fs-11 fw-normal text-muted">kg</span></strong>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($tailleVal))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">Taille</div>
                                        <strong class="text-dark fs-14">{{ $tailleVal }} <span class="fs-11 fw-normal text-muted">cm</span></strong>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($consultation->pouls))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">Pouls / Fréquence</div>
                                        <strong class="text-dark fs-14">{{ $consultation->pouls }} <span class="fs-11 fw-normal text-muted">bpm</span></strong>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($consultation->saturation_oxygene))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">Saturation O₂ (SpO2)</div>
                                        <strong class="text-dark fs-14">{{ $consultation->saturation_oxygene }} <span class="fs-11 fw-normal text-muted">%</span></strong>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($consultation->imc))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">I.M.C</div>
                                        <strong class="text-dark fs-14">{{ $consultation->imc }}</strong>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($consultation->gly_a_jeun) || !empty($consultation->gly_nn_jeun))
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="p-2 bg-light rounded border">
                                        <div class="text-muted fs-11">Glycémie</div>
                                        <strong class="text-dark fs-14">{{ $consultation->gly_a_jeun ?: $consultation->gly_nn_jeun }} <span class="fs-11 fw-normal text-muted">g/L</span></strong>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- CARTE ISSUE DE CONSULTATION & ORIENTATION -->
            <div class="box shadow-none border mb-3">
                <div class="box-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                    <h6 class="box-title fs-14 fw-bold text-dark mb-0">
                        <i class="fa-solid fa-arrows-split-up-and-left text-primary me-2"></i> Issue de Consultation &amp; Orientation
                    </h6>
                    @if($issue === 'refere-interne' || str_contains($justif, 'infirmier') || str_contains($justif, 'orienté') || str_contains($justif, 'référé'))
                        <span class="badge bg-info-subtle text-info border border-info-subtle fs-11">Affectation / Référé</span>
                    @elseif($issue === 'hospitalisation')
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fs-11">Hospitalisation</span>
                    @elseif($issue === 'observation')
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">Mise en observation</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">Sortie Domicile</span>
                    @endif
                </div>
                <div class="box-body p-3 fs-13">
                    <div class="p-3 bg-light rounded border">
                        <div class="mb-2">
                            <span class="text-muted fw-bold">Mode d'issue :</span> 
                            <strong>
                                @if($issue === 'refere-interne' || str_contains($justif, 'infirmier') || str_contains($justif, 'orienté') || str_contains($justif, 'référé'))
                                    Réaffectation / Référé interne
                                @elseif($issue === 'hospitalisation')
                                    Hospitalisation requise
                                @elseif($issue === 'observation')
                                    Mise en observation (M.O)
                                @elseif($issue === 'sortie')
                                    Sortie autorisée vers le domicile
                                @else
                                    {{ ucfirst($issue) }}
                                @endif
                            </strong>
                        </div>
                        <div>
                            <span class="text-muted fw-bold">Consignes &amp; Justification :</span> 
                            <span class="text-secondary">{{ $justif }}</span>
                        </div>
                        @if(!empty($consultation->date_prochain_rdv))
                            <div class="mt-2 pt-2 border-top">
                                <span class="text-muted fw-bold"><i class="fa-solid fa-calendar-check text-primary me-1"></i> Prochain Rendez-vous :</span>
                                <strong class="text-primary">{{ date('d/m/Y', strtotime($consultation->date_prochain_rdv)) }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- CARTE MOTIF & DIAGNOSTIC & OBSERVATIONS -->
            <div class="box shadow-none border mb-3">
                <div class="box-header bg-light py-2 px-3">
                    <h6 class="box-title fs-14 fw-bold text-dark mb-0">
                        <i class="fa-solid fa-notes-medical text-primary me-2"></i> Motif, Diagnostic &amp; Observations
                    </h6>
                </div>
                <div class="box-body p-3 fs-13">
                    @if(!empty($consultation->motif_consultation))
                        <div class="mb-3">
                            <span class="text-muted fw-bold d-block mb-1">Motif de consultation :</span>
                            <div class="p-2 bg-light rounded border text-secondary">
                                {{ $consultation->motif_consultation }}
                            </div>
                        </div>
                    @endif

                    @if(!empty($consultation->diagnostic))
                        <div class="mb-3">
                            <span class="text-muted fw-bold d-block mb-1">Diagnostic clinique :</span>
                            <div class="p-2 bg-light rounded border text-dark fw-semibold">
                                {{ $consultation->diagnostic }}
                            </div>
                        </div>
                    @endif

                    @if(!empty(optional($consultation->observation)->observations))
                        <div>
                            <span class="text-muted fw-bold d-block mb-1">Observations médicales :</span>
                            <div class="p-2 bg-light rounded border text-secondary">
                                {{ $consultation->observation->observations }}
                            </div>
                        </div>
                    @elseif(empty($consultation->motif_consultation) && empty($consultation->diagnostic))
                        <div class="p-2 bg-light rounded border text-muted">
                            Aucune observation particulière saisie.
                        </div>
                    @endif
                </div>
            </div>

            <!-- CARTE ORDONNANCES PRESCRITES -->
            <div class="box shadow-none border mb-3">
                <div class="box-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                    <h6 class="box-title fs-14 fw-bold text-dark mb-0">
                        <i class="fa-solid fa-pills text-primary me-2"></i> Ordonnance &amp; Prescriptions Médicamenteuses
                    </h6>
                    @if($ordonnance)
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-primary border font-monospace fs-11">{{ $ordonnance->reference ?? ('ORD-' . $ordonnance->id) }}</span>
                            <a href="{{ route('consultation.imprimer.post', ['post' => 'ordonnance', 'id' => $ordonnance->id]) }}" target="_blank" class="btn btn-xs btn-outline-primary fw-bold">
                                <i class="fa-solid fa-print me-1"></i> Imprimer PDF
                            </a>
                        </div>
                    @endif
                </div>
                <div class="box-body p-0">
                    @if($ordonnance && optional($ordonnance->prescriptions)->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0 fs-13">
                                <thead class="bg-light text-muted fs-11 text-uppercase">
                                    <tr>
                                        <th class="ps-3" style="width: 40px;">#</th>
                                        <th>Médicament</th>
                                        <th>Dosage</th>
                                        <th>Quantité</th>
                                        <th>Voie / Durée</th>
                                        <th>Posologie / Conseils</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ordonnance->prescriptions as $idx => $med)
                                        @php
                                            $drugName = optional($med->drug)->name 
                                                ?? optional(optional($med->drugHospital)->drug)->name 
                                                ?? ($med->medicament ?: 'Médicament');
                                        @endphp
                                        <tr>
                                            <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                                            <td><strong>{{ $drugName }}</strong></td>
                                            <td class="text-secondary">{{ $med->dosage ?: '-' }}</td>
                                            <td class="text-secondary">{{ $med->quantity ?: 1 }}</td>
                                            <td class="text-secondary">
                                                {{ $med->route_administration ?: '-' }}
                                                @if(!empty($med->duration))
                                                    <span class="text-muted">({{ $med->duration }} j)</span>
                                                @endif
                                            </td>
                                            <td class="text-secondary">
                                                {{ $med->posologie ?: ($med->instructions ?: ($med->frequence ?: ($med->health_dietetic_advice ?: 'Selon prescription'))) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted fs-13">
                            <i class="fa-solid fa-ban text-secondary me-1"></i> Aucune ordonnance médicamenteuse émise lors de cette consultation.
                        </div>
                    @endif
                </div>
            </div>

            <!-- CARTE EXAMENS & ANALYSES DE LABORATOIRE -->
            <div class="box shadow-none border mb-3">
                <div class="box-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                    <h6 class="box-title fs-14 fw-bold text-dark mb-0">
                        <i class="fa-solid fa-flask-vial text-primary me-2"></i> Examens &amp; Analyses de Laboratoire
                    </h6>
                    @if($bulletin)
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-primary border font-monospace fs-11">{{ $bulletin->code_bulletin ?? ('BLAB-' . $bulletin->id) }}</span>
                            <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-xs btn-outline-primary fw-bold">
                                <i class="fa-solid fa-file-pdf me-1"></i> Télécharger PDF
                            </a>
                        </div>
                    @endif
                </div>
                <div class="box-body p-0">
                    @if($bulletin && optional($bulletin->examens)->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0 fs-13">
                                <thead class="bg-light text-muted fs-11 text-uppercase">
                                    <tr>
                                        <th class="ps-3" style="width: 40px;">#</th>
                                        <th>Code Examen</th>
                                        <th>Nature de l'Analyse</th>
                                        <th>Date</th>
                                        <th class="text-center pe-3">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bulletin->examens as $idx => $ex)
                                        <tr>
                                            <td class="ps-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border font-monospace">{{ $ex->code_examen ?? ('EX-' . $ex->id) }}</span>
                                            </td>
                                            <td><strong>{{ $ex->nature_examen }}</strong></td>
                                            <td class="text-muted fs-12">{{ date('d/m/Y', strtotime($ex->date_examen ?? ($bulletin->date_bulletin ?? date('Y-m-d')))) }}</td>
                                            <td class="text-center pe-3">
                                                @if($ex->status == 1)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fs-11">
                                                        <i class="fa-solid fa-check me-1"></i> Terminé
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">
                                                        <i class="fa-solid fa-clock me-1"></i> En attente
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-3 text-center text-muted fs-13">
                            <i class="fa-solid fa-ban text-secondary me-1"></i> Aucun examen de laboratoire émis lors de cette consultation.
                        </div>
                    @endif
                </div>
            </div>

            <!-- CARTE ARRET DE TRAVAIL -->
            @if($arret)
                <div class="box shadow-none border mb-3">
                    <div class="box-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                        <h6 class="box-title fs-14 fw-bold text-dark mb-0">
                            <i class="fa-solid fa-user-clock text-primary me-2"></i> Arrêt de Travail
                        </h6>
                        <a href="{{ route('consultation.imprimer.post', ['post' => 'arret', 'id' => $arret->id]) }}" target="_blank" class="btn btn-xs btn-outline-primary fw-bold">
                            <i class="fa-solid fa-print me-1"></i> Imprimer PDF
                        </a>
                    </div>
                    <div class="box-body p-3 fs-13">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="p-2 bg-light rounded border">
                                    <span class="text-muted fs-11 d-block">Code Référence</span>
                                    <strong>{{ $arret->code }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-light rounded border">
                                    <span class="text-muted fs-11 d-block">Période</span>
                                    <strong>Du {{ date('d/m/Y', strtotime($arret->date_debut)) }} au {{ date('d/m/Y', strtotime($arret->date_fin)) }}</strong>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-light rounded border">
                                    <span class="text-muted fs-11 d-block">Durée</span>
                                    <strong>{{ $arret->nb_jour }} jour(s)</strong>
                                </div>
                            </div>
                        </div>
                        @if(!empty($arret->motif))
                            <div class="mt-2 text-muted fs-12">
                                <strong>Motif :</strong> {{ $arret->motif }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- LIENS HOSPITALISATION / OBSERVATION SI EXISTANTS -->
            @if($consultation->hospitalisation || $consultation->observation)
                <div class="box shadow-none border mb-3">
                    <div class="box-header bg-light py-2 px-3">
                        <h6 class="box-title fs-14 fw-bold text-dark mb-0">
                            <i class="fa-solid fa-bed text-primary me-2"></i> Séjour Hospitalier / Observation Associé
                        </h6>
                    </div>
                    <div class="box-body p-3 fs-13 d-flex gap-2 flex-wrap">
                        @if($consultation->hospitalisation)
                            <a href="{{ route('doctor.hospitalisation.edit', $consultation->hospitalisation->id) }}" class="btn btn-outline-info btn-sm fw-semibold">
                                <i class="fa-solid fa-bed me-1"></i> Voir le dossier d'hospitalisation
                            </a>
                        @endif
                        @if($consultation->observation)
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-12 p-2">
                                <i class="fa-solid fa-eye me-1"></i> Dossier de mise en observation #{{ $consultation->observation->code_observation ?? $consultation->observation->id }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</section>
@endsection
