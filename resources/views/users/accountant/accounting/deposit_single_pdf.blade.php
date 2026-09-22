<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bordereau de Versement Bancaire</title>
    <style>
        @page {
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #fff;
            line-height: 1.4;
            font-size: 11px;
        }
        * {
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            border: 1.5px solid #0d6efd;
            border-radius: 6px;
            padding: 15px;
        }
        .header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            color: #0d6efd;
            text-transform: uppercase;
            margin: 8px 0;
            letter-spacing: 0.5px;
        }
        .ref-badge {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-grid td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .info-grid .label {
            background-color: #f8fafc;
            font-weight: bold;
            color: #475569;
            width: 30%;
        }
        .amount-box {
            background-color: #f0fdf4;
            border: 2px dashed #16a34a;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            margin: 15px 0;
        }
        .amount-box .amount {
            font-size: 20px;
            font-weight: bold;
            color: #15803d;
        }
        .signatures {
            margin-top: 25px;
            width: 100%;
        }
        .sign-col {
            width: 48%;
            float: left;
            text-align: center;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px;
            min-height: 90px;
        }
        .sign-col-right {
            width: 48%;
            float: right;
            text-align: center;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px;
            min-height: 90px;
        }
        .sign-title {
            font-weight: bold;
            color: #334155;
            font-size: 10.5px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 35px;
        }
        .footer {
            margin-top: 15px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            clear: both;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 60%; border: none; padding: 0;">
                        <strong style="font-size: 13px; color: #0d6efd;">{{ $hospital->name ?? 'ÉTABLISSEMENT DE SANTÉ' }}</strong><br>
                        <span>{{ $hospital->adresse ?? '' }}</span><br>
                        <span>Tél : {{ $hospital->telephone ?? '' }}</span>
                    </td>
                    <td style="width: 40%; border: none; text-align: right; padding: 0;">
                        <span class="ref-badge">N° Pièce : {{ $deposit->reference_piece ?: ('DEP-' . str_pad($deposit->id, 6, '0', STR_PAD_LEFT)) }}</span><br>
                        <small style="color: #64748b;">Date saisie : {{ \Carbon\Carbon::parse($deposit->created_at)->format('d/m/Y à H:i') }}</small>
                    </td>
                </tr>
            </table>
            <div class="title">BORDEREAU DE VERSEMENT / DÉPÔT BANCAIRE</div>
        </div>

        <!-- Détails de l'opération -->
        <table class="info-grid">
            <tr>
                <td class="label">Date de valeur / Dépôt :</td>
                <td><strong>{{ \Carbon\Carbon::parse($deposit->deposit_date)->format('d/m/Y') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Banque de Destination :</td>
                <td>
                    <strong>[{{ $deposit->bank_account_number }}]</strong> {{ $deposit->bank_name ?: 'Compte Bancaire Principal' }}
                </td>
            </tr>
            <tr>
                <td class="label">Provenance des Fonds :</td>
                <td>
                    @php
                        $sourceLabel = match($deposit->source_type) {
                            'caisse' => '💵 Caisse Principale Espèces',
                            'mobile_money' => '📱 Mobile Money (Wave, Orange, MTN)',
                            'cheque' => '📝 Remise de Chèque(s)',
                            'assurance' => '🏢 Règlement Tiers Assurances',
                            'apport_associe' => '👤 Apport Associé / Promoteur',
                            'subvention_don' => '🎁 Subvention / Don',
                            default => '⚙️ Autre Source'
                        };
                    @endphp
                    <strong>{{ $sourceLabel }}</strong> (Compte débité/crédité : {{ $deposit->source_account_number }})
                </td>
            </tr>
            <tr>
                <td class="label">Mode de Versement :</td>
                <td><strong>{{ strtoupper($deposit->mode_depot) }}</strong></td>
            </tr>
            <tr>
                <td class="label">Motif / Libellé :</td>
                <td><strong>{{ $deposit->label }}</strong></td>
            </tr>
            @if($deposit->depositor_name)
            <tr>
                <td class="label">Déposant / Émetteur :</td>
                <td>{{ $deposit->depositor_name }}</td>
            </tr>
            @endif
            @if($deposit->description)
            <tr>
                <td class="label">Observations / Détails :</td>
                <td>{{ $deposit->description }}</td>
            </tr>
            @endif
        </table>

        <!-- Montant -->
        <div class="amount-box">
            <span style="font-size: 11px; color: #166534; text-transform: uppercase; font-weight: bold;">Montant Déposé en Banque</span><br>
            <span class="amount">{{ number_format($deposit->amount, 0, ',', ' ') }} FCFA</span>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sign-col">
                <div class="sign-title">Le Déposant / Coursier</div>
                <span>{{ $deposit->depositor_name ?: 'Nom & Signature' }}</span>
            </div>
            <div class="sign-col-right">
                <div class="sign-title">Le Chef Comptable / Caisse</div>
                <span>{{ $comptable->name ?? 'Visa & Signature' }}</span>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer">
            Bordereau interne de versement édité par le progiciel hospitalier GEMMA. Pièce justificative pour le rapprochement bancaire.
        </div>
    </div>
</body>
</html>
