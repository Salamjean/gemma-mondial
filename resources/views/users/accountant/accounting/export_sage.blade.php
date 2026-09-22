@extends('layouts.dashboard', ['title' => 'Passerelle & Export Sage'])

@section('content')
<div class="row">
    <!-- Panneau de Téléchargement -->
    <div class="col-xl-5 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-export text-primary me-2"></i> Exporter vers Sage SAARI</h4>
            </div>
            <form action="{{ route('accountant.accounting.export_sage_download') }}" method="GET">
                <div class="box-body">
                    <p class="text-muted mb-20">
                        Générez le fichier d'écritures conforme au <strong>format standard d'importation Sage 100 / SAARI (.txt)</strong>.
                    </p>

                    <div class="form-group mb-15">
                        <label class="form-label">Date de début <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label">Date de fin <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" required>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-bold">Modèle d'export Sage 100 i7 <span class="text-danger">*</span></label>
                        <select name="format_type" class="form-select" id="formatType">
                            <option value="sage100_standard" selected>Sage 100 i7 Standard (Journal;Date;Pièce;Compte;Tiers;Libellé;Sens;Montant) - Recommandé</option>
                            <option value="excel_csv">Tableur Excel (.csv avec en-têtes et colonnes complètes)</option>
                            <option value="sage100_debit_credit">Sage 100 i7 Débit/Crédit (Journal;Date;Pièce;Compte;Tiers;Libellé;Débit;Crédit)</option>
                            <option value="sage100_pnm">Sage 100 i7 Natif d'échange (.pnm avec balise #FLG 000)</option>
                            <option value="sage100_tab">Sage 100 i7 Délimité par Tabulation</option>
                        </select>
                        <small class="text-muted">Garantit la conformité stricte avec Sage 100 Comptabilité i7 et Excel.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-15">
                            <label class="form-label fw-bold">Code Journal Caisse</label>
                            <input type="text" name="journal_caisse" value="CAIS" class="form-control" placeholder="ex: CAIS ou CAI" required>
                        </div>
                        <div class="col-md-6 form-group mb-15">
                            <label class="form-label fw-bold">Code Journal Banque</label>
                            <input type="text" name="journal_banque" value="BQ1" class="form-control" placeholder="ex: BQ1 ou BQ" required>
                        </div>
                    </div>

                    <div class="form-group mb-15">
                        <label class="form-label fw-bold">Séparateur Décimal</label>
                        <select name="decimal_separator" class="form-select">
                            <option value="." selected>Point (ex: 15000.00)</option>
                            <option value=",">Virgule (ex: 15000,00)</option>
                        </select>
                    </div>

                    <div class="form-check mb-15">
                        <input class="form-check-input" type="checkbox" name="mark_exported" value="1" id="markExported" checked>
                        <label class="form-check-label fw-semibold" for="markExported">
                            Marquer les écritures comme <strong>« Déjà exportées »</strong>
                        </label>
                    </div>
                </div>
                <div class="box-footer d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <button type="submit" formaction="{{ route('accountant.accounting.export_excel_download') }}" class="btn btn-success fw-bold">
                        <i class="fa-solid fa-file-excel me-1"></i> Exporter en Excel (.xls)
                    </button>
                    <button type="submit" class="btn btn-primary fw-bold px-20">
                        <i class="ti-download me-1"></i> Exporter pour Sage (.txt)
                    </button>
                </div>
            </form>
        </div>

        <!-- Guide pas à pas pour Sage 100 Comptabilité i7 -->
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-help-alt text-info me-2"></i> Guide Sage 100 Comptabilité i7</h4>
            </div>
            <div class="box-body">
                <div class="alert alert-info py-10 px-15 mb-15 fs-13">
                    <i class="fa-solid fa-circle-info me-1"></i> Fichier 100% optimisé pour <strong>Sage 100 Comptabilité i7</strong> et <strong>Microsoft Excel</strong>.
                </div>
                <div class="mb-15">
                    <strong class="text-dark d-block mb-1 fs-13"><i class="fa-solid fa-arrow-right-long text-success me-1"></i> Option 1 : Export Tableur Excel (.xls)</strong>
                    <p class="fs-13 text-muted mb-0">Cliquez sur le bouton vert <strong>« Exporter en Excel »</strong> pour obtenir un tableau stylisé avec totaux, ouvrable directement sous Excel.</p>
                </div>
                <div class="mb-15">
                    <strong class="text-dark d-block mb-1 fs-13"><i class="fa-solid fa-arrow-right-long text-primary me-1"></i> Option 2 : Import dans Sage 100 i7 (Format Paramétrable)</strong>
                    <ol class="ps-3 mb-0 fs-13 text-muted">
                        <li>Téléchargez le fichier <code>.txt</code> ci-dessus.</li>
                        <li>Dans Sage 100 i7, menu <strong>Fichier > Importer > Format Paramétrable</strong>.</li>
                    </ol>
                </div>
                <div>
                    <strong class="text-dark d-block mb-1 fs-13"><i class="fa-solid fa-arrow-right-long text-warning me-1"></i> Option 3 : Copier / Coller direct depuis Excel</strong>
                    <p class="fs-13 text-muted mb-0">Ouvrez le fichier Excel téléchargé, copiez les lignes de votre journal, puis faites un <strong>Clic Droit > Coller</strong> directement dans la grille de saisie du journal Sage.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aperçu des Écritures à Exporter -->
    <div class="col-xl-7 col-12">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="box-title mb-0"><i class="ti-view-list text-primary me-2"></i> Écritures sélectionnées</h4>
                    <span class="badge badge-primary ms-2">{{ count($entriesToExport) }} écritures</span>
                </div>
                <a href="{{ route('accountant.accounting.export_excel_download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-success fw-bold">
                    <i class="fa-solid fa-file-excel me-1"></i> Télécharger Excel (.xls)
                </a>
            </div>
            <div class="box-body">
                <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Date</th>
                                <th>Journal</th>
                                <th>N° Pièce</th>
                                <th>Libellé</th>
                                <th class="text-end">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entriesToExport as $entry)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}</td>
                                    <td><span class="badge badge-primary">{{ $entry->journal_code }}</span></td>
                                    <td><code>{{ $entry->piece_number }}</code></td>
                                    <td>{{ $entry->libelle }}</td>
                                    <td class="text-end fw-600">{{ number_format($entry->totalDebit(), 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-20 text-muted">Aucune écriture trouvée sur cette plage de dates.</td>
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
