@extends('layouts.dashboard')

@section('content')
<div class="row">
    <div class="col-lg-12 col-md-12 col-12">
        <div class="box">
            <!-- En-tête -->
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="box-title fw-bold">DIFFUSER UNE NOTIFICATION PUSH MOBILE</h4>
                    </div>
                    <div>
                        <a href="{{ route('super.notifications.index') }}" class="btn btn-secondary btn-md shadow">
                            <i class="fa fa-arrow-left me-1"></i> Liste des envois
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulaire de diffusion -->
            <form class="form" action="{{ route('super.notifications.send') }}" method="POST" id="pushForm">
                @csrf
                <div class="box-body">

                    @if(isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-20" role="alert">
                            <h5 class="alert-heading fw-bold mb-5"><i class="fa fa-circle-exclamation me-1"></i> Veuillez corriger les erreurs ci-dessous :</h5>
                            <ul class="mb-0 ps-20">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Section 1 : Cible des Destinataires -->
                    <h4 class="box-title text-primary mb-0">
                        <i class="fa fa-users me-10"></i> 1. Cible des Destinataires
                    </h4>
                    <hr class="my-15">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="target" class="form-label fw-bold">
                                    Destinataires <span class="text-danger">*</span>
                                </label>
                                <select name="target" id="target" class="form-select @error('target') is-invalid @enderror" required onchange="toggleSpecificPatient(this.value)">
                                    <option value="all" {{ old('target') == 'all' ? 'selected' : '' }}>
                                        📱 Tous les patients mobiles (Global - {{ $patientsWithToken }} appareil(s) connecté(s))
                                    </option>
                                    <option value="android" {{ old('target') == 'android' ? 'selected' : '' }}>
                                        🤖 Appareils Android uniquement ({{ $androidDevices }} appareil(s))
                                    </option>
                                    <option value="ios" {{ old('target') == 'ios' ? 'selected' : '' }}>
                                        🍎 Appareils iOS / Apple uniquement ({{ $iosDevices }} appareil(s))
                                    </option>
                                    <option value="specific" {{ old('target') == 'specific' ? 'selected' : '' }}>
                                        👤 Un patient spécifique
                                    </option>
                                </select>
                                @error('target')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <!-- Sélection Patient Spécifique -->
                        <div class="col-md-6" id="specific_patient_wrapper" style="display: {{ old('target') === 'specific' ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label for="patient_id" class="form-label fw-bold">
                                    Patient destinataire <span class="text-danger">*</span>
                                </label>
                                <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror">
                                    <option value="">-- Choisir un patient dans la liste --</option>
                                    @foreach($patients as $p)
                                        @php
                                            $pName = trim(($p->user->name ?? '') . ' ' . ($p->user->prenom ?? ''));
                                            if (empty($pName)) {
                                                $pName = 'Patient #' . ($p->code_patient ?? $p->id);
                                            }
                                            $tokenActive = !empty($p->fcm_token) || !empty(optional($p->user)->fcm_token);
                                            $rawDevUpper = strtoupper($p->device_type ?: (optional($p->user)->device_type ?: 'ANDROID'));
                                            $devType = (in_array($rawDevUpper, ['IOS', 'IPHONE', 'IPAD', 'APPLE']) || str_contains($rawDevUpper, 'IOS') || str_contains($rawDevUpper, 'IPHONE')) ? 'iOS' : 'Android';
                                        @endphp
                                        <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->code_patient ?? 'N/A' }} - {{ $pName }}
                                            @if($tokenActive)
                                                ({{ $devType }} - Actif ✅)
                                            @else
                                                (Hors ligne)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('patient_id')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2 : Message et Aperçu en direct -->
                    <h4 class="box-title text-primary mb-0 mt-20">
                        <i class="fa fa-envelope-open-text me-10"></i> 2. Message & Aperçu en Direct
                    </h4>
                    <hr class="my-15">

                    <div class="row">
                        <!-- Colonne Formulaire Saisie -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label for="title" class="form-label fw-bold">
                                    Titre de la notification <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="title" name="title" 
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" required 
                                       placeholder="Ex: Campagne de dépistage ou Rappel important" maxlength="150">
                                <div class="d-flex justify-content-between mt-5">
                                    <small class="text-muted">Court et accrocheur</small>
                                    <small class="text-muted"><span id="title_count">0</span>/150</small>
                                </div>
                                @error('title')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="category" class="form-label fw-bold">
                                    Catégorie d'annonce <span class="text-danger">*</span>
                                </label>
                                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required onchange="updatePreviewBadge(this.value)">
                                    <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>ℹ️ Information Générale</option>
                                    <option value="alert" {{ old('category') == 'alert' ? 'selected' : '' }}>🚨 Alerte Sanitaire</option>
                                    <option value="reminder" {{ old('category') == 'reminder' ? 'selected' : '' }}>⏰ Rappel Médical</option>
                                    <option value="update" {{ old('category') == 'update' ? 'selected' : '' }}>🔄 Mise à jour de l'application</option>
                                    <option value="info" {{ old('category') == 'info' ? 'selected' : '' }}>🏥 Informations Hôpitaux</option>
                                </select>
                                @error('category')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label fw-bold">
                                    Corps du message <span class="text-danger">*</span>
                                </label>
                                <textarea id="message" name="message" rows="4" 
                                          class="form-control @error('message') is-invalid @enderror" 
                                          required placeholder="Saisissez ici le texte qui apparaîtra sur l'écran verrouillé du téléphone..." maxlength="1000">{{ old('message') }}</textarea>
                                <div class="d-flex justify-content-between mt-5">
                                    <small class="text-muted">Texte push instantané</small>
                                    <small class="text-muted"><span id="message_count">0</span>/1000</small>
                                </div>
                                @error('message')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="screen" class="form-label fw-bold">
                                    Page cible dans l'application mobile (Optionnel)
                                </label>
                                <select name="screen" id="screen" class="form-select">
                                    <option value="Notifications">🔔 Page Notifications</option>
                                    <option value="Home">🏠 Page d'Accueil</option>
                                    <option value="RdvList">📅 Mes Rendez-vous</option>
                                    <option value="DossierMedical">📂 Mon Dossier Médical</option>
                                    <option value="Teleconsultation">🩺 Téléconsultation</option>
                                </select>
                            </div>
                        </div>

                        <!-- Colonne Aperçu Mobile en Temps Réel -->
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                    <span><i class="fa fa-mobile-screen text-primary me-1"></i> Aperçu en direct sur le smartphone</span>
                                    <span class="badge bg-primary" id="preview_badge">Information</span>
                                </label>

                                <div class="p-20 rounded border bg-light d-flex flex-column justify-content-center" style="min-height: 280px; border-style: dashed !important; border-color: #cbd5e1 !important;">
                                    
                                    <small class="text-muted text-center mb-15 d-block">
                                        <i class="fa fa-eye me-1"></i> Simulation de la bannière push sur l'écran verrouillé :
                                    </small>

                                    <!-- Bulle de Notification Push -->
                                    <div class="bg-white rounded p-15 shadow-sm border" style="border-radius: 12px !important; border-color: #e2e8f0 !important;">
                                        
                                        <div class="d-flex align-items-center justify-content-between mb-10">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-10" style="width: 26px; height: 26px;">
                                                    <i class="fa fa-heart-pulse" style="font-size: 11px;"></i>
                                                </div>
                                                <span class="fw-bold text-dark fs-12">GEMMA SANTÉ</span>
                                            </div>
                                            <span class="text-muted fs-11">Maintenant</span>
                                        </div>

                                        <!-- Titre Live -->
                                        <h5 class="fw-bold text-dark mb-5 fs-14" id="live_preview_title">
                                            {{ old('title') ?: 'Titre de la notification...' }}
                                        </h5>

                                        <!-- Message Live -->
                                        <p class="text-secondary fs-12 mb-0" id="live_preview_message" style="line-height: 1.4; word-break: break-word;">
                                            {{ old('message') ?: 'Le message apparaîtra ici en temps réel au fur et à mesure que vous tapez dans le champ ci-contre...' }}
                                        </p>
                                    </div>

                                    <div class="text-center text-muted fs-11 mt-15">
                                        <i class="fa fa-circle-check text-success me-1"></i> Compatible avec tous les téléphones <strong>Android</strong> et <strong>iOS (Apple)</strong>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.box-body -->

                <div class="box-footer">
                    <a href="{{ route('super.notifications.index') }}" class="btn btn-warning me-1">
                        <i class="ti-close"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane me-1"></i> Diffuser la notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var titleInput = document.getElementById('title');
    var messageInput = document.getElementById('message');
    var liveTitle = document.getElementById('live_preview_title');
    var liveMessage = document.getElementById('live_preview_message');
    var titleCount = document.getElementById('title_count');
    var messageCount = document.getElementById('message_count');
    var previewBadge = document.getElementById('preview_badge');

    if (titleInput && liveTitle) {
        titleInput.addEventListener('input', function() {
            var val = this.value.trim();
            liveTitle.textContent = val.length > 0 ? val : 'Titre de la notification...';
            if (titleCount) titleCount.textContent = this.value.length;
        });
        if (titleCount) titleCount.textContent = titleInput.value.length;
    }

    if (messageInput && liveMessage) {
        messageInput.addEventListener('input', function() {
            var val = this.value.trim();
            liveMessage.textContent = val.length > 0 ? val : 'Le message apparaîtra ici en temps réel au fur et à mesure que vous tapez dans le champ ci-contre...';
            if (messageCount) messageCount.textContent = this.value.length;
        });
        if (messageCount) messageCount.textContent = messageInput.value.length;
    }

    function toggleSpecificPatient(value) {
        var wrapper = document.getElementById('specific_patient_wrapper');
        var select = document.getElementById('patient_id');
        if (wrapper && select) {
            if (value === 'specific') {
                wrapper.style.display = 'block';
                select.setAttribute('required', 'required');
            } else {
                wrapper.style.display = 'none';
                select.removeAttribute('required');
            }
        }
    }

    function updatePreviewBadge(category) {
        if (!previewBadge) return;
        var labels = {
            'general': 'Information',
            'alert': 'Alerte Sanitaire',
            'reminder': 'Rappel Médical',
            'update': 'Mise à jour',
            'info': 'Hôpitaux'
        };
        var colors = {
            'general': 'bg-primary',
            'alert': 'bg-danger',
            'reminder': 'bg-warning text-dark',
            'update': 'bg-info',
            'info': 'bg-secondary'
        };

        previewBadge.textContent = labels[category] || 'Information';
        previewBadge.className = 'badge ' + (colors[category] || 'bg-primary');
    }
</script>
@endsection
