@extends('layouts.dashboard')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-12">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="box-title fw-bold text-dark"><i class="fa fa-edit text-primary me-2"></i> Modifier le compte Ministère</h3>
                    <p class="text-muted mb-0 small">{{ $ministere->user->name ?? '' }} {{ $ministere->user->prenom ?? '' }} ({{ $ministere->nom_direction }})</p>
                </div>
                <div>
                    <a href="{{ route('super.ministere.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fa fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <form action="{{ route('super.ministere.update', $ministere->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="box-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-id-card me-1"></i> Informations Personnelles & Professionnelles</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $ministere->user->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Prénom(s) <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom', $ministere->user->prenom ?? '') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Direction / Département ministériel <span class="text-danger">*</span></label>
                            <input type="text" name="nom_direction" class="form-control @error('nom_direction') is-invalid @enderror" value="{{ old('nom_direction', $ministere->nom_direction) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fonction / Titre</label>
                            <input type="text" name="fonction" class="form-control @error('fonction') is-invalid @enderror" value="{{ old('fonction', $ministere->fonction) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Numéro de Téléphone</label>
                            <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact', $ministere->contact) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Code / Référence Ministère</label>
                            <input type="text" name="reference" class="form-control" value="{{ old('reference', $ministere->reference) }}">
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fa fa-lock me-1"></i> Identifiants de Connexion</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Adresse Email de connexion <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $ministere->user->email ?? '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nouveau mot de passe <small class="text-muted">(laisser vide pour ne pas changer)</small></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 caractères">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Confirmer le nouveau mot de passe</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Répéter le mot de passe">
                        </div>
                    </div>
                </div>

                <div class="box-footer text-end">
                    <a href="{{ route('super.ministere.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary px-4 shadow">
                        <i class="fa fa-save me-1"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
