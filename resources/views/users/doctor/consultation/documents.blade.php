@extends('layouts.dashboard', ['title' => 'Documents médicaux'])

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

    // Âge
    $patientAge = '';
    if (!empty(optional($patient)->birth_date)) {
        try {
            $patientAge = \Carbon\Carbon::parse($patient->birth_date)->age . ' ans';
        } catch (\Throwable $e) {
            $patientAge = $patient->birth_date;
        }
    }

    $gender = optional($patient)->gender ?? 'masculin';
    $avatar = optional($patient)->img_url 
        ? asset('assets/uploads/patient/' . $patient->img_url) 
        : ($gender == 'masculin' ? asset('assets/images/avatar/6.png') : asset('assets/images/avatar/2.png'));

    // Recherche résultat laboratoire
    $resultatFile = null;
    if ($consultation->bulletinExamen && $consultation->bulletinExamen->examens) {
        foreach ($consultation->bulletinExamen->examens as $ex) {
            if (!empty($ex->resultat)) {
                $resultatFile = $ex->resultat;
                break;
            }
        }
    }

    $serviceLibelle = strtolower(optional(optional(optional($consultation->prestationHospital)->prestationService)->service)->libelle ?? optional(optional($consultation->prestationHospital)->prestationService)->libelle ?? $consultation->motif_consultation ?? '');
    $isLaboratoire = (optional($consultation->registre)->type_consultation === 'Laboratoire')
        || str_contains($serviceLibelle, 'laboratoire')
        || str_contains($serviceLibelle, 'labo')
        || str_contains($serviceLibelle, 'analyse')
        || (!empty($consultation->observation_infirmiere) && (str_contains($consultation->observation_infirmiere, 'date_reception') || str_contains($consultation->observation_infirmiere, 'echantillons')));

    $ordonnances = $consultation->ordonnances ?? collect();
    $bulletin = $consultation->bulletinExamen ?? $consultation->examen;
    $arret = $consultation->arret ?? \App\Models\ArretTravail::where('consultation_id', $consultation->id)->first();
    $declaration = $consultation->declaration ?? \App\Models\Declaration::where('consultation_id', $consultation->id)->first();

    $nbDocuments = $ordonnances->count() 
        + ($bulletin ? 1 : 0) 
        + ($arret ? 1 : 0) 
        + ($declaration ? 1 : 0) 
        + ($resultatFile ? 1 : 0);

    $issueType = !empty(optional($consultation->registre)->issue_consultation) ? $consultation->registre->issue_consultation : 'sortie';
    $ajouterDocUrl = route('doctor.consultation.formulaire.issue', [
        'title' => 'Formulaire issue',
        'issue' => $issueType,
        'id' => $consultation->id
    ]);
@endphp

<!-- BLOC 1 : EN-TÊTE ET INFORMATIONS DU PATIENT -->
<div class="box border-0 shadow-sm rounded-16 mb-4 bg-white">
    <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h4 class="box-title mb-0 fw-bold text-dark fs-16 d-flex align-items-center gap-2">
            <i class="fa-solid fa-folder-open text-primary me-1 fs-18"></i>
            <span>DOCUMENTS MÉDICAUX DE LA CONSULTATION</span>
        </h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('doctor.consultation.detail', $consultation->id) }}" class="btn btn-sm btn-info rounded-10 fw-bold shadow-sm text-white">
                <i class="fa-solid fa-eye me-1"></i> Fiche consultation
            </a>
            @if(optional($patient)->id)
                <a href="{{ route('doctor.patient.dossier_medical', $patient->id) }}" class="btn btn-sm btn-outline-info rounded-10 fw-bold shadow-sm">
                    <i class="fa-solid fa-folder-open me-1"></i> Dossier patient
                </a>
            @endif
            <a href="{{ $ajouterDocUrl }}" class="btn btn-sm btn-primary rounded-10 fw-bold shadow-sm">
                <i class="fa-solid fa-file-circle-plus me-1"></i> Émettre un autre document
            </a>
            <a href="{{ route('doctor.consultation.history') }}" class="btn btn-sm btn-secondary rounded-10 fw-bold shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour à l'historique
            </a>
        </div>
    </div>
    
    <!-- RÉCAPITULATIF DU PATIENT ET DE LA CONSULTATION -->
    <div class="box-body p-20 bg-light-subtle border-bottom">
        <div class="row align-items-center gy-3">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex align-items-center gap-3">
                <img src="{{ $avatar }}" alt="Avatar" class="rounded-circle shadow-sm border border-2 border-white" style="width: 60px; height: 60px; object-fit: cover;">
                <div>
                    <h5 class="fw-bold mb-1 text-dark fs-16">{{ $patientFullName }}</h5>
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="custom-badge custom-badge-blue">
                            <i class="fa-solid fa-id-card me-1"></i>{{ $patientCode }}
                        </span>
                        @if($patientAge)
                            <span class="custom-badge custom-badge-gray">
                                {{ $patientAge }}
                            </span>
                        @endif
                        <span class="custom-badge custom-badge-gray text-capitalize">
                            {{ $gender }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6">
                <div class="fs-12 text-muted fw-bold text-uppercase mb-1">Motif / Service</div>
                <div class="fw-bold text-dark fs-13 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-stethoscope text-primary"></i>
                    <span>{{ $typeVisite }}</span>
                </div>
                <div class="text-muted fs-12 mt-0.5">
                    <i class="fa-solid fa-hospital me-1 text-secondary"></i> {{ $hospitalName }}
                </div>
            </div>

            <div class="col-xl-3 col-lg-2 col-md-6">
                <div class="fs-12 text-muted fw-bold text-uppercase mb-1">Date & Praticien</div>
                <div class="fw-bold text-dark fs-13 d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-calendar-day text-primary"></i>
                    <span>{{ $consultation->created_at ? $consultation->created_at->format('d/m/Y à H:i') : ($consultation->date_consultation ? date('d/m/Y', strtotime($consultation->date_consultation)) : 'N/A') }}</span>
                </div>
                <div class="text-muted fs-12 mt-0.5">
                    <i class="fa-solid fa-user-doctor me-1 text-info"></i> {{ $practitionerName }}
                </div>
            </div>

            <div class="col-xl-2 col-lg-2 col-md-6 text-xl-end">
                <div class="fs-12 text-muted fw-bold text-uppercase mb-1">Total pièces</div>
                <span class="custom-badge custom-badge-green fs-13 py-1 px-3">
                    <i class="fa-solid fa-file-circle-check me-1"></i> {{ $nbDocuments }} document{{ $nbDocuments > 1 ? 's' : '' }}
                </span>
            </div>
        </div>
    </div>
</div>

@if($nbDocuments === 0)
    <!-- ÉTAT VIDE : AUCUN DOCUMENT DÉLIVRÉ -->
    <div class="box border-0 shadow-sm rounded-16 text-center py-5 bg-white">
        <div class="box-body py-5">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background-color: #f1f5f9; color: #64748b;">
                    <i class="fa-solid fa-file-circle-question fs-32"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-2">Aucun document médical n'a encore été délivré</h4>
            <p class="text-muted mx-auto mb-4 fs-14" style="max-width: 500px;">
                Vous pouvez prescrire une ordonnance, éditer un bulletin d'examen ou délivrer un arrêt de travail pour ce patient.
            </p>
            <a href="{{ $ajouterDocUrl }}" class="btn btn-primary rounded-10 px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-file-circle-plus fs-15"></i>
                <span>Émettre des documents maintenant</span>
            </a>
        </div>
    </div>
@else
    <!-- BLOC 2 : LISTE STRUCTURÉE ET CLAIRE DES DOCUMENTS DISPONIBLES -->
    <div class="box border-0 shadow-sm rounded-16 bg-white mb-4">
        <div class="box-header with-border p-20">
            <h4 class="box-title mb-0 fw-bold text-dark fs-15 d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-primary"></i>
                <b>PIÈCES MÉDICALES DÉLIVRÉES À TÉLÉCHARGER</b>
            </h4>
        </div>
        <div class="box-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr class="text-dark fw-bold fs-13">
                            <th class="py-3 px-4" style="width: 280px;">Type de Document</th>
                            <th class="py-3 px-4">Contenu & Prescriptions</th>
                            <th class="py-3 px-4" style="width: 160px;">Date d'émission</th>
                            <th class="py-3 px-4 text-center" style="width: 220px;">Téléchargement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        
                        <!-- 1. ORDONNANCES -->
                        @if($ordonnances->isNotEmpty())
                            @foreach($ordonnances as $idx => $ord)
                                @php
                                    $prescriptionsCount = $ord->prescriptions ? $ord->prescriptions->count() : 0;
                                    $ordType = $ord->type ? strtoupper($ord->type) : 'EXTERNE';
                                @endphp
                                <tr>
                                    <td class="py-3 px-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="doc-icon-box bg-blue">
                                                <i class="fa-solid fa-prescription-bottle-medical"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">Ordonnance Médicale</div>
                                                <div class="d-flex align-items-center gap-1.5 mt-1">
                                                    <span class="custom-badge custom-badge-blue">
                                                        {{ $ordType }}
                                                    </span>
                                                    <span class="text-muted fs-11 font-monospace">N° ORD-{{ $ord->id }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($ord->prescriptions && $ord->prescriptions->isNotEmpty())
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($ord->prescriptions as $p)
                                                    @php
                                                        $drugName = optional($p->drug)->name ?? optional(optional($p->drugHospital)->drug)->name ?? 'Médicament';
                                                    @endphp
                                                    <div class="fs-13 text-dark">
                                                        <i class="fa-solid fa-circle-dot text-primary fs-8 me-1.5"></i>
                                                        <b>{{ $drugName }}</b>
                                                        @if(!empty($p->dosage) || !empty($p->posologie))
                                                            <span class="text-muted ms-1">({{ $p->dosage ?? $p->posologie }})</span>
                                                        @endif
                                                        @if(!empty($p->quantite))
                                                            <span class="badge bg-light text-dark border ms-1 font-monospace fs-11">Qté: {{ $p->quantite }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted fs-13 italic">Prescription médicale enregistrée</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 fs-13 text-dark fw-semibold">
                                        <i class="fa-solid fa-calendar me-1 text-secondary"></i>
                                        {{ $ord->created_at ? $ord->created_at->format('d/m/Y') : date('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('impression', ['ordonnance', $ord->id]) }}" target="_blank" class="btn btn-sm btn-primary rounded-10 fw-bold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2">
                                            <i class="fa-solid fa-file-pdf fs-14"></i>
                                            <span>Télécharger (PDF)</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        <!-- 2. BULLETIN D'EXAMEN & ANALYSES -->
                        @if($bulletin)
                            @php
                                $codeBulletin = $bulletin->code_bulletin ?? ('#EX-' . $bulletin->id);
                            @endphp
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="doc-icon-box bg-purple">
                                            <i class="fa-solid fa-microscope"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-14">Bulletin d'Examen</div>
                                            <div class="d-flex align-items-center gap-1.5 mt-1">
                                                <span class="custom-badge custom-badge-purple">
                                                    ANALYSES & BILANS
                                                </span>
                                                <span class="text-muted fs-11 font-monospace">{{ $codeBulletin }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($bulletin->examens && $bulletin->examens->isNotEmpty())
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($bulletin->examens as $ex)
                                                <div class="fs-13 text-dark d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                    <div>
                                                        <i class="fa-solid fa-check text-success fs-10 me-1.5"></i>
                                                        <span class="fw-semibold">{{ $ex->nature_examen }}</span>
                                                    </div>
                                                    @if($ex->resultat)
                                                        <span class="custom-badge custom-badge-green fs-10">Résultat joint</span>
                                                    @else
                                                        <span class="custom-badge custom-badge-orange fs-10">En attente résultat</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($consultation->examen)
                                        <div class="fs-13 text-dark fw-semibold">
                                            <i class="fa-solid fa-check text-success fs-10 me-1.5"></i>
                                            {{ optional($consultation->examen)->nature_examen ?? 'Examens médicaux' }}
                                        </div>
                                    @else
                                        <span class="text-muted fs-13 italic">Demande d'examens complémentaires</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 fs-13 text-dark fw-semibold">
                                    <i class="fa-solid fa-calendar me-1 text-secondary"></i>
                                    {{ $bulletin->created_at ? $bulletin->created_at->format('d/m/Y') : date('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($isLaboratoire)
                                        <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-sm btn-purple rounded-10 fw-bold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2 text-white" style="background-color: #7c3aed;">
                                            <i class="fa-solid fa-file-pdf fs-14"></i>
                                            <span>Fiche Labo (PDF)</span>
                                        </a>
                                    @else
                                        <a href="{{ route('impression', ['examen', $bulletin->id]) }}" target="_blank" class="btn btn-sm btn-purple rounded-10 fw-bold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2 text-white" style="background-color: #7c3aed;">
                                            <i class="fa-solid fa-file-pdf fs-14"></i>
                                            <span>Télécharger (PDF)</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endif

                        <!-- 3. RÉSULTAT D'ANALYSE DE LABORATOIRE (FICHIER TÉLÉVERSÉ) -->
                        @if($resultatFile)
                            <tr style="background-color: #f0fdf4;">
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="doc-icon-box bg-green">
                                            <i class="fa-solid fa-file-circle-check"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-14">Résultat de Laboratoire</div>
                                            <div class="d-flex align-items-center gap-1.5 mt-1">
                                                <span class="custom-badge custom-badge-green">
                                                    RAPPORT VALIDÉ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-file-pdf text-danger fs-18"></i>
                                        <div>
                                            <div class="fw-bold text-dark fs-13">{{ $resultatFile }}</div>
                                            <div class="text-muted fs-11">Rapport officiel des analyses validé par le laboratoire</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 fs-13 text-dark fw-semibold">
                                    <i class="fa-solid fa-calendar me-1 text-secondary"></i>
                                    {{ $consultation->updated_at ? $consultation->updated_at->format('d/m/Y') : date('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ asset('assets/uploads/analyses/' . $resultatFile) }}" target="_blank" download class="btn btn-sm btn-success rounded-10 fw-bold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2 text-white">
                                        <i class="fa-solid fa-download fs-14"></i>
                                        <span>Télécharger Résultat</span>
                                    </a>
                                </td>
                            </tr>
                        @endif

                        <!-- 4. ARRÊT DE TRAVAIL -->
                        @if($arret)
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="doc-icon-box bg-orange">
                                            <i class="fa-solid fa-user-clock"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-14">Arrêt de Travail</div>
                                            <div class="d-flex align-items-center gap-1.5 mt-1">
                                                <span class="custom-badge custom-badge-orange">
                                                    REPOS MÉDICAL
                                                </span>
                                                <span class="text-muted fs-11 font-monospace">N° ARR-{{ $arret->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="fs-13 text-dark">
                                        <b>Durée du repos :</b> <span class="fw-bold text-dark fs-14">{{ $arret->duree ?? $arret->nbr_jour ?? 'N/A' }} jour(s)</span>
                                        &bull; <b>À compter du :</b> {{ $arret->date_debut ? date('d/m/Y', strtotime($arret->date_debut)) : 'Date consultation' }}
                                        @if($arret->motif)
                                            <div class="text-muted fs-12 mt-1">
                                                <b>Motif :</b> {{ $arret->motif }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 fs-13 text-dark fw-semibold">
                                    <i class="fa-solid fa-calendar me-1 text-secondary"></i>
                                    {{ $arret->created_at ? $arret->created_at->format('d/m/Y') : date('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('impression', ['arret', $arret->id]) }}" target="_blank" class="btn btn-sm btn-warning rounded-10 fw-bold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2 text-dark">
                                        <i class="fa-solid fa-file-pdf fs-14"></i>
                                        <span>Télécharger (PDF)</span>
                                    </a>
                                </td>
                            </tr>
                        @endif

                        <!-- 5. DÉCLARATION OFFICIELLE (DÉCÈS / NAISSANCE) -->
                        @if($declaration)
                            @php
                                $declType = $declaration->type ? strtoupper($declaration->type) : 'DÉCLARATION';
                            @endphp
                            <tr>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="doc-icon-box bg-red">
                                            <i class="fa-solid fa-certificate"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-14">Déclaration Officielle</div>
                                            <div class="d-flex align-items-center gap-1.5 mt-1">
                                                <span class="custom-badge custom-badge-red">
                                                    {{ $declType }}
                                                </span>
                                                <span class="text-muted fs-11 font-monospace">N° DECL-{{ $declaration->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="fs-13 text-dark">
                                        <i class="fa-solid fa-info-circle text-danger me-1"></i>
                                        Certificat et déclaration médicale officielle enregistrée pour le dossier patient.
                                    </div>
                                </td>
                                <td class="py-3 px-4 fs-13 text-dark fw-semibold">
                                    <i class="fa-solid fa-calendar me-1 text-secondary"></i>
                                    {{ $declaration->created_at ? $declaration->created_at->format('d/m/Y') : date('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="{{ route('impression', ['deces', $declaration->id]) }}" target="_blank" class="btn btn-sm btn-danger rounded-10 fw-bold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2 text-white">
                                        <i class="fa-solid fa-file-pdf fs-14"></i>
                                        <span>Télécharger (PDF)</span>
                                    </a>
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<!-- STYLES CSS POUR LES BADGES À HAUT CONTRASTE ET LES ICÔNES NETTES -->
<style>
    .rounded-16 {
        border-radius: 16px !important;
    }
    .rounded-10 {
        border-radius: 10px !important;
    }
    
    /* BOITES ICÔNES SOLIDES */
    .doc-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #ffffff !important;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    .doc-icon-box.bg-blue { background-color: #0284c7 !important; }
    .doc-icon-box.bg-purple { background-color: #7c3aed !important; }
    .doc-icon-box.bg-green { background-color: #16a34a !important; }
    .doc-icon-box.bg-orange { background-color: #d97706 !important; }
    .doc-icon-box.bg-red { background-color: #dc2626 !important; }

    /* BADGES À TRÈS HAUT CONTRASTE & PARFAITEMENT LISIBLES */
    .custom-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 6px;
        line-height: 1.2;
        letter-spacing: 0.02em;
    }
    
    .custom-badge-blue {
        background-color: #0284c7 !important;
        color: #ffffff !important;
    }
    .custom-badge-purple {
        background-color: #7c3aed !important;
        color: #ffffff !important;
    }
    .custom-badge-green {
        background-color: #16a34a !important;
        color: #ffffff !important;
    }
    .custom-badge-orange {
        background-color: #d97706 !important;
        color: #ffffff !important;
    }
    .custom-badge-red {
        background-color: #dc2626 !important;
        color: #ffffff !important;
    }
    .custom-badge-gray {
        background-color: #475569 !important;
        color: #ffffff !important;
    }

    .table > :not(caption) > * > * {
        border-bottom-color: #f1f5f9;
    }
</style>
@endsection
