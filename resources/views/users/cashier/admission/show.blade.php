@extends('layouts.dashboard', ['title' => 'Encaissement & Validation du Paiement'])

@section('content')
@php
    $isAdmission = ($payment->type === 'admission');
    $admission = $isAdmission ? $payment->admission : null;
    $hospitalisation = !$isAdmission ? $payment->hospitalisation : null;
    
    $patient = $isAdmission 
        ? ($admission->patient ?? null) 
        : ($hospitalisation->consultation->patient ?? null);
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

    // Praticien en charge
    $praticienName = 'Non assigné';
    $praticienRole = 'Praticien';
    $praticienContact = '-';
    if ($isAdmission) {
        if ($admission && $admission->doctor && $admission->doctor->user) {
            $praticienName = $admission->doctor->user->name . ' ' . ($admission->doctor->user->prenom ?? '');
            $praticienRole = $admission->doctor->typeDoctor->label ?? ($admission->doctor->typeAgent->libelle ?? 'Médecin');
            $praticienContact = $admission->doctor->contact ?? '-';
        } elseif ($admission && $admission->infirmier && $admission->infirmier->user) {
            $praticienName = $admission->infirmier->user->name . ' ' . ($admission->infirmier->user->prenom ?? '');
            $praticienRole = 'Infirmier(ère)';
            $praticienContact = $admission->infirmier->contact ?? '-';
        }
    } else {
        if ($hospitalisation && $hospitalisation->consultation && $hospitalisation->consultation->doctor) {
            $doc = $hospitalisation->consultation->doctor;
            $praticienName = $doc->user->name . ' ' . ($doc->user->prenom ?? '');
            $praticienRole = $doc->typeDoctor->label ?? 'Médecin';
            $praticienContact = $doc->contact ?? '-';
        }
    }

    $codeReference = $isAdmission 
        ? ($admission->code_admission ?? ('ADM-' . $payment->id))
        : ($hospitalisation->code ?? ('HOSP-' . $payment->id));

    $libellePrestation = $isAdmission
        ? ($admission->prestationHospital->prestationService->libelle ?? ($admission->typeExamen->libelle ?? ($admission->type_admission ?? 'Consultation')))
        : 'Séjour d\'Hospitalisation (' . ($hospitalisation->number_days ?? 1) . ' jours)';

    $serviceName = $isAdmission
        ? ($admission->prestationHospital->prestationService->service->libelle ?? ($admission->doctor->serviceHospital->service->libelle ?? 'Médecine Générale'))
        : 'Service d\'Hospitalisation';

    $prixInitial = $payment->prix ?? ($isAdmission ? ($admission->montant ?? 0) : 0);
@endphp

<div class="container-fluid px-20 py-15">

    <!-- En-tête Navigation -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-25">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('cashier.admission.all') }}" class="btn btn-outline-secondary rounded-12 px-20 py-10 fw-semibold shadow-xs">
                <i class="fa-solid fa-arrow-left me-2"></i> File d'attente
            </a>
            <div>
                <h3 class="fw-bold text-dark mb-0 fs-22">
                    Validation du Paiement <span class="text-primary font-monospace">#{{ $codeReference }}</span>
                </h3>
                <small class="text-muted"><i class="fa-regular fa-clock me-1"></i> Demande enregistrée le {{ $payment->created_at ? $payment->created_at->format('d/m/Y à H:i') : date('d/m/Y') }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('cashier.admission.list') }}" class="btn btn-light border rounded-12 px-20 py-10 fw-semibold shadow-xs">
                <i class="fa-solid fa-list-check me-2"></i> Historique des encaissements
            </a>
        </div>
    </div>

    <div class="row g-4">

        <!-- COLONNE GAUCHE : IDENTITÉ PATIENT & DÉTAILS DE LA DEMANDE -->
        <div class="col-lg-5">
            
            <!-- Carte Patient -->
            <div class="card border-0 shadow-sm rounded-20 p-25 bg-white mb-20 position-relative overflow-hidden">
                <div class="d-flex align-items-start gap-3">
                    <div class="position-relative flex-shrink-0">
                        @if ($patient && $patient->img_url && file_exists(public_path('assets/uploads/patient/' . $patient->img_url)))
                            <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}" class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar" style="width: 75px; height: 75px; object-fit: cover;" />
                        @else
                            @if ($patient && (strtolower($patient->gender) == 'masculin' || strtolower($patient->gender) == 'm'))
                                <img src="{{ asset('assets/images/avatar/6.png') }}" class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar" style="width: 75px; height: 75px; object-fit: cover;" />
                            @else
                                <img src="{{ asset('assets/images/avatar/2.png') }}" class="rounded-circle shadow-sm border border-3 border-white" alt="Avatar" style="width: 75px; height: 75px; object-fit: cover;" />
                            @endif
                        @endif
                        <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary border border-2 border-white px-2 py-0.5 fs-10">
                            {{ $patient->gender ?? 'Patient' }}
                        </span>
                    </div>

                    <div class="flex-grow-1 overflow-hidden">
                        <span class="badge bg-primary-light text-primary fw-bold px-2.5 py-1 fs-12 rounded-pill mb-1">
                            <i class="fa-solid fa-id-card me-1"></i> {{ $patient->code_patient ?? 'N/A' }}
                        </span>
                        <h4 class="fw-bold text-dark mb-1 fs-18 text-truncate">
                            {{ $patientUser->name ?? 'Patient' }} {{ $patientUser->prenom ?? '' }}
                        </h4>
                        <p class="text-muted mb-0 fs-13">
                            <span><i class="fa-solid fa-cake-candles text-info me-1"></i> {{ $ageStr }}</span>
                        </p>
                    </div>
                </div>

                <div class="pt-15 mt-15 border-top">
                    <div class="d-flex justify-content-between mb-2 fs-13">
                        <span class="text-muted"><i class="fa-solid fa-phone text-success me-1"></i> Téléphone :</span>
                        <span class="fw-semibold text-dark">{{ $patient->telephone ?? 'Non renseigné' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 fs-13">
                        <span class="text-muted"><i class="fa-solid fa-location-dot text-danger me-1"></i> Résidence :</span>
                        <span class="fw-semibold text-dark">{{ $patient->residenceActuelle->name ?? ($patient->residence ?? 'Inconnue') }}</span>
                    </div>
                    <div class="d-flex justify-content-between fs-13">
                        <span class="text-muted"><i class="fa-solid fa-briefcase text-secondary me-1"></i> Profession :</span>
                        <span class="fw-semibold text-dark">{{ $patient->profession ?? 'Non renseignée' }}</span>
                    </div>
                    @if(!empty($patient->num_cmu))
                    <div class="d-flex justify-content-between mt-2 fs-13">
                        <span class="text-muted"><i class="fa-solid fa-shield-heart text-primary me-1"></i> N° Sécurité CMU :</span>
                        <span class="fw-bold text-primary font-monospace">{{ $patient->num_cmu }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Carte Détails Prestation -->
            <div class="card border-0 shadow-sm rounded-20 p-25 bg-white">
                <h5 class="fw-bold text-dark mb-15 fs-16 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-notes-medical text-primary"></i> Spécifications de la Demande
                </h5>

                <div class="p-15 bg-light rounded-16 border mb-15">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted fs-12 text-uppercase fw-bold">Acte / Prestation</span>
                        <span class="badge bg-primary text-white rounded-pill px-3 py-1 fs-12 fw-semibold">
                            {{ $isAdmission ? ($admission->type_admission ?? 'Admission') : 'Hospitalisation' }}
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-0 fs-16">{{ $libellePrestation }}</h5>
                    <small class="text-muted"><i class="fa-solid fa-hospital me-1"></i> {{ $serviceName }}</small>
                </div>

                <div class="d-flex justify-content-between mb-2 fs-13">
                    <span class="text-muted"><i class="fa-solid fa-user-doctor text-info me-1"></i> Traitant :</span>
                    <span class="fw-semibold text-dark">{{ $praticienName }} <small class="text-muted">({{ $praticienRole }})</small></span>
                </div>
                
                @if($isAdmission && !empty($admission->motif_consultation))
                <div class="mt-15 pt-15 border-top">
                    <span class="text-muted fs-12 text-uppercase fw-bold d-block mb-1"><i class="fa-solid fa-quote-left text-warning me-1"></i> Motif de la Visite</span>
                    <p class="fs-13 text-dark mb-0 bg-light p-10 rounded-10 border border-light">
                        {{ $admission->motif_consultation }}
                    </p>
                </div>
                @endif
            </div>

        </div>

                <!-- COLONNE DROITE : FORMULAIRE D'ENCAISSEMENT -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-20 bg-white encaissement-card">
                        
                        <!-- Header de la carte -->
                        <div class="card-header bg-transparent border-bottom px-25 py-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2 fs-18">
                                    <span class="encaissement-header-icon"><i class="fa-solid fa-cash-register text-success"></i></span>
                                    <span>Formulaire d'Encaissement</span>
                                </h4>
                                <p class="text-muted mb-0 fs-13">Sélectionnez la couverture et le moyen de règlement pour valider.</p>
                            </div>
                            <span class="badge badge-warning-custom fw-semibold px-3 py-2 fs-12 rounded-pill">
                                <i class="fa-solid fa-hourglass-half me-1"></i> En attente de paiement
                            </span>
                        </div>

                        <div class="card-body p-25">
                            <form id="paymentValidationForm" action="{{ route('cashier.payment.validated', $payment->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Inputs cachés pour le formulaire -->
                                <input type="hidden" name="assure" id="input_assure" value="0">
                                <input type="hidden" name="mode_paiement" id="input_mode_paiement" value="espece">

                                <!-- SECTION 1 : COUVERTURE ASSURANCE -->
                                <div class="form-section-block mb-25">
                                    <div class="d-flex align-items-center justify-content-between mb-12">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="step-badge bg-primary text-white">1</span>
                                            <span class="section-title text-dark fw-bold fs-15">Prise en charge Assurance</span>
                                        </div>
                                        <span class="text-muted fs-12">Le patient est-il couvert ?</span>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Option Non Assuré -->
                                        <div class="col-sm-6 mb-2 mb-sm-0">
                                            <div class="choice-btn is-active" id="btn_assure_non" onclick="selectAssurance(0)">
                                                <div class="choice-btn-icon bg-secondary-light text-secondary">
                                                    <i class="fa-solid fa-user-xmark fs-18"></i>
                                                </div>
                                                <div class="choice-btn-text">
                                                    <div class="choice-title">Non Assuré(e)</div>
                                                    <div class="choice-desc">Plein tarif (100% Patient)</div>
                                                </div>
                                                <div class="choice-check">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Option Assuré -->
                                        <div class="col-sm-6">
                                            <div class="choice-btn" id="btn_assure_oui" onclick="selectAssurance(1)">
                                                <div class="choice-btn-icon bg-primary-light text-primary">
                                                    <i class="fa-solid fa-shield-halved fs-18"></i>
                                                </div>
                                                <div class="choice-btn-text">
                                                    <div class="choice-title">Assuré(e)</div>
                                                    <div class="choice-desc">Tiers-payant / Réduction</div>
                                                </div>
                                                <div class="choice-check">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Champs déroulants Assurance -->
                                    <div id="assureFields" class="collapsible-box p-20 rounded-16 mt-15" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-7 mb-2 mb-md-0">
                                                <label for="type" class="form-label fw-bold text-dark fs-13 mb-1">
                                                    Organisme d'Assurance <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select custom-input-field" id="type" name="type">
                                                    <option value="" selected disabled>-- Choisir l'assurance --</option>
                                                    @foreach ($assurances as $item)
                                                        <option data-solde="{{ $prixInitial }}" data-info="{{ $item->reduction * 100 }}" value="{{ $item->id }}">
                                                            {{ $item->libelle }} ({{ $item->reduction * 100 }}% pris en charge)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="no_assurance" class="form-label fw-bold text-dark fs-13 mb-1">
                                                    N° Carte / Police <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control custom-input-field" id="no_assurance" name="no_assurance" placeholder="Ex: POL-849202">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION 2 : MODE DE PAIEMENT -->
                                <div class="form-section-block mb-25">
                                    <div class="d-flex align-items-center justify-content-between mb-12">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="step-badge bg-success text-white">2</span>
                                            <span class="section-title text-dark fw-bold fs-15">Mode de Règlement</span>
                                        </div>
                                        <span class="text-muted fs-12">Sélectionnez le canal de paiement</span>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Option Espèce -->
                                        <div class="col-sm-6 mb-2 mb-sm-0">
                                            <div class="choice-btn is-active" id="btn_mode_espece" onclick="selectMode('espece')">
                                                <div class="choice-btn-icon bg-success-light text-success">
                                                    <i class="fa-solid fa-money-bill-1-wave fs-18"></i>
                                                </div>
                                                <div class="choice-btn-text">
                                                    <div class="choice-title">Espèce (Cash)</div>
                                                    <div class="choice-desc">Règlement physique direct</div>
                                                </div>
                                                <div class="choice-check">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Option Mobile Money -->
                                        <div class="col-sm-6">
                                            <div class="choice-btn" id="btn_mode_mobile" onclick="selectMode('mobile_money')">
                                                <div class="choice-btn-icon bg-info-light text-info">
                                                    <i class="fa-solid fa-mobile-screen-button fs-18"></i>
                                                </div>
                                                <div class="choice-btn-text">
                                                    <div class="choice-title">Mobile Money</div>
                                                    <div class="choice-desc">Wave, Orange, MTN, Moov</div>
                                                </div>
                                                <div class="choice-check">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Champs déroulants Mobile Money -->
                                    <div id="mobileMoneyFields" class="collapsible-box p-20 rounded-16 mt-15" style="display: none;">
                                        
                                        <label class="form-label fw-bold text-dark fs-13 mb-10 d-block">
                                            Sélectionnez l'Opérateur Mobile <span class="text-danger">*</span>
                                        </label>

                                        <!-- Champ caché pour le formulaire -->
                                        <input type="hidden" id="operateur_mobile" name="operateur_mobile" value="Wave">

                                        <!-- Grille des 4 Opérateurs avec Logos PNG -->
                                        <div class="row g-2 mb-15">
                                            <!-- Wave -->
                                            <div class="col-6 col-sm-3">
                                                <div class="operator-badge-card is-active" id="op_card_Wave" onclick="selectOperator('Wave', '{{ asset('assets/images/payments/wave.png') }}')">
                                                    <div class="operator-logo-wrap mb-2">
                                                        <img src="{{ asset('assets/images/payments/wave.png') }}" alt="Wave" class="operator-png-img">
                                                    </div>
                                                    <span class="operator-name">Wave</span>
                                                    <span class="operator-check"><i class="fa-solid fa-circle-check"></i></span>
                                                </div>
                                            </div>

                                            <!-- Orange Money -->
                                            <div class="col-6 col-sm-3">
                                                <div class="operator-badge-card" id="op_card_Orange" onclick="selectOperator('Orange Money', '{{ asset('assets/images/payments/orange.png') }}')">
                                                    <div class="operator-logo-wrap mb-2">
                                                        <img src="{{ asset('assets/images/payments/orange.png') }}" alt="Orange Money" class="operator-png-img">
                                                    </div>
                                                    <span class="operator-name">Orange</span>
                                                    <span class="operator-check"><i class="fa-solid fa-circle-check"></i></span>
                                                </div>
                                            </div>

                                            <!-- MTN MoMo -->
                                            <div class="col-6 col-sm-3">
                                                <div class="operator-badge-card" id="op_card_MTN" onclick="selectOperator('MTN MoMo', '{{ asset('assets/images/payments/mtn.png') }}')">
                                                    <div class="operator-logo-wrap mb-2">
                                                        <img src="{{ asset('assets/images/payments/mtn.png') }}" alt="MTN MoMo" class="operator-png-img">
                                                    </div>
                                                    <span class="operator-name">MTN MoMo</span>
                                                    <span class="operator-check"><i class="fa-solid fa-circle-check"></i></span>
                                                </div>
                                            </div>

                                            <!-- Moov Money -->
                                            <div class="col-6 col-sm-3">
                                                <div class="operator-badge-card" id="op_card_Moov" onclick="selectOperator('Moov Money', '{{ asset('assets/images/payments/moov.png') }}')">
                                                    <div class="operator-logo-wrap mb-2">
                                                        <img src="{{ asset('assets/images/payments/moov.png') }}" alt="Moov Money" class="operator-png-img">
                                                    </div>
                                                    <span class="operator-name">Moov Money</span>
                                                    <span class="operator-check"><i class="fa-solid fa-circle-check"></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Champ Référence / N° Émetteur -->
                                        <div>
                                            <label for="reference_paiement" class="form-label fw-bold text-dark fs-13 mb-1">
                                                Réf. Transaction / N° Émetteur <span class="text-muted fw-normal fs-12">(Optionnel)</span>
                                            </label>
                                            <input type="text" class="form-control custom-input-field" id="reference_paiement" name="reference_paiement" placeholder="Ex: TXN-098234 ou 0707000000">
                                        </div>

                                    </div>
                                </div>

                                <!-- SECTION 3 : SYNTHÈSE TARIFAIRE & TOTAL À ENCAISSER -->
                                <div class="financial-summary-box p-20 rounded-18 mb-25">
                                    <div class="d-flex justify-content-between align-items-center mb-10 pb-10 border-bottom-dashed">
                                        <span class="text-muted fs-13"><i class="fa-solid fa-receipt text-secondary me-1"></i> Tarif Prestation initial :</span>
                                        <span class="fw-bold text-dark fs-14">{{ number_format($prixInitial, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-10 pb-10 border-bottom-dashed" id="row_reduction" style="display: none !important;">
                                        <span class="text-success fs-13 fw-semibold"><i class="fa-solid fa-tag me-1"></i> Réduction Assurance (<span id="reduc_percent">0</span>%) :</span>
                                        <span class="fw-bold text-success fs-14">- <span id="reduc_montant">0</span> FCFA</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-5">
                                        <div>
                                            <span class="text-uppercase fw-bold fs-11 text-muted letter-spacing-1 d-block">Montant Net à Encaisser</span>
                                            <span class="badge bg-white text-dark border px-2 py-1 fs-11 rounded-8 mt-1" id="recap_mode_label">
                                                <i class="fa-solid fa-money-bill-1-wave text-success me-1"></i> Paiement en Espèce
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-black text-danger fs-28 line-height-1" id="solde_display">
                                                {{ number_format($prixInitial, 0, ',', ' ') }} <span class="fs-16 fw-bold">FCFA</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BOUTON D'ACTION PRINCIPAL -->
                                <button type="submit" id="btnSubmitPayment" class="btn-validation-submit">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Encaisser & Valider le Paiement</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    <style>
        .rounded-8 { border-radius: 8px !important; }
        .rounded-10 { border-radius: 10px !important; }
        .rounded-12 { border-radius: 12px !important; }
        .rounded-16 { border-radius: 16px !important; }
        .rounded-18 { border-radius: 18px !important; }
        .rounded-20 { border-radius: 20px !important; }
        .fw-black { font-weight: 900 !important; }
        .letter-spacing-1 { letter-spacing: 0.05em; }
        .line-height-1 { line-height: 1.1; }

        .bg-secondary-light { background-color: #F1F5F9 !important; }
        .bg-primary-light { background-color: #EEF4FF !important; }
        .bg-success-light { background-color: #ECFDF5 !important; }
        .bg-info-light { background-color: #F0F9FF !important; }

        .badge-warning-custom {
            background-color: #FFF8EB !important;
            color: #D97706 !important;
            border: 1px solid #FDE68A !important;
        }

        .step-badge {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        /* Boutons de Choix Principaux */
        .choice-btn {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 14px 16px !important;
            background-color: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 14px !important;
            cursor: pointer !important;
            transition: all 0.2s ease-in-out !important;
            user-select: none !important;
            position: relative !important;
        }

        .choice-btn:hover {
            border-color: #94a3b8 !important;
            background-color: #f8fafc !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04) !important;
        }

        .choice-btn.is-active {
            border-color: #005AEC !important;
            background-color: #f0f7ff !important;
            box-shadow: 0 0 0 1.5px #005AEC, 0 4px 14px rgba(0, 90, 236, 0.1) !important;
        }

        .choice-btn-icon {
            width: 42px !important;
            height: 42px !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        .choice-btn-text {
            flex-grow: 1 !important;
            min-width: 0 !important;
        }

        .choice-title {
            font-weight: 700 !important;
            font-size: 14px !important;
            color: #1e293b !important;
            line-height: 1.2 !important;
            margin-bottom: 2px !important;
        }

        .choice-desc {
            font-size: 12px !important;
            color: #64748b !important;
            line-height: 1.2 !important;
        }

        .choice-btn.is-active .choice-title {
            color: #005AEC !important;
        }

        .choice-check {
            font-size: 18px !important;
            color: #cbd5e1 !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
        }

        .choice-btn.is-active .choice-check {
            color: #005AEC !important;
        }

        /* Style spécifique actif pour mode espèce (vert) */
        #btn_mode_espece.is-active {
            border-color: #10B981 !important;
            background-color: #ecfdf5 !important;
            box-shadow: 0 0 0 1.5px #10B981, 0 4px 14px rgba(16, 185, 129, 0.1) !important;
        }
        #btn_mode_espece.is-active .choice-title,
        #btn_mode_espece.is-active .choice-check {
            color: #10B981 !important;
        }

        /* Cartes Opérateurs Mobile Money */
        .operator-badge-card {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 12px 8px !important;
            background: #ffffff !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 12px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            position: relative !important;
            text-align: center !important;
            user-select: none !important;
            height: 100% !important;
        }

        .operator-badge-card:hover {
            border-color: #94a3b8 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05) !important;
        }

        .operator-badge-card.is-active {
            border-color: #005AEC !important;
            background-color: #f0f7ff !important;
            box-shadow: 0 0 0 1.5px #005AEC, 0 4px 12px rgba(0, 90, 236, 0.12) !important;
        }

        .operator-logo-wrap {
            width: 44px !important;
            height: 44px !important;
            border-radius: 10px !important;
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 4px !important;
            overflow: hidden !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.04) !important;
        }

        .operator-badge-card.is-active .operator-logo-wrap {
            border-color: #005AEC !important;
        }

        .operator-png-img {
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
        }

        .operator-name {
            font-weight: 700 !important;
            font-size: 12px !important;
            color: #1e293b !important;
            line-height: 1.2 !important;
        }

        .operator-badge-card.is-active .operator-name {
            color: #005AEC !important;
        }

        .operator-check {
            position: absolute !important;
            top: 6px !important;
            right: 6px !important;
            font-size: 13px !important;
            color: #cbd5e1 !important;
        }

        .operator-badge-card.is-active .operator-check {
            color: #005AEC !important;
        }

        /* Section déroulante */
        .collapsible-box {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
        }

        .custom-input-field {
            height: 46px !important;
            border-radius: 10px !important;
            border: 1.5px solid #dce4ec !important;
            background-color: #ffffff !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #1e293b !important;
            padding: 10px 14px !important;
            transition: all 0.2s ease !important;
            width: 100% !important;
        }

        .custom-input-field:focus {
            border-color: #005AEC !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(0, 90, 236, 0.12) !important;
        }

        /* Synthèse financière */
        .financial-summary-box {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%) !important;
            border: 1px solid #e2e8f0 !important;
        }

        .border-bottom-dashed {
            border-bottom: 1px dashed #cbd5e1 !important;
        }

        /* Bouton de validation moderne */
        .btn-validation-submit {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 52px !important;
            padding: 0 20px !important;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 14px !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            letter-spacing: 0.3px !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
            cursor: pointer !important;
            transition: all 0.25s ease-in-out !important;
            text-align: center !important;
            text-decoration: none !important;
            user-select: none !important;
            margin-top: 5px !important;
        }

        .btn-validation-submit i {
            font-size: 19px !important;
            margin-right: 10px !important;
            line-height: 1 !important;
        }

        .btn-validation-submit span {
            line-height: 1 !important;
        }

        .btn-validation-submit:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 22px rgba(16, 185, 129, 0.45) !important;
            color: #ffffff !important;
        }

        .btn-validation-submit:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3) !important;
        }
    </style>

    <script>
        const prixBase = parseInt('{{ $prixInitial }}') || 0;
        let discountPercent = 0;
        let currentOperator = 'Wave';
        let currentOperatorLogo = "{{ asset('assets/images/payments/wave.png') }}";

        function updatePrice() {
            let finalPrice = prixBase;
            const rowReduction = document.getElementById('row_reduction');
            const reducPercent = document.getElementById('reduc_percent');
            const reducMontant = document.getElementById('reduc_montant');
            const soldeDisplay = document.getElementById('solde_display');

            if (discountPercent > 0) {
                const discountAmount = Math.round((prixBase * discountPercent) / 100);
                finalPrice = Math.max(0, prixBase - discountAmount);

                if (rowReduction) rowReduction.style.setProperty('display', 'flex', 'important');
                if (reducPercent) reducPercent.textContent = discountPercent;
                if (reducMontant) reducMontant.textContent = discountAmount.toLocaleString('fr-FR');
            } else {
                if (rowReduction) rowReduction.style.setProperty('display', 'none', 'important');
            }

            if (soldeDisplay) {
                soldeDisplay.innerHTML = finalPrice.toLocaleString('fr-FR') + ' <span class="fs-16 fw-bold">FCFA</span>';
            }
        }

        // Sélection Assurance (0 = Non, 1 = Oui)
        function selectAssurance(val) {
            const inputAssure = document.getElementById('input_assure');
            const btnNon = document.getElementById('btn_assure_non');
            const btnOui = document.getElementById('btn_assure_oui');
            const assureFields = document.getElementById('assureFields');
            const typeSelect = document.getElementById('type');
            const noAssuranceInput = document.getElementById('no_assurance');

            if (inputAssure) inputAssure.value = val;

            if (val === 1) {
                if (btnOui) btnOui.classList.add('is-active');
                if (btnNon) btnNon.classList.remove('is-active');
                if (assureFields) assureFields.style.display = 'block';
                if (typeSelect) typeSelect.required = true;
                if (noAssuranceInput) noAssuranceInput.required = true;
            } else {
                if (btnNon) btnNon.classList.add('is-active');
                if (btnOui) btnOui.classList.remove('is-active');
                if (assureFields) assureFields.style.display = 'none';
                if (typeSelect) {
                    typeSelect.required = false;
                    typeSelect.value = '';
                }
                if (noAssuranceInput) {
                    noAssuranceInput.required = false;
                    noAssuranceInput.value = '';
                }
                discountPercent = 0;
                updatePrice();
            }
        }

        // Sélection Mode de paiement ('espece' ou 'mobile_money')
        function selectMode(mode) {
            const inputMode = document.getElementById('input_mode_paiement');
            const btnEspece = document.getElementById('btn_mode_espece');
            const btnMobile = document.getElementById('btn_mode_mobile');
            const mobileMoneyFields = document.getElementById('mobileMoneyFields');
            const operateurMobile = document.getElementById('operateur_mobile');
            const recapModeLabel = document.getElementById('recap_mode_label');

            if (inputMode) inputMode.value = mode;

            if (mode === 'mobile_money') {
                if (btnMobile) btnMobile.classList.add('is-active');
                if (btnEspece) btnEspece.classList.remove('is-active');
                if (mobileMoneyFields) mobileMoneyFields.style.display = 'block';
                if (operateurMobile) operateurMobile.value = currentOperator;
                if (recapModeLabel) {
                    recapModeLabel.innerHTML = `
                        <img src="${currentOperatorLogo}" alt="${currentOperator}" class="rounded-circle me-1" style="width: 16px; height: 16px; object-fit: contain; vertical-align: -2px;">
                        <span>Paiement ${currentOperator}</span>
                    `;
                }
            } else {
                if (btnEspece) btnEspece.classList.add('is-active');
                if (btnMobile) btnMobile.classList.remove('is-active');
                if (mobileMoneyFields) mobileMoneyFields.style.display = 'none';
                if (recapModeLabel) {
                    recapModeLabel.innerHTML = '<i class="fa-solid fa-money-bill-1-wave text-success me-1"></i> Paiement en Espèce';
                }
            }
        }

        // Sélection Opérateur Mobile Money
        function selectOperator(name, logoUrl) {
            currentOperator = name;
            currentOperatorLogo = logoUrl;

            const inputHidden = document.getElementById('operateur_mobile');
            const recapModeLabel = document.getElementById('recap_mode_label');
            if (inputHidden) inputHidden.value = name;

            // Retirer la classe active de tous les badges opérateurs
            const allCards = document.querySelectorAll('.operator-badge-card');
            allCards.forEach(c => c.classList.remove('is-active'));

            // Activer la carte choisie
            let cardId = 'op_card_Wave';
            if (name.includes('Orange')) cardId = 'op_card_Orange';
            else if (name.includes('MTN')) cardId = 'op_card_MTN';
            else if (name.includes('Moov')) cardId = 'op_card_Moov';

            const activeCard = document.getElementById(cardId);
            if (activeCard) activeCard.classList.add('is-active');

            if (recapModeLabel) {
                recapModeLabel.innerHTML = `
                    <img src="${logoUrl}" alt="${name}" class="rounded-circle me-1" style="width: 16px; height: 16px; object-fit: contain; vertical-align: -2px;">
                    <span>Paiement ${name}</span>
                `;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');

            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    discountPercent = parseFloat(selected.getAttribute('data-info')) || 0;
                    updatePrice();
                });
            }
        });
    </script>
@endsection
