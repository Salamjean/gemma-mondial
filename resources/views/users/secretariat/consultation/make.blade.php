@extends('layouts.dashboard',['title' => "Faire une admission"])

@section('content')
    <div class="row">

        <div class="col-12">
            <!-- /.box-header -->
            <div class="box-body " id="new-admission">
                <form action="{{ route('secretariat.consultation.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type_admission_id" value="Consultation" />
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
                                        <label class="form-label fw-bold"> Nom et Prénom(s) du patient : <span class="danger">*</span></label>
                                        <input type="text" name="patient_id" id="search" placeholder="Nom et Prénom(s)" class="form-control patientID" onfocus="this.value=''">
                                        <input type="hidden" name="patient_id" id="patient_id">
                                        <div id="patient_list"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="type_consultation_id" class="form-label fw-bold"> Motif de la visite : <span class="danger">*</span> </label>
                                        <select class="form-select" id="type_consultation_id" name="type_consultation_id">

                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="departement" class="form-label fw-bold"> Selectionner un département : <span class="danger">*</span> </label>
                                        <select class="form-select" id="departement" name="departement_id">
                                            <option value="" selected disabled>Selectionner</option>
                                            @foreach($departements as $key)
                                                <option class="search-result" value="{{ $key->id }}"> {{ $key->libelle }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor" class="form-label fw-bold"> Selectionner le Medecin : <span class="danger">*</span> </label>
                                        <select class="form-select" id="doctor" name="doctor_id" style="width: 100%;">
                                        </select>
                                    </div>
                                </div>


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
                                        <label for="motif" class="form-label fw-bold">Motif de la visite : <span class="danger">*</span></label>
                                        <textarea class="form-control" id="motif" name="motif_consultation" placeholder="motif de consultation" rows="4" maxlength="150"></textarea>
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
    <style>
        .mont-aff {
            text-align: center;
            width: max-content;
            padding: 10px;
            float: right;
            position: relative;
            margin-bottom: 1.5rem;
            /* width: 100%; */
            background-color: #ffffff;
            border-radius: 10px;
            /* padding: 0px; */
            -webkit-transition: .5s;
            transition: .5s;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-shadow: 0 3px 5px 1px rgba(0, 0, 0, 0.05);
            box-shadow: 0 3px 5px 1px rgba(0, 0, 0, 0.05);
            border: 1px solid #EFEFFD;
        }
        .btn-submit {
            float: right;
        }
    </style>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('secretariat.consultation.type_consultations') }}",
                method: 'GET',
                success: function(response) {
                    var type_consultations = response.type_consultations;
                    var select = $('#type_consultation_id');

                    select.empty(); // Vider le champ de sélection

                    select.append('<option value="">Selectionner</option>');


                    // Ajouter une option pour chaque type_consultation
                    type_consultations.forEach(function(type_consultation) {
                        var option = $('<option>').attr('value', type_consultation.id).text(type_consultation.libelle);
                        select.append(option);
                    });
                },
                error: function(xhr) {

                }
            });
        });
        $(document).ready(function() {
            $('#departement').change(function() {
                var departement = $(this).val();
                var doctorlist = $('#doctor');

                doctorlist.empty();
                doctorlist.append('<option value="">Selectionner</option>');

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
        $(document).ready(function() {
            $('#type_consultation_id').change(function() {
                var typeConsultationId = $(this).val();

                $.ajax({
                    url: "{{ route('secretariat.consultation.prix_consultation') }}",
                    method: 'GET',
                    data: { typeConsultationId: typeConsultationId },
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

        /**** recherche patient pour admission ****/
        $(document).ready(function(){
            $('#search').on('keyup',function(){
            //var _token = $('input[name="_token"]').val();
            var query= $(this).val();
                if(query.length >= 2) {
                    $.ajax({
                    url:'{{ route("secretariat.patient.searchPatient") }}',
                    type:"GET",
                    data:{'search':query},
                    success:function(data){
                        $('#patient_list').fadeIn();
                            $('#patient_list').html(data);
                    }
                });
                }
                else{
                    $('#patient_list').empty();
                }
             //end of ajax call
        });
        $(document).on('click', 'li', function(){
                $('#search').val($(this).text());
                $('#patient_list').fadeOut();
            });
        $(document).on('click', 'option', function(){
            $('#patient_id').val($(this).val());
        });
    });
    </script>
@endsection
