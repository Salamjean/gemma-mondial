<div class="row" id="ordonnanceContainerI">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Edition d'une ordonnance interne</h4>
                <h6 class="box-subtitle">Veuillez remplir le formulaire svp.</h6>
            </div>
            <form id="ordonnanceFormI">
                @csrf
                <section>
                    <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
                    <div class="container___fuildI">

                    </div>
                    <div class="container__buttonI">
                        <button type="button" id="addMedicationButtonI" class="btn btn-add"> Ajouter un
                            médicament</button>
                        <button type="submit" class="btn btn-primary btn-submit">Valider l'ordonnance</button>
                    </div>
                    <br />
                </section>
            </form>
        </div>
    </div>
</div>

<div id="impDocomentOrdonnanceI">

</div>


<script>
    (function($) {
        "use strict";

        function addMedicationItemI() {
            const medicationItemI = `<div class="medication-itemI border ">
                <select class="form-control select2 medication-input select custom-select-heightI" name="medicamentCodeI[]" data-placeholder="Nom du médicament" style="width: 100%;" required>
                    <option value="" selected disabled data-select2-id="1">Nom du médicament</option>
                    @foreach ($drugsHospital as $drug)
                        <option value="{{ $drug->id }}">{{ $drug->drug->name }}</option>
                    @endforeach
                </select>

                    <input type="text" class="medication-input form-control input-qteO" name="medicamentPosologieI[]" placeholder="Dose">

                <input type="number" class="medication-input form-control input-qteO" name="medicamentQteI[]" placeholder="Qté" min="1" required>
                <select class="medication-input form-select input-qteO" id="voie_admission" name="routeAdministrationI[]">
                    <option value="" disabled selected>Voie</option>
                    <option value="IVD">IVD</option>
                    <option value="IVL">IVL</option>
                    <option value="SC">SC</option>
                    <option value="IM">IM</option>
                    <option value="ID">ID</option>
                </select>
                <input type="text" class="medication-input form-control input-qteO" name="durationI[]" placeholder="Durée">
                <input type="text" class=" form-control input-qteO" name="healthDieteticAdviceI[]" placeholder="Conseil hygiéno-diététique">

                <button type="button" class="medication-remove btn btn-delete"><span class="fa fa-trash"></span></button>
                </div>`;

            $(".container___fuildI").append(medicationItemI);

            $(".custom-select-heightI").select2({
                width: "100%",
                placeholder: "Code du médicament"
            });
        }

        $("#addMedicationButtonI").click(function() {
            addMedicationItemI();
        });

        $(document).on("click", ".medication-removeI", function() {
            $(this).parent().remove();
        });

        //ordonnance
        $('#ordonnanceFormI').on('submit', function(e) {

            e.preventDefault();

            var data = $(this).serialize();

            storeOrdonnanceI(data)

        });

        function storeOrdonnanceI(data) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('consultation.store.post.ordonnance', 'interne') }}",
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'error') {
                        Swal.fire({
                            text: response.message,
                            icon: "error",
                            button: "ok",
                        });
                    }
                    if (response.status == 'success') {
                        Swal.fire({
                            text: response.message,
                            icon: "success",
                            button: "ok",
                        });

                        var imp =
                            `<div style="font-size:18px; padding:10px;"><a href="{{ url('consultation/post/imprimer/ordonnance/${response.id}') }}" target="_blank" class="btn" style="background-color:green; color:white">Imprimer l'ordonnance <span class="fa fa-print"></span></a></div>`;
                        $("#impDocomentOrdonnanceI").append(imp);
                        $('#ordonnanceContainerI').css("display", "none")

                    }
                }
            });
        }

    })(jQuery);
</script>

<style>
    .select2-selection {
        height: 40px !important;

    }

    .selection {
        align-content: center !important;
    }

    .container___fuildI {
        padding-top: 30px;
        padding-bottom: 5px;
        max-width: 1300px;
        margin: 0 auto;
    }

    .container__buttonI {
        max-width: 1300px;
        margin: 0 auto;
        display: flex;
        gap: 10px;
    }

    .select2 {
        max-width: 600px !important;
        margin-right: 10px !important;
    }

    .medication-itemI {
        margin-bottom: 10px;
        display: flex;
    }

    .medication-input {
        flex: 1;
        margin-right: 10px;
    }

    .medication-removeI {
        flex: 0;
    }

    .btn-delete {
        background-color: red;
        color: white;
        border: none;

        &:hover {
            background-color: rgb(229, 100, 100);
            color: black;
        }
    }

    .btn-add {
        background-color: rgb(0, 255, 0);
        color: black;
        border: none;

        &:hover {
            background-color: rgb(100, 229, 100);
            color: black;
        }
    }

    .input-qteO {
        max-width: 250px !important;
    }

    .input-montantO {
        max-width: 150px !important;
    }
</style>
