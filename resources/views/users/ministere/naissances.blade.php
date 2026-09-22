@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box shadow-sm">
            <div class="box-header with-border d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="box-title fw-bold text-dark"><i class="fa fa-baby text-primary me-2"></i> {{ $title }}</h3>
                    <p class="text-muted mb-0 small">Consultez l'ensemble des déclarations de naissances enregistrées sur le territoire</p>
                </div>
                <div>
                    <span class="badge bg-primary fs-14 px-3 py-2">Total : {{ $declarations->total() }} déclaration(s)</span>
                </div>
            </div>

            <div class="box-body">
                <!-- Filtres et Recherche -->
                <form method="GET" action="{{ route('ministere.naissances') }}" class="mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fs-12 fw-bold text-muted">Recherche rapide</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Nom enfant, N° déclaration, lieu, hôpital..." value="{{ request('search') }}">
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
                        <div class="col-md-2">
                            <label class="form-label fs-12 fw-bold text-muted">Genre</label>
                            <select name="genre" class="form-select form-select-sm">
                                <option value="">Tous les genres</option>
                                <option value="M" {{ request('genre') == 'M' ? 'selected' : '' }}>Masculin (Garçon)</option>
                                <option value="F" {{ request('genre') == 'F' ? 'selected' : '' }}>Féminin (Fille)</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fa fa-filter me-1"></i> Filtrer</button>
                            <a href="{{ route('ministere.naissances') }}" class="btn btn-secondary btn-sm" title="Réinitialiser"><i class="fa fa-times"></i></a>
                        </div>
                    </div>
                </form>

                <!-- Tableau des Naissances -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th class="text-center">N° Déclaration</th>
                                <th class="text-center">Enfant (Nom & Prénom)</th>
                                <th class="text-center">Genre</th>
                                <th class="text-center">Date & Heure</th>
                                <th class="text-center">Lieu de naissance</th>
                                <th class="text-center">Établissement Déclarant</th>
                                <th class="text-center">Médecin déclarant</th>
                                <th class="text-center">Enregistré le</th>
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
                                        <div class="fw-bold text-dark">{{ $item->enfant->user->name ?? 'Nouveau-né' }} {{ $item->enfant->user->prenom ?? '' }}</div>
                                        @if($item->enfant && $item->enfant->code_patient)
                                            <small class="text-muted">Code DM: {{ $item->enfant->code_patient }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(in_array(strtolower($item->genre), ['m', 'masculin']))
                                            <span class="badge bg-primary-subtle text-primary"><i class="fa fa-mars me-1"></i> Masculin</span>
                                        @elseif(in_array(strtolower($item->genre), ['f', 'feminin', 'féminin']))
                                            <span class="badge bg-danger-subtle text-danger"><i class="fa fa-venus me-1"></i> Féminin</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item->genre ?: 'Non précisé' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-medium text-dark">{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d/m/Y') : '-' }}</div>
                                        <small class="text-muted">{{ $item->heure ? 'à '.$item->heure : '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-dark">{{ $item->lieu ?: '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium text-dark">{{ $item->declaration->hospital->label ?? ($item->declaration->hospital->nom_direction_generale ?? 'Hôpital') }}</span>
                                        @if($item->declaration && $item->declaration->hospital && $item->declaration->hospital->district_sanitaire)
                                            <br><small class="text-muted">District: {{ $item->declaration->hospital->district_sanitaire }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted small">
                                            {{ $item->declaration->doctor->user->name ?? '' }} {{ $item->declaration->doctor->user->prenom ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa fa-baby fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-0">Aucune déclaration de naissance trouvée avec ces critères.</p>
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
