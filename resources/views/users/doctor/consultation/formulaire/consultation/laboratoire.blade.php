@php
    $patient = $consultation->patient ?? optional($consultation->admission)->patient;
    $patientUser = optional($patient)->user;
    $doctor = $consultation->doctor ?? Auth::user()->doctor;
    $doctorUser = optional($doctor)->user ?? Auth::user();
    $hospital = $consultation->hospital ?? optional($doctor)->hospital ?? Auth::user()->hospital;

    $patientFullName = trim((optional($patientUser)->name ?? '') . ' ' . (optional($patientUser)->prenom ?? '')) ?: 'Patient';
    $patientResidence = optional(optional($patient)->residenceActuelle)->name ?? (optional($patient)->adresse ?? '');
    $patientTelephone = optional($patient)->telephone ?: (optional($patientUser)->contact ?? '');
    $patientBirthDate = optional($patient)->birth_date ?? '';
    $patientGender = strtolower(optional($patient)->gender ?? 'masculin');
    $codePatient = optional($patient)->code_patient ?? ('#' . $consultation->id);

    $doctorFullName = 'Dr. ' . trim((optional($doctorUser)->name ?? '') . ' ' . (optional($doctorUser)->prenom ?? ''));
    $hospitalName = optional($hospital)->label ?: (optional($hospital)->nom_direction_generale ?: 'Hôpital GEMMA');
    $hospitalAddress = optional($hospital)->adresse ?: (optional(optional($hospital)->localiteCommune)->name ?? 'Abidjan');
    $doctorPhone = optional($doctorUser)->contact ?: (optional($hospital)->telephone ?? '');

    $hospitalId = optional($hospital)->id ?? Auth::user()->hospital_id ?? optional(Auth::user()->doctor)->hospital_id ?? 1;
    $servicesList = $servicesHospital ?? \App\Models\ServiceHospital::where('hospital_id', $hospitalId)
        ->whereHas('service')
        ->with('service')
        ->where('status', 0)
        ->get();
    $doctorsList = $doctorsHospital ?? \App\Models\Doctor::where('hospital_id', $hospitalId)
        ->with(['user', 'serviceHospital.service', 'typeDoctor'])
        ->get();
    $infirmiersList = $infirmiersHospital ?? \App\Models\Infirmier::where('hospital_id', $hospitalId)
        ->with(['user', 'serviceHospital.service'])
        ->get();

    $infirmiersArray = [];
    foreach ($infirmiersList as $inf) {
        $infirmiersArray[] = [
            'id' => $inf->id,
            'name' => trim((optional($inf->user)->name ?? '') . ' ' . (optional($inf->user)->prenom ?? '')),
            'service_hospital_id' => $inf->service_hospital_id,
            'service_libelle' => optional(optional($inf->serviceHospital)->service)->libelle ?? 'Soins Infirmiers'
        ];
    }
    $infirmiersJson = json_encode($infirmiersArray);

    $doctorsArray = [];
    foreach ($doctorsList as $doc) {
        $doctorsArray[] = [
            'id' => $doc->id,
            'name' => 'Dr. ' . trim((optional($doc->user)->name ?? '') . ' ' . (optional($doc->user)->prenom ?? '')),
            'service_hospital_id' => $doc->service_hospital_id,
            'service_libelle' => optional(optional($doc->serviceHospital)->service)->libelle ?? optional($doc->typeDoctor)->libelle ?? 'Médecine'
        ];
    }
    $doctorsJson = json_encode($doctorsArray);
@endphp

<!-- CONTENEUR GENERAL AVEC EXACTEMENT 2% DE MARGE A GAUCHE ET A DROITE -->
<div class="labo-outer-container">

    <!-- Barre d'actions supérieure -->
    <div class="labo-top-bar">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('doctor.consultation.today') }}" class="btn-labo-nav btn-labo-secondary">
                <i class="fa fa-arrow-left"></i> Retour aux consultations
            </a>
            <div class="labo-patient-pill">
                <i class="fa fa-folder-open text-primary"></i>
                <span>Dossier : <strong>{{ $codePatient }}</strong> &mdash; <strong>{{ $patientFullName }}</strong></span>
            </div>
            <!-- Bouton Relancer l'Appel Salle d'Attente -->
            <button type="button" 
                class="btn-labo-nav btn-labo-recall btn-recall-in-consultation"
                data-id="{{ $consultation->id }}"
                data-name="{{ $patientFullName }}"
                title="Relancer l'appel sonore et visuel sur l'écran de la salle d'attente">
                <i class="fa-solid fa-bullhorn text-warning"></i>
                <span>Relancer l'appel</span>
            </button>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-labo-nav btn-labo-info" onclick="window.print()">
                <i class="fa fa-print"></i> Imprimer la fiche
            </button>
        </div>
    </div>

    <!-- FORMULAIRE PRINCIPAL -->
    <form id="form-laboratoire" action="{{ route('doctor.consultation.store.laboratoire') }}" method="POST">
        @csrf
        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
        <input type="hidden" name="patient_id" value="{{ optional($patient)->id }}">

        <!-- FEUILLE DE STYLE MEDICALE -->
        <div class="labo-sheet">
            
            <!-- EN-TETE OFFICIEL DU FORMULAIRE -->
            <div class="labo-sheet-header">
                <div class="labo-sheet-header-left">
                    <div class="labo-badge-inst">Laboratoire d'Analyses Médicales & Biologie</div>
                    <h2 class="labo-main-title">
                        Formulaire de Demande d'Examen Médical
                    </h2>
                    <div class="labo-hospital-name">
                        <i class="fa-solid fa-hospital me-1"></i> {{ $hospitalName }}
                    </div>
                </div>
                <div class="labo-sheet-header-right">
                    <div class="labo-reception-box">
                        <div class="reception-label">Reçu le :</div>
                        <input type="date" name="date_reception" class="reception-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <!-- SECTION 1 : INFOS PATIENT & DEMANDEUR (50% / 50% - LECTURE SEULE) -->
            <div class="labo-grid-2 mb-3">
                <!-- Patient -->
                <div class="labo-box">
                    <div class="labo-box-title d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-user me-1 text-primary"></i> Informations sur le patient</span>
                        <span class="badge bg-light text-secondary border fs-11"><i class="fa-solid fa-lock me-1"></i> Lecture seule</span>
                    </div>
                    <div class="labo-box-body bg-light-subtle">
                        <div class="labo-field-row">
                            <span class="labo-field-label">Nom & Prénoms :</span>
                            <input type="text" name="patient_nom" class="labo-field-input labo-field-readonly fw-bold" value="{{ $patientFullName }}" readonly tabindex="-1">
                        </div>
                        <div class="labo-field-row">
                            <span class="labo-field-label">Adresse / Résidence :</span>
                            <input type="text" name="patient_adresse" class="labo-field-input labo-field-readonly" value="{{ $patientResidence }}" readonly tabindex="-1" placeholder="Non renseigné">
                        </div>
                        <div class="labo-field-row">
                            <span class="labo-field-label">N° Téléphone :</span>
                            <input type="text" name="patient_telephone" class="labo-field-input labo-field-readonly" value="{{ $patientTelephone }}" readonly tabindex="-1" placeholder="Non renseigné">
                        </div>
                        <div class="labo-field-row">
                            <span class="labo-field-label">Date de naissance :</span>
                            <input type="text" name="patient_birth_date" class="labo-field-input labo-field-readonly" value="{{ $patientBirthDate }}" readonly tabindex="-1" placeholder="Non renseigné">
                        </div>
                        <div class="labo-field-row">
                            <span class="labo-field-label">Sexe :</span>
                            <div class="labo-radio-group" style="pointer-events: none; opacity: 0.85;">
                                <label class="labo-choice">
                                    <input type="radio" name="patient_gender_view" value="masculin" {{ str_contains($patientGender, 'masc') || $patientGender == 'm' ? 'checked' : '' }} disabled>
                                    <span>Masculin</span>
                                </label>
                                <label class="labo-choice">
                                    <input type="radio" name="patient_gender_view" value="feminin" {{ str_contains($patientGender, 'fem') || $patientGender == 'f' ? 'checked' : '' }} disabled>
                                    <span>Féminin</span>
                                </label>
                                <input type="hidden" name="patient_gender" value="{{ $patientGender }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Demandeur -->
                <div class="labo-box">
                    <div class="labo-box-title d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-user-doctor me-1 text-primary"></i> Informations sur le demandeur</span>
                        <span class="badge bg-light text-secondary border fs-11"><i class="fa-solid fa-lock me-1"></i> Lecture seule</span>
                    </div>
                    <div class="labo-box-body bg-light-subtle">
                        <div class="labo-field-row">
                            <span class="labo-field-label">Médecin prescripteur :</span>
                            <input type="text" name="demandeur_nom" class="labo-field-input labo-field-readonly fw-bold" value="{{ $doctorFullName }}" readonly tabindex="-1">
                        </div>
                        <div class="labo-field-row">
                            <span class="labo-field-label">Établissement / Org. :</span>
                            <input type="text" name="demandeur_organisation" class="labo-field-input labo-field-readonly" value="{{ $hospitalName }}" readonly tabindex="-1">
                        </div>
                        <div class="labo-field-row">
                            <span class="labo-field-label">Adresse :</span>
                            <input type="text" name="demandeur_adresse" class="labo-field-input labo-field-readonly" value="{{ $hospitalAddress }}" readonly tabindex="-1">
                        </div>
                        <div class="labo-field-row">
                            <span class="labo-field-label">N° Téléphone :</span>
                            <input type="text" name="demandeur_telephone" class="labo-field-input labo-field-readonly" value="{{ $doctorPhone }}" readonly tabindex="-1" placeholder="Non renseigné">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2 : INFORMATIONS SUR L'ECHANTILLON -->
            <div class="labo-box mb-3">
                <div class="labo-box-title">
                    <i class="fa-solid fa-vial me-1 text-primary"></i> Informations sur l'échantillon
                </div>
                <div class="labo-box-body">
                    <!-- Urgence & Date/Heure de prélèvement -->
                    <div class="labo-grid-2 mb-3">
                        <div class="labo-sub-card">
                            <div class="labo-sub-label">Niveau d'Urgence :</div>
                            <div class="labo-radio-group">
                                <label class="labo-choice">
                                    <input type="radio" name="urgence" value="Normal" checked>
                                    <span class="badge-normal">Normal</span>
                                </label>
                                <label class="labo-choice">
                                    <input type="radio" name="urgence" value="URGENT">
                                    <span class="badge-urgent"><i class="fa fa-bolt me-1"></i> URGENT</span>
                                </label>
                            </div>
                        </div>

                        <div class="labo-sub-card">
                            <div class="labo-sub-label">Échantillon prélevé sur le patient :</div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="fs-12 text-muted fw-bold">Date :</span>
                                    <input type="date" name="date_prelevement" class="labo-inline-input" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="fs-12 text-muted fw-bold">Heure :</span>
                                    <input type="time" name="heure_prelevement" class="labo-inline-input" value="{{ date('H:i') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Condition à jeun -->
                    <div class="labo-sub-card mb-3">
                        <div class="d-flex align-items-center gap-4 flex-wrap">
                            <span class="labo-sub-label mb-0">Condition du patient :</span>
                            <div class="labo-radio-group">
                                <label class="labo-choice">
                                    <input type="radio" name="a_jeun" value="A jeun" checked>
                                    <span>À jeun</span>
                                </label>
                                <label class="labo-choice">
                                    <input type="radio" name="a_jeun" value="Non a jeun">
                                    <span>Non à jeun</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Types de prélèvements (8 items OMS) -->
                    <div class="labo-sub-card">
                        <div class="labo-sub-label mb-2">Nature du prélèvement :</div>
                        <div class="labo-sample-grid">
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Sang">
                                <span>Sang</span>
                            </label>
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Urine">
                                <span>Urine</span>
                            </label>
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Écouvillon">
                                <span>Écouvillon</span>
                            </label>
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Tissu">
                                <span>Tissu</span>
                            </label>
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Fèces">
                                <span>Fèces</span>
                            </label>
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Pus">
                                <span>Pus</span>
                            </label>
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Liquides">
                                <span>Liquides</span>
                            </label>
                            <label class="labo-sample-item">
                                <input type="checkbox" name="type_echantillon[]" value="Cytologie">
                                <span>Cytologie</span>
                            </label>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                            <span class="fs-12 fw-bold text-dark text-nowrap">Autre, préciser :</span>
                            <input type="text" name="echantillon_autre" class="labo-field-input" placeholder="Préciser tout autre type de prélèvement...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3 : INFORMATIONS CLINIQUES PERTINENTES -->
            <div class="labo-box mb-3">
                <div class="labo-box-title">
                    <i class="fa-solid fa-notes-medical me-1 text-primary"></i> Informations cliniques pertinentes
                </div>
                <div class="labo-box-body">
                    <div class="labo-grid-2 mb-2">
                        <div>
                            <span class="labo-field-label mb-1 d-block">Traitement médicamenteux en cours :</span>
                            <input type="text" name="traitement_medicamenteux" class="labo-field-input" placeholder="Antibiotiques, anticoagulants, etc.">
                        </div>
                        <div>
                            <span class="labo-field-label mb-1 d-block">Dernière prise / dose :</span>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <input type="text" name="derniere_dose" class="labo-field-input flex-grow-1" placeholder="Dose (ex: 500mg)">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="fs-12 text-muted">Date:</span>
                                    <input type="date" name="derniere_dose_date" class="labo-inline-input">
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="fs-12 text-muted">Heure:</span>
                                    <input type="time" name="derniere_dose_heure" class="labo-inline-input">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="labo-field-label mb-1 d-block">Motif / Renseignements cliniques & Diagnostic présomptif :</span>
                        <input type="text" name="autres_infos_cliniques" class="labo-field-input" value="{{ $consultation->motif_consultation ?? optional($consultation->admission)->motif_consultation ?? '' }}" placeholder="Indiquez les éléments cliniques utiles au biologiste...">
                    </div>
                </div>
            </div>

            <!-- SECTION 4 : EXAMENS DEMANDES (TABLEAU MEDICAL DE HAUTE PRECISION OMS) -->
            <div class="labo-box mb-3">
                <div class="labo-box-title d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-microscope me-2 text-primary"></i> Examens demandés :</span>
                    <span class="labo-counter-pill"><i class="fa-solid fa-check-double me-1"></i> <span id="examCounter">0</span> sélectionné(s)</span>
                </div>
                <div class="table-responsive p-0">
                    <table class="labo-table-examens">
                        <thead>
                            <tr>
                                <th style="width: 23%;">
                                    <div class="th-title">Profil d'examens</div>
                                </th>
                                <th style="width: 23%;">
                                    <div class="th-title">Biochimie</div>
                                </th>
                                <th style="width: 20%;">
                                    <div class="th-title">Hématologie</div>
                                </th>
                                <th style="width: 19%;">
                                    <div class="th-title">Microbiologie</div>
                                </th>
                                <th style="width: 15%;">
                                    <div class="th-title">Anatomo-pathologie</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <!-- 1. Profil d'examens (2 sous-colonnes avec séparateur) -->
                                <td class="align-top labo-td-col">
                                    <div class="labo-subgrid-2">
                                        <div class="labo-subcol pe-1">
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: G2000"><span>G2000</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: G 2000-X"><span>G 2000-X</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: GT9"><span>GT9</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: GTI"><span>GTI</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: NEO"><span>NEO</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: ES"><span>ES</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: HB3"><span>HB3</span></label>
                                        </div>
                                        <div class="labo-subcol ps-1 border-start">
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: DFS"><span>DFS</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: LFT"><span>LFT</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: RFT"><span>RFT</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: TFT"><span>TFT</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: MAC"><span>MAC</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: LGL"><span>LGL</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Profil: LIP"><span>LIP</span></label>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. Biochimie (2 sous-colonnes avec séparateur) -->
                                <td class="align-top labo-td-col">
                                    <div class="labo-subgrid-2">
                                        <div class="labo-subcol pe-1">
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: ACE"><span>ACE</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: CA 1"><span>CA 1</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: CA 5"><span>CA 5</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: CA 9"><span>CA 9</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: PSA"><span>PSA</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: AFP"><span>AFP</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: Glucose"><span>Glucose</span></label>
                                        </div>
                                        <div class="labo-subcol ps-1 border-start">
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: VIH 1 et 2"><span>VIH 1 et 2</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: HbA1c"><span>HbA1c</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: HBsAg"><span>HBsAg</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: H. pylori"><span>H. pylori</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: Ac. urique"><span>Ac. urique</span></label>
                                            <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Biochimie: T4 libre"><span>T4 libre</span></label>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. Hématologie -->
                                <td class="align-top labo-td-col">
                                    <div class="d-flex flex-column gap-1">
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Hématologie: bilan hématologique (avec VSG)"><span>Bilan hématologique (avec VSG)</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Hématologie: NFS"><span>NFS</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Hématologie: Hémoglobine"><span>Hémoglobine</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Hématologie: Leucocytes totaux et différenciés"><span>Leucocytes totaux et différenciés</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Hématologie: Plaquettes"><span>Plaquettes</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Hématologie: ABO et Rh (D)"><span>ABO et Rh (D)</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Hématologie: Parasites palustres"><span>Parasites palustres</span></label>
                                    </div>
                                </td>

                                <!-- 4. Microbiologie -->
                                <td class="align-top labo-td-col">
                                    <div class="d-flex flex-column gap-1">
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Microbiologie: Examen chimique et microscopique de l'urine"><span>Examen chimique et microscopique de l'urine</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Microbiologie: RPR (VDRL)"><span>RPR (VDRL)</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Microbiologie: Microscopie/Culture/Sensibilité"><span>Microscopie / Culture / Sensibilité</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Microbiologie: BAAR (ZN) frottis uniquement"><span>BAAR (ZN) frottis uniquement</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Microbiologie: BAAR frottis et culture"><span>BAAR frottis et culture</span></label>
                                    </div>
                                </td>

                                <!-- 5. Anatomo-pathologie -->
                                <td class="align-top labo-td-col">
                                    <div class="d-flex flex-column gap-1 mb-3">
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Anatomo-pathologie: Histologie"><span>Histologie</span></label>
                                        <label class="labo-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Anatomo-pathologie: Non-gynécologique/PAF"><span>Non-gynécologique / PAF</span></label>
                                    </div>
                                    <div class="pt-2 border-top">
                                        <span class="fs-12 fw-bold text-dark d-block mb-1">Site :</span>
                                        <input type="text" name="anapath_site" class="labo-field-input" placeholder="Préciser le site...">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECTION 5 : EXAMENS SUPPLEMENTAIRES & CYTOLOGIE CERVICALE (50% / 50%) -->
            <div class="labo-grid-2 mb-4">
                <!-- Examens supplémentaires -->
                <div class="labo-box">
                    <div class="labo-box-title">
                        <i class="fa-solid fa-list-check me-1 text-primary"></i> Examens supplémentaires
                    </div>
                    <div class="labo-box-body">
                        <textarea name="examens_supplementaires" class="labo-textarea" rows="8" placeholder="Saisir ici d'autres analyses ou examens spécifiques non listés ci-dessus (un par ligne)..."></textarea>
                    </div>
                </div>

                <!-- Cytologie cervicale -->
                <div class="labo-box">
                    <div class="labo-box-title">
                        <i class="fa-solid fa-dna me-1 text-primary"></i> Cytologie cervicale
                    </div>
                    <div class="labo-box-body">
                        <div class="labo-sample-grid mb-2">
                            <label class="labo-sample-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Cytologie: Frottis vaginal"><span>Frottis vaginal</span></label>
                            <label class="labo-sample-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Cytologie: Normal"><span>Normal</span></label>
                            <label class="labo-sample-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Cytologie: Sang après mononucléose"><span>Sang après mononucléose</span></label>
                            <label class="labo-sample-item"><input type="checkbox" class="exam-ch-input" name="examens[]" value="Cytologie: Lésion suspectée"><span>Lésion suspectée</span></label>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="fs-12 fw-bold text-dark text-nowrap">Autre :</span>
                            <input type="text" name="cytologie_autre" class="labo-field-input">
                        </div>

                        <div class="border-top pt-2 mb-2">
                            <span class="fs-12 fw-bold text-dark mb-1 d-block">Site du prélèvement cytologique :</span>
                            <div class="labo-sample-grid">
                                <label class="labo-sample-item"><input type="checkbox" name="cytologie_site[]" value="Col de l'utérus"><span>Col de l'utérus</span></label>
                                <label class="labo-sample-item"><input type="checkbox" name="cytologie_site[]" value="Endocol"><span>Endocol</span></label>
                                <label class="labo-sample-item"><input type="checkbox" name="cytologie_site[]" value="Dôme vaginal"><span>Dôme vaginal</span></label>
                                <label class="labo-sample-item"><input type="checkbox" name="cytologie_site[]" value="Paroi vaginale latérale"><span>Paroi latérale</span></label>
                                <label class="labo-sample-item"><input type="checkbox" name="cytologie_site[]" value="Cul-de-sac postérieur du vagin"><span>Cul-de-sac postérieur</span></label>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-12 text-muted fw-bold">Date :</span>
                                <input type="date" name="cytologie_date" class="labo-inline-input" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-12 fw-bold text-dark">Qualité frottis :</span>
                                <label class="labo-sample-item mb-0"><input type="checkbox" name="cytologie_faible" value="1"><span>Faible</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 6 : ISSUE DE CONSULTATION & AFFECTATION DU PATIENT -->
            <div class="labo-box mb-4 labo-issue-box">
                <div class="labo-box-title d-flex justify-content-between align-items-center">
                    <span><i class="fa-solid fa-arrows-split-up-and-left me-1 text-primary"></i> Issue de Consultation &amp; Affectation du Patient</span>
                    <span class="badge bg-primary text-white fs-11"><i class="fa-solid fa-route me-1"></i> Orientation de sortie</span>
                </div>
                <div class="labo-box-body">
                    <p class="fs-12 text-muted mb-3">
                        Veuillez préciser la suite de la prise en charge pour ce patient après l'émission des examens de laboratoire :
                    </p>

                    <!-- Tuiles de choix d'issue -->
                    <div class="labo-issue-grid mb-3">
                        <!-- Option 1 : Sortie normale -->
                        <label class="labo-issue-card active" id="card-issue-sortie" onclick="switchLaboIssue('sortie')">
                            <input type="radio" name="mode_sortie" id="radio-issue-sortie" value="sortie" checked class="d-none">
                            <div class="issue-card-icon text-success">
                                <i class="fa-solid fa-house-chimney-medical"></i>
                            </div>
                            <div class="issue-card-content">
                                <div class="issue-card-title text-success">Sortie Domicile</div>
                                <div class="issue-card-desc">Examen prescrit, sortie normale autorisée</div>
                            </div>
                            <div class="issue-card-check text-success">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                        </label>

                        <!-- Option 2 : Affecter à une Infirmière -->
                        <label class="labo-issue-card" id="card-issue-infirmier" onclick="switchLaboIssue('affecter-infirmier')">
                            <input type="radio" name="mode_sortie" id="radio-issue-infirmier" value="affecter-infirmier" class="d-none">
                            <div class="issue-card-icon text-info">
                                <i class="fa-solid fa-user-nurse"></i>
                            </div>
                            <div class="issue-card-content">
                                <div class="issue-card-title text-info">Affecter à une Infirmière</div>
                                <div class="issue-card-desc">Prélèvements, injections, soins infirmiers</div>
                            </div>
                            <div class="issue-card-check text-info">
                                <i class="fa-regular fa-circle"></i>
                            </div>
                        </label>

                        <!-- Option 3 : Affecter à un Médecin -->
                        <label class="labo-issue-card" id="card-issue-medecin" onclick="switchLaboIssue('affecter-medecin')">
                            <input type="radio" name="mode_sortie" id="radio-issue-medecin" value="affecter-medecin" class="d-none">
                            <div class="issue-card-icon text-primary">
                                <i class="fa-solid fa-user-doctor"></i>
                            </div>
                            <div class="issue-card-content">
                                <div class="issue-card-title text-primary">Affecter à un Médecin</div>
                                <div class="issue-card-desc">Médecin traitant, spécialiste ou confrère</div>
                            </div>
                            <div class="issue-card-check text-primary">
                                <i class="fa-regular fa-circle"></i>
                            </div>
                        </label>

                        <!-- Option 4 : Hospitalisation / Observation -->
                        <label class="labo-issue-card" id="card-issue-hospitalisation" onclick="switchLaboIssue('hospitalisation')">
                            <input type="radio" name="mode_sortie" id="radio-issue-hospitalisation" value="hospitalisation" class="d-none">
                            <div class="issue-card-icon text-warning">
                                <i class="fa-solid fa-bed-pulse"></i>
                            </div>
                            <div class="issue-card-content">
                                <div class="issue-card-title text-warning">Hospitalisation / Obs.</div>
                                <div class="issue-card-desc">Mise en observation ou hospitalisation</div>
                            </div>
                            <div class="issue-card-check text-warning">
                                <i class="fa-regular fa-circle"></i>
                            </div>
                        </label>
                    </div>

                    <!-- SOUS-PANNEAU 1 : SORTIE SIMPLE (MESSAGE CONFIRMATION) -->
                    <div id="subpanel-sortie" class="labo-subpanel p-3 bg-light rounded border border-success-subtle">
                        <div class="d-flex align-items-center gap-2 text-success">
                            <i class="fa-solid fa-circle-check fs-18"></i>
                            <span class="fw-bold fs-13">Clôture et sortie normale</span>
                        </div>
                        <div class="text-muted fs-12 mt-1">
                            Le patient repart avec son bon de demande d'examen. La consultation de laboratoire sera marquée comme effectuée et enregistrée dans le registre médical.
                        </div>
                    </div>

                    <!-- SOUS-PANNEAU 2 : AFFECTATION INFIRMIERE -->
                    <div id="subpanel-affecter-infirmier" class="labo-subpanel p-3 bg-light rounded border border-info-subtle" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-info-subtle">
                            <div class="d-flex align-items-center gap-2 text-info">
                                <i class="fa-solid fa-user-nurse fs-18"></i>
                                <span class="fw-bold fs-14">Affectation à l'infirmerie (Sélection par service)</span>
                            </div>
                            <span class="badge bg-info-subtle text-info border border-info-subtle fs-11">Étape 1 : Service &rarr; Étape 2 : Infirmier(ère) &rarr; Instructions</span>
                        </div>
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-4 col-md-4 col-12">
                                <label class="form-label fs-12 fw-bold text-dark">
                                    <span class="badge bg-info text-white me-1">1</span> Service / Unité de soins <span class="text-danger">*</span>
                                </label>
                                <select id="select_service_infirmier" class="form-select form-select-sm labo-select-custom" onchange="filterInfirmiersByService(this.value)">
                                    <option value="" selected>-- 1. Choisir le service --</option>
                                    @foreach($servicesList as $serv)
                                        <option value="{{ $serv->id }}">{{ optional($serv->service)->libelle ?? ('Service #' . $serv->id) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-12">
                                <label class="form-label fs-12 fw-bold text-dark">
                                    <span class="badge bg-info text-white me-1">2</span> Infirmier(ère) du service <span class="text-danger">*</span>
                                </label>
                                <select name="affectation_infirmier_id" id="select_affectation_infirmier" class="form-select form-select-sm labo-select-custom" disabled>
                                    <option value="" disabled selected>-- 2. Choisir l'infirmier(ère) --</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-12">
                                <label class="form-label fs-12 fw-bold text-dark">
                                    <span class="badge bg-info text-white me-1">3</span> Instructions de soins / Prélèvement
                                </label>
                                <input type="text" name="instructions_infirmier" id="input_instructions_infirmier" class="form-control form-control-sm" placeholder="Ex: Prélèvement sanguin urgent...">
                            </div>
                            <div class="col-12 mt-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fs-11 text-muted fw-bold">Modèles rapides :</span>
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill fs-11 py-0 px-2" onclick="setInfirmierInstruction('Prélèvement sanguin et acheminement au laboratoire')">Prélèvement sanguin</button>
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill fs-11 py-0 px-2" onclick="setInfirmierInstruction('Prélèvement cytologique / écouvillon')">Prélèvement cytologique</button>
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill fs-11 py-0 px-2" onclick="setInfirmierInstruction('Administration traitement injectable et surveillance')">Injection / Traitement</button>
                                    <button type="button" class="btn btn-xs btn-outline-info rounded-pill fs-11 py-0 px-2" onclick="setInfirmierInstruction('Prise des constantes et surveillance')">Surveillance constantes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SOUS-PANNEAU 3 : AFFECTATION MEDECIN -->
                    <div id="subpanel-affecter-medecin" class="labo-subpanel p-3 bg-light rounded border border-primary-subtle" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-primary-subtle">
                            <div class="d-flex align-items-center gap-2 text-primary">
                                <i class="fa-solid fa-user-doctor fs-18"></i>
                                <span class="fw-bold fs-14">Affectation / Référence au médecin (Sélection par spécialité)</span>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">Étape 1 : Spécialité &rarr; Étape 2 : Médecin &rarr; Note</span>
                        </div>
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-4 col-md-4 col-12">
                                <label class="form-label fs-12 fw-bold text-dark">
                                    <span class="badge bg-primary text-white me-1">1</span> Spécialité / Service médical <span class="text-danger">*</span>
                                </label>
                                <select id="select_service_doctor" class="form-select form-select-sm labo-select-custom" onchange="filterDoctorsByService(this.value)">
                                    <option value="" selected>-- 1. Choisir la spécialité --</option>
                                    @foreach($servicesList as $serv)
                                        <option value="{{ $serv->id }}">{{ optional($serv->service)->libelle ?? ('Service #' . $serv->id) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-12">
                                <label class="form-label fs-12 fw-bold text-dark">
                                    <span class="badge bg-primary text-white me-1">2</span> Médecin du service <span class="text-danger">*</span>
                                </label>
                                <select name="affectation_doctor_id" id="select_affectation_doctor" class="form-select form-select-sm labo-select-custom" disabled>
                                    <option value="" disabled selected>-- 2. Choisir le médecin --</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-12">
                                <label class="form-label fs-12 fw-bold text-dark">
                                    <span class="badge bg-primary text-white me-1">3</span> Note de transmission / Motif d'avis
                                </label>
                                <input type="text" name="note_transmission_medecin" id="input_note_medecin" class="form-control form-control-sm" placeholder="Ex: Retour vers médecin traitant...">
                            </div>
                            <div class="col-12 mt-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fs-11 text-muted fw-bold">Modèles rapides :</span>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill fs-11 py-0 px-2" onclick="setMedecinNote('Retour consultation médecin traitant')">Retour médecin traitant</button>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill fs-11 py-0 px-2" onclick="setMedecinNote('Avis spécialisé post-résultats analyses')">Avis spécialisé</button>
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill fs-11 py-0 px-2" onclick="setMedecinNote('Interprétation des résultats et adaptation thérapeutique')">Interprétation résultats</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SOUS-PANNEAU 4 : HOSPITALISATION / OBSERVATION -->
                    <div id="subpanel-hospitalisation" class="labo-subpanel p-3 bg-light rounded border border-warning-subtle" style="display: none;">
                        <div class="d-flex align-items-center gap-2 text-warning-emphasis mb-3">
                            <i class="fa-solid fa-bed-pulse fs-18 text-warning"></i>
                            <span class="fw-bold fs-14">Orientation vers l'hospitalisation ou mise en observation</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label fs-12 fw-bold text-dark">Type de séjour</label>
                                <div class="d-flex align-items-center gap-4 pt-1">
                                    <label class="labo-item mb-0"><input type="radio" name="hospitalisation_sub_type" value="observation" checked onchange="updateHospMode(this.value)"><span>Mise en observation (M.O)</span></label>
                                    <label class="labo-item mb-0"><input type="radio" name="hospitalisation_sub_type" value="hospitalisation" onchange="updateHospMode(this.value)"><span>Hospitalisation</span></label>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label fs-12 fw-bold text-dark">Motif / Justification clinique</label>
                                <input type="text" name="motif_hospitalisation" class="form-control form-control-sm" placeholder="Ex: Altération état général en attente bilans urgents...">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- PIED DE PAGE : LOGO HOPITAL + BOUTONS D'ENREGISTREMENT -->
            <div class="labo-footer-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="labo-hospital-badge shadow-sm">
                        @if(!empty(optional($hospital)->img_url))
                            <img src="{{ asset('assets/uploads/hospital/' . $hospital->img_url) }}" alt="Logo" class="labo-hospital-logo">
                        @else
                            <img src="{{ asset(iconsLoad()['logo'] ?? 'assets/images/logo.png') }}" alt="Logo" class="labo-hospital-logo">
                        @endif
                        <div class="lh-1">
                            <div class="fw-bold fs-12 text-dark text-uppercase">{{ $hospitalName }}</div>
                            <div class="fs-11 text-muted">Service de Biologie Médicale & Analyses de Laboratoire</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold">
                        <i class="fa fa-times me-1"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-success px-5 py-2.5 fw-bold fs-15 shadow">
                        <i class="fa-solid fa-check-circle me-1"></i> Valider et Transmettre la Demande
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- STYLES CSS SUR-MESURE HAUTE FIDELITE AVEC 2% DE MARGE -->
<style>
    /* 1. Conteneur principal calé à 2% de marge gauche et droite */
    .content-wrapper > .container-full > .content {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .labo-outer-container {
        width: 96% !important;
        margin-left: 2% !important;
        margin-right: 2% !important;
        padding: 0 !important;
        box-sizing: border-box;
    }

    /* 2. Barre d'action supérieure */
    .labo-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
        padding-top: 8px;
    }

    .btn-labo-nav {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        padding: 7px 14px;
        border-radius: 6px;
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        transition: all 0.2s;
    }
    .btn-labo-secondary {
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
    }
    .btn-labo-secondary:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .btn-labo-info {
        background: #0284c7;
        color: #ffffff;
        border: 1px solid #0284c7;
    }
    .btn-labo-info:hover {
        background: #0369a1;
        color: #ffffff;
    }

    .labo-patient-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13.5px;
        color: #1e293b;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    /* 3. Feuille blanche de document médical */
    .labo-sheet {
        background: #ffffff;
        border: 2px solid #334155;
        border-radius: 8px;
        padding: 24px 30px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        margin-bottom: 30px;
        width: 100%;
        box-sizing: border-box;
    }

    /* En-tête */
    .labo-sheet-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 2px solid #1e293b;
        padding-bottom: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .labo-badge-inst {
        display: inline-block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        background: #e0f2fe;
        color: #0369a1;
        padding: 3px 8px;
        border-radius: 4px;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }
    .labo-main-title {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .labo-hospital-name {
        font-size: 14px;
        font-weight: 600;
        color: #0284c7;
    }

    .labo-reception-box {
        border: 2px solid #1e293b;
        border-radius: 6px;
        padding: 6px 12px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .reception-label {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }
    .reception-input {
        border: none;
        background: transparent;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        outline: none;
    }

    /* 4. Encadrés médicaux */
    .labo-box {
        border: 1.5px solid #334155;
        border-radius: 6px;
        background: #ffffff;
        overflow: hidden;
    }
    .labo-box-title {
        background: #f1f5f9;
        border-bottom: 1.5px solid #334155;
        padding: 8px 14px;
        font-size: 13.5px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .labo-box-body {
        padding: 14px 16px;
    }

    .labo-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    @media (max-width: 991px) {
        .labo-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* Champs de saisie sur ligne pointillée */
    .labo-field-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    .labo-field-label {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
        min-width: 140px;
        flex-shrink: 0;
    }
    .labo-field-input {
        flex-grow: 1;
        border: none;
        border-bottom: 1.5px dashed #64748b;
        background: transparent;
        padding: 4px 6px;
        font-size: 13px;
        color: #0f172a;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
    }
    .labo-field-input:focus {
        border-bottom: 2px solid #0284c7;
        background: #f0f9ff;
    }
    .labo-field-readonly {
        border-bottom: 1.5px solid #cbd5e1 !important;
        background: #f8fafc !important;
        color: #334155 !important;
        cursor: not-allowed !important;
        user-select: text;
    }
    .labo-field-readonly:focus {
        border-bottom: 1.5px solid #cbd5e1 !important;
        background: #f8fafc !important;
    }
    .labo-inline-input {
        border: none;
        border-bottom: 1.5px dashed #64748b;
        background: transparent;
        padding: 2px 6px;
        font-size: 13px;
        font-weight: 600;
        outline: none;
    }
    .labo-inline-input:focus {
        border-bottom: 2px solid #0284c7;
        background: #f0f9ff;
    }

    .labo-textarea {
        width: 100%;
        border: 1.5px dashed #94a3b8;
        border-radius: 4px;
        padding: 10px 12px;
        font-size: 13px;
        color: #0f172a;
        background: #fafafa;
        resize: vertical;
        outline: none;
    }
    .labo-textarea:focus {
        border-color: #0284c7;
        background: #ffffff;
    }

    /* Sous-cartes */
    .labo-sub-card {
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 10px 14px;
        background: #f8fafc;
    }
    .labo-sub-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
    }

    /* Cases à cocher & Boutons Radio Réinitialisés */
    .labo-outer-container input[type="checkbox"],
    .labo-outer-container input[type="radio"] {
        position: static !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        width: 16px !important;
        height: 16px !important;
        margin: 0 6px 0 0 !important;
        vertical-align: middle !important;
        cursor: pointer !important;
        display: inline-block !important;
        appearance: auto !important;
        -webkit-appearance: auto !important;
        flex-shrink: 0;
    }
    .labo-outer-container input[type="checkbox"] + label:before,
    .labo-outer-container input[type="checkbox"] + label:after,
    .labo-outer-container input[type="radio"] + label:before,
    .labo-outer-container input[type="radio"] + label:after {
        display: none !important;
    }

    .labo-radio-group {
        display: flex;
        align-items: center;
        gap: 18px;
    }
    .labo-choice {
        display: inline-flex !important;
        align-items: center !important;
        cursor: pointer !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #1e293b !important;
        margin: 0 !important;
        user-select: none;
    }

    .badge-normal {
        font-weight: 600;
        color: #0f172a;
    }
    .badge-urgent {
        font-weight: 700;
        color: #dc2626;
        background: #fee2e2;
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* Grille des 8 types d'échantillons */
    .labo-sample-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    @media (max-width: 768px) {
        .labo-sample-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    .labo-sample-item {
        display: inline-flex !important;
        align-items: center !important;
        padding: 5px 8px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        cursor: pointer !important;
        font-size: 12.5px !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        margin: 0 !important;
        transition: background 0.15s;
    }
    .labo-sample-item:hover {
        background: #f1f5f9;
    }

    /* 5. Tableau officiel 5 colonnes d'examens demandés */
    .labo-table-examens {
        width: 100%;
        min-width: 880px;
        border-collapse: collapse;
        table-layout: fixed;
        border: 2px solid #1e293b;
        background: #ffffff;
    }
    .labo-table-examens th {
        background: #eef2f6;
        border-right: 1.5px solid #1e293b;
        border-bottom: 2px solid #1e293b;
        padding: 9px 8px;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .labo-table-examens th:last-child {
        border-right: none;
    }
    .labo-td-col {
        border-right: 1.5px solid #1e293b;
        padding: 10px 8px;
        vertical-align: top;
        background: #ffffff;
    }
    .labo-td-col:last-child {
        border-right: none;
    }

    .labo-subgrid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }
    .labo-subcol {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .labo-item {
        display: inline-flex !important;
        align-items: flex-start !important;
        padding: 3px 5px;
        border-radius: 4px;
        cursor: pointer !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        color: #1e293b !important;
        margin: 0 !important;
        line-height: 1.3 !important;
        transition: background 0.15s ease-in-out;
    }
    .labo-item:hover {
        background: #e0f2fe;
        color: #0369a1 !important;
    }
    .labo-item input {
        margin-top: 2px !important;
    }

    .labo-counter-pill {
        background: #0284c7;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(2, 132, 199, 0.3);
    }

    /* 6. Pied de page & validation */
    .labo-footer-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 2px solid #1e293b;
        padding-top: 16px;
        margin-top: 10px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .labo-hospital-badge {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        padding: 6px 14px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .labo-hospital-logo {
        height: 38px;
        width: auto;
        max-width: 90px;
        object-fit: contain;
    }

    .btn-labo-recall {
        background: #0f172a;
        color: #ffffff;
        border: 1px solid #0f172a;
    }
    .btn-labo-recall:hover {
        background: #1e293b;
        color: #f8fafc;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .labo-sheet, .labo-sheet * {
            visibility: visible;
        }
        .labo-outer-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .labo-top-bar, .labo-footer-bar button, .labo-footer-bar a {
            display: none !important;
        }
    }

    /* Styles Issue de consultation & Affectation */
    .labo-issue-box {
        border: 2px solid #0284c7 !important;
        background: #ffffff;
    }
    .labo-issue-box .labo-box-title {
        background: #f0f9ff;
        border-bottom: 2px solid #0284c7;
    }
    .labo-issue-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }
    @media (max-width: 991px) {
        .labo-issue-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 576px) {
        .labo-issue-grid {
            grid-template-columns: 1fr;
        }
    }
    .labo-issue-card {
        border: 1.5px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px 14px;
        background: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease-in-out;
        position: relative;
        user-select: none;
    }
    .labo-issue-card:hover {
        border-color: #0284c7;
        box-shadow: 0 3px 10px rgba(2, 132, 199, 0.12);
        transform: translateY(-1px);
    }
    .labo-issue-card.active {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow: 0 3px 12px rgba(2, 132, 199, 0.18);
    }
    .labo-issue-card.active .issue-card-check i {
        font-weight: 900;
    }
    .issue-card-icon {
        font-size: 24px;
        flex-shrink: 0;
    }
    .issue-card-content {
        flex-grow: 1;
        min-width: 0;
    }
    .issue-card-title {
        font-size: 13.5px;
        font-weight: 700;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .issue-card-desc {
        font-size: 11px;
        color: #64748b;
        line-height: 1.2;
    }
    .issue-card-check {
        font-size: 16px;
        flex-shrink: 0;
    }
    .labo-subpanel {
        animation: fadeInSubpanel 0.25s ease-out;
    }
    @keyframes fadeInSubpanel {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .labo-select-custom {
        border: 1.5px solid #94a3b8;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        background-color: #ffffff;
    }
    .labo-select-custom:focus {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
    }
</style>

<script>
    // Données du personnel médical sérialisées pour filtrage instantané
    const infirmiersData = {!! $infirmiersJson !!};
    const doctorsData = {!! $doctorsJson !!};

    // Filtrage des infirmiers selon le service sélectionné
    function filterInfirmiersByService(serviceHospitalId) {
        const select = document.getElementById('select_affectation_infirmier');
        if (!select) return;
        
        select.innerHTML = '';
        
        if (!serviceHospitalId) {
            select.disabled = true;
            const opt = document.createElement('option');
            opt.value = '';
            opt.disabled = true;
            opt.selected = true;
            opt.textContent = "-- Sélectionnez d'abord un service à gauche --";
            select.appendChild(opt);
            return;
        }
        
        const filtered = infirmiersData.filter(i => String(i.service_hospital_id) === String(serviceHospitalId));
        
        if (filtered.length === 0) {
            select.disabled = true;
            const opt = document.createElement('option');
            opt.value = '';
            opt.disabled = true;
            opt.selected = true;
            opt.textContent = "Aucun(e) infirmier(ère) rattaché(e) à ce service";
            select.appendChild(opt);
        } else {
            select.disabled = false;
            const optDefault = document.createElement('option');
            optDefault.value = '';
            optDefault.disabled = true;
            optDefault.selected = true;
            optDefault.textContent = `-- Choisir un(e) infirmier(ère) (${filtered.length} disponible${filtered.length > 1 ? 's' : ''}) --`;
            select.appendChild(optDefault);
            
            filtered.forEach(inf => {
                const opt = document.createElement('option');
                opt.value = inf.id;
                opt.textContent = `${inf.name} (${inf.service_libelle})`;
                select.appendChild(opt);
            });
        }
    }

    // Filtrage des médecins selon la spécialité / service sélectionné
    function filterDoctorsByService(serviceHospitalId) {
        const select = document.getElementById('select_affectation_doctor');
        if (!select) return;
        
        select.innerHTML = '';
        
        if (!serviceHospitalId) {
            select.disabled = true;
            const opt = document.createElement('option');
            opt.value = '';
            opt.disabled = true;
            opt.selected = true;
            opt.textContent = "-- Sélectionnez d'abord un service à gauche --";
            select.appendChild(opt);
            return;
        }
        
        const filtered = doctorsData.filter(d => String(d.service_hospital_id) === String(serviceHospitalId));
        
        if (filtered.length === 0) {
            select.disabled = true;
            const opt = document.createElement('option');
            opt.value = '';
            opt.disabled = true;
            opt.selected = true;
            opt.textContent = "Aucun médecin rattaché à ce service";
            select.appendChild(opt);
        } else {
            select.disabled = false;
            const optDefault = document.createElement('option');
            optDefault.value = '';
            optDefault.disabled = true;
            optDefault.selected = true;
            optDefault.textContent = `-- Choisir un médecin (${filtered.length} disponible${filtered.length > 1 ? 's' : ''}) --`;
            select.appendChild(optDefault);
            
            filtered.forEach(doc => {
                const opt = document.createElement('option');
                opt.value = doc.id;
                opt.textContent = `${doc.name} — ${doc.service_libelle}`;
                select.appendChild(opt);
            });
        }
    }

    // Gestion du basculement d'issue de sortie
    function switchLaboIssue(mode) {
        // Mettre à jour les classes actives sur les cartes
        document.querySelectorAll('.labo-issue-card').forEach(card => {
            card.classList.remove('active');
            const checkIcon = card.querySelector('.issue-card-check i');
            if (checkIcon) {
                checkIcon.className = 'fa-regular fa-circle';
            }
        });

        const activeCard = document.getElementById('card-issue-' + (mode === 'affecter-infirmier' ? 'infirmier' : (mode === 'affecter-medecin' ? 'medecin' : mode)));
        if (activeCard) {
            activeCard.classList.add('active');
            const checkIcon = activeCard.querySelector('.issue-card-check i');
            if (checkIcon) {
                checkIcon.className = 'fa-solid fa-circle-check';
            }
        }

        // Mettre à jour le radio input
        const radio = document.getElementById('radio-issue-' + (mode === 'affecter-infirmier' ? 'infirmier' : (mode === 'affecter-medecin' ? 'medecin' : mode)));
        if (radio) {
            radio.checked = true;
        }

        // Masquer tous les sous-panneaux
        document.querySelectorAll('.labo-subpanel').forEach(panel => {
            panel.style.display = 'none';
        });

        // Afficher le sous-panneau correspondant
        const targetPanel = document.getElementById('subpanel-' + mode);
        if (targetPanel) {
            targetPanel.style.display = 'block';
        }

        // Gérer les attributs required
        const selectInfirmier = document.getElementById('select_affectation_infirmier');
        const selectDoctor = document.getElementById('select_affectation_doctor');

        if (selectInfirmier) selectInfirmier.required = (mode === 'affecter-infirmier');
        if (selectDoctor) selectDoctor.required = (mode === 'affecter-medecin');
    }

    // Remplissage rapide des instructions infirmières
    function setInfirmierInstruction(text) {
        const input = document.getElementById('input_instructions_infirmier');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    // Remplissage rapide des notes médecin
    function setMedecinNote(text) {
        const input = document.getElementById('input_note_medecin');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    // Mise à jour du mode hospitalisation / observation
    function updateHospMode(val) {
        const radio = document.getElementById('radio-issue-hospitalisation');
        if (radio) {
            radio.value = val;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Compteur d'examens
        const checkboxes = document.querySelectorAll('.exam-ch-input');
        const counter = document.getElementById('examCounter');

        function updateExamCount() {
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) total++;
            });
            if (counter) counter.textContent = total;
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateExamCount);
        });

        updateExamCount();

        // Validation du formulaire avant soumission
        const formLabo = document.getElementById('form-laboratoire');
        if (formLabo) {
            formLabo.addEventListener('submit', function(e) {
                const checkedRadio = document.querySelector('input[name="mode_sortie"]:checked');
                const mode = checkedRadio ? checkedRadio.value : 'sortie';

                if (mode === 'affecter-infirmier') {
                    const sServ = document.getElementById('select_service_infirmier');
                    const sel = document.getElementById('select_affectation_infirmier');
                    if (!sServ || !sServ.value) {
                        e.preventDefault();
                        alert("Veuillez d'abord sélectionner un service pour l'affectation à l'infirmerie.");
                        if (sServ) sServ.focus();
                        return false;
                    }
                    if (!sel || !sel.value) {
                        e.preventDefault();
                        alert("Veuillez sélectionner un(e) infirmier(ère) cible.");
                        if (sel) sel.focus();
                        return false;
                    }
                } else if (mode === 'affecter-medecin') {
                    const sServ = document.getElementById('select_service_doctor');
                    const sel = document.getElementById('select_affectation_doctor');
                    if (!sServ || !sServ.value) {
                        e.preventDefault();
                        alert("Veuillez d'abord sélectionner une spécialité / service médical.");
                        if (sServ) sServ.focus();
                        return false;
                    }
                    if (!sel || !sel.value) {
                        e.preventDefault();
                        alert("Veuillez sélectionner un médecin confrère cible.");
                        if (sel) sel.focus();
                        return false;
                    }
                }
            });
        }

        // Relance de l'appel patient en salle d'attente
        const recallBtn = document.querySelector('.btn-recall-in-consultation');
        if (recallBtn) {
            recallBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const btn = this;
                const consultationId = btn.getAttribute('data-id');
                const originalHtml = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Appel...';

                fetch("{{ url('doctor/consultation/call-patient') }}/" + consultationId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    if (data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Appel relancé !',
                                text: data.message || 'Le patient a été rappelé sur l\'écran de la salle d\'attente.',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                        } else if (typeof toastr !== 'undefined') {
                            toastr.success(data.message || 'Patient rappelé avec succès.');
                        } else {
                            alert(data.message || 'Patient rappelé avec succès.');
                        }
                    } else {
                        alert(data.message || 'Erreur lors de la relance de l\'appel.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    console.error('Erreur relance appel :', err);
                    alert('Impossible de relancer l\'appel. Veuillez réessayer.');
                });
            });
        }
    });
</script>

