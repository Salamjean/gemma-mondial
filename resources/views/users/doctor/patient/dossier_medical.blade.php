@extends('layouts.dashboard', ['title' => 'Dossier Médical ' . $patient->code_patient])

@push('css')
<style>
    .patient-header-box {
        background: #ffffff;
        border-radius: 14px;
        padding: 25px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .patient-avatar-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #3b82f6;
    }
</style>
@endpush

@section('content')
@php
    $ageStr = 'N/A';
    if (!empty($patient->birth_date)) {
        try {
            $dateNaissance = \Carbon\Carbon::createFromFormat('d/m/Y', $patient->birth_date);
            $ageStr = $dateNaissance->diffInYears(\Carbon\Carbon::now()) . ' ans';
        } catch (\Exception $e) {
            $ageStr = $patient->birth_date;
        }
    }

    $detailUrl = '#';
    if (request()->routeIs('secretariat.*') && \Illuminate\Support\Facades\Route::has('secretariat.patient.detail')) {
        $detailUrl = route('secretariat.patient.detail', $patient->id);
    } elseif (request()->routeIs('hospital.*') && \Illuminate\Support\Facades\Route::has('hospital.patient.detail')) {
        $detailUrl = route('hospital.patient.detail', $patient->id);
    } elseif (\Illuminate\Support\Facades\Route::has('doctor.patient.detail')) {
        $detailUrl = route('doctor.patient.detail', $patient->id);
    }
@endphp

<div class="container-fluid">

    <!-- En-tête Profil Simple & Épuré -->
    <div class="patient-header-box mb-25">
        <div class="row align-items-center">
            <div class="col-lg-8 d-flex align-items-center flex-wrap gap-3">
                <div>
                    @if ($patient->img_url != null)
                        <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}" class="patient-avatar-img" alt="Photo de profil" />
                    @else
                        @if (strtolower($patient->gender) == 'masculin' || strtolower($patient->gender) == 'm')
                            <img src="{{ asset('assets/images/avatar/6.png') }}" class="patient-avatar-img" alt="Photo de profil" />
                        @else
                            <img src="{{ asset('assets/images/avatar/2.png') }}" class="patient-avatar-img" alt="Photo de profil" />
                        @endif
                    @endif
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <h2 class="fw-bold text-dark mb-0 fs-24">{{ $patient->user->name ?? '' }} {{ $patient->user->prenom ?? '' }}</h2>
                        <span class="badge bg-primary-light text-primary fw-bold px-3 py-1 fs-13">Dossier N° {{ $patient->code_patient }}</span>
                    </div>
                    <p class="text-muted mb-2 fs-14">
                        <span class="fw-semibold text-dark"><i class="fa-solid fa-mars-venus text-primary me-1"></i> {{ ucfirst($patient->gender) }}</span>
                        &nbsp;•&nbsp;
                        <span class="fw-semibold text-dark"><i class="fa-solid fa-cake-candles text-info me-1"></i> {{ $ageStr }}</span>
                        &nbsp;•&nbsp;
                        <span><i class="fa-solid fa-briefcase text-muted me-1"></i> {{ $patient->profession ?? 'Profession non renseignée' }}</span>
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark border px-3 py-1 fs-12"><i class="fa-solid fa-phone text-success me-1"></i> {{ $patient->telephone ?? 'Non renseigné' }}</span>
                        <span class="badge bg-light text-dark border px-3 py-1 fs-12"><i class="fa-solid fa-location-dot text-danger me-1"></i> {{ $patient->residenceActuelle->name ?? 'Résidence inconnue' }}</span>
                    </div>
                </div>
            </div>

            <!-- Boutons d'Action -->
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap align-items-center justify-content-lg-end gap-2">
                    <a href="{{ $detailUrl }}" class="btn btn-info text-white fw-semibold rounded-10 px-20">
                        <i class="fa-solid fa-user me-1"></i> Fiche Patient
                    </a>
                    <a href="{{ back()->getTargetUrl() }}" class="btn btn-secondary fw-semibold rounded-10 px-20">
                        <i class="fa-solid fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLEAU DES INTERVENTIONS ET PARCOURS DE SOINS DU PATIENT -->
    <div class="mb-25">
        @include('partials.patient_interventions_table', ['patient' => $patient])
    </div>
</div>
@endsection