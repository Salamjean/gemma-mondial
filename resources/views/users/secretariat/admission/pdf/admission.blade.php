<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Releve Admission</title>
</head>
<body>
    <div>
        <div class="header__section">
            <div><img src="{{ asset('assets/uploads/ministere.jpg') }}" style="width: 100px; height:100px" alt=""></div>
            <h1>ADMISSION :  {{ $admission->code_admission }}</h1>
            <div style="position: absolute; top:0; right :0;"><img src="{{ asset('assets/uploads/republique.png') }}"  style="width: 100px; height:100px" alt=""></div>
        </div>
        @if(!empty($admission->hospital->watermark_url))
        <div class="bg-img" style="top:250px; position:fixed; width:100%; text-align:center; z-index:-1000; opacity: 0.12;">
            <img src="{{ pdf_img('assets/uploads/hospital/' . $admission->hospital->watermark_url) }}" style="max-width:400px; max-height:400px;"/>
        </div>
        @endif
        <div style="margin-top: 30px;">
            <table class="table table-striped table-hover" style="border: none">
                <tbody>
                    <tr>
                        <td><b>N° Dossier Médical : </b></td>
                        <td>{{ $admission->patient->code_patient }}</td>
                    </tr>
                    <tr>
                        <td><b>Nom & Prénom(s) : </b></td>
                        <td>{{ $admission->patient->user->name }}&nbsp;{{ $admission->patient->user->prenom }}</td>
                    </tr>
                    
                    @php
                        $dateNaissance = \Carbon\Carbon::createFromFormat('d/m/Y', $admission->patient->birth_date);
                        $age = $dateNaissance->diffInYears(\Carbon\Carbon::now());
                    @endphp
                    <tr>
                        <td><b>Age : </b></td>
                        <td>{{ $age }} ans</td>
                    </tr>
                    <tr>
                        <td><b>Sexe : </b></td>
                        <td>{{ $admission->patient->gender }}</td>
                    </tr>
                </tbody>
            </table>
            <br /><br />
            <div class="section-content">
                <div>
                    <ul>
                        <li><b>Type d'admission:  </b>&nbsp;&nbsp;&nbsp;{{ $admission->type_admission }}</li>
                        <li><b>Mode d'entrée:  </b> &nbsp;&nbsp;&nbsp;{{ $admission->mode_entree }}</li>
                        <li><b>Motif de la consultation :   </b>&nbsp;&nbsp;&nbsp; {{ $admission->motif_consultation }}</li>
                    </ul>
                </div>
                <br/>
                <div>
                    <h4><b>Montant à payer : </b> <span class="badge-price">{{ $admission->montant }} Frs CFA</span></h4>
                    <h4><b>Statut : </b>@if($admission->statut_paiement == 1)<span class="badge-status">Consultation payé</span> <span style="float: right;margin-right : 50px;"><b>Approuvé par : </b> <span class="badge-name"> {{ $admission->cashier->user->name }}</span></span>@else<span class="badge-encours">Paiement en cours</span><span style="float: right;margin-right : 50px;"><b>( Passez à la caisse pour la suite... ) </b> </span>@endif</h4>
                </div>
                    <br/>
                <div>
                    @if ($admission->doctor_id)
                    <h4><u>Medecin en Charge</u></h4>
                    <h3><strong>{{ $admission->doctor->user->name }}&nbsp;{{ $admission->doctor->user->prenom }}</strong></h3>
                    <p><strong>Matricule :</strong> {{ $admission->doctor->matricule }} &nbsp; <strong>Contact : </strong> {{ $admission->doctor->contact }}</p>
                    <p><strong>Service :</strong> {{ $admission->prestationHospital->prestationService->libelle}}&nbsp;<strong>Type agent : </strong> {{ $admission->doctor->typeAgent->libelle }}</p>
                    @elseif ($admission->infirmier_id)
                    <h4><u>Infirmier en Charge</u></h4>
                    <h3><strong>{{ $admission->infirmier->user->name }}&nbsp;{{ $admission->infirmier->user->prenom }}</strong></h3>
                    <p><strong>Matricule :</strong> {{ $admission->infirmier->matricule }} &nbsp; <strong>Contact : </strong> {{ $admission->infirmier->contact }}</p>
                    <p><strong>Service :</strong> {{ $admission->prestationHospital->prestationService->libelle}} &nbsp;<strong></p>
                    @endif
                </div>
                <br/>
                <div style="float: right;margin-right : 50px;">
                    <p>Fait à {{ $admission->hospital->localite }}. Le {{ date('d/m/Y') }}. </p>
                </div>
                <br/>
                
            </div>
            <hr/>
        </div>

        <div class="footer">
            Art. 285.  Quiconque se rend coupable de fraude ou de fausse déclaration ou se fait délivrer un des documents prévus à l'article précédent, soit en faisant de fausses déclarations, soit en prenant un faux nom ou une fausse qualité, soit en fournissant de faux renseignements, certificats ou attestations est puni de deux(02) à dix(10) ans et d'une amende de 200.000 à 2.000.000 de francs Franc CFA selon Art 281 et 223
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
        height: 50px;
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

            th, td {
            text-align: left;
            padding: 6px;
            }

            tr:nth-child(even) {
            background-color: #f2f2f2;
            }
            ul {
                list-style-type: none; 
                padding: 0; 
                margin: 0; 
                }

            ul li {
            border: 1px solid #ebf6f4; 
            margin-top: -1px; 
            background-color: #fbfffe; 
            padding: 12px; 
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
            .badge-encours{
                background-color: rgb(255, 120, 24);
                color: white;
                padding: 4px 8px;
                text-align: center;
                border-radius: 5px;
            }
</style>