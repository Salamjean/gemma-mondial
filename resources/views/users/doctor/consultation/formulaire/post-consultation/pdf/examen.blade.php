<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bulletin d'examen médical</title>

</head>

<body>
    <div class="container">
        <div class="header__section">
            @if ($bulletin->consultation->hospital->img_url != null)
                <img src="{{ pdf_img('assets/uploads/hospital/' . $bulletin->consultation->hospital->img_url) }}"
                    alt="hôpital" width="100">
            @else
                <div><img src="{{ pdf_img('assets/uploads/ministere.jpg') }}" alt=""></div>
            @endif
            <div class="section-content-nob">
                <div style="font-size: 18px;margin-top: none">
                    <h5>{{ $bulletin->consultation->hospital->label }}</h5>
                </div>

            </div>
            </br></br></br>
            <h1>BULLETIN D’EXAMEN MEDICAL</h1>
            <div style="position: absolute; top:0; right :0;">
                <img src="{{ pdf_img('assets/uploads/republique.png') }}" alt="">
            </div>
        </div>

        <div class="bg-img">
            <img src="{{ pdf_img('assets/uploads/bulletin_examen.png') }}"
                style="top:305px; position:fixed; width:700px; opacity: 0.1; " />
        </div>
        <div class="section" style="margin-top: 20px;">
            <div class="section-content">
                <table class="table scroll" style="border: none">
                    <tr style="border: none">
                        <td style="border: none">
                            <div class="section-content-nob">

                                <div style="font-size: 18px;margin-top: 7px">N° Bulletin : <span
                                        style="font-weight: 900">{{ $bulletin->code_bulletin }}</span></div>
                                <div style="font-size: 18px;margin-top: 7px">Date :
                                    <b>{{ $bulletin->created_at->format('d/m/Y - H:i:s') }}</b></div>

                            </div>
                        </td>
                        <td style="border: none">
                            <div>N° Dossier Médical : <b>{{ $bulletin->consultation->patient->code_patient }}</b></div>
                            <div style="margin-top: 7px">Nom : {{ $bulletin->consultation->patient->user->name }}</div>
                            <div style="margin-top: 7px">Prénom(s) :
                                {{ $bulletin->consultation->patient->user->prenom }}</div>
                            @php
                                $dateNaissance = \Carbon\Carbon::createFromFormat('d/m/Y', $bulletin->consultation->patient->birth_date);
                                $age = $dateNaissance->diffInYears(\Carbon\Carbon::now());
                            @endphp
                            <div style="margin-top: 7px"><span> Age : {{ $age }}
                                    ans</span>&nbsp;&nbsp;&nbsp;<span> Sexe :
                                    {{ $bulletin->consultation->patient->gender }}</span> </div>
                        </td>

                    </tr>
                    <tr style="border: none">
                        <td style="border: none">
                            <h4 style="margin-top: -30px"><u>NATURE DE L'EXAMEN</u></h4>
                            @forelse ($bulletin->examens as $item)
                                <ul
                                    style="border: none; margin-left: -40px;none; margin-right: 20px; margin-top: -1;0px;">
                                    <li style="border: none; width:100%; list-style:none;">{{ $item->nature_examen }}
                                    </li>
                                </ul>
                            @empty
                                <li style="border: none">
                                    Liste d'examen vide !
                                </li style="border: none">
                            @endforelse

                        </td>
                        <td style="border: none">

                            <p>&nbsp;&nbsp;</p>
                            <p>&nbsp;&nbsp;</p>
                            <p>&nbsp;&nbsp;</p>
                            <p>&nbsp;&nbsp;</p>
                            <p>&nbsp;&nbsp;</p>
                            <p>&nbsp;&nbsp;</p>
                            <p>&nbsp;&nbsp;</p>
                            <p>&nbsp;&nbsp;</p>

                        </td>
                    </tr>
                </table>
                <br>


                <div>
                    <p style="float: right;margin-right : 50px;">
                        <u>Signature et Cachet du Médecin</u>
                    </p>
                </div>
                <br>
                <br>
                <br>


            </div>
        </div>

        <footer>
            Art. 285. Quiconque se rend coupable de fraude ou de fausse déclaration ou se fait délivrer un des documents
            prévus à l'article précédent, soit en faisant de fausses déclarations, soit en prenant un faux nom ou une
            fausse qualité, soit en fournissant de faux renseignements, certificats ou attestations est puni de deux(02)
            à dix(10) ans et d'une amende de 200.000 à 2.000.000 de francs Franc CFA selon Art 281 et 223
        </footer>
    </div>



</body>
<style>
    body {
        font-family: Arial, sans-serif;
    }

    .container {
        width: 700px;
        margin: 20px auto;
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

    footer {
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

    .patient-info {
        font-size: 18px !important;
        font-weight: 100;
        margin-bottom: 8px !important;
    }

    .content {
        margin-bottom: 30px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 40px;
    }

    .table td {
        padding: 7px 10px;
        border: 1px solid;
    }

    .word-break {
        word-break: break-all;
    }

    .scroll-container {
        overflow: auto;
        margin-bottom: 40px;
        border-left: 1px solid;
        border-right: 1px solid;

    }


    .scroll {
        margin: 0;
    }

    .scroll td:first-of-type {
        position: sticky;
        left: 0;
        border-left: none;
        /*background: #f4f4f4;*/
        /*color: #212121;
        font-weight: bold;*/
    }

    &:after {
        content: "";
        position: absolute;
        top: 0;
        right: -1px;
        height: 100%;
        width: 1px;
        background: #000;
    }


    .scroll td:last-of-type {
        border-right: none;
    }

    @media screen and (max-width: 600px) {
        .responsive thead {
            visibility: hidden;
            height: 0;
            position: absolute;
        }

        .responsive tr {
            display: block;
            margin-bottom: .625em;
        }

        .responsive td {
            border: 1px solid;
            border-bottom: none;
            display: block;
            font-size: .8em;
            text-align: right;
        }

        .responsive td::before {
            content: attr(data-label);
            float: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        .responsive td:last-child {
            border-bottom: 1px solid;
        }

    }
</style>

</html>
