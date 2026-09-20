@extends('layouts.dashboard', ['title' => 'Formulaire pour les premiers soins du patient'])
@section('content')
        @php
            $patient = $consultation->patient ?? ($consultation->admission->patient ?? null);
            $motif = $consultation->admission->motif_consultation ?? ($consultation->observation_infirmiere ?? 'Non spécifié');
            $age = 'N/A';
            if ($patient && $patient->birth_date) {
                try {
                    $agePatient = \Carbon\Carbon::createFromFormat('d/m/Y', $patient->birth_date);
                    $age = $agePatient->diffInYears(\Carbon\Carbon::now()) . ' ans';
                } catch (\Exception $e) {
                    $age = 'N/A';
                }
            }
            $patientServiceName = $consultation->prestationHospital->serviceHospital->service->libelle 
                ?? $consultation->prestationHospital->prestationService->service->libelle 
                ?? $consultation->admission->prestationHospital->serviceHospital->service->libelle 
                ?? $consultation->admission->prestationHospital->prestationService->service->libelle 
                ?? 'Consultation générale';
            if (!isset($isTeleconsultationActive)) {
                $hospitalId = (auth()->check() && auth()->user()->infirmier) ? auth()->user()->infirmier->hospital_id : (auth()->user()->hospital_id ?? $consultation->hospital_id ?? 1);
                $infHospital = (auth()->check() && auth()->user()->infirmier && auth()->user()->infirmier->hospital) 
                    ? auth()->user()->infirmier->hospital 
                    : \App\Models\Hospital::find($hospitalId);
                $isTeleconsultationActive = $infHospital ? (bool)$infHospital->is_teleconsultation_active : false;
            }
            $isCompleted = ($consultation->status == 1);
            $isTeleconsultProgrammed = ($consultation->orientation_infirmier === 'teleconsultation' || (!empty($consultation->desired_date) && !empty($consultation->doctor_id)));
            
            $isEmergencyActive = !$isCompleted && ($consultation->orientation_infirmier === 'urgence' || $consultation->is_urgence == 1) 
                && (!empty($consultation->call_channel) || $consultation->is_call_active || in_array($consultation->call_status, ['calling', 'accepted', 'in_call']))
                && $consultation->status == 0;
            $hasDoctorAccepted = $isEmergencyActive && (!empty($consultation->doctor_id) || in_array($consultation->call_status, ['accepted', 'in_call']));
            $emergencyDocUser = optional(optional($consultation->doctor)->user);
            $emergencyDocName = $emergencyDocUser->name ? ('Dr. ' . trim($emergencyDocUser->name . ' ' . ($emergencyDocUser->prenom ?? ''))) : 'Médecins de garde';
            $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
            $infName = trim((auth()->user()->name ?? '') . ' ' . (auth()->user()->prenom ?? ''));
            $emergencyToken = (!empty($consultation->call_channel) && auth()->check()) ? \App\Services\LiveKitTokenService::generateToken($consultation->call_channel, 'infirmier_' . auth()->user()->id, 'Inf. ' . $infName) : '';
            $patUser = optional(optional($consultation->patient)->user);
            $patientFullName = trim(($patUser->name ?? '') . ' ' . ($patUser->prenom ?? '')) ?: 'Patient';
        @endphp
        <div class="container">
            <form action="{{ route('infirmier.consultation.formulaire.store') }}" method="POST">
                @csrf
                <input type="hidden" name="consultation_id" value="{{ $consultation->id }}" />

                <div class="bt-3">
                    <div class="box bb-3 border-danger pe-95 pb-20 ps-95 pt-20 bg-color">
                        <div class="row">
                            <div class="col-md-2">
                                @if ($patient && $patient->img_url != null)
                                    <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}"
                                        class="rounded-circle" alt="Photo de profil" style="width:128px; height:128px" />
                                @else
                                    @if ($patient && $patient->gender == 'masculin')
                                        <img src="{{ asset('assets/images/avatar/6.png') }}" class="rounded-circle"
                                            alt="Photo de profil" />
                                    @else
                                        <img src="{{ asset('assets/images/avatar/2.png') }}" class="rounded-circle"
                                            alt="Photo de profil" />
                                    @endif
                                @endif
                            </div>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="row mt-20">
                                        <div class="col-md-6 d-flex align-items-center">
                                            <a href="{{ route('infirmier.consultation.today') }}" class="btn btn-sm btn-secondary me-3"><i class="fa-solid fa-arrow-left me-1"></i> Retour</a>
                                            <label class="form-label mb-0">N° Dossier médical | <span class="fw-bold fs-18"><span
                                                        id="dm_patient"
                                                        style="color:red;">{{ $patient->code_patient ?? 'N/A' }}</span></span></label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name" class="form-label"> <b>Nom complet </b> </label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '') }}"
                                                disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="birth_date" class="form-label"> <b>Né(e) le</b></label>
                                            <input type="text" class="form-control" id="birth_date" name="birth_date"
                                                value="{{ $patient->birth_date ?? 'N/A' }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="age" class="form-label"> <b>Age </b></label>
                                            <input type="text" class="form-control" id="age" name="age"
                                                value="{{ $age }}" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="gender" class="form-label"> <b>Sexe </b></label>
                                            <input type="text" class="form-control" id="gender" name="gender"
                                                value="{{ $patient->gender ?? 'N/A' }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label"><b>Résidence Actuelle</b></label>
                                        <input type="text" class="form-control"
                                            value="{{ $patient->residenceActuelle->name ?? 'N/A' }}"
                                            readonly />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><b>Profession</b></label>
                                        <input type="text" class="form-control"
                                            value="{{ $patient->profession ?? '' }}" readonly />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label"><b>Contact</b></label>
                                        <input type="text" class="form-control"
                                            value="{{ $patient->telephone ?? '' }}" readonly />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label"><b>N° CMU</b></label>
                                        <input type="text" class="form-control text-primary font-bold"
                                            value="{{ $patient->num_cmu ?? 'N/A' }}" readonly />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label"><b>No Assurance</b></label>
                                        <input type="text" class="form-control"
                                            value="{{ $patient->no_assurance ?? '' }}" readonly />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label"><b>Motif de la consultation</b></label>
                                        <textarea type="text" class="form-control" name="motif_consultation" id="motif_consultation"
                                            readonly>{{ $motif }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <br />
                    <div class="box bt-3">
                        <div class="box-body">

                            <div class="row">
                                <h4 class="fw-600 mb-10">Constantes physiques du patient </h4>
                                <hr>
                                <div class="row">
                                    @php
                                        $valPoids = $consultation->poids ?: ($consultation->registre->registreConsultationCurative->poids ?? '');
                                        $valTaille = $consultation->taille ?: ($consultation->registre->registreConsultationCurative->taille ?? '');
                                        $valImc = $consultation->imc ?: ($consultation->registre->registreConsultationCurative->imc ?? '');
                                        $valTemp = $consultation->temperature ?: ($consultation->registre->registreConsultationCurative->temperature ?? '');
                                        $valTA = $consultation->tension_arterielle ?: ($consultation->registre->registreConsultationCurative->ta ?? '');
                                        $valPouls = $consultation->pouls ?: ($consultation->registre->registreConsultationCurative->pouls ?? '');
                                    @endphp
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="poids" class="form-label"> <b>Poids(kg)</b></label>
                                            <input type="text" class="form-control" id="poids" name="poids"
                                                value="{{ $valPoids }}" placeholder="Kg" {{ ($isTeleconsultProgrammed || !empty($valPoids)) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="taille" class="form-label"> <b>Taille(cm)</b></label>
                                            <input type="text" class="form-control" id="taille" name="taille"
                                                value="{{ $valTaille }}" placeholder="ex: 162" {{ ($isTeleconsultProgrammed || !empty($valTaille)) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="imc" class="form-label"><b>IMC</b></label>
                                            <input type="text" class="form-control" id="imc" name="imc"
                                                value="{{ $valImc }}" placeholder="Kg/m²" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="temperature" class="form-label"> <b>T(°C)</b></label>
                                            <input type="text" class="form-control" id="temperature" name="temperature"
                                                value="{{ $valTemp }}" placeholder="°C" {{ ($isTeleconsultProgrammed || !empty($valTemp)) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="tension_arterielle" class="form-label"> <b>TA(mmHg)</b></label>
                                            <input type="text"
                                                class="form-control @error('tension_arterielle') is-invalid @enderror"
                                                id="tension_arterielle" name="tension_arterielle" value="{{ $valTA }}"
                                                placeholder="mmHg" {{ ($isTeleconsultProgrammed || !empty($valTA)) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="pouls" class="form-label"> <b>Pouls(batt/mn)</b></label>
                                            <input type="text" class="form-control @error('pouls') is-invalid @enderror"
                                                id="pouls" name="pouls" value="{{ $valPouls }}"
                                                placeholder="batt/mn" {{ ($isTeleconsultProgrammed || !empty($valPouls)) ? 'readonly' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                        </div>
                        @if(!$isTeleconsultationActive && !$isTeleconsultProgrammed)
                        <div class="box-footer bt-3 border-primary">
                            <div class="float-end">
                                <a href="{{ route('infirmier.consultation.today') }}" class="btn btn-warning me-1">
                                    <i class="ti-trash"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSubmitFormulaire">
                                    <i class="ti-save-alt"></i> <span id="btnSubmitText">Valider & Envoyer chez le médecin</span>
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($isTeleconsultationActive && !$isTeleconsultProgrammed)
                    <!-- Carte Dédiée : Actions & Orientation Immédiate (Optionnel) -->
                    <div class="box bt-3 border-info mt-20">
                        <div class="box-header with-border d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h4 class="box-title fw-600 mb-0">
                                <i class="fa-solid fa-arrows-split-up-and-left text-info me-2"></i>
                                Orientation & Actions Spécifiques <small class="text-muted fw-normal fs-13">(Optionnel - Par défaut : Envoi standard chez le médecin)</small>
                            </h4>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btnResetOrientation" style="display:none;">
                                <i class="fa-solid fa-rotate-left me-1"></i> Réinitialiser / Processus standard
                            </button>
                        </div>
                        <div class="box-body">
                            <!-- Radio Buttons / Cartes d'orientation -->
                            <div class="row g-3 mb-20">
                                <div class="col-md-4">
                                    <label class="orientation-option-card d-block p-15 rounded-12 border cursor-pointer text-center h-100 position-relative" for="radio_sortie">
                                        <input type="radio" name="orientation_infirmier" id="radio_sortie" value="sortie" class="position-absolute top-10 end-10">
                                        <div class="icon-circle bg-success-light text-success mx-auto mb-10 fs-24 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                            <i class="fa-solid fa-stethoscope"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1 fs-16">Consultation</h5>
                                        <p class="text-muted fs-12 mb-0">Soins administrés & consultation du patient</p>
                                    </label>
                                </div>

                                <div class="col-md-4">
                                    <label class="orientation-option-card d-block p-15 rounded-12 border cursor-pointer text-center h-100 position-relative" for="radio_teleconsultation">
                                        <input type="radio" name="orientation_infirmier" id="radio_teleconsultation" value="teleconsultation" class="position-absolute top-10 end-10">
                                        <div class="icon-circle bg-primary-light text-primary mx-auto mb-10 fs-24 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                            <i class="fa-solid fa-video"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1 fs-16">Téléconsultation</h5>
                                        <p class="text-muted fs-12 mb-0">Programmer date/heure avec un médecin</p>
                                    </label>
                                </div>

                                <div class="col-md-4">
                                    <label class="orientation-option-card d-block p-15 rounded-12 border cursor-pointer text-center h-100 position-relative" for="radio_urgence">
                                        <input type="radio" name="orientation_infirmier" id="radio_urgence" value="urgence" class="position-absolute top-10 end-10">
                                        <div class="icon-circle bg-danger-light text-danger mx-auto mb-10 fs-24 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                            <i class="fa-solid fa-phone-volume"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1 fs-16">Appel d'urgence</h5>
                                        <p class="text-muted fs-12 mb-0">Signalement & appel immédiat d'un médecin</p>
                                    </label>
                                </div>
                            </div>

                            <!-- Bloc Déployé : 1. CONSULTATION -->
                            <div id="bloc_sortie" class="p-20 rounded-12 bg-light border mb-15" style="display: none;">
                                <h5 class="fw-bold text-success mb-15"><i class="fa-solid fa-hand-holding-medical me-2"></i> Renseignements de Consultation & Soins Administrés</h5>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold">Type de soins administrés <span class="text-danger">*</span></label>
                                            <select class="form-select" name="type_soins_infirmier" id="type_soins_infirmier">
                                                <option value="" selected disabled>Choisir le type de soins</option>
                                                <option value="Pansement simple">Pansement simple</option>
                                                <option value="Pansement complexe">Pansement complexe / Escarre</option>
                                                <option value="Injection IM / IV / SC">Injection (IM / IV / SC)</option>
                                                <option value="Perfusion / Réhydratation">Perfusion / Réhydratation</option>
                                                <option value="Soins d'urgence simples">Soins d'urgence simples</option>
                                                <option value="Prise de constantes seule">Prise de constantes seule</option>
                                                <option value="Nettoyage et désinfection de plaie">Nettoyage et désinfection de plaie</option>
                                                <option value="Autre intervention">Autre intervention</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold">Observations & Détails de l'intervention</label>
                                            <textarea class="form-control" name="observation_soins" id="observation_soins" rows="2" placeholder="Précisez les gestes effectués, matériel utilisé, état du patient à la sortie..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bloc Déployé : 2. TÉLÉCONSULTATION -->
                            @php
                                $currentHosp = \App\Models\Hospital::find($consultation->hospital_id ?? auth()->user()->infirmier->hospital_id ?? auth()->user()->hospital_id ?? 1);
                                $currentHospName = $currentHosp ? ($currentHosp->label ?: ($currentHosp->nom_direction_generale ?: $currentHosp->reference)) : 'Hôpital';
                                $currentHospDistrict = $currentHosp ? ($currentHosp->district_sanitaire ?: optional($currentHosp->commune)->libelle ?: 'Côte d\'Ivoire') : '';
                                $currentHospRef = $currentHosp ? ($currentHosp->reference ?: 'HO-' . str_pad($currentHosp->id, 4, '0', STR_PAD_LEFT)) : '';
                            @endphp
                            <div id="bloc_teleconsultation" class="p-20 rounded-16 bg-white border border-primary-light shadow-sm mb-20" style="display: none;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-20 pb-15 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-12 bg-primary-light text-primary d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; font-size: 20px;">
                                            <i class="fa-solid fa-video"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-dark mb-0 fs-16">Programmation de la Téléconsultation</h5>
                                            <p class="text-muted fs-12 mb-0">Sélectionnez l'établissement et le médecin disponible pour la consultation à distance.</p>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary-light text-primary border border-primary-light rounded-pill px-15 py-8 fs-12 fw-semibold">
                                        <i class="fa-solid fa-stethoscope me-1"></i> Spécialité requise : <strong>{{ $patientServiceName }}</strong>
                                    </span>
                                </div>

                                <!-- Carte Hôpital Sélectionné (Sur une seule ligne) -->
                                <input type="hidden" name="teleconsultation_hospital_id" id="teleconsultation_hospital_id" value="{{ $currentHosp->id ?? '' }}">
                                <div class="hospital-chosen-card p-12 px-18 rounded-14 border d-flex align-items-center justify-content-between flex-wrap gap-3 mb-20">
                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                        <div class="hospital-chosen-icon rounded-10 d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px;">
                                            <i class="fa-solid fa-hospital-user fs-18"></i>
                                        </div>
                                        <span class="badge bg-primary text-white fs-11 fw-semibold px-10 py-5 rounded-pill shadow-xs">
                                            <i class="fa-solid fa-circle-check me-1"></i> Établissement
                                        </span>
                                        <h5 class="fw-bold text-dark mb-0 fs-15" id="selectedHospitalName">{{ $currentHospName }}</h5>
                                        <span class="text-muted">&bull;</span>
                                        <span class="text-muted fs-13 d-flex align-items-center gap-1" id="selectedHospitalMeta">
                                            <i class="fa-solid fa-location-dot text-danger"></i> {{ $currentHospDistrict }} <span class="text-muted ms-1">(Réf: <strong>{{ $currentHospRef }}</strong>)</span>
                                        </span>
                                        <span class="badge bg-success-light text-success fs-12 fw-bold px-10 py-5 rounded-pill" id="selectedHospitalDocBadge">
                                            <i class="fa-solid fa-user-doctor me-1"></i> Médecins disponibles
                                        </span>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary btn-sm px-16 py-7 rounded-pill fw-bold shadow-xs d-flex align-items-center gap-2 flex-shrink-0" id="btnChangeHospital">
                                            <i class="fa-solid fa-arrows-rotate"></i>
                                            <span>Changer d'hôpital</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold text-dark">Date de la téléconsultation <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control rounded-10" name="desired_date" id="desired_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold text-dark">Heure / Créneau souhaité</label>
                                            <input type="time" class="form-control rounded-10" name="desired_time" id="desired_time">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold text-dark">Médecin disponible <span class="text-danger">*</span></label>
                                            <select class="form-select rounded-10" name="doctor_id" id="doctor_id">
                                                <option value="" selected disabled>Chargement des médecins disponibles...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12" id="teleconsultation_doctors_helper">
                                        <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i> Seuls les médecins généralistes et spécialistes de <strong>{{ $patientServiceName }}</strong> en service sur cette date/heure sont listés.</small>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-semibold text-dark">Motif / Note pour la téléconsultation</label>
                                            <textarea class="form-control rounded-10" name="observation_infirmiere" id="observation_teleconsultation" rows="2" placeholder="Note clinique transmise au médecin pour préparer la téléconsultation..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                                $isEmergencyActive = ($consultation->orientation_infirmier === 'urgence' || $consultation->is_urgence == 1) 
                                    && (!empty($consultation->call_channel) || $consultation->is_call_active || in_array($consultation->call_status, ['calling', 'accepted', 'in_call', 'ended']))
                                    && $consultation->status == 0;
                                $hasDoctorAccepted = $isEmergencyActive && (!empty($consultation->doctor_id) || in_array($consultation->call_status, ['accepted', 'in_call', 'ended']));
                                $emergencyDocUser = optional(optional($consultation->doctor)->user);
                                $emergencyDocName = $emergencyDocUser->name ? ('Dr. ' . trim($emergencyDocUser->name . ' ' . ($emergencyDocUser->prenom ?? ''))) : 'Médecins de garde';
                                $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
                                $infName = trim((auth()->user()->name ?? '') . ' ' . (auth()->user()->prenom ?? ''));
                                $emergencyToken = (!empty($consultation->call_channel) && auth()->check()) ? \App\Services\LiveKitTokenService::generateToken($consultation->call_channel, 'infirmier_' . auth()->user()->id, 'Inf. ' . $infName) : '';
                                $patUser = optional(optional($consultation->patient)->user);
                                $patientFullName = trim(($patUser->name ?? '') . ' ' . ($patUser->prenom ?? '')) ?: 'Patient';
                            @endphp

                            <!-- Bloc Déployé : 3. URGENCE -->
                            <div id="bloc_urgence" class="p-20 rounded-14 bg-danger-light border border-danger mb-15" style="{{ ($consultation->orientation_infirmier === 'urgence' || $isEmergencyActive) ? 'display: block;' : 'display: none;' }}">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <h5 class="fw-bold text-danger mb-1 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-phone-volume"></i>
                                            <span>Appel d'Urgence Signalé</span>
                                            @if($isEmergencyActive)
                                                 @if($hasDoctorAccepted)
                                                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1 fs-11 fw-semibold" id="emergencyStatusBadge">
                                                        <i class="fa-solid fa-circle-check me-1"></i> {{ $emergencyDocName }} connecté
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 fs-11 fw-semibold" id="emergencyStatusBadge">
                                                        <span class="spinner-grow spinner-grow-sm me-1" style="width: 7px; height: 7px;"></span> Appel en cours de sonnerie...
                                                    </span>
                                                @endif
                                            @endif
                                        </h5>
                                        <p class="text-muted mb-1 fs-13">Le patient nécessite une prise en charge médicale prioritaire immédiate.</p>
                                        <div id="urgenceConstantesAlert" class="fs-12 fw-semibold mt-1">
                                            <!-- Message d'état des constantes généré dynamiquement en JS -->
                                        </div>
                                    </div>
                                    <div id="emergencyCallBtnContainer">
                                        @if($isEmergencyActive)
                                            <button type="button" class="btn text-white btn-lg px-25 py-12 rounded-12 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnRejoindreAppelDirect" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;" onclick="openInfirmierVideoCall({{ $consultation->id }}, '{{ $emergencyToken }}', '{{ $livekitUrl }}', '{{ addslashes($emergencyDocName) }}', '{{ addslashes($patientFullName) }}', '{{ $consultation->call_channel }}')">
                                                <i class="fa-solid fa-video fs-18"></i>
                                                <span>Rejoindre l'appel</span>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-lg px-25 py-12 rounded-12 fw-bold shadow-sm" id="btnAppelerMedecin" disabled>
                                                <i class="fa-solid fa-phone-volume me-2 fs-18"></i> Appeler un médecin
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer bt-3 border-primary">
                            <div class="float-end">
                                <a href="{{ route('infirmier.consultation.today') }}" class="btn btn-warning me-1">
                                    <i class="ti-trash"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary" id="btnSubmitFormulaire">
                                    <i class="ti-save-alt"></i> <span id="btnSubmitText">Valider & Envoyer chez le médecin</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($isTeleconsultProgrammed)
                    <!-- Carte Récapitulative Téléconsultation Programmée (Lecture Seule) -->
                    <div class="box bt-3 border-teal mt-20 shadow-sm" style="border-top-color: #0d9488 !important;">
                        <div class="box-header with-border bg-white p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-12 bg-teal-subtle text-teal d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px; background-color: #ccfbf1; color: #0d9488; font-size: 22px;">
                                    <i class="fa-solid fa-headset"></i>
                                </div>
                                <div>
                                    <h4 class="box-title fw-bold text-dark mb-0">Téléconsultation Programmée</h4>
                                    <span class="text-muted fs-12">Le dossier est verrouillé et prêt pour le rendez-vous vidéo.</span>
                                </div>
                            </div>
                            @if($isCompleted)
                                <span class="badge bg-secondary text-white border fw-bold px-3 py-1.5 rounded-pill fs-12">
                                    <i class="fa-solid fa-check-double me-1"></i> Téléconsultation terminée / Appel clos
                                </span>
                            @else
                                <span class="badge bg-success-light text-success border border-success fw-bold px-3 py-1.5 rounded-pill fs-12">
                                    <i class="fa-solid fa-calendar-check me-1"></i> Programmation validée
                                </span>
                            @endif
                        </div>
                        <div class="box-body p-20 bg-light-subtle">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="p-15 rounded-12 bg-white border h-100">
                                        <small class="text-muted text-uppercase fw-semibold fs-11 d-block mb-1">Date & Heure</small>
                                        <div class="fw-bold text-dark fs-14">
                                            <i class="fa-regular fa-calendar text-primary me-1"></i>
                                            {{ $consultation->desired_date ? date('d/m/Y', strtotime($consultation->desired_date)) : 'N/A' }}
                                            @if($consultation->desired_time)
                                                <span class="d-block text-primary fs-13 mt-1"><i class="fa-regular fa-clock me-1"></i> {{ $consultation->desired_time }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-15 rounded-12 bg-white border h-100">
                                        <small class="text-muted text-uppercase fw-semibold fs-11 d-block mb-1">Médecin Assigné</small>
                                        <div class="fw-bold text-dark fs-14">
                                            <i class="fa-solid fa-user-doctor text-success me-1"></i>
                                            Dr. {{ optional(optional($consultation->doctor)->user)->name ?? 'Médecin' }} {{ optional(optional($consultation->doctor)->user)->prenom ?? '' }}
                                        </div>
                                        <small class="text-muted fs-12 mt-1 d-block">
                                            <i class="fa-solid fa-hospital me-1"></i>
                                            {{ optional(optional($consultation->doctor)->hospital)->nom ?? optional(optional($consultation->doctor)->hospital)->label ?? optional($consultation->teleconsultationHospital)->nom ?? 'Hôpital destinataire' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="p-15 rounded-12 bg-white border h-100">
                                        <small class="text-muted text-uppercase fw-semibold fs-11 d-block mb-1">Note / Observation</small>
                                        <div class="fs-13 text-dark">
                                            {{ $consultation->observation_infirmiere ?: 'Aucune note spécifique transmise.' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer bg-white p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <a href="{{ route('infirmier.consultation.teleconsultations') }}" class="btn btn-outline-secondary rounded-10 px-20">
                                <i class="fa-solid fa-arrow-left me-1"></i> Retour aux téléconsultations
                            </a>
                            @php
                                $scheduledToken = '';
                                $livekitWsUrl = config('services.livekit.ws_url', env('LIVEKIT_WS_URL', 'wss://sante-13e5q71k.livekit.cloud'));
                                if (!empty($consultation->call_channel) && auth()->check()) {
                                    $nurseUser = auth()->user();
                                    $nurseName = trim(($nurseUser->name ?? '') . ' ' . ($nurseUser->prenom ?? ''));
                                    $scheduledToken = \App\Services\LiveKitTokenService::generateToken($consultation->call_channel, 'infirmier_' . $nurseUser->id, 'Inf. ' . $nurseName);
                                }
                                $docFullName = 'Dr. ' . trim((optional(optional($consultation->doctor)->user)->name ?? '') . ' ' . (optional(optional($consultation->doctor)->user)->prenom ?? ''));
                                $patFullName = trim((optional(optional($consultation->patient)->user)->name ?? '') . ' ' . (optional(optional($consultation->patient)->user)->prenom ?? ''));
                            @endphp
                            @if($isCompleted)
                                <span class="badge bg-light text-muted border px-3 py-2 fs-13 fw-semibold">
                                    <i class="fa-solid fa-phone-slash text-danger me-1"></i> Appel vidéo clôturé par le médecin
                                </span>
                            @else
                                <button type="button" class="btn text-white btn-lg px-25 py-10 rounded-12 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;" onclick="openInfirmierVideoCall({{ $consultation->id }}, '{{ $scheduledToken }}', '{{ $livekitWsUrl }}', '{{ addslashes($docFullName) }}', '{{ addslashes($patFullName) }}', '{{ $consultation->call_channel }}')">
                                    <i class="fa-solid fa-video fs-16"></i>
                                    <span>Lancer / Rejoindre l'appel vidéo</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    @endif
                    <br />
                </div>
            </form>
        </div>

    @if($isTeleconsultationActive && !$isTeleconsultProgrammed)
    <!-- Modal Pop-up : Choix de l'Hôpital avec disponibilité des Médecins en direct (Design Simple & Épuré) -->
    <div class="modal fade" id="modalSelectHospitalTeleconsultation" tabindex="-1" aria-labelledby="modalSelectHospitalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-16 border shadow-lg overflow-hidden bg-white">
                <!-- En-tête Simple & Clair -->
                <div class="modal-header bg-white border-bottom p-20 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-12 bg-light text-primary d-flex align-items-center justify-content-center border" style="width: 42px; height: 42px; font-size: 18px;">
                            <i class="fa-solid fa-hospital"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalSelectHospitalTitle">
                                Choisir un Hôpital pour la Téléconsultation
                            </h5>
                            <p class="text-muted fs-12 mb-0">Consultez la liste des établissements et le nombre de médecins disponibles</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>

                <div class="modal-body p-20 bg-light">
                    <!-- Recherche avec bouton Actualiser sur la même ligne -->
                    <div class="bg-white p-12 rounded-12 border shadow-xs mb-15">
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group flex-grow-1">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" id="searchHospitalInput" class="form-control border-start-0" placeholder="Rechercher par nom d'hôpital, commune, district, référence...">
                            </div>
                            <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1 px-15 py-8 rounded-8 flex-shrink-0" id="btnRefreshHospitals" title="Actualiser la liste">
                                <i class="fa-solid fa-rotate"></i> Actualiser
                            </button>
                        </div>
                    </div>

                    <!-- Loader -->
                    <div id="hospitalListLoading" class="text-center py-35">
                        <div class="spinner-border text-primary mb-10" style="width: 2.5rem; height: 2.5rem;" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="text-muted fs-13 mb-0">Chargement des établissements et des disponibilités...</p>
                    </div>

                    <!-- Liste des Hôpitaux Dynamique -->
                    <div id="hospitalListContainer" class="row g-3" style="display: none;">
                        <!-- Les cartes d'hôpitaux sont injectées ici via JavaScript -->
                    </div>

                    <!-- État Vide -->
                    <div id="hospitalListEmpty" class="text-center py-35 bg-white rounded-12 border p-20" style="display: none;">
                        <i class="fa-solid fa-hospital-slash text-muted fs-32 mb-10 d-block"></i>
                        <h6 class="fw-bold text-dark mb-1">Aucun établissement trouvé</h6>
                        <p class="text-muted fs-12 mb-10">Aucun résultat ne correspond à votre recherche.</p>
                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-15" onclick="$('#searchHospitalInput').val('').trigger('input');">
                            Réinitialiser la recherche
                        </button>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-15 d-flex align-items-center justify-content-between">
                    <small class="text-muted"><i class="fa-solid fa-circle-check text-success me-1"></i> Cliquez sur un hôpital pour le sélectionner</small>
                    <button type="button" class="btn btn-secondary btn-sm px-20 rounded-pill" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .orientation-option-card {
            background: #ffffff;
            border: 2px solid #e2e8f0 !important;
            transition: all 0.25s ease-in-out;
        }
        .orientation-option-card:hover {
            border-color: #94a3b8 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .orientation-option-card.active-sortie {
            border-color: #22c55e !important;
            background: #f0fdf4 !important;
        }
        .orientation-option-card.active-teleconsultation {
            border-color: #3b82f6 !important;
            background: #eff6ff !important;
        }
        .orientation-option-card.active-urgence {
            border-color: #ef4444 !important;
            background: #fef2f2 !important;
        }
        /* Carte de l'hôpital sélectionné dans le formulaire */
        .hospital-chosen-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
            border: 1.5px solid #dbeafe !important;
            border-left: 5px solid #005AEC !important;
            box-shadow: 0 4px 14px rgba(0, 90, 236, 0.06);
            transition: all 0.25s ease-in-out;
        }
        .hospital-chosen-card:hover {
            box-shadow: 0 6px 18px rgba(0, 90, 236, 0.1);
            border-color: #bfdbfe !important;
        }
        .hospital-chosen-icon {
            width: 52px;
            height: 52px;
            background: #eff6ff;
            color: #005AEC;
            border: 1.5px solid #bfdbfe;
            flex-shrink: 0;
        }
        /* Cartes d'hôpitaux dans le Pop-up */
        .hospital-card-item {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 16px;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            position: relative;
        }
        .hospital-card-item:hover {
            border-color: #005AEC;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 90, 236, 0.08);
        }
        .hospital-card-item.selected {
            border-color: #005AEC !important;
            background: #f4f8ff !important;
            box-shadow: 0 0 0 2px rgba(0, 90, 236, 0.25), 0 4px 12px rgba(0, 90, 236, 0.1);
        }
    </style>

    <script>
    // Récupérer les éléments des champs Poids, Taille et IMC
    const poidsInput = document.getElementById('poids');
    const tailleInput = document.getElementById('taille');
    const imcInput = document.getElementById('imc');

    // Fonction pour calculer l'IMC
    function calculerIMC() {
        const poids = parseFloat(poidsInput.value);
        const taille = parseFloat(tailleInput.value) / 100; // Convertir la taille en mètres

        if (poids > 0 && taille > 0) {
            const imc = (poids / (taille * taille)).toFixed(2);
            imcInput.value = imc;
        } else {
            imcInput.value = '';
        }
    }

    if (poidsInput) poidsInput.addEventListener('input', calculerIMC);
    if (tailleInput) tailleInput.addEventListener('input', calculerIMC);

    @if($isTeleconsultationActive && !$isTeleconsultProgrammed)
    let hospitalsDataCache = [];
    let currentSelectedHospitalId = $('#teleconsultation_hospital_id').val() || null;

    // Charger la liste des hôpitaux et les compteurs de médecins disponibles
    function loadTeleconsultationHospitals(autoOpenModal = false) {
        var consultationId = "{{ $consultation->id }}";
        var dateVal = $('#desired_date').val() || "{{ date('Y-m-d') }}";
        var timeVal = $('#desired_time').val() || '';

        $('#hospitalListLoading').show();
        $('#hospitalListContainer').hide();
        $('#hospitalListEmpty').hide();

        $.ajax({
            url: "{{ route('infirmier.consultation.teleconsultation_hospitals') }}",
            method: 'GET',
            data: {
                consultation_id: consultationId,
                date: dateVal,
                time: timeVal
            },
            success: function (response) {
                $('#hospitalListLoading').hide();
                if (response.status === 'success' && response.hospitals && response.hospitals.length > 0) {
                    hospitalsDataCache = response.hospitals;

                    if (!currentSelectedHospitalId) {
                        currentSelectedHospitalId = response.current_hospital_id || response.hospitals[0].id;
                        $('#teleconsultation_hospital_id').val(currentSelectedHospitalId);
                    }

                    renderHospitalCards(hospitalsDataCache);

                    // Mettre à jour l'en-tête de l'hôpital sélectionné
                    updateSelectedHospitalHeader(currentSelectedHospitalId);

                    if (autoOpenModal) {
                        var modalEl = document.getElementById('modalSelectHospitalTeleconsultation');
                        if (modalEl) {
                            var modalObj = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modalObj.show();
                        }
                    }
                } else {
                    $('#hospitalListEmpty').show();
                }
            },
            error: function () {
                $('#hospitalListLoading').hide();
                $('#hospitalListEmpty').show();
            }
        });
    }

    // Afficher les cartes des hôpitaux dans le modal
    function renderHospitalCards(hospitalsList) {
        var container = $('#hospitalListContainer');
        container.empty();

        if (!hospitalsList || hospitalsList.length === 0) {
            $('#hospitalListEmpty').show();
            container.hide();
            return;
        }

        $('#hospitalListEmpty').hide();
        container.show();

        hospitalsList.forEach(function (h) {
            var isSelected = (String(h.id) === String(currentSelectedHospitalId));
            var selectedClass = isSelected ? 'selected' : '';
            var hasDoctors = (parseInt(h.available_doctors_count) || 0) > 0;

            var doctorBadge = '';
            if (hasDoctors) {
                doctorBadge = `<span class="badge bg-success-light text-success fw-bold px-10 py-6 rounded-pill fs-12">
                    <i class="fa-solid fa-user-doctor me-1"></i> ${h.available_doctors_count} médecin(s) disponible(s)
                </span>`;
            } else {
                doctorBadge = `<span class="badge bg-light text-muted fw-semibold px-10 py-6 rounded-pill fs-12">
                    <i class="fa-solid fa-clock me-1"></i> 0 médecin en service
                </span>`;
            }

            var cardHtml = `
                <div class="col-md-6 hospital-card-wrapper" data-name="${(h.name || '').toLowerCase()}" data-district="${(h.district || '').toLowerCase()}" data-ref="${(h.reference || '').toLowerCase()}">
                    <div class="hospital-card-item h-100 ${selectedClass}" onclick="selectHospitalFromModal(${h.id})">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-10">
                            <div>
                                <h6 class="fw-bold text-dark mb-1 fs-15">${h.name}</h6>
                                <div class="text-muted fs-12">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i>${h.district} &bull; Réf: <strong>${h.reference}</strong>
                                </div>
                            </div>
                            ${isSelected ? '<span class="badge bg-success text-white rounded-circle p-6 fs-11"><i class="fa-solid fa-check"></i></span>' : ''}
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-12 pt-10 border-top">
                            <div>${doctorBadge}</div>
                            <button type="button" class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-outline-primary'} rounded-pill px-15 fw-semibold fs-12">
                                ${isSelected ? '<i class="fa-solid fa-check me-1"></i> Sélectionné' : 'Sélectionner'}
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(cardHtml);
        });
    }

    // Filtrer la liste des hôpitaux dans le modal
    $('#searchHospitalInput').on('input', function () {
        var query = $(this).val().toLowerCase().trim();
        if (!query) {
            $('.hospital-card-wrapper').show();
            $('#hospitalListEmpty').hide();
            $('#hospitalListContainer').show();
            return;
        }

        var matchCount = 0;
        $('.hospital-card-wrapper').each(function () {
            var name = $(this).data('name') || '';
            var district = $(this).data('district') || '';
            var ref = $(this).data('ref') || '';

            if (name.includes(query) || district.includes(query) || ref.includes(query)) {
                $(this).show();
                matchCount++;
            } else {
                $(this).hide();
            }
        });

        if (matchCount === 0) {
            $('#hospitalListEmpty').show();
            $('#hospitalListContainer').hide();
        } else {
            $('#hospitalListEmpty').hide();
            $('#hospitalListContainer').show();
        }
    });

    $('#btnRefreshHospitals').on('click', function () {
        loadTeleconsultationHospitals(false);
    });

    // Sélectionner un hôpital depuis le modal
    window.selectHospitalFromModal = function (hospitalId) {
        currentSelectedHospitalId = hospitalId;
        $('#teleconsultation_hospital_id').val(hospitalId);

        updateSelectedHospitalHeader(hospitalId);

        // Fermer le modal
        var modalEl = document.getElementById('modalSelectHospitalTeleconsultation');
        if (modalEl) {
            var modalObj = bootstrap.Modal.getInstance(modalEl);
            if (modalObj) modalObj.hide();
        }

        // Recharger les médecins pour cet hôpital spécifique
        loadTeleconsultationDoctors();
    };

    function updateSelectedHospitalHeader(hospitalId) {
        if (!hospitalsDataCache || hospitalsDataCache.length === 0) return;
        var found = hospitalsDataCache.find(h => String(h.id) === String(hospitalId));
        if (found) {
            $('#selectedHospitalName').text(found.name);
            $('#selectedHospitalMeta').html('<i class="fa-solid fa-location-dot text-danger"></i> ' + found.district + ' <span class="text-muted ms-1">(Réf: <strong>' + found.reference + '</strong>)</span>');

            var docCount = parseInt(found.available_doctors_count) || 0;
            if (docCount > 0) {
                $('#selectedHospitalDocBadge').html('<i class="fa-solid fa-user-doctor me-1"></i> ' + docCount + ' médecin(s) disponible(s)');
                $('#selectedHospitalDocBadge').removeClass('bg-secondary-light text-muted').addClass('bg-success-light text-success');
            } else {
                $('#selectedHospitalDocBadge').html('<i class="fa-solid fa-clock me-1"></i> 0 médecin en service');
                $('#selectedHospitalDocBadge').removeClass('bg-success-light text-success').addClass('bg-secondary-light text-muted');
            }
        }
    }

    // Bouton de changement d'hôpital
    $('#btnChangeHospital').on('click', function () {
        loadTeleconsultationHospitals(true);
    });

    // Fonction de chargement dynamique des médecins éligibles pour téléconsultation
    function loadTeleconsultationDoctors() {
        var dateVal = $('#desired_date').val();
        var timeVal = $('#desired_time').val();
        var hospitalIdVal = $('#teleconsultation_hospital_id').val() || currentSelectedHospitalId;
        var consultationId = "{{ $consultation->id }}";
        var doctorSelect = $('#doctor_id');
        var helperContainer = $('#teleconsultation_doctors_helper');

        if (!dateVal) {
            doctorSelect.empty().append('<option value="" selected disabled>-- Veuillez sélectionner une date --</option>');
            helperContainer.html('<small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i> Sélectionnez une date pour afficher les médecins disponibles.</small>');
            return;
        }

        doctorSelect.empty().append('<option value="" selected disabled>Recherche des médecins disponibles...</option>');
        helperContainer.html('<small class="text-info"><i class="fa-solid fa-spinner fa-spin me-1"></i> Recherche des médecins généralistes et spécialistes en service...</small>');

        $.ajax({
            url: "{{ route('infirmier.consultation.teleconsultation_doctors') }}",
            method: 'GET',
            data: {
                consultation_id: consultationId,
                date: dateVal,
                time: timeVal,
                hospital_id: hospitalIdVal
            },
            success: function (response) {
                doctorSelect.empty();
                var serviceLabel = response.service_name || '{{ $patientServiceName }}';
                if (response.doctors && response.doctors.length > 0) {
                    doctorSelect.append('<option value="" selected disabled>-- Choisir un médecin (' + response.doctors.length + ' disponible(s)) --</option>');
                    $.each(response.doctors, function (key, doc) {
                        doctorSelect.append('<option value="' + doc.id + '">' + doc.text + '</option>');
                    });
                    helperContainer.html('<small class="text-success"><i class="fa-solid fa-circle-check me-1"></i> ' + response.doctors.length + ' médecin(s) disponible(s) (Généraliste ou Spécialiste <strong>' + serviceLabel + '</strong>) sur ce créneau.</small>');
                } else {
                    doctorSelect.append('<option value="" selected disabled>⚠️ Aucun médecin disponible sur ce créneau</option>');
                    helperContainer.html('<small class="text-danger fw-semibold"><i class="fa-solid fa-triangle-exclamation me-1"></i> Aucun médecin généraliste ou spécialiste <strong>' + serviceLabel + '</strong> n\'est en service sur cette date/heure. Veuillez choisir une autre date, heure ou établissement.</small>');
                }
            },
            error: function () {
                doctorSelect.empty().append('<option value="" selected disabled>Erreur lors du chargement des médecins</option>');
                helperContainer.html('<small class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Impossible de récupérer la liste des médecins.</small>');
            }
        });
    }

    $('#desired_date, #desired_time').on('change input', function () {
        loadTeleconsultationDoctors();
    });
    @endif

    // Gestion de l'orientation spécifique
    $('input[name="orientation_infirmier"]').on('change', function () {
        var selected = $(this).val();
        $('#bloc_sortie').hide();
        $('#bloc_teleconsultation').hide();
        $('#bloc_urgence').hide();
        $('.orientation-option-card').removeClass('active-sortie active-teleconsultation active-urgence');
        $('#btnResetOrientation').show();

        if (selected === 'sortie') {
            $('#bloc_sortie').slideDown();
            $(this).closest('.orientation-option-card').addClass('active-sortie');
            $('#btnSubmitFormulaire').show().removeClass('btn-primary btn-danger').addClass('btn-success');
            $('#btnSubmitText').text('Enregistrer les soins & Valider la consultation');
        } else if (selected === 'teleconsultation') {
            $('#bloc_teleconsultation').slideDown();
            $(this).closest('.orientation-option-card').addClass('active-teleconsultation');
            $('#btnSubmitFormulaire').show().removeClass('btn-success btn-danger').addClass('btn-primary');
            $('#btnSubmitText').text('Programmer la téléconsultation');

            // Ouvrir directement le pop-up de choix de l'hôpital avec le nombre de médecins disponibles
            loadTeleconsultationHospitals(true);
            loadTeleconsultationDoctors();
        } else if (selected === 'urgence') {
            $('#bloc_urgence').slideDown();
            $(this).closest('.orientation-option-card').addClass('active-urgence');
            // Masquer le bouton de soumission classique car l'appel direct s'en charge
            $('#btnSubmitFormulaire').hide();
            checkEmergencyCallReadiness();
        }
    });

    $('#btnResetOrientation').on('click', function () {
        $('input[name="orientation_infirmier"]').prop('checked', false);
        $('.orientation-option-card').removeClass('active-sortie active-teleconsultation active-urgence');
        $('#bloc_sortie').slideUp();
        $('#bloc_teleconsultation').slideUp();
        $('#bloc_urgence').slideUp();
        $(this).hide();
        $('#btnSubmitFormulaire').show().removeClass('btn-success btn-danger').addClass('btn-primary');
        $('#btnSubmitText').text('Valider & Envoyer chez le médecin');
    });

    // Fonction de vérification de complétude des constantes physiques pour l'urgence
    function checkEmergencyCallReadiness() {
        var poids = $.trim($('#poids').val());
        var taille = $.trim($('#taille').val());
        var temp = $.trim($('#temperature').val());
        var ta = $.trim($('#tension_arterielle').val());
        var pouls = $.trim($('#pouls').val());

        var missing = [];
        if (!poids) missing.push('Poids');
        if (!taille) missing.push('Taille');
        if (!temp) missing.push('Température');
        if (!ta) missing.push('TA');
        if (!pouls) missing.push('Pouls');

        var isReady = (missing.length === 0);

        if (isReady) {
            $('#btnAppelerMedecin')
                .prop('disabled', false)
                .css('cursor', 'pointer')
                .removeAttr('title');
            $('#urgenceConstantesAlert').html(
                '<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Toutes les constantes physiques sont renseignées. L\'appel d\'urgence est prêt.</span>'
            );
        } else {
            $('#btnAppelerMedecin')
                .prop('disabled', true)
                .css('cursor', 'not-allowed')
                .attr('title', 'Veuillez renseigner toutes les constantes : ' + missing.join(', '));
            $('#urgenceConstantesAlert').html(
                '<span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Renseignez toutes les constantes pour activer l\'appel : <strong>' + missing.join(', ') + '</strong></span>'
            );
        }

        return isReady;
    }

    // Écouter en temps réel la saisie des constantes pour activer le bouton
    $('#poids, #taille, #temperature, #tension_arterielle, #pouls').on('input change keyup', function () {
        checkEmergencyCallReadiness();
    });

    @if($isTeleconsultationActive && !$isTeleconsultProgrammed)
    // Vérification initiale au chargement de la page
    checkEmergencyCallReadiness();

    @if($isEmergencyActive)
        $('input[name="orientation_infirmier"][value="urgence"]').prop('checked', true);
        $('#bloc_urgence').show();
        $('label[for="radio_urgence"]').addClass('active-urgence');
        $('#btnResetOrientation').show();
        $('#btnSubmitFormulaire').hide();
    @endif

    // Fonction pour lancer ou rejoindre directement l'appel d'urgence
    window.launchEmergencyCall = function() {
        if (!checkEmergencyCallReadiness()) {
            Swal.fire({
                title: "Constantes physiques requises",
                text: "Veuillez renseigner l'ensemble des constantes physiques (Poids, Taille, Température, TA, Pouls) avant de lancer l'appel.",
                icon: "warning",
                confirmButtonColor: '#ef4444'
            });
            return;
        }

        var consultationId = "{{ $consultation->id }}";
        var btn = $('#btnAppelerMedecin');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Enregistrement des constantes & Appel en cours...');

        $.ajax({
            url: "{{ route('infirmier.consultation.teleconsultation.emergency_call', $consultation->id) }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                poids: $('#poids').val(),
                taille: $('#taille').val(),
                imc: $('#imc').val(),
                temperature: $('#temperature').val(),
                tension_arterielle: $('#tension_arterielle').val(),
                pouls: $('#pouls').val()
            },
            success: function (res) {
                if (res.status === 'success') {
                    var docTitle = res.doctors_alerted_count > 0 
                        ? 'Médecins alertés (' + res.doctors_alerted_count + ')'
                        : 'Médecins de garde';

                    // Remplacer immédiatement le bouton par 'Rejoindre l'appel'
                    $('#emergencyCallBtnContainer').html(`
                        <button type="button" class="btn text-white btn-lg px-25 py-12 rounded-12 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnRejoindreAppelDirect" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;" onclick="openInfirmierVideoCall(${res.consultation_id}, '${res.token}', '${res.livekit_url}', '${docTitle}', '${res.patient_name}', '${res.channel}')">
                            <i class="fa-solid fa-video fs-18"></i>
                            <span>Rejoindre l'appel</span>
                        </button>
                    `);

                    // Ouvrir immédiatement la modale vidéo
                    if (typeof openInfirmierVideoCall === 'function') {
                        openInfirmierVideoCall(
                            res.consultation_id,
                            res.token,
                            res.livekit_url,
                            docTitle,
                            res.patient_name,
                            res.channel
                        );
                    } else {
                        // Si le script de la modale n'est pas encore prêt, retenter après 200ms
                        setTimeout(() => {
                            if (typeof openInfirmierVideoCall === 'function') {
                                openInfirmierVideoCall(
                                    res.consultation_id,
                                    res.token,
                                    res.livekit_url,
                                    docTitle,
                                    res.patient_name,
                                    res.channel
                                );
                            }
                        }, 200);
                    }
                } else {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-phone-volume me-2 fs-18"></i> Appeler un médecin');
                    Swal.fire({
                        text: res.message || "Erreur lors du déclenchement de l'appel d'urgence.",
                        icon: "error",
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function (err) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-phone-volume me-2 fs-18"></i> Appeler un médecin');
                Swal.fire({
                    text: "Impossible de joindre le serveur pour lancer l'appel d'urgence.",
                    icon: "error",
                    confirmButtonColor: '#ef4444'
                });
            }
        });
    };

    $(document).on('click', '#btnAppelerMedecin', function () {
        window.launchEmergencyCall();
    });

    // Polling d'état d'urgence pour mettre à jour en direct le statut si un médecin accepte ou si l'appel est en cours
    setInterval(async () => {
        var isUrgenceChecked = $('input[name="orientation_infirmier"]:checked').val() === 'urgence';
        if (!isUrgenceChecked) return;

        try {
            var consultationId = "{{ $consultation->id }}";
            const res = await fetch(`/infirmier/consultation/teleconsultation/active-list`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) {
                const data = await res.json();
                const list = data.teleconsultations || [];
                const current = list.find(t => t.id == consultationId);

                if (current && (current.channel || current.is_call_active || current.call_status === 'in_call' || current.call_status === 'accepted' || current.call_status === 'calling' || current.call_status === 'ended')) {
                    // Masquer le bouton de soumission standard
                    $('#btnSubmitFormulaire').hide();
                    $('#btnResetOrientation').show();

                    // Mettre à jour le badge de statut
                    var badge = document.getElementById('emergencyStatusBadge');
                    if (badge) {
                        if (current.has_doctor_accepted || current.call_status === 'in_call' || current.call_status === 'accepted') {
                            badge.className = 'badge bg-success text-white rounded-pill px-2.5 py-1 fs-11 fw-semibold';
                            badge.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i> ${current.doctor_name || 'Médecin'} connecté`;
                        } else {
                            badge.className = 'badge bg-warning text-dark rounded-pill px-2.5 py-1 fs-11 fw-semibold';
                            badge.innerHTML = `<span class="spinner-grow spinner-grow-sm me-1" style="width: 7px; height: 7px;"></span> Appel en cours de sonnerie...`;
                        }
                    }

                    // S'assurer que le bouton est 'Rejoindre l'appel'
                    if (!$('#btnRejoindreAppelDirect').length && current.token && current.livekit_url) {
                        var docTitle = current.has_doctor_accepted ? current.doctor_name : 'Médecins de garde';
                        $('#emergencyCallBtnContainer').html(`
                            <button type="button" class="btn text-white btn-lg px-25 py-12 rounded-12 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnRejoindreAppelDirect" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;" onclick="openInfirmierVideoCall(${current.id}, '${current.token}', '${current.livekit_url}', '${docTitle.replace(/'/g, "\\'")}', '${(current.patient_name || '').replace(/'/g, "\\'")}', '${current.channel}')">
                                <i class="fa-solid fa-video fs-18"></i>
                                <span>Rejoindre l'appel</span>
                            </button>
                        `);
                    }
                }
            }
        } catch(e) {}
    }, 3000);
    @endif

    // Validation à la soumission
    $('form[action*="formulaire"]').on('submit', function (e) {
        var orientation = $('input[name="orientation_infirmier"]:checked').val();
        
        var ta = $('#tension_arterielle').val() ? $('#tension_arterielle').val().trim() : '';
        var temp = $('#temperature').val() ? $('#temperature').val().trim() : '';
        var poids = $('#poids').val() ? $('#poids').val().trim() : '';
        var taille = $('#taille').val() ? $('#taille').val().trim() : '';
        var pouls = $('#pouls').val() ? $('#pouls').val().trim() : '';

        // Constantes physiques obligatoires (sauf pour appel d'urgence immédiat qui a son propre flux d'appel)
        if (orientation !== 'urgence') {
            if (!ta && !temp && !poids && !pouls && !taille) {
                e.preventDefault();
                Swal.fire({
                    title: "Constantes physiques requises",
                    text: "Veuillez renseigner au moins une constante physique du patient (Tension, Température, Poids, Pouls) avant d'enregistrer la téléconsultation ou de valider le dossier.",
                    icon: "warning",
                    confirmButtonColor: '#005AEC'
                });
                return false;
            }
        }

        if (orientation === 'teleconsultation') {
            var dateVal = $('#desired_date').val();
            var docVal = $('#doctor_id').val();
            var hospVal = $('#teleconsultation_hospital_id').val();

            if (!hospVal) {
                e.preventDefault();
                Swal.fire({
                    text: "Veuillez sélectionner un établissement pour la téléconsultation.",
                    icon: "warning",
                    confirmButtonColor: '#005AEC'
                });
                return false;
            }
            if (!dateVal) {
                e.preventDefault();
                Swal.fire({
                    text: "Veuillez renseigner la date de la téléconsultation.",
                    icon: "warning",
                    confirmButtonColor: '#005AEC'
                });
                return false;
            }
            if (!docVal) {
                e.preventDefault();
                Swal.fire({
                    text: "Veuillez sélectionner un médecin disponible dans la liste pour cette téléconsultation.",
                    icon: "warning",
                    confirmButtonColor: '#005AEC'
                });
                return false;
            }
        }
    });
    </script>

@endsection
