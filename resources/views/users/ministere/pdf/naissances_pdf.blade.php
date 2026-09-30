<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Registre National des Naissances - Ministère de la Santé</title>
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
            border-bottom: 2px solid #0d5c3a;
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
            color: #0d5c3a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-title h3 {
            margin: 3px 0 0 0;
            font-size: 12px;
            color: #198754;
            font-weight: normal;
        }
        .meta-box {
            background-color: #f8faf9;
            border: 1px solid #d1e7dd;
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
            background-color: #0d5c3a;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
            padding: 6px 4px;
            border: 1px solid #0a462c;
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
        .data-table tr:hover {
            background-color: #f1f8f4;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-male {
            background-color: #e3f2fd;
            color: #0d6efd;
            border: 1px solid #b6d4fe;
        }
        .badge-female {
            background-color: #fce4ec;
            color: #d63384;
            border: 1px solid #f8bbd0;
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
                <div style="font-size: 8px; color: #0d5c3a; font-weight: bold; margin-top: 2px;">MINISTÈRE DE LA SANTÉ</div>
            </td>
            <td style="width: 50%;" class="header-title">
                <h2>Registre National des Déclarations de Naissance</h2>
                <h3>Extraction Officielle des Données Sanitaires</h3>
            </td>
            <td style="width: 25%; text-align: right;">
                <div style="font-size: 8.5px;"><strong>Date d'export :</strong> {{ $dateExport }}</div>
                <div style="font-size: 8.5px; color: #0d5c3a;"><strong>Total lignes :</strong> {{ $totalCount }}</div>
            </td>
        </tr>
    </table>

    <!-- Filtres Appliqués -->
    <div class="meta-box">
        <strong>Critères appliqués :</strong>
        <span>Établissement : {{ $hospitalSelected ? ($hospitalSelected->label ?: $hospitalSelected->nom_direction_generale) : 'Tous les établissements' }}</span> |
        <span>Genre : {{ $request->genre == 'M' ? 'Masculin' : ($request->genre == 'F' ? 'Féminin' : 'Tous') }}</span> |
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
                <th style="width: 90px;">N° Déclaration</th>
                <th style="width: 130px;">Enfant (Nom & Prénoms)</th>
                <th style="width: 60px;">Genre</th>
                <th style="width: 90px;">Date & Heure Naiss.</th>
                <th style="width: 100px;">Lieu de naissance</th>
                <th style="width: 140px;">Établissement Déclarant</th>
                <th style="width: 110px;">Médecin Déclarant</th>
                <th style="width: 75px;">Date Saisie</th>
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
                        {{ $item->enfant->user->name ?? 'Nouveau-né' }} {{ $item->enfant->user->prenom ?? '' }}
                    </td>
                    <td>
                        @if(in_array(strtolower($item->genre), ['m', 'masculin']))
                            <span class="badge badge-male">Garçon</span>
                        @elseif(in_array(strtolower($item->genre), ['f', 'feminin', 'féminin']))
                            <span class="badge badge-female">Fille</span>
                        @else
                            {{ $item->genre ?: '-' }}
                        @endif
                    </td>
                    <td>
                        {{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d/m/Y') : '-' }}
                        {{ $item->heure ? ' à '.$item->heure : '' }}
                    </td>
                    <td>{{ $item->lieu ?: 'Centre hospitalier' }}</td>
                    <td style="text-align: left;">
                        {{ $item->declaration->hospital->label ?? ($item->declaration->hospital->nom_direction_generale ?? 'Hôpital') }}
                    </td>
                    <td>
                        {{ $item->declaration->doctor->user->name ?? '' }} {{ $item->declaration->doctor->user->prenom ?? '-' }}
                    </td>
                    <td>
                        {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding: 20px; color: #777;">Aucune déclaration de naissance trouvée pour les critères spécifiés.</td>
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
