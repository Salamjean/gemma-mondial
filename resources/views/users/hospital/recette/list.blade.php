@extends('layouts.dashboard', ['title' => 'Liste des recettes collectées'])

@php
    $grandTotal = $consultations->sum('somme');
    $grandTotalEspece = $consultations->sum('somme_espece');
    $grandTotalMobile = $consultations->sum('somme_mobile_money');
    $grandTotalNb = $consultations->sum('nb');

    $pourcentEspece = $grandTotal > 0 ? round(($grandTotalEspece / $grandTotal) * 100) : 0;
    $pourcentMobile = $grandTotal > 0 ? round(($grandTotalMobile / $grandTotal) * 100) : 0;
@endphp

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="row align-items-center">
                    <div class="col-md-8 col-12">
                        <h4 class="box-title">
                            <b>RECETTES GLOBALES DE L'HÔPITAL</b>
                        </h4>
                    </div>
                    <div class="col-md-4 col-12 text-md-end text-start mt-2 mt-md-0">
                        <a href="{{ route('hospital.recette.day', date('Y-m-d')) }}" class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-calendar-day me-1"></i> Recette d'Aujourd'hui
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CARTES DE SYNTHÈSE GLOBALE --}}
<div class="row">
    {{-- TOTAL GENERAL CUMULE --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-primary">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-white-50 mb-1 text-uppercase fs-12">Recette Globale</p>
                        <h3 class="text-white fw-bold mb-0">{{ number_format($grandTotal, 0, ',', ' ') }} <small class="fs-13 text-white-50">FCFA</small></h3>
                        <small class="text-white-50">{{ $consultations->count() }} journée(s) enregistrée(s)</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-vault text-white-50 fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL CUMULE ESPECE --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Total en Espèce</p>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($grandTotalEspece, 0, ',', ' ') }} <small class="fs-13 text-muted">FCFA</small></h3>
                        <small class="text-muted">{{ $pourcentEspece }}% du total global</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-money-bill-wave text-success fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL CUMULE MOBILE MONEY --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Total Mobile Money</p>
                        <h3 class="text-dark fw-bold mb-0">{{ number_format($grandTotalMobile, 0, ',', ' ') }} <small class="fs-13 text-muted">FCFA</small></h3>
                        <small class="text-muted">{{ $pourcentMobile }}% du total global</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-mobile-screen-button text-primary fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL ACTES --}}
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 text-uppercase fs-12">Total Actes</p>
                        <h3 class="text-dark fw-bold mb-0">{{ $grandTotalNb }} <small class="fs-13 text-muted">actes</small></h3>
                        <small class="text-muted">Toutes caisses confondues</small>
                    </div>
                    <div>
                        <i class="fa-solid fa-list-check text-info fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABLEAU DES RECETTES PAR JOUR --}}
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><b>JOURNAL DES RECETTES DE L'ÉTABLISSEMENT</b></h4>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                        <thead class="bg-primary">
                            <tr>
                                <th class="bb-2">Date</th>
                                <th class="bb-2 text-center">Nombre d'actes</th>
                                <th class="bb-2 text-end">Montant Espèce</th>
                                <th class="bb-2 text-end">Montant Mobile Money</th>
                                <th class="bb-2 text-end">Total Journée</th>
                                <th class="bb-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consultations as $item)
                                @php
                                    $isToday = $item->jour === date('Y-m-d');
                                @endphp
                                <tr>
                                    <td>
                                        <b>{{ dateCompletFr($item->jour) }}</b>
                                        @if($isToday)
                                            <span class="badge badge-success ms-2">Aujourd'hui</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <b>{{ $item->nb }}</b>
                                    </td>
                                    <td class="text-end">
                                        <b class="text-success">{{ number_format($item->somme_espece ?? 0, 0, ',', ' ') }} FCFA</b>
                                    </td>
                                    <td class="text-end">
                                        <b class="text-primary">{{ number_format($item->somme_mobile_money ?? 0, 0, ',', ' ') }} FCFA</b>
                                    </td>
                                    <td class="text-end">
                                        <b>{{ number_format($item->somme, 0, ',', ' ') }} FCFA</b>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('hospital.recette.day', $item->jour) }}" class="btn btn-sm btn-primary" title="Voir le détail de la journée">
                                            <i class="fa-solid fa-eye me-1"></i> Détails
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
