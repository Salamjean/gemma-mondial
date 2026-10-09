<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande d'Examen de Laboratoire - #{{ $consultation->id }}</title>
    <style>
        @page {
            margin: 10mm 12mm 10mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1e293b;
            line-height: 1.35;
        }
        #watermark {
            position: fixed;
            top: 250px;
            left: 30px;
            width: 100%;
            height: 100%;
            z-index: -2000;
            opacity: 0.18;
            text-align: center;
        }
        .img__content {
            width: 85%;
            height: 40%;
            object-fit: contain;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .hospital-logo {
            max-height: 45px;
            max-width: 120px;
        }
        .hospital-title {
            font-size: 13.5px;
            font-weight: bold;
            color: #0369a1;
            text-transform: uppercase;
        }
        .hospital-sub {
            font-size: 9px;
            color: #64748b;
        }
        .doc-badge {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            font-size: 8.5px;
            font-weight: bold;
            padding: 2px 5px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .main-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .bulletin-badge {
            font-size: 10.5px;
            font-weight: bold;
            color: #0284c7;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 8px;
        }
        .box-card {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            background-color: #f8fafc;
        }
        .box-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0369a1;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
        .field-row {
            margin-bottom: 2px;
            font-size: 9.5px;
        }
        .field-label {
            font-weight: bold;
            color: #475569;
            width: 38%;
            display: inline-block;
        }
        .field-val {
            color: #0f172a;
        }
        .conditions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .conditions-table td {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 5px 8px;
            font-size: 9.5px;
            width: 25%;
        }
        .cond-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .cond-val {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
        }
        .three-cards-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
            margin-bottom: 8px;
            margin-left: -6px;
            margin-right: -6px;
        }
        .three-cards-table td {
            width: 33.33%;
            vertical-align: top;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            border-radius: 4px;
            padding: 6px 8px;
        }
        .sub-card-title {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0369a1;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
        .exam-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .exam-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            padding: 5px 6px;
            text-align: center;
        }
        .exam-table td {
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            padding: 4px 6px;
            font-size: 9.5px;
            text-align: center;
        }
        .exam-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .extra-box {
            border: 1px solid #cbd5e1;
            background-color: #fefce8;
            border-left: 3px solid #eab308;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 8px;
        }
        .extra-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #854d0e;
            margin-bottom: 3px;
        }
        .footer-table {
            width: 100%;
            margin-top: 10px;
            border-top: 1px solid #cbd5e1;
            padding-top: 6px;
        }
        .signature-box {
            text-align: right;
            padding-right: 15px;
        }
        .signature-line {
            display: inline-block;
            margin-top: 25px;
            border-top: 1px dashed #64748b;
            padding-top: 3px;
            min-width: 160px;
            font-weight: bold;
            font-size: 9.5px;
        }
        .badge-urgent {
            color: #dc2626;
            font-weight: bold;
        }
        .badge-normal {
            color: #16a34a;
            font-weight: bold;
        }
    </style>
</head>
<body>

    @php
        $patient = $consultation->patient ?? optional($consultation->admission)->patient;
        $patientUser = optional($patient)->user;
        $doctor = $consultation->doctor ?? optional(Auth::user())->doctor;
        $doctorUser = optional($doctor)->user ?? Auth::user();
        $hospital = $consultation->hospital ?? optional($doctor)->hospital ?? Auth::user()->hospital;
        $bulletin = $consultation->bulletinExamen;

        $patientFullName = trim((optional($patientUser)->name ?? '') . ' ' . (optional($patientUser)->prenom ?? '')) ?: 'Patient';
        $patientCode = optional($patient)->code_patient ?? ('#' . $consultation->id);
        $doctorFullName = 'Dr. ' . trim((optional($doctorUser)->name ?? '') . ' ' . (optional($doctorUser)->prenom ?? ''));
        $hospitalName = optional($hospital)->label ?: (optional($hospital)->nom_direction_generale ?: 'Hôpital GEMMA');
        $hospitalAddress = optional($hospital)->adresse ?: (optional(optional($hospital)->localiteCommune)->name ?? 'Abidjan');
        $hospitalPhone = optional($hospital)->telephone ?? '';

        // Filigrane (Watermark) de l'hôpital ou par défaut
        $hospWatermark = optional($hospital)->watermark_url ?? optional($consultation->hospital)->watermark_url ?? null;

        // Décodage des métadonnées du formulaire de laboratoire
        $laboData = [];
        $rawObs = $consultation->observation_infirmiere;
        if (!empty($rawObs)) {
            if (is_array($rawObs)) {
                $laboData = $rawObs;
            } else {
                $d1 = json_decode($rawObs, true);
                if (is_array($d1)) {
                    $laboData = $d1;
                } elseif (is_string($d1)) {
                    $d2 = json_decode($d1, true);
                    if (is_array($d2)) {
                        $laboData = $d2;
                    }
                }
            }
        }

        // Échantillons et quantités
        $echantillons = $laboData['echantillons'] ?? [];
        if (empty($echantillons) && !empty($laboData['quantites']) && is_array($laboData['quantites'])) {
            foreach ($laboData['quantites'] as $k => $v) {
                if (!empty(trim($v))) {
                    $echantillons[] = ucfirst($k) . ' (Quantité : ' . trim($v) . ')';
                }
            }
        }
        if (!empty($laboData['echantillon_autre'])) {
            $autreVal = $laboData['echantillon_autre'];
            $alreadyThere = false;
            foreach ($echantillons as $e) {
                if (str_contains($e, $autreVal)) { $alreadyThere = true; break; }
            }
            if (!$alreadyThere) {
                $echantillons[] = $autreVal;
            }
        }

        // Traitement
        $traitement = $laboData['traitement_medicamenteux'] ?? null;
        $derniereDose = $laboData['derniere_dose'] ?? null;
        $derniereDoseDate = $laboData['derniere_dose_date'] ?? null;
        $derniereDoseHeure = $laboData['derniere_dose_heure'] ?? null;

        // Liste des analyses de laboratoire (sans les examens supplémentaires qui ont leur encadré dédié)
        $examList = optional($bulletin)->examens ?? collect();
        $allExams = collect($examList)->filter(function($item) {
            $nature = $item->nature_examen ?? '';
            return !str_contains($nature, 'Examen complémentaire') && !str_contains($nature, 'Examen supplémentaire');
        })->values();
        $totalExamCount = $allExams->count();
    @endphp

    <!-- FILIGRANE EN ARRIERE-PLAN (WATERMARK) -->
    <div id="watermark">
        @if($hospWatermark)
            <img class="img__content" src="{{ pdf_img('assets/uploads/hospital/' . $hospWatermark) }}" />
        @else
            <img class="img__content" src="{{ pdf_img('assets/uploads/ordonnance.jpg') }}" />
        @endif
    </div>

    <!-- EN-TETE OFFICIEL -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                @if(!empty(optional($hospital)->img_url) && file_exists(public_path('assets/uploads/hospital/' . $hospital->img_url)))
                    <img src="{{ public_path('assets/uploads/hospital/' . $hospital->img_url) }}" class="hospital-logo" alt="Logo">
                @else
                    <div class="hospital-title">{{ $hospitalName }}</div>
                @endif
                <div class="hospital-sub">{{ $hospitalAddress }} {{ $hospitalPhone ? ' | Tél : ' . $hospitalPhone : '' }}</div>
                <div class="hospital-sub">Service de Biologie Médicale &amp; Analyses de Laboratoire</div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <div class="doc-badge">Document Médical Officiel</div>
                <div class="main-title">Demande d'Examen de Laboratoire</div>
                <div class="bulletin-badge">Bulletin N° : {{ optional($bulletin)->code_bulletin ?? ('BLAB-' . $consultation->id) }}</div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">Émis le : {{ date('d/m/Y à H:i', strtotime($consultation->created_at ?? date('Y-m-d H:i:s'))) }}</div>
            </td>
        </tr>
    </table>

    <!-- GRILLE D'INFORMATIONS : PATIENT & DEMANDEUR -->
    <table class="info-grid">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 4px;">
                <div class="box-card">
                    <div class="box-title">Informations du Patient</div>
                    <div class="field-row"><span class="field-label">Nom &amp; Prénoms :</span> <span class="field-val"><strong>{{ $patientFullName }}</strong></span></div>
                    <div class="field-row"><span class="field-label">N° Dossier :</span> <span class="field-val"><strong>{{ $patientCode }}</strong></span></div>
                    <div class="field-row"><span class="field-label">Date Naiss. / Âge :</span> <span class="field-val">{{ optional($patient)->birth_date ? $patient->birth_date : 'Non renseigné' }} ({{ ucfirst(optional($patient)->gender ?? 'Non précisé') }})</span></div>
                    <div class="field-row"><span class="field-label">Contact / Tél :</span> <span class="field-val">{{ optional($patient)->telephone ?: (optional($patientUser)->contact ?? 'Non renseigné') }}</span></div>
                    <div class="field-row"><span class="field-label">Résidence :</span> <span class="field-val">{{ optional(optional($patient)->residenceActuelle)->name ?? (optional($patient)->adresse ?? 'Abidjan') }}</span></div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 4px;">
                <div class="box-card">
                    <div class="box-title">Praticien Prescripteur &amp; Établissement</div>
                    <div class="field-row"><span class="field-label">Médecin :</span> <span class="field-val"><strong>{{ $doctorFullName }}</strong></span></div>
                    <div class="field-row"><span class="field-label">Service :</span> <span class="field-val">{{ optional(optional(optional($doctor)->serviceHospital)->service)->libelle ?? 'Laboratoire & Analyses' }}</span></div>
                    <div class="field-row"><span class="field-label">Hôpital :</span> <span class="field-val">{{ $hospitalName }}</span></div>
                    <div class="field-row"><span class="field-label">Consultation ID :</span> <span class="field-val">#{{ $consultation->code_consultation ?? $consultation->id }}</span></div>
                    <div class="field-row"><span class="field-label">Date prescription :</span> <span class="field-val">{{ date('d/m/Y') }}</span></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- CONDITIONS DE PRELEVEMENT & RECEPTION -->
    <table class="conditions-table">
        <tr>
            <td>
                <div class="cond-title">Reçu le</div>
                <div class="cond-val">{{ date('d/m/Y', strtotime($laboData['date_reception'] ?? $consultation->date_consultation ?? date('Y-m-d'))) }}</div>
            </td>
            <td>
                <div class="cond-title">Prélèvement</div>
                <div class="cond-val">{{ date('d/m/Y', strtotime($laboData['date_prelevement'] ?? $consultation->date_consultation ?? date('Y-m-d'))) }} à {{ $laboData['heure_prelevement'] ?? date('H:i') }}</div>
            </td>
            <td>
                <div class="cond-title">Condition</div>
                <div class="cond-val">{{ ($laboData['a_jeun'] ?? 'A jeun') === 'A jeun' ? 'À jeun' : 'Non à jeun' }}</div>
            </td>
            <td>
                <div class="cond-title">Degré d'Urgence</div>
                <div class="cond-val">
                    @if(($laboData['urgence'] ?? 'Normal') === 'URGENT' || ($laboData['urgence'] ?? 'Normal') === 'Urgent')
                        <span class="badge-urgent">URGENT</span>
                    @else
                        <span class="badge-normal">Normal</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- LES 3 BLOCS : MOTIF/CLINIQUE, TRAITEMENT & PRISES, NATURES & QUANTITES -->
    <table class="three-cards-table">
        <tr>
            <!-- BLOC 1 : MOTIF & CLINIQUE -->
            <td>
                <div class="sub-card-title">Motif &amp; Clinique</div>
                <div style="font-size: 9px; color: #475569; margin-bottom: 2px; font-weight: bold;">Renseignements :</div>
                <div style="font-size: 9.5px; color: #0f172a; font-weight: 500;">
                    {{ $laboData['motif_clinique'] ?? $consultation->motif_consultation ?? 'Non spécifié' }}
                </div>
                @if(!empty($laboData['anapath_site']))
                    <div style="margin-top: 4px; font-size: 9px; border-top: 1px dashed #e2e8f0; padding-top: 2px;">
                        <span style="font-weight: bold; color: #475569;">Site Anapath :</span> <strong>{{ $laboData['anapath_site'] }}</strong>
                    </div>
                @endif
            </td>

            <!-- BLOC 2 : TRAITEMENT & DERNIÈRE PRISE -->
            <td>
                <div class="sub-card-title">Traitement &amp; Prise</div>
                <div style="font-size: 9px; color: #475569; margin-bottom: 1px; font-weight: bold;">En cours :</div>
                <div style="font-size: 9.5px; color: #0f172a; font-weight: bold; margin-bottom: 3px;">
                    {{ !empty($traitement) ? $traitement : 'Aucun' }}
                </div>
                <div style="font-size: 9px; color: #475569; margin-bottom: 1px; font-weight: bold; border-top: 1px dashed #e2e8f0; padding-top: 2px;">Dernière prise :</div>
                <div style="font-size: 9px; color: #0f172a;">
                    @if(!empty($derniereDose))
                        <strong>{{ $derniereDose }}</strong>
                        @if(!empty($derniereDoseDate))
                            <span style="color: #64748b;">({{ date('d/m/Y', strtotime($derniereDoseDate)) }} {{ $derniereDoseHeure ?? '' }})</span>
                        @endif
                    @else
                        <span style="color: #64748b; font-style: italic;">Non précisé</span>
                    @endif
                </div>
            </td>

            <!-- BLOC 3 : NATURE DES PRELEVEMENTS & QUANTITES -->
            <td>
                <div class="sub-card-title">Natures &amp; Quantités ({{ count($echantillons) }})</div>
                @forelse($echantillons as $ech)
                    <div style="font-size: 9px; margin-bottom: 1.5px; padding-bottom: 1px; border-bottom: 1px dotted #f1f5f9;">
                        &bull; <strong>{{ $ech }}</strong>
                    </div>
                @empty
                    <div style="font-size: 9px; color: #64748b; font-style: italic;">Prélèvement standard</div>
                @endforelse
            </td>
        </tr>
    </table>

    <!-- LISTE DES ANALYSES DEMANDEES (PARFAITEMENT CENTREE) -->
    <div style="font-size: 10px; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-bottom: 4px; margin-top: 4px;">
        Analyses et Examens de Laboratoire Demandés ({{ $totalExamCount }})
    </div>

    <table class="exam-table">
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 25%;">Code Examen</th>
                <th style="width: 45%;">Nature de l'Analyse</th>
                <th style="width: 22%;">Date prescrite</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allExams as $index => $exam)
                <tr>
                    <td style="font-weight: bold; color: #64748b;">{{ $index + 1 }}</td>
                    <td><span style="font-family: monospace; color: #0369a1; font-weight: bold;">{{ $exam->code_examen ?? ('EX-' . ($exam->id ?? ($index + 1))) }}</span></td>
                    <td style="font-weight: bold; color: #0f172a;">{{ $exam->nature_examen }}</td>
                    <td style="color: #64748b; font-size: 9px;">{{ date('d/m/Y', strtotime($exam->date_examen ?? date('Y-m-d'))) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b; padding: 6px;">
                        Aucune analyse de laboratoire enregistrée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- EXAMENS SUPPLEMENTAIRES DEMANDES -->
    @if(!empty($laboData['examens_supplementaires']))
        <div class="extra-box">
            <div class="extra-title">Examens supplémentaires demandés :</div>
            <div style="font-size: 9.5px; color: #0f172a; font-weight: bold; white-space: pre-line;">
                {{ $laboData['examens_supplementaires'] }}
            </div>
        </div>
    @endif

    <!-- SIGNATURE ET PIED DE PAGE -->
    <table class="footer-table">
        <tr>
            <td style="width: 55%; vertical-align: top; font-size: 8.5px; color: #64748b;">
                Document généré automatiquement via la plateforme hospitalière GEMMA.<br>
                Ce bulletin officiel doit être présenté au laboratoire pour la réalisation des analyses.
            </td>
            <td style="width: 45%; vertical-align: top;" class="signature-box">
                <div style="font-size: 9px; color: #475569;">Fait à {{ $hospitalAddress }}, le {{ date('d/m/Y') }}</div>
                <div style="font-size: 9.5px; font-weight: bold; margin-top: 1px;">{{ $doctorFullName }}</div>
                <div class="signature-line">Signature &amp; Cachet du Médecin</div>
            </td>
        </tr>
    </table>

</body>
</html>
