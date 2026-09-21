@extends('layouts.dashboard', ['title' => 'Comptabilité | Suivi des Assurances & Recouvrement'])

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
            <a href="{{ route('accountant.accounting.assurances_pdf') }}" class="btn btn-sm btn-primary shadow-sm" target="_blank">
                <i class="ti-download me-1"></i> Télécharger l'état en PDF
            </a>
        </div>
    </div>
</div>

<!-- Cartes de Synthèse Globale -->
<div class="row">
    <div class="col-xl-4 col-md-4 col-12">
        <div class="box bg-primary-light">
            <div class="box-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="text-dark mb-0 fw-600">{{ number_format($grandTotalPriseEnCharge, 0, ',', ' ') }} FCFA</h4>
                        <p class="text-muted mb-0">Total Prises en Charge Facturées</p>
                    </div>
                    <div class="bg-primary rounded p-10 text-white">
                        <i class="ti-shield fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-12">
        <div class="box bg-success-light">
            <div class="box-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="text-dark mb-0 fw-600">{{ number_format($grandTotalRegle, 0, ',', ' ') }} FCFA</h4>
                        <p class="text-muted mb-0">Total Recouvré / Encaissé</p>
                    </div>
                    <div class="bg-success rounded p-10 text-white">
                        <i class="ti-check-box fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4 col-12">
        <div class="box bg-warning-light">
            <div class="box-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="text-dark mb-0 fw-600">{{ number_format($grandTotalReste, 0, ',', ' ') }} FCFA</h4>
                        <p class="text-muted mb-0">Reste Global à Recouvrer</p>
                    </div>
                    <div class="bg-warning rounded p-10 text-white">
                        <i class="ti-time fs-24"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @if(auth()->user()->role_as == 'accountant')
    <!-- Formulaire d'encaissement d'un règlement d'assureur (réservé au Comptable) -->
    <div class="col-xl-4 col-lg-5 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-plus text-success me-2"></i> Encaisser un Règlement Assureur</h4>
            </div>
            <form class="form" action="{{ route('accountant.accounting.store_settlement') }}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="form-group mb-15">
                        <label class="form-label">Compagnie d'Assurance / Mutuelle <span class="text-danger">*</span></label>
                        <select name="type_assurance_id" class="form-select" required id="selectAssurance">
                            <option value="">Sélectionnez un assureur...</option>
                            @foreach($assurancesList as $assur)
                                <option value="{{ $assur->id }}">{{ $assur->libelle ?? $assur->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Date du règlement <span class="text-danger">*</span></label>
                        <input type="date" name="settlement_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Montant reçu (FCFA) <span class="text-danger">*</span></label>
                        <input type="number" step="any" name="amount" class="form-control" placeholder="ex: 500000" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Mode de règlement <span class="text-danger">*</span></label>
                        <select name="mode_paiement" class="form-select" required>
                            <option value="virement">Virement Bancaire</option>
                            <option value="cheque">Chèque</option>
                            <option value="espece">Espèces / Caisse</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">N° Chèque / Réf. Virement</label>
                        <input type="text" name="reference_piece" class="form-control" placeholder="ex: CHQ-897654 / VIR-0988">
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Note / Observation</label>
                        <textarea name="note" rows="2" class="form-control" placeholder="ex: Règlement bordereau du mois d'Août..."></textarea>
                    </div>
                </div>
                <div class="box-footer text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ti-save-alt"></i> Enregistrer l'encaissement
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Tableau de Synthèse par Assureur & Règlements -->
    <div class="{{ auth()->user()->role_as == 'accountant' ? 'col-xl-8 col-lg-7 col-12' : 'col-12' }}">
        <!-- État des Créances par Compagnie -->
        <div class="box mb-20">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="ti-stats-up text-primary me-2"></i> État des Créances par Assureur</h4>
                <a href="{{ route('accountant.accounting.assurances_pdf') }}" class="btn btn-xs btn-outline-primary" title="Exporter PDF" target="_blank">
                    <i class="ti-download"></i> PDF
                </a>
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
                        <thead class="bg-light">
                            <tr>
                                <th>Assurance / Mutuelle</th>
                                <th class="text-end">Total Prise en Charge</th>
                                <th class="text-end">Total Recouvré</th>
                                <th class="text-end">Reste à Payer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assurancesSummary as $row)
                                <tr>
                                    <td><strong>{{ $row['nom'] }}</strong></td>
                                    <td class="text-end fw-600 text-dark">
                                        {{ number_format($row['total_prise_en_charge'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end fw-600 text-success">
                                        {{ number_format($row['total_regle'], 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="text-end fw-bold {{ $row['reste_a_payer'] > 0 ? 'text-danger' : 'text-muted' }}">
                                        {{ number_format($row['reste_a_payer'], 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-20">Aucune assurance configurée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historique des Règlements Reçus -->
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-receipt text-success me-2"></i> Historique des Règlements Encaissés</h4>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 95px;">Date</th>
                                <th>Assurance</th>
                                <th>Mode & Réf.</th>
                                <th>Encaissé par</th>
                                <th class="text-end" style="width: 130px;">Montant Reçu</th>
                                @if(auth()->user()->role_as == 'accountant')
                                    <th class="text-center" style="width: 70px;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($settlements as $st)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($st->settlement_date)->format('d/m/Y') }}</td>
                                    <td><strong>{{ $st->typeAssurance->libelle ?? $st->typeAssurance->nom ?? 'Assurance' }}</strong></td>
                                    <td>
                                        <span class="badge badge-info-light">{{ ucfirst($st->mode_paiement) }}</span>
                                        @if($st->reference_piece)
                                            <small class="text-muted d-block">Réf: {{ $st->reference_piece }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary-light">
                                            <i class="ti-user me-1"></i>
                                            {{ $st->accountant->user->name ?? 'Comptable' }} {{ $st->accountant->user->prenom ?? '' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($st->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    @if(auth()->user()->role_as == 'accountant')
                                    <td class="text-center">
                                        <a href="{{ route('accountant.accounting.delete_settlement', $st->id) }}" 
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce règlement ?')" 
                                           class="btn btn-sm btn-danger-light text-danger" 
                                           title="Supprimer le règlement">
                                            <i class="ti-trash"></i>
                                        </a>
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role_as == 'accountant' ? 6 : 5 }}" class="text-center text-muted py-20">Aucun règlement enregistré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-15">
                    {{ $settlements->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
