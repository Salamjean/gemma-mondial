@extends('layouts.dashboard', ['title' => 'Passerelle & Export Sage SAARI 100'])

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

                    <div class="form-check mb-15">
                        <input class="form-check-input" type="checkbox" name="mark_exported" value="1" id="markExported" checked>
                        <label class="form-check-label" for="markExported">
                            Marquer comme <strong>« Déjà exportées »</strong>
                        </label>
                    </div>
                </div>
                <div class="box-footer text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ti-download"></i> Télécharger le Fichier Sage (.txt)
                    </button>
                </div>
            </form>
        </div>

        <!-- Guide pas à pas pour le comptable -->
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-help-alt text-info me-2"></i> Guide d'import Sage SAARI</h4>
            </div>
            <div class="box-body">
                <ol class="ps-3 mb-0">
                    <li class="mb-10">Ouvrez <strong>Sage 100 Comptabilité / SAARI</strong>.</li>
                    <li class="mb-10">Allez dans : <strong>Fichier > Importer > Format Sage</strong>.</li>
                    <li class="mb-10">Sélectionnez le fichier <code>.txt</code> téléchargé.</li>
                    <li class="mb-0">Cliquez sur <strong>Valider</strong> : toutes les écritures sont importées.</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Aperçu des Écritures à Exporter -->
    <div class="col-xl-7 col-12">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="ti-view-list text-primary me-2"></i> Écritures sélectionnées</h4>
                <span class="badge badge-primary">{{ count($entriesToExport) }} écritures</span>
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
