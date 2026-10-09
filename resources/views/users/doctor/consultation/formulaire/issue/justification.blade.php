<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Consultation de <span
                        style="text-transform: capitalize;">{{ $consultation->patient->user->name }}
                        {{ $consultation->patient->user->prenom }}</span> terminée</h4>
                <h6 class="box-subtitle">Veuillez donner une justification et choisir une orientation si nécessaire.</h6>
            </div>
        </div>

        <form id="justificationForm">
            @csrf
            <h3 class="text-dark"><b>Informations requises</b></h3>
            <div class="box">
                <div class="box-body ribbon-box">
                    <section class="m-60">

                        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">

                        <div class="row mt-20" id="questAdd">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="admission_patient" class="form-label"> 
                                        <b>Voulez-vous orienter le patient (Soins infirmier, Laboratoire, autre service) ? </b> 
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group">
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="Ad01" value="Oui" name="admission_patient">
                                        <label for="Ad01" class="me-30">Oui</label>
                                        <input type="radio" id="Ad02" value="Non" name="admission_patient" checked>
                                        <label for="Ad02" class="me-30">Non</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="admissionForm" style="display: none;">
                            <div class="box bb-3 border-info p-15" style="background-color: #f8fafc; border-radius: 8px;">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 13px;">
                                        <i class="fa fa-info-circle me-1"></i> 
                                        Les constantes du patient déjà prises seront automatiquement transmises au praticien ou à l'infirmier sélectionné.
                                    </div>
                                    <div class="mont-aff text-end">
                                        <label class="title mb-1 d-block text-muted" style="font-size: 12px;">Montant à payer à la Caisse</label>
                                        <h4 class="box-title text-primary mb-0">
                                            <input type="hidden" class="form-control" name="montant" id="montant" value="0">
                                            <b id="prix">0 Frs CFA</b>
                                        </h4>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <input type="hidden" name="type_admission_id" id="type_admission_id" value="Soins Infirmier" />
                                    <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}" />
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="service_id" class="form-label fw-bold"> Service : <span class="text-danger">*</span> </label>
                                                <select class="form-select" id="service_id" name="service_id" required>
                                                    <option value="">Chargement des services...</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="prestation_service_id" class="form-label fw-bold">Prestation / Soin : <span class="text-danger">*</span> </label>
                                                <select class="form-select" id="prestation_service_id" name="prestation_service_id" required>
                                                    <option value="">Sélectionner d'abord un service</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Champ Infirmier (affiché si Soins infirmiers) -->
                                        <div class="col-md-4" id="infirmierCol">
                                            <div class="form-group">
                                                <label for="infirmier_id" class="form-label fw-bold"> Infirmier(ère) : <span class="text-danger">*</span> </label>
                                                <select class="form-select" id="infirmier_id" name="infirmier_id" style="width: 100%;">
                                                    <option value="">Sélectionner un(e) infirmier(ère)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Champ Médecin / Agent de laboratoire (affiché si autre service) -->
                                        <div class="col-md-4" id="doctorCol" style="display: none;">
                                            <div class="form-group">
                                                <label for="doctor_id" id="doctorLabel" class="form-label fw-bold"> Médecin : <span class="text-danger">*</span> </label>
                                                <select class="form-select" id="doctor_id" name="doctor_id" style="width: 100%;">
                                                    <option value="">Sélectionner un médecin</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-15">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="justification" class="form-label fw-bold">Justification / Résumé de l'issue : <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <textarea name="justification" class="form-control" id="justification" cols="30" rows="6" placeholder="Veuillez préciser le motif de fin de consultation ou les détails de l'orientation..." required>{{ $consultation->registre == null ? '' : $consultation->registre->issue_consultation_justification }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                    </section>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-submit px-4 py-2">
                <i class="fa fa-save me-1"></i> Enregistrer
            </button>
        </form>
    </div>
</div>

<script>
    const admAdd = document.querySelectorAll('input[type=radio][name="admission_patient"]');
    const admissionForm = document.getElementById('admissionForm');

    Array.prototype.forEach.call(admAdd, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value === 'Oui') {
            admissionForm.style.display = 'block';
            if ($('#service_id').val()) {
                $('#service_id').trigger('change');
            }
        } else {
            admissionForm.style.display = 'none';
        }
    }

    // Charger les services hospitaliers
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('doctor.hopitalservices') }}",
            method: 'GET',
            success: function (response) {
                var servicelist = $('#service_id');
                servicelist.empty();
                servicelist.append('<option value="">Sélectionner un service</option>');

                var sortedServices = (response || []).slice().sort(function (a, b) {
                    var nameA = (a.service && a.service.libelle) ? a.service.libelle.toLowerCase() : (a.libelle ? a.libelle.toLowerCase() : '');
                    var nameB = (b.service && b.service.libelle) ? b.service.libelle.toLowerCase() : (b.libelle ? b.libelle.toLowerCase() : '');
                    var isInfA = nameA.includes('soins') || nameA.includes('infirmier');
                    var isInfB = nameB.includes('soins') || nameB.includes('infirmier');
                    if (isInfA && !isInfB) return -1;
                    if (!isInfA && isInfB) return 1;
                    return nameA.localeCompare(nameB);
                });

                var defaultService = null;
                $.each(sortedServices, function (key, service) {
                    var libelle = service.service ? service.service.libelle : service.libelle;
                    if (libelle) {
                        var isInf = libelle.toLowerCase().includes('soins') || libelle.toLowerCase().includes('infirmier');
                        if (isInf && !defaultService) {
                            defaultService = libelle;
                        }
                        servicelist.append('<option value="' + libelle + '">' + libelle + '</option>');
                    }
                });

                if (defaultService) {
                    servicelist.val(defaultService).trigger('change');
                }
            },
            error: function (xhr) {
                console.error("Erreur chargement services:", xhr);
            }
        });
    });

    // Lors du changement de service : adapter prestations, infirmiers ou médecins
    $(document).ready(function () {
        $('#service_id').on('change', function () {
            var service = $(this).val();
            var serviceLower = service ? service.toLowerCase() : '';
            var prestationlist = $('#prestation_service_id');
            prestationlist.empty();
            prestationlist.append('<option value="">Chargement des prestations...</option>');

            var isSoins = serviceLower.includes('soin') || serviceLower.includes('infirm');
            var isLabo = serviceLower.includes('labo') || serviceLower.includes('biolog') || serviceLower.includes('analyse');

            if (isSoins) {
                $('#type_admission_id').val('Soins Infirmier');
                $('#infirmierCol').show();
                $('#doctorCol').hide();
            } else if (isLabo) {
                $('#type_admission_id').val(service || 'Laboratoire');
                $('#doctorLabel').html('Agent de Laboratoire : <span class="text-danger">*</span>');
                $('#doctorCol').show();
                $('#infirmierCol').hide();
            } else {
                $('#type_admission_id').val(service || 'Consultation');
                $('#doctorLabel').html('Médecin : <span class="text-danger">*</span>');
                $('#doctorCol').show();
                $('#infirmierCol').hide();
            }

            if (service) {
                // 1. Charger prestations
                $.ajax({
                    url: '{{ route("doctor.prestations", ":id") }}'.replace(':id', encodeURIComponent(service)),
                    type: 'GET',
                    success: function (response) {
                        prestationlist.empty();
                        prestationlist.append('<option value="">Sélectionner</option>');
                        $.each(response || [], function (key, prestation) {
                            var lib = (prestation.prestation_service && prestation.prestation_service.libelle) ? prestation.prestation_service.libelle : (prestation.libelle || 'Prestation');
                            prestationlist.append('<option value="' + prestation.id + '">' + lib + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        prestationlist.empty().append('<option value="">Aucune prestation trouvée</option>');
                    }
                });

                // 2. Charger infirmiers (si soins)
                var infirmierlist = $('#infirmier_id');
                infirmierlist.empty().append('<option value="">Chargement...</option>');
                $.ajax({
                    url: '{{ route("doctor.infirmiers", ":id") }}'.replace(':id', encodeURIComponent(service)),
                    type: 'GET',
                    success: function (response) {
                        infirmierlist.empty().append('<option value="">Sélectionner un(e) infirmier(ère)</option>');
                        $.each(response || [], function (key, infirmier) {
                            var infName = infirmier.user ? (infirmier.user.name + ' ' + (infirmier.user.prenom || '')) : ('Inf. #' + infirmier.id);
                            infirmierlist.append('<option value="' + infirmier.id + '">' + infName + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        infirmierlist.empty().append('<option value="">Sélectionner un(e) infirmier(ère)</option>');
                    }
                });

                // 3. Charger médecins / agents de labo (si autre service)
                var doctorList = $('#doctor_id');
                var placeholderDoc = isLabo ? "Sélectionner un agent de laboratoire" : "Sélectionner un médecin";
                doctorList.empty().append('<option value="">Chargement...</option>');
                $.ajax({
                    url: '{{ route("doctor.doctors", ":id") }}'.replace(':id', encodeURIComponent(service)),
                    type: 'GET',
                    success: function (response) {
                        doctorList.empty().append('<option value="">' + placeholderDoc + '</option>');
                        $.each(response || [], function (key, doc) {
                            var docName = doc.user ? (doc.user.name + ' ' + (doc.user.prenom || '')) : ('Dr. #' + doc.id);
                            var servName = (doc.service_hospital && doc.service_hospital.service) ? ' (' + doc.service_hospital.service.libelle + ')' : '';
                            doctorList.append('<option value="' + doc.id + '">' + docName + servName + '</option>');
                        });
                    },
                    error: function (xhr, status, error) {
                        doctorList.empty().append('<option value="">' + placeholderDoc + '</option>');
                    }
                });
            } else {
                prestationlist.empty().append('<option value="">Sélectionner d\'abord un service</option>');
            }
        });

        // Prix de la prestation
        $('#prestation_service_id').on('change', function () {
            var prestationServiceId = $(this).val();
            if (prestationServiceId) {
                $.ajax({
                    url: "{{ route('doctor.prix_prestation') }}",
                    method: 'GET',
                    data: { prestationServiceId: prestationServiceId },
                    success: function (response) {
                        var prix = parseFloat(response.prix) || 0;
                        $('#prix').text(prix.toLocaleString('fr-FR') + ' Frs CFA');
                        $('#montant').val(prix);
                    },
                    error: function () {
                        $('#prix').text('0 Frs CFA');
                        $('#montant').val(0);
                    }
                });
            } else {
                $('#prix').text('0 Frs CFA');
                $('#montant').val(0);
            }
        });
    });

    // Soumission du formulaire
    (function ($) {
        "use strict";

        $('#justificationForm').on('submit', function (e) {
            e.preventDefault();
            var consultationId = $('input[name="consultation_id"]').val();
            var patientId = $('input[name="patient_id"]').val();
            var justification = $('textarea[name="justification"]').val().trim();
            var admissionPatient = $('input[name="admission_patient"]:checked').val() || 'Non';
            var prestationServiceId = $('select[name="prestation_service_id"]').val();
            var doctorId = $('select[name="doctor_id"]').val();
            var infirmierId = $('select[name="infirmier_id"]').val();
            var serviceId = $('select[name="service_id"]').val();
            var typeAdmission = $('#type_admission_id').val();
            var montant = $('input[name="montant"]').val() || 0;

            if (justification === '' || !consultationId) {
                Swal.fire({
                    text: "Veuillez donner une justification svp !",
                    icon: "warning",
                    confirmButtonText: "OK",
                });
                return false;
            }

            if (admissionPatient === 'Oui') {
                if (!serviceId || !prestationServiceId) {
                    Swal.fire({
                        text: "Veuillez remplir les champs nécessaires pour l'admission / orientation !",
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return false;
                }

                var serviceLower = serviceId.toLowerCase();
                var isSoins = serviceLower.includes('soin') || serviceLower.includes('infirm');
                var isLabo = serviceLower.includes('labo') || serviceLower.includes('biolog') || serviceLower.includes('analyse');

                if (isSoins && !infirmierId) {
                    Swal.fire({
                        text: "Veuillez sélectionner un(e) infirmier(ère) pour les soins !",
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return false;
                }

                if (!isSoins && !doctorId) {
                    var errorMsg = isLabo ? "Veuillez sélectionner l'agent de laboratoire destinataire !" : "Veuillez sélectionner le médecin destinataire !";
                    Swal.fire({
                        text: errorMsg,
                        icon: "warning",
                        confirmButtonText: "OK",
                    });
                    return false;
                }
            }

            verifyJustification(justification, consultationId, admissionPatient, prestationServiceId, infirmierId, doctorId, serviceId, typeAdmission, patientId, montant);
        });

        function verifyJustification(justification, consultationId, admissionPatient, prestationServiceId, infirmierId, doctorId, serviceId, typeAdmission, patientId, montant) {
            var data = {
                "justification": justification,
                "consultation_id": consultationId,
                "doctor_id": doctorId,
                "infirmier_id": infirmierId,
                "service_id": serviceId,
                "type_admission_id": typeAdmission,
                "prestation_service_id": prestationServiceId,
                "patient_id": patientId,
                "admission_patient": admissionPatient,
                "montant": montant,
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('doctor.consultation.store.issue.justification') }}",
                data: data,
                success: function (response) {
                    if (response.status === 'error') {
                        Swal.fire({
                            text: response.message || "Erreur lors de l'enregistrement de l'issue.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                        $("#prescription").css("display", "none");
                        $("#formIssue").css("display", "block");
                    } else {
                        Swal.fire({
                            title: "Succès !",
                            text: response.message || "Issue de consultation validée !",
                            icon: "success",
                            confirmButtonText: "Continuer",
                        }).then(function() {
                            $("#formIssue").css("display", "none");
                            $("#prescription").css("display", "block");
                        });
                    }
                },
                error: function (xhr, status, error) {
                    var errorMsg = "Une erreur est survenue lors de l'enregistrement de l'issue de consultation.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: "Erreur",
                        text: errorMsg,
                        icon: "error",
                        button: "OK",
                    });
                }
            });
        }

    })(jQuery);

</script>
<style>
    .select2-selection {
        height: 34px !important;
    }
</style>