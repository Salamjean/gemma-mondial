<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Journal des Écritures Comptables</title>
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
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .border-top-bold {
            border-top: 2px solid #64748b;
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
            <h1>Journal des Écritures Comptables</h1>
            <p class="subtitle">
                Journal : <strong>{{ $journalCode == 'ALL' ? 'Tous les journaux' : $journalCode }}</strong> |
                Période : Du <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">Date</th>
                    <th style="width: 45px;">Jnl</th>
                    <th style="width: 85px;">N° Pièce</th>
                    <th style="width: 75px;">N° Compte</th>
                    <th style="width: 70px;">Tiers</th>
                    <th>Libellé de l'écriture</th>
                    <th class="text-end" style="width: 95px;">Débit (FCFA)</th>
                    <th class="text-end" style="width: 95px;">Crédit (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalDebit = 0;
                    $totalCredit = 0;
                @endphp
                @forelse($entries as $entry)
                    @foreach($entry->lines as $index => $line)
                        @php
                            $totalDebit += $line->debit;
                            $totalCredit += $line->credit;
                        @endphp
                        <tr class="{{ $index == 0 ? 'border-top-bold' : '' }}">
                            @if($index == 0)
                                <td rowspan="{{ count($entry->lines) }}" class="text-center" style="background-color: #fff; vertical-align: top;">
                                    {{ \Carbon\Carbon::parse($entry->entry_date)->format('d/m/Y') }}
                                </td>
                                <td rowspan="{{ count($entry->lines) }}" class="text-center" style="background-color: #fff; vertical-align: top;">
                                    <strong>{{ $entry->journal_code }}</strong>
                                </td>
                                <td rowspan="{{ count($entry->lines) }}" style="background-color: #fff; vertical-align: top;">
                                    {{ $entry->piece_number }}
                                </td>
                            @endif
                            <td class="text-center"><strong>{{ $line->account_number }}</strong></td>
                            <td class="text-center">{{ $line->third_party_code ?: '-' }}</td>
                            <td>{{ $line->libelle ?: $entry->libelle }}</td>
                            <td class="text-end fw-bold">{{ $line->debit > 0 ? number_format($line->debit, 0, ',', ' ') : '-' }}</td>
                            <td class="text-end fw-bold">{{ $line->credit > 0 ? number_format($line->credit, 0, ',', ' ') : '-' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 15px;">Aucune écriture comptable sur cette sélection.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="6">TOTAL GÉNÉRAL DU JOURNAL</td>
                    <td class="text-end fw-bold">{{ number_format($totalDebit, 0, ',', ' ') }}</td>
                    <td class="text-end fw-bold">{{ number_format($totalCredit, 0, ',', ' ') }}</td>
                </tr>
            </tfoot>
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
