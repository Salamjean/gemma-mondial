<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Consultation de <span
                        style="text-transform: capitalize;">{{ $consultation->patient->user->name }}
                        {{ $consultation->patient->user->prenom }}</span> terminée</h4>
                <h6 class="box-subtitle">Veuillez donner une justification.</h6>
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="fonction" class="form-label"> <b>Voulez-vous orienter le patient pour des soins infirmier ? </b>  </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="form-group">
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="Ad01" value="Oui" name="admission_patient">
                                            <label for="Ad01" class="me-30">Oui</label>
                                            <input type="radio" id="Ad02" value="Non" name="admission_patient">
                                            <label for="Ad02" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="admissionForm">
                            <div class="box bb-3 border-info p-10">
                                <div class="mont-aff">
                                    <label class="title mb-4">Montant à payer à la Caisse</label>
                                    <h3 class="box-title">
                                        <input type="hidden" class="form-control" name="montant" id="montant">
                                        <b id="prix"> 0 Frs CFA</b>
                                    </h3>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="box-body">
                                        <input type="hidden" name="type_admission_id" value="Soins Infirmier" />
                                        <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}" />
                                        <div class="box-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="service_id" class="form-label fw-bold"> Service : <span class="danger">*</span> </label>
                                                        <select class="form-select" id="service_id" name="service_id">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="prestation_service_id" class="form-label fw-bold">Sélectionner le nom du soins : <span class="danger">*</span> </label>
                                                        <select class="form-select" id="prestation_service_id" name="prestation_service_id">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="infirmier_id" class="form-label fw-bold"> Infirmier(re) : <span class="danger">*</span> </label>
                                                        <select class="form-select" id="infirmier_id" name="infirmier_id" style="width: 100%;">
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="justification" class="form-label"> <br>Justification : </b><span
                                            class="danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <textarea name="justification" class="form-control" id="justification" cols="30" rows="10" required>{{ $consultation->registre == null ? '' : $consultation->registre->issue_consultation_justification }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                    </section>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-submit">Enregister</button>
        </form>
    </div>
</div>
<script>
    const admAdd = document.querySelectorAll('input[type=radio][name="admission_patient"]');
    const admissionForm = document.getElementById('admissionForm')

    admissionForm.style.display = 'none';

    Array.prototype.forEach.call(admAdd, function(radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            admissionForm.style.display = 'block';
        } else {
            admissionForm.style.display = 'none';
        }
    }
//services
$(document).ready(function() {
    $.ajax({
        url: "{{ route('doctor.hopitalservices') }}",
        method: 'GET',
        success: function(response) {
            var servicelist = $('#service_id');
            servicelist.empty();
            servicelist.append('<option value="">Selectionner</option>');

            $.each(response, function(key, service) {
                servicelist.append('<option value="' + service.service.libelle + '">' + service.service.libelle + '</option>');
            });
        },
        error: function(xhr) {
        }
    });
});
$(document).ready(function() {
        $('#service_id').change(function() {
            var service = $(this).val();
            var prestationlist = $('#prestation_service_id');
            prestationlist.empty();
            prestationlist.append('<option value="">Selectionner</option>');
            if (service) {
                $.ajax({
                    url: '{{ route("doctor.prestations", ":id") }}'.replace(':id', service),
                    type: 'GET',
                    success: function(response) {
                        $.each(response, function(key, prestation) {
                            prestationlist.append('<option value="' + prestation.id + '">' + prestation.prestation_service.libelle + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    });
    $(document).ready(function() {
        $('#service_id').change(function() {
            var service = $(this).val();
            var infirmierlist = $('#infirmier_id');
            infirmierlist.empty();
            infirmierlist.append('<option value="">Selectionner</option>');
            if (service) {
                $.ajax({
                    url: '{{ route("doctor.infirmiers", ":id") }}'.replace(':id', service),
                    type: 'GET',
                    success: function(response) {
                        $.each(response, function(key, infirmier) {
                            infirmierlist.append('<option value="' + infirmier.id + '">' + infirmier.user.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    });
    $(document).ready(function() {
        $('#prestation_service_id').change(function() {
            var prestationServiceId = $(this).val();
            $.ajax({
                url: "{{ route('doctor.prix_prestation') }}",
                method: 'GET',
                data: { prestationServiceId: prestationServiceId },
                success: function(response) {
                    var prix = response.prix;
                    $('#prix').text('Prix : ' + prix.toFixed(2) + ' Frs CFA');
                    $('#montant').val(response.prix);
                },
                error: function() {
                    $('#prix').val('Erreur lors de la récupération du prix.');
                }
            });
        });
    });
</script>
 <script>

        (function($) {
            "use strict";

            //justification
            $('#justificationForm').on('submit', function(e) {

                e.preventDefault();
                var consultationId = $('input[name="consultation_id"]').val();
                var patientId = $('input[name="patient_id"]').val();
                var justification = $('textarea[name="justification"]').val();
                var admissionPatient = $('input[name="admission_patient"]:checked').val() || 'Non';
                var prestationServiceId = $('select[name="prestation_service_id"]').val();
                var infirmierId = $('select[name="infirmier_id"]').val();
                var montant = $('input[name="montant"]').val();

                if (justification == '' || consultationId == '') {

                    Swal.fire({
                        text: "Veuillez donner une issue de consultation svp !",
                        icon: "error",
                        button: "ok",
                    });

                    return false;

                }
                if (admissionPatient === 'Oui' && (prestationServiceId === '' || infirmierId === '')) {
                        Swal.fire({
                            text: "Veuillez remplir les champs nécessaires pour l'admission!",
                            icon: "error",
                            button: "ok",
                        });
                        return false;
                    }

                verifyJustification(justification, consultationId, admissionPatient, prestationServiceId, infirmierId, patientId, montant)

            });

            function verifyJustification(justification, consultationId, admissionPatient, prestationServiceId, infirmierId, patientId, montant ) {
                console.log(justification, consultationId, admissionPatient, prestationServiceId, infirmierId, patientId)
                var data = {
                    "justification": justification,
                    "consultation_id": consultationId,
                    "infirmier_id": infirmierId,
                    "prestation_service_id": prestationServiceId,
                    "patient_id": patientId,
                    "admission_patient":admissionPatient,
                    "montant": montant,
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "{{ route('doctor.consultation.store.issue.justification') }}",
                    data: data,
                    success: function(response) {

                        if (response.status == 'error') {

                            Swal.fire({
                                text: "Pas de consultation effectué pour cette admission!",
                                icon: "error",
                                button: "ok",
                            });
                            $("#prescription").css("display", "none");
                            $("#formIssue").css("display", "block");
                        }
                        if (response.status == 'success') {

                            Swal.fire({
                                text: "Issue de consultation validée!",
                                icon: "success",
                                button: "ok",
                            });

                            $("#formIssue").css("display", "none");
                            $("#prescription").css("display", "block");

                        }

                    },
                    error: function(xhr, status, error) {
                        if (window.toastr) {
                            toastr.info("💾 Issue enregistrée en mode hors-ligne. Vous pouvez continuer vers la sélection de documents.", "Mode Hors-Ligne");
                        }
                        $("#formIssue").css("display", "none");
                        $("#prescription").css("display", "block");
                    }
                });


            }

        })(jQuery);

    </script>
    <style>
        .select2-selection{
            height: 34px !important;
        }
    </style>
