<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Grand Livre des Comptes</title>
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
        .account-header {
            background-color: #f1f5f9;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            margin-top: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 6px 5px;
            font-size: 10px;
            vertical-align: middle;
        }
        th {
            background-color: #f8fafc;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
            font-size: 9px;
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
                Service Comptabilité & Finances<br>
                Date d'édition : {{ date('d/m/Y à H:i') }}
            </div>
            <h1>Grand Livre Général des Comptes</h1>
            <p class="subtitle">
                Compte : <strong>{{ $accountNumber == 'ALL' ? 'Tous les comptes' : $accountNumber }}</strong> |
                Période : Du <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
            </p>
        </div>

        @forelse($groupedLines as $accNum => $lines)
            @php
                $totDebit = $lines->sum('debit');
                $totCredit = $lines->sum('credit');
                $solde = $totDebit - $totCredit;
                $accLabel = $lines->first()->account_label ?? '';
            @endphp
            <div class="account-header">
                Compte : {{ $accNum }} - {{ $accLabel }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">Date</th>
                        <th style="width: 45px;">Jnl</th>
                        <th style="width: 90px;">N° Pièce</th>
                        <th>Libellé de l'opération</th>
                        <th style="width: 80px;">Tiers</th>
                        <th class="text-end" style="width: 95px;">Débit (FCFA)</th>
                        <th class="text-end" style="width: 95px;">Crédit (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lines as $l)
                        <tr>
                            <td class="text-center">{{ \Carbon\Carbon::parse($l->entry_date)->format('d/m/Y') }}</td>
                            <td class="text-center">{{ $l->journal_code }}</td>
                            <td>{{ $l->piece_number }}</td>
                            <td>{{ $l->libelle }}</td>
                            <td class="text-center">{{ $l->third_party_code ?: '-' }}</td>
                            <td class="text-end fw-bold">{{ $l->debit > 0 ? number_format($l->debit, 0, ',', ' ') : '-' }}</td>
                            <td class="text-end fw-bold">{{ $l->credit > 0 ? number_format($l->credit, 0, ',', ' ') : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f1f5f9; font-weight: bold;">
                        <td colspan="5">
                            TOTAUX & SOLDE COMPTE {{ $accNum }} 
                            <span style="color: {{ $solde >= 0 ? '#0284c7' : '#d97706' }};">
                                (Solde {{ $solde >= 0 ? 'Débiteur' : 'Créditeur' }} : {{ number_format(abs($solde), 0, ',', ' ') }} FCFA)
                            </span>
                        </td>
                        <td class="text-end fw-bold">{{ number_format($totDebit, 0, ',', ' ') }}</td>
                        <td class="text-end fw-bold">{{ number_format($totCredit, 0, ',', ' ') }}</td>
                    </tr>
                </tfoot>
            </table>
        @empty
            <div style="text-align: center; padding: 20px; color: #64748b;">
                Aucun mouvement trouvé pour cette sélection.
            </div>
        @endforelse

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
