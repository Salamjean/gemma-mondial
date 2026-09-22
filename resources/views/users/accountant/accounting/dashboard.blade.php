@extends('layouts.dashboard', ['title' => 'Tableau de bord Comptabilité'])

@section('content')
<div class="row">
    <!-- Stat 1: Montant Total de la Journée (Recette) -->
    <div class="{{ auth()->user()->role_as == 'accountant' ? 'col-xl-3 col-md-6' : 'col-xl-4 col-md-4' }} col-12">
        <div class="box bg-info-light">
            <div class="box-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="text-dark mb-0 fw-600">{{ number_format($todayTotal ?? 0, 0, ',', ' ') }} FCFA</h4>
                        <p class="text-muted mb-0">Total Recette du Jour</p>
                    </div>
                    <div class="bg-info rounded p-10 text-white">
                        <i class="ti-wallet fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Total Dépenses du Jour -->
    <div class="{{ auth()->user()->role_as == 'accountant' ? 'col-xl-3 col-md-6' : 'col-xl-4 col-md-4' }} col-12">
        <div class="box bg-danger-light">
            <div class="box-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="text-dark mb-0 fw-600">{{ number_format($todayExpenses ?? 0, 0, ',', ' ') }} FCFA</h4>
                        <p class="text-muted mb-0">Dépenses du Jour <small class="text-danger fw-600">(Mois: {{ number_format($monthExpenses ?? 0, 0, ',', ' ') }})</small></p>
                    </div>
                    <div class="bg-danger rounded p-10 text-white">
                        <i class="ti-money fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Total Débits (Mois) -->
    <div class="{{ auth()->user()->role_as == 'accountant' ? 'col-xl-3 col-md-6' : 'col-xl-4 col-md-4' }} col-12">
        <div class="box bg-primary-light">
            <div class="box-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="text-dark mb-0 fw-600">{{ number_format($balance['total_debit'], 0, ',', ' ') }} FCFA</h4>
                        <p class="text-muted mb-0">Total Débits (Mois)</p>
                    </div>
                    <div class="bg-primary rounded p-10 text-white">
                        <i class="ti-arrow-up fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role_as == 'accountant')
    <!-- Stat 4: Écritures à exporter Sage (réservé au Comptable) -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-warning-light">
            <div class="box-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="text-dark mb-0 fw-600">{{ $unexportedEntries }} écriture(s)</h4>
                        <p class="text-muted mb-0">En attente Export Sage</p>
                    </div>
                    <div class="bg-warning rounded p-10 text-white">
                        <i class="ti-export fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Dernières Écritures -->
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="ti-list text-primary me-2"></i> Dernières écritures comptables générées</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('accountant.accounting.sync') }}" class="btn btn-sm btn-outline-secondary" title="Actualiser les écritures">
                        <i class="ti-reload me-1"></i> Actualiser
                    </a>
                    <a href="{{ route('accountant.accounting.journaux') }}" class="btn btn-sm btn-primary">
                        Voir tout
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

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Journal</th>
                                <th>N° Pièce</th>
                                <th>Libellé de l'opération</th>
                                <th>Compte & Mouvements</th>
                                <th class="text-end">Montant</th>
                                @if(auth()->user()->role_as == 'accountant')
                                    <th class="text-center">Statut Sage</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEntries as $entry)
                                <tr>
                                    <td><strong>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}</strong></td>
                                    <td><span class="badge badge-primary">{{ $entry->journal_code }}</span></td>
                                    <td><code>{{ $entry->piece_number }}</code></td>
                                    <td><strong>{{ $entry->libelle }}</strong></td>
                                    <td>
                                        @foreach($entry->lines as $line)
                                            <div class="small d-flex justify-content-between">
                                                <span><code>{{ $line->account_number }}</code> {{ \Illuminate\Support\Str::limit($line->account_label, 28) }}</span>
                                                <span class="fw-500">
                                                    @if($line->debit > 0)
                                                        <span class="text-primary">D: {{ number_format($line->debit, 0, ',', ' ') }}</span>
                                                    @else
                                                        <span class="text-success">C: {{ number_format($line->credit, 0, ',', ' ') }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="text-end fw-600">{{ number_format($entry->totalDebit(), 0, ',', ' ') }} FCFA</td>
                                    @if(auth()->user()->role_as == 'accountant')
                                    <td class="text-center">
                                        @if($entry->is_exported_sage)
                                            <span class="badge badge-success"><i class="fa fa-check"></i> Exporté</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fa fa-clock-o"></i> En attente</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role_as == 'accountant' ? 7 : 6 }}" class="text-center text-muted py-20">Aucune écriture comptable pour le moment.</td>
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
