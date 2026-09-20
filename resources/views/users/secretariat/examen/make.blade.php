@extends('layouts.dashboard',['title' => "Créer un Examen"])

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- /.box-header -->
            <div class="box-body " id="new-admission">
                <form action="{{ route('secretariat.examen.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type_admission_id" value="Examen" />
                        <div class="mont-aff">
                            <label class="title mb-4">Montant à payer à la Caisse</label>
                            <h3 class="box-title">
                                <input type="hidden" class="form-control" name="montant" id="montant">
                                <b id="prix"> 0 Frs CFA</b>
                            </h3>

                        </div>
                    <br/><br/>
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label fw-bold"> Selectionner le Patient : <span class="danger">*</span></label>
                                        <select id="patient_id" name="patient_id" required class="form-control select2" style="width: 100%;">
                                            <option value="" selected disabled>Selectionner</option>
                                            @foreach($patients as $key)
                                            <option value="{{ $key->id }}"> {{ $key->user->name }} {{ $key->user->prenom }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type_examen_id" class="form-label fw-bold"> Type d'examens : <span class="danger">*</span> </label>
                                        <select class="form-select" required id="type_examen_id" name="type_examen_id">
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departement" class="form-label fw-bold"> Selectionner un département : <span class="danger">*</span> </label>
                                        <select class="form-select" required id="departement" name="departement_id">
                                            <option value="" selected disabled>Selectionner</option>
                                            @foreach($departements as $key)
                                                <option value="{{ $key->id }}"> {{ $key->libelle }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor" class="form-label fw-bold"> Selectionner le Medecin : <span class="danger">*</span> </label>
                                        <select class="form-select select2" required id="doctor" name="doctor_id" style="width: 100%;">
                                            <option value="" selected="selected" disabled>Selectionner</option>

                                        </select>
                                    </div>
                                </div>

                                <br/>

                                <hr/>
                                <br/>
                                <div class="row">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="fonction" class="form-label"> <b>Mode d'entrée: </b>  </label>
                                                <div class="c-inputs-stacked">
                                                    <input type="radio" id="venue_lui_meme" value="Venue lui même" name="mode_entree">
                                                    <label for="venue_lui_meme" class="me-30">Venue lui même</label>
                                                    <input type="radio" id="referer" value="Référée par un centre de santé" name="mode_entree">
                                                    <label for="referer" class="me-30">Référée par un centre de santé</label>
                                                    <input type="radio" id="referer_tradi" value="Référée par un tradipraticien" name="mode_entree">
                                                    <label for="referer_tradi" class="me-30">Référée par un tradipraticien</label>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <br/>
                                <hr/>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="motif" class="form-label fw-bold">Motif de l'examen : <span class="danger">*</span></label>
                                        <textarea required class="form-control" id="motif" name="motif_consultation" placeholder="motif de consultation" rows="4" maxlength="150"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="btn-submit">
                        <button type="submit" class="btn btn-primary">Enregister</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

    <!-- Inclure la bibliothèque jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Cibler le champ select
            var selectField = $('#assurer');
            var selectField2 = $('#non_assurer');

            // Cibler les éléments du formulaire à afficher/masquer
            var formField1 = $('#assure');
            var formField2 = $('#non_assure');

            // Cacher les champs du formulaire au chargement de la page
            formField1.hide();
            formField2.hide();

            // Écouter les changements de sélection dans le champ select
            selectField.change(function() {
                // Récupérer la valeur sélectionnée
                var selectedValue = $(this).val();

                // Afficher ou masquer les champs du formulaire en fonction de la valeur sélectionnée
                if (selectedValue === "assurer") {
                    formField1.show();
                    formField2.hide();
                } else if (selectedValue === "non assurer") {
                    formField1.hide();
                    formField2.hide();
                } else {
                    formField1.hide();
                    formField2.hide();
                }
            });
            // Écouter les changements de sélection dans le champ select
            selectField2.change(function() {
                // Récupérer la valeur sélectionnée
                var selectedValue = $(this).val();

                // Afficher ou masquer les champs du formulaire en fonction de la valeur sélectionnée
                if (selectedValue === "non assurer") {
                    formField1.hide();
                    formField2.show();
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#departement').change(function() {
                var departement = $(this).val();
                var doctorlist = $('#doctor');

                doctorlist.empty();
                doctorlist.append('<option value="" selected disabled>Selectionner</option>');

                if (departement) {
                    $.ajax({
                        url: '{{ route("secretariat.admission.doctors", ":id") }}'.replace(':id', departement),
                        type: 'GET',
                        success: function(response) {
                            $.each(response, function(key, doctor) {
                                doctorlist.append('<option value="' + doctor.id + '">' + doctor.user.name + '</option>');
                            });

                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });
        });

    </script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('secretariat.examen.type_examens') }}",
                method: 'GET',
                success: function(response) {
                    var type_examens = response.type_examens;
                    var select = $('#type_examen_id');

                    select.empty(); // Vider le champ de sélection

                    select.append('<option value="" selected disabled>Selectionner</option>');

                    // Ajouter une option pour chaque type_examen
                    type_examens.forEach(function(type_examen) {
                        var option = $('<option>').attr('value', type_examen.id).text(type_examen.libelle);
                        select.append(option);
                    });
                },
                error: function(xhr) {
                    alert('Erreur de requête Ajax.');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#type_examen_id').change(function() {
                var typeExamenId = $(this).val();

                $.ajax({
                    url: "{{ route('secretariat.examen.prix_examen') }}",
                    method: 'GET',
                    data: { typeExamenId: typeExamenId },
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


