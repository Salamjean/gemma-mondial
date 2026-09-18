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

/* Cartes d'Issue Prioritaires (Sortie, Observation, À revoir) */
.med-issue-hero-card {
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.22s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    position: relative;
    user-select: none;
}
.med-issue-hero-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
}
.med-issue-hero-card.card-sortie:hover { border-color: #059669; background: #f0fdf4; }
.med-issue-hero-card.card-observation:hover { border-color: #d97706; background: #fffbeb; }
.med-issue-hero-card.card-arevoir:hover { border-color: #0d9488; background: #f0fdfa; }

.med-issue-hero-card:has(input[type="radio"]:checked) {
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
.med-issue-hero-card.card-sortie:has(input[type="radio"]:checked) {
    border-color: #059669;
    background: #ecfdf5;
}
.med-issue-hero-card.card-observation:has(input[type="radio"]:checked) {
    border-color: #d97706;
    background: #fffbeb;
}
.med-issue-hero-card.card-arevoir:has(input[type="radio"]:checked) {
    border-color: #0d9488;
    background: #f0fdfa;
}

/* Checkbox modules post-consultation (Cartes d'Action Haut de Gamme) */
.med-module-toggle-card {
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    min-height: 105px;
    user-select: none;
    position: relative;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
}
.med-module-toggle-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}
.med-module-toggle-card.mod-externe:hover { border-color: #3b82f6; background: #f8faff; }
.med-module-toggle-card.mod-interne:hover { border-color: #0d9488; background: #f2fcf9; }
.med-module-toggle-card.mod-examen:hover { border-color: #f59e0b; background: #fffdf5; }
.med-module-toggle-card.mod-arret:hover { border-color: #f43f5e; background: #fff5f6; }

.med-module-toggle-card:has(input[type="checkbox"]:checked) {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}
.med-module-toggle-card.mod-externe:has(input[type="checkbox"]:checked) {
    border-color: #2563eb;
    background: linear-gradient(180deg, #eff6ff 0%, #ffffff 100%);
}
.med-module-toggle-card.mod-interne:has(input[type="checkbox"]:checked) {
    border-color: #0d9488;
    background: linear-gradient(180deg, #f0fdfa 0%, #ffffff 100%);
}
.med-module-toggle-card.mod-examen:has(input[type="checkbox"]:checked) {
    border-color: #d97706;
    background: linear-gradient(180deg, #fffbeb 0%, #ffffff 100%);
}
.med-module-toggle-card.mod-arret:has(input[type="checkbox"]:checked) {
    border-color: #e11d48;
    background: linear-gradient(180deg, #fff1f2 0%, #ffffff 100%);
}

.med-module-toggle-card .mod-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s ease;
}
.med-module-toggle-card.mod-externe .mod-icon-box { background: #dbeafe; color: #2563eb; }
.med-module-toggle-card.mod-interne .mod-icon-box { background: #ccfbf1; color: #0d9488; }
.med-module-toggle-card.mod-examen .mod-icon-box { background: #fef3c7; color: #d97706; }
.med-module-toggle-card.mod-arret .mod-icon-box { background: #ffe4e6; color: #e11d48; }

.med-module-toggle-card:has(input[type="checkbox"]:checked) .mod-icon-box {
    transform: scale(1.08);
}
.med-module-toggle-card.mod-externe:has(input[type="checkbox"]:checked) .mod-icon-box { background: #2563eb; color: #ffffff; }
.med-module-toggle-card.mod-interne:has(input[type="checkbox"]:checked) .mod-icon-box { background: #0d9488; color: #ffffff; }
.med-module-toggle-card.mod-examen:has(input[type="checkbox"]:checked) .mod-icon-box { background: #d97706; color: #ffffff; }
.med-module-toggle-card.mod-arret:has(input[type="checkbox"]:checked) .mod-icon-box { background: #e11d48; color: #ffffff; }

.med-module-toggle-card input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}
.med-module-toggle-card.mod-externe input[type="checkbox"] { accent-color: #2563eb; }
.med-module-toggle-card.mod-interne input[type="checkbox"] { accent-color: #0d9488; }
.med-module-toggle-card.mod-examen input[type="checkbox"] { accent-color: #d97706; }
.med-module-toggle-card.mod-arret input[type="checkbox"] { accent-color: #e11d48; }

/* Conteneurs de modules et rangées de prescription */
.med-module-box {
    background: #ffffff;
    border-radius: 12px;
    border: 1.5px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    margin-bottom: 16px;
    overflow: hidden;
    transition: border-color 0.2s ease;
}
.med-module-box-header {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
}
.med-module-box-body {
    padding: 14px 16px;
    background: #f8fafc;
}

/* Ligne individuelle de prescription médicale */
.med-prescription-strip {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease;
}
.med-prescription-strip:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

/* Chips rapides pour les examens */
.med-exam-chip {
    font-size: 11px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 14px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    cursor: pointer;
    transition: all 0.18s ease;
    user-select: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.med-exam-chip:hover {
    background: #fef3c7;
    border-color: #f59e0b;
    color: #92400e;
    transform: translateY(-1px);
}

/* Bouton supprimer moderne */
.btn-med-delete {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 1px solid #fecdd3;
    background: #fff1f2;
    color: #e11d48;
    transition: all 0.18s ease;
    cursor: pointer;
}
.btn-med-delete:hover {
    background: #e11d48;
    color: #ffffff;
    border-color: #e11d48;
    transform: scale(1.05);
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

        <form id="form-modal-consultation" method="POST" action="{{ route('doctor.consultation.online.save.complete') }}" enctype="multipart/form-data">
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
            {{-- F. ISSUE DE LA CONSULTATION & MODULES DE TRANSMISSION      --}}
            {{-- ========================================================== --}}
            <div id="secIssueConsult" class="med-box border-2 shadow-sm" style="border-color: #0d9488 !important;">
                <div class="med-box-header text-white" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%);">
                    <h5 class="med-box-title text-white">
                        <i class="fa-solid fa-clipboard-check fs-16"></i> Issue de la consultation <span class="text-warning fw-bold">*</span>
                    </h5>
                    <span class="badge bg-white text-dark fs-12 px-2.5 py-1 fw-bold shadow-sm">Décision médicale requise</span>
                </div>
                <div class="med-box-body p-3 p-md-4">
                    
                    <div class="text-muted fs-13 mb-3">
                        <i class="fa-solid fa-info-circle text-teal me-1"></i> Choisissez l'issue de cette consultation pour débloquer les formulaires de transmission associés :
                    </div>

                    <!-- 3 Options Prioritaires Principales sous forme de superbes Hero Cards -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="med-issue-hero-card card-sortie" for="mdlRadioIssueSortie">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle bg-success-subtle text-success">
                                            <i class="fa-solid fa-door-open fs-18"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-15">Sortie du patient</div>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 px-2 py-0.5">Fin de consultation</span>
                                        </div>
                                    </div>
                                    <input type="radio" name="mode_sortie" id="mdlRadioIssueSortie" value="sortie" class="form-check-input mt-1" style="transform: scale(1.3); cursor: pointer;" onchange="handleIssueModeChange(this.value)">
                                </div>
                                <div class="text-muted fs-12 mt-1">
                                    Prescrire des ordonnances (externe/interne), éditer des bulletins d'analyses ou délivrer un arrêt de travail.
                                </div>
                            </label>
                        </div>

                        <div class="col-md-4">
                            <label class="med-issue-hero-card card-observation" for="mdlRadioIssueObs">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle bg-warning-subtle text-warning">
                                            <i class="fa-solid fa-eye fs-18"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-15">Mise en observation</div>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11 px-2 py-0.5">Surveillance</span>
                                        </div>
                                    </div>
                                    <input type="radio" name="mode_sortie" id="mdlRadioIssueObs" value="observation" class="form-check-input mt-1" style="transform: scale(1.3); cursor: pointer;" onchange="handleIssueModeChange(this.value)">
                                </div>
                                <div class="text-muted fs-12 mt-1">
                                    Mettre le patient sous surveillance médicale courte durée (2h, 4h, 12h...) avec consignes pour l'équipe soignante.
                                </div>
                            </label>
                        </div>

                        <div class="col-md-4">
                            <label class="med-issue-hero-card card-arevoir" for="mdlRadioIssueARevoir">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-circle bg-info-subtle text-info">
                                            <i class="fa-solid fa-calendar-plus fs-18"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-15">À revoir</div>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fs-11 px-2 py-0.5">Suivi / Contrôle</span>
                                        </div>
                                    </div>
                                    <input type="radio" name="mode_sortie" id="mdlRadioIssueARevoir" value="a-revoir" class="form-check-input mt-1" style="transform: scale(1.3); cursor: pointer;" onchange="handleIssueModeChange(this.value)">
                                </div>
                                <div class="text-muted fs-12 mt-1">
                                    Programmer une date de rendez-vous de suivi ou de contrôle des résultats d'examens.
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Autres issues secondaires (dépliables) -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-secondary fw-semibold fs-12" onclick="toggleMdlOtherIssues()">
                            <i class="fa-solid fa-ellipsis me-1"></i> Autres options d'issue (Hospitalisation, Référence...) <i id="iconToggleOtherIssues" class="fa-solid fa-chevron-down ms-1 fs-10"></i>
                        </button>
                        <div id="blocOtherIssues" class="mt-2 p-2 bg-light rounded border" style="display: none;">
                            <div class="med-issue-grid">
                                <label class="med-issue-option"><input type="radio" name="mode_sortie" value="hospitalisation" onchange="handleIssueModeChange(this.value)"> <span><i class="fa-solid fa-bed text-primary"></i> Hospitalisé(e)</span></label>
                                <label class="med-issue-option"><input type="radio" name="mode_sortie" value="refere-interne" onchange="handleIssueModeChange(this.value)"> <span><i class="fa-solid fa-hospital-user text-info"></i> Référé(e) en interne</span></label>
                                <label class="med-issue-option"><input type="radio" name="mode_sortie" value="refere-externe" onchange="handleIssueModeChange(this.value)"> <span><i class="fa-solid fa-ambulance text-danger"></i> Référé(e) en externe</span></label>
                                <label class="med-issue-option"><input type="radio" name="mode_sortie" value="cas-presume-tb-resume" onchange="handleIssueModeChange(this.value)"> <span><i class="fa-solid fa-lungs text-warning"></i> Cas présumé TB référé</span></label>
                                <label class="med-issue-option"><input type="radio" name="mode_sortie" value="declaration-deces-patient" onchange="handleIssueModeChange(this.value)"> <span><i class="fa-solid fa-skull text-dark"></i> Décédé(e)</span></label>
                            </div>
                        </div>
                    </div>

                    <!-- ── PLACEHOLDER VISUEL TANT QU'AUCUNE ISSUE N'EST SÉLECTIONNÉE ── -->
                    <div id="mdlIssuePlaceholder" class="p-4 text-center rounded-3 bg-light border border-dashed text-secondary my-3">
                        <i class="fa-solid fa-hand-pointer fs-24 text-teal mb-2 d-block"></i>
                        <div class="fw-bold fs-14 text-dark">Sélectionnez une issue de consultation ci-dessus</div>
                        <div class="text-muted fs-12 mt-1">Les modules correspondants (Ordonnances, Examens, Observation, RDV) s'afficheront immédiatement dès votre choix.</div>
                    </div>

                    <!-- ── PANNEAU 1 : SORTIE AVEC MODULES DE TRANSMISSION ────────────────── -->
                    <div id="panelIssueSortie" class="p-3 bg-white rounded-3 border border-success-subtle shadow-sm mt-3" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 rounded-circle bg-success-subtle text-success">
                                    <i class="fa-solid fa-folder-plus fs-16"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-success mb-0 fs-15">
                                        Modules de transmission &amp; Prescriptions de sortie
                                    </h6>
                                    <div class="text-muted fs-12">Sélectionnez les documents à générer et à transmettre pour cette consultation :</div>
                                </div>
                            </div>
                        </div>

                        <!-- Choix des modules par superbes cartes d'action interactives -->
                        <div class="row g-3 mb-4">
                            <!-- 1. ORDONNANCE EXTERNE -->
                            <div class="col-md-6 col-xl-3">
                                <label class="med-module-toggle-card mod-externe w-100" for="chkMdlOrdExterne">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="mod-icon-box">
                                            <i class="fa-solid fa-pills"></i>
                                        </div>
                                        <input type="checkbox" name="has_ordonnance_externe" id="chkMdlOrdExterne" value="1" onchange="toggleMdlSection('moduleOrdonnanceExterne', this.checked)">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14 mb-0.5">Ordonnance</div>
                                        <div class="text-muted fs-11">Prescription médicaments externe</div>
                                    </div>
                                </label>
                            </div>

                            <!-- 2. ORDONNANCE INTERNE -->
                            <div class="col-md-6 col-xl-3">
                                <label class="med-module-toggle-card mod-interne w-100" for="chkMdlOrdInterne">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="mod-icon-box">
                                            <i class="fa-solid fa-clinic-medical"></i>
                                        </div>
                                        <input type="checkbox" name="has_ordonnance_interne" id="chkMdlOrdInterne" value="1" onchange="toggleMdlSection('moduleOrdonnanceInterne', this.checked)">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14 mb-0.5">Ord. Interne</div>
                                        <div class="text-muted fs-11">Pharmacie interne de l'hôpital</div>
                                    </div>
                                </label>
                            </div>

                            <!-- 3. BULLETIN D'EXAMEN -->
                            <div class="col-md-6 col-xl-3">
                                <label class="med-module-toggle-card mod-examen w-100" for="chkMdlExamen">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="mod-icon-box">
                                            <i class="fa-solid fa-microscope"></i>
                                        </div>
                                        <input type="checkbox" name="has_bulletin_examen" id="chkMdlExamen" value="1" onchange="toggleMdlSection('moduleBulletinExamen', this.checked)">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14 mb-0.5">Bulletin d'examen</div>
                                        <div class="text-muted fs-11">Analyses biomédicales &amp; Imagerie</div>
                                    </div>
                                </label>
                            </div>

                            <!-- 4. ARRÊT DE TRAVAIL -->
                            <div class="col-md-6 col-xl-3">
                                <label class="med-module-toggle-card mod-arret w-100" for="chkMdlArret">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="mod-icon-box">
                                            <i class="fa-solid fa-calendar-xmark"></i>
                                        </div>
                                        <input type="checkbox" name="has_arret_travail" id="chkMdlArret" value="1" onchange="toggleMdlSection('moduleArretTravail', this.checked)">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14 mb-0.5">Arrêt de travail</div>
                                        <div class="text-muted fs-11">Certificat de repos médical</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- 1. MODULE ORDONNANCE EXTERNE -->
                        <div id="moduleOrdonnanceExterne" class="med-module-box border-primary-subtle" style="display: none;">
                            <div class="med-module-box-header bg-primary-subtle border-primary-subtle">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="p-1.5 rounded bg-primary text-white"><i class="fa-solid fa-pills"></i></span>
                                    <div>
                                        <span class="fw-bold fs-14 text-primary">Ordonnance Médicale Externe</span>
                                        <span id="mdlCountDrugsCatalog" class="badge bg-white text-primary border border-primary-subtle ms-2 fs-11">Chargement catalogue...</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold fs-12 shadow-sm" onclick="addMdlOrdonnanceRow()">
                                    <i class="fa-solid fa-plus me-1"></i> Ajouter un médicament
                                </button>
                            </div>
                            <div class="med-module-box-body">
                                <div id="mdlOrdonnanceRows" class="d-flex flex-column gap-2">
                                    <!-- Lignes de médicaments insérées dynamiquement -->
                                </div>
                            </div>
                        </div>

                        <!-- 2. MODULE ORDONNANCE INTERNE -->
                        <div id="moduleOrdonnanceInterne" class="med-module-box" style="display: none; border-color: #99f6e4;">
                            <div class="med-module-box-header" style="background: #ccfbf1; border-color: #99f6e4;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="p-1.5 rounded text-white" style="background: #0d9488;"><i class="fa-solid fa-clinic-medical"></i></span>
                                    <div>
                                        <span class="fw-bold fs-14 text-teal" style="color: #0f766e;">Ordonnance Interne (Pharmacie de l'Hôpital)</span>
                                        <span id="mdlCountHospitalDrugsCatalog" class="badge bg-white text-teal border border-teal-subtle ms-2 fs-11" style="color: #0d9488;">Chargement...</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm text-white rounded-pill px-3 fw-bold fs-12 shadow-sm" style="background: #0d9488;" onclick="addMdlOrdonnanceInternalRow()">
                                    <i class="fa-solid fa-plus me-1"></i> Ajouter un produit interne
                                </button>
                            </div>
                            <div class="med-module-box-body">
                                <div id="mdlOrdonnanceInternalRows" class="d-flex flex-column gap-2">
                                    <!-- Lignes de médicaments internes insérées dynamiquement -->
                                </div>
                            </div>
                        </div>

                        <!-- 3. MODULE BULLETIN D'EXAMEN -->
                        <div id="moduleBulletinExamen" class="med-module-box border-warning-subtle" style="display: none;">
                            <div class="med-module-box-header bg-warning-subtle border-warning-subtle">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="p-1.5 rounded bg-warning text-dark"><i class="fa-solid fa-microscope"></i></span>
                                    <div>
                                        <span class="fw-bold fs-14 text-dark">Bulletin d'Examen &amp; Analyses Médicales</span>
                                        <span class="badge bg-white text-secondary border ms-2 fs-11">Prescription examens</span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold fs-12 text-dark shadow-sm" onclick="addMdlExamenRow()">
                                    <i class="fa-solid fa-plus me-1"></i> Ajouter un examen
                                </button>
                            </div>
                            <div class="med-module-box-body">
                                <!-- Suggestions d'examens rapides (Chips) -->
                                <div class="mb-2 pb-2 border-bottom">
                                    <div class="text-muted fs-11 fw-bold text-uppercase mb-1.5"><i class="fa-solid fa-bolt text-warning me-1"></i> Ajout rapide d'examens fréquents :</div>
                                    <div class="d-flex flex-wrap gap-1.5">
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('NFS + Plaquettes')"><i class="fa-solid fa-plus fs-9"></i> NFS + Plaquettes</span>
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('Goutte Épaisse (GE) & TDR Paludisme')"><i class="fa-solid fa-plus fs-9"></i> GE &amp; TDR Palu</span>
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('Glycémie à jeun')"><i class="fa-solid fa-plus fs-9"></i> Glycémie à jeun</span>
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('ECBU avec antibiogramme')"><i class="fa-solid fa-plus fs-9"></i> ECBU</span>
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('Créatininémie + Urée')"><i class="fa-solid fa-plus fs-9"></i> Créatinine / Urée</span>
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('Test Widal & Félix (Typhoïde)')"><i class="fa-solid fa-plus fs-9"></i> Séro. Widal</span>
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('Radiographie du Thorax Face')"><i class="fa-solid fa-plus fs-9"></i> Radio Thorax</span>
                                        <span class="med-exam-chip" onclick="addMdlExamenRow('Échographie Abdomino-pelvienne')"><i class="fa-solid fa-plus fs-9"></i> Écho Abdo-pelvienne</span>
                                    </div>
                                </div>

                                <div id="mdlExamensRows" class="d-flex flex-column gap-2 mt-2">
                                    <!-- Lignes d'examens insérées dynamiquement -->
                                </div>
                            </div>
                        </div>

                        <!-- 4. MODULE ARRÊT DE TRAVAIL -->
                        <div id="moduleArretTravail" class="med-module-box border-danger-subtle" style="display: none;">
                            <div class="med-module-box-header bg-danger-subtle border-danger-subtle">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="p-1.5 rounded bg-danger text-white"><i class="fa-solid fa-calendar-xmark"></i></span>
                                    <span class="fw-bold fs-14 text-danger">Certificat d'Arrêt de Travail / Repos Médical</span>
                                </div>
                            </div>
                            <div class="med-module-box-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="med-field-label"><i class="fa-regular fa-calendar-check text-success"></i> Date début :</label>
                                        <input type="date" class="med-input-text" name="arret_date_debut" id="mdlArretDateDebut" value="{{ date('Y-m-d') }}" onchange="calculerDiffArretMdl()">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="med-field-label"><i class="fa-regular fa-calendar-xmark text-danger"></i> Date fin :</label>
                                        <input type="date" class="med-input-text" name="arret_date_fin" id="mdlArretDateFin" value="{{ date('Y-m-d', strtotime('+3 days')) }}" onchange="calculerDiffArretMdl()">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2.5 rounded-3 bg-white border border-danger-subtle text-center shadow-sm">
                                            <span class="text-muted fs-11 fw-bold text-uppercase d-block mb-1">Durée du repos</span>
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <input type="text" class="med-input-text fw-bold text-danger text-center border-0 p-0 fs-18" style="width: 50px; background: transparent;" name="arret_nb_jour" id="mdlArretNbJour" value="3" readonly>
                                                <span class="fw-bold text-danger fs-14">jours</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ── PANNEAU 2 : MISE EN OBSERVATION ──────────────────────────────── -->
                    <div id="panelIssueObservation" class="p-3 bg-white rounded-3 border border-warning-subtle shadow-sm mt-3" style="display: none;">
                        <h6 class="fw-bold text-warning mb-2 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-eye fs-16"></i> Protocole de mise en observation
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="med-field-label">Durée d'observation prévue :</label>
                                <select class="med-input-text" name="observation_duree" id="mdlObsDuree">
                                    <option value="2 heures">2 heures</option>
                                    <option value="4 heures" selected>4 heures</option>
                                    <option value="8 heures">8 heures</option>
                                    <option value="12 heures">12 heures</option>
                                    <option value="24 heures">24 heures</option>
                                    <option value="Autre durée">Autre durée</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="med-field-label">Conduite à tenir / Motif d'observation :</label>
                                <input type="text" class="med-input-text" name="observation_conduite" id="mdlObsConduite" placeholder="Ex: Réhydratation, surveillance tensionnelle...">
                            </div>
                            <div class="col-12">
                                <label class="med-field-label">Consignes de surveillance pour l'équipe soignante :</label>
                                <textarea class="med-textarea" name="observation_consigne" id="mdlObsConsigne" rows="2" placeholder="Prise des constantes toutes les heures, alerter si TA > 16/10..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── PANNEAU 3 : À REVOIR ─────────────────────────────────────────── -->
                    <div id="panelIssueARevoir" class="p-3 bg-white rounded-3 border border-info-subtle shadow-sm mt-3" style="display: none;">
                        <h6 class="fw-bold text-info mb-2 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calendar-check fs-16"></i> Planification du rendez-vous de contrôle
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="med-field-label">Date du prochain rendez-vous :</label>
                                <input type="date" class="med-input-text" name="date_rdv_prochain" id="mdlDateRdvProchain" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                            </div>
                            <div class="col-md-8">
                                <label class="med-field-label">Motif du contrôle / Examens à apporter :</label>
                                <textarea class="med-textarea" name="motif_rdv_prochain" id="mdlMotifRdvProchain" rows="2" placeholder="Contrôle de la glycémie, évaluation après traitement antibiotique..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── REÇUS & LIENS DE TÉLÉCHARGEMENT PDF (AFFICHÉ APRÈS ENREGISTREMENT) ── -->
            <div id="mdlGeneratedDocsReceipt" class="med-box border-success" style="display: none; background: #f0fdf4;">
                <div class="med-box-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-circle-check text-success fs-22"></i>
                            <div>
                                <h6 class="fw-bold text-success mb-0 fs-15">Consultation enregistrée avec succès !</h6>
                                <span class="text-muted fs-12">Les documents médicaux générés sont disponibles au téléchargement :</span>
                            </div>
                        </div>
                    </div>
                    <div id="mdlReceiptButtonsContainer" class="d-flex flex-wrap gap-2 mt-2">
                        <!-- Boutons PDF insérés dynamiquement -->
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
                        <i class="fa-solid fa-floppy-disk me-2 fs-16"></i> Enregistrer la consultation &amp; Transmettre
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

<script>
    // Catalogues globaux de médicaments
    window.mdlDrugsCatalog = [];
    window.mdlHospitalDrugsCatalog = [];

    // Basculer l'affichage conditionnel de sous-sections
    function toggleMdlSection(elementId, show) {
        const el = document.getElementById(elementId);
        if (el) {
            el.style.display = show ? 'block' : 'none';
        }
    }

    // Basculer les autres issues secondaires
    function toggleMdlOtherIssues() {
        const bloc = document.getElementById('blocOtherIssues');
        const icon = document.getElementById('iconToggleOtherIssues');
        if (bloc) {
            const isHidden = bloc.style.display === 'none';
            bloc.style.display = isHidden ? 'block' : 'none';
            if (icon) {
                icon.className = isHidden ? 'fa-solid fa-chevron-up ms-1 fs-10' : 'fa-solid fa-chevron-down ms-1 fs-10';
            }
        }
    }

    // Gestion de changement d'Issue de consultation (affiche uniquement le panneau sélectionné)
    function handleIssueModeChange(value) {
        const placeholder = document.getElementById('mdlIssuePlaceholder');
        const panelSortie = document.getElementById('panelIssueSortie');
        const panelObs = document.getElementById('panelIssueObservation');
        const panelARevoir = document.getElementById('panelIssueARevoir');

        if (!value) {
            if (placeholder) placeholder.style.display = 'block';
            if (panelSortie) panelSortie.style.display = 'none';
            if (panelObs) panelObs.style.display = 'none';
            if (panelARevoir) panelARevoir.style.display = 'none';
            return;
        }

        if (placeholder) placeholder.style.display = 'none';
        if (panelSortie) panelSortie.style.display = (value === 'sortie') ? 'block' : 'none';
        if (panelObs) panelObs.style.display = (value === 'observation') ? 'block' : 'none';
        if (panelARevoir) panelARevoir.style.display = (value === 'a-revoir') ? 'block' : 'none';
    }

    // Calcul automatique différence jours pour arrêt de travail
    function calculerDiffArretMdl() {
        const d1Input = document.getElementById('mdlArretDateDebut');
        const d2Input = document.getElementById('mdlArretDateFin');
        const nbInput = document.getElementById('mdlArretNbJour');

        if (!d1Input || !d2Input || !nbInput) return;
        if (!d1Input.value || !d2Input.value) return;

        const date1 = new Date(d1Input.value);
        const date2 = new Date(d2Input.value);

        if (date2 < date1) {
            nbInput.value = 1;
            return;
        }

        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        nbInput.value = diffDays;
    }

    // 1. Ajouter une ligne d'ordonnance externe
    function addMdlOrdonnanceRow(drugId = '', posology = '', qte = 1, route = 'Voie Orale', duration = '5 jours', advice = '') {
        const container = document.getElementById('mdlOrdonnanceRows');
        if (!container) return;

        let drugOptions = '<option value="" disabled ' + (!drugId ? 'selected' : '') + '>Sélectionner un médicament (' + (window.mdlDrugsCatalog.length || 0) + ' disponibles)...</option>';
        window.mdlDrugsCatalog.forEach(d => {
            const isSel = (drugId && String(d.id) === String(drugId)) ? 'selected' : '';
            const codeTxt = d.code ? ` [${d.code}]` : '';
            const unitTxt = d.unite ? ` - ${d.unite}` : '';
            drugOptions += `<option value="${d.id}" ${isSel}>${d.name}${codeTxt}${unitTxt}</option>`;
        });

        const rowId = 'ord_row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        const html = `
            <div id="${rowId}" class="med-prescription-strip">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-capsules text-primary"></i> Médicament <span class="text-danger">*</span> :</label>
                        <select class="med-input-text form-select fw-semibold text-dark" name="medicamentCode[]" required>
                            ${drugOptions}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-clock text-primary"></i> Posologie :</label>
                        <input type="text" class="med-input-text" name="medicamentPosologie[]" value="${posology}" placeholder="Ex: 1 cp matin et soir">
                    </div>
                    <div class="col-md-2">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-hashtag text-primary"></i> Quantité :</label>
                        <input type="number" class="med-input-text fw-bold text-center" name="medicamentQte[]" value="${qte}" min="1">
                    </div>
                    <div class="col-md-1 text-end">
                        <label class="med-field-label mb-1 d-none d-md-block">&nbsp;</label>
                        <button type="button" class="btn-med-delete" onclick="document.getElementById('${rowId}').remove()" title="Supprimer ce médicament">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-route text-secondary"></i> Voie d'administration :</label>
                        <select class="med-input-text form-select" name="routeAdministration[]">
                            <option value="Voie Orale" ${route==='Voie Orale'?'selected':''}>Voie Orale</option>
                            <option value="IVD" ${route==='IVD'?'selected':''}>IVD (Intraveineuse directe)</option>
                            <option value="IVL" ${route==='IVL'?'selected':''}>IVL (Intraveineuse lente)</option>
                            <option value="SC" ${route==='SC'?'selected':''}>SC (Sous-cutanée)</option>
                            <option value="IM" ${route==='IM'?'selected':''}>IM (Intramusculaire)</option>
                            <option value="ID" ${route==='ID'?'selected':''}>ID (Intradermique)</option>
                            <option value="Inhalation" ${route==='Inhalation'?'selected':''}>Inhalation / Nébulisation</option>
                            <option value="Locale" ${route==='Locale'?'selected':''}>Locale / Cutanée</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="med-field-label mb-1"><i class="fa-regular fa-calendar text-secondary"></i> Durée :</label>
                        <input type="text" class="med-input-text" name="duration[]" value="${duration}" placeholder="Ex: 5 jours">
                    </div>
                    <div class="col-md-5">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-circle-info text-secondary"></i> Conseil hygiéno-diététique :</label>
                        <input type="text" class="med-input-text" name="healthDieteticAdvice[]" value="${advice}" placeholder="Ex: Prendre pendant ou après le repas">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // 2. Ajouter une ligne d'ordonnance interne
    function addMdlOrdonnanceInternalRow(drugId = '', posology = '', qte = 1, route = 'Voie Orale', duration = '3 jours', advice = '') {
        const container = document.getElementById('mdlOrdonnanceInternalRows');
        if (!container) return;

        let drugOptions = '<option value="" disabled ' + (!drugId ? 'selected' : '') + '>Sélectionner un produit disponible...</option>';
        window.mdlHospitalDrugsCatalog.forEach(d => {
            const isSel = (drugId && String(d.id) === String(drugId)) ? 'selected' : '';
            const priceTxt = d.price ? ` — [${d.price} FCFA]` : '';
            drugOptions += `<option value="${d.id}" ${isSel}>${d.name}${priceTxt}</option>`;
        });

        const rowId = 'ord_i_row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        const html = `
            <div id="${rowId}" class="med-prescription-strip">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-capsules text-teal"></i> Produit pharmacie interne <span class="text-danger">*</span> :</label>
                        <select class="med-input-text form-select fw-semibold text-dark" name="medicamentCodeI[]" required>
                            ${drugOptions}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-clock text-teal"></i> Posologie :</label>
                        <input type="text" class="med-input-text" name="medicamentPosologieI[]" value="${posology}" placeholder="Ex: 1 ampoule matin et soir">
                    </div>
                    <div class="col-md-2">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-hashtag text-teal"></i> Quantité :</label>
                        <input type="number" class="med-input-text fw-bold text-center" name="medicamentQteI[]" value="${qte}" min="1">
                    </div>
                    <div class="col-md-1 text-end">
                        <label class="med-field-label mb-1 d-none d-md-block">&nbsp;</label>
                        <button type="button" class="btn-med-delete" onclick="document.getElementById('${rowId}').remove()" title="Supprimer ce produit">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-route text-secondary"></i> Voie d'administration :</label>
                        <select class="med-input-text form-select" name="routeAdministrationI[]">
                            <option value="Voie Orale" ${route==='Voie Orale'?'selected':''}>Voie Orale</option>
                            <option value="IVD" ${route==='IVD'?'selected':''}>IVD (Intraveineuse directe)</option>
                            <option value="IVL" ${route==='IVL'?'selected':''}>IVL (Intraveineuse lente)</option>
                            <option value="SC" ${route==='SC'?'selected':''}>SC (Sous-cutanée)</option>
                            <option value="IM" ${route==='IM'?'selected':''}>IM (Intramusculaire)</option>
                            <option value="ID" ${route==='ID'?'selected':''}>ID (Intradermique)</option>
                            <option value="Inhalation" ${route==='Inhalation'?'selected':''}>Inhalation / Nébulisation</option>
                            <option value="Locale" ${route==='Locale'?'selected':''}>Locale / Cutanée</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="med-field-label mb-1"><i class="fa-regular fa-calendar text-secondary"></i> Durée :</label>
                        <input type="text" class="med-input-text" name="durationI[]" value="${duration}" placeholder="Ex: 3 jours">
                    </div>
                    <div class="col-md-5">
                        <label class="med-field-label mb-1"><i class="fa-solid fa-circle-info text-secondary"></i> Conseil / Consigne :</label>
                        <input type="text" class="med-input-text" name="healthDieteticAdviceI[]" value="${advice}" placeholder="Conseil hygiéno-diététique">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }

    // 3. Ajouter une ligne d'examen
    function addMdlExamenRow(examName = '') {
        const container = document.getElementById('mdlExamensRows');
        if (!container) return;

        const rowId = 'exam_row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
        const html = `
            <div id="${rowId}" class="d-flex align-items-center gap-2 p-2.5 bg-white rounded-3 border shadow-sm">
                <div class="p-2 rounded-2 bg-warning-subtle text-warning">
                    <i class="fa-solid fa-vial fs-14"></i>
                </div>
                <input type="text" class="med-input-text flex-grow-1 fw-semibold" name="nature_examen[]" value="${examName}" placeholder="Ex: NFS + Plaquettes, Glycémie à jeun, ECBU, Radiographie..." required>
                <button type="button" class="btn-med-delete" onclick="document.getElementById('${rowId}').remove()" title="Supprimer cet examen">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
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

        // Vider conteneurs de prescriptions
        const ordRows = document.getElementById('mdlOrdonnanceRows');
        const ordIRows = document.getElementById('mdlOrdonnanceInternalRows');
        const examRows = document.getElementById('mdlExamensRows');
        if (ordRows) ordRows.innerHTML = '';
        if (ordIRows) ordIRows.innerHTML = '';
        if (examRows) examRows.innerHTML = '';

        // Masquer le bandeau de reçus
        const receipt = document.getElementById('mdlGeneratedDocsReceipt');
        if (receipt) receipt.style.display = 'none';

        // Réinitialiser l'état des radios d'issue
        document.querySelectorAll('input[name="mode_sortie"]').forEach(r => r.checked = false);
        handleIssueModeChange('');

        fetch(`/doctor/consultation/online/patient-info/${consultationId}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    console.warn("Erreur chargement consultation:", data.error);
                    return;
                }

                // Catalogues médicaments
                window.mdlDrugsCatalog = data.drugs || [];
                window.mdlHospitalDrugsCatalog = data.hospital_drugs || [];

                const countEl = document.getElementById('mdlCountDrugsCatalog');
                if (countEl) countEl.textContent = `${window.mdlDrugsCatalog.length} médicaments dispo`;
                const countHEl = document.getElementById('mdlCountHospitalDrugsCatalog');
                if (countHEl) countHEl.textContent = `${window.mdlHospitalDrugsCatalog.length} produits dispo`;

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

                // Issue de la consultation existante (si enregistrée précédemment)
                if (data.mode_sortie) {
                    setRadioChecked('mode_sortie', data.mode_sortie);
                    handleIssueModeChange(data.mode_sortie);
                } else {
                    handleIssueModeChange('');
                }

                // Rechargement des ordonnances et documents existants si disponibles
                if (data.existing_ordonnance_externe && data.existing_ordonnance_externe.prescriptions && data.existing_ordonnance_externe.prescriptions.length > 0) {
                    const chk = document.getElementById('chkMdlOrdExterne');
                    if (chk) {
                        chk.checked = true;
                        toggleMdlSection('moduleOrdonnanceExterne', true);
                    }
                    data.existing_ordonnance_externe.prescriptions.forEach(p => {
                        addMdlOrdonnanceRow(p.drug_id, p.dosage, p.quantity, p.route_administration, p.duration, p.health_dietetic_advice);
                    });
                } else {
                    addMdlOrdonnanceRow();
                }

                if (data.existing_ordonnance_interne && data.existing_ordonnance_interne.prescriptions && data.existing_ordonnance_interne.prescriptions.length > 0) {
                    const chk = document.getElementById('chkMdlOrdInterne');
                    if (chk) {
                        chk.checked = true;
                        toggleMdlSection('moduleOrdonnanceInterne', true);
                    }
                    data.existing_ordonnance_interne.prescriptions.forEach(p => {
                        addMdlOrdonnanceInternalRow(p.drug_id, p.dosage, p.quantity, p.route_administration, p.duration, p.health_dietetic_advice);
                    });
                } else {
                    addMdlOrdonnanceInternalRow();
                }

                if (data.existing_bulletin_examen && data.existing_bulletin_examen.examens && data.existing_bulletin_examen.examens.length > 0) {
                    const chk = document.getElementById('chkMdlExamen');
                    if (chk) {
                        chk.checked = true;
                        toggleMdlSection('moduleBulletinExamen', true);
                    }
                    data.existing_bulletin_examen.examens.forEach(e => {
                        addMdlExamenRow(e.nature_examen);
                    });
                } else {
                    addMdlExamenRow();
                }

                if (data.existing_arret_travail) {
                    const chk = document.getElementById('chkMdlArret');
                    if (chk) {
                        chk.checked = true;
                        toggleMdlSection('moduleArretTravail', true);
                    }
                    if (document.getElementById('mdlArretDateDebut')) document.getElementById('mdlArretDateDebut').value = data.existing_arret_travail.date_debut || '';
                    if (document.getElementById('mdlArretDateFin')) document.getElementById('mdlArretDateFin').value = data.existing_arret_travail.date_fin || '';
                    if (document.getElementById('mdlArretNbJour')) document.getElementById('mdlArretNbJour').value = data.existing_arret_travail.nb_jour || '';
                }
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
            alert("Veuillez sélectionner l'issue de la consultation (Sortie, Mise en observation, À revoir...).");
            const secIssue = document.getElementById('secIssueConsult');
            if (secIssue) secIssue.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Enregistrement &amp; Transmission...';
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
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2 fs-16"></i> Enregistrer la consultation &amp; Transmettre';

            if (res.ok && data.status === 'success') {
                if (statusMsg) {
                    statusMsg.className = 'fs-13 fw-bold text-success';
                    statusMsg.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Enregistré avec succès !';
                }

                // Afficher le bandeau de téléchargement des documents générés
                const receipt = document.getElementById('mdlGeneratedDocsReceipt');
                const btnContainer = document.getElementById('mdlReceiptButtonsContainer');
                if (receipt && btnContainer) {
                    btnContainer.innerHTML = '';
                    let hasDocs = false;

                    if (data.documents) {
                        if (data.documents.ordonnance_externe) {
                            hasDocs = true;
                            btnContainer.innerHTML += `
                                <a href="${data.documents.ordonnance_externe.url}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <i class="fa-solid fa-file-pdf"></i> Télécharger Ordonnance (PDF)
                                </a>
                            `;
                        }
                        if (data.documents.ordonnance_interne) {
                            hasDocs = true;
                            btnContainer.innerHTML += `
                                <a href="${data.documents.ordonnance_interne.url}" target="_blank" class="btn btn-sm text-white rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5" style="background: #0d9488;">
                                    <i class="fa-solid fa-file-pdf"></i> Ordonnance Interne (PDF)
                                </a>
                            `;
                        }
                        if (data.documents.bulletin_examen) {
                            hasDocs = true;
                            btnContainer.innerHTML += `
                                <a href="${data.documents.bulletin_examen.url}" target="_blank" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <i class="fa-solid fa-file-medical"></i> Bulletin d'examen (PDF)
                                </a>
                            `;
                        }
                        if (data.documents.arret_travail) {
                            hasDocs = true;
                            btnContainer.innerHTML += `
                                <a href="${data.documents.arret_travail.url}" target="_blank" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5">
                                    <i class="fa-solid fa-file-signature"></i> Arrêt de travail (PDF)
                                </a>
                            `;
                        }
                    }

                    if (hasDocs) {
                        receipt.style.display = 'block';
                        receipt.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }

                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || "La consultation et les prescriptions ont été enregistrées avec succès !");
                } else {
                    alert(data.message || "Consultation et prescriptions enregistrées avec succès !");
                }

                if (typeof loadPendingOnlineRequests === 'function') {
                    loadPendingOnlineRequests();
                }
            } else {
                throw new Error(data.message || "Erreur lors de l'enregistrement de la consultation.");
            }
        } catch (error) {
            console.error("Erreur enregistrement consultation:", error);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk me-2 fs-16"></i> Enregistrer la consultation &amp; Transmettre';
            if (statusMsg) {
                statusMsg.className = 'fs-13 fw-bold text-danger';
                statusMsg.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i> ' + (error.message || "Erreur lors de l'enregistrement.");
            }
            alert(error.message || "Une erreur s'est produite lors de l'enregistrement de la consultation.");
        }
    }
</script>
