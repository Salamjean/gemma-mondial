@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="box-title fw-bold text-dark"><i class="fa-solid fa-building-flag text-primary me-2"></i> Administrateurs DMHP</h3>
                    <p class="text-muted mb-0 small">Utilisateurs ayant le rôle admin pour le compte DMHP</p>
                </div>
                <div>
                    <a href="{{ route('super.ucpfm.add') }}" class="btn btn-primary btn-md shadow">
                        <i class="fa fa-plus-circle me-1"></i> Ajouter un administrateur
                    </a>
                </div>
            </div>

            <div class="box-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="GET" action="{{ route('super.ucpfm.index') }}" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, email, fonction, contact..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Rechercher</button>
                                @if(request('search'))
                                    <a href="{{ route('super.ucpfm.index') }}" class="btn btn-secondary"><i class="fa fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Administrateur</th>
                                <th>Rôle</th>
                                <th>Fonction</th>
                                <th>Contact</th>
                                <th>Date de création</th>
                                <th class="text-center">Statut</th>
                                <th class="text-center" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $item)
                                <tr>
                                    <td class="text-center fw-bold">{{ $admins->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold fs-6">
                                                {{ strtoupper(substr($item->user->name ?? 'A', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $item->user->name ?? '' }} {{ $item->user->prenom ?? '' }}</div>
                                                <div class="text-muted small"><i class="fa fa-envelope me-1"></i>{{ $item->user->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><i class="fa-solid fa-building-flag me-1"></i>Admin DMHP</span>
                                    </td>
                                    <td><span class="text-dark fw-medium">{{ $item->fonction ?: 'Non précisé' }}</span></td>
                                    <td><span class="text-muted"><i class="fa fa-phone me-1"></i>{{ $item->contact ?: '-' }}</span></td>
                                    <td><span class="text-muted small">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</span></td>
                                    <td class="text-center">
                                        @if($item->status == '0')
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Suspendu</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('super.ucpfm.status', $item->id) }}" class="btn btn-sm {{ $item->status == '0' ? 'btn-warning' : 'btn-success' }}" title="{{ $item->status == '0' ? 'Désactiver le compte' : 'Activer le compte' }}">
                                                <i class="fa {{ $item->status == '0' ? 'fa-ban' : 'fa-check' }}"></i>
                                            </a>
                                            <a href="{{ route('super.ucpfm.edit', $item->id) }}" class="btn btn-sm btn-info text-white" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('super.ucpfm.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur DMHP ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-building-flag fa-3x mb-2 text-secondary opacity-50"></i>
                                        <p class="mb-0">Aucun administrateur DMHP enregistré.</p>
                                        <a href="{{ route('super.ucpfm.add') }}" class="btn btn-sm btn-primary mt-2">Ajouter le premier administrateur</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($admins->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $admins->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
