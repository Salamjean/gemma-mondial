@extends('layouts.dashboard', ['title' => 'Comptabilité | Gestion des Dépenses & Charges'])

@section('content')
<!-- Barre d'actions supérieure : Bouton Retour et Export PDF -->
<div class="row mb-15">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('accountant.accounting.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ti-arrow-left me-1"></i> Retour au Tableau de bord
            </a>
        </div>
        <div>
            <a href="{{ route('accountant.accounting.expenses_pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-danger shadow-sm" target="_blank">
                <i class="ti-download me-1"></i> Télécharger l'état en PDF
            </a>
        </div>
    </div>
</div>

<div class="row">
    @if(auth()->user()->role_as == 'accountant')
    <!-- Formulaire d'enregistrement d'une dépense (réservé au Comptable) -->
    <div class="col-xl-4 col-lg-5 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-plus text-danger me-2"></i> Nouvelle Dépense</h4>
            </div>
            <form class="form" action="{{ route('accountant.accounting.store_expense') }}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="form-group mb-15">
                        <label class="form-label">Date de la dépense <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Montant (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="amount" class="form-control" placeholder="ex: 25000" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Compte de Charge (Classe 6) <span class="text-danger">*</span></label>
                        <select name="account_number" class="form-select" required>
                            @foreach($chargeAccounts as $acc)
                                <option value="{{ $acc->account_number }}">
                                    [{{ $acc->account_number }}] {{ $acc->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Motif / Libellé de la dépense <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control" placeholder="ex: Achat gants et seringues, Facture électricité..." required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Mode de règlement <span class="text-danger">*</span></label>
                        <select name="mode_paiement" class="form-select" required>
                            <option value="espece">Caisse Principale (Espèces)</option>
                            <option value="banque">Banque (Virement / Chèque)</option>
                            <option value="wave">Mobile Money - Wave</option>
                            <option value="orange_money">Mobile Money - Orange</option>
                            <option value="mtn">Mobile Money - MTN</option>
                        </select>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Bénéficiaire / Fournisseur</label>
                        <input type="text" name="beneficiaire" class="form-control" placeholder="ex: Pharmacie Centrale, SODECI, CIE...">
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">N° Reçu / Facture / Pièce</label>
                        <input type="text" name="piece_number" class="form-control" placeholder="ex: FACT-2026-089">
                    </div>
                </div>
                <div class="box-footer text-end">
                    <button type="submit" class="btn btn-danger">
                        <i class="ti-save-alt"></i> Enregistrer la dépense
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Liste et Historique des Dépenses -->
    <div class="{{ auth()->user()->role_as == 'accountant' ? 'col-xl-8 col-lg-7 col-12' : 'col-12' }}">
        <!-- Filtre de date -->
        <div class="box mb-15">
            <div class="box-body py-15">
                <form action="{{ route('accountant.accounting.expenses') }}" method="GET" class="row align-items-end g-2">
                    <div class="col-md-4">
                        <label class="form-label small">Date Début</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Date Fin</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="ti-filter"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="ti-money text-danger me-2"></i> Historique des dépenses</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-danger fs-14 py-2 px-3">
                        Total Période : {{ number_format($totalExpenses, 0, ',', ' ') }} FCFA
                    </span>
                    <a href="{{ route('accountant.accounting.expenses_pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-xs btn-outline-danger" title="Exporter PDF" target="_blank">
                        <i class="ti-download"></i> PDF
                    </a>
                </div>
            </div>
            <div class="box-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 95px;">Date</th>
                                <th>Motif & Bénéficiaire</th>
                                <th>Compte</th>
                                <th>Mode</th>
                                <th>Enregistré par</th>
                                <th class="text-end" style="width: 120px;">Montant</th>
                                @if(auth()->user()->role_as == 'accountant')
                                    <th class="text-center" style="width: 70px;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $exp)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $exp->label }}</strong>
                                        @if($exp->beneficiaire)
                                            <small class="text-muted d-block">Bénéficiaire : {{ $exp->beneficiaire }}</small>
                                        @endif
                                        @if($exp->piece_number)
                                            <small class="text-info d-block">Pièce : {{ $exp->piece_number }}</small>
                                        @endif
                                    </td>
                                    <td><code>{{ $exp->account_number }}</code></td>
                                    <td>
                                        <span class="badge badge-info-light">{{ ucfirst(str_replace('_', ' ', $exp->mode_paiement)) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary-light">
                                            <i class="ti-user me-1"></i>
                                            {{ $exp->accountant->user->name ?? 'Comptable' }} {{ $exp->accountant->user->prenom ?? '' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-danger">
                                        {{ number_format($exp->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    @if(auth()->user()->role_as == 'accountant')
                                    <td class="text-center">
                                        <a href="{{ route('accountant.accounting.delete_expense', $exp->id) }}" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')" 
                                           class="btn btn-sm btn-danger-light text-danger" 
                                           title="Supprimer la dépense">
                                            <i class="ti-trash"></i>
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role_as == 'accountant' ? 7 : 6 }}" class="text-center text-muted py-25">Aucune dépense enregistrée sur cette période.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-15">
                    {{ $expenses->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
