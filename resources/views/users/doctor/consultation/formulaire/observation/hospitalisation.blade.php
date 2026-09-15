<form class="form-horizontal" id="form-hospitalisation" action="{{ route('doctor.hospitalisation.make') }}" method="POST" enctype="multipart/form-data">
    @csrf
<!-- Champs pour le registe-->
    <input type="hidden" name="patient_id" value="{{$consultation->admission->patient->id}}" />
    <input type="hidden" name="consultation_id" value="{{$consultation->id}}" />
    <input type="hidden" name="doctor_id" value="{{$consultation->admission->doctor->id}}" />
    <input type="hidden" name="admission_id" value="{{$consultation->admission->id}}" />
    <input type="hidden" name="hospital_id" value="{{$consultation->admission->hospital->id}}" />
    <section class="content">
        <div class="container box p-10">
            <div class="row">
                @php
                    $agePatient = \Carbon\Carbon::createFromFormat('d/m/Y', $consultation->patient->birth_date);
                    $age = $agePatient->diffInYears(Carbon\Carbon::now());
                @endphp
                <div class="col-md-8">
                    <div class="section-header col-12">
                        <p><label class="form-label fw-700 fs-14">Nom de l'établissement : &nbsp;&nbsp;&nbsp;       </label>
                            <span class="dotted-title uppercase fw-600 bg-gray-200"><span class="w-100 p-md-100">{{\Illuminate\Support\Facades\Auth::user()->doctor->hospital->label }}</span></span>
                        </p>
                        <p><label class="form-label fw-700 fs-14">Service de : &nbsp;&nbsp;&nbsp;       </label>
                            <span class="dotted-title uppercase fw-600 bg-gray-200"><span class="p-lg-100">hospitalisation</span></span>

                        </p>
                    </div>
                    <br />
                    <div class="separator bt-double border-warning"></div>
                    <br />
                    <div class="title">
                        <p class="form-label fs-18">Date d'hospitalisation :  <input type="text" class="form-control col-md-4" name="date_hospitalisation" value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" readonly/> </p>
                    </div>
                    <br /><br />
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                <td class="fw-600">N° d'ordre : <input class="form-control" name="no_ordre" /></td>
                                <td class="fw-600">Dossier consultation : <input class="form-control" name="no_dossier_consultation" /></td>
                                <td class="fw-600">N° dossier d'hospitalisation : <input class="form-control" name="no_dossier_hospitalisation" /></td>
                                </tr>
                                <tr>
                                    <td class="fw-600">N° de chambre : <input class="form-control" name="no_chambre" /></td>
                                    <td class="fw-600">N° lit : <input class="form-control" name="no_lit" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name" class="form-label"> <b>Nom: </b> </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="ti-user"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Nom" value="{{ $consultation->patient->user->name }}" readonly>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="prenom" class="form-label"> <b>Prénom(s): </b></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="ti-user"></i></span>
                                    <input type="text" name="prenom" class="form-control" placeholder="Prénom(s)" value="{{ $consultation->patient->user->prenom }}" readonly>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="form-label"> <b>Profession: </b> </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="ti-briefcase"></i></span>
                                    <input type="text" name="profession" class="form-control" placeholder="Profession" value="{{ $consultation->patient->profession }}" readonly>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="prenom" class="form-label"> <b>Pays: </b></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" name="country" class="form-control" placeholder="pays" value="{{ $consultation->patient->country }}" readonly>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group row">
                                <label class="form-label col-4" readonly> Sexe : </label>
                                <input type="text" name="gender" class="form-control" placeholder="gender" value="{{ $consultation->patient->gender }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"> Age :</label>
                                <input type="text" name="age" class="form-control" placeholder="age" value="{{ $age }} ans" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"> <b>Tranche d'âge : </b> </label>
                                <div class="c-inputs-stacked">
                                    @if($age >= 0 && $age <= 4)
                                    <input type="radio" id="0-4ans" value="0-4ans" name="tranche_age" checked>
                                    <label for="0-4ans" class="me-30">0-4ans</label>
                                    @elseif($age >= 5 && $age <= 9)
                                    <input type="radio" id="5-9ans" value="5-9ans" name="tranche_age" checked>
                                    <label for="5-9ans" class="me-30">5-9ans</label>
                                    @elseif($age >= 10 && $age <= 14)
                                    <input type="radio" id="10-14ans" value="10-14ans" name="tranche_age" checked>
                                    <label for="10-14ans" class="me-30">10-14ans</label>
                                    @elseif($age >= 15 && $age <= 19)
                                    <input type="radio" id="15-19ans" value="15-19ans" name="tranche_age" checked>
                                    <label for="15-19ans" class="me-30">15-19ans</label>
                                    @elseif($age >= 20 && $age <= 24)
                                    <input type="radio" id="20-24ans" value="20-24ans" name="tranche_age" checked>
                                    <label for="20-24ans" class="me-30">20-24ans</label>
                                    @elseif($age >= 24 && $age <= 49)
                                    <input type="radio" id="24-49ans" value="24-49ans" name="tranche_age" checked>
                                    <label for="24-49ans" class="me-30">25-49ans</label>
                                    @elseif($age >= 50 )
                                    <input type="radio" id="50ans+" value="50ans+" name="tranche_age" checked>
                                    <label for="50ans+" class="me-30">50ans et plus</label>
                </div>

                <div class="col-md-4">
                    <img class="w-100 align-content-center" src="{{ asset('assets/images/logo.png') }}" style="max-height: 100px" alt="logo de la clinique"/>
                </div>

            </div>

            <div class="row border-2 border-danger">
                <div class="col-md-12 p-10 bg-warning-light">
                    <h4><b>IDENTITE DU PATIENT</b></h4>
                </div>
                <div class="col-md-12 p-10">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Nom : </b> </label>
                                <input class="form-control" type="text" id="nom" name="nom" value="{{ $patient->user->name ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Prénom(s) : </b> </label>
                                <input class="form-control" type="text"  id="prenom" name="prenom" value="{{ $patient->user->prenom ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Date de naissance : </b> </label>
                                <input class="form-control" type="text"  id="birth_date" name="birth_date" value="{{ $patient->birth_date ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Age : </b> </label>

                                <input class="form-control" type="text"  id="age" name="age" value="{{ $age }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Genre : </b> </label>
                                <input class="form-control" type="text"  id="genre" name="genre" value="{{ $patient->gender ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Profession: </b> </label>
                                <input class="form-control" type="text"  id="profession" name="profession" value="{{ $patient->profession ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Nationalité : </b> </label>
                                <input class="form-control" type="text"  id="nationalite" name="nationalite" value="{{ $patient->nationality->name ?? '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Cel : </b> </label>
                                <input class="form-control" type="text"  id="telephone" name="telephone" placeholder="N° téléphone" value="{{ $patient->telephone ?? '' }}" readonly>
                            </div>
                        </div>
                        @if (!empty($patient->contact2))
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label"> <b>Cel2 : </b> </label>
                                <input class="form-control" type="text"  id="contact2" name="contact2" placeholder="N° téléphone 2" value="{{ $patient->contact2 }}" readonly>
                            </div>
                        </div>
                        @endif

                    </div>
                    <div class="row">
                        @if(!empty($patient->habitualResidence->name))
                        <div class="col-md-4">
                            <label class="form-label">Résidence habituelle :</label>
                            <input class="form-control" type="text"  id="residence_habituelle" placeholder="Résidence habituelle" name="residence_habituelle" value="{{ $patient->habitualResidence->name }}" readonly>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <label class="form-label">Résidence actuelle :</label>
                            <input class="form-control" type="text"  id="residence_actuelle" placeholder="Résidence habituelle" name="residence_actuelle" value="{{ $patient->residenceActuelle->name ?? '' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status conjugal :</label>
                            <input class="form-control" type="text"  id="situation_matrimoniale" name="situation_matrimoniale" value="{{ $patient->situation_matrimoniale ?? '' }}" readonly>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="form-group">
                            <label for="fonction" class="form-label"> <b>Protection sociale : </b>
                            </label>
                            @if(optional($consultation->admission)->type_assurance_id != "")
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" value="{{ optional($consultation->admission->typeAssurance)->libelle }}" readonly>

                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" value="{{ optional($consultation->admission)->no_assurance }}" readonly>
                                    </div>
                                </div>
                            @else
                            <div class="c-inputs-stacked">
                                <input type="radio" id="non_assuree" checked>
                                <label for="non_assuree" class="me-30">Non Assuré</label>
                            </div>
                            @endif

                        </div>
                    </div>
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                <td class="fw-600"><u>Type admission</u></td>
                                <td>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="c-inputs-stacked">
                                                    <input type="radio" id="nouvelleAdmission" value="Nouvelle Admission" name="type_admission">
                                                    <label for="nouvelleAdmission" class="me-30">Nouvelle Admission</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="c-inputs-stacked">
                                                    <input type="radio" id="ancienneAdmission" value="Ancienne Admission" name="type_admission">
                                                    <label for="ancienneAdmission" class="me-30">Ancienne Admission (Poursuite d'hospitalisation)</label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br/>
                    <div class="col-12" id="ancienneAdmissionForm">
                        <p>Si ancienne admission, Report N° Page <input id="no_page" type="text" class="col-md-2" name="no_page_ancienne_admission"> N° Registre <input id="no_registre" type="text" class="col-md-2" name="no_registre_ancienne_admission"></p>
                    </div>
                    <br />

                </div>
                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header bg-danger-light">
                            <h4 class="box-title">DIAGNOSTIC PRINCIPAL :</h4>
                        </div>
                        <div class="box-body p-5 overflow-x-scroll">
                            <textarea class="form-control" rows="15" name="diagnostic"></textarea>
                        </div>
                    </div>
                    <br />
                    <div class="box">
                        <div class="box-header bg-primary-light">
                            <h4 class="box-title">OBSERVATIONS :</h4>
                        </div>
                        <div class="box-body p-5 overflow-x-scroll">
                            <textarea class="form-control" rows="15" name="observations"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header bg-traitement">
                            <h4 class="box-title">TRAITEMENTS PRESCRITS :
                                <small class="subtitle">Préciser date </small>
                            </h4>
                        </div>
                        <div class="box-body p-5 overflow-x-scroll">
                            <div class="form-group" id="traitement_item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Nom de traitement<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Nom de traitement" name="nom_traitement[]">

                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Date du traitement<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" placeholder="Précisez la date et heure du traitement" name="date_traitement[]">
                                    </div>
                                    <div class="col-md-2 mt-20 add_item">
                                        <button type="button" class="btn btn-success add_traitement_btn" ><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header bg-pathologie">
                            <h4 class="box-title">PATHOLOGIES ASSOCIES :
                                <small class="subtitle">Préciser date</small>
                            </h4>
                        </div>
                        <div class="box-body p-5 overflow-x-scroll">
                            <div class="form-group" id="pathologie_item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Nom de la pathologie<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Nom pathologie" name="nom_pathologie[]">

                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Date<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" placeholder="Précisez la date pathologie" name="date_pathologie[]">
                                    </div>
                                    <div class="col-md-2 mt-20 add_item">
                                        <button type="button" class="btn btn-success add_pathologie_btn" ><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header bg-soins">
                            <h4 class="box-title">SOINS ADMINISTRES  :

                                <small class="subtitle"><span class="fs-16">(perfusion, transfusion sanguine, ...)</span>Préciser date et heure de réalisation </small>
                            </h4>
                        </div>
                        <div class="box-body p-5 overflow-x-scroll">

                                <div class="form-group" id="soins_item">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Nom du soins<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Nom du soins" name="nom_soins[]">

                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Date et heure<span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" placeholder="Précisez la date du soins" name="date_soins[]">
                                        </div>
                                        <div class="col-md-2 mt-20 add_item">
                                            <button type="button" class="btn btn-success add_soins_btn" ><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                        </div>
                                    </div>

                                </div>

                        </div>
                    </div>
                </div>
            </div>

            <br/><br/>
            <div class="col-12">
                <p style="font-style: oblique">Date de clôture de la page: Total Nombre de journée d'hospitalisation pour la page = <input type="text" name="nombre_de_jour_hospitalisation" class="col-md-2"/>  Jours. <br/>
                **Si retranscription sur une autre page préciser N° Régistre <input type="text" name="no_registre_ancienne_admission" class="col-md-2 mt-2"/> et N° Page ........
                Nombre cumulé de journées d'hospitalisation du client à la fin du Mois: <input type="text" name="nombre_de_jour_dans_le_mois" class="col-md-2 mt-2"/> Jours <input type="checkbox" name="" value="" />
                </p>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-submit">Terminer l'hopitalisation</button>
    </section>
</form>

<script>

    const ancienneAdmission = document.querySelectorAll('input[type=radio][name="type_admission"]');
    const ancienneAdmissionForm = document.getElementById('ancienneAdmissionForm');
    const InputNoRegistre = document.getElementById('no_registre');
    const InputNoPage = document.getElementById('no_page');

    ancienneAdmissionForm.style.display = 'none';

    Array.prototype.forEach.call(ancienneAdmission, function(radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Ancienne Admission') {
            ancienneAdmissionForm.style.display = 'block';
            InputNoPage.required = true;
            InputNoRegistre.required = true;
        } else {
            ancienneAdmissionForm.style.display = 'none';
            InputNoPage.required = false;
            InputNoRegistre.required = false;
        }
    }

</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Add input field
        $('.add_pathologie_btn').click(function() {
            $('#pathologie_item').append('<br/> <div class="row"> <div class="col-md-6"> <input type="text" class="form-control" placeholder="Nom de pathologie" name="nom_pathologie[]"></div><div class="col-md-4">  <input type="date" class="form-control" placeholder="Précisez la date de la pathologie" name="date_pathologie[]"></div> <div class="col-md-2 mt-0 add_item"><button type="button" class="btn btn-sm btn-danger remove_pathologie_btn" >X</button></div></div>');
        });

        // Remove input field
        $(document).on('click', '.remove_pathologie_btn', function(e) {
            e.preventDefault();
            let pathologie_item = $(this).parent().parent();
            $(pathologie_item).remove();
        });
    });

    $(document).ready(function() {

        // Add input field
        $('.add_examen_btn').click(function() {
            $('#examen_item').append('<br/> <div class="row"> <div class="col-md-6"> <select name="type_examen" class="form-control" id="type_examen_id"></select></div><div class="col-md-4">  <input type="date" class="form-control" placeholder="Précisez la date de l examen"></div> <div class="col-md-2 mt-0 add_item"><button type="button" class="btn btn-sm btn-danger remove_examen_btn" >X</button></div></div>');
        });
        // Remove input field
        $(document).on('click', '.remove_examen_btn', function(e) {
            e.preventDefault();
            let examen_item = $(this).parent().parent();
            $(examen_item).remove();
        });
    });

    $(document).ready(function() {
        // Add input field
        $('.add_soins_btn').click(function() {
            $('#soins_item').append('<br/> <div class="row"> <div class="col-md-6"> <input type="text" class="form-control" placeholder="Nom de pathologie" name="nom_soins[]"></div><div class="col-md-4">  <input type="datetime-local" class="form-control" placeholder="Précisez la date et heure du soins" name="date_soins[]"></div> <div class="col-md-2 mt-0 add_item"><button type="button" class="btn btn-sm btn-danger remove_soins_btn" >X</button></div></div>');
        });

        // Remove input field
        $(document).on('click', '.remove_soins_btn', function(e) {
            e.preventDefault();
            let soins_item = $(this).parent().parent();
            $(soins_item).remove();
        });
    });

    $(document).ready(function() {
        // Add input field
        $('.add_traitement_btn').click(function() {
            $('#traitement_item').append('<br/> <div class="row"> <div class="col-md-6"> <input type="text" class="form-control" placeholder="Nom de traitement" name="nom_traitement[]"></div><div class="col-md-4"> <input type="date" class="form-control" placeholder="Précisez la date et heure de traitement" name="date_traitement[]"></div><div class="col-md-2 mt-0 add_item"><button type="button" class="btn btn-sm btn-danger remove_traitement_btn" >X</button></div></div>');
        });

        // Remove input field
        $(document).on('click', '.remove_traitement_btn', function(e) {
            e.preventDefault();
            let traitement_item = $(this).parent().parent();
            $(traitement_item).remove();
        });
    });
</script>

<style>
    .bg-examen
    {
        background-color: rgb(162, 251, 221);
    }
    .bg-traitement
    {
        background-color: rgb(219, 159, 250);
    }
    .bg-soins
    {
        background-color: palegoldenrod;
    }
    .bg-pathologie
    {
        background-color: rgb(247, 174, 202);
    }
    .dotted-title {
        border-bottom: 2px dotted #000; /* Bordure pointillée */
        width: max-content; /* Ajuste la largeur à celle du contenu */
        padding-bottom: 0px; /* Espacement en bas de la bordure */
        margin-bottom: 0px; /* Marge en bas pour l'espace entre les titres */
        position: relative; /* Permettra de positionner les pseudo-éléments */
        }

        .dotted-title::after {
        content: ""; /* Contenu vide */
        position: absolute; /* Position absolue par rapport à .dotted-title */
        bottom: 0px; /* Position en bas de .dotted-title */
        left: 0; /* Positionnement à gauche */
        width: 100%; /* Largeur égale à .dotted-title */
        height: 20px; /* Hauteur de la ligne */

        }
    .uppercase
    {
        text-transform: uppercase;
    }
</style>
