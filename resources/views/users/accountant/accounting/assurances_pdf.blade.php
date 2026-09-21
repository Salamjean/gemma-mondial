<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des Assurances & Recouvrement</title>
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
        h2 {
            font-size: 13px;
            text-transform: uppercase;
            color: #1a237e;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 7px;
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
        .text-success {
            color: #2e7d32;
        }
        .text-danger {
            color: #c62828;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .summary-cards td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: center;
            background-color: #f8faff;
        }
        .summary-cards .amount {
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
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
                Service Comptabilité & Finances<br>
                Date d'édition : {{ date('d/m/Y à H:i') }}
            </div>
            <h1>État des Assurances & Recouvrement</h1>
            <p class="subtitle">Situation globale des créances et règlements encaissés</p>
        </div>

        <!-- Synthèse globale -->
        <table class="summary-cards">
            <tr>
                <td>
                    <span style="font-size: 10px; color: #555; text-transform: uppercase;">Total Prises en Charge</span>
                    <div class="amount" style="color: #1a237e;">{{ number_format($grandTotalPriseEnCharge, 0, ',', ' ') }} FCFA</div>
                </td>
                <td>
                    <span style="font-size: 10px; color: #555; text-transform: uppercase;">Total Déjà Recouvré</span>
                    <div class="amount text-success">{{ number_format($grandTotalRegle, 0, ',', ' ') }} FCFA</div>
                </td>
                <td>
                    <span style="font-size: 10px; color: #555; text-transform: uppercase;">Reste à Recouvrer</span>
                    <div class="amount text-danger">{{ number_format($grandTotalReste, 0, ',', ' ') }} FCFA</div>
                </td>
            </tr>
        </table>

        <!-- Tableau Synthétique par Assureur -->
        <h2>1. État des Créances par Compagnie d'Assurance / Mutuelle</h2>
        <table>
            <thead>
                <tr>
                    <th>Compagnie d'Assurance</th>
                    <th class="text-end" style="width: 140px;">Total Prise en Charge</th>
                    <th class="text-end" style="width: 140px;">Total Recouvré</th>
                    <th class="text-end" style="width: 140px;">Reste à Payer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assurancesSummary as $row)
                    <tr>
                        <td><strong>{{ $row['nom'] }}</strong></td>
                        <td class="text-end fw-bold">{{ number_format($row['total_prise_en_charge'], 0, ',', ' ') }} FCFA</td>
                        <td class="text-end fw-bold text-success">{{ number_format($row['total_regle'], 0, ',', ' ') }} FCFA</td>
                        <td class="text-end fw-bold {{ $row['reste_a_payer'] > 0 ? 'text-danger' : '' }}">{{ number_format($row['reste_a_payer'], 0, ',', ' ') }} FCFA</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 15px;">Aucune donnée d'assurance disponible.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td>TOTAL GÉNÉRAL</td>
                    <td class="text-end fw-bold">{{ number_format($grandTotalPriseEnCharge, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end fw-bold text-success">{{ number_format($grandTotalRegle, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end fw-bold text-danger">{{ number_format($grandTotalReste, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        </table>

        <!-- Historique des Règlements Encaissés -->
        <h2>2. Historique des Règlements & Recouvrements Reçus</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 75px;">Date</th>
                    <th>Assurance</th>
                    <th style="width: 100px;">Mode Règlement</th>
                    <th style="width: 110px;">Réf. Pièce / Chèque</th>
                    <th>Encaissé par</th>
                    <th class="text-end" style="width: 120px;">Montant Reçu (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settlements as $st)
                    <tr>
                        <td class="text-center">{{ \Carbon\Carbon::parse($st->settlement_date)->format('d/m/Y') }}</td>
                        <td><strong>{{ $st->typeAssurance->libelle ?? $st->typeAssurance->nom ?? 'Assurance' }}</strong></td>
                        <td class="text-center">{{ ucfirst(str_replace('_', ' ', $st->mode_paiement)) }}</td>
                        <td class="text-center">{{ $st->reference_piece ?? '-' }}</td>
                        <td>{{ $st->accountant->user->name ?? 'Comptable' }} {{ $st->accountant->user->prenom ?? '' }}</td>
                        <td class="text-end fw-bold text-success">{{ number_format($st->amount, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 15px;">Aucun règlement enregistré pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

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
