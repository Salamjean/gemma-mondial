@php
    $patient = $consultation->patient ?? optional($consultation->admission)->patient;
    $patientUser = optional($patient)->user;
    $imgUrl = optional($patient)->img_url;
    $gender = optional($patient)->gender ?? 'masculin';
    $codePatient = optional($patient)->code_patient ?? 'N/A';
    $birthDate = optional($patient)->birth_date ?? '';
    $age = 'N/A';
    if ($birthDate) {
        try {
            $agePatient = \Carbon\Carbon::createFromFormat('d/m/Y', $birthDate);
            $age = $agePatient->diffInYears(\Carbon\Carbon::now()) . ' ans';
        } catch (\Throwable $e) {
            $age = 'N/A';
        }
    }
    $residence = optional(optional($patient)->residenceActuelle)->name ?? 'Non renseigné';
    $profession = optional($patient)->profession ?? 'Non renseignée';
    $telephone = optional($patient)->telephone ?? 'Non renseigné';
    $noAssurance = optional($patient)->no_assurance ?? 'Non renseigné';
    $patientFullName = trim(optional($patientUser)->name . ' ' . (optional($patientUser)->prenom ?? '')) ?: 'Patient';
    $motifText = $consultation->motif_consultation ?? optional($consultation->admission)->motif_consultation ?? (optional(optional($consultation->registre)->registreConsultationCurative)->motif_consultation ?? '');
@endphp

<div class="container">
    <div class="box bb-3 border pe-5 pb-20 px-20 ps-10 pt-20 bg-color">
        <div class="row">
            <div class="col-md-2">
                <div class="d-flex justify-content-center align-items-center">
                    @if ($imgUrl != null)
                        <img src="{{ asset('assets/uploads/patient/' . $imgUrl) }}"
                            class="rounded-circle" alt="Photo de profil" style="width:128px; height:128px" />
                    @else
                        @if ($gender == 'masculin')
                            <img src="{{ asset('assets/images/avatar/6.png') }}" class="rounded-circle"
                                alt="Photo de profil" style="width:100px; height:100px; object-fit: cover;" />
                        @else
                            <img src="{{ asset('assets/images/avatar/2.png') }}" class="rounded-circle"
                                alt="Photo de profil" style="width:100px; height:100px; object-fit: cover;" />
                        @endif
                    @endif
                </div>
            </div>
            <div class="col-md-10">
                <div class="px-2">
                    <div class="px-5 bg-color">
                        <div class="row">
                            <div class="d-flex justify-content-between align-items-center mt-10 flex-wrap gap-2">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    @if(!request()->has('embed'))
                                        <a href="{{ route('doctor.consultation.today') }}" class="btn btn-sm btn-secondary me-2"><i class="fa-solid fa-arrow-left me-1"></i> Retour</a>
                                    @endif
                                    <label class="form-label mb-0">N° Dossier médical | <span class="fw-bold fs-18"><span id="dm_patient"
                                                style="color:red;">{{ $codePatient }}</span></span></label>

                                    <!-- Bouton Relancer l'Appel Salle d'Attente -->
                                    <button type="button" 
                                        class="btn btn-sm btn-primary fw-bold px-3 py-1.5 rounded-8 shadow-sm d-inline-flex align-items-center gap-1.5 ms-2 btn-recall-in-consultation"
                                        data-id="{{ $consultation->id }}"
                                        data-name="{{ $patientFullName }}"
                                        title="Relancer l'appel sonore et visuel sur l'écran de la salle d'attente">
                                        <i class="fa-solid fa-bullhorn me-1"></i>
                                        <span>Relancer l'appel</span>
                                    </button>
                                </div>
                                <div class="d-flex items-center pb-1">

                                    <span class=" d-flex mt-1 text-success">
                                        <span>Consultation du {{ Carbon\Carbon::now()->format('d/m/Y') }}</span>
                                        <pre>  </pre> |
                                        <pre>  </pre> <span>Ordre N°{{ noOrdreConsultation() }}</span>
                                        <pre>  </pre>
                                    </span>
                                    <a href="" title="dossier medical" class="btn btn-sm btn-secondary mx-1"
                                        target="_blank"><i class="fa-solid fa-print"></i></a>
                                    @if($patient)
                                    <a href="{{ route('doctor.patient.detail', $patient->id) }}" title="info patient"
                                        class="btn btn-sm btn-success mx-1" target="_blank"><i class="fa-solid fa-info"></i></a>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="form-label"> <b>Nom complet </b> </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $patientFullName }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label"> <b>Né(e) le</b></label>
                                    <input type="text" class="form-control" id="birth_date" name="birth_date"
                                        value="{{ $birthDate }}" disabled>
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
                                        value="{{ $gender }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label"><b>Résidence Actuelle</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $residence }}" readonly />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><b>Profession</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $profession }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><b>Contact</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $telephone }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><b>N° Assurance</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $noAssurance }}" readonly />
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <h5 class="fw-400 mb-5 mt-5">Constantes physiques du patient | Infirmier : <span class=" text-success">
                            {{ optional(optional($consultation->infirmier)->user)->name ?? 'Non renseigné' }}</span> </h5>
                    <hr>
                    @php
                        $valPoids = $consultation->poids ?: (optional(optional($consultation->registre)->registreConsultationCurative)->poids ?? '');
                        $valTaille = $consultation->taille ?: (optional(optional($consultation->registre)->registreConsultationCurative)->taille ?? '');
                        $valImc = $consultation->imc ?: (optional(optional($consultation->registre)->registreConsultationCurative)->imc ?? '');
                        $valTemp = $consultation->temperature ?: (optional(optional($consultation->registre)->registreConsultationCurative)->temperature ?? '');
                        $valTA = $consultation->tension_arterielle ?: (optional(optional($consultation->registre)->registreConsultationCurative)->ta ?? '');
                        $valPouls = $consultation->pouls ?: (optional(optional($consultation->registre)->registreConsultationCurative)->pouls ?? '');
                    @endphp
                    <div class="row">

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="poids" class="form-label"> <b>Poids</b></label>
                                <input type="text" class="form-control" id="poids" name="poids"
                                    value="{{ $valPoids }}" placeholder="Kg" {{ !empty($valPoids) ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="taille" class="form-label"> <b>Taille</b></label>
                                <input type="text" class="form-control" id="taille" name="taille" value="{{ $valTaille }}"
                                    placeholder="0.00" pattern="\d+(\.\d{1,2})?" title="Veuillez saisir un nombre avec jusqu'à deux décimales" {{ !empty($valTaille) ? 'readonly' : '' }}>
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
                                <label for="temperature" class="form-label"> <b>Temp(°C)</b></label>
                                <input type="text" class="form-control" id="temperature" name="temperature"
                                    value="{{ $valTemp }}" placeholder="°C" {{ !empty($valTemp) ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="tension_arterielle" class="form-label"> <b>TA</b></label>
                                <input type="text"
                                    class="form-control @error('tension_arterielle') is-invalid @enderror"
                                    id="tension_arterielle" value="{{ $valTA }}"
                                    name="tension_arterielle" placeholder="mmHg" {{ !empty($valTA) ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="pouls" class="form-label"> <b>Pouls</b></label>
                                <input type="text" class="form-control @error('pouls') is-invalid @enderror"
                                    id="pouls" name="pouls" value="{{ $valPouls }}"
                                    placeholder="batt/mn" {{ !empty($valPouls) ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-12 pt-4">
                            <label class="form-label"><b>Motif de la consultation | description du mal</b></label>
                            <textarea type="text" class="form-control" name="motif_consultation" id="motif_consultation">{{ $motifText }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
 const poidsInput = document.getElementById('poids');
 const tailleInput = document.getElementById('taille');
 const imcInput = document.getElementById('imc');

 function calculerIMC() {
     if (!poidsInput || !tailleInput || !imcInput) return;
     const poids = parseFloat(poidsInput.value);
     const taille = parseFloat(tailleInput.value) / 100.0;

     if (!isNaN(poids) && !isNaN(taille) && taille > 0) {
         const imc = poids / (taille * taille);
         imcInput.value = imc.toFixed(2);
     } else {
         imcInput.value = '';
     }
 }

 if (poidsInput && tailleInput && imcInput) {
     poidsInput.addEventListener('input', calculerIMC);
     tailleInput.addEventListener('input', calculerIMC);
 }

 // Relance de l'appel patient en salle d'attente
 document.addEventListener('DOMContentLoaded', function() {
     const recallBtn = document.querySelector('.btn-recall-in-consultation');
     if (recallBtn) {
         recallBtn.addEventListener('click', function(e) {
             e.preventDefault();
             const btn = this;
             const consultationId = btn.getAttribute('data-id');
             const originalHtml = btn.innerHTML;

             btn.disabled = true;
             btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Appel en cours...';

             fetch("{{ url('doctor/consultation/call-patient') }}/" + consultationId, {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 }
             })
             .then(response => response.json())
             .then(data => {
                 btn.disabled = false;
                 btn.innerHTML = originalHtml;
                 if (data.success) {
                     if (typeof Swal !== 'undefined') {
                         Swal.fire({
                             icon: 'success',
                             title: 'Appel relancé !',
                             text: data.message || 'Le patient a été rappelé sur l\'écran de la salle d\'attente.',
                             toast: true,
                             position: 'top-end',
                             showConfirmButton: false,
                             timer: 3500,
                             timerProgressBar: true
                         });
                     } else {
                         alert(data.message);
                     }
                 } else {
                     alert(data.message || 'Erreur lors de la relance de l\'appel.');
                 }
             })
             .catch(err => {
                 btn.disabled = false;
                 btn.innerHTML = originalHtml;
                 console.error('Erreur relance appel :', err);
                 alert('Impossible de relancer l\'appel. Veuillez réessayer.');
             });
         });
     }
 });
</script>