<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des Dépenses & Charges</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2b2b2b;
            background-color: #fff;
            line-height: 1.4;
        }
        * {
            box-sizing: border-box;
        }
        .container {
            width: 100%;
        }
        .header__section {
            position: relative;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a237e;
        }
        h1 {
            text-transform: uppercase;
            font-size: 18px;
            text-align: center;
            margin-top: 15px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            color: #1a237e;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            color: #444;
            margin-bottom: 5px;
        }
        .hospital-info {
            font-size: 12px;
            line-height: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 9px 8px;
            font-size: 11px;
            vertical-align: middle;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.3px;
        }
        tr:nth-child(even) td {
            background-color: #fafbfc;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .summary-box {
            margin-top: 20px;
            border: 1.5px solid #1a237e;
            background-color: #f8faff;
            padding: 14px 18px;
            text-align: right;
            font-size: 13px;
            border-radius: 4px;
        }
        .summary-box strong {
            font-size: 15px;
            color: #c62828;
            margin-left: 10px;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            font-size: 12px;
        }
        .signature-space {
            height: 60px;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header__section">
            <div class="hospital-info">
                <strong>{{ $hospital->label ?? 'Hôpital' }}</strong><br>
                Service Comptabilité & Finances<br>
                Date d'édition : {{ date('d/m/Y à H:i') }}
            </div>
            <h1>État des Dépenses & Charges</h1>
            <p class="subtitle">
                Période : Du <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 75px;">Date</th>
                    <th style="width: 70px;">N° Compte</th>
                    <th>Motif / Libellé</th>
                    <th>Bénéficiaire</th>
                    <th style="width: 85px;">Mode</th>
                    <th style="width: 95px;">Enregistré par</th>
                    <th class="text-end" style="width: 95px;">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $exp->account_number }}</strong></td>
                        <td>{{ $exp->label }}</td>
                        <td>{{ $exp->beneficiaire ?? '-' }}</td>
                        <td class="text-center">{{ ucfirst(str_replace('_', ' ', $exp->mode_paiement)) }}</td>
                        <td>{{ $exp->accountant->user->name ?? 'Comptable' }} {{ $exp->accountant->user->prenom ?? '' }}</td>
                        <td class="text-end fw-bold">{{ number_format($exp->amount, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 20px;">Aucune dépense enregistrée sur cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary-box">
            Total des Dépenses de la période : <strong>{{ number_format($totalExpenses, 0, ',', ' ') }} FCFA</strong>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Le Comptable</p>
                <div class="signature-space"></div>
                <p><strong>{{ $comptable->user->name ?? '' }} {{ $comptable->user->prenom ?? '' }}</strong></p>
            </div>
        </div>

        <div class="footer">
            Document généré automatiquement par le logiciel de gestion hospitalière - Tous droits réservés.
        </div>
    </div>
</body>
</html>
