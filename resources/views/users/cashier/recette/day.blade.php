@extends('layouts.dashboard', ['title' => 'Recette du ' . dateCompletFr($day)])

@php
    $totalRecette = $payments->sum('prix');
    $totalPriseEnCharge = $payments->sum(function($p) {
        $normal = $p->prix_normal ?? $p->prix;
        return max(0, $normal - $p->prix);
    });

    $especePayments = $payments->filter(function($p) {
        return $p->mode_paiement === 'espece' || empty($p->mode_paiement);
    });
    $totalEspece = $especePayments->sum('prix');
    $nbEspece = $especePayments->count();

    $mobilePayments = $payments->filter(function($p) {
        return $p->mode_paiement === 'mobile_money';
    });
    $totalMobile = $mobilePayments->sum('prix');
    $nbMobile = $mobilePayments->count();

    $waveTotal = $mobilePayments->where('operateur_mobile', 'Wave')->sum('prix');
    $waveCount = $mobilePayments->where('operateur_mobile', 'Wave')->count();

    $orangeTotal = $mobilePayments->where('operateur_mobile', 'Orange Money')->sum('prix');
    $orangeCount = $mobilePayments->where('operateur_mobile', 'Orange Money')->count();

    $mtnTotal = $mobilePayments->where('operateur_mobile', 'MTN MoMo')->sum('prix');
    $mtnCount = $mobilePayments->where('operateur_mobile', 'MTN MoMo')->count();

    $moovTotal = $mobilePayments->where('operateur_mobile', 'Moov Money')->sum('prix');
    $moovCount = $mobilePayments->where('operateur_mobile', 'Moov Money')->count();

    $pourcentEspece = $totalRecette > 0 ? round(($totalEspece / $totalRecette) * 100) : 0;
    $pourcentMobile = $totalRecette > 0 ? round(($totalMobile / $totalRecette) * 100) : 0;
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row align-items-center">
                    <div class="col-md-8 col-12">
                        <h4 class="box-title">
                            <b>POINT DES RECETTES : {{ strtoupper(dateCompletFr($day)) }}</b>
                        </h4>
                    </div>
                    <div class="col-md-4 col-12 text-md-end text-start mt-2 mt-md-0">
                        <a href="{{ route('cashier.recette.list') }}" class="btn btn-sm btn-secondary me-2">
                            <i class="fa-solid fa-list me-1"></i> Liste des recettes
                        </a>
                        <a href="{{ route('cashier.admission.indicate', $day) }}" target="_blank" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-print me-1"></i> Imprimer le point PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CARTES DE STATISTIQUES SANS BORDURES COLOREES --}}
<div class="row">
    {{-- TOTAL RECETTE --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-primary">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-white-50 mb-1 text-uppercase fs-12">Recette Totale</p>
                        <h3 class="text-white fw-bold mb-0">{{ number_format($totalRecette, 0, ',', ' ') }} <small class="fs-13 text-white-50">FCFA</small></h3>
                        <small class="text-white-50">{{ count($payments) }} acte(s) encaissé(s)</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-wallet text-white-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL ESPECE --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Paiements en Espèce</p>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($totalEspece, 0, ',', ' ') }} <small class="fs-13 text-muted">FCFA</small></h3>
                        <small class="text-muted">{{ $nbEspece }} transaction(s) ({{ $pourcentEspece }}%)</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-money-bill-wave text-success fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL MOBILE MONEY --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Paiements Mobile Money</p>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($totalMobile, 0, ',', ' ') }} <small class="fs-13 text-muted">FCFA</small></h3>
                        <small class="text-muted">{{ $nbMobile }} transaction(s) ({{ $pourcentMobile }}%)</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-mobile-screen-button text-primary fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PRISE EN CHARGE ASSURANCE --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Prise en charge Assurance</p>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($totalPriseEnCharge, 0, ',', ' ') }} <small class="fs-13 text-muted">FCFA</small></h3>
                        <small class="text-muted">Part couverte par assurance</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-shield-halved text-warning fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL MOBILE MONEY PAR OPERATEUR --}}
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h5 class="box-title"><b>Détail Mobile Money par Opérateur</b></h5>
            </div>
            <div class="box-body">
                <div class="row">
                    {{-- Wave --}}
                    <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/payments/wave.png') }}" alt="Wave" style="height: 36px; width: 36px; object-fit: contain; margin-right: 12px;">
                                <div>
                                    <h6 class="mb-0 fw-bold">Wave</h6>
                                    <small class="text-muted">{{ $waveCount }} transaction(s)</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-bold">{{ number_format($waveTotal, 0, ',', ' ') }}</h6>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                    </div>

                    {{-- Orange Money --}}
                    <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/payments/orange.png') }}" alt="Orange" style="height: 36px; width: 36px; object-fit: contain; margin-right: 12px;">
                                <div>
                                    <h6 class="mb-0 fw-bold">Orange Money</h6>
                                    <small class="text-muted">{{ $orangeCount }} transaction(s)</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-bold">{{ number_format($orangeTotal, 0, ',', ' ') }}</h6>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                    </div>

                    {{-- MTN MoMo --}}
                    <div class="col-xl-3 col-md-6 col-12 mb-3 mb-xl-0">
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/payments/mtn.png') }}" alt="MTN" style="height: 36px; width: 36px; object-fit: contain; margin-right: 12px;">
                                <div>
                                    <h6 class="mb-0 fw-bold">MTN MoMo</h6>
                                    <small class="text-muted">{{ $mtnCount }} transaction(s)</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-bold">{{ number_format($mtnTotal, 0, ',', ' ') }}</h6>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                    </div>

                    {{-- Moov Money --}}
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="p-3 bg-light rounded d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/payments/moov.png') }}" alt="Moov" style="height: 36px; width: 36px; object-fit: contain; margin-right: 12px;">
                                <div>
                                    <h6 class="mb-0 fw-bold">Moov Money</h6>
                                    <small class="text-muted">{{ $moovCount }} transaction(s)</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <h6 class="mb-0 fw-bold">{{ number_format($moovTotal, 0, ',', ' ') }}</h6>
                                <small class="text-muted">FCFA</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABLEAU CLAIR ET PROPRE DES TRANSACTIONS DU JOUR --}}
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><b>TRANSACTIONS INDIVIDUELLES DU JOUR</b></h4>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                        <thead class="bg-primary">
                            <tr>
                                <th class="bb-2">Heure</th>
                                <th class="bb-2">N° Patient</th>
                                <th class="bb-2">Nom du Patient</th>
                                <th class="bb-2">Type</th>
                                <th class="bb-2">Prestation / Motif</th>
                                <th class="bb-2">Mode de Paiement</th>
                                <th class="bb-2">Montant</th>
                                <th class="bb-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $item)
                                @php
                                    $patientCode = '-';
                                    $patientName = '-';
                                    $prestation = '-';

                                    if ($item->type == 'hospitalisation' && $item->hospitalisation) {
                                        $patient = $item->hospitalisation->consultation->patient ?? null;
                                        if ($patient) {
                                            $patientCode = $patient->code_patient;
                                            $patientName = ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '');
                                        }
                                        $prestation = $item->hospitalisation->mot_sortie ?? 'Hospitalisation';
                                    } elseif ($item->type == 'admission' && $item->admission) {
                                        $patient = $item->admission->patient ?? null;
                                        if ($patient) {
                                            $patientCode = $patient->code_patient;
                                            $patientName = ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '');
                                        }
                                        $prestation = $item->admission->prestationHospital->prestationService->libelle ?? 'Admission';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <b>{{ $item->created_at ? $item->created_at->format('H:i') : '--:--' }}</b>
                                    </td>
                                    <td>
                                        <b>{{ $patientCode }}</b>
                                    </td>
                                    <td>
                                        <i>{{ $patientName }}</i>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->type == 'hospitalisation' ? 'badge-warning' : 'badge-primary' }}">
                                            {{ ucfirst($item->type ?? 'Admission') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $prestation }}
                                    </td>
                                    <td>
                                        @if ($item->mode_paiement == 'mobile_money')
                                            @php
                                                $op = $item->operateur_mobile;
                                                $logo = 'wave.png';
                                                if (stripos($op, 'orange') !== false) $logo = 'orange.png';
                                                elseif (stripos($op, 'mtn') !== false) $logo = 'mtn.png';
                                                elseif (stripos($op, 'moov') !== false) $logo = 'moov.png';
                                            @endphp
                                            <span class="d-inline-flex align-items-center">
                                                <img src="{{ asset('assets/images/payments/' . $logo) }}" alt="{{ $op }}" style="height: 20px; width: 20px; object-fit: contain; margin-right: 6px;">
                                                <b>{{ $op ?? 'Mobile Money' }}</b>
                                            </span>
                                            @if ($item->reference_paiement)
                                                <small class="text-muted d-block">Réf: {{ $item->reference_paiement }}</small>
                                            @endif
                                        @else
                                            <span class="badge badge-success">
                                                <i class="fa-solid fa-money-bill-wave me-1"></i> Espèce
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <b>{{ number_format($item->prix, 0, ',', ' ') }} FCFA</b>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('cashier.payment.imprimer', $item->id) }}" target="_blank" class="btn btn-sm btn-info" title="Imprimer le reçu">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
