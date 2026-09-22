@extends('layouts.dashboard', ['title' => 'Registre des déclarations de décès'])

@section('content')
<style>
    .deces-list-header {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }
    .table-deces {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-deces thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
        vertical-align: middle;
        text-align: center;
    }
    .table-deces tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        text-align: center;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.92rem;
    }
    .table-deces tbody tr:hover {
        background-color: #f8fafc;
    }
    .badge-ref {
        background-color: #fee2e2;
        color: #b91c1c;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 5px 10px;
        border-radius: 8px;
        border: 1px solid #fecaca;
        display: inline-block;
    }
    .badge-patient-type {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 600;
        font-size: 0.78rem;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
</style>

<div class="row justify-content-center">
    <div class="col-12">
        <div class="box shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
            
            <!-- EN-TÊTE DE LA PAGE -->
            <div class="box-header py-4 px-4 bg-white border-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                            <i class="fa-solid fa-file-medical fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="box-title mb-1 fw-bold text-dark">REGISTRE DES DÉCÈS</h4>
                            <p class="text-muted mb-0 small">Historique des déclarations et certificats médicaux de décès</p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('doctor.declaration.deces.direct') }}" class="btn btn-danger btn-md px-4 py-2 fw-bold shadow-sm rounded-pill">
                            <i class="fa-solid fa-circle-plus me-2"></i> Enregistrer un décès
                        </a>
                    </div>
                </div>
            </div>

            <div class="box-body p-4">
                
                <!-- BANNIÈRE DE SUCCÈS -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between p-3 rounded-4 shadow-sm mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-circle-check fa-2x me-3 text-success"></i>
                            <div>
                                <h6 class="fw-bold mb-0 text-success">{{ session('success') }}</h6>
                                <p class="mb-0 small text-muted">L'acte médical a été validé et archivé.</p>
                            </div>
                        </div>
                        @if(session('latest_declaration_id'))
                            <a target="_blank" href="{{ route('doctor.declaration.certificat.deces', session('latest_declaration_id')) }}" class="btn btn-danger btn-sm px-3 py-2 fw-semibold shadow rounded-pill">
                                <i class="fa-solid fa-print me-1"></i> Imprimer le Certificat (PDF)
                            </a>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- BARRE DE RECHERCHE -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-5">
                        <form method="GET" action="{{ route('doctor.declaration.deces.list') }}">
                            <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <span class="input-group-text bg-white border-end-0 text-muted">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Rechercher par référence, nom ou code patient..." value="{{ request('search') }}">
                                @if(request('search'))
                                    <a href="{{ route('doctor.declaration.deces.list') }}" class="btn btn-light border" title="Effacer le filtre">
                                        <i class="fa-solid fa-xmark text-muted"></i>
                                    </a>
                                @endif
                                <button type="submit" class="btn btn-primary px-4 fw-semibold">Filtrer</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TABLEAU PRINCIPAL STRUCTURÉ (TOUTES LES COLONNES CENTRÉES) -->
                <div class="table-responsive rounded-4 border">
                    <table class="table table-deces mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 140px;">N° Référence</th>
                                <th class="text-center">Personne Décédée</th>
                                <th class="text-center" style="width: 130px;">Genre & Âge</th>
                                <th class="text-center" style="width: 170px;">Date & Heure</th>
                                <th class="text-center">Lieu du Décès</th>
                                <th class="text-center">Médecin Déclarant</th>
                                <th class="text-center" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($declarations as $item)
                                <tr>
                                    <!-- 1. Référence -->
                                    <td class="text-center">
                                        <span class="badge-ref">{{ $item->reference }}</span>
                                    </td>

                                    <!-- 2. Personne Décédée (Nom + Code) -->
                                    <td class="text-center">
                                        <div class="fw-bold text-dark fs-6">
                                            {{ optional(optional($item->patient)->user)->name ?? 'Nom inconnu' }} 
                                            {{ optional(optional($item->patient)->user)->prenom ?? '' }}
                                        </div>
                                        <div class="small text-muted">
                                            Code DM : <span class="text-primary fw-semibold">{{ optional($item->patient)->code_patient ?? 'N/A' }}</span>
                                            @if(optional($item->deces)->person == 'enfant')
                                                <span class="badge bg-warning text-dark ms-1" style="font-size: 0.7rem;">Nouveau-né</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 3. Genre & Âge -->
                                    <td class="text-center">
                                        <div class="text-dark fw-semibold text-capitalize">
                                            <i class="fa-solid fa-venus-mars text-muted me-1 small"></i>{{ optional($item->deces)->genre ?? optional($item->patient)->gender ?? '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            @if(optional($item->deces)->age !== null)
                                                {{ optional($item->deces)->age }} an(s)
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 4. Date & Heure du Décès -->
                                    <td class="text-center">
                                        <div class="fw-semibold text-dark">
                                            <i class="fa-regular fa-calendar-days text-danger me-1"></i>
                                            {{ optional($item->deces)->date ? \Carbon\Carbon::parse($item->deces->date)->format('d/m/Y') : \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                                        </div>
                                        @if(optional($item->deces)->heure)
                                            <div class="small text-muted">
                                                <i class="fa-regular fa-clock text-muted me-1"></i>{{ $item->deces->heure }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 5. Lieu du Décès -->
                                    <td class="text-center">
                                        <div class="text-dark fw-semibold">
                                            <i class="fa-solid fa-location-dot text-secondary me-1"></i>
                                            {{ optional($item->deces)->lieu ?? optional($item->hospital)->label ?? 'Centre Hospitalier' }}
                                        </div>
                                        @if(optional($item->deces)->cause_initiale)
                                            <small class="text-muted d-block text-truncate mx-auto" style="max-width: 220px;" title="{{ $item->deces->cause_initiale }}">
                                                Cause : {{ $item->deces->cause_initiale }}
                                            </small>
                                        @endif
                                    </td>

                                    <!-- 6. Médecin Déclarant -->
                                    <td class="text-center">
                                        <div class="small fw-semibold text-dark">
                                            Dr. {{ optional(optional($item->doctor)->user)->name ?? '' }} {{ optional(optional($item->doctor)->user)->prenom ?? '' }}
                                        </div>
                                    </td>

                                    <!-- 7. Actions -->
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <a href="{{ route('doctor.declaration.deces.show', $item->id) }}" class="btn btn-sm btn-outline-info btn-action" title="Voir les détails complets">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a target="_blank" href="{{ route('doctor.declaration.certificat.deces', $item->id) }}" class="btn btn-sm btn-danger btn-action" title="Imprimer le certificat médical de décès (PDF)">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div class="py-3">
                                            <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted opacity-50"></i>
                                            <h6 class="fw-bold text-secondary">Aucune déclaration de décès enregistrée</h6>
                                            <p class="small text-muted mb-0">Les décès enregistrés apparaîtront ici avec leurs certificats.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION HARMONISÉE -->
                @if(method_exists($declarations, 'hasPages') && $declarations->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                        <div class="text-muted small">
                            Affichage de <b>{{ $declarations->firstItem() ?? 0 }}</b> à <b>{{ $declarations->lastItem() ?? 0 }}</b> sur <b>{{ $declarations->total() }}</b> déclarations
                        </div>
                        <div>
                            {{ $declarations->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
