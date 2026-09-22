@extends('layouts.dashboard', ['title' => 'Détails de declaration n° ' . $declaration->naissance ->numero_declaration])

@section('content')
    <div class="">
        <div class="container-full">

            <section class="content">

                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="box box-widget widget-user" style="position: relative;">
                            <div class="widget-user-header bg-img bbsr-0 bber-0" style="" data-overlay="5">
                                <h3 class="widget-user-username text-white">
                                        Nouveau née
                                </h3>
                                <h6 class="widget-user-desc text-white"></h6>
                            </div>
                            <div class=""
                                style="position: absolute; border-radius:100%; background-color:white; top:13%; left:2%;">

                                    @if ($declaration->patient->img_url != null)
                                        <img src="{{ asset('assets/uploads/patient/' . $declaration->patient->img_url) }}" width="120px" height="120px"
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
                                            <div class="declaration-header-title">Numéro de certificat médical (CMN) :</div>
                                            <h5 class="declaration-header-description">
                                                <span class="badge badge-primary fs-6">{{ $declaration->reference }}</span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Numéro de déclaration (DN) :</div>
                                            <h5 class="declaration-header-description">
                                                <span class="badge badge-info fs-6">{{ optional($declaration->naissance)->reference ?? '-' }}</span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Code DM Nouveau-né (Enfant) :</div>
                                            <h5 class="declaration-header-description">
                                                <span class="badge badge-success fs-6 fw-bold">
                                                    {{ optional(optional($declaration->naissance)->enfant)->code_patient ?? 'Généré à la déclaration' }}
                                                </span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Mère (Code DM & Nom) :</div>
                                            <h5 class="declaration-header-description">
                                                <b>{{ optional(optional($declaration->patient)->user)->name }} {{ optional(optional($declaration->patient)->user)->prenom }}</b>
                                                <span class="badge badge-secondary fs-7">({{ optional($declaration->patient)->code_patient }})</span>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title"></div>
                                            <h5 class="declaration-header-description">
                                                    <div class="declaration-header-title">Residence habituelle :</div>
                                                    <h5 class="declaration-header-description">
                                                        {{ $declaration->patient->habitualResidence->name }}
                                                    </h5>

                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Millieu de residence habituelle :</div>
                                            <h5 class="declaration-header-description">

                                                {{ $declaration->milieu_residence }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Date naissance :</div>
                                            <h5 class="declaration-header-description">

                                                {{ \Carbon\Carbon::parse($declaration->date)->format('d/m/Y') }}
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Heure naissance :</div>
                                            <h5 class="declaration-header-description">

                                                {{ \Carbon\Carbon::parse($declaration->heure)->format('H:i') }}
                                            </h5>
                                        </div>
                                    </div>



                                    <div class="col-sm-6">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Lieu de naissance :</div>
                                            <h5 class="declaration-header-description">

                                                {{ $declaration->hospital->localite }}
                                            </h5>
                                        </div>
                                    </div>

                                        <div class="col-sm-6">
                                            <div class="decalration-header">
                                                <div class="declaration-header-title">Etat de l'enfant à la naissance :</div>
                                                <h5 class="declaration-header-description">
                                                     {{ $declaration->naissance->nee }}
                                                </h5>
                                            </div>
                                        </div>

                                    <div class="col-sm-12">

                                    </div>

                                    <div class="col-sm-12">
                                        <div class="decalration-header">
                                            <div class="declaration-header-title">Observations :</div>
                                            <h5 class="declaration-header-description declaration-header-description-12">

                                                {{ $declaration->naissance->observations }}
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
