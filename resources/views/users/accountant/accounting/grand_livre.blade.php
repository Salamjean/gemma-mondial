@extends('layouts.dashboard', ['title' => 'Grand Livre des Comptes'])

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
            <a href="{{ route('accountant.accounting.grand_livre_pdf', ['account_number' => $accountNumber, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-primary shadow-sm" target="_blank">
                <i class="ti-download me-1"></i> Télécharger le Grand Livre en PDF
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Filtres -->
        <div class="box">
            <div class="box-body">
                <form action="{{ route('accountant.accounting.grand_livre') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label">Filtrer par Compte</label>
                        <select name="account_number" class="form-select">
                            <option value="ALL" {{ $accountNumber == 'ALL' ? 'selected' : '' }}>Tous les comptes</option>
                            @foreach($accountsList as $acc)
                                <option value="{{ $acc->account_number }}" {{ $accountNumber == $acc->account_number ? 'selected' : '' }}>
                                    [{{ $acc->account_number }}] {{ $acc->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Début</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Fin</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-5"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Comptes du Grand Livre -->
        @forelse($groupedLines as $accNum => $lines)
            @php
                $totDebit = $lines->sum('debit');
                $totCredit = $lines->sum('credit');
                $solde = $totDebit - $totCredit;
                $accLabel = $lines->first()->account_label ?? '';
            @endphp
            <div class="box mb-20">
                <div class="box-header with-border bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="box-title mb-0">
                            <code class="fs-18">{{ $accNum }}</code> - <strong>{{ $accLabel }}</strong>
                        </h4>
                    </div>
                    <div>
                        <span class="badge badge-primary me-2">Total Débit : {{ number_format($totDebit, 0, ',', ' ') }} FCFA</span>
                        <span class="badge badge-success me-2">Total Crédit : {{ number_format($totCredit, 0, ',', ' ') }} FCFA</span>
                        <span class="badge {{ $solde >= 0 ? 'badge-info' : 'badge-warning' }}">
                            Solde : {{ number_format(abs($solde), 0, ',', ' ') }} FCFA {{ $solde >= 0 ? '(Débiteur)' : '(Créditeur)' }}
                        </span>
                    </div>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 110px;">Date</th>
                                    <th style="width: 80px;">Journal</th>
                                    <th style="width: 140px;">N° Pièce</th>
                                    <th>Libellé de l'opération</th>
                                    <th>Tiers</th>
                                    <th class="text-end" style="width: 140px;">Débit (FCFA)</th>
                                    <th class="text-end" style="width: 140px;">Crédit (FCFA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lines as $l)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($l->entry_date)->format('d/m/Y') }}</td>
                                        <td><span class="badge badge-primary-light">{{ $l->journal_code }}</span></td>
                                        <td><code>{{ $l->piece_number }}</code></td>
                                        <td>{{ $l->libelle }}</td>
                                        <td>{{ $l->third_party_code ?: '-' }}</td>
                                        <td class="text-end text-primary fw-500">
                                            {{ $l->debit > 0 ? number_format($l->debit, 0, ',', ' ') : '-' }}
                                        </td>
                                        <td class="text-end text-success fw-500">
                                            {{ $l->credit > 0 ? number_format($l->credit, 0, ',', ' ') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="box">
                <div class="box-body text-center py-40 text-muted">
                    <i class="fa-solid fa-book-open fs-40 mb-10 text-secondary"></i>
                    <p class="mb-0">Aucun mouvement trouvé pour les critères sélectionnés.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
