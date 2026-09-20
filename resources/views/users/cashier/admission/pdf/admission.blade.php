<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Facture</title>
</head>

<body>
    @if ($payment->type == 'admission')
        @if ($payment->admission->prestationHospital->prestationService->service->libelle != 'Soins infirmier')
            <div>
                <div class="header__section">
                    <div><img src="{{ asset('assets/uploads/ministere.jpg') }}" style="width: 100px; height:100px"
                            alt=""></div>
                    <h1>ADMISSION : {{ $payment->admission->code_admission }}</h1>
                    <div style="position: absolute; top:0; right :0;"><img
                            src="{{ asset('assets/uploads/republique.png') }}" style="width: 100px; height:100px"
                            alt=""></div>
                </div>

                <div style="margin-top: 30px;">
                    <table class="table table-striped table-hover" style="border: none">
                        <tbody>
                            <tr>
                                <td><b>N° Dossier Médical : </b></td>
                                <td>{{ $payment->admission->patient->code_patient }}</td>
                            </tr>
                            <tr>
                                <td><b>Nom & Prénom(s) : </b></td>
                                <td>{{ $payment->admission->patient->user->name }}&nbsp;{{ $payment->admission->patient->user->prenom }}
                                </td>
                            </tr>

                            @php
                                $dateNaissance = \Carbon\Carbon::createFromFormat('d/m/Y', $payment->admission->patient->birth_date);
                                $age = $dateNaissance->diffInYears(\Carbon\Carbon::now());
                            @endphp
                            <tr>
                                <td><b>Age : </b></td>
                                <td>{{ $age }} ans</td>
                            </tr>
                            <tr>
                                <td><b>Sexe : </b></td>
                                <td>{{ $payment->admission->patient->gender }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br /><br />
                    <div class="section-content">
                        <div>
                            <ul>
                                <li><b>Type d'admission: </b>&nbsp;&nbsp;&nbsp;{{ $payment->admission->type_admission }}
                                </li>
                                <li><b>Mode d'entrée: </b> &nbsp;&nbsp;&nbsp;{{ $payment->admission->mode_entree }}</li>
                                <li><b>Motif de la consultation : </b>&nbsp;&nbsp;&nbsp;
                                    {{ $payment->admission->motif_consultation }}</li>
                            </ul>
                        </div>
                        <br />
                        <div>
                            @if ($payment->type_assurance_id != null)
                                <h4><b>Montant à payer : </b> <span class="badge-price">{{ $payment->prix_normal }}
                                    Frs
                                    CFA</span></h4>

                                    <h4><b>Type d'assurance : </b> <span >{{ $payment->typeAssurance->libelle }}</span></h4>
                                    <h4><b>Pourcentage : </b> <span >{{ $payment->assurance->percent * 100 }} %</span></h4>

                            <h4><b>Montant payer : </b> <span class="badge-price">{{ $payment->prix }}
                                    Frs
                                    CFA</span></h4>
                            @else
                                <h4><b>Montant payer : </b> <span class="badge-price">{{ $payment->prix_normal }}
                                    Frs
                                    CFA</span></h4>
                            <h4><b>Mode de règlement : </b>
                                @if ($payment->mode_paiement == 'mobile_money')
                                    <span>Mobile Money ({{ $payment->operateur_mobile ?? 'N/A' }}) @if($payment->reference_paiement) - Réf: {{ $payment->reference_paiement }} @endif</span>
                                @else
                                    <span>Espèce</span>
                                @endif
                            </h4>
                            <h4><b>Statut : </b>
                                @if ($payment->admission->statut_paiement == 1)
                                    <span class="badge-status">Consultation payé</span> <span
                                        style="float: right;margin-right : 50px;"><b>Approuvé par : </b> <span
                                            class="badge-name">
                                        {{ $payment->admission->cashier->user->name }}</span></span>@else<span
                                        class="badge-encours">Paiement en cours</span><span
                                        style="float: right;margin-right : 50px;"><b>( Passez à la caisse pour la
                                            suite... )
                                        </b> </span>
                                @endif
                            </h4>
                        </div>
                        <br />
                        <div>
                            <h4><u>Medecin en Charge</u></h4>
                            <h3><strong>{{ $payment->admission->doctor->user->name }}&nbsp;{{ $payment->admission->doctor->user->prenom }}</strong>
                            </h3>
                            <p><strong>Matricule :</strong> {{ $payment->admission->doctor->matricule }} &nbsp;
                                <strong>Contact : </strong> {{ $payment->admission->doctor->contact }}
                            </p>
                            <p><strong>Département :</strong>
                                {{ $payment->admission->doctor->serviceHospital->service->libelle }}&nbsp;<strong>Type
                                    agent : </strong> {{ $payment->admission->doctor->typeAgent->libelle }}</p>

                        </div>
                        <br />
                        <div style="float: right;margin-right : 50px;">
                            <p>Fait à {{ $payment->admission->hospital->localite }}. Le {{ date('d/m/Y') }}. </p>
                        </div>
                        <br />

                    </div>
                </div>
            </div>
        @else
            <div>
                <div class="header__section">
                    <div><img src="{{ asset('assets/uploads/ministere.jpg') }}" style="width: 100px; height:100px"
                            alt=""></div>
                    <h1>ADMISSION : {{ $payment->admission->code_admission }}</h1>
                    <div style="position: absolute; top:0; right :0;"><img
                            src="{{ asset('assets/uploads/republique.png') }}" style="width: 100px; height:100px"
                            alt=""></div>
                </div>

                <div style="margin-top: 30px;">
                    <table class="table table-striped table-hover" style="border: none">
                        <tbody>
                            <tr>
                                <td><b>N° Dossier Médical : </b></td>
                                <td>{{ $payment->admission->patient->code_patient }}</td>
                            </tr>
                            <tr>
                                <td><b>Nom & Prénom(s) : </b></td>
                                <td>{{ $payment->admission->patient->user->name }}&nbsp;{{ $payment->admission->patient->user->prenom }}
                                </td>
                            </tr>

                            @php
                                $dateNaissance = \Carbon\Carbon::createFromFormat('d/m/Y', $payment->admission->patient->birth_date);
                                $age = $dateNaissance->diffInYears(\Carbon\Carbon::now());
                            @endphp
                            <tr>
                                <td><b>Age : </b></td>
                                <td>{{ $age }} ans</td>
                            </tr>
                            <tr>
                                <td><b>Sexe : </b></td>
                                <td>{{ $payment->admission->patient->gender }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <br /><br />
                    <div class="section-content">
                        <div>
                            <ul>
                                <li><b>Type d'admission:
                                    </b>&nbsp;&nbsp;&nbsp;{{ $payment->admission->type_admission }}
                                </li>
                                <li><b>Mode d'entrée: </b> &nbsp;&nbsp;&nbsp;{{ $payment->admission->mode_entree }}
                                </li>
                                <li><b>Motif de la consultation : </b>&nbsp;&nbsp;&nbsp;
                                    {{ $payment->admission->motif_consultation }}</li>
                            </ul>
                        </div>
                        <br />
                        <div>
                            @if ($payment->type_assurance_id != null)
                                <h4><b>Montant à payer : </b> <span class="badge-price">{{ $payment->prix_normal }}
                                    Frs
                                    CFA</span></h4>

                                    <h4><b>Type d'assurance : </b> <span >{{ $payment->typeAssurance->libelle }}</span></h4>
                                    <h4><b>Pourcentage : </b> <span >{{ $payment->assurance->percent * 100 }} %</span></h4>

                            <h4><b>Montant payer : </b> <span class="badge-price">{{ $payment->prix }}
                                    Frs
                                    CFA</span></h4>
                            @else
                                <h4><b>Montant payer : </b> <span class="badge-price">{{ $payment->prix_normal }}
                                    Frs
                                    CFA</span></h4>
                            @endif
                            <h4><b>Mode de règlement : </b>
                                @if ($payment->mode_paiement == 'mobile_money')
                                    <span>Mobile Money ({{ $payment->operateur_mobile ?? 'N/A' }}) @if($payment->reference_paiement) - Réf: {{ $payment->reference_paiement }} @endif</span>
                                @else
                                    <span>Espèce</span>
                                @endif
                            </h4>
                            <h4><b>Statut : </b>
                                @if ($payment->admission->statut_paiement == 1)
                                    <span class="badge-status">Soin infirmier payé </span> <span
                                        style="float: right;margin-right : 50px;"><b>Approuvé par : </b> <span
                                            class="badge-name">
                                        {{ $payment->admission->cashier->user->name }}</span></span>@else<span
                                        class="badge-encours">Paiement en cours</span><span
                                        style="float: right;margin-right : 50px;"><b>( Passez à la caisse pour la
                                            suite... )
                                        </b> </span>
                                @endif
                            </h4>
                        </div>
                        <br />
                        <div>
                            <h4><u>Medecin en Charge</u></h4>
                            <h3><strong>{{ $payment->admission->infirmier->user->name }}&nbsp;{{ $payment->admission->infirmier->user->prenom }}</strong>
                            </h3>
                            <p><strong>Matricule :</strong> {{ $payment->admission->infirmier->matricule }} &nbsp;
                                <strong>Contact : </strong> {{ $payment->admission->infirmier->contact }}
                            </p>
                            <p><strong>Département :</strong>
                                {{ $payment->admission->infirmier->serviceHospital->service->libelle }}&nbsp;

                        </div>
                        <br />
                        <div style="float: right;margin-right : 50px;">
                            <p>Fait à {{ $payment->admission->hospital->localite }}. Le {{ date('d/m/Y') }}. </p>
                        </div>
                        <br />

                    </div>
                </div>
            </div>
        @endif
    @elseif ($payment->type == 'hospitalisation')
        <div>
            <div class="header__section">
                <div><img src="{{ asset('assets/uploads/ministere.jpg') }}" style="width: 100px; height:100px"
                        alt=""></div>
                <h1>HOSPITALISATION ( {{ dateNumberFr($payment->hospitalisation->updated_at) }} )</h1>
                <div style="position: absolute; top:0; right :0;"><img
                        src="{{ asset('assets/uploads/republique.png') }}" style="width: 100px; height:100px"
                        alt=""></div>
            </div>

            <div style="margin-top: 30px;">
                <table class="table table-striped table-hover" style="border: none">
                    <tbody>
                        <tr>
                            <td><b>N° Dossier Médical : </b></td>
                            <td>{{ $payment->hospitalisation->consultation->patient->code_patient }}</td>
                        </tr>
                        <tr>
                            <td><b>Nom & Prénom(s) : </b></td>
                            <td>{{ $payment->hospitalisation->consultation->patient->user->name }}&nbsp;{{ $payment->hospitalisation->consultation->patient->user->prenom }}
                            </td>
                        </tr>

                        @php
                            $dateNaissance = \Carbon\Carbon::createFromFormat('d/m/Y', $payment->hospitalisation->consultation->patient->birth_date);
                            $age = $dateNaissance->diffInYears(\Carbon\Carbon::now());
                        @endphp
                        <tr>
                            <td><b>Age : </b></td>
                            <td>{{ $age }} ans</td>
                        </tr>
                        <tr>
                            <td><b>Sexe : </b></td>
                            <td>{{ $payment->hospitalisation->consultation->patient->gender }}</td>
                        </tr>
                    </tbody>
                </table>
                <br /><br />
                <div class="section-content">
                    <div>
                        <ul>
                            <li><b>Type :
                                </b>&nbsp;&nbsp;&nbsp;{{ $payment->hospitalisation->type }}
                            </li>
                            <li><b>Mode d'entrée: </b>
                                &nbsp;&nbsp;&nbsp;{{ $payment->hospitalisation->mot_sortie }}
                            </li>
                            <li><b>Nombre de jours : </b>&nbsp;&nbsp;&nbsp;
                                {{ $payment->hospitalisation->number_days }} jr(s)</li>
                        </ul>
                    </div>
                    <br />
                    <div>
                        @if ($payment->type_assurance_id != null)
                                <h4><b>Montant à payer : </b> <span class="badge-price">{{ $payment->prix_normal }}
                                    Frs
                                    CFA</span></h4>

                                    <h4><b>Type d'assurance : </b> <span >{{ $payment->typeAssurance->libelle }}</span></h4>
                                    <h4><b>Pourcentage : </b> <span >{{ $payment->assurance->percent * 100 }} %</span></h4>

                            <h4><b>Montant payer : </b> <span class="badge-price">{{ $payment->prix }}
                                    Frs
                                    CFA</span></h4>
                            @else
                                <h4><b>Montant payer : </b> <span class="badge-price">{{ $payment->prix_normal }}
                                    Frs
                                    CFA</span></h4>
                            @endif
                        <h4><b>Mode de règlement : </b>
                            @if ($payment->mode_paiement == 'mobile_money')
                                <span>Mobile Money ({{ $payment->operateur_mobile ?? 'N/A' }}) @if($payment->reference_paiement) - Réf: {{ $payment->reference_paiement }} @endif</span>
                            @else
                                <span>Espèce</span>
                            @endif
                        </h4>
                        <h4><b>Statut : </b>
                            @if ($payment->status == 'success')
                                <span class="badge-status">Consultation payé</span> <span
                                    style="float: right;margin-right : 50px;"><b>Approuvé par : </b> <span
                                        class="badge-name">
                                    {{ $payment->cashier->user->name }}</span></span>@else<span
                                    class="badge-encours">Paiement en cours</span><span
                                    style="float: right;margin-right : 50px;"><b>( Passez à la caisse pour la
                                        suite... )
                                    </b> </span>
                            @endif
                        </h4>
                    </div>
                    <br />
                    <div>
                        <h4><u>Medecin en Charge</u></h4>
                        <h3><strong>{{ $payment->hospitalisation->consultation->doctor->user->name }}&nbsp;{{ $payment->hospitalisation->consultation->doctor->user->prenom }}</strong>
                        </h3>
                        <p><strong>Matricule :</strong>
                            {{ $payment->hospitalisation->consultation->doctor->matricule }} &nbsp; <br>
                            <strong>Contact : </strong>
                            {{ $payment->hospitalisation->consultation->doctor->contact }}
                        </p>
                        <p><strong>Département :</strong>
                            {{ $payment->hospitalisation->consultation->doctor->serviceHospital->service->libelle }}&nbsp;
                        </p>

                    </div>
                    <br />
                    <div style="float: right;margin-right : 50px;">
                        <p>Fait à {{ $payment->hospitalisation->consultation->hospital->localite }}. Le
                            {{ date('d/m/Y') }}. </p>
                    </div>

                </div>
            </div>
    @endif
    <div class="footer">
        <hr />

        Art. 285. Quiconque se rend coupable de fraude ou de fausse déclaration ou se fait délivrer un des
        documents prévus à l'article précédent, soit en faisant de fausses déclarations, soit en prenant un faux
        nom ou une fausse qualité, soit en fournissant de faux renseignements, certificats ou attestations est
        puni de deux(02) à dix(10) ans et d'une amende de 200.000 à 2.000.000 de francs Franc CFA selon Art 281
        et 223
    </div>
</body>

<style>
    body {
        font-family: Arial, sans-serif;
    }

    h1 {
        text-align: center;
        margin-bottom: 10px;
    }

    .section {
        margin-bottom: 10px;
    }

    .section-content {
        padding: 10px;

    }

    .section-content p {
        margin: 0;
    }

    .header__section {
        position: relative;
    }

    img {
        width: 100px;
    }

    h1 {
        text-transform: uppercase;
        font-size: 28px;
    }

    .footer {
        position: fixed;
        bottom: 2px;
        left: 0px;
        right: 0px;
        height: 60px;
        color: gray;
        text-align: center;
        font-size: 14px;
        line-height: 20px;

    }

    .content {
        margin-bottom: 30px;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        border: 1px solid #ddd;
    }

    th,
    td {
        text-align: left;
        padding: 6px;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    ul {
        list-style-type: none;
        /* Remove bullets */
        padding: 0;
        /* Remove padding */
        margin: 0;
        /* Remove margins */
    }

    ul li {
        border: 1px solid #ebf6f4;
        /* Add a thin border to each list item */
        margin-top: -1px;
        /* Prevent double borders */
        background-color: #fbfffe;
        /* Add a grey background color */
        padding: 12px;
        /* Add some padding */
    }

    .badge-price {
        background-color: rgb(37, 128, 254);
        color: white;
        padding: 4px 8px;
        text-align: center;
        border-radius: 5px;
    }

    .badge-name {
        background-color: rgb(224, 63, 141);
        color: white;
        padding: 4px 8px;
        text-align: center;
        border-radius: 5px;
    }

    .badge-status {
        background-color: rgb(7, 241, 225);
        color: white;
        padding: 4px 8px;
        text-align: center;
        border-radius: 5px;
    }

    .badge-encours {
        background-color: rgb(255, 120, 24);
        color: white;
        padding: 4px 8px;
        text-align: center;
        border-radius: 5px;
    }
</style>
