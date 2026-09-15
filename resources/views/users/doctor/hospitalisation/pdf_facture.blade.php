<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture d'Hospitalisation N° {{ $hospitalisation->code ?? $hospitalisation->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #1a5276;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8f9f9;
            border: 1px solid #d5dbdb;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            font-size: 11px;
            border: none;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            width: 25%;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #bdc3c7;
            padding: 8px;
            font-size: 11px;
        }
        .data-table th {
            background-color: #2980b9;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }
        .data-table tr:nth-child(even) {
            background-color: #f2f4f4;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .total-box {
            float: right;
            width: 40%;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 8px;
            font-size: 12px;
            border: 1px solid #bdc3c7;
        }
        .total-label {
            font-weight: bold;
            background-color: #eaeded;
        }
        .total-amount {
            font-weight: bold;
            font-size: 14px;
            color: #27ae60;
            background-color: #e8f8f5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #d5dbdb;
            font-size: 10px;
            color: #7f8c8d;
            text-align: center;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success { background-color: #27ae60; }
        .badge-warning { background-color: #f39c12; }
    </style>
</head>
<body>
    @php
        $patient = $hospitalisation->consultation->patient ?? ($hospitalisation->consultation->admission->patient ?? null);
        $user = $patient->user ?? null;
        $patientCode = $patient->code_patient ?? 'N/A';
        $patientName = trim(($user->name ?? '') . ' ' . ($user->prenom ?? ''));
        $birthDate = $patient->birth_date ?? 'N/A';
        $contact = $user->telephone ?? ($patient->phone ?? 'N/A');
        $gender = $patient->gender ?? 'N/A';
        $residence = $patient->place_residence ?? ($patient->residence_actuelle ?? 'N/A');
        $hospital = $hospitalisation->consultation->hospital ?? null;
        $totalChambres = 0;
        $totalMedicaments = 0;
    @endphp

    <div class="title">FACTURE D'HOSPITALISATION</div>
    <div class="subtitle">Référence N° : {{ $hospitalisation->code ?? $hospitalisation->id }} | Date : {{ date('d/m/Y') }}</div>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="info-label">N° Dossier Médical :</td>
                <td><b>{{ $patientCode }}</b></td>
                <td class="info-label">Nom & Prénom(s) :</td>
                <td><b>{{ $patientName }}</b></td>
            </tr>
            <tr>
                <td class="info-label">Né(e) le :</td>
                <td>{{ $birthDate }}</td>
                <td class="info-label">Sexe :</td>
                <td>{{ ucfirst($gender) }}</td>
            </tr>
            <tr>
                <td class="info-label">Téléphone :</td>
                <td>{{ $contact }}</td>
                <td class="info-label">Résidence :</td>
                <td>{{ $residence }}</td>
            </tr>
            <tr>
                <td class="info-label">Date début :</td>
                <td>{{ dateNumberFr($hospitalisation->created_at) }}</td>
                <td class="info-label">Statut :</td>
                <td>
                    @if ($hospitalisation->status == 'in_progress')
                        <span class="badge badge-warning">En cours</span>
                    @else
                        <span class="badge badge-success">Clôturée (Terminée)</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <h3>Détail des séjours & hébergements</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Chambre & Lit</th>
                <th class="text-center">Prix unitaire / Jour</th>
                <th class="text-center">Jours</th>
                <th class="text-right">Montant Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hospitalisation->daysHospitalisation as $day)
                @php
                    $bedPrice = $day->bed ? $day->bed->price : 0;
                    $nbDays = $day->number_days ?? 1;
                    if ($nbDays == 0) $nbDays = 1;
                    $subTotal = $bedPrice * $nbDays;
                    $totalChambres += $subTotal;
                @endphp
                <tr>
                    <td>{{ dateNumberFr($day->day) }}</td>
                    <td>
                        @if ($day->bed)
                            Chambre {{ $day->bed->bedroom->number ?? 'N/A' }} - Lit n° {{ $day->bed->number }}
                        @else
                            Non attribué
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($bedPrice, 0, ',', ' ') }} FCFA</td>
                    <td class="text-center">{{ $nbDays }}</td>
                    <td class="text-right">{{ number_format($subTotal, 0, ',', ' ') }} FCFA</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Aucune journée d'hospitalisation enregistrée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>Traitements & Protocoles Thérapeutiques</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Médicament / Soin</th>
                <th class="text-center">Type</th>
                <th class="text-center">Quantité</th>
                <th>Dosage</th>
            </tr>
        </thead>
        <tbody>
            @php $hasProtocols = false; @endphp
            @foreach ($hospitalisation->daysHospitalisation as $day)
                @foreach ($day->therapeutiqueProtocols as $therap)
                    @php $hasProtocols = true; @endphp
                    <tr>
                        <td>
                            @if ($therap->protocol_type == 'internal')
                                {{ $therap->drugHospital->drug->name ?? 'N/A' }}
                            @else
                                {{ $therap->drug->name ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="text-center">{{ ucfirst($therap->protocol_type ?? 'Interne') }}</td>
                        <td class="text-center">{{ $therap->quantity }}</td>
                        <td>{{ $therap->dosage }}</td>
                    </tr>
                @endforeach
            @endforeach

            @if (!$hasProtocols)
                <tr>
                    <td colspan="4" class="text-center">Aucun traitement administré.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="total-box">
        <table class="total-table">
            <tr>
                <td class="total-label">Frais d'hébergement :</td>
                <td class="text-right">{{ number_format($totalChambres, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td class="total-label total-amount">NET À PAYER :</td>
                <td class="text-right total-amount">{{ number_format($hospitalisation->price ?? $totalChambres, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        Document généré automatiquement le {{ date('d/m/Y à H:i') }}.
    </div>
</body>
</html>
