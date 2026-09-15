<div class="container">
    @php
        $patient = $consultation->patient ?? ($consultation->admission->patient ?? null);
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
    <div class="box bb-3 pe-5 pb-10 px-20 ps-10 pt-10 bg-color">
        <div class="row">
            <div class="col-md-12">
                <div class="px-2">
                    <div class="px-5 bg-color">
                        <div class="row">
                            <div class="d-flex justify-content-between mt-10">
                                <div class="">
                                    <label class="form-label">N° Dossier médical | <span class="fw-bold fs-18"><span
                                                id="dm_patient"
                                                style="color:red;">{{ $patient->code_patient ?? 'N/A' }}</span></span></label>
                                </div>
                                <div class="d-flex items-center pb-1">

                                    <span class=" d-flex mt-1 text-success">
                                        <span>Consultation du {{ Carbon\Carbon::now()->format('d/m/Y') }}</span>
                                        <pre>  </pre> |
                                        <pre>  </pre> <span>Ordre
                                            N°{{ noOrdreConsultation() }}</span>
                                        <pre>  </pre>
                                    </span>
                                    <a href="" title="dossier medical" class="btn btn-sm  btn-secondary mx-1"
                                        target="_blank"><i class="fa-solid fa-print"></i></a>
                                    @if ($patient)
                                        <a href="{{ route('doctor.patient.detail', $patient->id) }}"
                                            title="info patient" class="btn btn-sm  btn-success mx-1" target="_blank"><i
                                                class="fa-solid fa-info"></i></a>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="form-label"> <b>Nom complet </b> </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '') }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label"> <b>Né(e) le</b></label>
                                    <input type="text" class="form-control" id="birth_date" name="birth_date"
                                        value="{{ $patient->birth_date ?? 'N/A' }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-1">
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
                            <div class="col-md-2">
                                <label class="form-label"><b>Résidence Actuelle</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $patient->residenceActuelle->name ?? 'N/A' }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><b>Contact</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $patient->telephone ?? '' }}" readonly />
                            </div>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<form class="form-horizontal" id="formHospitalisation"
    action="{{ route('doctor.consultation.store.issue.hospitalisation', $consultation->id) }}" method="post">
    @csrf
    <input type="hidden" name="consultation_id" value="{{ $consultation->id }}" />
    <input type="hidden" name="type" value="hospitalisation" />

    <section class="content">
        <div class="container box p-10">
            <br />

            <div class="row">

                <div class="col-md-8">
                    <div class="box-body ">
                        <div class="ribbon-box">
                            <div class="ribbon ribbon-dark">Choisissez la chambre</div>
                            <div class="d-flex justify-content-end">
                                <div class=" badge-info-light p-2">
                                    <div>Montant journalier :</div>
                                    <div><span id="priceByDay" class="text-info-700 fs-3">0</span><span
                                            class=" text-dark"> FCFA</span>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <br />
                            <br />
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type_bedroom" class="form-label"> <b>Type de chambre </b>
                                        <small class="text-muted">(Optionnel)</small></label>
                                    <select class="form-select" id="type_bedroom" name="type_bedroom">
                                        <option value="" selected>Selectionner</option>
                                        <option value="individual">Individuelle</option>
                                        <option value="collective">Collective</option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bedroom" class="form-label"> <b>Chambre N° </b>
                                        <small class="text-muted">(Optionnel)</small></label>
                                    <select class="form-select" id="bedroom" name="bedroom">
                                        <option value="" selected>Selectionner</option>

                                    </select>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bed" class="form-label"> <b>Lit N° </b>
                                        <small class="text-muted">(Optionnel)</small></label>
                                    <select class="form-select" id="bed" name="bed">
                                        <option value="" selected>Selectionner</option>

                                    </select>

                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <div class="col-md-6 row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="">
                                            <input type="checkbox" name="regime" id="regime" value="regime" />
                                            <label class="form-label" for="regime"> <b>Régime alimentaire
                                                    particulier</b> </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="regime_description">

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="">
                                            <input type="checkbox" name="operation" id="operation"
                                                value="operation" />
                                            <label class="form-label" for="operation"> <b>Opération prévue?</b>
                                            </label>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div id="operation_date">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="box-body ">
                        <div class="ribbon-box">
                            <div class="ribbon ribbon-dark">Personnels soignants</div>
                            <br />
                            <br />
                            <br />
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label"><b>Médecin soignant</b><span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" disabled name="doctor_id"
                                    value="{{ \Illuminate\Support\Facades\Auth::user()->doctor->user->name . ' ' . \Illuminate\Support\Facades\Auth::user()->doctor->user->prenom }}">
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="infirmier_id" class="form-label"> <b>Infirmier soignant </b>
                                        <span class="danger">*</span></label>
                                    <select class="form-select" id="infirmier_id" name="infirmier_id">
                                        <option value="" disabled selected>Selectionner</option>

                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                    <br />

                </div>
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header bg-danger-light">
                            <h4 class="box-title">DIAGNOSTIC PRINCIPAL :</h4>
                        </div>
                        <div class="box-body p-5 overflow-x-scroll">
                            <textarea class="form-control" rows="8" name="diagnostic"></textarea>
                        </div>
                    </div>
                    <br />
                    <div class="box">
                        <div class="box-header bg-warning-light">
                            <h4 class="box-title">ANALYSES MEDICAUX AVANT ADMISSION :</h4>
                        </div>
                        <div class="box-body p-5 overflow-x-scroll">
                            <textarea class="form-control" rows="8" name="analyse_medical"></textarea>
                        </div>
                    </div>
                    <br />

                </div>
            </div>

            <div class="row">
                <div class="box-body">
                    <div class="ribbon-box">
                        <div class="ribbon ribbon-dark">Protocole Therapeutique Medecin</div>
                        <br />
                        <br />
                        <br />
                    </div>
                    <div class="d-flex justify-content-center m-4">
                        <div class="">
                            <button type="button" class="btn btn-outline-info  add__protocol__btn fs-3">
                                Ajouter un protocole <span class="fa-solid fa-plus-circle"></span>
                            </button>
                        </div>

                    </div>
                    <input type="hidden" id="protocol" value="0" name="protocol">

                    <div id="drug__item">

                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fonction" class="form-label"> <b>Sonde urinaire:
                                    </b>
                                </label>
                                <div class="c-inputs-stacked">
                                    <input type="radio" id="sonde" value="oui" name="sonde">
                                    <label for="sonde">Oui</label>
                                    <input type="radio" id="non_sonde" value="non" name="sonde">
                                    <label for="non_sonde">Non</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="fonction" class="form-label"> <b>Oxygénothérapie :
                                    </b>
                                </label>
                                <div class="c-inputs-stacked">
                                    <input type="radio" id="oxygenotherapie" value="oui"
                                        name="oxygenotherapie">
                                    <label for="oxygenotherapie">Oui</label>
                                    <input type="radio" id="non_oxygenotherapie" value="non"
                                        name="oxygenotherapie">
                                    <label for="non_oxygenotherapie">Non</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="volume" class="form-label"> <b>Volume (L) </b>
                                </label>
                                <select class="form-select" id="volume" name="volume">
                                    <option value="" disabled selected>Selectionner</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>

                                </select>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="remark" class="form-label"> <b>Remarque </b></label>
                                <textarea class="form-control" id="remark" rows="5" name="remark"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <br />

            </div>

            <br /><br />

        </div>
        <button type="submit" class="btn btn-primary btn-submit">Terminer l'hopitalisation</button>
    </section>
</form>


<script>
    (function($) {
        "use strict";

        var protocolCounter = 0;
        var hourCounter = 1;


        //infirmierByService
        dataInfirmier();

        //event
        //add protocol
        $('.add__protocol__btn').click(function() {
            addProtocol();
            incrementProtocolCounter();
        });

        $(document).off('click', '.remove__drug__btn');

        $(document).on('click', '.remove__drug__btn', function() {
            var uniqueId = $(this).closest(".row").attr("id");
            removeProtocol(uniqueId);
        });

        // checkbox regime
        $('#regime').click(function() {
            var description = $('#regime_description');
            description.empty();

            if ($(this).is(':checked')) {
                description.append(
                    `<div class="form-group">
                        <label for="description_regime" class="form-label"> <b>Description </b>
                            <small class="text-muted">(Optionnel)</small></label>
                        <textarea class="form-control" id="description_regime" rows="1" name="description_regime"></textarea>
                    </div>`
                )
            }
        });

        //checkbox operation
        $('#operation').click(function() {
            var date_op = $('#operation_date');
            date_op.empty();

            if ($(this).is(':checked')) {
                date_op.append(
                    `<div class="form-group">
                        <label for="date_operation" class="form-label"> <b>Date opération </b>
                            <small class="text-muted">(Optionnel)</small></label>
                        <input class="form-control" type="date" id="date_operation"
                            name="date_operation" />
                    </div>`
                )
            }
        });

        //ajax request
        $('#type_bedroom').change(function() {
            var type = $('#type_bedroom').val();
            $('#priceByDay').text('0');
            var bedrooms = $('#bedroom');
            bedrooms.empty();
            bedrooms.append('<option value="" disabled selected>Selectionner</option>');
            if (type) {
                $.ajax({
                    url: '{{ route('doctor.hospitalisation.bedroom', ':type') }}'.replace(':type',
                        type),
                    type: 'GET',
                    success: function(response) {
                        $.each(response.bedrooms, function(key, bedroom) {
                            bedrooms.append('<option value="' + bedroom.id + '">' +
                                bedroom.number + '</option>');
                        });

                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        //data elements
        $('#bedroom').change(function() {
            var bedroom = $('#bedroom').val();
            var beds = $('#bed');
            beds.empty();
            beds.append('<option value="" disabled selected>Selectionner</option>');
            if (bedroom) {
                $.ajax({
                    url: '{{ route('doctor.hospitalisation.bed', ':id') }}'.replace(':id',
                        bedroom),
                    type: 'GET',
                    success: function(response) {
                        console.log(response);

                        $.each(response.beds, function(key, bed) {
                            beds.append('<option value="' + bed.id + '">' +
                                bed.number + '</option>');
                        });

                        console.log(response.beds[0].price);
                        $('#priceByDay').text(response.beds[0].price);


                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });

        //data elements
        function dataDrugs() {
            var drugs = $('#drug_id');
            drugs.empty();
            drugs.append('<option value="" disabled selected>Selectionner</option>');
            $.ajax({
                url: '{{ route('doctor.hospitalisation.drugs') }}',
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $.each(response.drugs, function(key, drug) {
                        drugs.append('<option value="' + drug.id + '">' + drug.drug.name +
                            '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function dataInfirmier() {
            var infirmiers = $('#infirmier_id');
            infirmiers.empty();
            infirmiers.append('<option value="" disabled selected>Selectionner</option>');
            $.ajax({
                url: '{{ route('doctor.hospitalisation.infirmiers') }}',
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $.each(response.infirmiers, function(key, infirmier) {
                        infirmiers.append('<option value="' + infirmier.id + '">' +
                            infirmier.user.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        //form validation
        $("#formHospitalisation").submit(function(e) {
            e.preventDefault();

            var drugSelects = $('select[name="drug[]"]');
            var hasDrug = false;
            drugSelects.each(function() {
                if ($(this).val() !== "" && $(this).val() !== null) {
                    hasDrug = true;
                }
            });

            if (!hasDrug) {
                Swal.fire({
                    title: 'Protocole obligatoire',
                    text: 'Veuillez ajouter au moins un protocole thérapeutique avant d\'enregistrer.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            Swal.fire({
                title: 'Êtes-vous sûr d\'hospitaliser ce patient ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {

                    $("#formHospitalisation")[0].submit();

                    let timerInterval
                    Swal.fire({
                        title: 'Chargement...',
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    })
                }
            });
        });

        //function
        //method validation
        function storeHospitalisation(data) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "#",
                data: data,
                success: function(response) {}
            });

        }

        function incrementProtocolCounter() {
            protocolCounter++;
            updateProtocolInputValue();
        }

        function decrementProtocolCounter() {
            if (protocolCounter > 0) {
                protocolCounter = protocolCounter - 0.5;
                updateProtocolInputValue();
            }
        }

        function updateProtocolInputValue() {
            $("#protocol").val(protocolCounter);
        }

        function removeProtocol(uniqueId) {
            $("#" + uniqueId).remove();
            decrementProtocolCounter();
        }


        //method add protocols
        function addProtocol() {
            var uniqueId = `drug__item_${Date.now()}`;
            var drugCounter = 1;

            $("#drug__item").append(`<div id="${uniqueId}" class="row  border border-b-3 border-info p-3 m-4 rounded-3">
                        <div class="col-md-5">
                            <div class="form-group">
                                <div class="">
                                    <label for="drug" class="form-label"> <b>Nom du produit </b>
                                        <small class="text-muted">(Optionnel)</small></label>
                                    <select class="select2 form-select custom-select-heightI" id="drug" name="drug[]">
                                        <option value="" disabled selected>Selectionner</option>
                                        @foreach ($drugsHospital as $drug)
                                            <option value="{{ $drug->id }}">{{ $drug->drug->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <div class="">
                                    <label for="quantity" class="form-label"> <b>Quantité </b>
                                        <small class="text-muted">(Optionnel)</small></label>
                                    <input type="number" class="form-control" id="quantity" name="quantity[]" min="1" />

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="">
                                    <label for="dosage" class="form-label"> <b>Dosage </b>
                                    </label>
                                    <input type="text" class="form-control" id="dosage" name="dosage[]"
                                        />

                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="">
                                    <label for="voie_admission" class="form-label"> <b>Voie d'admission </b>
                                        </label>
                                    <select class="form-select" id="voie_admission" name="voie_admission[]">
                                        <option value="" disabled selected>Selectionner</option>
                                        <option value="IVD">IVD</option>
                                        <option value="IVL">IVL</option>
                                        <option value="SC">SC</option>
                                        <option value="IM">IM</option>
                                        <option value="ID">ID</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 mt-20">
                            <button type="button" class="btn btn-danger remove__drug__btn">
                                <i class="fa-solid fa-remove" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="row hour__item">
                        </div>

                        <div class="col-md-4 mt-20">
                            <button type="button" class="btn btn-outline-success add__hour__btn">
                                <i class="fa-solid fa-plus-circle" aria-hidden="true"></i> Ajout d'heure
                            </button>

                        </div>
                    </div>
            `);

            $(".custom-select-heightI").select2({
                width: "100%",
            });

            $(`#${uniqueId} .remove__drug__btn`).click(function() {
                $(this).closest(".row").remove();
                removeProtocol();
            });

            $(`#${uniqueId} .add__hour__btn`).click(addHour);
        }

        function addHour() {
            var closestDrugSection = $(this).closest(".row");
            // var drugCounter = parseInt(closestDrugSection.data("drug-counter"));

            var uniqueId = `hour_item_${Date.now()}`;
            var hourNumber = closestDrugSection.find(".hour__item > .row").length + 1;


            closestDrugSection.find(".hour__item").append(`
                <div class="col-md-3 row" id="${uniqueId}">
                <div class="col-md-9">
                    <div class="form-group">
                        <div class="">
                            <label for="hour${hourCounter}" class="form-label"> <b>Heure ${hourNumber}</b>
                            </label>
                            <input type="time" class="form-control" id="hour${hourCounter}" name="hour${protocolCounter}[]"
                                />
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="hour1" class="form-label"> </label>  <br />
                        <button type="button" class="btn btn-outline-danger remove__hour__btn">
                            <i class="fa-solid fa-remove" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
            `);

            $(".custom-select-heightI").select2({
                width: "100%",
            });

            $(`#${uniqueId} .remove__hour__btn`).click(function() {
                $(this).closest(".row").remove();
                removeHour();
            });

            hourCounter++;

            $(`#${uniqueId} .add__hour__btn`).click(addHour);
        }

        function removeHour() {
            hourCounter--;
        }

    })(jQuery);
</script>
