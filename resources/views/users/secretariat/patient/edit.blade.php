@extends('layouts.dashboard', ['title' => 'Modifier le Patient ' . $patient->code_patient])

@push('css')
    @livewireStyles()
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/select2/dist/css/select2.min.css') }}">
    <style>
        .p-search {
            background-image: url("{{ asset('assets/images/bg/bg-search.png') }}");
            border-radius: 10px;
            margin: 10px auto;
        }
        .w-80 {
            width: 80px !important;
        }
        .align-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .select2-selection {
            height: 34px !important;
            width: 100%;
        }
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }
        .mont-aff {
            background: #f4f6f9;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- En-tête de la page -->
            <div class="box bb-3 border-danger">
                <div class="box-header with-border">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="box-title fw-bold fs-22 fs-md-26 text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Modification du Dossier Patient</h4>
                            <h6 class="box-subtitle mb-0 text-muted">Patient : <b>{{ $patient->user->name }} {{ $patient->user->prenom }}</b> — N° Dossier Médical : <b>{{ $patient->code_patient }}</b></h6>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto">
                            <a href="{{ route('secretariat.patient.detail', $patient->id) }}" class="btn btn-info btn-sm rounded-10 shadow-xs flex-fill flex-md-grow-0 text-center">
                                <i class="fa-solid fa-eye me-1"></i> Fiche Patient
                            </a>
                            <a href="{{ route('secretariat.patient.list') }}" class="btn btn-dark btn-sm rounded-10 shadow-xs flex-fill flex-md-grow-0 text-center">
                                <i class="fa fa-arrow-left me-1"></i> Liste des patients
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de modification du patient -->
            <div class="container-fluid px-0" id="addPatient">
                <form id="formEditPatient" action="{{ route('secretariat.patient.updatepatient', $patient->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" value="{{ $patient->id }}">

                    @php
                        $isNewborn = str_contains(strtoupper($patient->user->name ?? ''), 'NOUVEAU');
                    @endphp

                    <!-- Step 1 : Données sur le Patient -->
                    <div class="box bb-3 border-warning p-15 p-md-25 bg-color">
                        <div class="box-body ribbon-box">
                            <div class="ribbon ribbon-dark rounded5">Données sur le Patient</div>
                            <br /><br />

                            @if ($isNewborn)
                                <div class="alert alert-warning border-0 rounded-10 shadow-sm p-15 mb-20">
                                    <i class="fa-solid fa-baby-carriage fs-20 text-warning me-2"></i>
                                    <strong>Dossier Nouveau-né :</strong> Vous pouvez renseigner le <strong>Nom</strong>, <strong>Prénom(s)</strong> et les coordonnées de l'enfant. Conformément à la déclaration de naissance, le <strong>Sexe</strong> et la <strong>Date de naissance</strong> restent strictement verrouillés.
                                </div>
                            @else
                                <div class="alert alert-info border-0 rounded-10 shadow-sm p-15 mb-20">
                                    <i class="fa-solid fa-user-pen fs-20 text-info me-2"></i>
                                    <strong>Modification des informations :</strong> Vous pouvez modifier le <strong>Nom</strong>, <strong>Prénom(s)</strong>, <strong>Email</strong> et les coordonnées du patient. Conformément au dossier médical, le <strong>Sexe</strong> et la <strong>Date de naissance</strong> restent verrouillés.
                                </div>
                            @endif

                            <div class="box bb-3 border-danger p-10">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name_up" class="form-label"> <b>Nom : </b> <span class="danger">*</span> </label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="ti-user"></i></span>
                                                <input type="text" name="name_up" class="form-control" placeholder="Nom" id="name_up" 
                                                    value="{{ old('name_up', $patient->user->name) }}" required 
                                                    oninput="this.value = this.value.toUpperCase()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="prenom_up" class="form-label"> <b>Prénom(s) : </b> <span class="danger">*</span> </label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="ti-user"></i></span>
                                                <input type="text" name="prenom_up" class="form-control" placeholder="Prénom(s)" id="prenom_up" 
                                                    value="{{ old('prenom_up', $patient->user->prenom) }}" required 
                                                    oninput="this.value = this.value.toUpperCase()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email_up" class="form-label"> <b>E-mail : </b> </label>
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="ti-email"></i></span>
                                                <input type="email" name="email_up" class="form-control" id="email_up" placeholder="Email" 
                                                    value="{{ old('email_up', $patient->user->email) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="gender_up" class="form-label"> <b>Sexe : </b> <span class="text-muted fs-11">(Verrouillé)</span> </label>
                                            <input type="text" class="form-control" value="{{ ucfirst($patient->gender) }}" readonly style="background-color: #f1f3f5; cursor: not-allowed;">
                                            <input type="hidden" name="gender_up" value="{{ $patient->gender }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="birth_date_up" class="form-label"> <b>Date de naissance : </b> <span class="text-muted fs-11">(Verrouillée)</span> </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="birth_date_up" id="birth_date_up" class="form-control" 
                                                    value="{{ $patient->birth_date }}" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask=""
                                                    readonly style="background-color: #f1f3f5; cursor: not-allowed;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="telephone" class="form-label"> <b>Téléphone : </b> <span class="danger">*</span> </label>
                                            <div class="d-flex">
                                                <input type="text" name="prefix_telephone" id="prefix_telephone" class="form-control text-center fw-bold bg-white text-dark" style="max-width: 80px; border-top-right-radius: 0; border-bottom-right-radius: 0;" value="+225" placeholder="+225" title="Préfixe / Indicatif pays">
                                                <input type="text" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;" name="telephone" id="telephone" class="form-control fw-bold border-primary" value="{{ old('telephone', $patient->telephone) }}" placeholder="0101010101" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="contact2" class="form-label"> <b>N° Téléphone 2 : </b> </label>
                                            <div class="d-flex">
                                                <input type="text" name="prefix_contact2" id="prefix_contact2" class="form-control text-center fw-bold bg-white text-dark" style="max-width: 80px; border-top-right-radius: 0; border-bottom-right-radius: 0;" value="+225" placeholder="+225" title="Préfixe / Indicatif pays">
                                                <input type="text" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;" name="contact2" id="contact2" class="form-control" value="{{ old('contact2', $patient->contact2) }}" placeholder="0707000000">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lieu_naissance_up" class="form-label"> <b>Lieu de naissance : </b> <span class="danger">*</span> </label>
                                            <div class="input-group mb-3">
                                                <select class="form-control select2" name="lieu_de_naissance" id="lieu_naissance_up" style="width: 100%"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="residence_habituelle_up" class="form-label"> <b>Lieu de résidence habituelle : </b> <span class="danger">*</span> </label>
                                            <div class="input-group mb-3">
                                                <select class="form-control select2" name="residence_habituelle_up" id="residence_habituelle_up" style="width: 100%"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="residence_actuelle_up" class="form-label"> <b>Lieu de résidence actuelle : </b> <span class="danger">*</span> </label>
                                            <div class="input-group mb-3">
                                                <select class="form-control select2" id="residence_actuelle_up" name="residence_actuelle_up" style="width: 100%" required></select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 : Renseignements Administratifs -->
                        <div class="box-body ribbon-box">
                            <div class="ribbon ribbon-danger rounded5">Renseignements Administratifs du Patient</div>
                            <br /><br /><br />
                            <div class="box bb-3 border-info p-10">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="situation_matrimoniale_up" class="form-label"> <b> Situation Matrimoniale: </b> <span class="danger">*</span></label>
                                            <select class="form-select" id="situation_matrimoniale_up" name="situation_matrimoniale_up" required>
                                                <option value="" disabled>Sélectionner</option>
                                                <option value="celibataire" {{ $patient->situation_matrimoniale == 'celibataire' ? 'selected' : '' }}>Celibataire</option>
                                                <option value="marie(e)" {{ $patient->situation_matrimoniale == 'marie(e)' ? 'selected' : '' }}>Marié(e)</option>
                                                <option value="divorce(e)" {{ $patient->situation_matrimoniale == 'divorce(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                                <option value="Concubinage" {{ $patient->situation_matrimoniale == 'Concubinage' ? 'selected' : '' }}>Concubinage</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pays_up" class="form-label"> <b> Pays de naissance: </b> <span class="danger">*</span></label>
                                            <select class="form-select" id="pays_up" name="pays_up">
                                                <option value="Côte d'Ivoire" {{ $patient->country == "Côte d'Ivoire" || empty($patient->country) ? 'selected' : '' }}>Côte d'Ivoire</option>
                                                <option value="autre" {{ $patient->country != "Côte d'Ivoire" && !empty($patient->country) ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="precision_up" style="{{ $patient->country != "Côte d'Ivoire" && !empty($patient->country) ? '' : 'display: none;' }}">
                                        <div class="form-group">
                                            <label for="autre_pays_up" class="form-label"> <b>Précisez le pays : </b> <span class="danger">*</span> </label>
                                            <select name="autre_pays_up" id="autre_pays_up" class="form-control select2" style="width: 100%"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="profession_up" class="form-label"> <b>Profession: </b> </label>
                                            <input type="text" class="form-control" name="profession_up" id="profession_up" value="{{ old('profession_up', $patient->profession) }}" placeholder="Ex: Enseignant, Commerçant, Étudiant...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="nbre_enfant_up" class="form-label"> <b>Nombre d'enfant: </b> </label>
                                            <select class="form-select" name="nbre_enfant_up" id="nbre_enfant_up">
                                                @for ($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ $patient->nbre_enfant == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row mb-10">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="num_cmu_up" class="form-label"> <b>N° CMU (Sécurité Sociale) : </b> </label>
                                            <input type="text" class="form-control" name="num_cmu_up" id="num_cmu_up" value="{{ old('num_cmu_up', $patient->num_cmu) }}" placeholder="Ex: 12345678901">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type_piece_up" class="form-label"> <b>Type de pièce d'identité: </b> <span class="danger">*</span></label>
                                            <select class="form-select" id="type_piece_up" name="type_piece_up" required>
                                                <option value="" disabled>Sélectionner</option>
                                                <option value="CNI" {{ $patient->type_piece == 'CNI' ? 'selected' : '' }}>CNI (Carte d'Identité Nationale)</option>
                                                <option value="Attestation identite" {{ $patient->type_piece == 'Attestation identite' ? 'selected' : '' }}>Attestation d'Identité</option>
                                                <option value="Passeport" {{ $patient->type_piece == 'Passeport' ? 'selected' : '' }}>Passeport</option>
                                                <option value="Carte consulaire" {{ $patient->type_piece == 'Carte consulaire' ? 'selected' : '' }}>Carte Consulaire</option>
                                                <option value="Permis de conduire" {{ $patient->type_piece == 'Permis de conduire' ? 'selected' : '' }}>Permis de conduire</option>
                                                <option value="Pas de pièce" {{ $patient->type_piece == 'Pas de pièce' ? 'selected' : '' }}>Pas de pièce</option>
                                                <option value="Autre" {{ $patient->type_piece == 'Autre' ? 'selected' : '' }}>Autre</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="no_piece_up">
                                        <div class="form-group">
                                            <label for="numero_identite_up" class="form-label" id="label_numero_identite_up"> <b>{{ $patient->type_piece == 'CNI' ? 'N° NNI :' : "N° Pièce d'identité:" }} </b> </label>
                                            <input type="text" class="form-control" name="numero_identite_up" id="numero_identite_up" value="{{ old('numero_identite_up', $patient->numero_identite) }}" placeholder="{{ $patient->type_piece == 'CNI' ? 'Ex: 12345678901' : 'CI12899312AP002' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="ethnie_up" class="form-label"> <b>Ethnie: </b> <span class="danger">*</span></label>
                                            <select class="form-control select2" id="ethnie_up" name="ethnie_up" style="width: 100%"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="no_assurance_up" class="form-label"> <b>N° d'assurance: </b> </label>
                                            <input type="text" class="form-control" name="no_assurance_up" id="no_assurance_up" value="{{ old('no_assurance_up', $patient->no_assurance) }}" placeholder="Numéro d'assurance">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="address_up" class="form-label"> <b>Adresse / Quartier: </b> </label>
                                            <input type="text" class="form-control" name="address_up" id="address_up" value="{{ old('address_up', $patient->address) }}" placeholder="Ex: Cocody Angré 8ème Tranche">
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="nom_personne_cas_urgence_up" class="form-label"> <b>Nom & Prénom en cas d'urgence : </b></label>
                                            <input type="text" class="form-control" name="nom_personne_cas_urgence_up" id="nom_personne_cas_urgence_up" value="{{ old('nom_personne_cas_urgence_up', $patient->nom_personne_cas_urgence) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="telephone_personne_cas_urgence_up" class="form-label"> <b>Téléphone en cas d'urgence : </b></label>
                                            <input type="text" class="form-control" name="telephone_personne_cas_urgence_up" id="telephone_personne_cas_urgence_up" value="{{ old('telephone_personne_cas_urgence_up', $patient->telephone_personne_cas_urgence) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="lien_personne_cas_urgence_up" class="form-label"> <b>Lien personne en cas d'urgence: </b></label>
                                            <input type="text" class="form-control" name="lien_personne_cas_urgence_up" id="lien_personne_cas_urgence_up" value="{{ old('lien_personne_cas_urgence_up', $patient->lien_personne_cas_urgence) }}">
                                        </div>
                                    </div>
                                </div>
                                <br />
                            </div>
                        </div>

                        <!-- Footer actions -->
                        <div class="box-footer">
                            <div class="float-end">
                                <button type="button" class="btn btn-warning me-1" onclick="history.back()">
                                    <i class="ti-arrow-left"></i> Annuler
                                </button>
                                <button type="submit" class="btn btn-primary" id="btnSubmitEdit">
                                    <i class="ti-save-alt"></i> Mettre à jour
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @livewireScripts()
    <script src="{{ asset('assets/vendor_plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Activer Select2
            $('.select2').select2({ width: '100%' });

            // Données d'admission existantes du patient (si applicables)
            const patientData = {
                lieuNaissanceId: "{{ $patient->lieu_de_naissance_id }}",
                residenceActuelleId: "{{ $patient->residence_actuelle_id }}",
                residenceHabituelleId: "{{ $patient->residence_habituelle_id }}",
                profession: "{{ $patient->profession }}",
                ethnie: "{{ $patient->ethnie }}",
                country: "{{ $patient->country }}"
            };

            // Toggle Pays autre
            $('#pays_up').change(function() {
                if ($(this).val() === 'autre') {
                    $('#precision_up').show();
                } else {
                    $('#precision_up').hide();
                }
            });

            // 1. Lieux de naissance
            $.ajax({
                url: "{{ route('secretariat.lieu_naissance') }}",
                method: 'GET',
                success: function(response) {
                    const select = $('#lieu_naissance_up');
                    select.empty().append('<option value="">Selectionner</option>');
                    $.each(response, function(key, item) {
                        const isSelected = (item.id == patientData.lieuNaissanceId);
                        select.append(new Option(item.name, item.id, false, isSelected));
                    });
                    select.trigger('change');
                }
            });

            // 2. Résidence actuelle
            $.ajax({
                url: "{{ route('secretariat.residence_actuelle') }}",
                method: 'GET',
                success: function(response) {
                    const select = $('#residence_actuelle_up');
                    select.empty().append('<option value="">Selectionner</option>');
                    $.each(response, function(key, item) {
                        const isSelected = (item.id == patientData.residenceActuelleId);
                        select.append(new Option(item.name, item.id, false, isSelected));
                    });
                    select.trigger('change');
                }
            });

            // 3. Résidence habituelle
            $.ajax({
                url: "{{ route('secretariat.residence_habituelle') }}",
                method: 'GET',
                success: function(response) {
                    const select = $('#residence_habituelle_up');
                    select.empty().append('<option value="">Selectionner</option>');
                    $.each(response, function(key, item) {
                        const isSelected = (item.id == patientData.residenceHabituelleId);
                        select.append(new Option(item.name, item.id, false, isSelected));
                    });
                    select.trigger('change');
                }
            });

            // 4. Pays (countries-FR.json)
            fetch('{{ asset('assets/src/countries-FR.json') }}')
                .then(response => response.json())
                .then(data => {
                    const select = $('#autre_pays_up');
                    select.empty().append('<option value="">Selectionner</option>');
                    for (const code in data) {
                        const nom = data[code];
                        const selected = (nom === patientData.country) ? 'selected' : '';
                        select.append(`<option value="${nom}" ${selected}>${nom}</option>`);
                    }
                    select.trigger('change');
                });


            // 6. Ethnie (ethnies.json)
            fetch('{{ asset('assets/src/ethnies.json') }}')
                .then(response => response.json())
                .then(data => {
                    const select = $('#ethnie_up');
                    select.empty().append('<option value="">Selectionner</option>');
                    for (const libelle in data) {
                        const nom = data[libelle];
                        const selected = (nom === patientData.ethnie) ? 'selected' : '';
                        select.append(`<option value="${nom}" ${selected}>${nom}</option>`);
                    }
                    select.trigger('change');
                });

            // Change dynamic label for type_piece_up
            $('#type_piece_up').on('change', function () {
                var val = $(this).val();
                if (val === 'CNI') {
                    $('#label_numero_identite_up').html('<b>N° NNI : </b>');
                    $('#numero_identite_up').attr('placeholder', 'Ex: 12345678901');
                } else {
                    $('#label_numero_identite_up').html('<b>N° Pièce d\'identité: </b>');
                    $('#numero_identite_up').attr('placeholder', 'CI12899312AP002');
                }
            });

            // 7. Soumission AJAX du formulaire
            $('#formEditPatient').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let btn = $('#btnSubmitEdit');
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Mise à jour...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        btn.prop('disabled', false).html('<i class="ti-save-alt"></i> Mettre à jour');
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès !',
                            text: response.success || 'Modifications enregistrées avec succès.',
                            confirmButtonColor: '#3596f7'
                        }).then(() => {
                            window.location.href = "{{ route('secretariat.patient.detail', $patient->id) }}";
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="ti-save-alt"></i> Mettre à jour');
                        let errorMessage = 'Une erreur est survenue lors de la mise à jour.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur de validation',
                            html: errorMessage,
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            });
        });
    </script>
@endpush
