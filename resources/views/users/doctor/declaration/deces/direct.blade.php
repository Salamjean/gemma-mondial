@extends('layouts.dashboard', ['title' => 'Déclaration de Décès Directe'])

@section('content')
<style>
    .deces-header-card {
        background: linear-gradient(135deg, #991b1b 0%, #dc2626 50%, #f97316 100%);
        border-radius: 16px;
        color: white;
        box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.25);
    }
    .custom-card-section {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
    }
    .form-floating-custom label {
        font-weight: 600;
        font-size: 0.88rem;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 11px 14px;
        font-size: 0.95rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
    }
    .patient-selected-box {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #86efac;
        border-radius: 14px;
    }
    .hospital-pill {
        background: #f8fafc;
        border-radius: 14px;
        padding: 16px;
        border: 1px solid #e2e8f0;
    }
    .section-badge-num {
        width: 32px;
        height: 32px;
        background: #dc2626;
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .sub-section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-11 col-12">
        
        <!-- En-tête de la page -->
        <div class="deces-header-card p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle text-white shadow-sm">
                        <i class="fa-solid fa-book-skull fa-2x"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-1 text-white">Enregistrement Direct d'un Décès</h3>
                        <p class="mb-0 text-white-50">Constat immédiat et déclaration officielle sans formalités de consultation préalable.</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('doctor.declaration.deces.list') }}" class="btn btn-light btn-md fw-semibold shadow-sm text-danger">
                        <i class="fa-solid fa-list-ul me-1"></i> Registre des Décès
                    </a>
                </div>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-circle-exclamation fa-2x me-3 text-danger"></i>
                    <div>
                        <h6 class="fw-bold mb-0">Erreur lors de l'enregistrement</h6>
                        <p class="mb-0 small">{{ session('error') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center mb-2">
                    <i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>
                    <h6 class="fw-bold mb-0">Veuillez vérifier les informations suivantes :</h6>
                </div>
                <ul class="mb-0 ps-4 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('doctor.declaration.deces.store_direct') }}" method="POST" id="formDirectDeces">
            @csrf

            <!-- ========================================================================= -->
            <!-- CARTE UNIQUE 1 : IDENTIFICATION DU DÉFUNT, LIEU, DATE & CIRCONSTANCES -->
            <!-- ========================================================================= -->
            <div class="custom-card-section p-4">
                
                <!-- 1. IDENTIFICATION DU DÉFUNT -->
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <span class="section-badge-num">1</span>
                        <h5 class="fw-bold text-dark mb-0">Identification de la Personne Décédée</h5>
                    </div>

                    <!-- Select du Statut de la personne -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 form-floating-custom">
                            <label><i class="fa-solid fa-user-tag text-danger me-1"></i> Statut de la personne décédée <span class="text-danger">*</span></label>
                            <select name="patient_mode" id="patient_mode_select" class="form-select fw-semibold" onchange="togglePatientModeSelect(this.value)">
                                <option value="existing" {{ old('patient_mode', 'existing') === 'existing' ? 'selected' : '' }}>
                                    👤 Patient déjà enregistré à l'hôpital (Recherche par Nom ou Numéro DM)
                                </option>
                                <option value="new" {{ old('patient_mode') === 'new' ? 'selected' : '' }}>
                                    ➕ Défunt non enregistré / Corps externe (Saisie libre et création directe)
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- SOUS-SECTION : PATIENT EXISTANT -->
                    <div id="sectionExistingPatient" style="{{ old('patient_mode', 'existing') === 'existing' ? '' : 'display: none;' }}">
                        <div class="bg-light p-3 rounded-4 mb-3 border">
                            <label class="form-label fw-bold text-dark mb-2">
                                <i class="fa-solid fa-magnifying-glass text-primary me-1"></i> Rechercher le patient dans la base
                            </label>
                            <div class="row g-2 align-items-center">
                                <div class="col-md-4">
                                    <select id="searchType" class="form-select">
                                        <option value="name">Recherche par Nom & Prénoms</option>
                                        <option value="reference">Recherche par Code Patient (DM)</option>
                                        <option value="date">Recherche par Date de naissance</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" id="patientSearchInput" class="form-control" placeholder="Tapez le nom, prénom ou référence...">
                                        <button type="button" class="btn btn-primary px-4 fw-semibold" id="btnSearchPatient" onclick="searchPatientAjax()">
                                            <i class="fa-solid fa-search me-1"></i> Chercher
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Résultats AJAX -->
                            <div id="searchResults" class="mt-3" style="display: none;"></div>
                        </div>

                        <!-- Carte de sélection active -->
                        <input type="hidden" name="patient_id" id="selectedPatientId" value="{{ old('patient_id') }}">
                        <div id="selectedPatientCard" class="patient-selected-box p-3 shadow-sm" style="{{ old('patient_id') ? '' : 'display: none;' }}">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success text-white p-3 rounded-circle shadow-sm">
                                        <i class="fa-solid fa-user-check fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="badge bg-success mb-1">Patient Sélectionné</div>
                                        <h5 class="fw-bold text-dark mb-1" id="displayPatientName">--</h5>
                                        <div class="text-muted small d-flex flex-wrap gap-3">
                                            <span><i class="fa-solid fa-id-card me-1 text-primary"></i>Code: <strong class="text-primary" id="displayPatientCode">--</strong></span>
                                            <span><i class="fa-solid fa-venus-mars me-1 text-secondary"></i>Genre: <strong id="displayPatientGender">--</strong></span>
                                            <span><i class="fa-solid fa-cake-candles me-1 text-secondary"></i>Né(e) le: <strong id="displayPatientBirth">--</strong></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" onclick="clearSelectedPatient()">
                                        <i class="fa-solid fa-rotate-left me-1"></i> Changer de patient
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SOUS-SECTION : DÉFUNT NON ENREGISTRÉ (CORPS EXTERNE) -->
                    <div id="sectionNewPatient" style="{{ old('patient_mode') === 'new' ? '' : 'display: none;' }}">
                        <div class="p-3 bg-light rounded-4 border">
                            <div class="row g-3">
                                <div class="col-md-6 form-floating-custom">
                                    <label>Nom de famille du défunt <span class="text-danger">*</span></label>
                                    <input type="text" name="nom" class="form-control" placeholder="ex: KOUAME" value="{{ old('nom') }}">
                                </div>
                                <div class="col-md-6 form-floating-custom">
                                    <label>Prénom(s)</label>
                                    <input type="text" name="prenom" class="form-control" placeholder="ex: Yao Michel" value="{{ old('prenom') }}">
                                </div>
                                <div class="col-md-4 form-floating-custom">
                                    <label>Sexe / Genre <span class="text-danger">*</span></label>
                                    <select name="genre" class="form-select">
                                        <option value="masculin" {{ old('genre') === 'masculin' ? 'selected' : '' }}>Masculin</option>
                                        <option value="feminin" {{ old('genre') === 'feminin' ? 'selected' : '' }}>Féminin</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-floating-custom">
                                    <label>Date de naissance (si connue)</label>
                                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                                </div>
                                <div class="col-md-4 form-floating-custom">
                                    <label>Ou Âge estimé (années)</label>
                                    <input type="number" name="age_estime" class="form-control" placeholder="ex: 50" min="0" max="130" value="{{ old('age_estime') }}">
                                </div>
                                <div class="col-md-6 form-floating-custom">
                                    <label>Profession</label>
                                    <input type="text" name="profession" class="form-control" placeholder="ex: Enseignant, Agriculteur..." value="{{ old('profession') }}">
                                </div>
                                <div class="col-md-6 form-floating-custom">
                                    <label>Contact des proches / Famille</label>
                                    <input type="text" name="telephone" class="form-control" placeholder="ex: 0700000000" value="{{ old('telephone') }}">
                                </div>
                                <div class="col-md-12 form-floating-custom">
                                    <label>Lieu de résidence habituelle</label>
                                    <input type="text" name="lieu_residence" class="form-control" placeholder="ex: Quartier, Commune, Ville..." value="{{ old('lieu_residence') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SÉPARATEUR INTERNE ÉLÉGANT -->
                <hr class="my-4" style="border-top: 2px dashed #e2e8f0;">

                <!-- 2. LIEU, DATE ET CIRCONSTANCES TEMPORELLES -->
                <div>
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <span class="section-badge-num">2</span>
                        <h5 class="fw-bold text-dark mb-0">Lieu, Date et Circonstances Temporelles</h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6 form-floating-custom">
                            <label>Date du décès <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-regular fa-calendar-days text-danger"></i></span>
                                <input type="date" name="date" class="form-control fw-bold" value="{{ old('date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6 form-floating-custom">
                            <label>Heure du décès <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa-regular fa-clock text-danger"></i></span>
                                <input type="time" name="heure" class="form-control fw-bold" value="{{ old('heure', date('H:i')) }}" required>
                            </div>
                        </div>

                        <!-- SELECT DU LIEU DE DÉCÈS -->
                        <div class="col-12 mt-3">
                            <div class="form-floating-custom">
                                <label><i class="fa-solid fa-location-dot text-danger me-1"></i> Lieu du décès <span class="text-danger">*</span></label>
                                <select name="lieu_type" id="lieu_type_select" class="form-select fw-semibold" onchange="toggleLieuModeSelect(this.value)">
                                    <option value="hospital" {{ old('lieu_type', 'hospital') === 'hospital' ? 'selected' : '' }}>
                                        🏥 Au sein de l'établissement ({{ $hospital->label ?? 'Centre Hospitalier' }})
                                    </option>
                                    <option value="hors" {{ old('lieu_type') === 'hors' ? 'selected' : '' }}>
                                        🏡 Hors de l'établissement (Domicile, Voie publique, Extérieur, Autre...)
                                    </option>
                                </select>
                            </div>

                            <!-- Précision Lieu : Dans l'hôpital -->
                            <div id="sectionLieuHospital" class="hospital-pill mt-2" style="{{ old('lieu_type', 'hospital') === 'hospital' ? '' : 'display: none;' }}">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-success-light text-success fw-bold"><i class="fa-solid fa-check me-1"></i> Établissement connecté :</span>
                                    <strong class="text-dark">{{ $hospital->label ?? 'Centre Hospitalier' }}</strong>
                                </div>
                                <div class="form-floating-custom">
                                    <label>Précision du service / Unité (optionnel)</label>
                                    <input type="text" name="service_hospital" class="form-control" placeholder="ex: Service des Urgences, Réanimation, Bloc opératoire, Pavillon A..." value="{{ old('service_hospital') }}">
                                </div>
                            </div>

                            <!-- Précision Lieu : Hors établissement -->
                            <div id="sectionLieuHors" class="bg-light p-3 rounded-4 border mt-2" style="{{ old('lieu_type') === 'hors' ? '' : 'display: none;' }}">
                                <div class="form-floating-custom">
                                    <label>Veuillez renseigner le lieu exact du décès <span class="text-danger">*</span></label>
                                    <input type="text" name="lieu_autre" id="inputLieuAutre" class="form-control" placeholder="ex: Domicile du défunt (Cocody), Voie publique (Autoroute du Nord), En cours d'évacuation..." value="{{ old('lieu_autre') }}">
                                </div>
                            </div>
                        </div>

                        <!-- SELECTS ALIGNÉS SUR LA MÊME LIGNE -->
                        <div class="col-md-4 form-floating-custom">
                            <label>Milieu de résidence <span class="text-danger">*</span></label>
                            <select name="milieu_residence" class="form-select">
                                <option value="Urbain" {{ old('milieu_residence', 'Urbain') === 'Urbain' ? 'selected' : '' }}>Urbain</option>
                                <option value="Rural" {{ old('milieu_residence', 'Rural') === 'Rural' ? 'selected' : '' }}>Rural</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-floating-custom">
                            <label>Catégorie de la personne <span class="text-danger">*</span></label>
                            <select name="person" class="form-select">
                                <option value="patient" {{ old('person', 'patient') === 'patient' ? 'selected' : '' }}>Patient (Adulte ou Enfant)</option>
                                <option value="enfant" {{ old('person') === 'enfant' ? 'selected' : '' }}>Nouveau-né</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-floating-custom">
                            <label>Décès lié à la grossesse / maternel ?</label>
                            <select name="deces_maternel" class="form-select">
                                <option value="non" {{ old('deces_maternel', 'non') === 'non' ? 'selected' : '' }}>Non</option>
                                <option value="oui" {{ old('deces_maternel') === 'oui' ? 'selected' : '' }}>Oui</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- CARTE 2 : CONSTAT MÉDICAL & CAUSES DU DÉCÈS -->
            <!-- ========================================== -->
            <div class="custom-card-section p-4">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="section-badge-num">3</span>
                    <h5 class="fw-bold text-dark mb-0">Constat Médical & Causes du Décès</h5>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 form-floating-custom">
                        <label>Cause Initiale / Sous-jacente <span class="text-danger">*</span></label>
                        <textarea name="cause_initiale" class="form-control" rows="3" placeholder="Pathologie ou événement ayant déclenché l'enchaînement morbide (ex: AVC ischémique massif, Polytraumatisme par AVP, Choc septique sur péritonite...)" required>{{ old('cause_initiale') }}</textarea>
                    </div>
                    <div class="col-md-6 form-floating-custom">
                        <label>Cause Directe du Décès <span class="text-danger">*</span></label>
                        <textarea name="cause_directe" class="form-control" rows="3" placeholder="État morbide final ayant provoqué la mort (ex: Arrêt cardio-respiratoire irréversible, Asystolie, Choc réfractaire...)" required>{{ old('cause_directe') }}</textarea>
                    </div>

                    <div class="col-md-12 form-floating-custom">
                        <label>Médecin déclarant certifié</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="fa-solid fa-user-doctor"></i></span>
                            <input type="text" class="form-control bg-light fw-bold" value="Dr. {{ auth()->user()->name }} {{ auth()->user()->prenom }}" disabled>
                        </div>
                    </div>

                    <div class="col-12 form-floating-custom">
                        <label>Observations & Notes médicales complémentaires (facultatif)</label>
                        <textarea name="observation" class="form-control" rows="2" placeholder="Circonstances particulières, antécédents notables, constats médico-légaux éventuels...">{{ old('observation') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Boutons de soumission -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 bg-white rounded-4 shadow-sm border mb-5">
                <a href="{{ route('doctor.declaration.deces.list') }}" class="btn btn-outline-secondary px-4 fw-semibold">
                    <i class="fa-solid fa-xmark me-1"></i> Annuler
                </a>
                <button type="submit" class="btn btn-danger btn-lg px-5 shadow fw-bold">
                    <i class="fa-solid fa-file-circle-check me-2"></i> Valider et Déclarer le Décès
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePatientModeSelect(mode) {
    const secExisting = document.getElementById('sectionExistingPatient');
    const secNew = document.getElementById('sectionNewPatient');

    if (mode === 'existing') {
        secExisting.style.display = 'block';
        secNew.style.display = 'none';
    } else {
        secExisting.style.display = 'none';
        secNew.style.display = 'block';
    }
}

function toggleLieuModeSelect(mode) {
    const secHospital = document.getElementById('sectionLieuHospital');
    const secHors = document.getElementById('sectionLieuHors');

    if (mode === 'hospital') {
        secHospital.style.display = 'block';
        secHors.style.display = 'none';
    } else {
        secHospital.style.display = 'none';
        secHors.style.display = 'block';
        document.getElementById('inputLieuAutre').focus();
    }
}

function searchPatientAjax() {
    const searchType = document.getElementById('searchType').value;
    const searchVal = document.getElementById('patientSearchInput').value.trim();
    const resultsContainer = document.getElementById('searchResults');

    if (!searchVal) {
        alert('Veuillez saisir un terme de recherche.');
        return;
    }

    resultsContainer.style.display = 'block';
    resultsContainer.innerHTML = '<div class="text-center p-3 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i> Recherche en cours...</div>';

    fetch(`{{ route('doctor.declaration.search') }}?searchBy=${searchType}&search=${encodeURIComponent(searchVal)}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.patients && data.patients.length > 0) {
                let html = '<div class="list-group shadow-sm border-0 rounded-3 overflow-hidden" style="max-height: 250px; overflow-y: auto;">';
                data.patients.forEach(patient => {
                    const userName = patient.user ? `${patient.user.name} ${patient.user.prenom || ''}` : 'Inconnu';
                    const code = patient.code_patient || 'N/A';
                    const birth = patient.birth_date || 'N/A';
                    const gender = patient.gender || 'N/A';
                    
                    html += `
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3" onclick="selectPatient(${patient.id}, '${escapeHtml(userName)}', '${escapeHtml(code)}', '${escapeHtml(gender)}', '${escapeHtml(birth)}')">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><i class="fa-solid fa-user me-2 text-primary"></i>${userName}</h6>
                                <div class="text-muted small">
                                    <span class="me-2">DM: <b class="text-primary">${code}</b></span> | 
                                    <span class="me-2">Né(e): <b>${birth}</b></span> | 
                                    <span>Genre: <b class="text-capitalize">${gender}</b></span>
                                </div>
                            </div>
                            <span class="btn btn-sm btn-primary px-3 rounded-pill fw-semibold">Sélectionner</span>
                        </button>
                    `;
                });
                html += '</div>';
                resultsContainer.innerHTML = html;
            } else {
                resultsContainer.innerHTML = '<div class="alert alert-warning mb-0 py-2 rounded-3"><i class="fa-solid fa-circle-info me-2"></i> Aucun patient trouvé correspondant à cette recherche.</div>';
            }
        })
        .catch(err => {
            resultsContainer.innerHTML = '<div class="alert alert-danger mb-0 py-2 rounded-3">Une erreur est survenue lors de la recherche.</div>';
        });
}

function selectPatient(id, name, code, gender, birth) {
    document.getElementById('selectedPatientId').value = id;
    document.getElementById('displayPatientName').textContent = name;
    document.getElementById('displayPatientCode').textContent = code;
    document.getElementById('displayPatientGender').textContent = gender;
    document.getElementById('displayPatientBirth').textContent = birth;

    document.getElementById('selectedPatientCard').style.display = 'block';
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('patientSearchInput').value = '';
}

function clearSelectedPatient() {
    document.getElementById('selectedPatientId').value = '';
    document.getElementById('selectedPatientCard').style.display = 'none';
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// Validation avant envoi
document.getElementById('formDirectDeces').addEventListener('submit', function(e) {
    const patientMode = document.getElementById('patient_mode_select').value;
    if (patientMode === 'existing') {
        const patientId = document.getElementById('selectedPatientId').value;
        if (!patientId) {
            e.preventDefault();
            alert('Veuillez sélectionner un patient existant dans la liste de recherche ou basculer en mode "Défunt non enregistré".');
            return false;
        }
    }

    const lieuMode = document.getElementById('lieu_type_select').value;
    if (lieuMode === 'hors') {
        const lieuAutre = document.getElementById('inputLieuAutre').value.trim();
        if (!lieuAutre) {
            e.preventDefault();
            alert('Veuillez renseigner le lieu du décès hors établissement.');
            document.getElementById('inputLieuAutre').focus();
            return false;
        }
    }
});
</script>
@endsection
