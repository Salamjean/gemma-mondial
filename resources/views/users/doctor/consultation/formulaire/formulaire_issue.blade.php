@extends('layouts.dashboard', ['title' => $title])

@section('content')
    <div id="formIssue">
        @php
            $normalizedType = strtolower(trim((string) $type));
        @endphp

        @if ($normalizedType == 'declaration-naissance')
            @include('users.doctor.consultation.formulaire.issue.naissance')
        @elseif ($normalizedType == 'declaration-deces-patient' || in_array($normalizedType, ['décédé', 'décédée', 'decede', 'decedee', 'deces']))
            @include('users.doctor.consultation.formulaire.issue.deces', ['person' => 'patient'])
        @elseif ($normalizedType == 'declaration-deces-enfant')
            @include('users.doctor.consultation.formulaire.issue.deces', ['person' => 'enfant'])
        @elseif ($normalizedType == 'hospitalisation' || in_array($normalizedType, ['hospitalise', 'hospitalisé', 'hospitalisée']))
            @include('users.doctor.consultation.formulaire.issue.hospitalisation')
        @elseif ($normalizedType == 'observation')
            @include('users.doctor.consultation.formulaire.issue.hospitalisation')
        @else
            @include('users.doctor.consultation.formulaire.issue.justification')
        @endif
    </div>

    <div id="prescription">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-lg-1"></div>

                            <div class="col-lg-5">
                                <h4 class="box-title">
                                    <img src="{{ asset('assets/uploads/valide.png') }}" alt="" srcset="">
                                </h4>
                                <h3 class="box-subtitle">Votre consultation a été validée.</h3>
                            </div>
                            <form id="prescriptionForm" class="col-lg-6">
                                <div class="box-subtitle" style="font-size:30px;margin-top: 30px;">Voulez vous :</div>
                                <div style="position: absolute;">

                                    <div class="div-group" style="margin-top: 30px; margin-left:30px;">
                                        <div class="c-inputs-stacked">
                                            <div style="padding-bottom: 25px">
                                                <input type="checkbox" id="ordonnance" name="postIssue" value="ordonnance">
                                                <label class="me-30" style="font-size: 20px;" for="ordonnance">Prescrire
                                                    une ordonnance</label>
                                            </div>
                                            <div style="padding-bottom: 25px">
                                                <input type="checkbox" id="ordonnanceI" name="postIssue"
                                                    value="ordonnanceI">
                                                <label class="me-30" style="font-size: 20px;" for="ordonnanceI">Prescrire
                                                    une ordonnance interne</label>
                                            </div>

                                            <div style="padding-bottom: 25px">
                                                <input type="checkbox" id="examen" name="postIssue" value="examen">
                                                <label class="me-30" style="font-size: 20px;" for="examen">Editer un
                                                    bulletin medical</label>

                                            </div>
                                            <div style="padding-bottom: 25px">
                                                <input type="checkbox" id="arret_travail" name="postIssue"
                                                    value="arret_travail">
                                                <label class="me-30" style="font-size: 20px;" for="arret_travail">Editer un
                                                    arrêt de travail</label>
                                            </div>



                                        </div>
                                    </div>

                                    <div style="position: relative; bottom: 5px; right:5px;">
                                        <div class="row">
                                            <div class="col-4">
                                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-submit"
                                                    style="background-color: rgb(0, 255, 55); margin-left:30px; border:none;">Accueil</a>
                                            </div>
                                            <div class="col-8">
                                                <button type="submit" class="btn btn-primary btn-submit">Generer les
                                                    formulaires</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div id="impDocumentDeces">
    </div>

    <div id="sectionPrescription">
        <div id="ordonnanceSection" class="prescription">
            @include('users.doctor.consultation.formulaire.post-consultation.ordonnance')
        </div>

        <div id="ordonnanceSectionI" class="prescription">
            @include('users.doctor.consultation.formulaire.post-consultation.ordonnance_interne')
        </div>

        <div id="examenSection" class="prescription">
            @include('users.doctor.consultation.formulaire.post-consultation.examen')
        </div>

        <div id="arretSection" class="prescription">
            @include('users.doctor.consultation.formulaire.post-consultation.arret')
        </div>

        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-submit"
                style="background-color: rgb(18, 1, 78) margin-left:30px; border:none;">
                Aller au menu de consultation
            </a>
        </div>

    </div>

    <style>
        .prescription {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
    </style>

    <script>
        (function($) {
            "use strict";

            $("#sectionPrescription").css("display", "none");
            $("#prescription").css("display", "none");
            $("#ordonnanceSection").css("display", "none");
            $("#ordonnanceSectionI").css("display", "none");
            $("#arretSection").css("display", "none");
            $("#examenSection").css("display", "none");

            $('#prescriptionForm').on('submit', function(e) {

                e.preventDefault();
                let valeurCheck = []

                $.each($("input[name='postIssue']:checked"), function() {
                    valeurCheck.push($(this).val());
                });

                if (valeurCheck.length < 1) {

                    Swal.fire({
                        text: "Veuillez selectionner les formulaires que vous disirez ouvrir!",
                        icon: "error",
                        button: "ok",
                    });

                    return false;
                }

                if (valeurCheck.find(e => e == 'ordonnance'))
                    $("#ordonnanceSection").css("display", "block");

                if (valeurCheck.find(e => e == 'ordonnanceI'))
                    $("#ordonnanceSectionI").css("display", "block");

                if (valeurCheck.find(e => e == 'examen'))
                    $("#examenSection").css("display", "block");

                if (valeurCheck.find(e => e == 'arret_travail'))
                    $("#arretSection").css("display", "block");

                $("#sectionPrescription").css("display", "block");

                $("#prescription").css("display", "none");

                return false;

                // verifyJustification(justification, consultationId)

            });


        })(jQuery);
    </script>
@endsection
