@extends('layouts.dashboard', ['title' => 'Parcours de l\'Intervention ' . ($consultation->code_consultation ?? 'CONS-' . $consultation->id)])

@push('css')
<style>
    .patient-header-box {
        background: #ffffff;
        border-radius: 14px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .patient-avatar-img {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #3b82f6;
    }
    .parcours-card {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        margin-bottom: 24px;
        background: #ffffff;
        overflow: hidden;
    }
    .parcours-card .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 18px 24px;
    }
    .parcours-card .card-body {
        padding: 24px;
    }
</style>
@endpush

@section('content')
@php
    $ageStr = 'N/A';
    if (!empty($patient->birth_date)) {
        try {
            $dateNaissance = \Carbon\Carbon::createFromFormat('d/m/Y', $patient->birth_date);
            $ageStr = $dateNaissance->diffInYears(\Carbon\Carbon::now()) . ' ans';
        } catch (\Exception $e) {
            $ageStr = $patient->birth_date;
        }
    }

    $dossierUrl = '#';
    if (request()->routeIs('secretariat.*') && \Illuminate\Support\Facades\Route::has('secretariat.patient.dossier_medical')) {
        $dossierUrl = route('secretariat.patient.dossier_medical', $patient->id);
    } elseif (request()->routeIs('hospital.*') && \Illuminate\Support\Facades\Route::has('hospital.patient.dossier_medical')) {
        $dossierUrl = route('hospital.patient.dossier_medical', $patient->id);
    } elseif (request()->routeIs('doctor.*') && \Illuminate\Support\Facades\Route::has('doctor.patient.dossier_medical')) {
        $dossierUrl = route('doctor.patient.dossier_medical', $patient->id);
    } elseif (\Illuminate\Support\Facades\Route::has('doctor.patient.dossier_medical')) {
        $dossierUrl = route('doctor.patient.dossier_medical', $patient->id);
    } elseif (\Illuminate\Support\Facades\Route::has('secretariat.patient.dossier_medical')) {
        $dossierUrl = route('secretariat.patient.dossier_medical', $patient->id);
    } else {
        $dossierUrl = url('dossier_medical/' . $patient->id);
    }

    $dateStr = \Carbon\Carbon::parse($consultation->created_at)->format('d/m/Y') . ' à ' . \Carbon\Carbon::parse($consultation->created_at)->format('H:i');
    
    $serviceName = optional(optional($consultation->prestationHospital)->prestationService)->libelle 
        ?? optional(optional(optional(optional($consultation->prestationHospital)->prestationService)->service))->libelle 
        ?? 'Consultation générale';

    $doctorName = trim(optional(optional($consultation->doctor)->user)->name . ' ' . optional(optional($consultation->doctor)->user)->prenom) 
        ?: trim(optional(optional(optional($consultation->admission)->doctor)->user)->name . ' ' . optional(optional(optional($consultation->admission)->doctor)->user)->prenom);
    
    $infirmierName = trim(optional(optional($consultation->infirmier)->user)->name . ' ' . optional(optional($consultation->infirmier)->user)->prenom)
        ?: trim(optional(optional(optional($consultation->admission)->infirmier)->user)->name . ' ' . optional(optional(optional($consultation->admission)->infirmier)->user)->prenom);

    $caissiereName = trim(optional(optional(optional($consultation->admission)->cashier)->user)->name . ' ' . optional(optional(optional($consultation->admission)->cashier)->user)->prenom)
        ?: trim(optional(optional(optional($consultation->admission)->secretariat)->user)->name . ' ' . optional(optional(optional($consultation->admission)->secretariat)->user)->prenom);

    $reg = $consultation->registre;
    $regCur = optional($reg)->registreConsultationCurative;
    
    $valPoids = $consultation->poids ?: (optional($regCur)->poids ?? 'N/A');
    $valTaille = $consultation->taille ?: (optional($regCur)->taille ?? 'N/A');
    $valImc = $consultation->imc ?: (optional($regCur)->imc ?? 'N/A');
    $valTemp = $consultation->temperature ?: (optional($regCur)->temperature ?? 'N/A');
    $valTA = $consultation->tension_arterielle ?: (optional($regCur)->ta ?? 'N/A');
    $valPouls = $consultation->pouls ?: (optional($regCur)->pouls ?? 'N/A');
    $valSat = $consultation->saturation_oxygene ?: (optional($regCur)->saturation_oxygene ?? 'N/A');
    $valGlyA = $consultation->gly_a_jeun ?: (optional($regCur)->glycemie_a_jeun ?? 'N/A');

    $motifFull = $consultation->motif_consultation ?? optional($consultation->admission)->motif_consultation ?? (optional($regCur)->motif_consultation ?? 'Non renseigné');
    $diagnosticFull = optional($regCur)->diagnostic_retenu ?? optional($consultation->hospitalisation)->diagnostic ?? 'Aucun diagnostic renseigné';
    $examenPhysique = optional($regCur)->examen_physique ?? 'Non renseigné';
    $justification = optional($reg)->issue_consultation_justification ?? optional($consultation->hospitalisation)->remark ?? 'Aucune remarque enregistrée';
@endphp

<div class="container-fluid">

    <!-- En-tête Patient & Intervention -->
    <div class="patient-header-box mb-25">
        <div class="row align-items-center">
            <div class="col-lg-8 d-flex align-items-center flex-wrap gap-3">
                <div>
                    @if ($patient->img_url != null)
                        <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}" class="patient-avatar-img" alt="Photo de profil" />
                    @else
                        @if (strtolower($patient->gender) == 'masculin' || strtolower($patient->gender) == 'm')
                            <img src="{{ asset('assets/images/avatar/6.png') }}" class="patient-avatar-img" alt="Photo de profil" />
                        @else
                            <img src="{{ asset('assets/images/avatar/2.png') }}" class="patient-avatar-img" alt="Photo de profil" />
                        @endif
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h2 class="fw-bold text-dark mb-0 fs-22">{{ $patient->user->name ?? '' }} {{ $patient->user->prenom ?? '' }}</h2>
                        <span class="badge bg-primary-light text-primary fw-bold px-3 py-1 fs-12">Dossier N° {{ $patient->code_patient }}</span>
                        <span class="badge bg-info-light text-info fw-bold px-3 py-1 fs-12">Intervention : {{ $consultation->code_consultation ?? ('CONS-' . $consultation->id) }}</span>
                    </div>
                    <p class="text-muted mb-2 fs-13">
                        <span class="fw-semibold text-dark"><i class="fa-solid fa-calendar-day text-primary me-1"></i> Date : {{ $dateStr }}</span>
                        &nbsp;•&nbsp;
                        <span class="fw-semibold text-dark"><i class="fa-solid fa-hospital text-info me-1"></i> Service : {{ $serviceName }}</span>
                        &nbsp;•&nbsp;
                        <span><i class="fa-solid fa-cake-candles text-muted me-1"></i> {{ $ageStr }} ({{ ucfirst($patient->gender) }})</span>
                    </p>
                </div>
            </div>

            <!-- Bouton Retour -->
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ $dossierUrl }}" class="btn btn-secondary fw-semibold rounded-10 px-20 shadow-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Retour au Dossier Médical
                </a>
            </div>
        </div>
    </div>

    <!-- CONTENT CARDS -->
    <div class="row">
        <div class="col-12">

            <!-- 1. ÉQUIPE INTERVENANTE -->
            <div class="parcours-card">
                <div class="card-header">
                    <h5 class="card-title fw-bold text-dark fs-16 mb-0">
                        <i class="fa-solid fa-users-medical text-primary me-2"></i>1. Équipe Médicale & Intervenants
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-15 bg-light rounded-12 border border-info-subtle">
                                <span class="fs-12 text-muted fw-semibold text-uppercase d-block mb-1"><i class="fa-solid fa-user-md text-info me-1"></i> Médecin Traitant</span>
                                <span class="fs-15 fw-bold text-dark d-block">{{ $doctorName ?: 'Non renseigné' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-15 bg-light rounded-12 border border-success-subtle">
                                <span class="fs-12 text-muted fw-semibold text-uppercase d-block mb-1"><i class="fa-solid fa-user-nurse text-success me-1"></i> Infirmier(ère) Responsable</span>
                                <span class="fs-15 fw-bold text-dark d-block">{{ $infirmierName ?: 'Non renseigné' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-15 bg-light rounded-12 border border-secondary-subtle">
                                <span class="fs-12 text-muted fw-semibold text-uppercase d-block mb-1"><i class="fa-solid fa-user-check text-secondary me-1"></i> Secrétariat / Accueil</span>
                                <span class="fs-15 fw-bold text-dark d-block">{{ $caissiereName ?: 'Non renseigné' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. CONSTANTES PHYSIQUES -->
            <div class="parcours-card">
                <div class="card-header">
                    <h5 class="card-title fw-bold text-dark fs-16 mb-0">
                        <i class="fa-solid fa-heart-pulse text-danger me-2"></i>2. Constantes Physiques (Prises de soins)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">Poids</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valPoids }} {{ $valPoids != 'N/A' ? 'kg' : '' }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">Taille</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valTaille }} {{ $valTaille != 'N/A' ? 'cm' : '' }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">IMC</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valImc }} {{ $valImc != 'N/A' ? 'kg/m²' : '' }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">Température</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valTemp }} {{ $valTemp != 'N/A' ? '°C' : '' }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">Tension Artérielle</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valTA }} {{ $valTA != 'N/A' ? 'mmHg' : '' }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">Pouls</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valPouls }} {{ $valPouls != 'N/A' ? 'batt/mn' : '' }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">Saturation O₂</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valSat }} {{ $valSat != 'N/A' ? '%' : '' }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-12 text-center bg-white rounded-12 border shadow-2xs">
                                <span class="text-muted fs-12 fw-semibold d-block">Glycémie à jeûn</span>
                                <span class="fs-16 fw-bold text-dark">{{ $valGlyA }} {{ $valGlyA != 'N/A' ? 'g/l' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. CONSULTATION & DIAGNOSTIC MÉDICAL -->
            <div class="parcours-card">
                <div class="card-header">
                    <h5 class="card-title fw-bold text-dark fs-16 mb-0">
                        <i class="fa-solid fa-stethoscope text-info me-2"></i>3. Examen Médical & Diagnostic
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-15">
                        <span class="fw-bold text-dark d-block mb-1 fs-14">Motif de consultation :</span>
                        <p class="bg-light p-12 rounded-10 text-secondary mb-0 fs-13 border">{{ $motifFull }}</p>
                    </div>
                    <div class="mb-15">
                        <span class="fw-bold text-dark d-block mb-1 fs-14">Examen physique :</span>
                        <p class="bg-light p-12 rounded-10 text-secondary mb-0 fs-13 border">{{ $examenPhysique }}</p>
                    </div>
                    <div>
                        <span class="fw-bold text-dark d-block mb-1 fs-14">Diagnostic retenu :</span>
                        <div class="p-12 bg-primary-light text-primary rounded-10 border border-primary-subtle fw-bold fs-14">
                            <i class="fa-solid fa-clipboard-check me-1"></i> {{ $diagnosticFull }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. PRESCRIPTIONS & ORDONNANCES -->
            <div class="parcours-card">
                <div class="card-header">
                    <h5 class="card-title fw-bold text-dark fs-16 mb-0">
                        <i class="fa-solid fa-pills text-warning me-2"></i>4. Prescriptions & Imagerie Médicale
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($consultation->ordonnances ?? []) > 0)
                        <h6 class="fw-bold text-dark fs-14 mb-2"><i class="fa-solid fa-prescription-bottle-medical text-primary me-1"></i> Ordonnances Médicamenteuses :</h6>
                        @foreach($consultation->ordonnances as $ord)
                            <div class="table-responsive mb-3 border rounded-12 overflow-hidden">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Médicament (Type: {{ ucfirst($ord->type) }})</th>
                                            <th>Dosage / Posologie</th>
                                            <th>Quantité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ord->prescriptions ?? [] as $p)
                                            @php
                                                $drugName = optional($p->drug)->name ?? optional(optional($p->drugHospital)->drug)->name ?? 'Médicament';
                                            @endphp
                                            <tr>
                                                <td class="fw-bold text-dark">{{ $drugName }}</td>
                                                <td>{{ $p->dosage ?? 'Selon prescription' }}</td>
                                                <td>{{ $p->quantity }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-muted">Aucune ligne de médicament enregistrée.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted fs-13 mb-3"><i>Aucune ordonnance émise lors de cette consultation.</i></p>
                    @endif

                    <div class="row g-2 mt-2">
                        <div class="col-md-6">
                            <div class="p-12 bg-light rounded-10 border">
                                <span class="fw-bold text-dark d-block fs-13"><i class="fa-solid fa-x-ray text-secondary me-1"></i> Bulletin d'examen :</span>
                                <span class="fs-13 text-secondary">{{ $consultation->examen ? 'Examen prescrit (Imagerie / Labo)' : 'Aucun examen demandé' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-12 bg-light rounded-10 border">
                                <span class="fw-bold text-dark d-block fs-13"><i class="fa-solid fa-file-contract text-primary me-1"></i> Arrêt de travail :</span>
                                <span class="fs-13 text-secondary">{{ $consultation->arret ? 'Certificat d\'arrêt délivré' : 'Aucun arrêt de travail' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. ISSUE DE LA CONSULTATION -->
            <div class="parcours-card">
                <div class="card-header">
                    <h5 class="card-title fw-bold text-dark fs-16 mb-0">
                        <i class="fa-solid fa-door-open text-success me-2"></i>5. Issue de la Consultation & Sortie
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fw-bold text-dark fs-14">Mode de sortie / Décision :</span>
                        <span class="badge bg-success text-white fw-bold px-3 py-2 fs-13 text-uppercase">
                            {{ optional($consultation->registre)->issue_consultation ?? 'Standard' }}
                        </span>
                    </div>
                    <div>
                        <span class="fw-bold text-dark d-block mb-1 fs-14">Justification & Remarques du Médecin :</span>
                        <p class="bg-light p-12 rounded-10 text-secondary mb-0 fs-13 border">{{ $justification }}</p>
                    </div>
                </div>
            </div>

            <!-- 6. REGISTRE MÉDICAL DÉTAILLÉ (LECTURE SEULE - NON ÉDITABLE) -->
            @if($reg)
                <div class="parcours-card">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="card-title fw-bold text-dark fs-16 mb-0">
                                <i class="fa-solid fa-book-medical text-primary me-2"></i>6. Registre Médical (N° {{ $reg->code ?? 'N/A' }})
                            </h5>
                            <span class="text-muted fs-12">Consultation enregistrée au registre médical (lecture seule)</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary text-white fw-semibold px-3 py-2 fs-12 rounded-8">
                                <i class="fa-solid fa-lock me-1"></i> Lecture seule
                            </span>
                            <span class="badge bg-primary-light text-primary fw-bold px-3 py-2 fs-12 text-capitalize rounded-8">
                                {{ $reg->type_consultation ?? 'Consultation' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-20">

                        {{-- 6.1 CONSULTATION CURATIVE (READ ONLY) --}}
                        @if(optional($reg)->type_consultation == 'consultation curative' && $regCur)
                            <!-- Antécédents Médicaux & Habitudes -->
                            <div class="mb-20">
                                <h6 class="fw-bold text-dark fs-14 mb-3 pb-2 border-bottom">
                                    <i class="fa-solid fa-notes-medical text-info me-2"></i>Antécédents Médicaux & Habitudes
                                </h6>
                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">HTA</span>
                                            <span class="badge {{ str_contains($regCur->hta ?? '', 'Oui') ? 'bg-danger' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->hta ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Diabète</span>
                                            <span class="badge {{ str_contains($regCur->diabete ?? '', 'Oui') ? 'bg-danger' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->diabete ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Alcool</span>
                                            <span class="badge {{ ($regCur->alcool ?? '') == 'Oui' ? 'bg-warning text-dark' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->alcool ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Tabac</span>
                                            <span class="badge {{ ($regCur->tabac ?? '') == 'Oui' ? 'bg-warning text-dark' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->tabac ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">UGD</span>
                                            <span class="badge {{ ($regCur->ugd ?? '') == 'Oui' ? 'bg-warning text-dark' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->ugd ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Drépanocytaire</span>
                                            <span class="badge {{ ($regCur->drepanocytaire ?? '') == 'Oui' ? 'bg-danger' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->drepanocytaire ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Traitement Antérieur / En cours</span>
                                            <span class="fs-13 fw-semibold text-dark">{{ $regCur->traitement_medicamenteux_anterieur ?: 'Aucun traitement antérieur' }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($regCur->autre_antecedent_medical))
                                    <div class="mt-2 p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Autres Antécédents Médicaux :</span>
                                        <span class="fs-13 text-dark">{{ $regCur->autre_antecedent_medical }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Antécédents Chirurgicaux -->
                            <div class="mb-20">
                                <h6 class="fw-bold text-dark fs-14 mb-3 pb-2 border-bottom">
                                    <i class="fa-solid fa-scalpel text-danger me-2"></i>Antécédents Chirurgicaux
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Opération antérieure</span>
                                            <span class="badge {{ ($regCur->antecedent_chirurgical ?? '') == 'Oui' ? 'bg-warning text-dark' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->antecedent_chirurgical ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Détail de l'opération</span>
                                            <span class="fs-13 fw-semibold text-dark">{{ $regCur->nom_operation ?: 'Aucune intervention spécifiée' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Examens Complémentaires & Biologie -->
                            <div class="mb-20">
                                <h6 class="fw-bold text-dark fs-14 mb-3 pb-2 border-bottom">
                                    <i class="fa-solid fa-microscope text-primary me-2"></i>Examens Complémentaires & Biologie
                                </h6>
                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">TDR Paludisme</span>
                                            <span class="badge {{ ($regCur->tdr_paludisme ?? '') == 'Positif' ? 'bg-danger' : (($regCur->tdr_paludisme ?? '') == 'Negatif' ? 'bg-success' : 'bg-secondary') }} px-2 py-1 fs-12">
                                                {{ $regCur->tdr_paludisme ?? 'Non réalisé' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Goutte Épaisse</span>
                                            <span class="badge {{ ($regCur->goutte_epaise ?? '') == 'Positive' ? 'bg-danger' : (($regCur->goutte_epaise ?? '') == 'Negative' ? 'bg-success' : 'bg-secondary') }} px-2 py-1 fs-12">
                                                {{ $regCur->goutte_epaise ?? 'Non réalisée' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="p-12 bg-light rounded-10 border">
                                            <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Recherche Tuberculose</span>
                                            <span class="badge {{ ($regCur->tuberculose ?? '') == 'Oui' ? 'bg-danger' : 'bg-secondary' }} px-2 py-1 fs-12">
                                                {{ $regCur->tuberculose ?? 'Non' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Synthèse du Registre Curatif -->
                            <div>
                                <h6 class="fw-bold text-dark fs-14 mb-3 pb-2 border-bottom">
                                    <i class="fa-solid fa-clipboard-check text-success me-2"></i>Examen & Synthèse Curative
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-15 bg-light rounded-12 border">
                                            <span class="fw-bold text-dark d-block mb-1 fs-13">Examen Physique :</span>
                                            <p class="text-secondary mb-0 fs-13">{{ $regCur->examen_physique ?: 'Non renseigné' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-15 bg-primary-light rounded-12 border border-primary-subtle">
                                            <span class="fw-bold text-primary d-block mb-1 fs-13">Diagnostic Retenu :</span>
                                            <p class="fw-bold text-primary mb-0 fs-14"><i class="fa-solid fa-stethoscope me-1"></i> {{ $regCur->diagnostic_retenu ?: 'Aucun diagnostic retenu' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        {{-- 6.2 REGISTRE ACCOUCHEMENT (READ ONLY) --}}
                        @elseif(optional($reg)->type_consultation == 'accouchement' && optional($reg)->registreAccouchement)
                            @php $regAcc = $reg->registreAccouchement; @endphp
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Date Accouchement</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regAcc->date_accouchement ?: 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Mode d'Accouchement</span>
                                        <span class="badge bg-primary px-2 py-1 fs-12">{{ $regAcc->mode_accouchement ?: 'Voie basse' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Sexe de l'Enfant</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regAcc->sexe_enfant ?: 'Non spécifié' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-12 bg-light rounded-10 border text-center">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Poids de naissance</span>
                                        <span class="fs-15 fw-bold text-dark">{{ $regAcc->poids_enfant ? $regAcc->poids_enfant . ' g' : 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-12 bg-light rounded-10 border text-center">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Taille</span>
                                        <span class="fs-15 fw-bold text-dark">{{ $regAcc->taille_enfant ? $regAcc->taille_enfant . ' cm' : 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-12 bg-light rounded-10 border text-center">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Score APGAR 1mn / 5mn</span>
                                        <span class="fs-15 fw-bold text-dark">{{ $regAcc->apgar_1mn ?: '-' }} / {{ $regAcc->apgar_5mn ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-12 bg-light rounded-10 border text-center">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">État Nouveau-Né</span>
                                        <span class="badge bg-success px-2 py-1 fs-12">{{ $regAcc->etat_nouveau_ne ?: 'Vivant' }}</span>
                                    </div>
                                </div>
                            </div>

                        {{-- 6.3 REGISTRE PRÉ-NATALE (CPN) --}}
                        @elseif(optional($reg)->type_consultation == 'pre-natale' && optional($reg)->registreConsultationPreNatale)
                            @php $regPre = $reg->registreConsultationPreNatale; @endphp
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">N° CPN</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regPre->numero_cpn ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">DDR</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regPre->ddr ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">DPA</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regPre->dpa ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Hauteur Utérine</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regPre->hauteur_uterine ? $regPre->hauteur_uterine . ' cm' : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                        {{-- 6.4 REGISTRE POST-NATALE --}}
                        @elseif(optional($reg)->type_consultation == 'post-natale' && optional($reg)->registreConsultationPostNatale)
                            @php $regPost = $reg->registreConsultationPostNatale; @endphp
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Pression Artérielle</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regPost->ta ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Involution Utérine</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regPost->involution_uterine ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-12 bg-light rounded-10 border">
                                        <span class="fs-11 text-muted fw-bold text-uppercase d-block mb-1">Contraceptive / PF</span>
                                        <span class="fs-14 fw-bold text-dark">{{ $regPost->methode_pf ?? 'Aucune' }}</span>
                                    </div>
                                </div>
                            </div>

                        @else
                            <div class="p-15 bg-light rounded-12 text-center border">
                                <i class="fa-solid fa-circle-info text-primary fs-20 mb-2 d-block"></i>
                                <span class="fs-13 text-secondary fw-semibold">Le registre médical n° {{ $reg->code ?? 'N/A' }} est enregistré en base de données (mode affichage seul).</span>
                            </div>
                        @endif

                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
