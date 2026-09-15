<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnance médicale</title>

</head>


<body>
    <div id="watermark">
        <img class="img__content" w src="{{ pdf_img('assets/uploads/ordonnance.jpg') }}" />
    </div>
    <footer>
        Art. 285. Quiconque se rend coupable de fraude ou de fausse déclaration ou se fait délivrer un des documents
        prévus à l'article précédent, soit en faisant de fausses déclarations, soit en prenant un faux nom ou une fausse
        qualité, soit en fournissant de faux renseignements, certificats ou attestations est puni de deux(02) à dix(10)
        ans et d'une amende de 200.000 à 2.000.000 de francs Franc CFA selon Art 281 et 223
    </footer>
    <div class="container">
        <div class="hospital-info">
            @if ($ordonnance->consultation->hospital->img_url != null)
                <img src="{{ pdf_img('assets/uploads/hospital/' . $ordonnance->consultation->hospital->img_url) }}"
                    alt="hôpital" width="100">
            @else
            @endif
            <h3>{{ $ordonnance->consultation->hospital->label }}</h3>
            <p>Adresse: {{ $ordonnance->consultation->hospital->localite }}</p>
            <p>Tel: {{ $ordonnance->consultation->hospital->contact }}</p>
            <p style=""> Date d’édition : {{ dateCompletFr($ordonnance->created_at) }} </p>
        </div>
        <div class="ministry-logo">
            <img src="{{ pdf_img('assets/uploads/republique.png') }}" alt="">
        </div>
        <div style="clear: both;"></div>

        <div class="header">
            <h1>Ordonnance médicale</h1>
        </div>
        <div class="patient-info">
            <p class="patient-name">Code du patient : {{ $ordonnance->consultation->patient->code_patient }}</p>
            <p class=""><strong>Ordonnance n° : </strong> {{ $ordonnance->reference }} </p>
            <p class=""><strong>Nom du patient : </strong> {{ $ordonnance->consultation->patient->user->name }}
                {{ $ordonnance->consultation->patient->user->prenom }}</p>
            <p><strong>Date de naissance :</strong> {{ $ordonnance->consultation->patient->birth_date }} à
                {{ $ordonnance->consultation->patient->birthPlace->name }} </p>
            <p><strong>Adresse :</strong> {{ $ordonnance->consultation->patient->address }}</p>
        </div>
        <div class="prescriptions">
            <h2>Médicaments prescrits :</h2>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Nom du médicament</th>
                        <th>Dosage</th>
                        <th>Quantité</th>
                        @if ($ordonnance->type === 'interne')
                            <th>P.Unitaire</th>
                            <th>P.Total</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordonnance->prescriptions as $medicament)
                        <tr>
                            <td>
                                @if ($ordonnance->type === 'interne')
                                    {{ $medicament->drugHospital->drug->name }}
                                @else
                                    {{ $medicament->drug->name }}
                                @endif
                            </td>
                            <td>{{ $medicament->dosage }}</td>
                            <td>{{ $medicament->quantity }}</td>
                            @if ($ordonnance->type === 'interne')
                                <th>{{ $medicament->drugHospital->price  . ' FCFA' ?? '' }}</th>
                                <th>{{ $medicament->drugHospital->price ? $medicament->drugHospital->price * intval($medicament->quantity) . ' FCFA' : '' }}
                                </th>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            vide...
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
        <div class="doctor-signature">
            @if ($ordonnance->consultation->doctor)
                <p><strong>{{ $ordonnance->consultation->doctor->user->name }}
                        {{ $ordonnance->consultation->doctor->user->prenom }}</strong></p>
                <p>Médecin traitant</p>
            @else
                <p><strong>{{ $ordonnance->consultation->infirmier->user->name }}
                        {{ $ordonnance->consultation->infirmier->user->prenom }}</strong></p>
                <p>Infirmier(e) traitant(e)</p>
            @endif
        </div>

    </div>

    <style>
        #watermark {
            position: fixed;
            top: 250px;
            left: 30px;
            width: 100%;
            height: 100%;
            z-index: -2000;
            opacity: 0.3;
        }

        .img__content {
            width: 90%;
            height: 40%
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

        .hospital-info {
            float: left;
            width: 50%;
            text-align: left;
        }

        .ministry-logo {
            float: right;
            width: 50%;
            text-align: right;
        }

        .header {
            text-align: center;
            text-decoration: underline;
        }

        .ministry-logo img {
            max-width: 100px;
        }

        .prescriptions {
            margin-top: 30px;
            width: 100%;
        }

        .prescription-table {
            width: 100%;
            border-collapse: collapse;
        }

        .prescription-table th,
        .prescription-table td {
            text-align: left;
            padding: 5px;
        }

        .patient-name {
            margin-top: 20px;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .doctor-signature {
            margin-top: 150px;
            text-align: right;
        }
    </style>
</body>

</html>
