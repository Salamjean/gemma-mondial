@extends('layouts.dashboard', ['title' => 'Tous les paiements en attente'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <div class="row align-items-center">
                        <div class="col-xs-12 col-xl-8 col-lg-8 col-md-8 col-sm-8">
                            <h4 class="box-title">
                                <b>TOUS LES PAIEMENTS EN ATTENTE (SANS FILTRE DE DATE)</b>
                            </h4>
                        </div>
                        <div class="col-xs-12 col-xl-4 col-lg-4 col-md-4 col-sm-4 text-end">
                            <span class="badge badge-info fs-14">
                                <i class="fa-solid fa-clock me-1"></i> Total en attente : {{ count($payments) }}
                            </span>
                        </div>
                    </div>
                </div>
                <br />
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                            <thead class="bg-primary">
                                <tr>
                                    <th class="bb-2">Date de demande</th>
                                    <th class="bb-2">Code Patient</th>
                                    <th class="bb-2">Type</th>
                                    <th class="bb-2">Motif / Prestation</th>
                                    <th class="bb-2">Nom du Patient</th>
                                    <th class="bb-2">Montant</th>
                                    <th class="bb-2 text-center">Statut</th>
                                    <th class="bb-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $item)
                                    <tr>
                                        <td>
                                            @if ($item->created_at->isToday())
                                                <span class="badge badge-success fs-13">
                                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning fs-13" data-bs-toggle="tooltip" title="Demande en attente d'un jour antérieur">
                                                    <i class="fa-solid fa-clock-rotate-left me-1"></i>{{ $item->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->type == 'hospitalisation')
                                                <b>{{ $item->hospitalisation->consultation->patient->code_patient ?? 'N/A' }}</b>
                                            @elseif ($item->type == 'admission')
                                                <b>{{ $item->admission->patient->code_patient ?? 'N/A' }}</b>
                                            @else
                                                <b>-</b>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary text-uppercase">{{ $item->type }}</span>
                                        </td>
                                        <td>
                                            @if ($item->type == 'hospitalisation')
                                                Hospitalisation
                                            @elseif ($item->type == 'admission')
                                                {{ $item->admission->prestationHospital->prestationService->libelle ?? 'N/A' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->type == 'hospitalisation')
                                                <i>{{ $item->hospitalisation->consultation->patient->user->name ?? '' }}&nbsp;
                                                    {{ $item->hospitalisation->consultation->patient->user->prenom ?? '' }}</i>
                                            @elseif ($item->type == 'admission')
                                                <i>{{ $item->admission->patient->user->name ?? '' }}&nbsp;
                                                    {{ $item->admission->patient->user->prenom ?? '' }}</i>
                                            @else
                                                <i>-</i>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark fs-14">{{ number_format($item->prix, 0, ',', ' ') }} FCFA</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-warning">
                                                En attente
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('cashier.admission.show', $item->id) }}"
                                                style="cursor: pointer" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Procéder au paiement">
                                                <i class="fa-solid fa-credit-card me-1"></i> Procéder au paiement
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
