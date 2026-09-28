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
</style>

<script>
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
