<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande d'Examen & Issue de Laboratoire - #{{ $consultation->id }}</title>
    <style>
        @page {
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .hospital-logo {
            max-height: 50px;
            max-width: 130px;
        }
        .hospital-title {
            font-size: 14px;
            font-weight: bold;
            color: #0369a1;
            text-transform: uppercase;
        }
        .hospital-sub {
            font-size: 9.5px;
            color: #64748b;
        }
        .doc-badge {
            display: inline-block;
            background-color: #e0f2fe;
            color: #0369a1;
            font-size: 9px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .main-title {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .bulletin-badge {
            font-size: 11px;
            font-weight: bold;
            color: #0284c7;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 10px;
        }
        .box-card {
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 7px 10px;
            background-color: #f8fafc;
        }
        .box-title {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0369a1;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 5px;
        }
        .field-row {
            margin-bottom: 2.5px;
        }
        .field-label {
            font-weight: bold;
            color: #475569;
            width: 35%;
            display: inline-block;
        }
        .field-val {
            color: #0f172a;
        }
        .issue-box {
            border: 1.5px solid #0284c7;
            background-color: #f0f9ff;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 10px;
        }
        .issue-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .exam-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .exam-table th {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 9.5px;
            text-transform: uppercase;
            padding: 5px 8px;
            text-align: left;
        }
        .exam-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 4.5px 8px;
            font-size: 10px;
        }
        .exam-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer-table {
            width: 100%;
            margin-top: 15px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
        }
        .signature-box {
            text-align: right;
            padding-right: 15px;
        }
        .signature-line {
            display: inline-block;
            margin-top: 30px;
            border-top: 1px dashed #64748b;
            padding-top: 3px;
            min-width: 170px;
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
        $registre = $consultation->registre;

        $patientFullName = trim((optional($patientUser)->name ?? '') . ' ' . (optional($patientUser)->prenom ?? '')) ?: 'Patient';
        $patientCode = optional($patient)->code_patient ?? ('#' . $consultation->id);
        $doctorFullName = 'Dr. ' . trim((optional($doctorUser)->name ?? '') . ' ' . (optional($doctorUser)->prenom ?? ''));
        $hospitalName = optional($hospital)->label ?: (optional($hospital)->nom_direction_generale ?: 'Hôpital GEMMA');
        $hospitalAddress = optional($hospital)->adresse ?: (optional(optional($hospital)->localiteCommune)->name ?? 'Abidjan');
        $hospitalPhone = optional($hospital)->telephone ?? '';
    @endphp

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
                <div class="main-title">Fiche de Demande d'Examen</div>
                <div class="bulletin-badge">Bulletin N° : {{ optional($bulletin)->code_bulletin ?? ('BLAB-' . $consultation->id) }}</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 2px;">Émis le : {{ date('d/m/Y à H:i', strtotime($consultation->created_at ?? date('Y-m-d H:i:s'))) }}</div>
            </td>
        </tr>
    </table>

    <!-- GRILLE D'INFORMATIONS : PATIENT & DEMANDEUR -->
    <table class="info-grid">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 5px;">
                <div class="box-card">
                    <div class="box-title">Informations du Patient</div>
                    <div class="field-row"><span class="field-label">Nom &amp; Prénoms :</span> <span class="field-val"><strong>{{ $patientFullName }}</strong></span></div>
                    <div class="field-row"><span class="field-label">N° Dossier :</span> <span class="field-val"><strong>{{ $patientCode }}</strong></span></div>
                    <div class="field-row"><span class="field-label">Date Naiss. / Âge :</span> <span class="field-val">{{ optional($patient)->birth_date ? $patient->birth_date : 'Non renseigné' }} ({{ ucfirst(optional($patient)->gender ?? 'Non précisé') }})</span></div>
                    <div class="field-row"><span class="field-label">Contact / Tél :</span> <span class="field-val">{{ optional($patient)->telephone ?: (optional($patientUser)->contact ?? 'Non renseigné') }}</span></div>
                    <div class="field-row"><span class="field-label">Résidence :</span> <span class="field-val">{{ optional(optional($patient)->residenceActuelle)->name ?? (optional($patient)->adresse ?? 'Abidjan') }}</span></div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 5px;">
                <div class="box-card">
                    <div class="box-title">Praticien Prescripteur</div>
                    <div class="field-row"><span class="field-label">Médecin :</span> <span class="field-val"><strong>{{ $doctorFullName }}</strong></span></div>
                    <div class="field-row"><span class="field-label">Service :</span> <span class="field-val">{{ optional(optional(optional($doctor)->serviceHospital)->service)->libelle ?? 'Laboratoire & Analyses' }}</span></div>
                    <div class="field-row"><span class="field-label">Hôpital :</span> <span class="field-val">{{ $hospitalName }}</span></div>
                    <div class="field-row"><span class="field-label">Consultation ID :</span> <span class="field-val">#{{ $consultation->code_consultation ?? $consultation->id }}</span></div>
                    <div class="field-row"><span class="field-label">Date émission :</span> <span class="field-val">{{ date('d/m/Y') }}</span></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- BLOC ISSUE DE SORTIE & ORIENTATION DU PATIENT -->
    <div class="issue-box">
        <div class="issue-title">Issue de Consultation &amp; Orientation du Patient</div>
        <div style="font-size: 10.5px;">
            <strong>Mode d'issue retenu :</strong> 
            @php
                $issue = optional($registre)->issue_consultation ?? 'sortie';
                $justif = optional($registre)->issue_consultation_justification ?? 'Examen de laboratoire émis';
            @endphp
            @if($issue === 'refere-interne' || str_contains($justif, 'infirmier') || str_contains($justif, 'orienté') || str_contains($justif, 'référé'))
                <span style="color: #0284c7; font-weight: bold;">Affectation / Référence interne</span>
            @elseif($issue === 'hospitalisation')
                <span style="color: #ea580c; font-weight: bold;">Hospitalisation</span>
            @elseif($issue === 'observation')
                <span style="color: #d97706; font-weight: bold;">Mise en observation</span>
            @else
                <span style="color: #16a34a; font-weight: bold;">Sortie autorisée vers le domicile</span>
            @endif
        </div>
        <div style="font-size: 10px; margin-top: 2px; color: #334155;">
            <strong>Détails &amp; Instructions :</strong> {{ $justif }}
        </div>
    </div>

    <!-- LISTE DES EXAMENS ENREGISTRES -->
    <div style="font-size: 10.5px; font-weight: bold; text-transform: uppercase; color: #0f172a; margin-bottom: 5px;">
        Analyses et Examens de Laboratoire Demandés ({{ optional(optional($bulletin)->examens)->count() ?? 0 }})
    </div>

    <table class="exam-table">
        <thead>
            <tr>
                <th style="width: 8%;">N°</th>
                <th style="width: 25%;">Code Examen</th>
                <th style="width: 52%;">Nature de l'Examen / Analyse Demandée</th>
                <th style="width: 15%; text-align: center;">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse(optional($bulletin)->examens ?? [] as $index => $exam)
                <tr>
                    <td><strong>{{ $index + 1 }}</strong></td>
                    <td><span style="font-family: monospace; color: #0369a1;">{{ $exam->code_examen ?? ('EX-' . $exam->id) }}</span></td>
                    <td><strong>{{ $exam->nature_examen }}</strong></td>
                    <td style="text-align: center;">
                        <span style="color: #ea580c; font-weight: bold;">En attente</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #64748b; padding: 8px;">
                        Demande de bilan général de laboratoire.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($consultation->motif_consultation))
        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 5px 8px; margin-bottom: 8px;">
            <span style="font-weight: bold; color: #475569; font-size: 9.5px;">Renseignements / Notes cliniques :</span>
            <span style="font-size: 9.5px; color: #0f172a;">{{ $consultation->motif_consultation }}</span>
        </div>
    @endif

    <!-- SIGNATURE ET PIED DE PAGE -->
    <table class="footer-table">
        <tr>
            <td style="width: 50%; vertical-align: top; font-size: 9px; color: #64748b;">
                Document généré automatiquement via la plateforme hospitalière GEMMA.<br>
                Ce bulletin officiel doit être présenté au laboratoire pour la réalisation des analyses.
            </td>
            <td style="width: 50%; vertical-align: top;" class="signature-box">
                <div style="font-size: 9.5px; color: #475569;">Fait à {{ $hospitalAddress }}, le {{ date('d/m/Y') }}</div>
                <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">{{ $doctorFullName }}</div>
                <div class="signature-line">Signature &amp; Cachet du Médecin</div>
            </td>
        </tr>
    </table>

</body>
</html>
