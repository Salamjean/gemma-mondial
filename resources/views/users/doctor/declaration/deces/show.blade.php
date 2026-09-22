@extends('layouts.dashboard', ['title' => 'Certificat de Décès - ' . (optional($declaration->deces)->reference ?? $declaration->reference)])

@section('content')
<style>
    .deces-view-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .deces-hero-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 28px 32px;
        color: #0f172a;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid #e2e8f0;
        margin-bottom: 25px;
    }

    .deces-hero-card::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(239, 68, 68, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .status-badge-deces {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fca5a5;
        padding: 5px 13px;
        border-radius: 30px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-action-hero {
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 10px 18px;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-action-hero:hover {
        transform: translateY(-2px);
    }

    .info-card-premium {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.25s ease;
        height: 100%;
    }

    .info-card-premium:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
    }

    .card-header-custom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-title-custom {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .icon-badge-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .icon-badge-red { background: #fee2e2; color: #dc2626; }
    .icon-badge-blue { background: #e0f2fe; color: #0284c7; }
    .icon-badge-amber { background: #fef3c7; color: #d97706; }
    .icon-badge-purple { background: #f3e8ff; color: #7e22ce; }

    .data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .data-item {
        background: #f8fafc;
        border-radius: 12px;
        padding: 14px 16px;
        border: 1px solid #edf2f7;
    }

    .data-label {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .data-value {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        word-break: break-word;
    }

    .clinical-cause-box {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        border-radius: 0 12px 12px 0;
        padding: 16px;
        margin-bottom: 14px;
    }

    .clinical-cause-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #991b1b;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .clinical-cause-text {
        font-size: 1.02rem;
        color: #450a0a;
        font-weight: 600;
        margin: 0;
        line-height: 1.5;
    }

    .observation-box-custom {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 16px;
        color: #334155;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .patient-avatar-box {
        width: 95px;
        height: 95px;
        border-radius: 20px;
        object-fit: cover;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        border: 3px solid #f1f5f9;
    }

    @media print {
        .btn-screen-only {
            display: none !important;
        }
        .deces-hero-card {
            background: #ffffff !important;
            color: #000000 !important;
            box-shadow: none !important;
            border: 1px solid #cccccc !important;
        }
    }
</style>

<div class="deces-view-container container-fluid p-3 p-md-4">

    <!-- En-tête / Bannière Principale Light -->
    <div class="deces-hero-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                @if (optional($declaration->patient)->img_url != null)
                    <img src="{{ asset('assets/uploads/patient/' . $declaration->patient->img_url) }}" 
                         class="patient-avatar-box" alt="Photo défunt">
                @elseif (optional($declaration->patient)->gender == 'feminin' || optional($declaration->deces)->genre == 'feminin')
                    <img src="{{ asset('assets/images/avatar/2.png') }}" 
                         class="patient-avatar-box bg-light p-1" alt="Photo défunt">
                @else
                    <img src="{{ asset('assets/images/avatar/6.png') }}" 
                         class="patient-avatar-box bg-light p-1" alt="Photo défunt">
                @endif
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="status-badge-deces">
                            <i class="fa-solid fa-file-medical"></i> Acte de Décès
                        </span>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-1 font-monospace">
                            Réf : {{ $declaration->reference }}
                        </span>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">
                        {{ optional(optional($declaration->patient)->user)->name ?? 'Défunt' }} {{ optional(optional($declaration->patient)->user)->prenom ?? '' }}
                    </h2>
                    <p class="text-muted mb-0 small mt-1">
                        <i class="fa-solid fa-hospital text-danger me-1"></i> Établissement : <strong class="text-dark">{{ optional($declaration->hospital)->label ?? optional(optional($declaration->doctor)->hospital)->label ?? 'Centre Hospitalier' }}</strong>
                        &nbsp;|&nbsp;
                        <i class="fa-solid fa-user-doctor text-primary me-1"></i> Médecin : <strong class="text-dark">{{ optional(optional($declaration->doctor)->user)->name ?? optional($declaration->doctor)->name ?? auth()->user()->name ?? 'Médecin' }}</strong>
                    </p>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="d-flex align-items-center gap-2 flex-wrap btn-screen-only">
                <a href="{{ route('doctor.declaration.deces.list') }}" class="btn btn-action-hero btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Registre
                </a>
                <button onclick="window.print()" class="btn btn-action-hero btn-danger shadow-sm">
                    <i class="fa-solid fa-print"></i> Imprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Grille de Détails -->
    <div class="row g-4">

        <!-- 1. IDENTITÉ DU DÉFUNT -->
        <div class="col-lg-6 col-12">
            <div class="info-card-premium">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <span class="icon-badge-box icon-badge-blue">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        Identité & État Civil du Défunt
                    </h5>
                    <span class="badge bg-light text-secondary border">Fiche Patient</span>
                </div>

                <div class="data-grid">
                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-signature"></i> Nom de Famille</div>
                        <div class="data-value">{{ optional(optional($declaration->patient)->user)->name ?? 'Non spécifié' }}</div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-user-tag"></i> Prénoms</div>
                        <div class="data-value">{{ optional(optional($declaration->patient)->user)->prenom ?? '-' }}</div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-id-card"></i> Code Dossier Médical (DM)</div>
                        <div class="data-value text-primary font-monospace">{{ optional($declaration->patient)->code_patient ?? 'N/A' }}</div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-venus-mars"></i> Genre</div>
                        <div class="data-value">
                            @php
                                $gender = optional($declaration->patient)->gender ?? optional($declaration->deces)->genre ?? 'masculin';
                            @endphp
                            @if(strtolower($gender) === 'feminin')
                                <span class="badge bg-pink text-white rounded-pill px-2 py-1" style="background-color: #ec4899;">
                                    <i class="fa-solid fa-venus me-1"></i> Féminin
                                </span>
                            @else
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1">
                                    <i class="fa-solid fa-mars me-1"></i> Masculin
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-hourglass-half"></i> Âge au Décès</div>
                        <div class="data-value">
                            {{ optional($declaration->deces)->age !== null ? optional($declaration->deces)->age . ' ans' : 'N/A' }}
                        </div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-cake-candles"></i> Date de Naissance</div>
                        <div class="data-value">{{ optional($declaration->patient)->birth_date ?? 'Non renseignée' }}</div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-phone"></i> Contact / Téléphone</div>
                        <div class="data-value">{{ optional($declaration->patient)->telephone ?? 'Non renseigné' }}</div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-briefcase"></i> Profession</div>
                        <div class="data-value">{{ optional($declaration->patient)->profession ?? 'Sans profession' }}</div>
                    </div>

                    <div class="data-item" style="grid-column: 1 / -1;">
                        <div class="data-label"><i class="fa-solid fa-location-dot"></i> Résidence Habituelle</div>
                        <div class="data-value text-secondary">
                            {{ optional(optional($declaration->patient)->habitualResidence)->name ?? optional($declaration->patient)->address ?? 'Non renseignée' }}
                            <span class="badge bg-light text-muted border ms-2">Milieu : {{ optional($declaration->deces)->milieu_residence ?? 'Urbain' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. CIRCONSTANCES & CONSTATIONS DU DÉCÈS -->
        <div class="col-lg-6 col-12">
            <div class="info-card-premium">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <span class="icon-badge-box icon-badge-red">
                            <i class="fa-solid fa-book-skull"></i>
                        </span>
                        Circonstances & Constat du Décès
                    </h5>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                        Constat Officiel
                    </span>
                </div>

                <div class="data-grid mb-3">
                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-hashtag"></i> Numéro Déclaration</div>
                        <div class="data-value text-danger font-monospace">
                            {{ optional($declaration->deces)->reference ?? 'DD-' . $declaration->id }}
                        </div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-calendar-day"></i> Date du Décès</div>
                        <div class="data-value text-dark">
                            {{ !empty(optional($declaration->deces)->date) ? \Carbon\Carbon::parse($declaration->deces->date)->format('d/m/Y') : \Carbon\Carbon::parse($declaration->created_at)->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-clock"></i> Heure du Décès</div>
                        <div class="data-value text-dark">
                            {{ !empty(optional($declaration->deces)->heure) ? \Carbon\Carbon::parse($declaration->deces->heure)->format('H:i') : 'Non précisée' }}
                        </div>
                    </div>

                    <div class="data-item">
                        <div class="data-label"><i class="fa-solid fa-person-breastfeeding"></i> Décès Maternel</div>
                        <div class="data-value">
                            @php
                                $maternel = strtolower(optional($declaration->deces)->deces_maternel ?? 'non');
                            @endphp
                            @if($maternel === 'oui' || $maternel === '1')
                                <span class="badge bg-danger text-white rounded-pill px-3 py-1">Oui</span>
                            @else
                                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">Non</span>
                            @endif
                        </div>
                    </div>

                    <div class="data-item" style="grid-column: 1 / -1;">
                        <div class="data-label"><i class="fa-solid fa-map-location-dot"></i> Lieu du Décès</div>
                        <div class="data-value text-dark fw-bold">
                            {{ optional($declaration->deces)->lieu ?? 'Établissement Hospitalier' }}
                        </div>
                    </div>
                </div>

                <!-- Section Diagnostic Médical -->
                <div class="mt-3">
                    <div class="clinical-cause-box">
                        <div class="clinical-cause-label">
                            <i class="fa-solid fa-virus"></i> Cause Initiale / Antécédent
                        </div>
                        <p class="clinical-cause-text">
                            {{ optional($declaration->deces)->cause_initiale ?? 'Non spécifiée' }}
                        </p>
                    </div>

                    <div class="clinical-cause-box" style="background: #fff1f2; border-color: #be123c;">
                        <div class="clinical-cause-label" style="color: #9f1239;">
                            <i class="fa-solid fa-heart-crack"></i> Cause Directe du Décès
                        </div>
                        <p class="clinical-cause-text" style="color: #881337;">
                            {{ optional($declaration->deces)->cause_directe ?? 'Non spécifiée' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. OBSERVATIONS & SIGNATURE MÉDICALE -->
        <div class="col-12">
            <div class="info-card-premium">
                <div class="card-header-custom">
                    <h5 class="card-title-custom">
                        <span class="icon-badge-box icon-badge-amber">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </span>
                        Observations Cliniques & Validation Médicale
                    </h5>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                        <i class="fa-solid fa-shield-check me-1"></i> Enregistrement Validé
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-lg-8 col-12">
                        <div class="data-label mb-2"><i class="fa-solid fa-comment-medical"></i> Observations du Médecin Praticien :</div>
                        <div class="observation-box-custom">
                            @if(!empty(optional($declaration->deces)->observations))
                                {{ $declaration->deces->observations }}
                            @else
                                <span class="text-muted fst-italic">Aucune observation complémentaire renseignée lors de la déclaration.</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        <div class="data-item h-100 d-flex flex-column justify-content-center bg-light border p-3 rounded-4">
                            <div class="data-label"><i class="fa-solid fa-user-check"></i> Praticien Déclarant</div>
                            <div class="data-value fs-6 text-dark mt-1">
                                Dr. {{ optional(optional($declaration->doctor)->user)->name ?? optional($declaration->doctor)->name ?? auth()->user()->name ?? 'Médecin' }} {{ optional(optional($declaration->doctor)->user)->prenom ?? '' }}
                            </div>
                            <small class="text-muted mt-1">
                                <i class="fa-solid fa-calendar-check me-1"></i> Certifié le {{ \Carbon\Carbon::parse($declaration->created_at)->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
