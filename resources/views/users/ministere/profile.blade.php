@extends('layouts.dashboard', ['title' => $title])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-11">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-20 shadow-sm" role="alert">
                <i class="fa fa-circle-check fs-20 me-10"></i>
                <div class="fw-600">{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-20 shadow-sm" role="alert">
                <div class="fw-bold mb-5"><i class="fa fa-triangle-exclamation me-5"></i> Veuillez corriger les erreurs suivantes :</div>
                <ul class="mb-0 ps-20">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <!-- Carte Récapitulative Profil & Institution -->
            <div class="col-12 col-lg-4">
                <div class="box shadow-sm border-0 rounded-15 overflow-hidden">
                    <div class="box-body text-center pt-30 pb-25 px-20" style="background: linear-gradient(145deg, #0d5c3a, #128052); color: #fff;">
                        <div class="position-relative d-inline-block mb-15">
                            <div class="rounded-circle bg-white p-2 shadow-sm d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px; overflow: hidden;">
                                @if($ministere->img_url)
                                    <img id="preview-avatar" src="{{ asset('assets/uploads/ministere/' . $ministere->img_url) }}" alt="Photo Ministère" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                                @else
                                    <img id="preview-avatar" src="{{ asset('assets/uploads/republique.png') }}" alt="Logo Ministère" class="w-100 h-100" style="object-fit: contain;">
                                @endif
                            </div>
                            <span class="badge bg-warning text-dark position-absolute bottom-0 end-0 rounded-pill px-2 py-1 fs-11 fw-bold shadow-xs">
                                <i class="fa fa-shield-halved"></i> Officiel
                            </span>
                        </div>
                        <h4 class="mb-5 text-white fw-bold text-uppercase">{{ $user->name }} {{ $user->prenom }}</h4>
                        <p class="mb-10 text-white-70 fs-13"><i class="fa fa-envelope me-5"></i> {{ $user->email }}</p>
                        <span class="badge bg-white-20 text-white px-12 py-6 rounded-pill fs-12 fw-600 border border-white-30">
                            {{ roleFr($user->role_as) }}
                        </span>
                    </div>

                    <div class="box-body bg-white py-20 px-20">
                        <h6 class="text-uppercase text-muted fw-bold fs-11 tracking-wide mb-15">Détails Institutionnels</h6>
                        <div class="d-flex align-items-center mb-15 pb-10 border-bottom">
                            <div class="bg-success-light text-success rounded-circle p-10 me-12 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa fa-hashtag"></i>
                            </div>
                            <div>
                                <span class="text-muted fs-12 d-block">Référence Compte</span>
                                <strong class="text-dark fs-14">{{ $ministere->reference ?? 'MIN-AUTH-001' }}</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-15 pb-10 border-bottom">
                            <div class="bg-info-light text-info rounded-circle p-10 me-12 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa fa-building-columns"></i>
                            </div>
                            <div>
                                <span class="text-muted fs-12 d-block">Direction / Département</span>
                                <strong class="text-dark fs-13">{{ $ministere->nom_direction ?: 'Direction Générale de la Santé' }}</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-15 pb-10 border-bottom">
                            <div class="bg-warning-light text-warning rounded-circle p-10 me-12 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa fa-user-tag"></i>
                            </div>
                            <div>
                                <span class="text-muted fs-12 d-block">Fonction / Titre</span>
                                <strong class="text-dark fs-13">{{ $ministere->fonction ?: 'Administrateur Statistique' }}</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-primary-light text-primary rounded-circle p-10 me-12 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div>
                                <span class="text-muted fs-12 d-block">Contact Direct</span>
                                <strong class="text-dark fs-13">{{ $ministere->contact ?: 'Non renseigné' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de modification du profil -->
            <div class="col-12 col-lg-8">
                <div class="box shadow-sm border-0 rounded-15">
                    <div class="box-header with-border bg-white py-15 px-25">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="box-title text-dark fw-bold mb-0">
                                <i class="fa fa-user-pen text-success me-2"></i> Modifier mon Profil Ministère
                            </h4>
                            <a href="{{ route('ministere.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-15">
                                <i class="fa fa-arrow-left me-1"></i> Retour au tableau de bord
                            </a>
                        </div>
                    </div>

                    <form class="form" action="{{ route('ministere.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="box-body px-25 py-20">

                            <!-- Section 1 : Informations personnelles & administratives -->
                            <div class="d-flex align-items-center mb-15">
                                <span class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center me-10" style="width: 26px; height: 26px; font-size: 12px; font-weight: bold;">1</span>
                                <h5 class="text-dark fw-bold mb-0">Identité & Affectation Institutionnelle</h5>
                            </div>

                            <div class="row g-3 mb-25">
                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="name" class="form-label text-dark fw-600 fs-13">
                                            Nom de famille <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-user text-muted"></i></span>
                                            <input type="text" id="name" name="name" class="form-control border-start-0 @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user->name) }}" required placeholder="Ex: KASSI">
                                        </div>
                                        @error('name')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="prenom" class="form-label text-dark fw-600 fs-13">
                                            Prénoms
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-user-tag text-muted"></i></span>
                                            <input type="text" id="prenom" name="prenom" class="form-control border-start-0 @error('prenom') is-invalid @enderror"
                                                value="{{ old('prenom', $user->prenom) }}" placeholder="Ex: DIDIER ALAIN">
                                        </div>
                                        @error('prenom')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="nom_direction" class="form-label text-dark fw-600 fs-13">
                                            Direction / Département Ministériel
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-building-columns text-muted"></i></span>
                                            <input type="text" id="nom_direction" name="nom_direction" class="form-control border-start-0 @error('nom_direction') is-invalid @enderror"
                                                value="{{ old('nom_direction', $ministere->nom_direction) }}" placeholder="Ex: Direction Générale de la Santé Publique">
                                        </div>
                                        @error('nom_direction')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="fonction" class="form-label text-dark fw-600 fs-13">
                                            Fonction / Poste
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-briefcase text-muted"></i></span>
                                            <input type="text" id="fonction" name="fonction" class="form-control border-start-0 @error('fonction') is-invalid @enderror"
                                                value="{{ old('fonction', $ministere->fonction) }}" placeholder="Ex: Data Analyste / Responsable Suivi">
                                        </div>
                                        @error('fonction')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="contact" class="form-label text-dark fw-600 fs-13">
                                            Contact Téléphonique
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-phone text-muted"></i></span>
                                            <input type="text" id="contact" name="contact" class="form-control border-start-0 @error('contact') is-invalid @enderror"
                                                value="{{ old('contact', $ministere->contact) }}" placeholder="Ex: 0102030405">
                                        </div>
                                        @error('contact')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="image" class="form-label text-dark fw-600 fs-13">
                                            Photo de profil / Logo de Direction
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-image text-muted"></i></span>
                                            <input type="file" id="image" name="image" class="form-control border-start-0 @error('image') is-invalid @enderror"
                                                accept="image/*" onchange="previewProfileImage(event)">
                                        </div>
                                        <span class="text-muted fs-11">Formats acceptés : JPG, PNG, WEBP (Max: 2Mo)</span>
                                        @error('image')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-20">

                            <!-- Section 2 : Identifiants & Sécurité -->
                            <div class="d-flex align-items-center mb-15">
                                <span class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center me-10" style="width: 26px; height: 26px; font-size: 12px; font-weight: bold;">2</span>
                                <h5 class="text-dark fw-bold mb-0">Identifiants de Connexion & Sécurité</h5>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label class="form-label text-dark fw-600 fs-13">
                                            Adresse E-mail <span class="badge bg-light text-muted ms-1 fs-10">Non modifiable</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-lock text-muted"></i></span>
                                            <input type="email" class="form-control bg-light text-muted border-start-0" value="{{ $user->email }}" disabled readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="password" class="form-label text-dark fw-600 fs-13">
                                            Nouveau mot de passe
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-key text-muted"></i></span>
                                            <input type="password" id="password" name="password" class="form-control border-start-0 @error('password') is-invalid @enderror"
                                                placeholder="Laisser vide pour ne pas changer" autocomplete="new-password">
                                        </div>
                                        @error('password')
                                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="password_confirmation" class="form-label text-dark fw-600 fs-13">
                                            Confirmer le mot de passe
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-check-double text-muted"></i></span>
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control border-start-0"
                                                placeholder="Répéter le mot de passe" autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="box-footer bg-light px-25 py-15 text-end d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-12"><span class="text-danger fw-bold">*</span> Champs obligatoires</span>
                            <button type="submit" class="btn btn-success px-30 rounded-pill shadow-xs fw-600">
                                <i class="fa fa-floppy-disk me-1"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    function previewProfileImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview-avatar');
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.objectFit = 'cover';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
