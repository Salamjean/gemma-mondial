<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>État des Dépôts & Versements Bancaires</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
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
            padding-bottom: 12px;
            border-bottom: 2px solid #0d6efd;
        }
        h1 {
            text-transform: uppercase;
            font-size: 17px;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
            color: #0d6efd;
        }
        .subtitle {
            text-align: center;
            font-size: 11px;
            color: #444;
            margin-bottom: 5px;
        }
        .hospital-info {
            font-size: 11px;
            line-height: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            font-size: 10px;
            vertical-align: middle;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 9.5px;
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
            margin-top: 15px;
            border: 1.5px solid #0d6efd;
            background-color: #f0f7ff;
            padding: 12px 16px;
            text-align: right;
            font-size: 12px;
            border-radius: 4px;
        }
        .summary-box strong {
            font-size: 14px;
            color: #198754;
            margin-left: 8px;
        }
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
            font-size: 11px;
        }
        .signature-space {
            height: 60px;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 9px;
            border-radius: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .footer-note {
            margin-top: 20px;
            font-size: 9px;
            color: #64748b;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête de l'hôpital -->
        <div class="header__section">
            <table style="border: none; margin: 0; width: 100%;">
                <tr style="background: transparent;">
                    <td style="border: none; width: 60%; padding: 0;" class="hospital-info">
                        <strong style="font-size: 14px; color: #0d6efd;">{{ $hospital->name ?? 'ÉTABLISSEMENT DE SANTÉ' }}</strong><br>
                        <span>{{ $hospital->adresse ?? '' }}</span><br>
                        <span>Tél : {{ $hospital->telephone ?? '' }}</span>
                    </td>
                    <td style="border: none; width: 40%; text-align: right; padding: 0; font-size: 11px;">
                        <strong>Date d'édition :</strong> {{ date('d/m/Y à H:i') }}<br>
                        <strong>Édité par :</strong> {{ $comptable->name ?? 'Le Comptable' }}
                    </td>
                </tr>
            </table>

            <h1>ÉTAT RÉCAPITULATIF DES DÉPÔTS & VERSEMENTS BANCAIRES</h1>
            <div class="subtitle">
                Période du <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> au <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong>
            </div>
        </div>

        <!-- Tableau des versements -->
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">Date</th>
                    <th style="width: 85px;">Réf / Bordereau</th>
                    <th>Banque Réceptrice</th>
                    <th>Source & Mode</th>
                    <th>Motif & Déposant</th>
                    <th style="width: 105px;" class="text-end">Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $dep)
                @php
                    $sourceLabel = match($dep->source_type) {
                        'caisse' => 'Caisse Espèces',
                        'mobile_money' => 'Mobile Money',
                        'cheque' => 'Remise Chèque',
                        'assurance' => 'Assurance/Tiers',
                        'apport_associe' => 'Apport Associé',
                        'subvention_don' => 'Subvention/Don',
                        default => 'Autre'
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($dep->deposit_date)->format('d/m/Y') }}</td>
                    <td class="text-center fw-bold">{{ $dep->reference_piece ?: ('DEP-' . str_pad($dep->id, 5, '0', STR_PAD_LEFT)) }}</td>
                    <td>
                        <strong>[{{ $dep->bank_account_number }}]</strong> {{ $dep->bank_name ?: 'Banque' }}
                    </td>
                    <td>
                        {{ $sourceLabel }} ({{ $dep->source_account_number }})<br>
                        <span style="font-size: 8.5px; color: #64748b;">Mode: {{ ucfirst($dep->mode_depot) }}</span>
                    </td>
                    <td>
                        <strong>{{ $dep->label }}</strong>
                        @if($dep->depositor_name)
                            <br><span style="font-size: 8.5px; color: #475569;">Déposant: {{ $dep->depositor_name }}</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold" style="color: #198754;">
                        + {{ number_format($dep->amount, 0, ',', ' ') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">Aucun dépôt bancaire enregistré sur cette période.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="5" class="text-end" style="font-size: 11px;">TOTAL DES DÉPÔTS BANCAIRES SUR LA PÉRIODE :</td>
                    <td class="text-end" style="font-size: 12px; color: #198754;">{{ number_format($totalDeposits, 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        </table>

        <!-- Synthèse globale -->
        <div class="summary-box">
            <span>Nombre total d'opérations : <strong>{{ $deposits->count() }}</strong></span> &nbsp;&nbsp;|&nbsp;&nbsp;
            <span>Montant Global Versé en Banque : <strong>{{ number_format($totalDeposits, 0, ',', ' ') }} FCFA</strong></span>
        </div>

        <!-- Zone Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <strong>Visa & Signature du Comptable</strong>
                <div class="signature-space"></div>
                <span>{{ $comptable->name ?? 'Le Comptable' }}</span>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer-note">
            Document généré automatiquement par le Système de Gestion Hospitalière & Comptable GEMMA. Écritures conformes au plan SYSCOHADA.
        </div>
    </div>
</body>
</html>
