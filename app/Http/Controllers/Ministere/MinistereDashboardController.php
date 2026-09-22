<?php

namespace App\Http\Controllers\Ministere;

use App\Http\Controllers\Controller;
use App\Models\Declaration;
use App\Models\DeclarationDeces;
use App\Models\DeclarationNaissance;
use App\Models\Hospital;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MinistereDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $hospitalId = $request->input('hospital_id', null);

        // Liste des années disponibles pour le filtre
        $years = Declaration::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        if (!in_array(Carbon::now()->year, $years)) {
            array_unshift($years, Carbon::now()->year);
        }

        // Liste des hôpitaux pour filtre
        $hospitals = Hospital::where('status', 0)->where('delete', 0)->orderBy('label')->get();

        // Requête de base pour les naissances de l'année
        $birthQuery = DeclarationNaissance::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        });

        // Requête de base pour les décès de l'année
        $deathQuery = DeclarationDeces::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        });

        // KPI Globaux
        $totalBirthsYear = (clone $birthQuery)->count();
        $totalDeathsYear = (clone $deathQuery)->count();
        $totalBirthsAll = DeclarationNaissance::count();
        $totalDeathsAll = DeclarationDeces::count();
        $maternalDeathsYear = (clone $deathQuery)->where('deces_maternel', 1)->count();
        $activeHospitalsCount = Hospital::where('status', 0)->where('delete', 0)->count();

        // 1. Évolution Mensuelle (Janvier à Décembre pour l'année sélectionnée)
        $monthsLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        $birthsMonthly = array_fill(1, 12, 0);
        $deathsMonthly = array_fill(1, 12, 0);

        // Naissances par mois
        $birthsByMonthData = DeclarationNaissance::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })
        ->select(DB::raw('MONTH(COALESCE(date, created_at)) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();

        foreach ($birthsByMonthData as $m => $count) {
            if ($m >= 1 && $m <= 12) {
                $birthsMonthly[$m] = (int)$count;
            }
        }

        // Décès par mois
        $deathsByMonthData = DeclarationDeces::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })
        ->select(DB::raw('MONTH(COALESCE(date, created_at)) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();

        foreach ($deathsByMonthData as $m => $count) {
            if ($m >= 1 && $m <= 12) {
                $deathsMonthly[$m] = (int)$count;
            }
        }

        // 2. Répartition par Genre des Naissances
        $birthsMale = (clone $birthQuery)->where(function ($q) {
            $q->where('genre', 'M')->orWhere('genre', 'Masculin')->orWhere('genre', 'masculin');
        })->count();

        $birthsFemale = (clone $birthQuery)->where(function ($q) {
            $q->where('genre', 'F')->orWhere('genre', 'Feminin')->orWhere('genre', 'feminin')->orWhere('genre', 'Féminin');
        })->count();

        $birthsOther = max(0, $totalBirthsYear - ($birthsMale + $birthsFemale));

        // 3. Répartition par Genre des Décès
        $deathsMale = (clone $deathQuery)->where(function ($q) {
            $q->where('genre', 'M')->orWhere('genre', 'Masculin')->orWhere('genre', 'masculin');
        })->count();

        $deathsFemale = (clone $deathQuery)->where(function ($q) {
            $q->where('genre', 'F')->orWhere('genre', 'Feminin')->orWhere('genre', 'feminin')->orWhere('genre', 'Féminin');
        })->count();

        // 4. Répartition des Décès par Tranches d'Âge
        $ageCategories = [
            'Nouveau-nés (< 1 an)' => 0,
            'Enfants (1-5 ans)' => 0,
            'Jeunes (6-17 ans)' => 0,
            'Adultes (18-59 ans)' => 0,
            'Séniors (60+ ans)' => 0,
            'Âge non précisé' => 0
        ];

        $allDeaths = (clone $deathQuery)->get(['age', 'person']);
        foreach ($allDeaths as $d) {
            if ($d->person == 'enfant') {
                $ageCategories['Nouveau-nés (< 1 an)']++;
            } else {
                $ageNum = is_numeric($d->age) ? (int)$d->age : null;
                if ($ageNum === null) {
                    $ageCategories['Âge non précisé']++;
                } elseif ($ageNum < 1) {
                    $ageCategories['Nouveau-nés (< 1 an)']++;
                } elseif ($ageNum <= 5) {
                    $ageCategories['Enfants (1-5 ans)']++;
                } elseif ($ageNum <= 17) {
                    $ageCategories['Jeunes (6-17 ans)']++;
                } elseif ($ageNum <= 59) {
                    $ageCategories['Adultes (18-59 ans)']++;
                } else {
                    $ageCategories['Séniors (60+ ans)']++;
                }
            }
        }

        // 5. Top 8 Hôpitaux déclarants (Naissances & Décès)
        $topHospitalsBirths = Declaration::where('type', 'birth')
            ->whereYear('created_at', $year)
            ->whereNotNull('hospital_id')
            ->select('hospital_id', DB::raw('COUNT(*) as total'))
            ->groupBy('hospital_id')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hospital')
            ->get();

        $topHospitalsDeaths = Declaration::where('type', 'death')
            ->whereYear('created_at', $year)
            ->whereNotNull('hospital_id')
            ->select('hospital_id', DB::raw('COUNT(*) as total'))
            ->groupBy('hospital_id')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hospital')
            ->get();

        // 6. Dernières déclarations enregistrées
        $recentBirths = DeclarationNaissance::with(['declaration.hospital', 'enfant.user'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $recentDeaths = DeclarationDeces::with(['declaration.hospital', 'declaration.patient.user'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $title = "Observatoire National - Ministère de la Santé";

        return view('users.ministere.dashboard', compact(
            'title',
            'year',
            'years',
            'hospitalId',
            'hospitals',
            'totalBirthsYear',
            'totalDeathsYear',
            'totalBirthsAll',
            'totalDeathsAll',
            'maternalDeathsYear',
            'activeHospitalsCount',
            'monthsLabels',
            'birthsMonthly',
            'deathsMonthly',
            'birthsMale',
            'birthsFemale',
            'birthsOther',
            'deathsMale',
            'deathsFemale',
            'ageCategories',
            'topHospitalsBirths',
            'topHospitalsDeaths',
            'recentBirths',
            'recentDeaths'
        ));
    }

    public function live(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $hospitalId = $request->input('hospital_id', null);

        $hospitals = Hospital::where('status', 0)->where('delete', 0)->orderBy('label')->get();

        $birthQuery = DeclarationNaissance::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        });

        $deathQuery = DeclarationDeces::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        });

        $totalBirthsYear = (clone $birthQuery)->count();
        $totalDeathsYear = (clone $deathQuery)->count();
        $totalBirthsAll = DeclarationNaissance::count();
        $totalDeathsAll = DeclarationDeces::count();
        $maternalDeathsYear = (clone $deathQuery)->where('deces_maternel', 1)->count();
        $activeHospitalsCount = Hospital::where('status', 0)->where('delete', 0)->count();

        $monthsLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        $birthsMonthly = array_fill(1, 12, 0);
        $deathsMonthly = array_fill(1, 12, 0);

        $birthsByMonthData = DeclarationNaissance::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })
        ->select(DB::raw('MONTH(COALESCE(date, created_at)) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();

        foreach ($birthsByMonthData as $m => $count) {
            if ($m >= 1 && $m <= 12) {
                $birthsMonthly[$m] = (int)$count;
            }
        }

        $deathsByMonthData = DeclarationDeces::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })
        ->select(DB::raw('MONTH(COALESCE(date, created_at)) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();

        foreach ($deathsByMonthData as $m => $count) {
            if ($m >= 1 && $m <= 12) {
                $deathsMonthly[$m] = (int)$count;
            }
        }

        $birthsMale = (clone $birthQuery)->where(function ($q) {
            $q->where('genre', 'M')->orWhere('genre', 'Masculin')->orWhere('genre', 'masculin');
        })->count();

        $birthsFemale = (clone $birthQuery)->where(function ($q) {
            $q->where('genre', 'F')->orWhere('genre', 'Feminin')->orWhere('genre', 'feminin')->orWhere('genre', 'Féminin');
        })->count();

        $birthsOther = max(0, $totalBirthsYear - ($birthsMale + $birthsFemale));

        $deathsMale = (clone $deathQuery)->where(function ($q) {
            $q->where('genre', 'M')->orWhere('genre', 'Masculin')->orWhere('genre', 'masculin');
        })->count();

        $deathsFemale = (clone $deathQuery)->where(function ($q) {
            $q->where('genre', 'F')->orWhere('genre', 'Feminin')->orWhere('genre', 'feminin')->orWhere('genre', 'Féminin');
        })->count();

        $ageCategories = [
            'Nouveau-nés (< 1 an)' => 0,
            'Enfants (1-5 ans)' => 0,
            'Jeunes (6-17 ans)' => 0,
            'Adultes (18-59 ans)' => 0,
            'Séniors (60+ ans)' => 0,
            'Âge non précisé' => 0
        ];

        $allDeaths = (clone $deathQuery)->get(['age', 'person']);
        foreach ($allDeaths as $d) {
            if ($d->person == 'enfant') {
                $ageCategories['Nouveau-nés (< 1 an)']++;
            } else {
                $ageNum = is_numeric($d->age) ? (int)$d->age : null;
                if ($ageNum === null) {
                    $ageCategories['Âge non précisé']++;
                } elseif ($ageNum < 1) {
                    $ageCategories['Nouveau-nés (< 1 an)']++;
                } elseif ($ageNum <= 5) {
                    $ageCategories['Enfants (1-5 ans)']++;
                } elseif ($ageNum <= 17) {
                    $ageCategories['Jeunes (6-17 ans)']++;
                } elseif ($ageNum <= 59) {
                    $ageCategories['Adultes (18-59 ans)']++;
                } else {
                    $ageCategories['Séniors (60+ ans)']++;
                }
            }
        }

        $topHospitalsBirths = Declaration::where('type', 'birth')
            ->whereYear('created_at', $year)
            ->whereNotNull('hospital_id')
            ->select('hospital_id', DB::raw('COUNT(*) as total'))
            ->groupBy('hospital_id')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hospital')
            ->get();

        $topHospitalsDeaths = Declaration::where('type', 'death')
            ->whereYear('created_at', $year)
            ->whereNotNull('hospital_id')
            ->select('hospital_id', DB::raw('COUNT(*) as total'))
            ->groupBy('hospital_id')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hospital')
            ->get();

        $recentBirths = DeclarationNaissance::with(['declaration.hospital', 'enfant.user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentDeaths = DeclarationDeces::with(['declaration.hospital', 'declaration.patient.user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $title = "OBSERVATOIRE NATIONAL DE LA SANTÉ - PROJECTION GRAND ÉCRAN 24/7";

        return view('users.ministere.live', compact(
            'title',
            'year',
            'hospitalId',
            'hospitals',
            'totalBirthsYear',
            'totalDeathsYear',
            'totalBirthsAll',
            'totalDeathsAll',
            'maternalDeathsYear',
            'activeHospitalsCount',
            'monthsLabels',
            'birthsMonthly',
            'deathsMonthly',
            'birthsMale',
            'birthsFemale',
            'birthsOther',
            'deathsMale',
            'deathsFemale',
            'ageCategories',
            'topHospitalsBirths',
            'topHospitalsDeaths',
            'recentBirths',
            'recentDeaths'
        ));
    }

    public function ajaxStats(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $hospitalId = $request->input('hospital_id', null);

        $hospitals = Hospital::where('status', 0)->where('delete', 0)->orderBy('label')->get();

        $birthQuery = DeclarationNaissance::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        });

        $deathQuery = DeclarationDeces::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        });

        $totalBirthsYear = (clone $birthQuery)->count();
        $totalDeathsYear = (clone $deathQuery)->count();
        $totalBirthsAll = DeclarationNaissance::count();
        $totalDeathsAll = DeclarationDeces::count();
        $maternalDeathsYear = (clone $deathQuery)->where('deces_maternel', 1)->count();
        $activeHospitalsCount = Hospital::where('status', 0)->where('delete', 0)->count();

        // Évolution mensuelle
        $birthsMonthly = array_fill(1, 12, 0);
        $deathsMonthly = array_fill(1, 12, 0);

        $birthsByMonthData = DeclarationNaissance::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })
        ->select(DB::raw('MONTH(COALESCE(date, created_at)) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();

        foreach ($birthsByMonthData as $m => $count) {
            if ($m >= 1 && $m <= 12) {
                $birthsMonthly[$m] = (int)$count;
            }
        }

        $deathsByMonthData = DeclarationDeces::whereHas('declaration', function ($q) use ($year, $hospitalId) {
            $q->whereYear('created_at', $year);
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })
        ->select(DB::raw('MONTH(COALESCE(date, created_at)) as month'), DB::raw('COUNT(*) as count'))
        ->groupBy('month')
        ->pluck('count', 'month')
        ->toArray();

        foreach ($deathsByMonthData as $m => $count) {
            if ($m >= 1 && $m <= 12) {
                $deathsMonthly[$m] = (int)$count;
            }
        }

        // Genre naissances
        $birthsMale = (clone $birthQuery)->where(function ($q) {
            $q->where('genre', 'M')->orWhere('genre', 'Masculin')->orWhere('genre', 'masculin');
        })->count();

        $birthsFemale = (clone $birthQuery)->where(function ($q) {
            $q->where('genre', 'F')->orWhere('genre', 'Feminin')->orWhere('genre', 'feminin')->orWhere('genre', 'Féminin');
        })->count();

        $birthsOther = max(0, $totalBirthsYear - ($birthsMale + $birthsFemale));

        // Tranches d'âge décès
        $ageCategories = [
            'Nouveau-nés (< 1 an)' => 0,
            'Enfants (1-5 ans)' => 0,
            'Jeunes (6-17 ans)' => 0,
            'Adultes (18-59 ans)' => 0,
            'Séniors (60+ ans)' => 0,
            'Âge non précisé' => 0
        ];

        $allDeaths = (clone $deathQuery)->get(['age', 'person']);
        foreach ($allDeaths as $d) {
            if ($d->person == 'enfant') {
                $ageCategories['Nouveau-nés (< 1 an)']++;
            } else {
                $ageNum = is_numeric($d->age) ? (int)$d->age : null;
                if ($ageNum === null) {
                    $ageCategories['Âge non précisé']++;
                } elseif ($ageNum < 1) {
                    $ageCategories['Nouveau-nés (< 1 an)']++;
                } elseif ($ageNum <= 5) {
                    $ageCategories['Enfants (1-5 ans)']++;
                } elseif ($ageNum <= 17) {
                    $ageCategories['Jeunes (6-17 ans)']++;
                } elseif ($ageNum <= 59) {
                    $ageCategories['Adultes (18-59 ans)']++;
                } else {
                    $ageCategories['Séniors (60+ ans)']++;
                }
            }
        }

        // Top Hôpitaux
        $topHospitalsBirths = Declaration::where('type', 'birth')
            ->whereYear('created_at', $year)
            ->whereNotNull('hospital_id')
            ->select('hospital_id', DB::raw('COUNT(*) as total'))
            ->groupBy('hospital_id')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hospital')
            ->get();

        $topHospitalsDeaths = Declaration::where('type', 'death')
            ->whereYear('created_at', $year)
            ->whereNotNull('hospital_id')
            ->select('hospital_id', DB::raw('COUNT(*) as total'))
            ->groupBy('hospital_id')
            ->orderByDesc('total')
            ->limit(8)
            ->with('hospital')
            ->get();

        $hospNames = [];
        $hospBirths = [];
        $hospDeaths = [];
        $allTopHospIds = $topHospitalsBirths->pluck('hospital_id')->merge($topHospitalsDeaths->pluck('hospital_id'))->unique()->take(6);
        foreach ($allTopHospIds as $hId) {
            $hObj = $hospitals->firstWhere('id', $hId);
            $hName = $hObj ? ($hObj->label ?: $hObj->nom_direction_generale) : "Hôpital #$hId";
            $hospNames[] = \Illuminate\Support\Str::limit($hName, 18);
            $hospBirths[] = $topHospitalsBirths->firstWhere('hospital_id', $hId)->total ?? 0;
            $hospDeaths[] = $topHospitalsDeaths->firstWhere('hospital_id', $hId)->total ?? 0;
        }

        // Listes récentes
        $recentBirths = DeclarationNaissance::with(['declaration.hospital', 'enfant.user'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function($b) {
                return [
                    'nom' => ($b->enfant->user->name ?? 'Enfant') . ' ' . ($b->enfant->user->prenom ?? ''),
                    'numero' => $b->numero_declaration ?: ($b->reference ?: '#'.$b->id),
                    'hopital' => $b->declaration->hospital->label ?? ($b->declaration->hospital->nom_direction_generale ?? 'Hôpital'),
                    'genre' => $b->genre,
                    'date' => ($b->date ? \Carbon\Carbon::parse($b->date)->format('d/m/Y') : ($b->created_at ? $b->created_at->format('d/m/Y') : '-')) . ($b->heure ? ' à '.$b->heure : '')
                ];
            });

        $recentDeaths = DeclarationDeces::with(['declaration.hospital', 'declaration.patient.user'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->map(function($d) {
                return [
                    'nom' => $d->person == 'enfant' ? 'Nouveau-né' : (($d->declaration->patient->user->name ?? 'Patient') . ' ' . ($d->declaration->patient->user->prenom ?? '')),
                    'numero' => $d->numero_declaration ?: ($d->reference ?: '#'.$d->id),
                    'hopital' => $d->declaration->hospital->label ?? ($d->declaration->hospital->nom_direction_generale ?? 'Hôpital'),
                    'deces_maternel' => (bool)$d->deces_maternel,
                    'cause' => $d->cause_initiale ?: ($d->cause_directe ?: 'Cause non précisée'),
                    'date' => ($d->date ? \Carbon\Carbon::parse($d->date)->format('d/m/Y') : ($d->created_at ? $d->created_at->format('d/m/Y') : '-')) . ($d->heure ? ' à '.$d->heure : '')
                ];
            });

        return response()->json([
            'totalBirthsYear' => number_format($totalBirthsYear, 0, ',', ' '),
            'totalDeathsYear' => number_format($totalDeathsYear, 0, ',', ' '),
            'totalBirthsAll' => number_format($totalBirthsAll, 0, ',', ' '),
            'totalDeathsAll' => number_format($totalDeathsAll, 0, ',', ' '),
            'maternalDeathsYear' => number_format($maternalDeathsYear, 0, ',', ' '),
            'maternalRate' => $totalBirthsYear > 0 ? round(($maternalDeathsYear / $totalBirthsYear) * 1000, 2) : 0,
            'activeHospitalsCount' => $activeHospitalsCount,
            'birthsMonthly' => array_values($birthsMonthly),
            'deathsMonthly' => array_values($deathsMonthly),
            'birthsMale' => $birthsMale,
            'birthsFemale' => $birthsFemale,
            'birthsOther' => $birthsOther,
            'birthsMalePct' => $totalBirthsYear > 0 ? round(($birthsMale / $totalBirthsYear) * 100, 1) : 0,
            'birthsFemalePct' => $totalBirthsYear > 0 ? round(($birthsFemale / $totalBirthsYear) * 100, 1) : 0,
            'ageCategories' => $ageCategories,
            'hospLabels' => $hospNames,
            'hospBirthsData' => $hospBirths,
            'hospDeathsData' => $hospDeaths,
            'recentBirths' => $recentBirths,
            'recentDeaths' => $recentDeaths,
            'updated_at' => Carbon::now()->format('H:i:s')
        ]);
    }

    public function naissances(Request $request)
    {
        $query = DeclarationNaissance::with(['declaration.hospital', 'declaration.doctor.user', 'enfant.user']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('numero_declaration', 'like', "%{$s}%")
                  ->orWhere('reference', 'like', "%{$s}%")
                  ->orWhere('lieu', 'like', "%{$s}%")
                  ->orWhereHas('enfant.user', function ($u) use ($s) {
                      $u->where('name', 'like', "%{$s}%")->orWhere('prenom', 'like', "%{$s}%");
                  })
                  ->orWhereHas('declaration.hospital', function ($h) use ($s) {
                      $h->where('label', 'like', "%{$s}%")->orWhere('nom_direction_generale', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('hospital_id')) {
            $query->whereHas('declaration', function ($q) use ($request) {
                $q->where('hospital_id', $request->hospital_id);
            });
        }

        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        if ($request->filled('year')) {
            $query->whereHas('declaration', function ($q) use ($request) {
                $q->whereYear('created_at', $request->year);
            });
        }

        $declarations = $query->orderByDesc('created_at')->paginate(20);
        $hospitals = Hospital::where('status', 0)->where('delete', 0)->orderBy('label')->get();
        $title = "Registre National des Déclarations de Naissance";

        return view('users.ministere.naissances', compact('declarations', 'hospitals', 'title'));
    }

    public function deces(Request $request)
    {
        $query = DeclarationDeces::with(['declaration.hospital', 'declaration.doctor.user', 'declaration.patient.user']);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('numero_declaration', 'like', "%{$s}%")
                  ->orWhere('reference', 'like', "%{$s}%")
                  ->orWhere('cause_initiale', 'like', "%{$s}%")
                  ->orWhere('cause_directe', 'like', "%{$s}%")
                  ->orWhere('lieu', 'like', "%{$s}%")
                  ->orWhereHas('declaration.patient.user', function ($u) use ($s) {
                      $u->where('name', 'like', "%{$s}%")->orWhere('prenom', 'like', "%{$s}%");
                  })
                  ->orWhereHas('declaration.hospital', function ($h) use ($s) {
                      $h->where('label', 'like', "%{$s}%")->orWhere('nom_direction_generale', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('hospital_id')) {
            $query->whereHas('declaration', function ($q) use ($request) {
                $q->where('hospital_id', $request->hospital_id);
            });
        }

        if ($request->filled('deces_maternel')) {
            $query->where('deces_maternel', $request->deces_maternel);
        }

        if ($request->filled('year')) {
            $query->whereHas('declaration', function ($q) use ($request) {
                $q->whereYear('created_at', $request->year);
            });
        }

        $declarations = $query->orderByDesc('created_at')->paginate(20);
        $hospitals = Hospital::where('status', 0)->where('delete', 0)->orderBy('label')->get();
        $title = "Registre National des Déclarations de Décès";

        return view('users.ministere.deces', compact('declarations', 'hospitals', 'title'));
    }

    public function hospitals()
    {
        $hospitals = Hospital::with(['localiteH.department.region'])
            ->withCount([
                'declarations as total_births' => function ($q) {
                    $q->where('type', 'birth');
                },
                'declarations as total_deaths' => function ($q) {
                    $q->where('type', 'death');
                }
            ])->where('status', 0)->where('delete', 0)->orderByDesc('total_births')->get();

        $title = "Cartographie et Statistiques des Hôpitaux Déclarants";
        return view('users.ministere.hospitals', compact('hospitals', 'title'));
    }
}
