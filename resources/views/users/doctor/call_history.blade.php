@extends('layouts.dashboard', ['title' => 'Historique des appels vidéo'])

@section('content')
<div class="container-full">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h3 class="page-title fw-bold text-dark">
                    <i class="fa-solid fa-video text-primary me-2"></i> Historique des Téléconsultations
                </h3>
                <p class="text-muted mb-0">Consultez l'historique de tous vos appels vidéo avec la durée de chaque consultation.</p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Tableau de bord
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content mt-4">
        <div class="box shadow-sm border-0 rounded-4">
            <div class="box-header with-border bg-white px-4 py-3 d-flex align-items-center justify-content-between">
                <h4 class="box-title fw-bold mb-0 text-primary">
                    <i class="fa-solid fa-list-ul me-2"></i> Appels Réalisés ({{ $consultations->total() }})
                </h4>
            </div>

            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Patient</th>
                                <th>Prestation / Service</th>
                                <th>Date & Heure</th>
                                <th>Durée d'appel</th>
                                <th>Statut</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($consultations as $consultation)
                                @php
                                    $patientUser = optional(optional($consultation->patient)->user);
                                    $patientName = trim(($patientUser->name ?? '') . ' ' . ($patientUser->prenom ?? ''));
                                    if(empty($patientName)) $patientName = 'Patient Non Spécifié';

                                    $durSec = (int) ($consultation->call_duration ?? 0);
                                    $durMin = floor($durSec / 60);
                                    $durRemSec = $durSec % 60;
                                    $durStr = sprintf('%02d min %02d sec', $durMin, $durRemSec);
                                    if ($durSec == 0) $durStr = '00 min 00 sec';

                                    $status = $consultation->call_status ?? 'ended';
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md me-3 bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $patientName }}</h6>
                                                <small class="text-muted">ID Patient: #{{ $consultation->patient_id ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-secondary">
                                            {{ optional(optional($consultation->prestationHospital)->prestationService)->libelle ?? 'Consultation Vidéo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">
                                                <i class="fa-regular fa-calendar-days text-muted me-1"></i>
                                                {{ $consultation->call_started_at ? date('d/m/Y', strtotime($consultation->call_started_at)) : date('d/m/Y', strtotime($consultation->created_at)) }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="fa-regular fa-clock me-1"></i>
                                                {{ $consultation->call_started_at ? date('H:i', strtotime($consultation->call_started_at)) : date('H:i', strtotime($consultation->created_at)) }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace fw-bold">
                                            <i class="fa-solid fa-stopwatch text-success me-1"></i> {{ $durStr }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($status === 'rejected')
                                            <span class="badge bg-danger-light text-danger px-3 py-2 rounded-pill fw-bold">
                                                <i class="fa-solid fa-circle-xmark me-1"></i> Refusé par le patient
                                            </span>
                                        @elseif($status === 'patient_left')
                                            <span class="badge bg-warning-light text-warning px-3 py-2 rounded-pill fw-bold">
                                                <i class="fa-solid fa-right-from-bracket me-1"></i> Patient s'est déconnecté
                                            </span>
                                        @elseif($status === 'accepted' || $status === 'calling')
                                            <span class="badge bg-info-light text-info px-3 py-2 rounded-pill fw-bold">
                                                <i class="fa-solid fa-signal me-1"></i> En cours / Décroché
                                            </span>
                                        @else
                                            <span class="badge bg-success-light text-success px-3 py-2 rounded-pill fw-bold">
                                                <i class="fa-solid fa-circle-check me-1"></i> Terminé avec succès
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('doctor.consultation.info', $consultation->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" title="Voir détails">
                                            <i class="fa-solid fa-eye me-1"></i> Détails
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <i class="fa-solid fa-video-slash fs-1 mb-3 text-secondary"></i>
                                            <h5>Aucun appel vidéo enregistré</h5>
                                            <p class="fs-13 text-muted mb-0">L'historique de vos téléconsultations s'affichera ici.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($consultations->hasPages())
                <div class="box-footer bg-white border-top px-4 py-3 d-flex justify-content-end">
                    {{ $consultations->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
