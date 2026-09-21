@extends('layouts.dashboard', ['title' => 'Plan Comptable - SYSCOHADA'])

@section('content')
<!-- Barre supérieure avec bouton retour -->
<div class="row mb-15">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('accountant.accounting.dashboard') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="ti-arrow-left me-1"></i> <strong>Retour au tableau de bord</strong>
            </a>
        </div>
        <div>
            <span class="badge badge-primary fs-14 py-2 px-3">{{ count($accounts) }} comptes configurés</span>
        </div>
    </div>
</div>

<div class="row">
    @if(auth()->user()->role_as == 'accountant')
    <!-- Formulaire d'ajout de compte (réservé au Comptable) -->
    <div class="col-xl-4 col-lg-5 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-plus text-primary me-2"></i> Nouveau Compte</h4>
            </div>
            <form class="form" action="{{ route('accountant.accounting.store_compte') }}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="form-group">
                        <label class="form-label">Numéro de compte <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control" placeholder="ex: 706100, 411100" required>
                        <small class="text-muted d-block mt-1">Conforme SYSCOHADA / Sage SAARI</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Intitulé du compte <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control" placeholder="ex: Prestations Médicales..." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nature / Catégorie <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="produit">Produit (Classe 7 - Recettes)</option>
                            <option value="client">Client (Classe 4 - Patients)</option>
                            <option value="tiers">Tiers / Assurance (Classe 4 - Mutuelles)</option>
                            <option value="tresorerie">Trésorerie (Classe 5 - Caisse / Banque)</option>
                            <option value="charge">Charge (Classe 6 - Dépenses)</option>
                            <option value="general">Général (Autre)</option>
                        </select>
                    </div>
                </div>
                <div class="box-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti-save-alt"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Tableau du plan de comptes -->
    <div class="{{ auth()->user()->role_as == 'accountant' ? 'col-xl-8 col-lg-7 col-12' : 'col-12' }}">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="ti-list text-primary me-2"></i> Plan des comptes de l'établissement</h4>
            </div>
            <div class="box-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 120px;">N° Compte</th>
                                <th>Intitulé du compte</th>
                                <th>Catégorie</th>
                                <th class="text-center" style="width: 90px;">Statut</th>
                                @if(auth()->user()->role_as == 'accountant')
                                    <th class="text-center" style="width: 80px;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $acc)
                                <tr>
                                    <td><code>{{ $acc->account_number }}</code></td>
                                    <td><strong>{{ $acc->label }}</strong></td>
                                    <td>
                                        @if($acc->type == 'produit')
                                            <span class="badge badge-success">Produit</span>
                                        @elseif($acc->type == 'tresorerie')
                                            <span class="badge badge-info">Trésorerie</span>
                                        @elseif($acc->type == 'client')
                                            <span class="badge badge-primary">Client / Patient</span>
                                        @elseif($acc->type == 'tiers')
                                            <span class="badge badge-warning">Tiers / Assurance</span>
                                        @elseif($acc->type == 'charge')
                                            <span class="badge badge-danger">Charge</span>
                                        @else
                                            <span class="badge badge-secondary">{{ ucfirst($acc->type) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($acc->is_active)
                                            <span class="badge badge-success"><i class="fa fa-check"></i> Actif</span>
                                        @else
                                            <span class="badge badge-danger">Inactif</span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->role_as == 'accountant')
                                    <td class="text-center">
                                        <a href="{{ route('accountant.accounting.delete_compte', $acc->id) }}" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer le compte [{{ $acc->account_number }} - {{ addslashes($acc->label) }}] ?')" 
                                           class="btn btn-sm btn-danger-light text-danger" 
                                           title="Supprimer ce compte">
                                            <i class="ti-trash"></i>
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role_as == 'accountant' ? 5 : 4 }}" class="text-center text-muted py-20">Aucun compte configuré.</td>
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
