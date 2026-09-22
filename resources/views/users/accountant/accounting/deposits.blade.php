@extends('layouts.dashboard', ['title' => 'Comptabilité | Dépôts & Versements Bancaires'])

@section('content')
<!-- Barre d'actions supérieure -->
<div class="row mb-15">
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="mb-0 text-dark fw-600">
                <i class="fa-solid fa-building-columns text-primary me-2"></i> Dépôts & Versements Bancaires Multi-Sources
            </h4>
            <span class="text-muted small">Enregistrement des versements d'espèces, remises de chèques, virements Mobile Money et apports en banque</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('accountant.accounting.deposits_pdf', ['start_date' => $startDate, 'end_date' => $endDate, 'source_type' => $selectedSource]) }}" class="btn btn-sm btn-danger shadow-sm" target="_blank">
                <i class="ti-download me-1"></i> Télécharger l'état en PDF
            </a>
            <a href="{{ route('accountant.accounting.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="ti-arrow-left me-1"></i> Tableau de bord
            </a>
        </div>
    </div>
</div>

<!-- Cartes Statistiques / Indicateurs -->
<div class="row mb-20">
    <div class="col-xl-4 col-md-4 col-12">
        <div class="box bg-primary-light mb-0 shadow-sm border-0">
            <div class="box-body d-flex align-items-center">
                <div class="me-15 bg-primary text-white p-15 rounded-circle fs-24 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Dépôts Aujourd'hui</h6>
                    <h3 class="fw-bold mb-0 text-primary">{{ number_format($todayDeposits, 0, ',', ' ') }} <small class="fs-14">FCFA</small></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-12">
        <div class="box bg-success-light mb-0 shadow-sm border-0">
            <div class="box-body d-flex align-items-center">
                <div class="me-15 bg-success text-white p-15 rounded-circle fs-24 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                    <i class="fa-solid fa-calendar-alt"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Dépôts du Mois en cours</h6>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($monthDeposits, 0, ',', ' ') }} <small class="fs-14">FCFA</small></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-4 col-12">
        <div class="box bg-info-light mb-0 shadow-sm border-0">
            <div class="box-body d-flex align-items-center">
                <div class="me-15 bg-info text-white p-15 rounded-circle fs-24 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                    <i class="fa-solid fa-vault"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Période Sélectionnée</h6>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format($totalDeposits, 0, ',', ' ') }} <small class="fs-14">FCFA</small></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @if(auth()->user()->role_as == 'accountant')
    <!-- Formulaire d'enregistrement d'un versement / dépôt bancaire -->
    <div class="col-xl-4 col-lg-5 col-12">
        <div class="box shadow-sm border-0">
            <div class="box-header with-border bg-light py-10">
                <h4 class="box-title fs-16 fw-600 mb-0">
                    <i class="fa-solid fa-plus-circle text-primary me-2"></i> Nouveau Dépôt / Versement
                </h4>
            </div>
            <form class="form" action="{{ route('accountant.accounting.store_deposit') }}" method="POST">
                @csrf
                <div class="box-body">
                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Date du Dépôt / Valeur <span class="text-danger">*</span></label>
                        <input type="date" name="deposit_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Montant versé (FCFA) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="any" name="amount" id="deposit_amount" class="form-control form-control-lg fw-bold text-success" placeholder="ex: 1500000" required>
                            <span class="input-group-text fw-bold">FCFA</span>
                        </div>
                    </div>

                    <div class="form-group mb-15">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-600 mb-0">Compte Récepteur (Banque / Trésorerie) <span class="text-danger">*</span></label>
                            <a href="{{ route('accountant.accounting.plan_comptable') }}" class="small text-primary fw-600" target="_blank" title="Ajouter un compte bancaire (ex: 512200 BOA, 512300 SGBCI...)">
                                <i class="ti-plus"></i> Nouveau compte
                            </a>
                        </div>
                        <select name="bank_account_number" id="bank_account_number" class="form-select" required>
                            @foreach($bankAccounts as $acc)
                                <option value="{{ $acc->account_number }}" {{ $acc->account_number == '512100' ? 'selected' : '' }}>
                                    [{{ $acc->account_number }}] {{ $acc->label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Vous pouvez créer autant de comptes bancaires que nécessaire dans le <a href="{{ route('accountant.accounting.plan_comptable') }}" target="_blank">Plan Comptable</a> (ex: 512200 BOA, 512300 SGBCI...).</small>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Nom de la Banque / Agence</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="ex: BOA, SGBCI, NSIA, Ecobank, BICICI...">
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600 text-primary">Provenance / Source des Fonds <span class="text-danger">*</span></label>
                        <select name="source_type" id="source_type_select" class="form-select bg-light fw-600" required>
                            <option value="caisse" data-account="531100" data-label="Versement espèces Caisse Principale" data-mode="espece">💵 Caisse Principale (Espèces)</option>
                            <option value="mobile_money" data-account="518100" data-label="Virement Trésorerie Mobile Money vers Banque" data-mode="mobile_money">📱 Mobile Money (Wave, Orange, MTN)</option>
                            <option value="cheque" data-account="511200" data-label="Remise de chèques à l'encaissement" data-mode="cheque">📝 Remise de Chèque(s)</option>
                            <option value="assurance" data-account="411200" data-label="Virement / Règlement Tiers Assurances" data-mode="virement">🏢 Virement Assurance / Tiers-Payant</option>
                            <option value="apport_associe" data-account="455100" data-label="Apport personnel / Compte courant associé" data-mode="virement">👤 Apport Associé / Promoteur</option>
                            <option value="subvention_don" data-account="771000" data-label="Subvention d'exploitation / Don reçu" data-mode="virement">🎁 Subvention / Don / Partenaire</option>
                            <option value="autre" data-account="" data-label="Autre versement bancaire" data-mode="virement">⚙️ Autre Compte Personnalisé</option>
                        </select>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Compte Source à Créditer (SYSCOHADA) <span class="text-danger">*</span></label>
                        <select name="source_account_number" id="source_account_select" class="form-select" required>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->account_number }}" {{ $acc->account_number == '531100' ? 'selected' : '' }}>
                                    [{{ $acc->account_number }}] {{ $acc->label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Le compte crédité voit son solde diminuer du montant versé.</small>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Mode de Versement <span class="text-danger">*</span></label>
                        <select name="mode_depot" id="mode_depot_select" class="form-select" required>
                            <option value="espece">Versement d'Espèces</option>
                            <option value="cheque">Remise de Chèque(s)</option>
                            <option value="virement">Virement Bancaire</option>
                            <option value="mobile_money">Transfert Digital / Mobile Money</option>
                        </select>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">N° Bordereau de Remise / Référence reçu</label>
                        <input type="text" name="reference_piece" class="form-control" placeholder="ex: BORD-2026-045, CHQ-88921...">
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Nom du Déposant / Émetteur</label>
                        <input type="text" name="depositor_name" class="form-control" placeholder="ex: M. KONE (Coursier), Dr. KOUAME, AXA Assurances...">
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Motif / Libellé de l'opération <span class="text-danger">*</span></label>
                        <input type="text" name="label" id="deposit_label_input" class="form-control" value="Versement espèces Caisse Principale" placeholder="ex: Versement des recettes du 20 au 22 septembre" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-600">Observation / Détails complémentaires</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Informations complémentaires, détail des coupures ou numéros de chèques..."></textarea>
                    </div>
                </div>
                <div class="box-footer bg-light text-end">
                    <button type="submit" class="btn btn-primary fw-600 shadow-sm w-150 py-10">
                        <i class="ti-save-alt me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Liste et Historique des Dépôts Bancaires -->
    <div class="{{ auth()->user()->role_as == 'accountant' ? 'col-xl-8 col-lg-7 col-12' : 'col-12' }}">
        <!-- Filtres -->
        <div class="box mb-15 shadow-sm border-0">
            <div class="box-body py-15">
                <form action="{{ route('accountant.accounting.deposits') }}" method="GET" class="row align-items-end g-2">
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-600">Date Début</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-600">Date Fin</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label small fw-600">Source</label>
                        <select name="source_type" class="form-select form-select-sm">
                            <option value="">-- Toutes les sources --</option>
                            <option value="caisse" {{ $selectedSource == 'caisse' ? 'selected' : '' }}>💵 Caisse Espèces</option>
                            <option value="mobile_money" {{ $selectedSource == 'mobile_money' ? 'selected' : '' }}>📱 Mobile Money</option>
                            <option value="cheque" {{ $selectedSource == 'cheque' ? 'selected' : '' }}>📝 Remise Chèques</option>
                            <option value="assurance" {{ $selectedSource == 'assurance' ? 'selected' : '' }}>🏢 Assurances</option>
                            <option value="apport_associe" {{ $selectedSource == 'apport_associe' ? 'selected' : '' }}>👤 Apports Associés</option>
                            <option value="subvention_don" {{ $selectedSource == 'subvention_don' ? 'selected' : '' }}>🎁 Subventions / Dons</option>
                            <option value="autre" {{ $selectedSource == 'autre' ? 'selected' : '' }}>⚙️ Autre</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-12">
                        <label class="form-label small fw-600">Recherche</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Réf, motif, déposant...">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="ti-search"></i>
                            </button>
                            @if(!empty($search))
                                <a href="{{ route('accountant.accounting.deposits', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-secondary" title="Effacer la recherche">
                                    <i class="ti-close"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des dépôts -->
        <div class="box shadow-sm border-0">
            <div class="box-header with-border py-12 d-flex justify-content-between align-items-center">
                <h4 class="box-title fs-16 fw-600 mb-0">
                    <i class="ti-receipt text-primary me-2"></i> Historique des Versements Bancaires
                </h4>
                <span class="badge bg-primary-light text-primary fw-600 fs-13 px-10 py-5">
                    {{ $deposits->count() }} opération(s)
                </span>
            </div>
            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Date</th>
                                <th>Réf / Bordereau</th>
                                <th>Banque & Source</th>
                                <th>Motif & Déposant</th>
                                <th class="text-end">Montant Versé</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deposits as $deposit)
                            @php
                                $badgeClass = match($deposit->source_type) {
                                    'caisse' => 'bg-success-light text-success border border-success',
                                    'mobile_money' => 'bg-purple-light text-purple border border-purple',
                                    'cheque' => 'bg-info-light text-info border border-info',
                                    'assurance' => 'bg-warning-light text-warning border border-warning',
                                    'apport_associe' => 'bg-primary-light text-primary border border-primary',
                                    'subvention_don' => 'bg-danger-light text-danger border border-danger',
                                    default => 'bg-secondary-light text-secondary border'
                                };
                                $sourceLabel = match($deposit->source_type) {
                                    'caisse' => '💵 Caisse Espèces',
                                    'mobile_money' => '📱 Mobile Money',
                                    'cheque' => '📝 Remise Chèque',
                                    'assurance' => '🏢 Assurance',
                                    'apport_associe' => '👤 Apport Associé',
                                    'subvention_don' => '🎁 Subvention/Don',
                                    default => '⚙️ Autre Source'
                                };
                            @endphp
                            <tr>
                                <td class="text-nowrap">
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($deposit->deposit_date)->format('d/m/Y') }}</span>
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($deposit->created_at)->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($deposit->reference_piece)
                                        <span class="badge bg-light text-dark border fw-600"><i class="ti-bookmark me-1"></i> {{ $deposit->reference_piece }}</span>
                                    @else
                                        <span class="badge bg-light text-muted">DEP-{{ str_pad($deposit->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        <i class="fa-solid fa-building-columns text-primary me-1"></i>
                                        [{{ $deposit->bank_account_number }}] {{ $deposit->bank_name ?: 'Banque' }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge {{ $badgeClass }} fs-11 px-8 py-3">
                                            {{ $sourceLabel }} ({{ $deposit->source_account_number }})
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-600 text-dark">{{ $deposit->label }}</div>
                                    @if($deposit->depositor_name)
                                        <small class="text-muted"><i class="ti-user me-1"></i>Déposant: <strong>{{ $deposit->depositor_name }}</strong></small>
                                    @endif
                                    @if($deposit->description)
                                        <div class="small text-muted fst-italic">{{ Str::limit($deposit->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold fs-15 text-success">
                                        + {{ number_format($deposit->amount, 0, ',', ' ') }} FCFA
                                    </span>
                                    <br><small class="text-muted text-uppercase">{{ ucfirst($deposit->mode_depot) }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('accountant.accounting.deposit_pdf', $deposit->id) }}" class="btn btn-sm btn-outline-info" title="Imprimer le bordereau de remise (PDF)">
                                            <i class="ti-printer"></i>
                                        </a>
                                        @if(auth()->user()->role_as == 'accountant')
                                        <a href="{{ route('accountant.accounting.delete_deposit', $deposit->id) }}" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Confirmez-vous l\'annulation et la suppression de ce versement bancaire ? L\'écriture comptable associée sera également supprimée.')" 
                                           title="Supprimer">
                                            <i class="ti-trash"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-40 text-muted">
                                    <i class="fa-solid fa-building-columns fs-40 text-light mb-15 d-block"></i>
                                    <h5>Aucun dépôt bancaire enregistré sur cette période.</h5>
                                    <p class="small">Utilisez le formulaire à gauche pour enregistrer un versement d'espèces, une remise de chèque ou un virement en banque.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($deposits->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end fw-bold fs-14">TOTAL DES DÉPÔTS :</th>
                                <th class="text-end fw-bold fs-16 text-success">+ {{ number_format($totalDeposits, 0, ',', ' ') }} FCFA</th>
                                <th></th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
                @if($deposits->hasPages())
                <div class="px-15 py-10 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="text-muted small">
                        Affichage de <strong>{{ $deposits->firstItem() }}</strong> à <strong>{{ $deposits->lastItem() }}</strong> sur <strong>{{ $deposits->total() }}</strong> versements
                    </span>
                    <div>
                        {{ $deposits->withQueryString()->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sourceSelect = document.getElementById('source_type_select');
        const accountSelect = document.getElementById('source_account_select');
        const modeSelect = document.getElementById('mode_depot_select');
        const labelInput = document.getElementById('deposit_label_input');

        if (sourceSelect && accountSelect) {
            sourceSelect.addEventListener('change', function() {
                const selectedOption = sourceSelect.options[sourceSelect.selectedIndex];
                const targetAccount = selectedOption.getAttribute('data-account');
                const defaultLabel = selectedOption.getAttribute('data-label');
                const defaultMode = selectedOption.getAttribute('data-mode');

                if (targetAccount) {
                    accountSelect.value = targetAccount;
                }
                if (defaultMode && modeSelect) {
                    modeSelect.value = defaultMode;
                }
                if (defaultLabel && labelInput && (labelInput.value === '' || labelInput.value.startsWith('Versement') || labelInput.value.startsWith('Virement') || labelInput.value.startsWith('Remise') || labelInput.value.startsWith('Apport') || labelInput.value.startsWith('Subvention'))) {
                    labelInput.value = defaultLabel;
                }
            });
        }
    });
</script>
@endpush
@endsection
