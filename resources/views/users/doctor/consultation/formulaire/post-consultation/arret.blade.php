<div class="row" id="arretTravailContainer">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Edition d'un arrêt de travail</h4>
                <h6 class="box-subtitle">Veuillez remplir le formulaire svp.</h6>
                <form id="arretTravailForm">
                    @csrf
                    <section>
                        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
                        <br />
                    </section>
                    <section>
                        <div class="col-lg-12 col-12">
                            <div class="box">
                                <div class="box-body ribbon-box">
                                    <div class="ribbon ribbon-info">Données du Patient</div>

                                    <div class="mb-0">
                                        <section>
                                            <br /><br /><br />
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="name" class="form-label"> <b>Nom Patient : </b>
                                                        </label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text"><i
                                                                    class="ti-user"></i></span>
                                                            <input type="text" name="name" class="form-control"
                                                                value="{{ $consultation->patient->user->name }}"
                                                                disabled>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="prenom" class="form-label"> <b>Prénom(s) Patient :
                                                            </b> </label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text"><i
                                                                    class="ti-user"></i></span>
                                                            <input type="text" name="prenom" class="form-control"
                                                                value="{{ $consultation->patient->user->prenom }}"
                                                                disabled>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="gender" class="form-label"> <b>Sexe : </b>
                                                        </label>
                                                        <input type="gender" name="gender" class="form-control"
                                                            value="{{ $consultation->patient->gender }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="birth_date" class="form-label"> <b>Date de naissance
                                                                : </b>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" name="birth_date" class="form-control"
                                                                value="{{ $consultation->patient->birth_date }} ({{ \Carbon\Carbon::createFromFormat('d/m/Y', $consultation->patient->birth_date)->diffInYears(Carbon\Carbon::now()) }} ans)"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="telephone" class="form-label"> <b>Contact : </b>
                                                    </label>
                                                    <div class="d-flex">

                                                        <span class="form-control w-80 text-center align-center"
                                                            style="border-top-right-radius: 0; border-bottom-right-radius: 0;">+225</span>
                                                        <input type="text"
                                                            style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;"
                                                            min="10" max="10" name="telephone"
                                                            class="form-control"
                                                            value="{{ $consultation->patient->telephone }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="residence_actuelle" class="form-label"> <b>Lieu de
                                                                résidence
                                                                actuelle : </b> </label>

                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text"><i
                                                                    class="fa-solid fa-street-view"></i></span>
                                                            <input type="text" class="form-control"
                                                                name="residence_actuelle"
                                                                value="{{ $consultation->patient->currentResidence->name }}"
                                                                disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="date_consultation" class="form-label"> <b>Date de
                                                                consultation : </b>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" name="date_consultation"
                                                                id="date_consultation"
                                                                value="{{ date('d/m/Y', strtotime($consultation->date_consultation)) }}"
                                                                class="form-control" style="color:blue;" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section>
                        <div class="col-lg-12 col-12">
                            <div class="box" style="border:none;box-shadow:0;">
                                <div class="box-body ribbon-box">
                                    <div class="ribbon ribbon-info">Informations médicales</div>

                                    <div class="mb-0">
                                        <section>
                                            <br /><br /><br />
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="prenom" class="form-label"> <b>N° Dossier
                                                                Médical</b></label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text"><i
                                                                    class="ti-bookmark-alt"></i></span>
                                                            <input type="text" name="prenom" class="form-control"
                                                                value="{{ $consultation->patient->code_patient }}"
                                                                disabled>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="date_debut" class="form-label"><b>Date
                                                                Début</b></label>
                                                        <input type="date" class="form-control" id="date_debut"
                                                            name="date_debut" onchange="calculerDifferenceDates()" value="{{ date('Y-m-d') }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="date_fin" class="form-label"><b>Date de
                                                                Fin</b></label>
                                                        <input type="date" class="form-control" id="date_fin"
                                                            name="date_fin" onchange="calculerDifferenceDates()" min="{{ now()->format('Y-m-d') }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="nb_jour" class="form-label"> <b>Nombre de jours
                                                            </b></label>
                                                        <input type="text" class="form-control" id="nb_jour"
                                                            name="nb_jour" value="" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <br />


                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input class="btn btn-primary btn-submit" type="submit" value="Enregister" />
                    </section>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="impDocumentArret">
</div>

<script>
    function calculerDifferenceDates() {
        const dateDebut = new Date(document.getElementById('date_debut').value);
        const dateFin = new Date(document.getElementById('date_fin').value);


        if (isNaN(dateDebut) || isNaN(dateFin))
            return;

        const differenceEnMillisecondes = dateFin - dateDebut;

        const differenceEnJours = differenceEnMillisecondes / (1000 * 60 * 60 * 24);

        document.getElementById('nb_jour').value = differenceEnJours;

    }
</script>
<script>
    (function($) {
        "use strict";
        // $('#impDocumentArret').css("display", "none")

        //ordonnance
        $('#arretTravailForm').on('submit', function(e) {

            e.preventDefault();
            var dateDebut = $('input[name="date_debut"]').val();
            var dateFin = $('input[name="date_fin"]').val();
            var nbDay = $('input[name="nb_jour"]').val();

            if (dateDebut == '' || dateFin == '' || nbDay == '') {

                if (dateDebut == '') {
                    Swal.fire({
                        text: "Veuillez renseigner la date de début svp",
                        icon: "error",
                        button: "ok",
                    });
                    return false;
                }

                if (dateFin == '') {
                    Swal.fire({
                        text: "Veuillez renseigner la date de fin svp",
                        icon: "error",
                        button: "ok",
                    });
                    return false;
                }

                if (nbDay == '') {
                    Swal.fire({
                        text: "Veuillez renseigner le nombre de jours svp",
                        icon: "error",
                        button: "ok",
                    });
                    return false;
                }

            }

            var data = {
                "consultation_id": '{{ $consultation->id }}',
                'date_debut': dateDebut,
                'date_fin': dateFin,
                'nb_jour': nbDay,
            }

            storeArretTravail(data)

        });

        function storeArretTravail(data) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('consultation.store.post.arret.travail') }}",
                data: data,
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
                            `<div style="font-size:18px; padding:10px;"><a href="{{ url('consultation/post/imprimer/arret/${response.id}') }}" target="_blank" class="btn" style="background-color:green; color:white">Imprimer l'arrêt de travail <span class="fa fa-print"></span></a></div>`;
                        $("#impDocumentArret").append(imp);
                        $('#arretTravailContainer').css("display", "none")

                    }

                }
            });

        }

    })(jQuery);
</script>
