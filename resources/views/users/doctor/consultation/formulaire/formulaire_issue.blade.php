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
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="$('#prescription').hide(); $('#formIssue').show();">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Modifier la justification / issue
                                    </button>
                                </div>
                            </div>
                            <form id="prescriptionForm" class="col-lg-6">
                                @php
                                    $hasOrdonnanceExterne = \App\Models\Ordonnance::where('consultation_id', $consultation->id)
                                        ->where(function($q) {
                                            $q->whereNull('type')->orWhere('type', 'externe')->orWhere('type', '')->orWhere('type', 'Ordinaire');
                                        })->exists();

                                    $hasOrdonnanceInterne = \App\Models\Ordonnance::where('consultation_id', $consultation->id)
                                        ->where('type', 'interne')->exists();

                                    $hasBulletinExamen = \App\Models\BulletinExamen::where('consultation_id', $consultation->id)->exists();

                                    $hasArretTravail = \App\Models\ArretTravail::where('consultation_id', $consultation->id)->exists();

                                    $allDocsIssued = $hasOrdonnanceExterne && $hasOrdonnanceInterne && $hasBulletinExamen && $hasArretTravail;
                                @endphp
                                <div class="box-subtitle" style="font-size:26px;margin-top: 20px;">Sélectionnez les nouveaux documents à émettre :</div>
                                <div style="position: relative;">

                                    <div class="div-group" style="margin-top: 20px; margin-left:10px;">
                                        <div class="c-inputs-stacked">
                                            <div style="padding-bottom: 20px">
                                                <input type="checkbox" id="ordonnance" name="postIssue" value="ordonnance" {{ $hasOrdonnanceExterne ? 'disabled' : '' }}>
                                                <label class="me-30" style="font-size: 18px; {{ $hasOrdonnanceExterne ? 'opacity: 0.6; cursor: not-allowed;' : '' }}" for="ordonnance">
                                                    Prescrire une ordonnance
                                                    @if($hasOrdonnanceExterne)
                                                        <span class="badge bg-success text-white ms-2 fs-12 fw-bold"><i class="fa-solid fa-check me-1"></i> Déjà délivrée</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div style="padding-bottom: 20px">
                                                <input type="checkbox" id="ordonnanceI" name="postIssue" value="ordonnanceI" {{ $hasOrdonnanceInterne ? 'disabled' : '' }}>
                                                <label class="me-30" style="font-size: 18px; {{ $hasOrdonnanceInterne ? 'opacity: 0.6; cursor: not-allowed;' : '' }}" for="ordonnanceI">
                                                    Prescrire une ordonnance interne
                                                    @if($hasOrdonnanceInterne)
                                                        <span class="badge bg-success text-white ms-2 fs-12 fw-bold"><i class="fa-solid fa-check me-1"></i> Déjà délivrée</span>
                                                    @endif
                                                </label>
                                            </div>

                                            <div style="padding-bottom: 20px">
                                                <input type="checkbox" id="examen" name="postIssue" value="examen" {{ $hasBulletinExamen ? 'disabled' : '' }}>
                                                <label class="me-30" style="font-size: 18px; {{ $hasBulletinExamen ? 'opacity: 0.6; cursor: not-allowed;' : '' }}" for="examen">
                                                    Editer un bulletin médical
                                                    @if($hasBulletinExamen)
                                                        <span class="badge bg-success text-white ms-2 fs-12 fw-bold"><i class="fa-solid fa-check me-1"></i> Déjà délivré</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div style="padding-bottom: 20px">
                                                <input type="checkbox" id="arret_travail" name="postIssue" value="arret_travail" {{ $hasArretTravail ? 'disabled' : '' }}>
                                                <label class="me-30" style="font-size: 18px; {{ $hasArretTravail ? 'opacity: 0.6; cursor: not-allowed;' : '' }}" for="arret_travail">
                                                    Editer un arrêt de travail
                                                    @if($hasArretTravail)
                                                        <span class="badge bg-success text-white ms-2 fs-12 fw-bold"><i class="fa-solid fa-check me-1"></i> Déjà délivré</span>
                                                    @endif
                                                </label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            @if(!$allDocsIssued)
                                                <button type="submit" class="btn btn-primary btn-submit">
                                                    <i class="fa-solid fa-file-signature me-1"></i> Générer les formulaires
                                                </button>
                                            @endif
                                            <a href="{{ route('doctor.consultation.documents', $consultation->id) }}" class="btn btn-info text-white">
                                                <i class="fa-solid fa-file-pdf me-1"></i> Voir les documents délivrés
                                            </a>
                                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                                <i class="fa-solid fa-home me-1"></i> Accueil
                                            </a>
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
            $("#ordonnanceSection").css("display", "none");
            $("#ordonnanceSectionI").css("display", "none");
            $("#arretSection").css("display", "none");
            $("#examenSection").css("display", "none");

            var hasJustified = {{ (!empty(optional($consultation->registre)->issue_consultation_justification) || \App\Models\Hospitalisation::where('consultation_id', $consultation->id)->exists()) ? 'true' : 'false' }};
            if (hasJustified) {
                $("#formIssue").css("display", "none");
                $("#prescription").css("display", "block");
            } else {
                $("#formIssue").css("display", "block");
                $("#prescription").css("display", "none");
            }

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
