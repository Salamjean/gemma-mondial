@extends('layouts.dashboard', ['title' => 'Journaux des Écritures Comptables'])

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
            <a href="{{ route('accountant.accounting.journaux_pdf', ['journal' => $journalCode, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-primary shadow-sm" target="_blank">
                <i class="ti-download me-1"></i> Télécharger le Journal en PDF
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Filtres -->
        <div class="box">
            <div class="box-body">
                <form action="{{ route('accountant.accounting.journaux') }}" method="GET" class="row align-items-end g-2">
                    <div class="col-md-2 col-6">
                        <label class="form-label small fw-600">Journal</label>
                        <select name="journal" class="form-select form-select-sm">
                            <option value="ALL" {{ $journalCode == 'ALL' ? 'selected' : '' }}>Tous les journaux</option>
                            @foreach($journals as $j)
                                <option value="{{ $j->code }}" {{ $journalCode == $j->code ? 'selected' : '' }}>
                                    [{{ $j->code }}] {{ $j->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small fw-600">Date Début</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small fw-600">Date Fin</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label small fw-600">Recherche</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="N° pièce, libellé, compte...">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ti-search"></i>
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('accountant.accounting.journaux', ['journal' => $journalCode, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-secondary" title="Effacer la recherche">
                                    <i class="ti-close"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2 col-6 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="ti-filter me-1"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des Écritures -->
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="fa-solid fa-book-journal-whills me-5"></i> Écritures comptables en partie double</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('accountant.accounting.journaux_pdf', ['journal' => $journalCode, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="ti-download me-1"></i> Imprimer PDF
                    </a>
                    <a href="{{ route('accountant.accounting.export_sage') }}" class="btn btn-sm btn-success">
                        <i class="fa-solid fa-file-arrow-down me-5"></i> Exporter vers Sage SAARI
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 100px;">Date</th>
                                <th style="width: 80px;">Journal</th>
                                <th style="width: 130px;">N° Pièce</th>
                                <th>Compte</th>
                                <th>Tiers / Auxiliaire</th>
                                <th>Libellé de l'écriture</th>
                                <th class="text-end" style="width: 130px;">Débit (FCFA)</th>
                                <th class="text-end" style="width: 130px;">Crédit (FCFA)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $entry)
                                @foreach($entry->lines as $index => $line)
                                    <tr class="{{ $index == 0 ? 'border-top-2 border-dark' : '' }}">
                                        @if($index == 0)
                                            <td rowspan="{{ count($entry->lines) }}" class="align-middle bg-white fw-600">
                                                {{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}
                                            </td>
                                            <td rowspan="{{ count($entry->lines) }}" class="align-middle bg-white text-center">
                                                <span class="badge badge-primary">{{ $entry->journal_code }}</span>
                                            </td>
                                            <td rowspan="{{ count($entry->lines) }}" class="align-middle bg-white">
                                                <code>{{ $entry->piece_number }}</code>
                                            </td>
                                        @endif
                                        <td>
                                            <code>{{ $line->account_number }}</code>
                                            <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($line->account_label, 30) }}</small>
                                        </td>
                                        <td>
                                            @if($line->third_party_code)
                                                <span class="badge badge-info-light">{{ $line->third_party_code }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $line->libelle ?: $entry->libelle }}</td>
                                        <td class="text-end fw-600 text-primary">
                                            {{ $line->debit > 0 ? number_format($line->debit, 0, ',', ' ') : '-' }}
                                        </td>
                                        <td class="text-end fw-600 text-success">
                                            {{ $line->credit > 0 ? number_format($line->credit, 0, ',', ' ') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-20 text-muted">Aucune écriture trouvée pour cette sélection.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($entries->hasPages())
                <div class="px-15 py-10 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="text-muted small">
                        Affichage de <strong>{{ $entries->firstItem() }}</strong> à <strong>{{ $entries->lastItem() }}</strong> sur <strong>{{ $entries->total() }}</strong> écritures
                    </span>
                    <div>
                        {{ $entries->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
