@extends('layouts.dashboard', ['title' => "Détail du Patient - " . ($patient->user->name ?? '') . " " . ($patient->user->prenom ?? '')])

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
    .detail-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.02);
        margin-bottom: 20px;
        overflow: hidden;
    }
    .detail-card-header {
        background: #f8fafc;
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 1rem;
        color: #1e293b;
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        padding: 20px;
    }
    .detail-field {
        background: #f8fafc;
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid #f1f5f9;
    }
    .detail-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .detail-value {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
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
@endphp

@php
    $dossierUrl = '#';
    if (request()->routeIs('secretariat.*') && \Illuminate\Support\Facades\Route::has('secretariat.patient.dossier_medical')) {
        $dossierUrl = route('secretariat.patient.dossier_medical', $patient->id);
    } elseif (request()->routeIs('hospital.*') && \Illuminate\Support\Facades\Route::has('hospital.patient.dossier_medical')) {
        $dossierUrl = route('hospital.patient.dossier_medical', $patient->id);
    } elseif (\Illuminate\Support\Facades\Route::has('doctor.patient.dossier_medical')) {
        $dossierUrl = route('doctor.patient.dossier_medical', $patient->id);
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
                    <a href="{{ $dossierUrl }}" class="btn btn-primary fw-semibold rounded-10 px-20">
                        <i class="fa-solid fa-folder-open me-1"></i> Voir dossier
                    </a>
                    @if (\Illuminate\Support\Facades\Route::has('secretariat.patient.edit'))
                    <a href="{{ route('secretariat.patient.edit', $patient->id) }}" class="btn btn-warning fw-semibold rounded-10 px-20">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Modifier
                    </a>
                    @endif
                    <a href="{{ back()->getTargetUrl() }}" class="btn btn-secondary fw-semibold rounded-10 px-20">
                        <i class="fa-solid fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Disposition à 2 Colonnes Propre & Bien Ordonnée -->
    <div class="row">
        <!-- Colonne Gauche: Identité & Domicile -->
        <div class="col-lg-7">

            <!-- Card 1: Identité & Naissance -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-user text-primary me-2"></i> Identité & État Civil
                </div>
                <div class="detail-grid">
                    <div class="detail-field">
                        <div class="detail-label">Nom de famille</div>
                        <div class="detail-value">{{ $patient->user->name ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Prénom(s)</div>
                        <div class="detail-value">{{ $patient->user->prenom ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Genre</div>
                        <div class="detail-value">{{ ucfirst($patient->gender ?? 'N/A') }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Date de Naissance</div>
                        <div class="detail-value">{{ $patient->birth_date ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Âge Calculé</div>
                        <div class="detail-value">{{ $ageStr }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Lieu de Naissance</div>
                        <div class="detail-value">{{ $patient->lieuNaissance->name ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Pays de Naissance</div>
                        <div class="detail-value">{{ $patient->country ?? 'Côte d\'Ivoire' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Profession</div>
                        <div class="detail-value">{{ $patient->profession ?? 'Non renseignée' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Type de Pièce</div>
                        <div class="detail-value">{{ $patient->type_piece ?? 'N/A' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">N° d'Identité</div>
                        <div class="detail-value">{{ $patient->numero_identite ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Domicile & Contacts -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-house-user text-success me-2"></i> Domicile & Contacts Directs
                </div>
                <div class="detail-grid">
                    <div class="detail-field">
                        <div class="detail-label">Résidence Actuelle</div>
                        <div class="detail-value">{{ $patient->residenceActuelle->name ?? 'Non renseignée' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Résidence Habituelle</div>
                        <div class="detail-value">{{ $patient->residenceHabituelle->name ?? 'Non renseignée' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Téléphone Principal</div>
                        <div class="detail-value text-success"><i class="fa-solid fa-phone me-1"></i> {{ $patient->telephone ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Téléphone Secondaire</div>
                        <div class="detail-value">{{ $patient->contact2 ?? 'Aucun' }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Enfants (Si Féminin) -->
            @if (strtolower($patient->gender) == 'feminin' || strtolower($patient->gender) == 'f')
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-baby me-2 text-info"></i> Enfants Enregistrés
                </div>
                <div class="p-20">
                    @if(isset($patient->enfantsPatient) && $patient->enfantsPatient->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($patient->enfantsPatient as $item)
                                @if(isset($item->naissance->enfant))
                                    <a href="{{ \Illuminate\Support\Facades\Route::has('secretariat.patient.detail') ? route('secretariat.patient.detail', $item->naissance->enfant->id) : route('hospital.patient.detail', $item->naissance->enfant->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                        <i class="fa-solid fa-child me-1"></i> Dossier N° {{ $item->naissance->enfant->code_patient }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted fs-14"><i class="fa-solid fa-circle-info me-1"></i> Aucun enfant associé enregistre dans le système.</span>
                    @endif
                </div>
            </div>
            @endif

        </div>

        <!-- Colonne Droite: Urgences & Assurance -->
        <div class="col-lg-5">

            <!-- Card 4: Personne à contacter d'urgence N°1 -->
            <div class="detail-card">
                <div class="detail-card-header text-danger" style="background: #fff5f5;">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Contact Urgence N°1
                </div>
                <div class="p-20">
                    <div class="detail-field mb-12">
                        <div class="detail-label">Nom & Prénom(s)</div>
                        <div class="detail-value">{{ $patient->nom_personne_cas_urgence ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="detail-field mb-12">
                        <div class="detail-label">Numéro de Téléphone</div>
                        <div class="detail-value text-danger"><i class="fa-solid fa-phone me-1"></i> {{ $patient->telephone_personne_cas_urgence ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Lien de Parenté</div>
                        <div class="detail-value">{{ $patient->lien_personne_cas_urgence ?? 'Non spécifié' }}</div>
                    </div>
                </div>
            </div>

            <!-- Card 5: Personne à contacter d'urgence N°2 (Si présent) -->
            @if($patient->nom_personne2_cas_urgence)
            <div class="detail-card">
                <div class="detail-card-header text-warning" style="background: #fffbeb;">
                    <i class="fa-solid fa-user-shield me-2"></i> Contact Urgence N°2
                </div>
                <div class="p-20">
                    <div class="detail-field mb-12">
                        <div class="detail-label">Nom & Prénom(s)</div>
                        <div class="detail-value">{{ $patient->nom_personne2_cas_urgence }}</div>
                    </div>
                    <div class="detail-field mb-12">
                        <div class="detail-label">Numéro de Téléphone</div>
                        <div class="detail-value text-dark"><i class="fa-solid fa-phone me-1"></i> {{ $patient->telephone_personne2_cas_urgence ?? 'Non renseigné' }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Lien de Parenté</div>
                        <div class="detail-value">{{ $patient->lien_personne2_cas_urgence ?? 'Non spécifié' }}</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Card 6: Couverture Santé & Assurance -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-shield-halved text-primary me-2"></i> Couverture Santé / Assurance
                </div>
                <div class="p-20">
                    <div class="detail-field mb-12">
                        <div class="detail-label">Statut Assurance</div>
                        <div class="detail-value">
                            @if($patient->assurer)
                                <span class="badge bg-success-light text-success fw-bold px-3 py-1 fs-13"><i class="fa-solid fa-check me-1"></i> Assuré (Oui)</span>
                            @else
                                <span class="badge bg-secondary-light text-secondary fw-bold px-3 py-1 fs-13"><i class="fa-solid fa-xmark me-1"></i> Non Assuré</span>
                            @endif
                        </div>
                    </div>
                    @if($patient->assurer)
                    <div class="detail-field">
                        <div class="detail-label">Numéro d'Assurance / Police</div>
                        <div class="detail-value">{{ $patient->no_assurance ?? 'Non renseigné' }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card 7: Métadonnées du dossier -->
            <div class="detail-card">
                <div class="detail-card-header">
                    <i class="fa-solid fa-circle-info text-info me-2"></i> Infos Dossier
                </div>
                <div class="p-20">
                    <div class="detail-field mb-12">
                        <div class="detail-label">Code Dossier Médical</div>
                        <div class="detail-value text-primary">{{ $patient->code_patient }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-label">Date de Création du Compte</div>
                        <div class="detail-value">{{ optional($patient->created_at)->format('d/m/Y - H:i:s') }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
