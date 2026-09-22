@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box shadow-sm">
            <div class="box-header with-border d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="box-title fw-bold text-dark"><i class="fa fa-cross text-danger me-2"></i> {{ $title }}</h3>
                    <p class="text-muted mb-0 small">Registre officiel et surveillance des causes de mortalité hospitalière</p>
                </div>
                <div>
                    <span class="badge bg-danger fs-14 px-3 py-2">Total : {{ $declarations->total() }} décès déclaré(s)</span>
                </div>
            </div>

            <div class="box-body">
                <!-- Filtres et Recherche -->
                <form method="GET" action="{{ route('ministere.deces') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fs-12 fw-bold text-muted">Recherche rapide</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nom défunt, N° déclaration, cause..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-12 fw-bold text-muted">Établissement</label>
                            <select name="hospital_id" class="form-select form-select-sm">
                                <option value="">Tous les établissements</option>
                                @foreach($hospitals as $h)
                                    <option value="{{ $h->id }}" {{ request('hospital_id') == $h->id ? 'selected' : '' }}>{{ $h->label ?: $h->nom_direction_generale }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fs-12 fw-bold text-muted">Mortalité maternelle</label>
                            <select name="deces_maternel" class="form-select form-select-sm">
                                <option value="">Tous les décès</option>
                                <option value="1" {{ request('deces_maternel') == '1' ? 'selected' : '' }}>Décès maternels uniquement</option>
                                <option value="0" {{ request('deces_maternel') === '0' ? 'selected' : '' }}>Non maternels</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-danger btn-sm flex-grow-1"><i class="fa fa-filter me-1"></i> Filtrer</button>
                            <a href="{{ route('ministere.deces') }}" class="btn btn-secondary btn-sm" title="Réinitialiser"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                </form>

                <!-- Tableau des Décès -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th class="text-center">N° Déclaration</th>
                                <th class="text-center">Défunt (Nom & Prénom)</th>
                                <th class="text-center">Âge / Genre</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Date du décès</th>
                                <th class="text-center">Causes (Initiale / Directe)</th>
                                <th class="text-center">Établissement Déclarant</th>
                                <th class="text-center">Médecin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($declarations as $item)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration + ($declarations->currentPage() - 1) * $declarations->perPage() }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border fw-bold">{{ $item->numero_declaration ?: ($item->reference ?: '#'.$item->id) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-dark">
                                            @if($item->person == 'enfant')
                                                <i class="fa fa-baby text-muted me-1"></i> Nouveau-né
                                            @else
                                                {{ $item->declaration->patient->user->name ?? 'Patient' }} {{ $item->declaration->patient->user->prenom ?? '' }}
                                            @endif
                                        </div>
                                        @if($item->declaration && $item->declaration->patient && $item->declaration->patient->code_patient)
                                            <small class="text-muted">Code DM: {{ $item->declaration->patient->code_patient }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <span class="fw-medium">{{ $item->age ? $item->age.' ans' : ($item->person == 'enfant' ? '< 1 an' : 'Âge non précisé') }}</span>
                                        </div>
                                        <small class="text-muted">
                                            @if(in_array(strtolower($item->genre), ['m', 'masculin']))
                                                <i class="fa fa-mars text-primary"></i> Masculin
                                            @elseif(in_array(strtolower($item->genre), ['f', 'feminin', 'féminin']))
                                                <i class="fa fa-venus text-danger"></i> Féminin
                                            @else
                                                {{ $item->genre ?: '-' }}
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($item->deces_maternel)
                                            <span class="badge bg-warning text-dark"><i class="fa fa-female me-1"></i> Décès Maternel</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Standard</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-medium text-dark">{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d/m/Y') : '-' }}</div>
                                        <small class="text-muted">{{ $item->heure ? 'à '.$item->heure : '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($item->cause_initiale)
                                            <div class="fw-semibold text-dark"><small>Initiale :</small> {{ $item->cause_initiale }}</div>
                                        @endif
                                        @if($item->cause_directe)
                                            <div class="text-muted small"><small>Directe :</small> {{ $item->cause_directe }}</div>
                                        @endif
                                        @if(!$item->cause_initiale && !$item->cause_directe)
                                            <span class="text-muted italic small">Non spécifiée</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium text-dark">{{ $item->declaration->hospital->label ?? ($item->declaration->hospital->nom_direction_generale ?? 'Hôpital') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted small">
                                            {{ $item->declaration->doctor->user->name ?? '' }} {{ $item->declaration->doctor->user->prenom ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa fa-cross fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0">Aucun décès trouvé avec ces critères.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($declarations->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $declarations->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
