@extends('layouts.dashboard', ['title' => 'File d\'Attente Globale - Tous les Patients'])

@section('content')
    @php
        $totalCount = $consultations->count();
        $todayCount = $consultations->filter(function($c) {
            return \Carbon\Carbon::parse($c->created_at)->isToday();
        })->count();
        $previousCount = $totalCount - $todayCount;
    @endphp

    <div class="row mb-4">
        <!-- Stat Cards -->
        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
            <div class="box border-0 shadow-sm rounded-15 bg-white p-20 h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold fs-13 d-block mb-1">Total Patients en Attente</span>
                        <h2 class="fw-extrabold text-primary mb-0 fs-28">{{ $totalCount }}</h2>
                    </div>
                    <div class="bg-light-primary text-primary rounded-circle p-15 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        <i class="fa-solid fa-users-viewfinder fs-24"></i>
                    </div>
                </div>
                <div class="progress mt-15 mb-0" style="height: 4px;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
            <div class="box border-0 shadow-sm rounded-15 bg-white p-20 h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold fs-13 d-block mb-1">Nouveaux (Aujourd'hui)</span>
                        <h2 class="fw-extrabold text-success mb-0 fs-28">{{ $todayCount }}</h2>
                    </div>
                    <div class="bg-light-success text-success rounded-circle p-15 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        <i class="fa-solid fa-calendar-day fs-24"></i>
                    </div>
                </div>
                <div class="progress mt-15 mb-0" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalCount > 0 ? round(($todayCount/$totalCount)*100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <div class="box border-0 shadow-sm rounded-15 bg-white p-20 h-100 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold fs-13 d-block mb-1">En attente (Jours Précédents)</span>
                        <h2 class="fw-extrabold text-warning mb-0 fs-28">{{ $previousCount }}</h2>
                    </div>
                    <div class="bg-light-warning text-warning rounded-circle p-15 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                        <i class="fa-solid fa-clock-rotate-left fs-24"></i>
                    </div>
                </div>
                <div class="progress mt-15 mb-0" style="height: 4px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalCount > 0 ? round(($previousCount/$totalCount)*100) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="box border-0 shadow-sm rounded-15 bg-white">
        <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h3 class="box-title fw-bold text-dark mb-1 fs-20">
                    <i class="fa-solid fa-notes-medical text-primary me-2"></i> File d'Attente Globale - Prise des Constantes
                </h3>
                <p class="text-muted mb-0 fs-13">
                    Liste intégrale de tous les patients en attente de prise de constantes ou de soins, sans restriction sur la date.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('infirmier.consultation.today') }}" class="btn btn-outline-primary rounded-10 px-15 py-8 fw-semibold fs-13">
                    <i class="fa-solid fa-calendar-check me-1"></i> File du jour
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-10 px-15 py-8 fw-semibold fs-13 shadow-sm">
                    <i class="fa-solid fa-house me-1"></i> Tableau de bord
                </a>
            </div>
        </div>

        <div class="box-body p-20">
            <div class="table-responsive">
                <table id="example1" class="table table-hover align-middle mb-0 display nowrap w-p100" style="border-collapse: separate; border-spacing: 0 8px;">
                    <thead class="bg-light text-uppercase fs-12 text-muted fw-bold">
                        <tr>
                            <th class="py-12 px-15 border-0 rounded-start">Date & Heure</th>
                            <th class="py-12 px-15 border-0">Code Patient</th>
                            <th class="py-12 px-15 border-0">Nom & Prénom(s)</th>
                            <th class="py-12 px-15 border-0">Service / Prestation</th>
                            <th class="py-12 px-15 border-0">Motif de Visite</th>
                            <th class="py-12 px-15 border-0 text-center">Statut</th>
                            <th class="py-12 px-15 border-0 text-center rounded-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($consultations as $item)
                            @php
                                $createdAt = \Carbon\Carbon::parse($item->created_at);
                                $isToday = $createdAt->isToday();
                                $patientName = trim(($item->patient->user->name ?? '') . ' ' . ($item->patient->user->prenom ?? ''));
                                $initials = strtoupper(substr($item->patient->user->name ?? 'P', 0, 1) . substr($item->patient->user->prenom ?? '', 0, 1));
                            @endphp
                            <tr class="bg-white shadow-xs rounded-10 mb-2">
                                <td class="px-15 py-12">
                                    <span class="badge {{ $isToday ? 'bg-light-success text-success border border-success-subtle' : 'bg-light-warning text-dark border border-warning-subtle' }} fw-bold fs-12 px-10 py-6 rounded-pill">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ $createdAt->format('d/m/Y') }} à {{ $createdAt->format('H:i') }}
                                    </span>
                                </td>
                                <td class="px-15 py-12">
                                    <span class="badge bg-light text-dark fw-bold border fs-12 px-10 py-6 font-monospace">
                                        <i class="fa-solid fa-id-card text-muted me-1"></i>
                                        {{ $item->patient->code_patient ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-15 py-12">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-light text-primary rounded-circle fw-bold me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; min-width: 38px;">
                                            {{ $initials ?: 'PT' }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark fs-14">{{ $patientName ?: 'Patient Inconnu' }}</h6>
                                            @if(isset($item->patient->telephone) && $item->patient->telephone)
                                                <small class="text-muted fs-12"><i class="fa-solid fa-phone me-1"></i>{{ $item->patient->telephone }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-15 py-12">
                                    <span class="badge bg-light-info text-info fw-semibold fs-12 px-10 py-6 rounded-pill">
                                        <i class="fa-solid fa-hospital-user me-1"></i>
                                        {{ $item->prestationHospital->prestationService->libelle ?? ($item->prestationHospital->serviceHospital->service->libelle ?? 'Consultation') }}
                                    </span>
                                </td>
                                <td class="px-15 py-12">
                                    <span class="text-secondary fs-13 text-truncate d-inline-block" style="max-width: 220px;" title="{{ $item->admission->motif_consultation ?? 'Non spécifié' }}">
                                        <i>{{ $item->admission->motif_consultation ?? 'Non spécifié' }}</i>
                                    </span>
                                </td>
                                <td class="px-15 py-12 text-center">
                                    @if ($item->status_inf == 0)
                                        <span class="badge bg-warning text-dark fw-bold px-12 py-6 rounded-pill shadow-xs">
                                            <i class="fa-solid fa-spinner fa-spin me-1"></i> En attente de constantes
                                        </span>
                                    @else
                                        <span class="badge bg-success fw-bold px-12 py-6 rounded-pill">
                                            <i class="fa-solid fa-circle-check me-1"></i> Prise terminée
                                        </span>
                                    @endif
                                </td>
                                <td class="px-15 py-12 text-center">
                                    @if ($item->status_inf == 0)
                                        <a href="{{ route('infirmier.consultation.formulaire', $item->id) }}" class="btn btn-sm btn-primary rounded-10 fw-bold shadow-sm px-15 py-6">
                                            <i class="fa-solid fa-heart-pulse me-1"></i> Prendre constantes
                                        </a>
                                    @else
                                        <a href="{{ route('infirmier.consultation.detail', $item->id) }}" class="btn btn-sm btn-outline-info rounded-10 fw-bold px-15 py-6">
                                            <i class="fa-solid fa-eye me-1"></i> Voir Fiche
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="p-30">
                                        <div class="bg-light-success text-success rounded-circle d-inline-flex p-20 mb-3" style="width: 80px; height: 80px; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-circle-check fs-40"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">Aucun patient en attente</h5>
                                        <p class="text-muted mb-0 fs-13">La file d'attente globale des constantes est actuellement vide.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
