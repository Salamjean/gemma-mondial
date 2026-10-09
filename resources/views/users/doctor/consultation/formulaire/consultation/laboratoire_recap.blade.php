@extends('layouts.dashboard', ['title' => "Récapitulatif & Résultat d'Analyse - Laboratoire"])

@section('content')
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
    $examList = optional($bulletin)->examens ?? collect();

    // Décodage ultra-robuste des données enregistrées
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

    // Extraction robuste des échantillons et quantités
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

    // Traitement médicamenteux & dose
    $traitement = $laboData['traitement_medicamenteux'] ?? null;
    $derniereDose = $laboData['derniere_dose'] ?? null;
    $derniereDoseDate = $laboData['derniere_dose_date'] ?? null;
    $derniereDoseHeure = $laboData['derniere_dose_heure'] ?? null;

    // Liste des analyses de laboratoire standards (les examens supplémentaires sont affichés dans leur propre carte dédiée)
    $allExams = collect($examList)->filter(function($item) {
        $nature = $item->nature_examen ?? '';
        return !str_contains($nature, 'Examen complémentaire') && !str_contains($nature, 'Examen supplémentaire');
    })->values();
    $totalExamCount = $allExams->count();

    // Recherche d'un résultat d'analyse déjà téléversé
    $resultatFile = null;
    $resultatObservation = null;
    foreach ($allExams as $ex) {
        if (!empty($ex->resultat)) {
            $resultatFile = $ex->resultat;
        }
        if (!empty($ex->observation)) {
            $resultatObservation = $ex->observation;
        }
        if ($resultatFile) break;
    }
@endphp

<style>
    .labo-table-centered th,
    .labo-table-centered td {
        text-align: center !important;
        vertical-align: middle !important;
    }
    .labo-field-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .labo-field-card:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .labo-field-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .labo-field-val {
        font-size: 13px;
        font-weight: 600;
        color: #1e293b;
    }

    /* Grille des 3 cartes sur la même ligne */
    .labo-three-cards-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        width: 100%;
        margin-bottom: 20px;
    }
    @media (max-width: 991px) {
        .labo-three-cards-grid {
            grid-template-columns: 1fr;
        }
    }
    .labo-sub-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .labo-sub-box-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 12px;
        font-size: 12.5px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .labo-sub-box-body {
        padding: 10px 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        font-size: 12.5px;
    }
</style>

<div class="content-header mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="page-title text-dark fw-bold mb-1">
                <i class="fa-solid fa-flask-vial text-primary me-2"></i> Récapitulatif de Consultation &bull; Laboratoire
            </h4>
            <div class="text-muted fs-13">
                Demande N° <strong>{{ optional($bulletin)->code_bulletin ?? ('BLAB-' . $consultation->id) }}</strong> &bull; Patient : <strong>{{ $patientFullName }}</strong> (<span class="font-monospace">{{ $patientCode }}</span>)
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Consultations du jour
            </a>
            <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-primary btn-sm px-3 fw-bold">
                <i class="fa-solid fa-file-pdf me-1"></i> Télécharger le bon PDF
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border border-success-subtle p-3 mb-4 rounded shadow-sm" role="alert">
        <div class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-circle-check fs-22 text-success"></i>
            <div class="fs-14 fw-semibold text-dark">{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- COLONNE GAUCHE (8) : TOUS LES DETAILS, LES 3 CARTES ET LE TABLEAU DES ANALYSES -->
    <div class="col-lg-8 col-12">

        <!-- 1. SECTION UPLOAD DU RESULTAT DE L'ANALYSE (MISE EN AVANT) -->
        <div class="box mb-3 shadow-sm border border-primary-subtle" style="background: #ffffff; border-radius: 8px; overflow: hidden;">
            <div class="box-header bg-primary text-white d-flex justify-content-between align-items-center py-2.5 px-3">
                <h5 class="box-title fs-14 fw-bold text-white mb-0">
                    <i class="fa-solid fa-file-arrow-up me-2"></i> Résultat d'Analyse de Laboratoire
                </h5>
                @if($resultatFile)
                    <span class="badge bg-success text-white border border-light fs-11">
                        <i class="fa-solid fa-check-circle me-1"></i> Résultat disponible
                    </span>
                @else
                    <span class="badge bg-warning text-dark border fs-11">
                        <i class="fa-solid fa-clock me-1"></i> En attente de résultat
                    </span>
                @endif
            </div>
            <div class="box-body p-3">
                @if($resultatFile)
                    <!-- Résultat déjà présent -->
                    <div class="p-2.5 bg-light rounded border border-success-subtle mb-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="text-success fs-26">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </div>
                                <div>
                                    <div class="fw-bold fs-13 text-dark">Document de résultat téléversé</div>
                                    <div class="text-muted fs-11 font-monospace">{{ $resultatFile }}</div>
                                    @if($resultatObservation)
                                        <div class="text-secondary fs-12 mt-0.5"><strong>Note :</strong> {{ $resultatObservation }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ asset('assets/uploads/analyses/' . $resultatFile) }}" target="_blank" class="btn btn-success btn-sm fw-bold px-3">
                                    <i class="fa-solid fa-eye me-1"></i> Voir le résultat
                                </a>
                                <a href="{{ asset('assets/uploads/analyses/' . $resultatFile) }}" download class="btn btn-outline-secondary btn-sm px-2.5">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Formulaire d'upload / mise à jour du résultat -->
                <form action="{{ route('doctor.consultation.laboratoire.upload_resultat', $consultation->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-2.5 bg-light-subtle rounded border">
                        <div class="fw-bold fs-12.5 text-dark mb-2">
                            <i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> 
                            {{ $resultatFile ? 'Remplacer ou mettre à jour le fichier :' : 'Téléverser le fichier du résultat d\'analyse :' }}
                        </div>
                        
                        <div class="row g-2 align-items-end">
                            <div class="col-md-7 col-12">
                                <label class="form-label fs-11.5 text-muted fw-semibold mb-1">
                                    Fichier du résultat (PDF, JPG, PNG, DOCX) <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="fichier_resultat" class="form-control form-control-sm" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            </div>
                            <div class="col-md-5 col-12">
                                <label class="form-label fs-11.5 text-muted fw-semibold mb-1">
                                    Conclusion / Note rapide
                                </label>
                                <input type="text" name="commentaire_resultat" class="form-control form-control-sm" placeholder="Optionnel...">
                            </div>
                            <div class="col-12 text-end mt-2">
                                <button type="submit" class="btn btn-primary btn-sm px-3.5 fw-bold shadow-sm">
                                    <i class="fa-solid fa-upload me-1"></i> Enregistrer le Résultat
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. CONDITIONS DU PRELEVEMENT & RECEPTION -->
        <div class="box mb-3 shadow-sm border" style="border-radius: 8px; overflow: hidden;">
            <div class="box-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                <h5 class="box-title fs-13 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-clipboard-check text-primary me-1.5"></i> Conditions de Prélèvement &amp; Réception
                </h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11">
                    Général
                </span>
            </div>
            <div class="box-body p-2.5 fs-12.5">
                <div class="row g-2">
                    <!-- Date de réception du formulaire -->
                    <div class="col-md-3 col-6">
                        <div class="labo-field-card">
                            <div class="labo-field-title"><i class="fa-regular fa-calendar-check text-primary me-1"></i> Reçu le</div>
                            <div class="labo-field-val">
                                {{ date('d/m/Y', strtotime($laboData['date_reception'] ?? $consultation->date_consultation ?? date('Y-m-d'))) }}
                            </div>
                        </div>
                    </div>

                    <!-- Date & Heure du prélèvement -->
                    <div class="col-md-3 col-6">
                        <div class="labo-field-card">
                            <div class="labo-field-title"><i class="fa-regular fa-clock text-primary me-1"></i> Prélèvement</div>
                            <div class="labo-field-val">
                                {{ date('d/m/Y', strtotime($laboData['date_prelevement'] ?? $consultation->date_consultation ?? date('Y-m-d'))) }}
                                à {{ $laboData['heure_prelevement'] ?? date('H:i') }}
                            </div>
                        </div>
                    </div>

                    <!-- Condition du patient (À jeun) -->
                    <div class="col-md-3 col-6">
                        <div class="labo-field-card">
                            <div class="labo-field-title"><i class="fa-solid fa-utensils text-primary me-1"></i> Condition</div>
                            <div class="labo-field-val">
                                @if(($laboData['a_jeun'] ?? 'A jeun') === 'A jeun')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-0.5 fs-11.5">
                                        <i class="fa-solid fa-mug-saucer me-1"></i> À jeun
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-0.5 fs-11.5">
                                        Non à jeun
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Degré d'urgence -->
                    <div class="col-md-3 col-6">
                        <div class="labo-field-card">
                            <div class="labo-field-title"><i class="fa-solid fa-triangle-exclamation text-primary me-1"></i> Urgence</div>
                            <div class="labo-field-val">
                                @if(($laboData['urgence'] ?? 'Normal') === 'URGENT' || ($laboData['urgence'] ?? 'Normal') === 'Urgent')
                                    <span class="badge bg-danger text-white px-2 py-0.5 fs-11.5">
                                        <i class="fa-solid fa-bolt me-1"></i> URGENT
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5 fs-11.5">
                                        <i class="fa-solid fa-check me-1"></i> Normal
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. LES 3 CARTES SUR LA MÊME LIGNE (GRILLE PARFAITEMENT ÉQUILIBRÉE ET PROPORTIONNÉE) -->
        <div class="labo-three-cards-grid">
            
            <!-- CARTE 1 : MOTIF / RENSEIGNEMENTS CLINIQUES & DIAGNOSTIC PRESOMPTIF -->
            <div class="labo-sub-box">
                <div class="labo-sub-box-header">
                    <span><i class="fa-solid fa-comment-medical text-primary me-1.5"></i> Motif &amp; Clinique</span>
                </div>
                <div class="labo-sub-box-body">
                    <div class="p-2 bg-light rounded border text-dark fs-12 mb-2 flex-grow-1">
                        <div class="text-muted fs-11 fw-bold text-uppercase mb-1">Renseignements :</div>
                        <div class="fw-semibold" style="line-height: 1.4;">
                            {{ $laboData['motif_clinique'] ?? $consultation->motif_consultation ?? 'Aucun motif particulier renseigné.' }}
                        </div>
                    </div>
                    @if(!empty($laboData['anapath_site']))
                        <div class="p-1.5 px-2 bg-light-subtle rounded border d-flex align-items-center justify-content-between fs-11.5 mt-auto">
                            <span class="text-muted fw-bold text-uppercase">Site Anapath :</span>
                            <strong class="text-dark">{{ $laboData['anapath_site'] }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- CARTE 2 : TRAITEMENT MEDICAMENTEUX & DERNIERE PRISE -->
            <div class="labo-sub-box">
                <div class="labo-sub-box-header">
                    <span><i class="fa-solid fa-pills text-warning me-1.5"></i> Traitement &amp; Prise</span>
                </div>
                <div class="labo-sub-box-body">
                    <div class="p-2 bg-light rounded border mb-2">
                        <div class="text-muted fs-11 fw-bold text-uppercase mb-0.5">
                            <i class="fa-solid fa-capsules text-warning me-1"></i> En cours
                        </div>
                        <div class="fw-bold text-dark fs-12 text-truncate" title="{{ $traitement }}">
                            {{ !empty($traitement) ? $traitement : 'Aucun' }}
                        </div>
                    </div>
                    <div class="p-2 bg-light rounded border mt-auto">
                        <div class="text-muted fs-11 fw-bold text-uppercase mb-0.5">
                            <i class="fa-solid fa-clock-rotate-left text-primary me-1"></i> Dernière prise
                        </div>
                        <div class="fw-bold text-dark fs-12">
                            @if(!empty($derniereDose))
                                {{ $derniereDose }}
                                @if(!empty($derniereDoseDate))
                                    <div class="text-muted fs-11 fw-normal">{{ date('d/m/Y', strtotime($derniereDoseDate)) }} {{ $derniereDoseHeure ?? '' }}</div>
                                @endif
                            @else
                                <span class="text-muted fst-italic fw-normal">Non précisé</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARTE 3 : NATURE DES PRELEVEMENTS & QUANTITES PRELEVEES -->
            <div class="labo-sub-box">
                <div class="labo-sub-box-header">
                    <span><i class="fa-solid fa-vials text-info me-1.5"></i> Natures &amp; Quantités</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-10 px-1.5 py-0.5">
                        {{ count($echantillons) }}
                    </span>
                </div>
                <div class="labo-sub-box-body">
                    <div class="d-flex flex-column gap-1.5">
                        @forelse($echantillons as $ech)
                            @php
                                $parts = explode(' (Quantité : ', $ech);
                                $echName = trim($parts[0]);
                                $echQty = isset($parts[1]) ? trim(rtrim($parts[1], ')')) : '';
                            @endphp
                            <div class="d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border fs-11.5">
                                <span class="fw-bold text-dark text-truncate me-1">
                                    <i class="fa-solid fa-droplet text-danger me-1"></i> {{ $echName }}
                                </span>
                                @if(!empty($echQty))
                                    <span class="badge bg-primary text-white font-monospace fs-10 px-1.5 py-0.5 flex-shrink-0">{{ $echQty }}</span>
                                @else
                                    <span class="badge bg-white text-secondary border fs-10 px-1 py-0 flex-shrink-0">Prélevé</span>
                                @endif
                            </div>
                        @empty
                            <div class="p-2 text-center text-muted fs-11.5 fst-italic">
                                Prélèvement standard.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- 4. TABLEAU DES ANALYSES DEMANDEES (PARFAITEMENT CENTRE) -->
        <div class="box mb-3 shadow-sm border" style="border-radius: 8px; overflow: hidden;">
            <div class="box-header bg-light d-flex justify-content-between align-items-center py-2.5 px-3 border-bottom">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Analyses demandées ({{ $totalExamCount }})
                </h5>
                <span class="badge bg-light text-secondary border fs-11">
                    Bulletin N° {{ optional($bulletin)->code_bulletin ?? ('BLAB-' . $consultation->id) }}
                </span>
            </div>

            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center labo-table-centered">
                        <thead class="bg-light text-muted fs-11 text-uppercase text-center">
                            <tr>
                                <th class="text-center py-3" style="width: 70px; text-align: center !important;">#</th>
                                <th class="text-center py-3" style="width: 200px; text-align: center !important;">Code Examen</th>
                                <th class="text-center py-3" style="text-align: center !important;">Nature de l'Analyse</th>
                                <th class="text-center py-3" style="width: 180px; text-align: center !important;">Date prescrite</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13 text-center">
                            @forelse($allExams as $index => $exam)
                                <tr>
                                    <td class="text-center py-3 fw-bold text-muted" style="text-align: center !important;">{{ $index + 1 }}</td>
                                    <td class="text-center py-3" style="text-align: center !important;">
                                        <span class="badge bg-light text-dark border font-monospace px-2.5 py-1.5 fs-12">{{ $exam->code_examen ?? ('EX-' . ($exam->id ?? ($index + 1))) }}</span>
                                    </td>
                                    <td class="text-center py-3 fw-bold text-dark" style="text-align: center !important;">
                                        {{ $exam->nature_examen }}
                                    </td>
                                    <td class="text-center py-3 text-muted fs-12" style="text-align: center !important;">
                                        <i class="fa-regular fa-calendar-check me-1 text-primary"></i>
                                        {{ date('d/m/Y', strtotime($exam->date_examen ?? date('Y-m-d'))) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4" style="text-align: center !important;">
                                        Aucune analyse de laboratoire enregistrée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- COLONNE DROITE (4) : ACTIONS RAPIDES & SYNTHÈSE PATIENT & ÉTABLISSEMENT & EXAMENS SUPPLÉMENTAIRES -->
    <div class="col-lg-4 col-12">

        <!-- CARTE D'ACTIONS PRINCIPALES -->
        <div class="box mb-4 shadow-sm border" style="border-radius: 8px;">
            <div class="box-header bg-light py-2.5 px-3 border-bottom">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-bolt text-primary me-2"></i> Actions rapides
                </h5>
            </div>
            <div class="box-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-primary fw-bold py-2">
                        <i class="fa-solid fa-file-pdf me-2"></i> Télécharger la demande PDF
                    </a>
                    <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary fw-semibold py-2">
                        <i class="fa-solid fa-list me-2"></i> Consultations du jour
                    </a>
                </div>
            </div>
        </div>

        <!-- CARTE SYNTHESE DU PATIENT -->
        <div class="box mb-4 shadow-sm border" style="border-radius: 8px;">
            <div class="box-header bg-light py-2.5 px-3 border-bottom">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-user text-primary me-2"></i> Informations du Patient
                </h5>
            </div>
            <div class="box-body p-3 fs-13">
                <div class="mb-2">
                    <div class="fw-bold fs-15 text-dark">{{ $patientFullName }}</div>
                    <div class="text-muted fs-12 font-monospace">{{ $patientCode }}</div>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Genre :</span>
                    <strong class="text-dark">{{ ucfirst(optional($patient)->gender ?? 'Non précisé') }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Date de Naissance :</span>
                    <strong class="text-dark">{{ optional($patient)->birth_date ?: 'Non renseigné' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Téléphone :</span>
                    <strong class="text-dark">{{ optional($patient)->telephone ?: (optional($patientUser)->contact ?? 'Non renseigné') }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Résidence :</span>
                    <strong class="text-dark">{{ optional(optional($patient)->residenceActuelle)->name ?? (optional($patient)->adresse ?? 'Abidjan') }}</strong>
                </div>
            </div>
        </div>

        <!-- CARTE PRATICIEN & ETABLISSEMENT -->
        <div class="box mb-4 shadow-sm border" style="border-radius: 8px;">
            <div class="box-header bg-light py-2.5 px-3 border-bottom">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-hospital text-primary me-2"></i> Établissement &amp; Praticien
                </h5>
            </div>
            <div class="box-body p-3 fs-13">
                <div class="fw-bold text-dark mb-1">{{ $hospitalName }}</div>
                <div class="text-muted fs-12 mb-2">{{ optional($hospital)->adresse ?: 'Abidjan' }}</div>
                <hr class="my-2">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Praticien :</span>
                    <strong class="text-dark">{{ $doctorFullName }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Service :</span>
                    <strong class="text-dark">{{ optional(optional(optional($doctor)->serviceHospital)->service)->libelle ?? 'Laboratoire' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Date :</span>
                    <strong class="text-dark">{{ date('d/m/Y H:i') }}</strong>
                </div>
            </div>
        </div>

        <!-- CARTE EXAMENS SUPPLEMENTAIRES DEMANDES (EN BAS DE ETABLISSEMENT & PRATICIEN) -->
        @if(!empty($laboData['examens_supplementaires']))
            <div class="box mb-4 shadow-sm border" style="border-radius: 8px;">
                <div class="box-header bg-light py-2.5 px-3 border-bottom">
                    <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                        <i class="fa-solid fa-list-check text-primary me-2"></i> Examens supplémentaires
                    </h5>
                </div>
                <div class="box-body p-3 fs-13">
                    <div class="p-2.5 bg-light rounded border text-dark fw-semibold" style="white-space: pre-line; line-height: 1.5;">
                        {{ $laboData['examens_supplementaires'] }}
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
