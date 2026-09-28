@extends('layouts.dashboard')

@section('content')
<div class="row">
    <!-- En-tête -->
    <div class="col-12 mb-20">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="box-title fw-bold text-dark mb-1">
                    <i class="fa fa-bell text-primary me-2"></i> DÉTAIL DE LA DIFFUSION PUSH & IN-APP
                </h3>
                <p class="text-muted mb-0 fs-13">
                    Consultez les informations de la diffusion et la répartition des envois Push FCM et In-App.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super.notifications.index') }}" class="btn btn-secondary btn-md shadow">
                    <i class="fa fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <!-- Carte 1 : Récapitulatif de la Campagne -->
    <div class="col-12 mb-20">
        <div class="box">
            <div class="box-header with-border bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-18 fw-bold text-dark">{{ $first->title }}</span>
                        @switch($category)
                            @case('alert')
                                <span class="badge bg-danger">Alerte Sanitaire</span>
                                @break
                            @case('reminder')
                                <span class="badge bg-warning text-dark">Rappel Médical</span>
                                @break
                            @case('update')
                                <span class="badge bg-info">Mise à jour</span>
                                @break
                            @default
                                <span class="badge bg-primary">Information</span>
                        @endswitch
                    </div>

                    <div>
                        <span class="text-muted fs-12 me-10">Lot : <code>{{ $batchKey }}</code></span>
                        <form action="{{ route('super.notifications.destroy', $batchKey) }}" method="POST" 
                              onsubmit="return confirm('Confirmez-vous la suppression de cette campagne ?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger shadow-sm">
                                <i class="fa fa-trash me-1"></i> Supprimer la campagne
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <!-- Message -->
                    <div class="col-lg-7 col-12 mb-15">
                        <label class="form-label fw-bold text-muted fs-12 mb-5">CORPS DU MESSAGE DIFFUSÉ</label>
                        <div class="p-15 rounded bg-light border text-dark fs-14 fw-500" style="line-height: 1.6; word-break: break-word;">
                            {{ $first->message }}
                        </div>
                    </div>

                    <!-- Métadonnées & Répartition -->
                    <div class="col-lg-5 col-12">
                        <label class="form-label fw-bold text-muted fs-12 mb-5">RÉSUMÉ & RÉPARTITION DE L'ENVOI</label>
                        <div class="p-15 rounded border bg-white">
                            <div class="d-flex justify-content-between mb-10 pb-5 border-bottom">
                                <span class="text-muted fs-13">Date d'envoi :</span>
                                <span class="fw-bold text-dark fs-13">{{ $sentAt }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-10 pb-5 border-bottom">
                                <span class="text-muted fs-13">Diffusé par :</span>
                                <span class="fw-bold text-dark fs-13">{{ $sentByName }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-10 pb-5 border-bottom">
                                <span class="text-muted fs-13">Total Destinataires :</span>
                                <span class="badge bg-primary fs-12">{{ $totalRecipients }} patient(s)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-10 pb-5 border-bottom">
                                <span class="text-muted fs-13">Délivrés en Push Mobile FCM :</span>
                                <span class="badge bg-success fs-12"><i class="fa fa-paper-plane me-1"></i> {{ $pushDeliveredCount }} appareil(s)</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted fs-13">Enregistrés In-App (Fil App) :</span>
                                <span class="badge bg-info fs-12"><i class="fa fa-inbox me-1"></i> {{ $inAppOnlyCount }} patient(s)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Tableau des Destinataires -->
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title fw-bold">
                    <i class="fa fa-users text-primary me-2"></i> LISTE DÉTAILLÉE DES DESTINATAIRES ({{ $totalRecipients }})
                </h4>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover display nowrap margin-top-10 w-p100 align-middle">
                        <thead>
                            <tr>
                                <th class="bb-2 text-center" style="width: 50px;">N°</th>
                                <th class="bb-2 text-center">Code Patient</th>
                                <th class="bb-2 text-center">Nom & Prénoms</th>
                                <th class="bb-2 text-center">Téléphone</th>
                                <th class="bb-2 text-center">Type Appareil</th>
                                <th class="bb-2 text-center">Canal de Réception</th>
                                <th class="bb-2 text-center">Statut de Lecture</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recipients as $item)
                                @php
                                    $p = $item->patient;
                                    $pUser = optional($p)->user;
                                    $pName = trim(($pUser->name ?? '') . ' ' . ($pUser->prenom ?? ''));
                                    if (empty($pName)) {
                                        $pName = 'Patient #' . (optional($p)->code_patient ?? $item->patient_id);
                                    }
                                    $hasPushToken = !empty(optional($p)->fcm_token) || !empty(optional($pUser)->fcm_token) || ($item->data['has_push'] ?? false);
                                    $devType = strtoupper(optional($p)->device_type ?: (optional($pUser)->device_type ?: 'ANDROID'));
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">#{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <span class="fw-bold text-primary">{{ optional($p)->code_patient ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-dark">{{ $pName }}</div>
                                        <div class="text-muted small">{{ optional($pUser)->email ?? '' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span>{{ optional($p)->telephone ?: (optional($pUser)->contact ?: '-') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($devType === 'IOS')
                                            <span class="badge bg-info"><i class="fa-brands fa-apple me-1"></i> iOS</span>
                                        @else
                                            <span class="badge bg-success"><i class="fa-brands fa-android me-1"></i> Android</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($hasPushToken)
                                            <span class="badge bg-success">
                                                <i class="fa fa-paper-plane me-1"></i> Push Mobile FCM ✅
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                <i class="fa fa-inbox me-1"></i> In-App 📱
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->read_at)
                                            <span class="badge bg-success" title="Lu le {{ $item->read_at->format('d/m/Y H:i') }}">
                                                <i class="fa fa-check-double me-1"></i> Lu
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fa fa-clock me-1"></i> Non lu
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Aucun destinataire trouvé pour cette diffusion.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination stylisée des destinataires -->
                <div class="mt-25 pt-15 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted fs-13">
                        @if($recipients->total() > 0)
                            Affichage de <strong class="text-dark">{{ $recipients->firstItem() ?? 1 }}</strong> à <strong class="text-dark">{{ $recipients->lastItem() ?? $recipients->total() }}</strong> sur <strong class="text-dark">{{ $recipients->total() }}</strong> destinataire(s)
                        @else
                            Aucun destinataire à afficher
                        @endif
                    </div>
                    <div class="gemma-pagination">
                        {{ $recipients->withQueryString()->links('pagination::bootstrap-5') }}
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
