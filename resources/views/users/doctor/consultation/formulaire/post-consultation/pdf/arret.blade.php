<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Arret</title>
    <style>
        body {
            font-family: Verdana, Geneva, Tahoma, sans-serif;
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

        #watermark {
            position: fixed;
            top: 150px;
            left: 20px;
            width: 100%;
            height: 100%;
            z-index: -2000;
            opacity: 0.05;
        }

        .img__content {
            width: 100%;
            height: 70%
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

        .content {
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <div id="watermark">
        <img class="img__content" src="{{ pdf_img('assets/uploads/arret.jpg') }}" />
    </div>
    <footer>
        Art. 285. Quiconque se rend coupable de fraude ou de fausse déclaration ou se fait délivrer un des documents
        prévus à l'article précédent, soit en faisant de fausses déclarations, soit en prenant un faux nom ou une fausse
        qualité, soit en fournissant de faux renseignements, certificats ou attestations est puni de deux(02) à dix(10)
        ans et d'une amende de 200.000 à 2.000.000 de francs Franc CFA selon Art 281 et 223
    </footer>

    <div class="container">
        <div class="header__section">
            @if ($arret->consultation->hospital->img_url != null)
                <img src="{{ pdf_img('assets/uploads/hospital/' . $arret->consultation->hospital->img_url) }}"
                    alt="hôpital" width="100">
            @else
                <div><img src="{{ pdf_img('assets/uploads/ministere.jpg') }}" alt=""></div>
            @endif
            <h1>Arrêt de travail</h1>
            <div style="position: absolute; top:0; right :0;"><img src="{{ pdf_img('assets/uploads/republique.png') }}"
                    alt=""></div>

        </div>

        <div class="section" style="margin-top: 30px;">
            <div class="section-content-nob">
                <p>Formation Sanitaire :&nbsp;&nbsp;&nbsp; {{ $arret->consultation->hospital->label }}</p>
                <p>Commune de Naissance : {{ $arret->consultation->hospital->localite }}</p>
                <p>Date : {{ Carbon\Carbon::parse($arret->consultation->created_at)->format('d/m/Y - H:i:s') }}</p>
                <p>N° Déclaration : {{ $arret->consultation->code_consultation }}</p>
                <p>N° Dossier Medical :<b> {{ $arret->consultation->patient->code_patient }}</b></p>
            </div>
        </div>

        <div class="section" style="margin-top: 30px;">
            <div class="section-content">
                <p class="content" style="margin-top: 20px;">
                    Le {{ dateCompletFr($arret->created_at) }} , à {{ heureFr($arret->created_at) }} à
                    " {{ $arret->consultation->hospital->label }} ".
                </p>
                <br>
                <p>
                    @if ($arret->consultation->doctor)
                        Je, soussigné(e) Docteur {{ $arret->consultation->doctor->user->name }} , certifie avoir
                        examiner :
                    @else
                        Je, soussigné(e) Mme/M {{ $arret->consultation->infirmier->user->name }} , certifie avoir
                        examiner :
                    @endif
                    @if ($arret->consultation->patient->gender == 'masculin')
                        Mr
                    @else
                        Mme
                    @endif
                    {{ $arret->consultation->patient->user->name }} {{ $arret->consultation->patient->user->prenom }}
                    et lui avoir prescrit un arrêt de travail de {{ $arret->nb_jour }} jours allant du
                    {{ dateFr($arret->date_debut) }} au {{ dateFr($arret->date_fin) }}, inclus.
                </p>

                <br>
                <br>
                <br>
                <div>
                    <p style="float: right;margin-right : 50px;">
                        Fait à {{ $arret->consultation->hospital->localite }} . Le {{ date('d-m-Y') }}.
                    </p>
                </div>
                <br>
                <br>
                <br>
                <br>
                <div>
                    <p style="float: right;margin-right : 150px;">
                        @if ($arret->consultation->doctor)
                            Le Médecin :
                        @else
                            L'infirmier(e) :
                        @endif

                    </p>
                    <br>
                    <br>

                    <div style="color: gray;float: right;margin-right : 150px;">
                        @if ($arret->consultation->doctor)
                            {{ $arret->consultation->doctor->user->name }}
                            {{ $arret->consultation->doctor->user->prenom }}
                        @else
                            {{ $arret->consultation->infirmier->user->name }}
                            {{ $arret->consultation->infirmier->user->prenom }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
