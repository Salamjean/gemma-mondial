@extends('layouts.dashboard', ['title' => 'Détail de l\'Examen / Admission'])

@section('content')
@php
    $patient = $admission->patient;
    $patientUser = $patient->user ?? null;
    
    // Calcul de l'âge
    $ageStr = 'Âge non renseigné';
    if ($patient && $patient->birth_date) {
        try {
            $birth = \Carbon\Carbon::hasFormat($patient->birth_date, 'd/m/Y')
                ? \Carbon\Carbon::createFromFormat('d/m/Y', $patient->birth_date)
                : \Carbon\Carbon::parse($patient->birth_date);
            $ageStr = $birth->diffInYears(\Carbon\Carbon::now()) . ' ans (' . $birth->format('d/m/Y') . ')';
        } catch (\Throwable $e) {
            $ageStr = $patient->birth_date;
        }
    }

    // Date admission formatée
    $dateAdmissionStr = '-';
    if ($admission->date_admission || $admission->created_at) {
        $dateObj = \Carbon\Carbon::parse($admission->date_admission ?? $admission->created_at);
        $dateAdmissionStr = $dateObj->locale('fr')->isoFormat('LLLL');
    }

    // Praticien en charge
    $praticienName = 'Non assigné';
    $praticienRole = 'Médecin Prescripteur / Examinateur';
    $praticienContact = '-';
    $praticienMatricule = '-';
    if ($admission->doctor && $admission->doctor->user) {
        $praticienName = $admission->doctor->user->name . ' ' . ($admission->doctor->user->prenom ?? '');
        $praticienRole = $admission->doctor->typeDoctor->label ?? ($admission->doctor->typeAgent->libelle ?? 'Médecin');
        $praticienContact = $admission->doctor->contact ?? '-';
        $praticienMatricule = $admission->doctor->matricule ?? '-';
    }

    // Validateur caisse
    $validateurName = $admission->cashier->user->name ?? ($admission->caissiere->user->name ?? null);

    // URL Impression
    $imprimerUrl = route('secretariat.admission.imprimer', $admission->id);
    if (auth()->check() && auth()->user()->role_as == 'cashier' && \Illuminate\Support\Facades\Route::has('cashier.admission.imprimer')) {
        $imprimerUrl = route('cashier.admission.imprimer', $admission->id);
    }
@endphp

<div class="container-fluid px-20 py-15">

    <!-- En-tête Navigation & Actions -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-25">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ back()->getTargetUrl() }}" class="btn btn-outline-secondary rounded-12 px-20 py-10 fw-semibold shadow-xs">
                <i class="fa-solid fa-arrow-left me-2"></i> Retour
            </a>
            <div>
                <h3 class="fw-bold text-dark mb-0 fs-22">
                    Détail de l'Examen <span class="text-primary font-monospace">#{{ $admission->code_admission ?? ('ADM-' . str_pad($admission->id, 5, '0', STR_PAD_LEFT)) }}</span>
                </h3>
                <small class="text-muted"><i class="fa-regular fa-calendar-check me-1"></i> {{ ucfirst($dateAdmissionStr) }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ $imprimerUrl }}" target="_blank" class="btn btn-primary rounded-12 px-20 py-10 fw-semibold shadow-sm">
                <i class="fa-solid fa-print me-2"></i> Imprimer le Reçu / Fiche
            </a>
        </div>
    </div>

    <!-- CARTE PATIENT (Header Banner) -->
    <div class="card border-0 shadow-sm rounded-20 p-25 bg-white mb-25 position-relative overflow-hidden">
        <div class="position-absolute top-0 end-0 p-3 opacity-10 pointer-events-none d-none d-md-block">
            <i class="fa-solid fa-flask-vial text-primary" style="font-size: 140px; transform: rotate(10deg); margin-top: -30px; margin-right: -20px;"></i>
        </div>
        <div class="row align-items-center position-relative">
            <div class="col-lg-8 d-flex align-items-center flex-wrap gap-4">
                <div class="position-relative">
                    @if ($patient && $patient->img_url && file_exists(public_path('assets/uploads/patient/' . $patient->img_url)))
                        <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}" class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar" style="width: 90px; height: 90px; object-fit: cover;" />
                    @else
                        @if ($patient && (strtolower($patient->gender) == 'masculin' || strtolower($patient->gender) == 'm'))
                            <img src="{{ asset('assets/images/avatar/6.png') }}" class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar" style="width: 90px; height: 90px; object-fit: cover;" />
                        @else
                            <img src="{{ asset('assets/images/avatar/2.png') }}" class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar" style="width: 90px; height: 90px; object-fit: cover;" />
                        @endif
                    @endif
                    <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary border border-2 border-white px-2 py-1 fs-11">
                        {{ $patient->gender ?? 'Patient' }}
                    </span>
                </div>

                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                        <h2 class="fw-bold text-dark mb-0 fs-24">
                            {{ $patientUser->name ?? 'Patient' }} {{ $patientUser->prenom ?? '' }}
                        </h2>
                        <span class="badge bg-primary-light text-primary fw-bold px-3 py-1 fs-13 rounded-pill">
                            <i class="fa-solid fa-id-card me-1"></i> {{ $patient->code_patient ?? 'N/A' }}
                        </span>
                        @if ($admission->statut_paiement == 1)
                            <span class="badge bg-success-light text-success fw-bold px-3 py-1 fs-13 rounded-pill">
                                <i class="fa-solid fa-circle-check me-1"></i> Examen Payé
                            </span>
                        @else
                            <span class="badge bg-warning-light text-warning fw-bold px-3 py-1 fs-13 rounded-pill">
                                <i class="fa-solid fa-clock me-1"></i> Paiement en Attente
                            </span>
                        @endif
                    </div>

                    <p class="text-muted mb-2 fs-14">
                        <span class="fw-semibold text-dark"><i class="fa-solid fa-cake-candles text-info me-1"></i> {{ $ageStr }}</span>
                        &nbsp;•&nbsp;
                        <span class="text-secondary"><i class="fa-solid fa-briefcase text-muted me-1"></i> {{ $patient->profession ?? 'Profession non renseignée' }}</span>
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border px-3 py-1.5 fs-12 rounded-10">
                            <i class="fa-solid fa-phone text-success me-1"></i> {{ $patient->telephone ?? 'Non renseigné' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-1.5 fs-12 rounded-10">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $patient->residenceActuelle->name ?? ($patient->residence ?? 'Résidence inconnue') }}
                        </span>
                        @if(!empty($patient->num_cmu))
                        <span class="badge bg-light text-dark border px-3 py-1.5 fs-12 rounded-10">
                            <i class="fa-solid fa-shield-heart text-primary me-1"></i> CMU: {{ $patient->num_cmu }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="p-20 bg-light rounded-16 border text-center text-lg-end d-inline-block w-100 w-lg-auto">
                    <small class="text-muted text-uppercase fw-bold fs-11 letter-spacing-1 d-block mb-1">Montant de l'Examen</small>
                    <div class="fw-bold fs-26 text-danger mb-1">
                        @if($admission->montant == 0)
                            <span class="badge bg-success text-white fs-18 px-3 py-1.5">GRATUIT (GTC)</span>
                        @else
                            {{ number_format($admission->montant, 0, ',', ' ') }} <span class="fs-16 text-muted">FCFA</span>
                        @endif
                    </div>
                    <div class="fs-12 text-muted">
                        @if ($admission->mode_paiement == 'mobile_money')
                            <span class="badge bg-info-light text-info fw-bold px-2 py-1 rounded-8">
                                <i class="fa-solid fa-mobile-screen-button me-1"></i> {{ $admission->operateur_mobile ?? 'Mobile Money' }}
                                @if($admission->reference_paiement) <small class="text-muted">({{ $admission->reference_paiement }})</small> @endif
                            </span>
                        @else
                            <span class="badge bg-secondary-light text-secondary fw-bold px-2 py-1 rounded-8">
                                <i class="fa-solid fa-money-bill-1-wave me-1"></i> Règlement en Espèce
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRILLE DE SYNTHÈSE (3 CARDS KPI) -->
    <div class="row g-3 mb-25">
        
        <!-- CARD 1 : PRATICIEN EN CHARGE -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-20 p-20 bg-white h-100">
                <div class="d-flex align-items-center gap-3 mb-15">
                    <div class="avatar-box bg-primary-light text-primary rounded-14 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-user-doctor fs-20"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase fs-11 d-block">Médecin Prescripteur</small>
                        <h5 class="fw-bold text-dark mb-0 fs-16">{{ $praticienName }}</h5>
                    </div>
                </div>
                <div class="pt-10 border-top">
                    <div class="d-flex justify-content-between mb-1 fs-13">
                        <span class="text-muted">Spécialité :</span>
                        <span class="fw-semibold text-dark">{{ $praticienRole }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 fs-13">
                        <span class="text-muted">Matricule :</span>
                        <span class="fw-semibold font-monospace text-dark">{{ $praticienMatricule }}</span>
                    </div>
                    <div class="d-flex justify-content-between fs-13">
                        <span class="text-muted">Contact :</span>
                        <span class="fw-semibold text-dark">{{ $praticienContact }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2 : COUVERTURE & ASSURANCE -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-20 p-20 bg-white h-100">
                <div class="d-flex align-items-center gap-3 mb-15">
                    <div class="avatar-box bg-info-light text-info rounded-14 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-shield-halved fs-20"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase fs-11 d-block">Couverture Sociale</small>
                        <h5 class="fw-bold text-dark mb-0 fs-16">
                            {{ $admission->type_assurance->libelle ?? ($admission->typeAssurance->libelle ?? 'Plein Tarif (100% Patient)') }}
                        </h5>
                    </div>
                </div>
                <div class="pt-10 border-top">
                    <div class="d-flex justify-content-between mb-1 fs-13">
                        <span class="text-muted">Prise en charge :</span>
                        <span class="fw-semibold text-dark">
                            @if($admission->type_assurance || $admission->typeAssurance)
                                <span class="badge bg-success-light text-success fw-bold">Assuré(e)</span>
                            @else
                                <span class="badge bg-light text-muted border">Non assuré</span>
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 fs-13">
                        <span class="text-muted">N° Police / Assurance :</span>
                        <span class="fw-semibold font-monospace text-dark">{{ $admission->no_assurance ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between fs-13">
                        <span class="text-muted">Mode d'entrée :</span>
                        <span class="fw-semibold text-dark">{{ $admission->mode_entree ?? 'Venue lui-même' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3 : ÉTAT DE VALIDATION & CAISSE -->
        <div class="col-lg-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-20 p-20 bg-white h-100">
                <div class="d-flex align-items-center gap-3 mb-15">
                    <div class="avatar-box bg-success-light text-success rounded-14 p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-receipt fs-20"></i>
                    </div>
                    <div>
                        <small class="text-muted fw-bold text-uppercase fs-11 d-block">Validation Caisse</small>
                        <h5 class="fw-bold text-dark mb-0 fs-16">
                            @if ($admission->statut_validation == 1)
                                Validé par {{ $validateurName ?? 'la Caisse' }}
                            @else
                                En attente d'approbation
                            @endif
                        </h5>
                    </div>
                </div>
                <div class="pt-10 border-top">
                    <div class="d-flex justify-content-between mb-1 fs-13">
                        <span class="text-muted">Statut Paiement :</span>
                        <span>
                            @if ($admission->statut_paiement == 1)
                                <span class="badge bg-success text-white fw-semibold">Payé</span>
                            @else
                                <span class="badge bg-warning text-dark fw-semibold">Non payé</span>
                            @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-1 fs-13">
                        <span class="text-muted">Mode de règlement :</span>
                        <span class="fw-semibold text-dark">
                            {{ $admission->mode_paiement == 'mobile_money' ? ('Mobile Money (' . ($admission->operateur_mobile ?? 'Opérateur') . ')') : 'Espèce (Cash)' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between fs-13">
                        <span class="text-muted">Date règlement :</span>
                        <span class="fw-semibold text-dark">{{ $admission->date_paiement ? \Carbon\Carbon::parse($admission->date_paiement)->format('d/m/Y') : ($admission->statut_paiement ? 'Aujourd\'hui' : '-') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- DÉTAILS DE L'EXAMEN & MOTIF -->
    <div class="row g-3">
        
        <!-- Colonne Gauche : Spécifications de l'Examen -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-20 p-25 bg-white h-100">
                <h5 class="fw-bold text-dark mb-20 fs-16 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-flask text-primary"></i> Spécifications de l'Examen
                </h5>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody>
                            <tr class="border-bottom">
                                <td class="text-muted py-3 fs-14 fw-medium" style="width: 40%;"><i class="fa-solid fa-tag text-primary me-2"></i> Type de Demande</td>
                                <td class="py-3 fs-14 fw-bold text-dark">Examen Médical</td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted py-3 fs-14 fw-medium"><i class="fa-solid fa-microscope text-info me-2"></i> Examen Demandé</td>
                                <td class="py-3 fs-14">
                                    <span class="badge bg-primary text-white fs-13 px-3 py-1.5 rounded-pill fw-semibold">
                                        {{ $admission->typeExamen->libelle ?? ($admission->prestationHospital->prestationService->libelle ?? 'Examen') }}
                                    </span>
                                </td>
                            </tr>
                            <tr class="border-bottom">
                                <td class="text-muted py-3 fs-14 fw-medium"><i class="fa-solid fa-hospital text-success me-2"></i> Laboratoire / Service</td>
                                <td class="py-3 fs-14 fw-bold text-dark">
                                    {{ $admission->prestationHospital->prestationService->service->libelle ?? ($admission->doctor->serviceHospital->service->libelle ?? 'Laboratoire d\'Analyses') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted py-3 fs-14 fw-medium"><i class="fa-solid fa-arrow-right-to-bracket text-warning me-2"></i> Mode d'accès</td>
                                <td class="py-3 fs-14 fw-semibold text-dark">{{ $admission->mode_entree ?? 'Venue spontanée' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Colonne Droite : Motif de l'Examen -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-20 p-25 bg-white h-100">
                <h5 class="fw-bold text-dark mb-20 fs-16 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-warning"></i> Motif de la Prescription / Examen
                </h5>

                <div class="p-20 bg-light rounded-16 border h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold fs-13">
                            <i class="fa-solid fa-quote-left"></i> Motif & Renseignements Cliniques
                        </div>
                        <p class="fs-15 text-dark lh-base mb-0" style="white-space: pre-line;">
                            {{ $admission->motif_consultation ?: 'Aucun renseignement clinique particulier spécifié pour cet examen.' }}
                        </p>
                    </div>
                    
                    <div class="mt-20 pt-15 border-top d-flex align-items-center justify-content-between text-muted fs-12">
                        <span><i class="fa-regular fa-clock me-1"></i> Enregistré le {{ $admission->created_at ? $admission->created_at->format('d/m/Y à H:i') : '-' }}</span>
                        <span><i class="fa-solid fa-hospital me-1"></i> {{ $admission->hospital->label ?? 'Établissement' }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
    .rounded-12 { border-radius: 12px !important; }
    .rounded-14 { border-radius: 14px !important; }
    .rounded-16 { border-radius: 16px !important; }
    .rounded-20 { border-radius: 20px !important; }
    .rounded-10 { border-radius: 10px !important; }
    .rounded-8 { border-radius: 8px !important; }
    .shadow-xs { box-shadow: 0 2px 6px rgba(0,0,0,0.04) !important; }
    .bg-primary-light { background-color: #EEF4FF !important; color: #005AEC !important; }
    .bg-success-light { background-color: #EDFAF2 !important; color: #10B981 !important; }
    .bg-warning-light { background-color: #FFF8EB !important; color: #F59E0B !important; }
    .bg-info-light { background-color: #F0F9FF !important; color: #0284C7 !important; }
    .bg-secondary-light { background-color: #F1F5F9 !important; color: #475569 !important; }
</style>
@endsection
