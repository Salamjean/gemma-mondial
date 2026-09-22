@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box shadow-sm">
            <div class="box-header with-border d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="box-title fw-bold text-dark"><i class="fa fa-hospital-alt text-primary me-2"></i> {{ $title }}</h3>
                    <p class="text-muted mb-0 small">Surveillance des déclarations et de la couverture par centre hospitalier</p>
                </div>
                <div>
                    <span class="badge bg-info fs-14 px-3 py-2">{{ count($hospitals) }} Établissement(s) actif(s)</span>
                </div>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th class="text-center">Établissement</th>
                                <th class="text-center">Localité / Région</th>
                                <th class="text-center">District Sanitaire</th>
                                <th class="text-center">Contact</th>
                                <th class="text-center">Total Naissances</th>
                                <th class="text-center">Total Décès</th>
                                <th class="text-center">Solde Démographique</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hospitals as $item)
                                @php
                                    $solde = $item->total_births - $item->total_deaths;
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center text-start">
                                            <div class="avatar avatar-md bg-light-primary text-primary rounded me-2 d-flex align-items-center justify-content-center fw-bold">
                                                <i class="fa fa-hospital"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->label ?: $item->nom_direction_generale }}</div>
                                                <small class="text-muted">Réf: {{ $item->reference }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-semibold text-dark">
                                            {{ $item->localiteH->name ?? ($item->localite && !is_numeric($item->localite) ? $item->localite : 'Non renseignée') }}
                                        </div>
                                        @if($item->localiteH && $item->localiteH->department)
                                            <small class="text-muted">
                                                {{ $item->localiteH->department->name ?? '' }}
                                                @if($item->localiteH->department->region)
                                                    ({{ $item->localiteH->department->region->name ?? '' }})
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $item->district_sanitaire ?: 'Non assigné' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted small"><i class="fa fa-phone me-1"></i>{{ $item->contact ?: '-' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-13 px-2 py-1"><i class="fa fa-baby me-1"></i> {{ $item->total_births }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger fs-13 px-2 py-1"><i class="fa fa-cross me-1"></i> {{ $item->total_deaths }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($solde > 0)
                                            <span class="badge bg-success-subtle text-success fw-bold">+{{ $solde }} (Excédent)</span>
                                        @elseif($solde < 0)
                                            <span class="badge bg-danger-subtle text-danger fw-bold">{{ $solde }} (Déficit)</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary fw-bold">0 (Neutre)</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('ministere.dashboard', ['hospital_id' => $item->id]) }}" class="btn btn-sm btn-primary" title="Voir les graphes de cet hôpital">
                                                <i class="fa fa-chart-pie me-1"></i> Graphes
                                            </a>
                                            <a href="{{ route('ministere.naissances', ['hospital_id' => $item->id]) }}" class="btn btn-sm btn-outline-info" title="Naissances de cet hôpital">
                                                <i class="fa fa-baby"></i>
                                            </a>
                                            <a href="{{ route('ministere.deces', ['hospital_id' => $item->id]) }}" class="btn btn-sm btn-outline-danger" title="Décès de cet hôpital">
                                                <i class="fa fa-cross"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Aucun établissement enregistré.</td>
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
