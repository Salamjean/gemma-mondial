@extends('layouts.dashboard')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12 col-md-12 col-12">
        
        <div class="box">
            <!-- En-tête avec titre et bouton retour -->
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="box-title text-dark">
                        <i class="fa fa-cogs text-primary me-2"></i> Paramètres & Profil de l'Hôpital
                    </h4>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-md shadow-sm">
                        <i class="ti-arrow-left me-1"></i> Retour au Tableau de Bord
                    </a>
                </div>
            </div>

            <div class="box-body fs-14">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i> <strong>Veuillez vérifier les champs suivants :</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Récapitulatif actuel de l'établissement -->
                <h4 class="box-title text-primary mb-0">
                    <i class="ti-home me-2"></i> Établissement : <span class="text-uppercase fw-bold text-dark">{{ $hospital->user->name ?? $hospital->label }}</span>
                </h4>
                <hr class="my-15">
                
                <div class="row align-items-center mb-20">
                    <div class="col-md-2 col-sm-4 col-12 text-center mb-3 mb-md-0">
                        @if($hospital->img_url)
                            <img src="{{ asset("assets/uploads/hospital/{$hospital->img_url}") }}" alt="Logo" class="img-thumbnail rounded shadow-sm" style="max-height: 120px; max-width: 120px; object-fit: contain;">
                        @else
                            <img src="{{ asset('assets/uploads/hospital.gif') }}" alt="Logo" class="img-thumbnail rounded shadow-sm" style="max-height: 120px; max-width: 120px; object-fit: contain;">
                        @endif
                    </div>
                    <div class="col-md-10 col-sm-8 col-12">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 mb-10">
                                <div class="form-label mb-1"><strong>Référence :</strong> <span class="text-danger fw-bold">{{ $hospital->reference }}</span></div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-10">
                                <div class="form-label mb-1"><strong>Nom :</strong> {{ $hospital->user->name ?? $hospital->label }}</div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-10">
                                <div class="form-label mb-1"><strong>E-mail :</strong> {{ $hospital->user->email }}</div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-10">
                                <div class="form-label mb-1"><strong>Contact :</strong> {{ $hospital->contact ?? 'Non renseigné' }}</div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-10">
                                <div class="form-label mb-1"><strong>District :</strong> {{ $hospital->district_sanitaire ?? 'Non renseigné' }}</div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-10">
                                <div class="form-label mb-1">
                                    <strong>Statut :</strong>
                                    @if($hospital->status == 0)
                                        <span class="badge badge-success">Actif</span>
                                    @else
                                        <span class="badge badge-danger">Inactif</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION ÉCRAN SALLE D'ATTENTE & TV ANDROID (REDESIGN PREMIUM) -->
                @php
                    $token = $hospital->getOrGenerateTvToken();
                    $tvUrl = route('tv.waiting_screen', $token);
                @endphp
                
                <div class="card border-0 shadow-sm rounded-24 mb-30 overflow-hidden" style="background: linear-gradient(135deg, #f0fdf4 0%, #f0f9ff 50%, #ffffff 100%); border: 1.5px solid #bae6fd !important; box-shadow: 0 15px 35px -10px rgba(2, 132, 199, 0.12) !important;">
                    <div class="card-body p-25">
                        <div class="row align-items-center g-4">
                            <!-- Colonne Gauche : Présentation & Fonctionnalités -->
                            <div class="col-xl-7 col-lg-6 col-12">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-20 d-flex align-items-center justify-content-center text-white shadow" style="width: 68px; height: 68px; font-size: 28px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); flex-shrink: 0;">
                                        <i class="fa-solid fa-tv"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <span class="badge px-3 py-1 rounded-pill text-white fw-bold fs-11 shadow-xs" style="background: linear-gradient(135deg, #059669, #10b981);">
                                                <i class="fa-solid fa-signal-stream me-1"></i> EN DIRECT H24
                                            </span>
                                            <span class="badge bg-white text-primary border px-2.5 py-1 rounded-pill fw-bold fs-11">
                                                <i class="fa-solid fa-volume-high text-primary me-1"></i> Synthèse Vocale & Carillon
                                            </span>
                                            <span class="badge bg-white text-dark border px-2.5 py-1 rounded-pill fw-bold fs-11">
                                                <i class="fa-brands fa-android text-success me-1"></i> TV Android
                                            </span>
                                        </div>

                                        <h3 class="fw-extrabold text-dark mb-2" style="font-size: 1.35rem; letter-spacing: -0.3px;">
                                            Écran d'Appel des Patients en Salle d'Attente
                                        </h3>
                                        <p class="text-secondary fs-13 mb-3 leading-relaxed">
                                            Projetez cet écran sur vos <strong>TV Android, Smart TV ou vidéoprojecteurs</strong>. Les patients sont automatiquement appelés par leur nom avec le nom du médecin et la salle dès que la consultation démarre.
                                        </p>

                                        <!-- 3 Puces caractéristiques clés -->
                                        <div class="row g-2 pt-1">
                                            <div class="col-sm-6 col-12">
                                                <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-12 border shadow-xs">
                                                    <i class="fa-solid fa-bolt text-warning fs-14"></i>
                                                    <span class="fs-12 fw-semibold text-dark">Appel instantané au clic médecin</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-12 border shadow-xs">
                                                    <i class="fa-solid fa-unlock-keyhole text-success fs-14"></i>
                                                    <span class="fs-12 fw-semibold text-dark">Accès TV sans mot de passe</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne Droite : Boîte d'Actions & Lien Direct -->
                            <div class="col-xl-5 col-lg-6 col-12">
                                <div class="bg-white p-20 rounded-20 border shadow-xs">
                                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                        <span class="fw-bold text-dark fs-13">
                                            <i class="fa-solid fa-link text-primary me-1"></i> Lien d'accès TV & Vidéoprojecteur
                                        </span>
                                        <button type="button" class="btn btn-sm btn-light text-primary border rounded-pill px-2.5 py-1 fs-11 fw-bold" onclick="showTvQrCode('{{ $tvUrl }}')">
                                            <i class="fa-solid fa-qrcode me-1"></i> QR Code
                                        </button>
                                    </div>

                                    <!-- Champ URL Stylé avec Copie Instantanée -->
                                    <div class="input-group mb-3">
                                        <span class="input-group-text bg-light border-end-0 text-muted">
                                            <i class="fa-solid fa-globe fs-13"></i>
                                        </span>
                                        <input type="text" id="tvUrlInput" class="form-control bg-light border-start-0 text-dark fw-bold fs-12" value="{{ $tvUrl }}" readonly onclick="this.select()">
                                        <button class="btn btn-primary px-3 fw-bold fs-13" type="button" onclick="copyTvLink('{{ $tvUrl }}')">
                                            <i class="fa-solid fa-copy me-1"></i> Copier
                                        </button>
                                    </div>

                                    <!-- Boutons Principaux -->
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-success w-100 rounded-12 py-2.5 fw-bold fs-12 shadow-xs d-flex align-items-center justify-content-center gap-1.5" onclick="copyTvLink('{{ $tvUrl }}')">
                                                <i class="fa-brands fa-android fs-15 text-success"></i>
                                                <span>Lien TV Android</span>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('hospital.waiting_screen') }}" target="_blank" class="btn btn-primary w-100 rounded-12 py-2.5 fw-bold fs-12 shadow-xs d-flex align-items-center justify-content-center gap-1.5">
                                                <i class="fa-solid fa-up-right-from-square fs-13"></i>
                                                <span>Ouvrir l'Écran</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de Modification -->
                <form class="form" action="{{ route('hospital.update') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <!-- SECTION 1 : INFORMATIONS GÉNÉRALES -->
                    <h4 class="box-title text-success pt-15">
                        <i class="ti-pencil-alt me-2"></i> 1. Modifier les Informations Générales
                    </h4>
                    <hr class="my-10">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-15">
                                <label for="label" class="form-label fw-bold">Nom de l'Hôpital <span class="text-danger">*</span></label>
                                <input type="text" id="label" name="label" class="form-control @error('label') is-invalid @enderror" value="{{ old('label', $hospital->label ?? $hospital->user->name) }}" required placeholder="Nom de l'établissement">
                                @error('label')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-15">
                                <label for="contact" class="form-label fw-bold">Contact Téléphonique <span class="text-danger">*</span></label>
                                <div class="d-flex">
                                    <span class="form-control w-70 text-center bg-light" style="border-top-right-radius: 0; border-bottom-right-radius: 0; max-width: 65px;">+225</span>
                                    <input type="text" id="contact" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;" name="contact" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact', $hospital->contact) }}" placeholder="Ex: 0102030405">
                                </div>
                                @error('contact')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-15">
                                <label for="district_sanitaire" class="form-label fw-bold">District Sanitaire</label>
                                <input type="text" id="district_sanitaire" name="district_sanitaire" class="form-control @error('district_sanitaire') is-invalid @enderror" value="{{ old('district_sanitaire', $hospital->district_sanitaire) }}" placeholder="Ex: District Sanitaire de Cocody">
                                @error('district_sanitaire')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-15">
                                <label for="nom_direction_generale" class="form-label fw-bold">Direction Générale</label>
                                <input type="text" id="nom_direction_generale" name="nom_direction_generale" class="form-control @error('nom_direction_generale') is-invalid @enderror" value="{{ old('nom_direction_generale', $hospital->nom_direction_generale) }}" placeholder="Ex: Direction Régionale de la Santé">
                                @error('nom_direction_generale')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2 : LOGO & IMAGE DE FILIGRANE (WATERMARK) -->
                    <h4 class="box-title text-success pt-20">
                        <i class="ti-image me-2"></i> 2. Identité Visuelle & Filigrane pour Impressions
                    </h4>
                    <hr class="my-10">

                    <div class="row">
                        <!-- Logo Officiel -->
                        <div class="col-md-6 mb-20">
                            <div class="p-15 bg-light rounded border">
                                <label for="image" class="form-label fw-bold text-dark mb-2">
                                    <i class="fa-solid fa-hospital text-primary me-1"></i> Logo Officiel de l'Hôpital
                                </label>
                                <div class="d-flex align-items-center gap-3 mb-10">
                                    <div class="bg-white p-1 rounded border text-center" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        @if($hospital->img_url)
                                            <img src="{{ asset("assets/uploads/hospital/{$hospital->img_url}") }}" alt="Logo" style="max-height: 60px; max-width: 60px; object-fit: contain;">
                                        @else
                                            <img src="{{ asset('assets/uploads/hospital.gif') }}" alt="Logo" style="max-height: 60px; max-width: 60px; object-fit: contain;">
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/*">
                                        <small class="text-muted d-block mt-1">Affiché sur l'en-tête de la plateforme et des fiches.</small>
                                    </div>
                                </div>
                                @error('image')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Image de Filigrane (Watermark) -->
                        <div class="col-md-6 mb-20">
                            <div class="p-15 bg-light rounded border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="watermark" class="form-label fw-bold text-dark mb-0">
                                        <i class="fa-solid fa-stamp text-primary me-1"></i> Image de Filigrane (Watermark)
                                    </label>
                                    @if($hospital->watermark_url)
                                        <a href="{{ route('hospital.delete.watermark') }}" class="btn btn-xs btn-outline-danger" onclick="return confirm('Voulez-vous vraiment supprimer cette image de filigrane ?');">
                                            <i class="fa fa-trash me-1"></i> Supprimer
                                        </a>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-3 mb-10">
                                    <div class="bg-white p-1 rounded border text-center" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                        @if($hospital->watermark_url)
                                            <img src="{{ asset("assets/uploads/hospital/{$hospital->watermark_url}") }}" alt="Filigrane" style="max-height: 60px; max-width: 60px; object-fit: contain; opacity: 0.6;">
                                        @else
                                            <span class="text-muted fs-11">Aucun</span>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input class="form-control @error('watermark') is-invalid @enderror" type="file" id="watermark" name="watermark" accept="image/png, image/jpeg, image/jpg, image/webp">
                                        <small class="text-muted d-block mt-1">
                                            Incrusté en arrière-plan transparent de tous les <strong>documents PDF et imprimés</strong> (ordonnances, bulletins, factures).
                                        </small>
                                    </div>
                                </div>
                                @error('watermark')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3 : INFOS DE CONNEXION & MOT DE PASSE -->
                    <h4 class="box-title text-success pt-15">
                        <i class="ti-lock me-2"></i> 3. Infos de Connexion & Sécurité
                    </h4>
                    <hr class="my-10">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-15">
                                <label class="form-label fw-bold">E-mail de Connexion</label>
                                <input type="email" id="email" name="email" class="form-control bg-light" value="{{ $hospital->user->email }}" disabled readonly>
                                <small class="text-muted">Identifiant unique de votre compte hôpital.</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-15">
                                <label for="password" class="form-label fw-bold">Nouveau Mot de Passe</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" placeholder="Laisser vide si inchangé">
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-15">
                                <label for="password_confirmation" class="form-label fw-bold">Confirmation</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" placeholder="Confirmer le mot de passe">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-10">
                        <div class="col-12">
                            <p class="text-dark fw-bold mb-0"><span class="text-danger fw-bold">*</span> Champs obligatoires</p>
                        </div>
                    </div>

                    <!-- PIED DE PAGE AVEC BOUTON DE SOUMISSION -->
                    <div class="box-footer text-end mt-20 px-0 pb-0">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="ti-save-alt me-1"></i> <strong>Enregistrer les modifications</strong>
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyTvLink(url) {
        if (!url) return;
        navigator.clipboard.writeText(url).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Lien TV Android copié !',
                html: '<p class="text-muted fs-14 mb-2">Collez cette adresse dans le navigateur de votre TV Android / Smart TV :</p><code class="d-block p-2 bg-light rounded text-dark fs-12 text-break mb-3">' + url + '</code><p class="fs-13 text-success fw-bold mb-0">✓ Aucun mot de passe requis sur la TV !</p>',
                confirmButtonText: 'Compris'
            });
        }).catch(function() {
            const input = document.getElementById('tvUrlInput');
            if (input) {
                input.select();
                document.execCommand('copy');
                alert('Lien copié : ' + url);
            }
        });
    }

    function showTvQrCode(url) {
        if (!url) return;
        const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=" + encodeURIComponent(url);
        Swal.fire({
            title: 'QR Code pour TV & Mobile',
            html: '<p class="text-muted fs-13 mb-3">Scannez ce QR Code avec un smartphone ou une tablette pour ouvrir la salle d\'attente ou partager le lien :</p>' +
                  '<div class="p-3 bg-light rounded-16 d-inline-block border shadow-xs mb-2"><img src="' + qrUrl + '" alt="QR Code" class="rounded-12" style="width: 200px; height: 200px;"></div>' +
                  '<p class="fs-12 text-success fw-bold mt-2 mb-0"><i class="fa-solid fa-lock-open me-1"></i> Connexion automatique</p>',
            showCloseButton: true,
            confirmButtonText: 'Fermer',
            confirmButtonColor: '#0284c7'
        });
    }
</script>
@endpush