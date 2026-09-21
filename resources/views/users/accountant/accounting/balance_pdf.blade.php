<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Balance Générale des Comptes</title>
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
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a237e;
        }
        h1 {
            text-transform: uppercase;
            font-size: 18px;
            text-align: center;
            margin-top: 10px;
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
            padding: 7px 6px;
            font-size: 10px;
            vertical-align: middle;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 9px;
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
        .signature-section {
            margin-top: 35px;
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
                Service Comptabilité & Finances - SYSCOHADA<br>
                Date d'édition : {{ date('d/m/Y à H:i') }}
            </div>
            <h1>Balance Générale des Comptes</h1>
            <p class="subtitle">
                Période : Du <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 75px;">N° Compte</th>
                    <th rowspan="2">Intitulé du Compte</th>
                    <th colspan="2" class="text-center" style="background-color: #e2e8f0;">Mouvements Période</th>
                    <th colspan="2" class="text-center" style="background-color: #e0f2fe;">Soldes Finaux</th>
                </tr>
                <tr>
                    <th class="text-end" style="width: 105px;">Débit (FCFA)</th>
                    <th class="text-end" style="width: 105px;">Crédit (FCFA)</th>
                    <th class="text-end" style="width: 105px;">Solde Débiteur</th>
                    <th class="text-end" style="width: 105px;">Solde Créditeur</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balance['accounts'] as $row)
                    <tr>
                        <td class="text-center"><strong>{{ $row['account_number'] }}</strong></td>
                        <td>{{ $row['account_label'] }}</td>
                        <td class="text-end">{{ $row['total_debit'] > 0 ? number_format($row['total_debit'], 0, ',', ' ') : '-' }}</td>
                        <td class="text-end">{{ $row['total_credit'] > 0 ? number_format($row['total_credit'], 0, ',', ' ') : '-' }}</td>
                        <td class="text-end fw-bold">{{ $row['solde_debiteur'] > 0 ? number_format($row['solde_debiteur'], 0, ',', ' ') : '-' }}</td>
                        <td class="text-end fw-bold">{{ $row['solde_crediteur'] > 0 ? number_format($row['solde_crediteur'], 0, ',', ' ') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 15px;">Aucun mouvement comptable sur cette période.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #0f172a; color: #fff; font-weight: bold;">
                    <td colspan="2" style="color: #fff;">TOTAUX GÉNÉRAUX</td>
                    <td class="text-end" style="color: #fff;">{{ number_format($balance['total_debit'], 0, ',', ' ') }}</td>
                    <td class="text-end" style="color: #fff;">{{ number_format($balance['total_credit'], 0, ',', ' ') }}</td>
                    <td class="text-end" style="color: #fff;">{{ number_format($balance['total_solde_debiteur'], 0, ',', ' ') }}</td>
                    <td class="text-end" style="color: #fff;">{{ number_format($balance['total_solde_crediteur'], 0, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="signature-section">
            <div class="signature-box">
                <p>Le Chef Comptable</p>
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
