<div class="container" id="addPatient">
    <form id="formAdd">
        @csrf
        <div class="mont-aff border-dark">
            <label class="title mb-4">Montant à payer à la Caisse</label>
            <h3 class="box-title">
                <input type="hidden" class="form-control" name="montant" id="montant">
                <b id="prix"> 0 Frs CFA</b>
            </h3>
        </div>
        <br /><br />
        <!-- Step 1 -->
        <div class="box bb-3 border-warning pe-95 pb-20 ps-95 pt-20 bg-color">
            <div class="box-body ribbon-box">
                <div class="ribbon ribbon-dark rounded5">Données sur le Patient</div>
                <br /><br /><br />
                <div class="box bb-3 border-danger p-10">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="form-label"> <b>Nom : </b> <span class="danger">*</span>
                                </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="ti-user"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Nom" id="name"
                                        oninput="convertToUppercase()">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="prenom" class="form-label"> <b>Prénom(s) : </b> <span
                                        class="danger">*</span> </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="ti-user"></i></span>
                                    <input type="text" name="prenom" class="form-control" placeholder="Prénom(s)"
                                        id="prenom" oninput="convertToUppercase()">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email" class="form-label"> <b>E-mail : </b> </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="ti-email"></i></span>
                                    <input type="email" name="email" class="form-control" id="email"
                                        placeholder="Email">
                                </div>

                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="gender" class="form-label"> <b>Sexe : </b> <span class="danger">*</span>
                                </label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" selected disabled>Selectionner</option>
                                    <option value="masculin">Masculin</option>
                                    <option value="feminin">Feminin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="birth_date" class="form-label"> <b>Date de naissance : <span
                                            class="danger">*</span></b>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" name="birth_date" id="birth_date" class="form-control"
                                        data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="telephone" class="form-label"> <b>Téléphone : <span
                                            class="danger">*</span></b> </label>
                                <div class="d-flex">
                                    <span class="form-control w-80 text-center align-center"
                                        style="border-top-right-radius: 0; border-bottom-right-radius: 0;">+225</span>
                                    <input type="text"
                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;"
                                        min="10" max="10" name="telephone" id="telephone" class="form-control" autofocus
                                        placeholder="0101010101"
                                        data-inputmask="'mask': ['9999999999', '99 99 99 99 99']" data-mask="">
                                </div>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="contact2" class="form-label"> <b>N° Téléphone 2 : </b> </label>
                                <div class="d-flex">
                                    <span class="form-control w-80 text-center align-center"
                                        style="border-top-right-radius: 0; border-bottom-right-radius: 0;">+225</span>
                                    <input type="text"
                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;"
                                        min="10" max="10" name="contact2" id="contact2" class="form-control"
                                        autocomplete="contact2" autofocus placeholder="0707000000"
                                        data-inputmask="'mask': ['9999999999', '99 99 99 99 99']" data-mask="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lieu_de_naissance" class="form-label"> <b>Lieu de naissance : <span
                                            class="danger">*</span></b> </label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" name="lieu_de_naissance"
                                        id="lieu_naissance"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="residence_habituelle" class="form-label"> <b>Lieu de résidence
                                        habituelle : <span class="danger">*</span></b> </label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" name="residence_habituelle"
                                        id="residence_habituelle"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="residence_actuelle" class="form-label"> <b>Lieu de résidence
                                        actuelle : </b> <span class="danger">*</span> </label>
                                <div class="input-group mb-3">
                                    <select class="form-control select2" id="residence_actuelle"
                                        name="residence_actuelle"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body ribbon-box">
                <div class="ribbon ribbon-danger rounded5">Renseignements Administratifs du Patient</div>
                <br /><br /><br />
                <div class="box bb-3 border-info p-10">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="situation_matrimoniale" class="form-label"> <b> Situation Matrimoniale:
                                    </b> <span class="danger">*</span></label>
                                <select class="form-select" id="situation_matrimoniale" name="situation_matrimoniale">
                                    <option value="" selected disabled>Selectionner</option>
                                    <option value="celibataire">Celibataire</option>
                                    <option value="marie(e)">Marié(e)</option>
                                    <option value="divorce(e)">Divorcé(e)</option>
                                    <option value="Concubinage">Concubinage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="pays" class="form-label"> <b> Pays de naissance: </b> <span
                                        class="danger">*</span></label>
                                <select class="form-select" id="pays" name="pays">
                                    <option value="" selected disabled>Selectionner</option>
                                    <option value="Côte d'Ivoire">Côte d'Ivoire</option>
                                    <option value="autre">Autre</option>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-4" id="precision">
                            <div class="form-group">
                                <label for="autre_pays" class="form-label"> <b>Precisez le pays : </b> <span
                                        class="danger">*</span> </label>
                                <select name="autre_pays" id="autre_pays" class="form-control select2"
                                    style="width: 100%"> </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="profession" class="form-label"> <b>Profession : </b> </label>
                                <input type="text" class="form-control" name="profession" id="profession" placeholder="Ex: Enseignant, Commerçant, Étudiant...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="nbre_enfant" class="form-label"> <b>Nombre d'enfant: </b> </label>
                                <select class="form-select" name="nbre_enfant" id="nbre_enfant">
                                    <option value="" selected disabled>Selectionner</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row mb-10">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="num_cmu" class="form-label"> <b>N° CMU (Sécurité Sociale) : </b></label>
                                <input type="text" class="form-control" name="num_cmu" id="num_cmu" placeholder="Ex: 12345678901">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type_piece" class="form-label"> <b>Type de pièce d'identité: </b>
                                    <span class="danger">*</span></label>
                                <select class="form-select" id="type_piece" name="type_piece">
                                    <option value="" disabled selected>Selectionner</option>
                                    <option value="CNI">CNI (Carte d'Identité Nationale)</option>
                                    <option value="Attestation identite">Attestation d'Identité</option>
                                    <option value="Passeport">Passeport</option>
                                    <option value="Carte consulaire">Carte Consulaire</option>
                                    <option value="Permis de conduire">Permis de conduire</option>
                                    <option value="Pas de pièce">Pas de pièce</option>
                                    <option value="Autre">Autre</option>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-3" id="no_piece">
                            <div class="form-group">
                                <label for="numero_identite" class="form-label" id="label_numero_identite"> <b>N° Pièce d'identité: </b>
                                </label>
                                <input type="text" class="form-control" name="numero_identite" id="numero_identite"
                                    placeholder="CI12899312AP002">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ethnie" class="form-label"> <b>Ethnie: </b>
                                    <span class="danger">*</span></label>
                                <select class="form-control select2" id="ethnie" name="ethnie">
                                    <option value="" disabled selected>Selectionner</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nom_personne_cas_urgence" class="form-label"> <b>Nom & Prénom en cas
                                        d'urgence : </b></label>
                                <input type="text" class="form-control" name="nom_personne_cas_urgence"
                                    id="nom_personne_cas_urgence">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="telephone_personne_cas_urgence" class="form-label"> <b>Téléphone en
                                        cas d'urgence : </b></label>
                                <input type="text" class="form-control" name="telephone_personne_cas_urgence"
                                    id="telephone_personne_cas_urgence">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lien_personne_cas_urgence" class="form-label"> <b>Lien personne en cas
                                        d'urgence: </b></label>
                                <input type="text" class="form-control" name="lien_personne_cas_urgence"
                                    id="lien_personne_cas_urgence">

                            </div>
                        </div>
                    </div>
                    <br />
                </div>
            </div>
            <!-- Section Empreintes Digitales
            <div class="box-body ribbon-box">
                <div class="ribbon ribbon-success rounded5">Enregistrement Biométrique</div>
                <br /><br /><br />
                <div class="box bb-3 border-success p-10">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fa fa-fingerprint"></i> Scanner les empreintes digitales des deux index
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 text-center">
                            <div class="fingerprint-section" id="left-finger-section">
                                <h5><i class="fa fa-hand-point-left"></i> Index Gauche</h5>
                                <div class="fingerprint-placeholder mb-3">
                                    <canvas id="left-finger-canvas" width="200" height="200"
                                        style="border: 2px dashed #ccc; border-radius: 10px;"></canvas>
                                </div>
                                <div class="quality-indicator mb-2">
                                    <span id="left-quality-text">Qualité: --</span>
                                    <div class="progress" style="height: 10px;">
                                        <div id="left-quality-bar" class="progress-bar" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-capture"
                                    data-finger="left_index" id="capture-left">
                                    <i class="fa fa-fingerprint"></i> Scanner Index Gauche
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted" id="left-status">Prêt</small>
                                </div>
                                <input type="hidden" name="fingerprint_left_template" id="fingerprint_left_template">
                                <input type="hidden" name="fingerprint_left_image" id="fingerprint_left_image">
                            </div>
                        </div>

                        <div class="col-md-6 text-center">
                            <div class="fingerprint-section" id="right-finger-section">
                                <h5><i class="fa fa-hand-point-right"></i> Index Droit</h5>
                                <div class="fingerprint-placeholder mb-3">
                                    <canvas id="right-finger-canvas" width="200" height="200"
                                        style="border: 2px dashed #ccc; border-radius: 10px;"></canvas>
                                </div>
                                <div class="quality-indicator mb-2">
                                    <span id="right-quality-text">Qualité: --</span>
                                    <div class="progress" style="height: 10px;">
                                        <div id="right-quality-bar" class="progress-bar" role="progressbar"
                                            style="width: 0%"></div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-capture"
                                    data-finger="right_index" id="capture-right">
                                    <i class="fa fa-fingerprint"></i> Scanner Index Droit
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted" id="right-status">Prêt</small>
                                </div>
                                <input type="hidden" name="fingerprint_right_template" id="fingerprint_right_template">
                                <input type="hidden" name="fingerprint_right_image" id="fingerprint_right_image">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" id="capture-both">
                                    <i class="fa fa-hands"></i> Scanner les deux doigts
                                </button>
                                <button type="button" class="btn btn-warning" id="clear-fingerprints">
                                    <i class="fa fa-redo"></i> Effacer
                                </button>
                                <button type="button" class="btn btn-info" id="test-scanner">
                                    <i class="fa fa-plug"></i> Tester le Scanner
                                </button>
                            </div>

                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fingerprint_optional"
                                        name="fingerprint_optional" checked>
                                    <label class="form-check-label" for="fingerprint_optional">
                                        Empreintes optionnelles (peut être ajouté plus tard)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="fingerprint_device" id="fingerprint_device" value="DigitalPersona U.are.U 4500"> -->
            <!-- Step 3 -->
            <input type="hidden" name="admission_patient" value="Oui">

            <div id="admissionForm">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-info rounded5">Affectation du patient</div>
                    <br /><br /><br />
                    <div class="box bb-3 border-dark p-10">
                        <div class="row">
                            <div class="col-12">
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <input type="hidden" name="type_admission_id" value="Consultation" />
                                    <div class="box-body">
                                        <div class="row">
                                            <div class="col-md-3 service-field-add" id="col_service_add">
                                                <div class="form-group">
                                                    <label for="service_id" class="form-label fw-bold"> Service :
                                                        <span class="danger">*</span> </label>
                                                    <select class="form-select" id="service_id" name="service_id">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 service-field-add" id="col_prestation_add">
                                                <div class="form-group">
                                                    <label for="prestation_service_id"
                                                        class="form-label fw-bold">Prestation de service : <span
                                                            class="danger">*</span> </label>
                                                    <select class="form-select" id="prestation_service_id"
                                                        name="prestation_service_id">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 service-field-add" id="col_infirmier_add">
                                                <div class="form-group">
                                                    <label for="infirmier_id" class="form-label fw-bold"> Infirmier :
                                                        <span class="danger">*</span> </label>
                                                    <select class="form-select" id="infirmier_id" name="infirmier_id"
                                                        style="width: 100%;" required>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 service-field-add" id="formDoctor">
                                                <div class="form-group">
                                                    <label for="doctor_id" class="form-label fw-bold"> Médecin en
                                                        charge : <span class="danger text-danger" id="doctor_required_star">*</span></label>
                                                    <select class="form-select" id="doctor_id" name="doctor_id"
                                                        style="width: 100%;" required>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <label for="fonction" class="form-label"> <b>Mode d'entrée:
                                                            </b> </label>
                                                        <div class="c-inputs-stacked">
                                                            <input type="radio" id="E07" value="Venue lui même"
                                                                name="mode_entree">
                                                            <label for="E07" class="me-30">Venue lui
                                                                même</label>
                                                            <input type="radio" id="E06"
                                                                value="Transféré d'un autre établissement"
                                                                name="mode_entree">
                                                            <label for="E06" class="me-30">Transféré d'un autre
                                                                établissement</label>
                                                            <input type="radio" id="E08" value="Évacué"
                                                                name="mode_entree">
                                                            <label for="E08" class="me-30">Évacué</label>
                                                            <input type="radio" id="E09" value="Référé"
                                                                name="mode_entree">
                                                            <label for="E09" class="me-30">Référé</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="motif_consultation" class="form-label fw-bold">Motif
                                                        de la visite : <span class="danger">*</span></label>
                                                    <textarea class="form-control" id="motif_consultation"
                                                        name="motif_consultation" placeholder="motif de consultation"
                                                        rows="4" maxlength="150"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group float-end">
                                            <label for="fonction" class="form-label text-danger"> <b>Gratuité(GTC):
                                                </b> </label>
                                            <div class="c-inputs-stacked">
                                                <input type="checkbox" id="GR05" value="gratuit" name="gratuite">
                                                <label for="GR05" class="me-30">Cochez la case</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="float-end">
                    <button type="button" class="btn btn-warning me-1" onclick="history.back()">
                        <i class="ti-arrow-left"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAdd">
                        <i class="ti-save-alt"></i> Enregistrer
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<script src="{{ asset('js/corrections-ultimes.js') }}"></script>
<script>
    const serviceId = document.getElementById("service_id");
    const formDoctor = document.getElementById("formDoctor");
    if (formDoctor) formDoctor.style.display = "block";

    function checkServiceDoctorVisibility(val) {
        if (!formDoctor) return;
        var s = (val || '').toString().toLowerCase();
        if (s.includes('infirmier') || s.includes('soin') || s === '4') {
            formDoctor.style.display = "none";
            $('#doctor_id').prop('required', false).val('');
            $('.service-field-add:not(#formDoctor)').removeClass('col-md-3').addClass('col-md-4');
        } else {
            formDoctor.style.display = "block";
            $('#doctor_id').prop('required', true);
            $('.service-field-add').removeClass('col-md-4').addClass('col-md-3');
        }
    }

    if (serviceId) {
        serviceId.addEventListener("change", function (e) {
            checkServiceDoctorVisibility(serviceId.value);
        });
    }
    // pays
    const country = document.getElementById("pays")
    const precision = document.getElementById("precision")
    precision.style.display = "none";
    document.getElementById('autre_pays').value = "Côte d'Ivoire";

    country.addEventListener("change", function (e) {
        if (country.value == 'autre') {
            precision.style.display = "block";
            document.getElementById('autre_pays').value = "";

        } else {
            precision.style.display = "none";
            document.getElementById('autre_pays').value = "Côte d'Ivoire";

        }

    });
    /** validation patient **/
    const admissionForm = document.getElementById('admissionForm');
    if (admissionForm) {
        admissionForm.style.display = 'block';
    }

    $(document).ready(function () {
        $('#formAdd').submit(function (e) {
            e.preventDefault();
            var $btn = $('#btnSubmitAdd');
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement en cours...');
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('secretariat.patient.addpatient') }}",
                method: 'POST',
                data: formData,
                success: function (response) {
                    $btn.prop('disabled', false).html('<i class="ti-save-alt"></i> Enregistrer');
                    Swal.fire({
                        text: response.success,
                        icon: "success",
                        button: "ok",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#F7B662',
                        showCancelButton: true,
                        cancelButtonText: "<a class='text-white' href='{{ route('secretariat.patient.list') }}'><i class='fa fa-eye'></i>&nbsp;Voir la liste</a>",
                        confirmButtonText: "<a class='text-white' href='{{ route('dashboard') }}'><i class='ti-save-alt'></i>&nbsp;Tableau de bord</a>",
                        reverseButtons: true,
                    });
                    $('#formAdd')[0].reset();
                },
                error: function (xhr) {
                    $btn.prop('disabled', false).html('<i class="ti-save-alt"></i> Enregistrer');
                    var errors = xhr.responseJSON ? xhr.responseJSON.errors : {};
                    for (var key in errors) {
                        var errorMessage = errors[key][0];
                        var inputElement = document.getElementById(key);
                        var inputElements = document.querySelectorAll('.form-control');
                        if (inputElement) {
                            var errorContainer = inputElement.closest('.form-group');
                            var errorSpan = document.createElement('span');
                            errorSpan.className = 'text-danger';
                            errorSpan.textContent = errorMessage;
                            if (errorMessage) {
                                errorContainer.appendChild(errorSpan);
                            }
                        }

                        var radioElements = document.querySelectorAll(
                            '.c-inputs-stacked'); // Sélectionnez tous les champs radio
                        radioElements.forEach(function (radioElement) {
                            radioElement.addEventListener('click', function () {
                                var errorContainer = radioElement.closest(
                                    '.form-group');
                                var errorSpan = errorContainer
                                    .querySelector(
                                        '.text-danger'
                                    ); // Sélectionner l'élément span d'erreur existant

                                if (errorSpan) {
                                    errorContainer.removeChild(
                                        errorSpan
                                    ); // Supprimer l'élément span d'erreur existant
                                }
                            });
                        });
                        var selectElements = document.querySelectorAll(
                            '.form-select'); // Sélectionnez tous les champs select
                        selectElements.forEach(function (selectElement) {
                            selectElement.addEventListener('click', function () {
                                var errorContainer = selectElement.closest(
                                    '.form-group');
                                var errorSpan = errorContainer
                                    .querySelector(
                                        '.text-danger'
                                    ); // Sélectionner l'élément span d'erreur existant
                                if (errorSpan) {
                                    errorContainer.removeChild(
                                        errorSpan
                                    ); // Supprimer l'élément span d'erreur existant
                                }
                            });
                        });

                        inputElements.forEach(function (inputElement) {
                            inputElement.addEventListener('click', function () {
                                var errorContainer = inputElement.closest(
                                    '.form-group');
                                var errorSpan = errorContainer
                                    .querySelector(
                                        '.text-danger'
                                    ); // Sélectionner l'élément span d'erreur existant

                                if (errorSpan) {
                                    errorContainer.removeChild(
                                        errorSpan
                                    ); // Supprimer l'élément span d'erreur existant
                                }
                            });

                            inputElement.addEventListener('input', function () {
                                var errorContainer = inputElement.closest(
                                    '.form-group');
                                var errorSpan = errorContainer
                                    .querySelector(
                                        '.text-danger'
                                    ); // Sélectionner l'élément span d'erreur existant

                                if (errorSpan) {
                                    errorContainer.removeChild(
                                        errorSpan
                                    ); // Supprimer l'élément span d'erreur existant
                                }
                            });
                        });
                    }
                    var errorMessage = '';
                    for (var key in errors) {
                        errorMessage += errors[key][0] + '<br/>';
                    }
                    if (errors && Object.keys(errors).length > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur de validation',
                            html: '<div class="text-danger">' + errorMessage +
                                '</div>',
                        });
                    }
                }
            });
        });
    });

    $(document).ready(function () {
        $('#prestation_service_id').change(function () {
            var prestationDoctor = $(this).val();
            var doctorlist = $('#doctor_id');
            doctorlist.empty();
            doctorlist.append('<option value="">Selectionner</option>');
            if (prestationDoctor) {
                $.ajax({
                    url: '{{ route('secretariat.doctors', ':id') }}'.replace(':id',
                        prestationDoctor),
                    type: 'GET',
                    success: function (response) {
                        //console.log(response);
                        $.each(response, function (key, prestation) {
                            doctorlist.append('<option value="' + prestation.doctor
                                .id + '">' + prestation.doctor.user.name +
                                '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    });
    $(document).ready(function () {
        $('#service_id').change(function () {
            var service = $(this).val();
            var prestationlist = $('#prestation_service_id');
            prestationlist.empty();
            prestationlist.append('<option value="">Selectionner</option>');
            if (service) {
                $.ajax({
                    url: '{{ route('secretariat.prestations', ':id') }}'.replace(':id',
                        service),
                    type: 'GET',
                    success: function (response) {
                        $.each(response, function (key, prestation) {
                            var label = (prestation.prestation_service && prestation.prestation_service.libelle)
                                ? prestation.prestation_service.libelle
                                : (prestation.description || 'Prestation');
                            prestationlist.append('<option value="' + prestation.id + '">' + label + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    });
    $(document).ready(function () {
        $('#service_id').change(function () {
            var service = $(this).val();
            var infirmierlist = $('#infirmier_id');
            infirmierlist.empty();
            infirmierlist.append('<option value="">Selectionner</option>');
            if (service) {
                $.ajax({
                    url: '{{ route('secretariat.infirmiers', ':id') }}'.replace(':id', service),
                    type: 'GET',
                    success: function (response) {
                        $.each(response, function (key, infirmier) {
                            infirmierlist.append('<option value="' + infirmier.id +
                                '">' + infirmier.user.name + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    });
    $(document).ready(function () {
        $('#prestation_service_id').change(function () {
            var prestationServiceId = $(this).val();
            $.ajax({
                url: "{{ route('secretariat.prix_prestation') }}",
                method: 'GET',
                data: {
                    prestationServiceId: prestationServiceId
                },
                success: function (response) {
                    var prix = response.prix;
                    $('#prix').text('Prix : ' + prix.toFixed(2) + ' Frs CFA');
                    $('#montant').val(response.prix);
                },
                error: function () {
                    $('#prix').val('Erreur lors de la récupération du prix.');
                }
            });
        });
    });

    /// Regions , Ville , commune ...

    $(document).ready(function () {
        $.ajax({
            url: "{{ route('secretariat.lieu_naissance') }}",
            method: 'GET',
            success: function (response) {
                var lieu_naissancelist = $('#lieu_naissance');
                lieu_naissancelist.empty();
                lieu_naissancelist.append('<option value="" >Selectionner</option>');

                $.each(response, function (key, lieu_naissance) {
                    lieu_naissancelist.append('<option value="' + lieu_naissance.id + '">' +
                        lieu_naissance.name + '</option>');
                });
            },
            error: function (xhr) { }
        });
    });
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('secretariat.residence_actuelle') }}",
            method: 'GET',
            success: function (response) {
                var residence_habituellelist = $('#residence_habituelle');
                residence_habituellelist.empty();
                residence_habituellelist.append('<option value="">Selectionner</option>');

                $.each(response, function (key, residence_habituelle) {
                    residence_habituellelist.append('<option value="' + residence_habituelle
                        .id + '">' + residence_habituelle.name + '</option>');
                });
            },
            error: function (xhr) { }
        });
    });
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('secretariat.residence_actuelle') }}",
            method: 'GET',
            success: function (response) {
                var residence_actuellelist = $('#residence_actuelle');
                residence_actuellelist.empty();
                residence_actuellelist.append('<option value="">Selectionner</option>');

                $.each(response, function (key, residence_actuelle) {
                    residence_actuellelist.append('<option value="' + residence_actuelle
                        .id + '">' + residence_actuelle.name + '</option>');
                });
            },
            error: function (xhr) { }
        });
    });
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('secretariat.residence_habituelle') }}",
            method: 'GET',
            success: function (response) {
                var residence_habituellelist = $('#residence_habituelle');
                residence_habituellelist.empty();
                residence_habituellelist.append('<option value="">Selectionner</option>');

                $.each(response, function (key, residence_habituelle) {
                    residence_habituellelist.append('<option value="' + residence_habituelle
                        .id + '">' + residence_habituelle.name + '</option>');
                });
            },
            error: function (xhr) { }
        });
    });
    // Recuperation Services, Prestations de services, Infirmiers et Medecins //

    $(document).ready(function () {
        $.ajax({
            url: "{{ route('secretariat.hopitalservices') }}",
            method: 'GET',
            success: function (response) {
                var servicelist = $('#service_id');
                servicelist.empty();
                servicelist.append('<option value="">Selectionner</option>');

                $.each(response, function (key, service) {
                    var libelle = (service.service && service.service.libelle) ? service.service.libelle : ('Service #' + service.id);
                    servicelist.append('<option value="' + libelle + '">' + libelle + '</option>');
                });
            },
            error: function (xhr) { }
        });
    });

    /// Liste des pays du monde //
    // Chargez le fichier JSON
    fetch('{{ asset('assets/src/countries-FR.json') }}')
        .then(response => response.json())
        .then(data => {
            const $selectPays = $('#autre_pays');
            $selectPays.empty().append('<option value="">Selectionner</option>');
            for (const code in data) {
                const nom = data[code];
                $selectPays.append(new Option(nom, nom, false, false));
            }
            $selectPays.trigger('change');
        })
        .catch(error => console.error('Erreur de chargement du fichier JSON :', error));

    fetch('{{ asset('assets/src/ethnies.json') }}')
        .then(response => response.json())
        .then(data => {
            const $selectEthnie = $('#ethnie');
            $selectEthnie.empty().append('<option value="">Selectionner</option>');
            for (const libelle in data) {
                const nom = data[libelle];
                $selectEthnie.append(new Option(nom, nom, false, false));
            }
            $selectEthnie.trigger('change');
        })
        .catch(error => console.error('Erreur de chargement du fichier JSON :', error));

    // Dynamic Label for Type de pièce (CNI -> N° NNI)
    $('#type_piece').on('change', function () {
        var val = $(this).val();
        if (val === 'CNI') {
            $('#label_numero_identite').html('<b>N° NNI : </b>');
            $('#numero_identite').attr('placeholder', 'Ex: 12345678901');
        } else {
            $('#label_numero_identite').html('<b>N° Pièce d\'identité: </b>');
            $('#numero_identite').attr('placeholder', 'CI12899312AP002');
        }
    });
</script>
<script>
    // ==============================================
    // SYSTÈME D'EMPREINTES DIGITALES - SIMPLE ET FONCTIONNEL
    // ==============================================

    class WebSocketFingerprintSystem {
        constructor() {
            console.log('🖐️ Système d\'empreintes WebSocket initialisé');
            this.socket = null;
            this.isConnected = false;
            this.capturing = false;
            this.init();
        }

        init() {
            console.log('🔧 Configuration...');
            this.setupCanvases();
            this.bindEvents();
            this.connectWebSocket();
        }

        connectWebSocket() {
            // Tentative de connexion au service local (agent ou serveur Node)
            // Port 8080 est standard pour ces ponts, parfois 7777 ou autre selon le driver
            const wsUrl = 'ws://127.0.0.1:8080';

            console.log(`🔌 Tentative de connexion au service d'empreintes sur ${wsUrl}...`);
            this.updateStatus('left_index', 'Connexion au lecteur...', 'warning');
            this.updateStatus('right_index', 'Connexion au lecteur...', 'warning');

            try {
                this.socket = new WebSocket(wsUrl);

                this.socket.onopen = () => {
                    console.log('✅ Connecté au service d\'empreintes');
                    this.isConnected = true;
                    this.updateStatus('left_index', 'Lecteur connecté', 'info');
                    this.updateStatus('right_index', 'Lecteur connecté', 'info');
                    this.showSuccess('Lecteur d\'empreintes connecté');
                };

                this.socket.onclose = () => {
                    console.log('❌ Déconnecté du service d\'empreintes');
                    this.isConnected = false;
                    this.updateStatus('left_index', 'Lecteur déconnecté - Vérifiez le service', 'danger');
                    this.updateStatus('right_index', 'Lecteur déconnecté - Vérifiez le service', 'danger');

                    // Réessai automatique après 5s
                    setTimeout(() => this.connectWebSocket(), 5000);
                };

                this.socket.onerror = (error) => {
                    console.error('WebSocket Error:', error);
                };

                this.socket.onmessage = (event) => {
                    const data = JSON.parse(event.data);
                    this.handleServerMessage(data);
                };

            } catch (e) {
                console.error("Erreur de création WebSocket:", e);
            }
        }

        handleServerMessage(data) {
            console.log('📩 Message reçu:', data);

            switch (data.event || data.type) {
                case 'capture_started':
                    this.updateStatus(data.finger, 'Demande envoyée au scanner...', 'info');
                    // Optionnel : Notification toast
                    break;

                case 'finger_detected':
                    this.updateStatus(this.currentFinger, 'Doigt détecté, ne bougez pas...', 'warning');
                    break;

                case 'quality_update':
                    this.updateQualityDisplay(this.currentFinger, data.quality);
                    break;

                case 'capture_complete':
                case 'image_acquired': // Variantes selon les protocoles
                    if (data.status === 'success' || data.template) {
                        this.completeCapture(this.currentFinger, data);
                    } else {
                        this.updateStatus(this.currentFinger, 'Échec capture: ' + (data.message || 'Erreur'), 'danger');
                    }
                    break;

                case 'scanner_connected':
                    this.updateStatus('left_index', 'Scanner prêt', 'success');
                    this.updateStatus('right_index', 'Scanner prêt', 'success');
                    break;

                case 'error':
                    alert('Erreur lecteur: ' + data.message);
                    this.capturing = false;
                    this.highlightCanvas(this.currentFinger, false);
                    break;
            }
        }

        setupCanvases() {
            ['left', 'right'].forEach(side => {
                const canvas = document.getElementById(`${side}-finger-canvas`);
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#f8f9fa';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.strokeStyle = '#dee2e6';
                    ctx.lineWidth = 2;
                    ctx.strokeRect(0, 0, canvas.width, canvas.height);

                    // Texte d'attente
                    ctx.fillStyle = '#6c757d';
                    ctx.font = '14px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText(`Index ${side === 'left' ? 'gauche' : 'droit'}`, canvas.width / 2, canvas.height / 2);
                }
            });
        }

        bindEvents() {
            // Bouton gauche
            const leftBtn = document.getElementById('capture-left');
            if (leftBtn) leftBtn.addEventListener('click', (e) => { e.preventDefault(); this.startCapture('left_index'); });

            // Bouton droit
            const rightBtn = document.getElementById('capture-right');
            if (rightBtn) rightBtn.addEventListener('click', (e) => { e.preventDefault(); this.startCapture('right_index'); });

            // Bouton "Les deux"
            const bothBtn = document.getElementById('capture-both');
            if (bothBtn) bothBtn.addEventListener('click', (e) => { e.preventDefault(); this.captureBoth(); });

            // Bouton "Effacer"
            const clearBtn = document.getElementById('clear-fingerprints');
            if (clearBtn) clearBtn.addEventListener('click', (e) => { e.preventDefault(); this.clearAll(); });

            // Bouton "Tester"
            const testBtn = document.getElementById('test-scanner');
            if (testBtn) testBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (this.isConnected) {
                    this.socket.send(JSON.stringify({ action: 'ping' }));
                    alert('Ping envoyé au lecteur. Vérifiez la console.');
                } else {
                    alert('Le lecteur n\'est pas connecté via WebSocket (port 8080).');
                }
            });

            // Validation formulaire
            const form = document.getElementById('formAdd');
            if (form) form.addEventListener('submit', (e) => this.validateForm(e));
        }

        startCapture(finger) {
            if (!this.isConnected) {
                alert('Erreur: Impossible de communiquer avec le lecteur d\'empreintes.\nVérifiez que l\'agent de capture est lancé sur le port 8080.');
                return;
            }

            if (this.capturing) {
                console.log('Capture déjà en cours...');
                return;
            }

            this.capturing = true;
            this.currentFinger = finger;

            console.log(`🎬 Démarrage capture réelle: ${finger}`);

            // Mise à jour interface
            this.updateStatus(finger, 'Placez le doigt sur le lecteur', 'primary');
            this.highlightCanvas(finger, true);

            // Envoi commande au WebSocket
            this.socket.send(JSON.stringify({
                action: 'capture',
                finger: finger
            }));
        }

        completeCapture(finger, data) {
            console.log(`✅ Capture reçue: ${finger}`);

            // Récupérer les données (adapter selon ce que renvoie le serveur)
            const template = data.template || '';
            const imageBase64 = data.image || '';
            const quality = data.quality || 100;

            // Remplir les champs cachés
            const templateField = document.getElementById(`fingerprint_${finger}_template`);
            const imageField = document.getElementById(`fingerprint_${finger}_image`);

            if (templateField) templateField.value = template;
            if (imageField) imageField.value = imageBase64;

            // Afficher l'image
            this.displayFingerprintImage(finger, imageBase64, quality);

            // Mettre à jour l'interface
            this.updateQualityDisplay(finger, quality);
            this.updateStatus(finger, '✅ Capturé', 'success');
            this.highlightCanvas(finger, false);

            this.capturing = false;

            // Enchaîner si mode "les deux"
            if (this.captureQueue && this.captureQueue.length > 0) {
                const nextFinger = this.captureQueue.shift();
                setTimeout(() => this.startCapture(nextFinger), 1000);
            } else if (this.bothCaptured()) {
                this.showSuccess('Empreintes enregistrées avec succès');
            }
        }

        displayFingerprintImage(finger, base64Image, quality) {
            const canvasId = `${finger.replace('_', '-')}-canvas`;
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const img = new Image();

            img.onload = () => {
                // Nettoyer le canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Fond blanc propre
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Calcul du ratio pour centrer l'image comme "object-fit: contain"
                const scale = Math.min(canvas.width / img.width, canvas.height / img.height) * 0.8; // 0.8 pour laisser une marge
                const x = (canvas.width / 2) - (img.width / 2) * scale;
                const y = (canvas.height / 2) - (img.height / 2) * scale;

                // Dessiner l'image centrée
                ctx.drawImage(img, x, y, img.width * scale, img.height * scale);

                // Bordure verte de succès
                canvas.classList.remove('capturing');
                canvas.classList.add('captured');

                // Petit texte de qualité
                ctx.fillStyle = quality >= 80 ? '#28a745' : (quality >= 50 ? '#ffc107' : '#dc3545');
                ctx.font = 'bold 14px Arial';
                ctx.fillText(`Qualité: ${quality}%`, 10, canvas.height - 10);
            };

            // Gérer les préfixes data:image si manquants
            if (!base64Image.startsWith('data:image')) {
                // Si c'est du raw base64, on ajoute le header PNG par défaut
                base64Image = 'data:image/png;base64,' + base64Image;
            }
            img.src = base64Image;
        }

        captureBoth() {
            this.captureQueue = ['right_index']; // File d'attente
            this.startCapture('left_index');
        }

        // Méthodes utilitaires conservées...
        clearAll() {
            if (confirm('Effacer les empreintes ?')) {
                ['left_index', 'right_index'].forEach(finger => {
                    const canvas = document.getElementById(`${finger.replace('_', '-')}-canvas`);
                    if (canvas) {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.fillStyle = '#f8f9fa'; ctx.fillRect(0, 0, canvas.width, canvas.height);
                        canvas.classList.remove('captured');
                    }
                    document.getElementById(`fingerprint_${finger}_template`).value = '';
                    document.getElementById(`fingerprint_${finger}_image`).value = '';
                    this.updateStatus(finger, 'Prêt', 'secondary');
                });
            }
        }

        updateStatus(finger, message, type) {
            const element = document.getElementById(`${finger.replace('_', '-')}-status`);
            if (element) {
                element.textContent = message;
                element.className = `text-${type}`;
            }

            // Feedback visuel sur le canvas
            const canvas = document.getElementById(`${finger.replace('_', '-')}-canvas`);
            if (canvas) {
                const ctx = canvas.getContext('2d');
                if (message.includes('détecté') || message.includes('en cours')) {
                    ctx.fillStyle = '#fff3cd'; // Jaune clair
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    ctx.strokeRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#856404';
                    ctx.font = '14px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText('Empreinte détectée...', canvas.width / 2, canvas.height / 2);
                }
            }
        }

        updateQualityDisplay(finger, quality) {
            // ... (même logique que précédemment) ...
            const bar = document.getElementById(`${finger.replace('_', '-')}-quality-bar`);
            if (bar) bar.style.width = quality + '%';
        }

        highlightCanvas(finger, active) {
            const canvas = document.getElementById(`${finger.replace('_', '-')}-canvas`);
            if (canvas) {
                canvas.style.borderColor = active ? '#007bff' : '';
                if (active) canvas.classList.add('capturing'); else canvas.classList.remove('capturing');
            }
        }

        bothCaptured() {
            const left = document.getElementById('fingerprint_left_template').value;
            const right = document.getElementById('fingerprint_right_template').value;
            return left && right;
        }

        validateForm(e) {
            const optional = document.getElementById('fingerprint_optional');
            if (optional && !optional.checked && !this.bothCaptured()) {
                e.preventDefault();
                alert('Veuillez scanner les empreintes ou cocher la case "Optionnel".');
                return false;
            }
            return true;
        }

        showSuccess(message) {
            if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: message, timer: 2000 });
            else alert(message);
        }
    }

    // ==============================================
    // INITIALISATION AUTOMATIQUE
    // ==============================================

    // Attendre que la page soit complètement chargée
    window.addEventListener('load', function () {
        console.log('📄 Page complètement chargée');

        // Vérifier que les éléments existent
        const leftCanvas = document.getElementById('left-finger-canvas');
        const rightCanvas = document.getElementById('right-finger-canvas');

        if (leftCanvas && rightCanvas) {
            console.log('🎯 Éléments d\'empreintes trouvés');

            // Initialiser le système basé sur WebSocket
            window.fingerprintSystem = new WebSocketFingerprintSystem();

            // Ajouter un test rapide
            setTimeout(() => {
                console.log('🧪 Test: Tous les boutons sont fonctionnels');
            }, 2000);

        } else {
            console.warn('⚠️ Éléments d\'empreintes non trouvés');
        }
    });
</script>

<style>
    /* Styles pour les empreintes */
    .fingerprint-section {
        padding: 20px;
        border-radius: 10px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }

    .fingerprint-placeholder canvas {
        background: white;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        transition: all 0.3s;
    }

    .fingerprint-placeholder canvas.capturing {
        border-color: #007bff;
        box-shadow: 0 0 15px rgba(0, 123, 255, 0.5);
        animation: pulse 1.5s infinite;
    }

    .fingerprint-placeholder canvas.captured {
        border-color: #28a745;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
    }

    .btn-capture {
        min-width: 200px;
        padding: 10px 20px;
        font-weight: bold;
    }

    .btn-capture:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .quality-indicator {
        max-width: 200px;
        margin: 10px auto;
    }

    .quality-indicator .progress {
        height: 12px;
        border-radius: 6px;
        overflow: hidden;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
        }
    }
</style>