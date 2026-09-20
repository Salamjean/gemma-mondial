@extends('layouts.dashboard', ['title' => 'Recette Caissière - ' . dateCompletFr($day)])

@php
    $totalRecette = $payments->sum('prix');
    $especePayments = $payments->filter(fn($p) => $p->mode_paiement === 'espece' || empty($p->mode_paiement));
    $totalEspece = $especePayments->sum('prix');
    $mobilePayments = $payments->filter(fn($p) => $p->mode_paiement === 'mobile_money');
    $totalMobile = $mobilePayments->sum('prix');
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row align-items-center">
                    <div class="col-md-8 col-12">
                        <h4 class="box-title">
                            <b>DÉTAIL ENCAISSEMENTS : {{ strtoupper($cashier->user->name ?? 'Caissière') }} {{ strtoupper($cashier->user->prenom ?? '') }}</b>
                            <span class="text-muted fs-13 ms-2">({{ dateCompletFr($day) }})</span>
                        </h4>
                    </div>
                    <div class="col-md-4 col-12 text-md-end text-start mt-2 mt-md-0">
                        <a href="{{ route('hospital.recette.day', $day) }}" class="btn btn-sm btn-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Retour à la journée
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CARTES DE SYNTHÈSE DE LA CAISSIÈRE --}}
<div class="row">
    <div class="col-xl-4 col-md-4 col-12">
        <div class="box bg-primary">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-white-50 mb-1 text-uppercase fs-12">Total Encaissé</p>
                        <h3 class="text-white fw-bold mb-0">{{ number_format($totalRecette, 0, ',', ' ') }} <small class="fs-13 text-white-50">FCFA</small></h3>
                        <small class="text-white-50">{{ count($payments) }} acte(s)</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-wallet text-white-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Paiements en Espèce</p>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($totalEspece, 0, ',', ' ') }} <small class="fs-13 text-muted">FCFA</small></h3>
                        <small class="text-muted">{{ $especePayments->count() }} transaction(s)</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-money-bill-wave text-success fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Paiements Mobile Money</p>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($totalMobile, 0, ',', ' ') }} <small class="fs-13 text-muted">FCFA</small></h3>
                        <small class="text-muted">{{ $mobilePayments->count() }} transaction(s)</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-mobile-screen-button text-primary fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABLEAU DES TRANSACTIONS DE LA CAISSIÈRE --}}
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><b>TRANSACTIONS EFFECTUÉES PAR CETTE CAISSIÈRE</b></h4>
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
                                <th class="bb-2 text-end">Montant</th>
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
                                    <td class="text-end">
                                        <b>{{ number_format($item->prix, 0, ',', ' ') }} FCFA</b>
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
