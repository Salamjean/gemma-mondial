@php
    $hasDay = count($hospitalisation->daysHospitalisation) > 0 && isset($hospitalisation->daysHospitalisation[0]->bed);
    if (!$hasDay || $hospitalisation->daysHospitalisation[0]->day != date('Y-m-d')) {
        $form = 'doctor.hospitalisation.update.new';
    } else {
        $form = 'doctor.hospitalisation.update.already';
    }
@endphp

<form class="form-horizontal" id="formHospitalisation" action=" {{ route($form, $hospitalisation->id) }}" method="post">
    @csrf
    @method('POST')
    <input type="hidden" name="consultation_id" value="{{ $hospitalisation->consultation->id }}" />
    <input type="hidden" name="type" value="hospitalisation" />
    <input type="hidden" name="protocol_type" value="interne" />

    <section class="content">
        <div class="container box p-10">
            <br />

            <div class="row">
                @if (!$hasDay || $hospitalisation->daysHospitalisation[0]->day != date('Y-m-d'))
                    <div class="col-md-12">

                        <div class="box-body ">
                            <div class="ribbon-box">
                                <div class="ribbon ribbon-dark">Choisissez la chambre</div>
                                <div class="d-flex justify-content-end ">
                                    <div class=" badge-info-light p-2 priceeeee">
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
                            @if ($hasDay)
                                <div class="bed--occupied">
                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <th>
                                                    Type de chambre : <span
                                                        class="text-info">{{ $hospitalisation->daysHospitalisation[0]->bed->bedroom->type }}</span>
                                                </th>
                                                <th>
                                                    Chambre : <span
                                                        class="text-warning">{{ $hospitalisation->daysHospitalisation[0]->bed->bedroom->number }}</span>
                                                </th>
                                                <th>
                                                    Lit n° <span
                                                        class="text-success">{{ $hospitalisation->daysHospitalisation[0]->bed->number }}</span>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <div class="col-md-6 row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="">
                                            <input type="checkbox" name="bedcheck" id="bedcheck" value="bedcheck" />
                                            <label class="form-label" for="bedcheck"> <b>Le patient desire de changer de
                                                    chambre?</b>
                                            </label>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class=" bed-template">
                                <div class="row">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type_bedroom" class="form-label"> <b>Type de chambre </b>
                                                <span class="danger">*</span></label>
                                            <select class="form-select" id="type_bedroom" name="type_bedroom">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="individual">Individuelle</option>
                                                <option value="collective">Collective</option>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bedroom" class="form-label"> <b>Chambre N° </b>
                                                <span class="danger">*</span></label>
                                            <select class="form-select" id="bedroom" name="bedroom">
                                                <option value="" disabled selected>Selectionner</option>

                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bed" class="form-label"> <b>Lit N° </b>
                                                <span class="danger">*</span></label>
                                            <select class="form-select" id="bed" name="bed">
                                                <option value="" disabled selected>Selectionner</option>

                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <br>
                                <br>

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
                @endif

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
        @if (!$hasDay)
            $('.bed-template').css('display', 'block');
            $('.priceeeee').css('display', 'block');
            $("select[name=type_bedroom]").prop('required', true);
            $("select[name=bedroom]").prop('required', true);
            $("select[name=bed]").prop('required', true);
        @elseif ($hospitalisation->daysHospitalisation[0]->day != date('Y-m-d'))

            $('.bed-template').css('display', 'none');
            $('.priceeeee').css('display', 'none');
            //checkbox bed
            $('#bedcheck').click(function() {


                if ($(this).is(':checked')) {
                    $('.bed-template').css('display', 'block');

                    $('.priceeeee').css('display', 'block');

                    $('.bed--occupied').css('display', 'none');

                    $("select[name=type_bedroom]").prop('required', true);
                    $("select[name=bedroom]").prop('required', true);
                    $("select[name=bed]").prop('required', true);

                } else {
                    $('.bed-template').css('display', 'none');


                    $('.priceeeee').css('display', 'none');

                    $('.bed--occupied').css('display', 'block');

                    $("select[name=type_bedroom]").prop('required', false);
                    $("select[name=bedroom]").prop('required', false);
                    $("select[name=bed]").prop('required', false);

                }
            });
        @endif

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
                            <span class="danger">*</span></label>
                        <textarea class="form-control" id="description_regime" rows="1" name="description_regime" required></textarea>
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
                            <span class="danger">*</span></label>
                        <input class="form-control" type="date" id="date_operation"
                            name="date_operation" required />
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

            var drugsCount = $("#drug__item .row").length;

            if (drugsCount === 0) {
                Swal.fire({
                    title: 'Erreur!',
                    text: 'Veuillez ajouter au moins un protocole.',
                    icon: 'error',
                });
            } else {
                Swal.fire({
                    title: 'Etes vous sûr d\'hospitalisé le patient?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $("#formHospitalisation")[0].submit();

                        // var data = {
                        //     "consultation_id": '{{ $hospitalisation->consultation->id }}',
                        // }

                        // store(data)

                        let timerInterval
                        Swal.fire({
                            title: 'Chargement!',
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
            }



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
                                        <span class="danger">*</span></label>
                                    <select class="select2 form-select custom-select-heightI" id="drug" name="drug[]" required>
                                        <option value="" disabled selected>Selectionner</option>
                                        @foreach ($drugs as $drug)
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
                                        <span class="danger">*</span></label>
                                    <input type="number" class="form-control" id="quantity" name="quantity[]" min="1"
                                        required />

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
