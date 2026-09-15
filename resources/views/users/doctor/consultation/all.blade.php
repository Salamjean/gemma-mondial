@extends('layouts.dashboard', ['title' => 'Toutes les consultations en attente'])

@section('content')
    <!-- 1. Tableau File d'Attente Globale Médecin - Tous les Patients (EN HAUT) -->
    <div class="box border-0 shadow-sm rounded-20 bg-white mb-4">
        <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h3 class="box-title fw-bold text-dark mb-1 fs-20">
                    <i class="fa-solid fa-users-viewfinder text-primary me-2"></i> File d'Attente Globale Médecin - Tous les Patients
                </h3>
                <p class="text-muted mb-0 fs-13">
                    Liste intégrale de tous les patients en attente de consultation médicale, sans restriction sur la date.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary rounded-10 px-15 py-8 fw-semibold me-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary rounded-10 px-15 py-8 fw-semibold">
                    <i class="fa-solid fa-calendar-day me-1"></i> Consultations du Jour
                </a>
            </div>
        </div>

        <div class="box-body p-20">
            <div class="table-responsive">
                <table id="example1" class="table table-striped table-hover display nowrap margin-top-10 w-p100 align-middle border">
                    <thead style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                        <tr class="text-dark fw-bold fs-13">
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 140px;">Date & Heure</th>
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 110px;">Code Patient</th>
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 180px;">Nom & Prénom(s)</th>
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 160px;">Prestation / Service</th>
                            <th class="py-3 px-3 text-dark fw-bold text-center" style="min-width: 150px;">Dossier médical</th>
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 160px;">Statut Soins Infirmier</th>
                            <th class="py-3 px-3 text-dark fw-bold text-center" style="min-width: 130px;">Statut Médecin</th>
                            <th class="py-3 px-3 text-dark fw-bold text-center" style="min-width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($consultations as $item)
                            @php
                                $isToday = \Carbon\Carbon::parse($item->created_at)->isToday();
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge {{ $isToday ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} fw-bold fs-12 px-2 py-1 rounded-pill">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-13">{{ optional($item->patient)->code_patient ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-14">
                                        {{ optional(optional($item->patient)->user)->name ?? '' }} {{ optional(optional($item->patient)->user)->prenom ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light-primary text-primary fw-semibold fs-12">
                                        {{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? 'Consultation' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if (optional($item->patient)->id)
                                        <a href="{{ route('doctor.patient.dossier_medical', $item->patient->id) }}"
                                            class="btn btn-sm btn-outline-info rounded-pill fw-bold shadow-xs px-3 py-1.5 text-nowrap d-inline-flex align-items-center gap-1.5" title="Ouvrir le dossier médical complet">
                                            <i class="fa-solid fa-folder-open fs-13"></i>
                                            <span>Dossier médical</span>
                                        </a>
                                    @else
                                        <span class="text-muted fs-11">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status_inf == 1)
                                        <span class="badge bg-light-success text-success fw-bold px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-check me-1"></i> Constantes prises
                                        </span>
                                    @else
                                        <span class="badge bg-light-warning text-warning fw-bold px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-clock me-1"></i> En attente soins
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->status == 0)
                                        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">En attente</span>
                                    @else
                                        <span class="badge bg-success fw-bold px-3 py-2 rounded-pill">Terminée</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->status == 0)
                                        @php
                                            $srvLibelle = optional(optional(optional($item->prestationHospital)->prestationService)->service)->libelle ?? '';
                                            $prestLibelle = optional(optional($item->prestationHospital)->prestationService)->libelle ?? '';
                                            $isConsultationService = (stripos($srvLibelle, 'consultation') !== false) || (stripos($prestLibelle, 'consultation') !== false);
                                        @endphp
                                        <a href="{{ route('doctor.consultation.formulaire', $item->id) }}" class="btn btn-sm btn-primary rounded-8 fw-semibold shadow-sm px-15 me-1">
                                            <i class="fa-solid fa-stethoscope me-1"></i> Commencer
                                        </a>
                                    @else
                                        <a href="{{ route('doctor.consultation.detail', $item->id) }}" class="btn btn-sm btn-info text-white rounded-8 fw-semibold px-15 me-1">
                                            <i class="fa-solid fa-eye me-1"></i> Détail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted fs-14">
                                    <i class="fa-solid fa-check-circle fs-24 text-success d-block mb-2"></i>
                                    Aucune consultation en attente dans la file d'attente globale.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Section Demandes de Téléconsultation en ligne en attente (EN BAS) -->
    <div class="mt-4">
        @include('partials.doctor_pending_teleconsultations')
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
