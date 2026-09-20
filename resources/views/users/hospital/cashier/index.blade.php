@extends('layouts.dashboard')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <h4 class="box-title">Caissières</h4>
                        </div>
                        <div class="">
                            <a href="{{ route('hospital.cashier.add') }}" class="btn btn-success btn-md shadow">Ajouter une Caissière</a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                            <thead>
                            <tr>
                                <th class="bb-2">N°</th>
                                <th class="bb-2">Photo</th>
                                <th class="bb-2">Matricule</th>
                                <th class="bb-2">Nom & Prénoms</th>
                                <th class="bb-2">Contact</th>
                                <th class="bb-2">Disponibilité</th>
                                <th class="bb-2 text-center">Accueil</th>
                                <th class="bb-2 text-center">Status</th>
                                <th class="bb-2 text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($cashiers as $item)
                                <tr>
                                    <td class="text-dark fw-bold fs-6">{{ $loop->index + 1 }}</td>
                                    <td>
                                        @if($item->img_url == null)
                                            <img src="{{ asset('assets/images/user2.png') }}" class="avatar avatar-lg rounded10" alt="Photo de profil"/>
                                        @else
                                            <img src="{{ asset("assets/uploads/cashier/$item->img_url")}}" class="avatar avatar-lg rounded10" alt="Photo de profil"/>
                                        @endif
                                    </td>
                                    <td>{{ $item->matricule }}</td>
                                    <td>
                                        {{ $item->user->name }}
                                    </td>
                                    <td>
                                        {{ $item->contact }}
                                    </td>
                                    <td>
                                            {{ dayIndexNameString(json_decode($item->user->availability->days)) }}
                                        </td>
                                    <td class="text-center">
                                        @if($item->is_accueil)
                                            <span class="badge badge-success-light" title="Gère l'accueil et l'admission des patients">
                                                <i class="fa-solid fa-check-circle me-1 text-success"></i> Activé
                                            </span>
                                        @else
                                            <span class="badge badge-secondary-light" title="Caisse uniquement">
                                                <i class="fa-solid fa-minus-circle me-1 text-muted"></i> Non
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 0)
                                            <span class="text-success">Activé</span>
                                        @else
                                            <span class="text-danger">Désactivé</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('hospital.cashier.status',$item->id) }}" class="btn btn-sm {{ $item->status == 0 ? 'btn-info' : 'btn-success' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Détail"><i class="fa-solid {{ $item->status == 0 ? 'fa-eye-slash' : 'fa-eye' }}"></i></a>
                                        <a href="{{ route('hospital.cashier.show',$item->id) }}" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Modifier"><i class="fa-solid fa-pen-to-square"></i></a>
                                        <a href="{{ route('hospital.cashier.delete',$item->id) }}" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Supprimer"><i class="fa-solid fa-trash"></i></a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ $empty }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
