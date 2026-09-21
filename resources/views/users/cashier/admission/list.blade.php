@extends('layouts.dashboard', ['title' => 'Liste des Patients pour une consultation'])

@section('content')
    <div class="row">
        <div class="col-12  ">
            <div class="box">
                <div class="box-header">
                    <div class="row">
                        <div class="col-xs-12  col-xl-9 col-lg-9 col-md-9 col-sm-9">
                            <h4 class="box-title"><b>
                                    @if ($type == 'all')
                                        ADMISSIONS ET HOSPITALISATIONS
                                    @elseif ($type == 'admission')
                                        ADMISSIONS
                                    @else
                                        HOSPITALISATIONS
                                    @endif
                                </b></h4>
                        </div>
                    </div>
                </div>
                <br /><br />
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                            <thead class="bg-primary">
                                <tr>
                                    <th class="bb-2">Date</th>
                                    <th class="bb-2">Type</th>
                                    <th class="bb-2">N° Patient</th>
                                    <th class="bb-2">Nom du Patient</th>
                                    <th class="bb-2">Agent en charge</th>
                                    <th class="bb-2">Services</th>
                                    <th class="bb-2">Acte médical</th>
                                    <th class="bb-2">Montant</th>
                                    <th class="bb-2 text-center">Mode Paiement</th>
                                    <th class="bb-2 text-center">Status</th>
                                    <th class="bb-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($payments as $item)
                                    <tr>
                                        <td><b>{{ dateNumberFr($item->created_at) }}</b></td>
                                        <td>{{ $item->type }}</td>
                                        <td>
                                            @if ($item->type == 'hospitalisation')
                                                <b>{{ $item->hospitalisation->consultation->patient->code_patient }}</b>
                                            @elseif ($item->type == 'admission')
                                                <b>{{ $item->admission->patient->code_patient }}</b>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->type == 'hospitalisation')
                                                <i>{{ $item->hospitalisation->consultation->patient->user->name }}&nbsp;
                                                    {{ $item->hospitalisation->consultation->patient->user->prenom }}</i>
                                            @elseif ($item->type == 'admission')
                                                <i>{{ $item->admission->patient->user->name }}&nbsp;
                                                    {{ $item->admission->patient->user->prenom }}</i>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->type == 'hospitalisation')
                                                <i>{{ $item->hospitalisation->consultation->doctor->user->name }}</i>
                                            @elseif ($item->type == 'admission')
                                                @if ($item->admission->doctor)
                                                    <i>{{ $item->admission->doctor->user->name }}</i>
                                                @elseif ($item->admission->infirmier)
                                                    <i>{{ $item->admission->infirmier->user->name }}</i>
                                                @endif
                                            @endif

                                        </td>

                                        <td>
                                            <span class="badge badge-primary">
                                                @if ($item->type == 'hospitalisation')
                                                    Hospitalisation
                                                @elseif ($item->type == 'admission')
                                                    {{ $item->admission->prestationHospital->prestationService->libelle }}
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->type == 'hospitalisation')
                                                {{ $item->hospitalisation->consultation->admission->prestationHospital->prestationService->service->libelle }}
                                            @elseif ($item->type == 'admission')
                                                {{ $item->admission->prestationHospital->prestationService->service->libelle }}
                                            @endif
                                        </td>

                                        <td class="text-white fw-bold fs-6"> <span
                                                class="badge badge-dark">{{ $item->prix }} F CFA</span> </td>
                                        <td class="text-center">
                                            @if ($item->status == 'success')
                                                @if ($item->mode_paiement == 'gtc')
                                                    <span class="badge bg-success-light text-success fw-bold">
                                                        <i class="fa-solid fa-gift me-1"></i> GTC
                                                    </span>
                                                @elseif ($item->mode_paiement == 'mobile_money')
                                                    <span class="badge badge-info" title="{{ $item->reference_paiement ? 'Réf: ' . $item->reference_paiement : '' }}">
                                                        <i class="fa-solid fa-mobile-screen-button me-1"></i> {{ $item->operateur_mobile ?? 'Mobile Money' }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        <i class="fa-solid fa-money-bill-1 me-1"></i> Espèce
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-dark fw-bold fs-6">
                                            @if ($item->status == 'success')
                                                @if ($item->mode_paiement == 'gtc')
                                                    <span class="badge badge-success">GTC (Pris en charge)</span>
                                                @else
                                                    <span class="badge badge-success">Payé</span>
                                                @endif
                                                <a class="btn btn-sm btn-none" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title="approuvé">
                                                    <i class="d-flex no-block fa fa-check-circle text-success"></i>
                                                </a>
                                            @else
                                                <span class="badge badge-warning">Paiement en attente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status == 'pending')
                                                <a class="btn btn-sm btn-info"
                                                    href="{{ route('cashier.admission.show', $item->id) }}"
                                                    style="cursor: pointer" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title="Valider">
                                                    Procéder au paiement
                                                </a>
                                            @else
                                                <a href="{{ route('cashier.payment.imprimer', $item->id) }}"
                                                    class="btn btn-sm btn-info" target="_blank" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title="Imprimer">
                                                    <i class="fa-solid fa-print"></i>
                                                </a>
                                            @endif
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
