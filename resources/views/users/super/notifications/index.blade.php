@extends('layouts.dashboard')

@section('content')
<div class="row">
    <!-- 4 Cartes Statistiques KPI -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body d-flex align-items-center">
                <div class="avatar avatar-lg bg-primary rounded me-15 d-flex align-items-center justify-content-center">
                    <i class="fa fa-mobile-screen fs-24 text-white"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $patientsWithToken }}</h3>
                    <span class="text-muted fs-13">Appareils Connectés</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body d-flex align-items-center">
                <div class="avatar avatar-lg bg-success rounded me-15 d-flex align-items-center justify-content-center">
                    <i class="fa-brands fa-android fs-24 text-white"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $androidDevices }}</h3>
                    <span class="text-muted fs-13">Appareils Android</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body d-flex align-items-center">
                <div class="avatar avatar-lg bg-info rounded me-15 d-flex align-items-center justify-content-center">
                    <i class="fa-brands fa-apple fs-24 text-white"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $iosDevices }}</h3>
                    <span class="text-muted fs-13">Appareils iOS (Apple)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="box">
            <div class="box-body d-flex align-items-center">
                <div class="avatar avatar-lg bg-warning rounded me-15 d-flex align-items-center justify-content-center">
                    <i class="fa fa-bullhorn fs-24 text-white"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $totalBroadcasts }}</h3>
                    <span class="text-muted fs-13">Campagnes Diffusées</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes Flash -->
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2 fs-16 align-middle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fa fa-triangle-exclamation me-2 fs-16 align-middle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Tableau des Diffusions Groupées -->
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="box-title fw-bold">HISTORIQUE DES NOTIFICATIONS PUSH</h4>
                    </div>
                    <div>
                        <a href="{{ route('super.notifications.add') }}" class="btn btn-success btn-md shadow">
                            <i class="fa fa-paper-plane me-1"></i> Nouvelle diffusion
                        </a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <!-- Filtres et Recherche -->
                <form method="GET" action="{{ route('super.notifications.index') }}" class="mb-20">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Rechercher par titre, message ou patient..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Filtrer</button>
                                @if(request('search') || request('category'))
                                    <a href="{{ route('super.notifications.index') }}" class="btn btn-secondary"><i class="fa fa-times"></i></a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Toutes les catégories --</option>
                                <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>ℹ️ Information Générale</option>
                                <option value="alert" {{ request('category') == 'alert' ? 'selected' : '' }}>🚨 Alerte Sanitaire</option>
                                <option value="reminder" {{ request('category') == 'reminder' ? 'selected' : '' }}>⏰ Rappel Médical</option>
                                <option value="update" {{ request('category') == 'update' ? 'selected' : '' }}>🔄 Mise à jour</option>
                                <option value="info" {{ request('category') == 'info' ? 'selected' : '' }}>🏥 Centres & Hôpitaux</option>
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Tableau Groupé -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover display nowrap margin-top-10 w-p100 align-middle">
                        <thead>
                            <tr>
                                <th class="bb-2 text-center">Titre & Message</th>
                                <th class="bb-2 text-center">Cible & Destinataires</th>
                                <th class="bb-2 text-center">Catégorie</th>
                                <th class="bb-2 text-center">Date d'envoi</th>
                                <th class="bb-2 text-center">Canaux de Diffusion</th>
                                <th class="bb-2 text-center" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $batchKey => $group)
                                @php
                                    $first = $group->first();
                                    $data = is_array($first->data) ? $first->data : (json_decode($first->data ?? '[]', true) ?: []);
                                    $category = $data['category'] ?? 'general';
                                    $target = $data['target'] ?? 'all';
                                    $totalRecipients = $group->count();
                                    $pushCount = $group->filter(fn($item) => !empty(optional($item->patient)->fcm_token) || !empty(optional(optional($item->patient)->user)->fcm_token) || ($item->data['has_push'] ?? false))->count();
                                    $inAppOnlyCount = $totalRecipients - $pushCount;
                                    $showUrl = route('super.notifications.show', (string)$batchKey);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <a href="{{ $showUrl }}" class="fw-bold text-dark fs-15 text-decoration-none hover-primary d-block">
                                            {{ $first->title }}
                                        </a>
                                        <div class="text-muted small mx-auto" style="max-width: 380px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $first->message }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            @switch($target)
                                                @case('android')
                                                    <span class="badge bg-success"><i class="fa-brands fa-android me-1"></i> Android</span>
                                                    @break
                                                @case('ios')
                                                    <span class="badge bg-info"><i class="fa-brands fa-apple me-1"></i> iOS</span>
                                                    @break
                                                @case('specific')
                                                    <span class="badge bg-warning text-dark"><i class="fa fa-user me-1"></i> Patient Spécifique</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-primary"><i class="fa fa-globe me-1"></i> Tous les patients</span>
                                            @endswitch

                                            <span class="badge bg-primary-light text-primary border border-primary-light fw-bold">
                                                <i class="fa fa-users me-1"></i> {{ $totalRecipients }} destinataire(s)
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @switch($category)
                                            @case('alert')
                                                <span class="badge bg-danger">Alerte</span>
                                                @break
                                            @case('reminder')
                                                <span class="badge bg-warning text-dark">Rappel</span>
                                                @break
                                            @case('update')
                                                <span class="badge bg-info">Mise à jour</span>
                                                @break
                                            @default
                                                <span class="badge bg-primary">Information</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-dark">{{ $first->created_at ? $first->created_at->format('d/m/Y - H:i') : '-' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1 flex-wrap">
                                            @if($pushCount > 0)
                                                <span class="badge bg-success" title="{{ $pushCount }} patients ont reçu la notification push directe sur leur smartphone">
                                                    <i class="fa fa-paper-plane me-1"></i> {{ $pushCount }} Push FCM
                                                </span>
                                            @endif

                                            @if($inAppOnlyCount > 0)
                                                <span class="badge bg-info" title="{{ $inAppOnlyCount }} patients recevront la notification dans le fil de l'application">
                                                    <i class="fa fa-inbox me-1"></i> {{ $inAppOnlyCount }} In-App
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <!-- Bouton Relancer la notification push -->
                                            <form action="{{ route('super.notifications.resend', (string)$batchKey) }}" method="POST" 
                                                  onsubmit="return confirm('Voulez-vous vraiment relancer et renvoyer cette même notification push ?');" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary shadow-sm" title="Relancer cette notification push">
                                                    Relancer
                                                </button>
                                            </form>

                                            <!-- Bouton Détails & Destinataires -->
                                            <a href="{{ $showUrl }}" class="btn btn-sm btn-info shadow-sm" title="Voir tous les destinataires sur une page dédiée">
                                               Destinataires
                                            </a>

                                            <!-- Bouton Supprimer Campagne -->
                                            <form action="{{ route('super.notifications.destroy', (string)$batchKey) }}" method="POST" 
                                                  onsubmit="return confirm('Confirmez-vous la suppression de cette campagne de diffusion ?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa fa-bell-slash fs-24 mb-2 d-block opacity-50"></i>
                                        Aucune diffusion de notification enregistrée pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination stylisée -->
                <div class="mt-25 pt-15 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted fs-13">
                        @if($campaigns->total() > 0)
                            Affichage de <strong class="text-dark">{{ $campaigns->firstItem() ?? 1 }}</strong> à <strong class="text-dark">{{ $campaigns->lastItem() ?? $campaigns->total() }}</strong> sur <strong class="text-dark">{{ $campaigns->total() }}</strong> diffusion(s) groupée(s)
                        @else
                            Aucune diffusion à afficher
                        @endif
                    </div>
                    <div class="gemma-pagination">
                        {{ $campaigns->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.gemma-pagination .pagination {
    margin-bottom: 0;
    gap: 5px;
    display: flex;
    align-items: center;
}
.gemma-pagination .page-item .page-link {
    border-radius: 8px !important;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 13px;
    padding: 7px 14px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    background-color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
}
.gemma-pagination .page-item:not(.active):not(.disabled) .page-link:hover {
    background-color: #f8fafc;
    color: #0d6efd;
    border-color: #cbd5e1;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
}
.gemma-pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%) !important;
    border-color: #0b5ed7 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(13, 110, 253, 0.35);
}
.gemma-pagination .page-item.disabled .page-link {
    background-color: #f8fafc;
    border-color: #edf2f7;
    color: #94a3b8;
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endsection
