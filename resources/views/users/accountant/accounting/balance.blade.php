@extends('layouts.dashboard', ['title' => 'Balance Générale des Comptes'])

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
            <a href="{{ route('accountant.accounting.balance_pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-primary shadow-sm" target="_blank">
                <i class="ti-download me-1"></i> Télécharger la Balance en PDF
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Filtres -->
        <div class="box">
            <div class="box-body">
                <form action="{{ route('accountant.accounting.balance') }}" method="GET" class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date Début</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date Fin</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-filter me-5"></i> Calculer la Balance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau de la Balance -->
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="fa-solid fa-scale-balanced me-5"></i> Balance des Comptes (Période : {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})</h4>
                <div class="d-flex align-items-center gap-2">
                    @if($balance['is_balanced'])
                        <span class="badge badge-success fs-14 py-2 px-3"><i class="fa-solid fa-check me-1"></i> Balance Équilibrée (Débit = Crédit)</span>
                    @else
                        <span class="badge badge-danger fs-14 py-2 px-3"><i class="fa-solid fa-triangle-exclamation me-1"></i> Écart Détecté</span>
                    @endif
                    <a href="{{ route('accountant.accounting.balance_pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-xs btn-outline-primary" title="Exporter PDF" target="_blank">
                        <i class="ti-download"></i> PDF
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th rowspan="2" class="align-middle" style="width: 140px;">N° Compte</th>
                                <th rowspan="2" class="align-middle">Intitulé du Compte</th>
                                <th colspan="2" class="text-center bg-primary-light">Mouvements de la période</th>
                                <th colspan="2" class="text-center bg-info-light">Soldes Finaux</th>
                            </tr>
                            <tr>
                                <th class="text-end" style="width: 150px;">Débit (FCFA)</th>
                                <th class="text-end" style="width: 150px;">Crédit (FCFA)</th>
                                <th class="text-end" style="width: 150px;">Solde Débiteur</th>
                                <th class="text-end" style="width: 150px;">Solde Créditeur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balance['accounts'] as $row)
                                <tr>
                                    <td><code class="fs-15 fw-600">{{ $row['account_number'] }}</code></td>
                                    <td><strong>{{ $row['account_label'] }}</strong></td>
                                    <td class="text-end fw-500 text-primary">
                                        {{ $row['total_debit'] > 0 ? number_format($row['total_debit'], 0, ',', ' ') : '-' }}
                                    </td>
                                    <td class="text-end fw-500 text-success">
                                        {{ $row['total_credit'] > 0 ? number_format($row['total_credit'], 0, ',', ' ') : '-' }}
                                    </td>
                                    <td class="text-end fw-600 text-info">
                                        {{ $row['solde_debiteur'] > 0 ? number_format($row['solde_debiteur'], 0, ',', ' ') : '-' }}
                                    </td>
                                    <td class="text-end fw-600 text-warning">
                                        {{ $row['solde_crediteur'] > 0 ? number_format($row['solde_crediteur'], 0, ',', ' ') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-20 text-muted">Aucune donnée disponible pour cette période.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-dark text-white fw-700">
                            <tr>
                                <td colspan="2" class="text-uppercase">TOTAUX GÉNÉRAUX</td>
                                <td class="text-end text-primary-light">{{ number_format($balance['total_debit'], 0, ',', ' ') }}</td>
                                <td class="text-end text-success-light">{{ number_format($balance['total_credit'], 0, ',', ' ') }}</td>
                                <td class="text-end text-info-light">{{ number_format($balance['total_solde_debiteur'], 0, ',', ' ') }}</td>
                                <td class="text-end text-warning-light">{{ number_format($balance['total_solde_crediteur'], 0, ',', ' ') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
