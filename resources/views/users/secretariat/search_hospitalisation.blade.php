@extends('layouts.dashboard')

@section('content') 
<div class="container-fluid px-30 py-20">
    
    <!-- En-tête de la page -->
    <div class="card border-0 shadow-sm rounded-20 mb-30 p-25 bg-white">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h3 class="fw-bold text-dark mb-1 fs-24">
                    <i class="fa-solid fa-magnifying-glass text-primary me-2"></i> Recherche & Affectation Directe des Patients
                </h3>
                <p class="text-muted mb-0 fs-14">
                    Recherchez un dossier existant par nom, prénom ou téléphone. Sélectionnez le patient pour mettre à jour sa fiche ou l'affecter immédiatement à un service, un acte/prestation, un médecin ou un infirmier.
                </p>
            </div>
            <div>
                <a href="{{ route('secretariat.patient.create') }}" class="btn btn-primary rounded-12 px-20 py-10 fw-semibold shadow-sm">
                    <i class="fa-solid fa-user-plus me-2"></i> Créer un Nouveau Patient
                </a>
            </div>
        </div>
    </div>

    <!-- Formulaire de recherche -->
    <div class="card border-0 shadow-sm rounded-20 p-25 bg-white mb-30">
        <h5 class="fw-bold text-dark mb-20 fs-16"><i class="fa-solid fa-filter text-info me-2"></i> Critères de Recherche</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark fs-13">N° de Téléphone</label>
                <input type="search" class="form-control h-45 rounded-10" min="10" max="10" autofocus
                    data-inputmask="'mask': ['9999999999', '99 99 99 99 99']" data-mask=""
                    id="no_telephone" name="no_telephone" placeholder="Ex: 0707000000">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark fs-13">Nom & Prénom(s)</label>
                <input type="search" name="fullname" id="fullname" placeholder="Saisir le nom et/ou prénom(s)"
                    class="form-control h-45 rounded-10" oninput="convertToUppercase()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark fs-13">Date de Naissance</label>
                <div class="input-group">
                    <input type="text" name="birth_date" id="birth_date" class="form-control h-45 rounded-start-10"
                        data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" placeholder="dd/mm/yyyy">
                    <button type="button" id="search-button" class="btn btn-primary h-45 px-25 rounded-end-10 fw-bold shadow-sm">
                        <i class="fa-solid fa-search me-1"></i> Rechercher
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Conteneur des Résultats de Recherche -->
    <div id="resultat-recherche-patient" class="mb-30" style="display: none;"></div>

    <!-- Formulaire d'Affectation et de Mise à Jour du Patient -->
    <div id="update-form" style="display: none;" class="card border-0 shadow-sm rounded-20 p-25 bg-white mb-30">
        <form id="editPatient">
            @csrf
            <input type="hidden" name="id" id="patient_id" />
            <input type="hidden" name="admission_patient_up" value="Oui" />
            
            <div class="d-flex align-items-center justify-content-between border-bottom pb-15 mb-25">
                <h4 class="fw-bold text-primary mb-0">
                    <i class="fa-solid fa-user-gear me-2"></i> Modifier & Affecter le Patient
                </h4>
                <div class="badge bg-light-primary text-primary px-20 py-10 rounded-pill fs-15 fw-bold border">
                    Tarif Prestation : <span id="prix_up" class="text-danger fw-bold ms-1">0 FCFA</span>
                    <input type="hidden" name="montant" id="montant_up" value="0">
                </div>
            </div>

            <!-- Étape 1 : Identité & Coordonnées -->
            <div class="p-20 bg-light rounded-16 mb-20 border">
                <h5 class="fw-bold text-dark mb-15 fs-15"><i class="fa-solid fa-id-card text-primary me-2"></i> Identité du Patient</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold fs-12 text-muted">Nom <span class="text-danger">*</span></label>
                        <input type="text" name="name_up" id="name_up" class="form-control h-45 rounded-10" oninput="convertToUppercase()">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold fs-12 text-muted">Prénom(s) <span class="text-danger">*</span></label>
                        <input type="text" name="prenom_up" id="prenom_up" class="form-control h-45 rounded-10" oninput="convertToUppercase()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted">Email</label>
                        <input type="email" name="email_up" id="email_up" class="form-control h-45 rounded-10">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold fs-12 text-muted">Sexe</label>
                        <input type="text" name="gender_up" id="gender_up" class="form-control h-45 rounded-10" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold fs-12 text-muted">Date de naissance</label>
                        <input type="text" name="birth_date_up" id="birth_date_up" class="form-control h-45 rounded-10">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold fs-12 text-muted">N° Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="telephone" id="telephone" class="form-control h-45 rounded-10" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold fs-12 text-muted">Lieu d'habitation</label>
                        <input type="text" name="residence_habituelle_up" id="residence_habituelle_up" class="form-control h-45 rounded-10">
                    </div>
                </div>
            </div>

            <!-- Étape 2 : Service Médical, Prestation & Intervenant -->
            <div class="p-20 bg-light-primary rounded-16 mb-25 border border-primary">
                <h5 class="fw-bold text-primary mb-15 fs-15">
                    <i class="fa-solid fa-stethoscope me-2"></i> Affectation & Prestation Médicale
                </h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark fs-13">Service Médical <span class="text-danger">*</span></label>
                        <select name="service_id_up" id="service_id_up" class="form-select h-45 rounded-10 fw-semibold">
                            <option value="">-- Sélectionner un service --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark fs-13">Prestation / Acte Médical <span class="text-danger">*</span></label>
                        <select name="prestation_service_id" id="prestation_service_id" class="form-select h-45 rounded-10 fw-semibold">
                            <option value="">-- Sélectionner un service d'abord --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark fs-13">Infirmier(ère) De Service</label>
                        <select name="infirmier_id" id="infirmier_id_up" class="form-select h-45 rounded-10 fw-semibold">
                            <option value="">-- Aucun ou Choisir Infirmier --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark fs-13">Médecin Traitant</label>
                        <select name="doctor_id" id="doctor_id_up" class="form-select h-45 rounded-10 fw-semibold">
                            <option value="">-- Aucun ou Choisir Médecin --</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-bold text-dark fs-13">Motif de Consultation / Observation</label>
                        <input type="text" name="motif_consultation" id="motif_consultation" placeholder="Ex: Prise de tension, Consultation générale, Soins pansement..." class="form-control h-45 rounded-10">
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="form-check form-switch pt-20">
                            <input class="form-check-input" type="checkbox" id="GRU05" name="gratuite" value="gratuit" style="width: 45px; height: 22px; cursor: pointer;">
                            <label class="form-check-label fw-bold text-danger ms-2 fs-14" for="GRU05" style="cursor: pointer;">
                                <i class="fa-solid fa-gift me-1"></i> Gratuité (GTC)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons de Validation -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" onclick="$('#update-form').slideUp();" class="btn btn-light px-25 rounded-10 fw-semibold">Annuler</button>
                <button type="submit" class="btn btn-primary px-30 rounded-10 fw-bold shadow-sm">
                    <i class="fa-solid fa-check me-2"></i> Enregistrer les Modifications & Affecter
                </button>
            </div>
        </form>
    </div>

</div>

@push('js')
<script>
    var allServicesList = [];

    function formatUserName(user, defaultLabel) {
        if (!user) return defaultLabel || '';
        var name = (user.name && user.name !== 'null' && user.name !== 'undefined') ? user.name.toString().trim() : '';
        var prenom = (user.prenom && user.prenom !== 'null' && user.prenom !== 'undefined') ? user.prenom.toString().trim() : '';
        var full = (name + ' ' + prenom).trim();
        return full || defaultLabel || '';
    }

    function isMaleGender(gender) {
        if (!gender) return false;
        var g = gender.toString().trim().toLowerCase();
        return g === 'm' || g === 'masculin' || g === 'homme' || g.indexOf('masc') !== -1;
    }

    function isGynecoService(serviceLibelle) {
        if (!serviceLibelle) return false;
        var s = serviceLibelle.toString().toLowerCase();
        return /gyn/i.test(s) || /obst/i.test(s);
    }

    function populateServices(gender) {
        var servicelist = $('#service_id_up');
        var selectedVal = servicelist.val();
        servicelist.empty();
        servicelist.append('<option value="">-- Sélectionner un service --</option>');
        
        var isMale = isMaleGender(gender);

        $.each(allServicesList, function(key, service) {
            if (service && service.service) {
                var libelle = service.service.libelle;
                if (isMale && isGynecoService(libelle)) {
                    return true; // Exclure Gynécologie-Obstétrique pour les hommes
                }
                servicelist.append('<option value="' + libelle + '">' + libelle + '</option>');
            }
        });

        if (selectedVal && servicelist.find('option[value="' + selectedVal + '"]').length > 0) {
            servicelist.val(selectedVal);
        } else {
            servicelist.val('');
            $('#service_id_up').trigger('change');
        }
    }

    $(document).ready(function() {
        
        // Charger la liste des services disponibles
        $.ajax({
            url: "{{ route('secretariat.hopitalservices') }}",
            method: 'GET',
            success: function(response) {
                allServicesList = response;
                var currentGender = $('#gender_up').val();
                populateServices(currentGender);
            }
        });

        // Charger infirmiers au chargement initial
        loadInfirmiers('all');

        // Gérer le changement de service
        $('#service_id_up').change(function() {
            var serviceName = $(this).val();
            var doctorSelect = $('#doctor_id_up');
            var prestationSelect = $('#prestation_service_id');
            
            doctorSelect.empty();
            prestationSelect.empty();
            $('#prix_up').text('0 FCFA');
            $('#montant_up').val('0');

            if (serviceName) {
                // Charger les prestations du service
                var prestUrl = "{{ route('secretariat.prestations', ':service') }}".replace(':service', encodeURIComponent(serviceName));
                $.ajax({
                    url: prestUrl,
                    method: 'GET',
                    success: function(response) {
                        prestationSelect.append('<option value="">-- Sélectionner une prestation --</option>');
                        if (Array.isArray(response) && response.length > 0) {
                            $.each(response, function(key, item) {
                                var label = (item.prestation_service && item.prestation_service.libelle) ? item.prestation_service.libelle : (item.description || 'Prestation');
                                prestationSelect.append('<option value="' + item.id + '" data-prix="' + (item.prix || 0) + '">' + label + '</option>');
                            });
                        } else {
                            prestationSelect.append('<option value="">Aucune prestation disponible pour ce service</option>');
                        }
                    }
                });

                // Charger les médecins
                $.ajax({
                    url: "{{ route('secretariat.medecins') }}",
                    method: 'GET',
                    data: { service_name: serviceName },
                    success: function(response) {
                        doctorSelect.append('<option value="">-- Aucun médecin --</option>');
                        if (response.medecins && response.medecins.length > 0) {
                            $.each(response.medecins, function(key, doctor) {
                                var docName = formatUserName(doctor.user, 'Médecin');
                                doctorSelect.append('<option value="' + doctor.id + '">' + docName + '</option>');
                            });
                        }
                    }
                });

                // Charger les infirmiers du service
                loadInfirmiers(serviceName);
            } else {
                doctorSelect.append('<option value="">-- Choisir un service d\'abord --</option>');
                prestationSelect.append('<option value="">-- Choisir un service d\'abord --</option>');
                loadInfirmiers('all');
            }
        });

        // Gérer le tarif au choix de la prestation
        $('#prestation_service_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var prestationId = $(this).val();
            var prix = selectedOption.data('prix');

            if ($('#GRU05').is(':checked')) {
                $('#prix_up').text('0 FCFA (Gratuit GTC)');
                $('#montant_up').val('0');
                return;
            }

            if (prix !== undefined && prix !== null && prix !== '') {
                $('#prix_up').text(prix + ' FCFA');
                $('#montant_up').val(prix);
            } else if (prestationId) {
                $.ajax({
                    url: "{{ route('secretariat.prix_prestation') }}",
                    method: 'GET',
                    data: { prestationServiceId: prestationId },
                    success: function(response) {
                        var pVal = response.prix || 0;
                        $('#prix_up').text(pVal + ' FCFA');
                        $('#montant_up').val(pVal);
                    }
                });
            } else {
                $('#prix_up').text('0 FCFA');
                $('#montant_up').val('0');
            }
        });

        // Gérer l'option Gratuité (GTC)
        $('#GRU05').change(function() {
            if ($(this).is(':checked')) {
                $('#prix_up').text('0 FCFA (Gratuit GTC)');
                $('#montant_up').val('0');
            } else {
                $('#prestation_service_id').trigger('change');
            }
        });

        // Déclencher la recherche sur la touche Entrée
        $('#no_telephone, #fullname, #birth_date').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#search-button').click();
            }
        });

        function loadInfirmiers(serviceName) {
            var infirmierSelect = $('#infirmier_id_up');
            infirmierSelect.empty();
            var url = "{{ route('secretariat.infirmiers', ':service') }}".replace(':service', encodeURIComponent(serviceName));

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    infirmierSelect.append('<option value="">-- Aucun infirmier --</option>');
                    if (Array.isArray(response) && response.length > 0) {
                        $.each(response, function(key, inf) {
                            var infName = formatUserName(inf.user, 'Infirmier(ère)');
                            infirmierSelect.append('<option value="' + inf.id + '">' + infName + '</option>');
                        });
                    } else if (response.infirmiers && response.infirmiers.length > 0) {
                        $.each(response.infirmiers, function(key, inf) {
                            var infName = formatUserName(inf.user, 'Infirmier(ère)');
                            infirmierSelect.append('<option value="' + inf.id + '">' + infName + '</option>');
                        });
                    }
                }
            });
        }

        // Lancer la recherche
        $('#search-button').click(function() {
            var telephone = $('#no_telephone').val();
            var fullname = $('#fullname').val();
            var birth_date = $('#birth_date').val();

            if (!telephone && !fullname && !birth_date) {
                Swal.fire({
                    text: "Veuillez saisir au moins un critère de recherche !",
                    icon: "warning",
                    confirmButtonColor: '#005AEC'
                });
                return false;
            }

            $.ajax({
                url: "{{ route('secretariat.searchPatients') }}",
                method: 'GET',
                data: {
                    telephone: telephone,
                    fullname: fullname,
                    birth_date: birth_date
                },
                success: function(response) {
                    var patients = response.patients;

                    if (patients && patients.length > 0) {
                        Swal.fire({
                            text: patients.length + " Patient(s) trouvé(s).",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });

                        var html = '<div class="card border-0 shadow-sm rounded-20 p-20 bg-white">';
                        html += '<h5 class="fw-bold text-dark mb-15 fs-16"><i class="fa-solid fa-list text-primary me-2"></i> Résultats de la recherche (' + patients.length + ')</h5>';
                        html += '<div class="table-responsive"><table class="table table-hover align-middle mb-0">';
                        html += '<thead class="table-dark"><tr>';
                        html += '<th>N° Dossier</th>';
                        html += '<th>Nom & Prénom(s)</th>';
                        html += '<th>Date de naissance</th>';
                        html += '<th>Téléphone</th>';
                        html += '<th>Sexe</th>';
                        html += '<th class="text-center">Actions</th>';
                        html += '</tr></thead><tbody>';

                        for (var i = 0; i < patients.length; i++) {
                            var p = patients[i];
                            var fullnameStr = formatUserName(p.user, 'N/A');
                            var detailUrl = "{{ route('secretariat.patient.detail', ':id') }}".replace(':id', p.id);

                            html += '<tr>';
                            html += '<td><span class="badge bg-light-primary text-primary fw-bold fs-13 px-3 py-2 rounded-pill">' + (p.code_patient || 'N/A') + '</span></td>';
                            html += '<td><span class="fw-bold text-dark fs-14">' + fullnameStr + '</span></td>';
                            html += '<td><span class="text-muted fs-13">' + (p.birth_date || 'N/A') + '</span></td>';
                            html += '<td><span class="fw-semibold text-dark fs-13">' + (p.telephone || 'N/A') + '</span></td>';
                            html += '<td><span class="badge bg-light text-dark border fs-12 px-2 py-1">' + (p.gender || 'N/A') + '</span></td>';
                            html += '<td class="text-center">';
                            html += '<button type="button" class="btn btn-sm btn-primary me-2 rounded-8 fw-semibold" onclick="showUpdateForm(' + p.id + ')"><i class="fa-solid fa-user-gear me-1"></i> Sélectionner & Affecter</button>';
                            html += '<a href="' + detailUrl + '" class="btn btn-sm btn-info text-white rounded-8 fw-semibold" title="Voir Fiche"><i class="fa-solid fa-eye me-1"></i> Fiche Patient</a>';
                            html += '</td>';
                            html += '</tr>';
                        }

                        html += '</tbody></table></div></div>';
                        $('#resultat-recherche-patient').html(html).slideDown();

                        if (patients.length === 1) {
                            showUpdateForm(patients[0].id);
                        }
                    } else {
                        Swal.fire({
                            text: "Aucun patient ne correspond aux critères saisis.",
                            icon: "error",
                            confirmButtonColor: '#005AEC'
                        });
                        $('#resultat-recherche-patient').slideUp();
                        $('#update-form').slideUp();
                    }
                },
                error: function(xhr) {
                    var errorMsg = "Une erreur est survenue lors de la recherche.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        text: errorMsg,
                        icon: "error",
                        confirmButtonColor: '#005AEC'
                    });
                }
            });
        });

        // Soumission de la mise à jour & affectation du patient
        $('#editPatient').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var patientId = $('#patient_id').val();

            $.ajax({
                url: "{{ route('secretariat.patient.updatepatient', ['id' => ':id']) }}".replace(':id', patientId),
                method: 'PUT',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        text: response.success || "Patient mis à jour et affecté avec succès !",
                        icon: "success",
                        confirmButtonColor: '#005AEC'
                    }).then(function() {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var errorText = "Une erreur s'est produite lors de l'affectation.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errs = Object.values(xhr.responseJSON.errors).flat();
                        errorText = errs.join('<br>');
                    }
                    Swal.fire({
                        html: errorText,
                        icon: "error",
                        confirmButtonColor: '#005AEC'
                    });
                }
            });
        });
    });

    // Fonction pour remplir le formulaire avec le patient sélectionné
    function showUpdateForm(patientId) {
        $.ajax({
            url: "{{ route('secretariat.patient.showpatient', ['id' => ':id']) }}".replace(':id', patientId),
            method: 'GET',
            data: { patient_id: patientId },
            success: function(response) {
                var patient = response.patient;
                if (patient) {
                    var userName = (patient.user && patient.user.name) ? patient.user.name : '';
                    var userPrenom = (patient.user && patient.user.prenom) ? patient.user.prenom : '';
                    var userEmail = (patient.user && patient.user.email) ? patient.user.email : '';
                    var resHabituelleName = (patient.residence_habituelle && patient.residence_habituelle.name) ? patient.residence_habituelle.name : '';

                    $('#patient_id').val(patient.id);
                    $('#name_up').val(userName);
                    $('#prenom_up').val(userPrenom);
                    $('#email_up').val(userEmail);
                    $('#birth_date_up').val(patient.birth_date || '');
                    $('#gender_up').val(patient.gender || '');
                    $('#telephone').val(patient.telephone || '');
                    $('#residence_habituelle_up').val(resHabituelleName);

                    // Filtrer le service médical selon le sexe du patient
                    populateServices(patient.gender);

                    $('#update-form').slideDown();
                    $('html, body').animate({
                        scrollTop: $("#update-form").offset().top - 100
                    }, 500);
                }
            }
        });
    }

    function convertToUppercase() {
        var inputFull = document.getElementById('fullname');
        if (inputFull) inputFull.value = inputFull.value.toUpperCase();
        var inputName = document.getElementById('name_up');
        if (inputName) inputName.value = inputName.value.toUpperCase();
        var inputPrenom = document.getElementById('prenom_up');
        if (inputPrenom) inputPrenom.value = inputPrenom.value.toUpperCase();
    }
</script>
@endpush
@endsection