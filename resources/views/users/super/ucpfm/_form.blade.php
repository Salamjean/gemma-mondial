@php($isEdit = isset($admin))

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

<div class="alert alert-info py-2 small">
    <i class="fa-solid fa-building-flag me-1"></i>
    Ce compte aura le rôle <strong>admin</strong> rattaché à l'organisation <strong>DMHP</strong>.
</div>

<h5 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-id-card me-1"></i> Informations Personnelles & Professionnelles</h5>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $isEdit ? $admin->user->name : '') }}" placeholder="Ex: KOUAME" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Prénom(s) <span class="text-danger">*</span></label>
        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror" value="{{ old('prenom', $isEdit ? $admin->user->prenom : '') }}" placeholder="Ex: Jean-Marc" required>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Fonction / Titre</label>
        <input type="text" name="fonction" class="form-control @error('fonction') is-invalid @enderror" value="{{ old('fonction', $isEdit ? $admin->fonction : '') }}" placeholder="Ex: Coordonnateur, Responsable suivi-évaluation">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Numéro de Téléphone</label>
        <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror" value="{{ old('contact', $isEdit ? $admin->contact : '') }}" placeholder="Ex: +225 07 00 00 00 00">
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Adresse</label>
        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $isEdit ? $admin->address : '') }}" placeholder="Ex: Abidjan Plateau">
    </div>
</div>

<h5 class="text-primary border-bottom pb-2 mb-3 mt-4"><i class="fa fa-lock me-1"></i> Identifiants de Connexion</h5>
<div class="row">
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Adresse Email de connexion <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $isEdit ? $admin->user->email : '') }}" placeholder="Ex: admin@ucp-fm.ci" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Mot de passe @unless($isEdit)<span class="text-danger">*</span>@endunless</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ $isEdit ? 'Laisser vide pour ne pas modifier' : 'Min. 6 caractères' }}" {{ $isEdit ? '' : 'required' }}>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">Confirmer le mot de passe @unless($isEdit)<span class="text-danger">*</span>@endunless</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Répéter le mot de passe" {{ $isEdit ? '' : 'required' }}>
    </div>
</div>
