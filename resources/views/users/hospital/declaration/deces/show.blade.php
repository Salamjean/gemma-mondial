@extends('layouts.dashboard', ['title' => 'Détails de declaration n° ' . $declaration->deces->numero_declaration])

@section('content')
    <div class="">
        <div class="container-full">

            <section class="content">

                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="box box-widget widget-user" style="position: relative;">
                            <div class="widget-user-header bg-img bbsr-0 bber-0" style="" data-overlay="5">
                                <h3 class="widget-user-username text-white">
                                    Personne concernée :
                                    @if ($declaration->patient_id != null)
                                        Patient
                                    @else
                                        Nouveau née
                                    @endif
                                </h3>
                                <h6 class="widget-user-desc text-white"></h6>
                            </div>
                            <div class=""
                                style="position: absolute; border-radius:100%; background-color:white; top:13%; left:2%;">

                                    @if ($declaration->patient->img_url != null)
                                        <img src="{{ asset('assets/uploads/patient/' . $declaration->patient->img_url) }}"
                                            class="rounded-circle" alt="Photo de profil" />
                                    @elseif ($declaration->patient->gender == 'masculin')
                                        <img src="{{ asset('assets/images/avatar/6.png') }}" class="rounded-circle"
                                            alt="Photo de profil" />
                                    @elseif($declaration->patient->gender == 'feminin')
                                        <img src="{{ asset('assets/images/avatar/2.png') }}" class="rounded-circle"
                                            alt="Photo de profil" />
                                    @endif


                            </div>
                            <div class="box-footer" style="padding-top: 100px;">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Numéro de certificat : </div>
                                            <h5 class="declaration-header-description">
                                                {{ $declaration->reference }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Numéro de déclaration de décès :</div>
                                            <h5 class="declaration-header-description">
                                                {{ optional($declaration->deces)->reference ?? 'N/A' }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                                <div class="declaration-header-title">Nom & Prénom :</div>
                                                <h5 class="declaration-header-description">
                                                    {{ optional(optional($declaration->patient)->user)->name ?? 'Défunt' }} {{ optional(optional($declaration->patient)->user)->prenom ?? '' }}
                                                </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                                <div class="declaration-header-title">Code du patient :</div>
                                                <h5 class="declaration-header-description">
                                                    {{ optional($declaration->patient)->code_patient ?? 'N/A' }}
                                                </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Résidence habituelle :</div>
                                            <h5 class="declaration-header-description">
                                                {{ optional(optional($declaration->patient)->habitualResidence)->name ?? optional($declaration->patient)->address ?? 'Non renseignée' }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Milieu de résidence :</div>
                                            <h5 class="declaration-header-description">
                                                {{ optional($declaration->deces)->milieu_residence ?? 'Urbain' }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Date décès :</div>
                                            <h5 class="declaration-header-description">
                                                {{ !empty(optional($declaration->deces)->date) ? \Carbon\Carbon::parse($declaration->deces->date)->format('d/m/Y') : \Carbon\Carbon::parse($declaration->created_at)->format('d/m/Y') }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Heure décès :</div>
                                            <h5 class="declaration-header-description">
                                                {{ !empty(optional($declaration->deces)->heure) ? \Carbon\Carbon::parse($declaration->deces->heure)->format('H:i') : 'N/A' }}
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="declaration-header-title">Âge du patient :</div>
                                        <div class="decalration-header">
                                            <h5 class="declaration-header-description">
                                                 {{ optional($declaration->deces)->age !== null ? optional($declaration->deces)->age . ' an(s)' : 'N/A' }}
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Lieu de décès :</div>
                                            <h5 class="declaration-header-description">
                                                {{ optional($declaration->deces)->lieu ?? 'N/A' }}
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Décès maternel :</div>
                                            <h5 class="declaration-header-description">
                                                 {{ ucfirst(optional($declaration->deces)->deces_maternel ?? 'non') }}
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">

                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title ">Cause initiale du décès :</div>
                                            <h5 class="declaration-header-description declaration-header-description-12">

                                                {{ $declaration->deces->cause_initiale }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Cause directe du décès :</div>
                                            <h5 class="declaration-header-description declaration-header-description-12">

                                                {{ $declaration->deces->cause_directe }}
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Observations :</div>
                                            <h5 class="declaration-header-description declaration-header-description-12">

                                                {{ $declaration->deces->observations }}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </section>
        </div>
    </div>
    <style>
        .decalration-header
        {
            margin-bottom: 10px;
        }
        .declaration-header-title
        {
            font-weight: bold;
            font-size: 17px;
        }
        .declaration-header-description
        {
            font-size: 16px;
            font-style: italic;
        }
        .declaration-header-description-12
        {
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            font-style: italic;
        }
    </style>
@endsection
