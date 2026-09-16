<div class="row" id="ordonnanceContainer">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Edition d'une ordonnance externe</h4>
                <h6 class="box-subtitle">Veuillez remplir le formulaire svp.</h6>
            </div>
            <form id="ordonnanceForm">
                @csrf
                <section>
                    <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
                    <div class="container___fuild">

                    </div>
                    <div class="container__button">
                        <button type="button" id="addMedicationButton" class="btn btn-add"> Ajouter un
                            médicament</button>
                        <button type="submit" class="btn btn-primary btn-submit">Valider l'ordonnance</button>
                    </div>
                    <br />
                </section>
            </form>
        </div>
    </div>
</div>

<div id="impDocomentOrdonnance">

</div>


<script>
    (function($) {
        "use strict";

        function addMedicationItem() {
            const medicationItem = `
            <div class="medication-item border ">
                <select class="form-control select2 medication-input select custom-select-height" name="medicamentCode[]" data-placeholder="Nom du médicament" style="width: 100%;" required>
                    <option value="" selected disabled data-select2-id="1">Nom du médicament</option>
                    @foreach ($drugs as $drug)
                        <option value="{{ $drug->id }}">{{ $drug->name }}</option>
                    @endforeach
                </select>

                    <input type="text" class="medication-input form-control input-qteO" name="medicamentPosologie[]" placeholder="Dose">

                <input type="number" class="medication-input form-control input-qteO" name="medicamentQte[]" placeholder="Qte" min="1" required>
                <select class="medication-input form-select input-qteO" id="voie_admission" name="routeAdministration[]">
                    <option value="" disabled selected>Voie</option>
                    <option value="IVD">IVD</option>
                    <option value="IVL">IVL</option>
                    <option value="SC">SC</option>
                    <option value="IM">IM</option>
                    <option value="ID">ID</option>
                </select>
                <input type="text" class="medication-input form-control input-qteO" name="duration[]" placeholder="Durée">
                <input type="text" class=" form-control input-qteO" name="healthDieteticAdvice[]" placeholder="Conseil hygiéno-diététique">

                <button type="button" class="medication-remove btn btn-delete"><span class="fa fa-trash"></span></button>
                </div>`;

            $(".container___fuild").append(medicationItem);

            $(".custom-select-height").select2({
                width: "100%",
                placeholder: "Code du médicament"
            });
        }

        $("#addMedicationButton").click(function() {
            addMedicationItem();
        });

        $(document).on("click", ".medication-remove", function() {
            $(this).parent().remove();
        });

        //ordonnance
        $('#ordonnanceForm').on('submit', function(e) {

            e.preventDefault();

            var data = $(this).serialize();

            storeOrdonnance(data)

        });

        function storeOrdonnance(data) {
            console.log(data)
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('consultation.store.post.ordonnance', 'externe') }}",
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
                        $("#impDocomentOrdonnance").append(imp);
                        $('#ordonnanceContainer').css("display", "none")

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

    .container___fuild {
        padding-top: 30px;
        padding-bottom: 5px;
        max-width: 1300px;
        margin: 0 auto;
    }

    .container__button {
        max-width: 1300px;
        margin: 0 auto;
        display: flex;
        gap: 10px;
    }

    .select2 {
        max-width: 400px !important;
        margin-right: 10px !important;
    }

    .medication-item {
        margin-bottom: 10px;
        display: flex;
    }

    .medication-input {
        flex: 1;
        margin-right: 10px;
    }

    .medication-remove {
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
</style>
