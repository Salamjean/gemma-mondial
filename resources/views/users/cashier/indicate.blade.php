<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Point des recettes du jour</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        body {
            padding: 20px 25px 50px 25px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }

        .hospital-info h3 {
            font-size: 14px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .hospital-info p {
            font-size: 10px;
            color: #555;
            line-height: 1.4;
            margin: 0;
        }

        .doc-title {
            text-align: center;
            margin: 15px 0 15px 0;
            padding: 8px 0;
            border-top: 1px solid #1e3c72;
            border-bottom: 1px solid #1e3c72;
        }

        .doc-title h1 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3c72;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .doc-title p {
            font-size: 11px;
            color: #666;
            margin-top: 3px;
        }

        /* KPI Summary Box */
        .summary-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
        }

        .summary-box td {
            padding: 8px 12px;
            border: none;
            vertical-align: middle;
        }

        .summary-item {
            font-size: 11px;
        }

        .summary-item strong {
            font-size: 12px;
        }

        /* Table transactions */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background-color: #1e3c72;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            padding: 7px 6px;
            text-align: left;
            border: 1px solid #1e3c72;
        }

        .data-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }

        .badge-mode {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }

        .mode-espece {
            color: #065f46;
            background-color: #d1fae5;
        }

        .mode-mobile {
            color: #3730a3;
            background-color: #e0e7ff;
        }

        /* Signatures */
        .signature-section {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            border: none;
            vertical-align: top;
            width: 50%;
        }

        .signature-box {
            text-align: right;
            padding-right: 20px;
        }

        .signature-box .role {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin-bottom: 45px;
        }

        .signature-box .name {
            font-size: 11px;
            font-weight: bold;
            color: #1e3c72;
            text-decoration: underline;
        }

        /* Footer */
        footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 35px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
            line-height: 1.2;
        }
    </style>
</head>
<body>

    <footer>
        Art. 285. Quiconque se rend coupable de fraude ou de fausse déclaration ou se fait délivrer un des documents prévus à l'article précédent est puni de peines prévues par la loi en vigueur. — GEMMA SAnté
    </footer>

    @php
        $especeSum = $payments->filter(fn($p) => $p->mode_paiement === 'espece' || empty($p->mode_paiement))->sum('prix');
        $mobileSum = $payments->filter(fn($p) => $p->mode_paiement === 'mobile_money')->sum('prix');
        $totalSum = $payments->sum('prix');
        $totalCount = $payments->count();
    @endphp

    {{-- En-tête avec informations hôpital --}}
    <table class="header-table">
        <tr>
            <td style="width: 60%;" class="hospital-info">
                @if ($cashier && $cashier->hospital)
                    <h3>{{ $cashier->hospital->label ?? 'Établissement Hospitalier' }}</h3>
                    <p><strong>Adresse :</strong> {{ $cashier->hospital->localiteH->name ?? 'Non spécifiée' }}</p>
                    <p><strong>Téléphone :</strong> {{ $cashier->hospital->contact ?? 'N/A' }}</p>
                @else
                    <h3>Établissement Hospitalier</h3>
                @endif
                <p><strong>Caissier(e) :</strong> {{ $cashier->user->name ?? '' }} {{ $cashier->user->prenom ?? '' }} (Matricule : {{ $cashier->matricule ?? 'N/A' }})</p>
            </td>
            <td style="width: 40%; text-align: right;">
                <p style="font-size: 10px; color: #555;"><strong>Date d'édition :</strong> {{ date('d/m/Y H:i') }}</p>
                <p style="font-size: 10px; color: #555;"><strong>Journée arrêtée :</strong> {{ dateCompletFr($day ?? date('Y-m-d')) }}</p>
            </td>
        </tr>
    </table>

    {{-- Titre --}}
    <div class="doc-title">
        <h1>Point Quotidien des Recettes de Caisse</h1>
        <p>Arrêté des encaissements de la journée du <strong>{{ dateCompletFr($day ?? date('Y-m-d')) }}</strong></p>
    </div>

    {{-- Synthèse des règlements --}}
    <table class="summary-box">
        <tr>
            <td style="width: 33%;">
                <div class="summary-item">
                    <span style="color: #065f46;">💵 Total Espèces :</span><br>
                    <strong style="color: #065f46; font-size: 13px;">{{ number_format($especeSum, 0, ',', ' ') }} FCFA</strong>
                </div>
            </td>
            <td style="width: 33%;">
                <div class="summary-item">
                    <span style="color: #3730a3;">📱 Total Mobile Money :</span><br>
                    <strong style="color: #3730a3; font-size: 13px;">{{ number_format($mobileSum, 0, ',', ' ') }} FCFA</strong>
                </div>
            </td>
            <td style="width: 34%; text-align: right;">
                <div class="summary-item">
                    <span style="color: #1e3c72;">💳 Total Général Collecté :</span><br>
                    <strong style="color: #1e3c72; font-size: 15px;">{{ number_format($totalSum, 0, ',', ' ') }} FCFA</strong>
                    <span style="font-size: 9px; color: #64748b; display: block;">({{ $totalCount }} encaissement{{ $totalCount > 1 ? 's' : '' }})</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Tableau des transactions --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">N°</th>
                <th style="width: 10%;">Heure</th>
                <th style="width: 15%;">N° Patient</th>
                <th style="width: 26%;">Nom & Prénom(s)</th>
                <th style="width: 18%;">Prestation / Acte</th>
                <th style="width: 13%;">Mode</th>
                <th style="width: 13%;" class="text-end">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $index => $item)
                @php
                    $patientCode = '-';
                    $patientName = '-';
                    $prestation = '-';

                    if ($item->type == 'hospitalisation' && $item->hospitalisation) {
                        $patient = $item->hospitalisation->consultation->patient ?? null;
                        if ($patient) {
                            $patientCode = $patient->code_patient ?? '-';
                            $patientName = ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '');
                        }
                        $prestation = $item->hospitalisation->mot_sortie ?? 'Hospitalisation';
                    } elseif ($item->type == 'admission' && $item->admission) {
                        $patient = $item->admission->patient ?? null;
                        if ($patient) {
                            $patientCode = $patient->code_patient ?? '-';
                            $patientName = ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '');
                        }
                        $prestation = $item->admission->prestationHospital->prestationService->libelle ?? 'Admission';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->created_at ? $item->created_at->format('H:i') : '--:--' }}</td>
                    <td><strong>{{ $patientCode }}</strong></td>
                    <td>{{ $patientName }}</td>
                    <td>{{ $prestation }}</td>
                    <td>
                        @if ($item->mode_paiement == 'mobile_money')
                            <span class="badge-mode mode-mobile">
                                {{ $item->operateur_mobile ?? 'Mobile Money' }}
                            </span>
                        @else
                            <span class="badge-mode mode-espece">
                                Espèce
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <strong>{{ number_format($item->prix, 0, ',', ' ') }}</strong> <small>FCFA</small>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Aucun encaissement enregistré pour cette journée.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if ($totalCount > 0)
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="5" style="text-transform: uppercase; font-size: 10px; color: #1e3c72; padding: 8px 6px;">
                        TOTAL DE LA JOURNÉE ({{ $totalCount }} actes)
                    </td>
                    <td style="font-size: 9px; color: #475569;">
                        Esp : {{ number_format($especeSum, 0, ',', ' ') }}<br>
                        Mob : {{ number_format($mobileSum, 0, ',', ' ') }}
                    </td>
                    <td class="text-end" style="color: #1e3c72; font-size: 12px; padding: 8px 6px;">
                        {{ number_format($totalSum, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- Signatures --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td></td>
                <td>
                    <div class="signature-box">
                        <p class="role">La Caissière :</p>
                        <p class="name">{{ $cashier->user->name ?? '' }} {{ $cashier->user->prenom ?? '' }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
