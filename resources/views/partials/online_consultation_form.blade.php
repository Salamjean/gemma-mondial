{{-- =====================================================================
     DOSSIER MÉDICAL & FORMULAIRE DE CONSULTATION EN LIGNE (SPLIT-SCREEN HD)
     Design moderne, intuitif et complet intégrant 100% des données médicales
     ===================================================================== --}}

<style>
/* ── Design System & Typographie de la Consultation ───────────────── */
.med-consult-page {
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #1e293b;
    background: #f8fafc;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Barre de navigation rapide / Onglets */
.med-quick-nav {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px 16px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-width: thin;
}
.med-quick-nav::-webkit-scrollbar {
    height: 4px;
}
.med-quick-nav::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.med-nav-pill {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    padding: 6px 12px;
    border-radius: 20px;
    background: #f1f5f9;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.med-nav-pill:hover {
    background: #e2e8f0;
    color: #0f172a;
}
.med-nav-pill.active {
    background: #0d9488;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(13, 148, 136, 0.25);
}

/* Boîte de section / Cartes médicales */
.med-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
    margin-bottom: 18px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.med-box:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}
.med-box-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.med-box-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.med-box-body {
    padding: 16px 18px;
}

/* Champs de formulaires */
.med-field-label {
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.med-input-text {
    width: 100%;
    height: 38px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 13px;
    color: #0f172a;
    background: #ffffff;
    transition: all 0.2s ease;
}
.med-input-text:focus {
    border-color: #0d9488;
    outline: none;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
}
.med-input-text[readonly], .med-input-text:disabled {
    background: #f8fafc;
    color: #334155;
    font-weight: 600;
    border-color: #e2e8f0;
}
.med-textarea {
    width: 100%;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    color: #0f172a;
    background: #ffffff;
    resize: vertical;
    transition: all 0.2s ease;
}
.med-textarea:focus {
    border-color: #0d9488;
    outline: none;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
}

/* Cartes Métriques Vitaux */
.med-metric-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: all 0.2s;
}
.med-metric-card:focus-within {
    background: #ffffff;
    border-color: #0d9488;
    box-shadow: 0 2px 8px rgba(13, 148, 136, 0.1);
}

/* Toggles Radio Modernes (Oui / Non / NA) */
.med-toggle-group {
    display: inline-flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}
.med-toggle-btn {
    margin: 0;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
    display: flex;
    align-items: center;
    gap: 4px;
    user-select: none;
}
.med-toggle-btn input[type="radio"] {
    display: none;
}
.med-toggle-btn:has(input[type="radio"]:checked) {
    background: #0d9488;
    color: #ffffff;
    box-shadow: 0 2px 4px rgba(13, 148, 136, 0.25);
}

/* Tuiles de sélection d'Issue de Consultation */
.med-issue-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 10px;
}
.med-issue-option {
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 10px 14px;
    cursor: pointer;
    transition: all 0.2s;
    background: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    user-select: none;
    margin: 0;
}
.med-issue-option:hover {
    border-color: #0d9488;
    background: #f0fdfa;
    transform: translateY(-1px);
}
.med-issue-option:has(input[type="radio"]:checked) {
    border-color: #059669;
    background: #ecfdf5;
    color: #047857;
    box-shadow: 0 3px 8px rgba(5, 150, 105, 0.18);
}
.med-issue-option input[type="radio"] {
    accent-color: #059669;
    transform: scale(1.15);
}

/* Badge IMC Dynamique */
.imc-badge {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 12px;
    font-weight: 700;
}
.imc-normal { background: #dcfce7; color: #15803d; }
.imc-warning { background: #fef3c7; color: #b45309; }
.imc-danger { background: #fee2e2; color: #b91c1c; }
</style>

<div id="doctorModalConsultationContainer" class="med-consult-page" style="overflow: hidden;">

    <!-- ── 1. BARRE DE TITRE & ACTIONS DU FORMULAIRE ───────────────────── -->
    <div class="px-3 py-2 bg-white border-bottom d-flex align-items-center justify-content-between flex-shrink-0" style="height: 52px; border-bottom: 1px solid #e2e8f0 !important;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge px-3 py-1.5 rounded-pill text-white fw-bold d-flex align-items-center fs-12" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%);">
                <i class="fa-solid fa-laptop-medical me-1.5"></i> Téléconsultation
            </span>
            <span class="fw-bold text-dark fs-14 d-none d-md-inline">Fiche de Consultation Curative</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a id="mdlBtnPatientDetail" href="#" target="_blank" class="btn btn-sm btn-light border text-teal rounded-pill px-3 fw-bold shadow-sm" title="Voir le profil complet du patient">
                <i class="fa-solid fa-user-circle me-1"></i> Fiche Patient
            </a>
            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-secondary shadow-sm" onclick="refreshDoctorModalConsultation()" title="Recharger les données">
                <i class="fa-solid fa-rotate-right me-1 text-teal"></i> Actualiser
            </button>
            <button type="button" class="btn btn-sm btn-light border text-secondary rounded-circle shadow-sm" onclick="toggleConsultationFormSplit()" title="Masquer le volet du dossier" style="width: 32px; height: 32px; padding: 0;">
                <i class="fa-solid fa-xmark fs-15"></i>
            </button>
        </div>
    </div>

    <!-- ── 2. BANDEAU DE NAVIGATION RAPIDE PAR ONGLETS (STICKY) ──────────── -->
    <div class="med-quick-nav flex-shrink-0">
        <a href="#secPatientHeader" class="med-nav-pill active"><i class="fa-solid fa-id-card"></i> Patient</a>
        <a href="#secConstantes" class="med-nav-pill"><i class="fa-solid fa-heart-pulse text-danger"></i> Constantes &amp; Motif</a>
        <a href="#secAntecedents" class="med-nav-pill"><i class="fa-solid fa-notes-medical text-primary"></i> Antécédents</a>
        <a href="#secExamenClinique" class="med-nav-pill"><i class="fa-solid fa-stethoscope text-teal"></i> Examen &amp; Diagnostic</a>
        <a href="#secExamensComp" class="med-nav-pill"><i class="fa-solid fa-vials text-warning"></i> Complémentaires &amp; Dépistages</a>
        <a href="#secIssueConsult" class="med-nav-pill"><i class="fa-solid fa-clipboard-check text-success"></i> Issue de consultation</a>
    </div>

    <!-- ── 3. ZONE PRINCIPALE DE DÉFILEMENT (OCCUPE 100% DE L'ESPACE) ───── -->
    <div class="flex-grow-1 p-3 p-md-4" style="overflow-y: auto; height: calc(100% - 94px);" id="mdlConsultScrollZone">

        <form id="form-modal-consultation" method="POST" action="{{ route('doctor.consultation.store.curative') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="consultation_id" id="mdlConsultationId" value="">
            <input type="hidden" name="patient_id" id="mdlPatientId" value="">

            {{-- ========================================================== --}}
            {{-- A. EN-TÊTE DU DOSSIER PATIENT                              --}}
            {{-- ========================================================== --}}
            <div id="secPatientHeader" class="med-box border-top border-4" style="border-top-color: #0d9488 !important;">
                <div class="med-box-body">
                    <div class="row align-items-center g-3">
                        
                        <!-- Photo / Avatar du patient -->
                        <div class="col-auto text-center">
                            <div class="position-relative d-inline-block">
                                <img id="mdlPatientAvatar" src="{{ asset('assets/images/avatar/6.png') }}" 
                                     class="rounded-circle shadow-sm" alt="Photo patient" 
                                     style="width: 88px; height: 88px; object-fit: cover; border: 3px solid #0d9488; background: #fff;" />
                            </div>
                        </div>

                        <!-- Nom complet, code dossier, ordre et badges -->
                        <div class="col">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h3 class="fw-bold text-dark mb-0 fs-18" id="mdlNomCompletHeader">Patient</h3>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fs-11">
                                            <i class="fa-solid fa-circle me-1 fs-9"></i> En consultation
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1.5 flex-wrap">
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-bold fs-12">
                                            N° Dossier : <span id="mdlCodePatient">DM--------</span>
                                        </span>
                                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fs-12">
                                            <i class="fa-solid fa-venus-mars me-1 text-teal"></i> <span id="mdlGenderBadge">--</span>
                                        </span>
                                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fs-12">
                                            <i class="fa-solid fa-cake-candles me-1 text-teal"></i> <span id="mdlAgeBadge">-- ans</span>
                                        </span>
                                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fs-12" id="mdlBadgeInfirmier" title="Infirmier référent">
                                            <i class="fa-solid fa-user-nurse me-1 text-success"></i> Infirmier : <span id="mdlInfirmierName" class="fw-bold">Non renseigné</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <span class="badge px-3 py-1.5 rounded-pill fw-bold fs-12" style="background: #ccfbf1; color: #0f766e; border: 1px solid #99f6e4;">
                                        <i class="fa-solid fa-calendar-check me-1"></i> Consultation du <span id="mdlDateConsult">{{ now()->format('d/m/Y') }}</span>
                                    </span>
                                    <div class="text-muted fs-12 mt-1 font-monospace">
                                        Ordre N° <span id="mdlOrdreNo" class="fw-bold text-dark">01</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Grille des coordonnées détaillées du patient -->
                            <div class="row g-2 pt-2 border-top">
                                <div class="col-lg-3 col-sm-6">
                                    <span class="med-field-label"><i class="fa-regular fa-calendar text-teal"></i> Né(e) le</span>
                                    <input type="text" class="med-input-text" id="mdlBirthDate" disabled value="--/--/----">
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <span class="med-field-label"><i class="fa-solid fa-location-dot text-teal"></i> Résidence Actuelle</span>
                                    <input type="text" class="med-input-text" id="mdlResidence" readonly value="--">
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <span class="med-field-label"><i class="fa-solid fa-briefcase text-teal"></i> Profession</span>
                                    <input type="text" class="med-input-text" id="mdlProfession" readonly value="--">
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <span class="med-field-label"><i class="fa-solid fa-phone text-teal"></i> Contact</span>
                                    <input type="text" class="med-input-text" id="mdlPhone" readonly value="--">
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <span class="med-field-label"><i class="fa-solid fa-shield-halved text-teal"></i> N° Assurance</span>
                                    <input type="text" class="med-input-text" id="mdlAssurance" readonly value="--">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- B. CONSTANTES PHYSIQUES & PARAMÈTRES VITAUX               --}}
            {{-- ========================================================== --}}
            <div id="secConstantes" class="med-box">
                <div class="med-box-header">
                    <h5 class="med-box-title">
                        <i class="fa-solid fa-heart-pulse text-danger fs-15"></i> Constantes physiques &amp; Paramètres vitaux
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span id="mdlImcStatusBadge" class="imc-badge imc-normal" style="display: none;">IMC Normal</span>
                        <small class="text-muted fs-12">Calcul d'IMC automatique</small>
                    </div>
                </div>
                <div class="med-box-body">
                    <div class="row g-2.5">
                        
                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-weight-scale text-primary"></i> Poids (kg)</label>
                                <input type="number" step="0.1" class="med-input-text" id="mdlPoids" name="poids" placeholder="Ex: 70">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-ruler-vertical text-info"></i> Taille (m/cm)</label>
                                <input type="text" class="med-input-text" id="mdlTaille" name="taille" placeholder="Ex: 1.75 ou 175">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-calculator text-success"></i> IMC (kg/m²)</label>
                                <input type="text" class="med-input-text fw-bold bg-light" id="mdlImc" name="imc" placeholder="--" readonly>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-temperature-half text-danger"></i> Température (°C)</label>
                                <input type="text" class="med-input-text" id="mdlTemp" name="temperature" placeholder="Ex: 37.2">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-gauge-high text-warning"></i> TA (mmHg)</label>
                                <input type="text" class="med-input-text" id="mdlTA" name="ta" placeholder="Ex: 120/80">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-wave-square text-danger"></i> Pouls (batt/mn)</label>
                                <input type="text" class="med-input-text" id="mdlPouls" name="pouls" placeholder="Ex: 75">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-lungs text-teal"></i> Saturation O₂ (%)</label>
                                <input type="text" class="med-input-text" id="mdlSatO2" name="saturation_oxygene" placeholder="Ex: 98%">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-wind text-secondary"></i> Fréq. respiratoire</label>
                                <input type="text" class="med-input-text" id="mdlFreqResp" name="frequence_respiratoire" placeholder="Cycles/mn">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-child text-primary"></i> Périm. brachial (cm)</label>
                                <input type="text" class="med-input-text" id="mdlPeriBrach" name="perimetre_brachial" placeholder="cm">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-circle-notch text-info"></i> Périm. crânien (cm)</label>
                                <input type="text" class="med-input-text" id="mdlPeriCran" name="perimetre_cranien" placeholder="cm">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-chart-line text-purple"></i> Z-Score</label>
                                <input type="text" class="med-input-text" id="mdlZscore" name="zscore" placeholder="Z-score">
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                            <div class="med-metric-card">
                                <label class="med-field-label"><i class="fa-solid fa-droplet text-danger"></i> Glycémie (à jeûn)</label>
                                <input type="text" class="med-input-text" id="mdlGlycJeun" name="glycemie_a_jeun" placeholder="g/l">
                            </div>
                        </div>

                    </div>

                    <!-- Motif de consultation (Requis) -->
                    <div class="mt-3 pt-3 border-top">
                        <label class="med-field-label fs-13 text-dark">
                            <i class="fa-solid fa-comment-medical text-teal fs-14"></i> Motif de la consultation | Description du mal <span class="text-danger fw-bold">*</span>
                        </label>
                        <textarea class="med-textarea" name="motif_consultation" id="mdlMotif" rows="3" placeholder="Saisissez la plainte principale, l'histoire de la maladie et les symptômes décrits par le patient..." required></textarea>
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- C. ANTÉCÉDENTS MÉDICAUX, CHIRURGICAUX & GYNÉCOLOGIQUES     --}}
            {{-- ========================================================== --}}
            <div id="secAntecedents" class="med-box">
                <div class="med-box-header">
                    <h5 class="med-box-title">
                        <i class="fa-solid fa-notes-medical text-primary fs-15"></i> Antécédents médicaux, chirurgicaux &amp; mode de vie
                    </h5>
                </div>
                <div class="med-box-body">
                    
                    <!-- Antécédents Médicaux Principaux -->
                    <h6 class="fw-bold text-secondary fs-12 text-uppercase mb-2">1. Antécédents Médicaux &amp; Facteurs de risque</h6>
                    <div class="row g-2.5">
                        
                        <div class="col-lg-4 col-sm-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="fw-bold fs-13"><i class="fa-solid fa-heart-crack text-danger me-1"></i> HTA :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="hta" id="mdlHtaOui" value="Oui HTA"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="hta" id="mdlHtaNon" value="Non HTA" checked><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="fw-bold fs-13"><i class="fa-solid fa-cubes-stacked text-warning me-1"></i> Diabète :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="diabete" id="mdlDiabeteOui" value="Oui Diabétique"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="diabete" id="mdlDiabeteNon" value="Non Diabétique" checked><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="fw-bold fs-13"><i class="fa-solid fa-smoking text-secondary me-1"></i> Tabac :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="tabac" id="mdlTabacOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="tabac" id="mdlTabacNon" value="Non" checked><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="fw-bold fs-13"><i class="fa-solid fa-wine-glass text-purple me-1"></i> Alcool :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="alcool" id="mdlAlcoolOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="alcool" id="mdlAlcoolNon" value="Non" checked><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="fw-bold fs-13"><i class="fa-solid fa-capsules text-info me-1"></i> UGD :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="UGD" id="mdlUgdOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="UGD" id="mdlUgdNon" value="Non" checked><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="fw-bold fs-13"><i class="fa-solid fa-dna text-danger me-1"></i> Drépanocytaire :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="drepanocytaire" id="mdlDrepanoOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="drepanocytaire" id="mdlDrepanoNon" value="Non" checked><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Traitements antérieurs et Autres antécédents médicaux -->
                    <div class="row g-3 mt-2 pt-2 border-top">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-prescription-bottle-medical text-teal me-1"></i> Traitement médicamenteux antérieur/en cours :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="traitement_medicamenteux" value="Oui" onchange="toggleMdlSection('mdlBlocTraitement', this.value==='Oui')"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="traitement_medicamenteux" value="Non" checked onchange="toggleMdlSection('mdlBlocTraitement', this.value==='Oui')"><span>Non</span></label>
                                </div>
                            </div>
                            <div id="mdlBlocTraitement" style="display:none;" class="mt-2">
                                <textarea class="med-textarea" name="traitement_medicamenteux_anterieur" id="mdlTraitementMed" rows="2" placeholder="Précisez les molécules et posologies en cours..."></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-file-waveform text-teal me-1"></i> Autres antécédents médicaux :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="antecedent_medical" value="Oui" onchange="toggleMdlSection('mdlBlocAutreAntecedent', this.value==='Oui')"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="antecedent_medical" value="Non" checked onchange="toggleMdlSection('mdlBlocAutreAntecedent', this.value==='Oui')"><span>Non</span></label>
                                </div>
                            </div>
                            <div id="mdlBlocAutreAntecedent" style="display:none;" class="mt-2">
                                <textarea class="med-textarea" name="autre_antecedent_medical" id="mdlAutreAntecedent" rows="2" placeholder="Asthme, allergies, pathologies chroniques..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Antécédents Chirurgicaux (Liste Complète) -->
                    <h6 class="fw-bold text-secondary fs-12 text-uppercase mt-4 mb-2">2. Antécédents Chirurgicaux</h6>
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-3 col-sm-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="fw-bold fs-13"><i class="fa-solid fa-scissors text-info me-1"></i> Opération ?</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="antecedent_chirurgical" id="mdlChirOui" value="Oui" onchange="toggleMdlSection('mdlBlocNomOperation', this.value==='Oui')"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="antecedent_chirurgical" id="mdlChirNon" value="Non" checked onchange="toggleMdlSection('mdlBlocNomOperation', this.value==='Oui')"><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5 col-sm-6" id="mdlBlocNomOperation" style="display:none;">
                            <label class="med-field-label">Préciser l'opération chirurgicale :</label>
                            <select class="med-input-text" id="nom_operation" name="nom_operation">
                                <option value="" selected>--- Sélectionner l'intervention ---</option>
                                <optgroup label="Chirurgie générale">
                                    <option value="Appendicectomie">Appendicectomie</option>
                                    <option value="Cholécystectomie (ablation de la vésicule biliaire)">Cholécystectomie (ablation de la vésicule biliaire)</option>
                                    <option value="Hernie réparatrice">Hernie réparatrice</option>
                                    <option value="Colectomie (ablation d'une partie ou de la totalité du côlon)">Colectomie (ablation d'une partie ou de la totalité du côlon)</option>
                                </optgroup>
                                <optgroup label="Chirurgie cardiothoracique">
                                    <option value="Pontage coronarien">Pontage coronarien</option>
                                    <option value="Remplacement de valve cardiaque">Remplacement de valve cardiaque</option>
                                    <option value="Réparation d'anévrisme aortique">Réparation d'anévrisme aortique</option>
                                    <option value="Lobectomie pulmonaire">Lobectomie pulmonaire</option>
                                </optgroup>
                                <optgroup label="Chirurgie orthopédique">
                                    <option value="Arthroplastie">Arthroplastie</option>
                                    <option value="Réparation de fracture">Réparation de fracture</option>
                                    <option value="Fusion vertébrale">Fusion vertébrale</option>
                                    <option value="Chirurgie du ligament croisé antérieur (LCA)">Chirurgie du ligament croisé antérieur (LCA)</option>
                                </optgroup>
                                <optgroup label="Chirurgie gynécologique">
                                    <option value="Hystérectomie">Hystérectomie</option>
                                    <option value="Ovariectomie (ablation des ovaires)">Ovariectomie (ablation des ovaires)</option>
                                    <option value="Réparation de prolapsus pelvien">Réparation de prolapsus pelvien</option>
                                    <option value="Stérilisation tubaire">Stérilisation tubaire</option>
                                    <option value="Césarienne">Césarienne</option>
                                </optgroup>
                                <optgroup label="Chirurgie urologique">
                                    <option value="Prostatectomie">Prostatectomie</option>
                                    <option value="Néphrectomie (ablation d'un rein)">Néphrectomie (ablation d'un rein)</option>
                                    <option value="Lithotomie (enlèvement de calculs rénaux ou vésicaux)">Lithotomie (enlèvement de calculs rénaux ou vésicaux)</option>
                                    <option value="Cystectomie (ablation de la vessie)">Cystectomie (ablation de la vessie)</option>
                                </optgroup>
                                <optgroup label="Chirurgie plastique et reconstructive">
                                    <option value="Augmentation mammaire">Augmentation mammaire</option>
                                    <option value="Rhinoplastie">Rhinoplastie</option>
                                    <option value="Liposuccion">Liposuccion</option>
                                    <option value="Reconstruction mammaire">Reconstruction mammaire</option>
                                </optgroup>
                                <optgroup label="Chirurgie neurologique">
                                    <option value="Craniotomie (ouverture du crâne)">Craniotomie (ouverture du crâne)</option>
                                    <option value="Décompression du nerf">Décompression du nerf</option>
                                    <option value="Résection de tumeur cérébrale">Résection de tumeur cérébrale</option>
                                    <option value="Chirurgie de la colonne vertébrale">Chirurgie de la colonne vertébrale</option>
                                </optgroup>
                                <optgroup label="Chirurgie ophtalmologique">
                                    <option value="Chirurgie de la cataracte">Chirurgie de la cataracte</option>
                                    <option value="Correction de la vision au laser (LASIK)">Correction de la vision au laser (LASIK)</option>
                                    <option value="Greffe de cornée">Greffe de cornée</option>
                                    <option value="Réparation de décollement de rétine">Réparation de décollement de rétine</option>
                                </optgroup>
                                <optgroup label="Chirurgie ORL">
                                    <option value="Tympanoplastie (réparation du tympan)">Tympanoplastie (réparation du tympan)</option>
                                    <option value="Adénoïdectomie">Adénoïdectomie</option>
                                    <option value="Amygdalectomie">Amygdalectomie</option>
                                    <option value="Septoplastie (correction de la cloison nasale)">Septoplastie (correction de la cloison nasale)</option>
                                </optgroup>
                                <optgroup label="Chirurgie gastro-intestinale">
                                    <option value="Gastrostomie (pose d'une sonde d'alimentation)">Gastrostomie (pose d'une sonde d'alimentation)</option>
                                    <option value="Résection intestinale">Résection intestinale</option>
                                    <option value="Proctectomie (ablation du rectum)">Proctectomie (ablation du rectum)</option>
                                    <option value="Chirurgie bariatrique (pour la perte de poids)">Chirurgie bariatrique (pour la perte de poids)</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <label class="med-field-label">Autre antécédent chirurgical :</label>
                            <input type="text" class="med-input-text" id="mdlAutreChirurgical" name="autre_antecedent_chirurgical" placeholder="Autre opération effectuée...">
                        </div>
                    </div>

                    <!-- 3. Antécédents Gynéco-obstétriques (Femmes) -->
                    <div id="mdlCardGyneco" style="display:none;" class="mt-3 pt-3 border-top">
                        <h6 class="fw-bold fs-12 text-uppercase mb-2" style="color: #db2777;">
                            <i class="fa-solid fa-person-breastfeeding me-1"></i> 3. Antécédents Gynéco-obstétriques
                        </h6>
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-pink-subtle border border-pink-subtle" style="background: #fdf2f8;">
                                    <span class="fw-bold fs-13 text-dark">Grossesse en cours :</span>
                                    <div class="med-toggle-group">
                                        <label class="med-toggle-btn"><input type="radio" name="en_cours_de_grossesse" id="mdlGrossesseOui" value="Oui" onchange="toggleMdlSection('mdlBlocGrossesse', this.value==='Oui')"><span>Oui</span></label>
                                        <label class="med-toggle-btn"><input type="radio" name="en_cours_de_grossesse" id="mdlGrossesseNon" value="Non" checked onchange="toggleMdlSection('mdlBlocGrossesse', this.value==='Oui')"><span>Non</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="med-field-label">DDR (Dernières Règles) :</label>
                                <input type="text" class="med-input-text" name="ddr" id="mdlDDR" placeholder="JJ/MM/AAAA">
                            </div>
                            <div class="col-md-5" id="mdlBlocGrossesse" style="display:none;">
                                <label class="med-field-label">Description de la grossesse :</label>
                                <input type="text" class="med-input-text" name="description_grossesse" id="mdlDescGrossesse" placeholder="Terme, évolution, suivi CPN...">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- D. EXAMEN CLINIQUE & DIAGNOSTIC MÉDICAL                    --}}
            {{-- ========================================================== --}}
            <div id="secExamenClinique" class="med-box">
                <div class="med-box-header">
                    <h5 class="med-box-title">
                        <i class="fa-solid fa-stethoscope text-teal fs-15" style="color:#0d9488;"></i> Examen clinique &amp; Diagnostic
                    </h5>
                </div>
                <div class="med-box-body">
                    
                    <!-- Recherche Active Tuberculose & Autre examen clinique -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-lungs-virus text-warning me-1"></i> Recherche active de la tuberculose :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="tuberculose" id="mdlTbOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="tuberculose" id="mdlTbNon" value="Non" checked><span>Non</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="tuberculose" id="mdlTbNa" value="NA"><span>NA</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-microscope text-teal me-1"></i> Autre(s) examens cliniques ? :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="autre_examen" value="Oui" onchange="toggleMdlSection('mdlInputAutreExam', this.value==='Oui')"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="autre_examen" value="Non" checked onchange="toggleMdlSection('mdlInputAutreExam', this.value==='Oui')"><span>Non</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="mdlInputAutreExam" style="display:none;">
                            <label class="med-field-label">Préciser les autres examens cliniques :</label>
                            <textarea class="med-textarea" name="autre_examen_clinique" id="mdlAutreExamClinique" rows="2" placeholder="Détail des autres examens cliniques..."></textarea>
                        </div>
                    </div>

                    <!-- Examen physique, Diagnostic retenu et Pathologies associées -->
                    <div class="row g-3 pt-2 border-top">
                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-hand-holding-medical text-teal"></i> Examen physique ?</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="examen_physique_q" value="Oui" onchange="toggleMdlSection('mdlBlocExamPhysique', this.value==='Oui')"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="examen_physique_q" value="Non" checked onchange="toggleMdlSection('mdlBlocExamPhysique', this.value==='Oui')"><span>Non</span></label>
                                </div>
                            </div>
                            <div id="mdlBlocExamPhysique" style="display:none;">
                                <textarea class="med-textarea" name="examen_physique" id="mdlExamPhysique" rows="4" placeholder="Constatations de l'examen physique (auscultation, palpation, inspection)..."></textarea>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-clipboard-check text-success"></i> Diagnostic retenu ?</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="diagnostic_retenu_q" value="Oui" onchange="toggleMdlSection('mdlBlocDiagnostic', this.value==='Oui')"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="diagnostic_retenu_q" value="Non" checked onchange="toggleMdlSection('mdlBlocDiagnostic', this.value==='Oui')"><span>Non</span></label>
                                </div>
                            </div>
                            <div id="mdlBlocDiagnostic" style="display:none;">
                                <textarea class="med-textarea" name="diagnostic_retenu" id="mdlDiagnosticRetenu" rows="4" placeholder="Diagnostic médical principal retenu..."></textarea>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-virus text-warning"></i> Autres pathologies associées ?</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="pathologie_associee_q" value="Oui" onchange="toggleMdlSection('mdlBlocPathologie', this.value==='Oui')"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="pathologie_associee_q" value="Non" checked onchange="toggleMdlSection('mdlBlocPathologie', this.value==='Oui')"><span>Non</span></label>
                                </div>
                            </div>
                            <div id="mdlBlocPathologie" style="display:none;">
                                <textarea class="med-textarea" name="autre_pathologie_associee" id="mdlPathologieAssociee" rows="4" placeholder="Affections secondaires, comorbidités..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- E. EXAMENS COMPLÉMENTAIRES & DÉPISTAGE                     --}}
            {{-- ========================================================== --}}
            <div id="secExamensComp" class="med-box">
                <div class="med-box-header">
                    <h5 class="med-box-title">
                        <i class="fa-solid fa-vials text-warning fs-15" style="color:#f59e0b;"></i> Examens complémentaires &amp; Dépistages
                    </h5>
                </div>
                <div class="med-box-body">
                    <div class="row g-3">
                        
                        <!-- TDR Paludisme -->
                        <div class="col-md-6">
                            <div class="p-2.5 rounded bg-light border">
                                <label class="med-field-label"><i class="fa-solid fa-mosquito text-danger me-1"></i> TDR Paludisme :</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="tdr_paludisme" id="mdlTdrPositif" value="Positif"> Positif</label>
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="tdr_paludisme" id="mdlTdrNegatif" value="Negatif"> Négatif</label>
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="tdr_paludisme" id="mdlTdrNonRealise" value="nonrealise" checked> Non réalisé</label>
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="tdr_paludisme" id="mdlTdrNa" value="TDRna"> NA</label>
                                </div>
                            </div>
                        </div>

                        <!-- Goutte Épaisse -->
                        <div class="col-md-6">
                            <div class="p-2.5 rounded bg-light border">
                                <label class="med-field-label"><i class="fa-solid fa-droplet text-danger me-1"></i> Goutte Épaisse :</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="goutte_epaise" id="mdlGePositive" value="Positive"> Positive</label>
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="goutte_epaise" id="mdlGeNegative" value="Negative"> Négative</label>
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="goutte_epaise" id="mdlGeNonRealise" value="non realise" checked> Non réalisé</label>
                                    <label class="med-issue-option py-1 px-3"><input type="radio" name="goutte_epaise" id="mdlGeNa" value="NA"> NA</label>
                                </div>
                            </div>
                        </div>

                        <!-- MILDA Enfant 12 - 59 mois Eligible -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-bed text-teal me-1"></i> MILDA Enfant (12 à 59 mois) Éligible : <span class="text-danger">*</span></span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="milda_enfant_eligible" id="mdlMildaEligibleOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="milda_enfant_eligible" id="mdlMildaEligibleNon" value="Non"><span>Non</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="milda_enfant_eligible" id="mdlMildaEligibleNa" value="Na" checked><span>NA</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Remise MILDA -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-gift text-teal me-1"></i> Remise MILDA Enfant (12 à 59 mois) :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="remise_milda_enfant" id="mdlRemiseMildaOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="remise_milda_enfant" id="mdlRemiseMildaNon" value="Non"><span>Non</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="remise_milda_enfant" id="mdlRemiseMildaNa" value="Na" checked><span>NA</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- CDIP Proposé -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-shield-virus text-info me-1"></i> CDIP proposé :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="cdip_propose" id="mdlCdipProposeOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="cdip_propose" id="mdlCdipProposeNon" value="Non"><span>Non</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="cdip_propose" id="mdlCdipProposeNa" value="Na" checked><span>NA</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- CDIP Réalisé -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                                <span class="med-field-label mb-0"><i class="fa-solid fa-check-double text-info me-1"></i> CDIP réalisé :</span>
                                <div class="med-toggle-group">
                                    <label class="med-toggle-btn"><input type="radio" name="cdip_realise" id="mdlCdipRealiseOui" value="Oui"><span>Oui</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="cdip_realise" id="mdlCdipRealiseNon" value="Non"><span>Non</span></label>
                                    <label class="med-toggle-btn"><input type="radio" name="cdip_realise" id="mdlCdipRealiseNa" value="Na" checked><span>NA</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Code dépistage client & Glycémies complémentaires -->
                        <div class="col-md-6">
                            <label class="med-field-label"><i class="fa-solid fa-barcode text-secondary me-1"></i> Code dépistage client :</label>
                            <input type="text" class="med-input-text" name="code_depistage_client" id="mdlCodeDepistage" placeholder="Ex: DP-0000">
                        </div>

                        <div class="col-md-6">
                            <label class="med-field-label"><i class="fa-solid fa-chart-pie text-secondary me-1"></i> Glycémies de contrôle (g/l) :</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="text" class="med-input-text" name="glycemie_non_a_jeun" id="mdlGlycNonJeun" placeholder="Non à jeûn (g/l)">
                                <label class="med-issue-option py-1 px-3 text-nowrap"><input type="radio" name="naglycemeie" value="NA"> Glycémie NA</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- F. ISSUE DE LA CONSULTATION (OBLIGATOIRE)                  --}}
            {{-- ========================================================== --}}
            <div id="secIssueConsult" class="med-box border-2" style="border-color: #059669 !important;">
                <div class="med-box-header text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <h5 class="med-box-title text-white">
                        <i class="fa-solid fa-clipboard-check"></i> Issue de la consultation <span class="text-warning fw-bold">*</span>
                    </h5>
                    <span class="badge bg-white text-success fs-12 px-2.5 py-1 fw-bold shadow-sm">Choix décisionnel obligatoire</span>
                </div>
                <div class="med-box-body">
                    <div class="med-issue-grid">
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="sortie" required>
                            <span><i class="fa-solid fa-door-open text-success fs-15"></i> Sortie</span>
                        </label>
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="hospitalisation">
                            <span><i class="fa-solid fa-bed text-primary fs-15"></i> Hospitalisé(e)</span>
                        </label>
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="observation">
                            <span><i class="fa-solid fa-eye text-warning fs-15"></i> Mise en observation</span>
                        </label>
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="refere-interne">
                            <span><i class="fa-solid fa-hospital-user text-info fs-15"></i> Référé(e) en interne</span>
                        </label>
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="refere-externe">
                            <span><i class="fa-solid fa-ambulance text-danger fs-15"></i> Référé(e) en externe</span>
                        </label>
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="cas-presume-tb-resume">
                            <span><i class="fa-solid fa-lungs text-warning fs-15"></i> Cas présumé TB référé</span>
                        </label>
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="a-revoir">
                            <span><i class="fa-solid fa-calendar-plus text-teal fs-15"></i> À revoir</span>
                        </label>
                        <label class="med-issue-option">
                            <input type="radio" name="mode_sortie" value="declaration-deces-patient">
                            <span><i class="fa-solid fa-skull text-dark fs-15"></i> Décédé(e)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ── G. BARRE DE VALIDATION & ENREGISTREMENT FLOTTANTE ───────── --}}
            <div class="p-3 bg-white border rounded-3 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4 sticky-bottom" style="bottom: 10px; z-index: 10;">
                <div class="d-flex align-items-center gap-2">
                    <span id="mdlSaveStatusMsg" class="fs-13 fw-bold" style="display:none;"></span>
                </div>
                <div class="d-flex gap-2 ms-auto">
                    <button type="button" class="btn btn-light border rounded-pill px-3 fw-bold text-secondary shadow-sm" onclick="refreshDoctorModalConsultation()">
                        <i class="fa-solid fa-rotate-left me-1"></i> Réinitialiser
                    </button>
                    <button type="button" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow d-flex align-items-center fs-14" 
                            id="btnSubmitModalConsultation" onclick="submitDoctorModalConsultation()"
                            style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;">
                        <i class="fa-solid fa-floppy-disk me-2 fs-16"></i> Enregistrer la consultation
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

<script>
    // Basculer l'affichage conditionnel de sous-sections
    function toggleMdlSection(elementId, show) {
        const el = document.getElementById(elementId);
        if (el) {
            el.style.display = show ? 'block' : 'none';
        }
    }

    // Calcul automatique IMC dans le modal avec interprétation visuelle
    (function initMdlImc() {
        const pInput = document.getElementById('mdlPoids');
        const tInput = document.getElementById('mdlTaille');
        const iInput = document.getElementById('mdlImc');
        const badge = document.getElementById('mdlImcStatusBadge');

        function updateImc() {
            if (!pInput || !tInput || !iInput) return;
            const p = parseFloat(pInput.value);
            let t = parseFloat(tInput.value);

            if (isNaN(p) || isNaN(t) || t <= 0) {
                iInput.value = '';
                if (badge) badge.style.display = 'none';
                return;
            }

            // Si taille entrée en cm (ex: 175) -> convertir en mètres (1.75)
            if (t > 3) {
                t = t / 100.0;
            }

            const imc = p / (t * t);
            iInput.value = imc.toFixed(2);

            if (badge) {
                badge.style.display = 'inline-block';
                if (imc < 18.5) {
                    badge.className = 'imc-badge imc-warning';
                    badge.textContent = 'Insuffisance pondérale (' + imc.toFixed(1) + ')';
                } else if (imc < 25) {
                    badge.className = 'imc-badge imc-normal';
                    badge.textContent = 'Poids normal (' + imc.toFixed(1) + ')';
                } else if (imc < 30) {
                    badge.className = 'imc-badge imc-warning';
                    badge.textContent = 'Surpoids (' + imc.toFixed(1) + ')';
                } else {
                    badge.className = 'imc-badge imc-danger';
                    badge.textContent = 'Obésité (' + imc.toFixed(1) + ')';
                }
            }
        }

        if (pInput) pInput.addEventListener('input', updateImc);
        if (tInput) tInput.addEventListener('input', updateImc);
    })();

    // Défilement fluide vers les sections depuis la barre d'onglets
    document.querySelectorAll('.med-nav-pill').forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.med-nav-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const targetId = this.getAttribute('href');
            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Helper pour cocher un radio par nom et valeur
    function setRadioChecked(name, value) {
        if (!value) return;
        const radio = document.querySelector(`input[name="${name}"][value="${value}"]`);
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
        }
    }

    // Charger les données du patient en cours dans le modal
    function loadDoctorModalConsultation(consultationId) {
        if (!consultationId) return;

        const consultInput = document.getElementById('mdlConsultationId');
        if (consultInput) consultInput.value = consultationId;

        fetch(`/doctor/consultation/online/patient-info/${consultationId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    console.warn("Erreur chargement consultation:", data.error);
                    return;
                }

                // Données d'identité
                if (document.getElementById('mdlPatientId')) document.getElementById('mdlPatientId').value = data.patient_id || '';
                if (document.getElementById('mdlCodePatient')) document.getElementById('mdlCodePatient').textContent = data.code || 'DM--------';
                if (document.getElementById('mdlNomCompletHeader')) document.getElementById('mdlNomCompletHeader').textContent = data.name || 'Patient';
                if (document.getElementById('mdlGenderBadge')) document.getElementById('mdlGenderBadge').textContent = data.gender || '--';
                if (document.getElementById('mdlAgeBadge')) document.getElementById('mdlAgeBadge').textContent = data.age || '-- ans';
                if (document.getElementById('mdlInfirmierName')) document.getElementById('mdlInfirmierName').textContent = data.infirmier || 'Non renseigné';

                if (document.getElementById('mdlBirthDate')) document.getElementById('mdlBirthDate').value = data.birth_date || '';
                if (document.getElementById('mdlResidence')) document.getElementById('mdlResidence').value = data.residence || 'Non renseigné';
                if (document.getElementById('mdlProfession')) document.getElementById('mdlProfession').value = data.profession || 'Non renseignée';
                if (document.getElementById('mdlPhone')) document.getElementById('mdlPhone').value = data.phone || 'Non renseigné';
                if (document.getElementById('mdlAssurance')) document.getElementById('mdlAssurance').value = data.assurance || 'Non renseigné';

                if (document.getElementById('mdlDateConsult')) document.getElementById('mdlDateConsult').textContent = data.date_du_jour || '';
                if (document.getElementById('mdlOrdreNo')) document.getElementById('mdlOrdreNo').textContent = data.ordre_no || '01';

                // Lien fiche patient
                const btnPatient = document.getElementById('mdlBtnPatientDetail');
                if (btnPatient && data.patient_detail_url) {
                    btnPatient.href = data.patient_detail_url;
                }

                // Avatar
                const avatarImg = document.getElementById('mdlPatientAvatar');
                if (avatarImg && data.avatar) avatarImg.src = data.avatar;

                // Constantes physiques
                if (document.getElementById('mdlPoids')) document.getElementById('mdlPoids').value = data.poids || '';
                if (document.getElementById('mdlTaille')) document.getElementById('mdlTaille').value = data.taille || '';
                if (document.getElementById('mdlImc')) document.getElementById('mdlImc').value = data.imc || '';
                if (document.getElementById('mdlTemp')) document.getElementById('mdlTemp').value = data.temperature || '';
                if (document.getElementById('mdlTA')) document.getElementById('mdlTA').value = data.ta || '';
                if (document.getElementById('mdlPouls')) document.getElementById('mdlPouls').value = data.pouls || '';
                if (document.getElementById('mdlSatO2')) document.getElementById('mdlSatO2').value = data.saturation_oxygene || '';
                if (document.getElementById('mdlFreqResp')) document.getElementById('mdlFreqResp').value = data.frequence_respiratoire || '';
                if (document.getElementById('mdlPeriBrach')) document.getElementById('mdlPeriBrach').value = data.perimetre_brachial || '';
                if (document.getElementById('mdlPeriCran')) document.getElementById('mdlPeriCran').value = data.perimetre_cranien || '';
                if (document.getElementById('mdlZscore')) document.getElementById('mdlZscore').value = data.zscore || '';
                if (document.getElementById('mdlGlycJeun')) document.getElementById('mdlGlycJeun').value = data.glycemie_a_jeun || '';
                if (document.getElementById('mdlGlycNonJeun')) document.getElementById('mdlGlycNonJeun').value = data.glycemie_non_a_jeun || '';

                // Motif de consultation
                if (document.getElementById('mdlMotif')) document.getElementById('mdlMotif').value = data.motif || '';

                // Antécédents médicaux
                if (data.hta) setRadioChecked('hta', data.hta);
                if (data.diabete) setRadioChecked('diabete', data.diabete);
                if (data.tabac) setRadioChecked('tabac', data.tabac);
                if (data.alcool) setRadioChecked('alcool', data.alcool);
                if (data.ugd) setRadioChecked('UGD', data.ugd);
                if (data.drepanocytaire) setRadioChecked('drepanocytaire', data.drepanocytaire);

                if (data.traitement_medicamenteux) {
                    setRadioChecked('traitement_medicamenteux', data.traitement_medicamenteux);
                    if (data.traitement_medicamenteux === 'Oui') {
                        toggleMdlSection('mdlBlocTraitement', true);
                        if (document.getElementById('mdlTraitementMed')) document.getElementById('mdlTraitementMed').value = data.traitement_medicamenteux_anterieur || '';
                    }
                }

                if (data.antecedent_medical) {
                    setRadioChecked('antecedent_medical', data.antecedent_medical);
                    if (data.antecedent_medical === 'Oui') {
                        toggleMdlSection('mdlBlocAutreAntecedent', true);
                        if (document.getElementById('mdlAutreAntecedent')) document.getElementById('mdlAutreAntecedent').value = data.autre_antecedent_medical || '';
                    }
                }

                // Antécédents chirurgicaux
                if (data.antecedent_chirurgical) {
                    setRadioChecked('antecedent_chirurgical', data.antecedent_chirurgical);
                    if (data.antecedent_chirurgical === 'Oui') {
                        toggleMdlSection('mdlBlocNomOperation', true);
                        if (document.getElementById('nom_operation') && data.nom_operation) {
                            document.getElementById('nom_operation').value = data.nom_operation;
                        }
                    }
                }
                if (document.getElementById('mdlAutreChirurgical')) {
                    document.getElementById('mdlAutreChirurgical').value = data.autre_antecedent_chirurgical || '';
                }

                // Gynéco-obstétrique (femmes)
                const cardGyneco = document.getElementById('mdlCardGyneco');
                const isFeminin = (data.gender && data.gender.toLowerCase() === 'feminin');
                if (cardGyneco) {
                    cardGyneco.style.display = isFeminin ? 'block' : 'none';
                    if (isFeminin) {
                        if (data.en_cours_de_grossesse) {
                            setRadioChecked('en_cours_de_grossesse', data.en_cours_de_grossesse);
                            if (data.en_cours_de_grossesse === 'Oui') {
                                toggleMdlSection('mdlBlocGrossesse', true);
                                if (document.getElementById('mdlDescGrossesse')) document.getElementById('mdlDescGrossesse').value = data.description_grossesse || '';
                            }
                        }
                        if (document.getElementById('mdlDDR')) document.getElementById('mdlDDR').value = data.ddr || '';
                    }
                }

                // Examen clinique & Tuberculose
                if (data.tuberculose) setRadioChecked('tuberculose', data.tuberculose);
                if (data.autre_examen_clinique) {
                    setRadioChecked('autre_examen', 'Oui');
                    toggleMdlSection('mdlInputAutreExam', true);
                    if (document.getElementById('mdlAutreExamClinique')) document.getElementById('mdlAutreExamClinique').value = data.autre_examen_clinique;
                }
                if (data.examen_physique) {
                    setRadioChecked('examen_physique_q', 'Oui');
                    toggleMdlSection('mdlBlocExamPhysique', true);
                    if (document.getElementById('mdlExamPhysique')) document.getElementById('mdlExamPhysique').value = data.examen_physique;
                }
                if (data.diagnostic_retenu) {
                    setRadioChecked('diagnostic_retenu_q', 'Oui');
                    toggleMdlSection('mdlBlocDiagnostic', true);
                    if (document.getElementById('mdlDiagnosticRetenu')) document.getElementById('mdlDiagnosticRetenu').value = data.diagnostic_retenu;
                }
                if (data.autre_pathologie_associee) {
                    setRadioChecked('pathologie_associee_q', 'Oui');
                    toggleMdlSection('mdlBlocPathologie', true);
                    if (document.getElementById('mdlPathologieAssociee')) document.getElementById('mdlPathologieAssociee').value = data.autre_pathologie_associee;
                }

                // Examens complémentaires
                if (data.tdr_paludisme) setRadioChecked('tdr_paludisme', data.tdr_paludisme);
                if (data.goutte_epaise) setRadioChecked('goutte_epaise', data.goutte_epaise);
                if (data.milda_enfant_eligible) setRadioChecked('milda_enfant_eligible', data.milda_enfant_eligible);
                if (data.remise_milda_enfant) setRadioChecked('remise_milda_enfant', data.remise_milda_enfant);
                if (data.cdip_propose) setRadioChecked('cdip_propose', data.cdip_propose);
                if (data.cdip_realise) setRadioChecked('cdip_realise', data.cdip_realise);
                if (document.getElementById('mdlCodeDepistage')) document.getElementById('mdlCodeDepistage').value = data.code_depistage_client || '';

                // Issue de la consultation
                if (data.mode_sortie) setRadioChecked('mode_sortie', data.mode_sortie);
            })
            .catch(err => {
                console.error("Erreur récupération infos patient consultation:", err);
            });
    }

    function refreshDoctorModalConsultation() {
        if (currentConsultationId) {
            loadDoctorModalConsultation(currentConsultationId);
        }
    }

    // Soumettre le formulaire de consultation
    async function submitDoctorModalConsultation() {
        const form = document.getElementById('form-modal-consultation');
        const btn = document.getElementById('btnSubmitModalConsultation');
        const statusMsg = document.getElementById('mdlSaveStatusMsg');

        // Validation Motif
        const motif = document.getElementById('mdlMotif');
        if (!motif || !motif.value.trim()) {
            alert("Veuillez renseigner le motif de la consultation.");
            if (motif) {
                motif.focus();
                motif.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // Validation Issue
        const modeSortie = form.querySelector('input[name="mode_sortie"]:checked');
        if (!modeSortie) {
            alert("Veuillez sélectionner l'issue de la consultation (Sortie, Hospitalisé(e), etc.).");
            const secIssue = document.getElementById('secIssueConsult');
            if (secIssue) secIssue.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Enregistrement en cours...';
        if (statusMsg) {
            statusMsg.style.display = 'inline-block';
            statusMsg.className = 'fs-13 fw-bold text-muted';
            statusMsg.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Enregistrement en cours...';
        }

        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2 fs-16"></i> Enregistrer la consultation';

            if (res.ok || res.redirected) {
                if (statusMsg) {
                    statusMsg.className = 'fs-13 fw-bold text-success';
                    statusMsg.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Consultation enregistrée avec succès !';
                }
                if (typeof toastr !== 'undefined') {
                    toastr.success("La consultation a été enregistrée avec succès !");
                } else {
                    alert("Consultation enregistrée avec succès !");
                }
                if (typeof loadPendingOnlineRequests === 'function') {
                    loadPendingOnlineRequests();
                }
            } else {
                throw new Error("Erreur serveur code: " + res.status);
            }
        } catch (error) {
            console.error("Erreur enregistrement consultation:", error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2 fs-16"></i> Enregistrer la consultation';
            if (statusMsg) {
                statusMsg.className = 'fs-13 fw-bold text-danger';
                statusMsg.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i> Erreur lors de l\'enregistrement.';
            }
            alert("Une erreur s'est produite lors de l'enregistrement de la consultation.");
        }
    }
</script>
