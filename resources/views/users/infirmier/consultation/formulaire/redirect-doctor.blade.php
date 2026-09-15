<div class="container">
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
        @endphp
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
                                    <div class="col-md-6">
                                        <label class="form-label">N° Dossier médical | <span class="fw-bold fs-18"><span id="dm_patient" style="color:red;">{{ $patient->code_patient ?? 'N/A' }}</span></span></label>
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="form-label"> <b>Nom complet </b> </label>
                                        <input type="text" class="form-control" id="name" name="name"
                                        value="{{ ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '') }}" disabled>
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
                                <div class="col-md-4">
                                    <label class="form-label"><b>Résidence Actuelle</b></label>
                                    <input type="text" class="form-control"
                                        value="{{ $patient->residenceActuelle->name ?? 'N/A' }}" readonly />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><b>Contact</b></label>
                                    <input type="text" class="form-control"
                                        value="{{ $patient->telephone ?? '' }}" readonly />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><b>N° de securité sociale</b></label>
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="poids" class="form-label"> <b>Poids</b></label>
                                        <input type="text" class="form-control" id="poids" name="poids" placeholder="Kg">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="taille" class="form-label"> <b>Taille</b></label>
                                        <input type="text" class="form-control" id="taille" name="taille" placeholder="cm">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="imc" class="form-label"><b>IMC</b></label>
                                        <input type="text" class="form-control" id="imc" name="imc" placeholder="Kg/m²" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="temperature" class="form-label"> <b>Température</b></label>
                                        <input type="text" class="form-control" id="temperature" name="temperature" placeholder="°C">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tension_arterielle" class="form-label"> <b>TA</b></label>
                                        <input type="text" class="form-control @error('tension_arterielle') is-invalid @enderror" id="tension_arterielle" name="tension_arterielle" placeholder="mmHg">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pouls" class="form-label"> <b>Pouls</b></label>
                                        <input type="text" class="form-control @error('pouls') is-invalid @enderror" id="pouls" name="pouls" placeholder="batt/mn">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="gly_a_jeun" class="form-label"> <b>Glycémie</b></label>
                                        <input type="text" class="form-control @error('gly_a_jeun') is-invalid @enderror" id="gly_a_jeun" name="gly_a_jeun" placeholder="A jeûn(g/l)">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="gly_nn_jeun" class="form-label"> <b>&nbsp;</b></label>
                                        <input type="text" class="form-control @error('gly_nn_jeun') is-invalid @enderror" id="gly_nn_jeun" name="gly_nn_jeun" placeholder="non-jeûn(g/l)">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="saturation_oxygene" class="form-label"> <b>Oxygène</b></label>
                                        <input type="text" class="form-control @error('saturation_oxygene') is-invalid @enderror" id="saturation_oxygene" name="saturation_oxygene">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="perimetre_brachial" class="form-label"> <b>PB</b></label>
                                        <input type="text" class="form-control @error('perimetre_brach') is-invalid @enderror" id="perimetre_brach" name="perimetre_brach" placeholder="cm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                    </div>
                    <div class="box-footer bt-3 border-primary">
                        <div class="float-end">
                            <button type="button" class="btn btn-warning me-1">
                                <i class="ti-trash"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-save-alt"></i> Valider le premier soins
                            </button>
                        </div>
                    </div>
                </div>
                <br />
            </div>
        </form>
    </div>
    <script>
        // Sélection des champs poids, taille et IMC
        const poidsInput = document.getElementById('poids');
        const tailleInput = document.getElementById('taille');
        const imcInput = document.getElementById('imc');

        // Fonction pour calculer l'IMC
        function calculerIMC() {
            const poids = parseFloat(poidsInput.value);
            const taille = parseFloat(tailleInput.value);

            if (!isNaN(poids) && !isNaN(taille) && taille > 0) {
                const imc = poids / (taille * taille);
                imcInput.value = imc.toFixed(2); // Afficher l'IMC avec deux décimales
            } else {
                imcInput.value = ''; // Réinitialiser le champ IMC si les valeurs ne sont pas valides
            }
        }

        // Écouter les événements de changement dans les champs Poids et Taille
        poidsInput.addEventListener('input', calculerIMC);
        tailleInput.addEventListener('input', calculerIMC);
    </script>
