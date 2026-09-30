<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Registre National des Décès - Ministère de la Santé</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #2c3e50;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #842029;
            padding-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-title {
            text-align: center;
        }
        .header-title h2 {
            margin: 0;
            font-size: 15px;
            color: #842029;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h3 {
            margin: 3px 0 0 0;
            font-size: 12px;
            color: #dc3545;
            font-weight: normal;
        }
        .meta-box {
            background-color: #fdf7f7;
            border: 1px solid #f5c2c7;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 9.5px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .data-table th {
            background-color: #842029;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
            padding: 6px 4px;
            border: 1px solid #58151c;
            text-align: center;
        }
        .data-table td {
            padding: 5px 4px;
            border: 1px solid #dee2e6;
            font-size: 8.5px;
            text-align: center;
        }
        .data-table tr:nth-child(even) {
            background-color: #fdfdfd;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-maternal {
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
        }
        .badge-code {
            background-color: #f8f9fa;
            color: #212529;
            border: 1px solid #ced4da;
            font-family: monospace;
        }
        .footer-table {
            width: 100%;
            margin-top: 15px;
            border-top: 1px solid #ced4da;
            padding-top: 6px;
            font-size: 8px;
            color: #6c757d;
        }
    </style>
</head>
<body>

    <!-- En-tête officiel -->
    <table class="header-table">
        <tr>
            <td style="width: 25%; text-align: left;">
                <div style="font-weight: bold; font-size: 9px; text-transform: uppercase;">RÉPUBLIQUE DE CÔTE D'IVOIRE</div>
                <div style="font-size: 8px; color: #555;">Union - Discipline - Travail</div>
                <div style="font-size: 8px; color: #842029; font-weight: bold; margin-top: 2px;">MINISTÈRE DE LA SANTÉ</div>
            </td>
            <td style="width: 50%;" class="header-title">
                <h2>Registre National des Déclarations de Décès</h2>
                <h3>Surveillance Épidémiologique & Mortalité Hospitalière</h3>
            </td>
            <td style="width: 25%; text-align: right;">
                <div style="font-size: 8.5px;"><strong>Date d'export :</strong> {{ $dateExport }}</div>
                <div style="font-size: 8.5px; color: #842029;"><strong>Total lignes :</strong> {{ $totalCount }}</div>
            </td>
        </tr>
    </table>

    <!-- Filtres Appliqués -->
    <div class="meta-box">
        <strong>Critères appliqués :</strong>
        <span>Établissement : {{ $hospitalSelected ? ($hospitalSelected->label ?: $hospitalSelected->nom_direction_generale) : 'Tous les établissements' }}</span> |
        <span>Mortalité maternelle : {{ $request->deces_maternel == '1' ? 'Maternels uniquement' : ($request->deces_maternel === '0' ? 'Non maternels' : 'Tous') }}</span> |
        <span>Année : {{ $request->year ?: 'Toutes' }}</span>
        @if($request->filled('search'))
            | <span>Recherche : "{{ $request->search }}"</span>
        @endif
    </div>

    <!-- Tableau de données -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 85px;">N° Déclaration</th>
                <th style="width: 120px;">Défunt (Nom & Prénoms)</th>
                <th style="width: 50px;">Âge</th>
                <th style="width: 55px;">Genre</th>
                <th style="width: 70px;">Type</th>
                <th style="width: 80px;">Date Décès</th>
                <th style="width: 140px;">Causes du Décès (Initiale / Directe)</th>
                <th style="width: 130px;">Établissement Déclarant</th>
                <th style="width: 100px;">Médecin Déclarant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($declarations as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        <span class="badge badge-code">{{ $item->numero_declaration ?: ($item->reference ?: '#'.$item->id) }}</span>
                    </td>
                    <td style="text-align: left; font-weight: bold;">
                        @if($item->person == 'enfant')
                            Nouveau-né
                        @else
                            {{ $item->declaration->patient->user->name ?? 'Patient' }} {{ $item->declaration->patient->user->prenom ?? '' }}
                        @endif
                    </td>
                    <td>
                        {{ $item->age ? $item->age.' ans' : ($item->person == 'enfant' ? '< 1 an' : '-') }}
                    </td>
                    <td>
                        @if(in_array(strtolower($item->genre), ['m', 'masculin']))
                            Masculin
                        @elseif(in_array(strtolower($item->genre), ['f', 'feminin', 'féminin']))
                            Féminin
                        @else
                            {{ $item->genre ?: '-' }}
                        @endif
                    </td>
                    <td>
                        @if($item->deces_maternel)
                            <span class="badge badge-maternal">Maternel</span>
                        @else
                            Standard
                        @endif
                    </td>
                    <td>
                        {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d/m/Y') : '-' }}
                        {{ $item->heure ? ' à '.$item->heure : '' }}
                    </td>
                    <td style="text-align: left;">
                        <div><strong>Initiale :</strong> {{ $item->cause_initiale ?: 'Non précisée' }}</div>
                        @if($item->cause_directe)
                            <div><strong>Directe :</strong> {{ $item->cause_directe }}</div>
                        @endif
                    </td>
                    <td style="text-align: left;">
                        {{ $item->declaration->hospital->label ?? ($item->declaration->hospital->nom_direction_generale ?? 'Hôpital') }}
                    </td>
                    <td>
                        {{ $item->declaration->doctor->user->name ?? '' }} {{ $item->declaration->doctor->user->prenom ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="padding: 20px; color: #777;">Aucune déclaration de décès trouvée pour les critères spécifiés.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pied de page -->
    <table class="footer-table">
        <tr>
            <td style="text-align: left;">
                Plateforme Nationale Gemma Santé - Direction de la Statistique et de l'Information Sanitaire
            </td>
            <td style="text-align: right;">
                Document certifié généré le {{ date('d/m/Y à H:i:s') }}
            </td>
        </tr>
    </table>

</body>
</html>
