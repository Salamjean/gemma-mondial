@extends('layouts.dashboard')

@section('content')
<div class="content-header mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="me-auto">
            <h3 class="page-title fw-bold text-dark mb-0">
                <i class="fa fa-chart-line text-primary me-2"></i> Observatoire National de la Santé
            </h3>
            <div class="d-flex align-items-center gap-2 mt-1">
                <p class="text-muted mb-0 small">Surveillance épidémiologique et démographique (Naissances & Décès hospitaliers)</p>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-11" id="liveIndicator">
                    <i class="fa fa-circle text-success fs-10 me-1 pulse"></i> En direct (auto 30s)
                </span>
                <small class="text-muted fs-11" id="lastUpdatedText">Dernière mise à jour : {{ now()->format('H:i:s') }}</small>
            </div>
        </div>
        
        <!-- Formulaire de filtrage interactif -->
        <form id="filterForm" method="GET" action="{{ route('ministere.dashboard') }}" class="d-flex align-items-center flex-wrap gap-2">
            <div class="input-group input-group-sm shadow-sm" style="min-width: 140px;">
                <span class="input-group-text bg-white fw-bold"><i class="fa fa-calendar-alt text-primary me-1"></i> Année</span>
                <select id="selectYear" name="year" class="form-select fw-semibold" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-group input-group-sm shadow-sm" style="min-width: 220px;">
                <span class="input-group-text bg-white fw-bold"><i class="fa fa-hospital text-primary me-1"></i> Hôpital</span>
                <select id="selectHospital" name="hospital_id" class="form-select fw-semibold" onchange="this.form.submit()">
                    <option value="">Tous les établissements (National)</option>
                    @foreach($hospitals as $h)
                        <option value="{{ $h->id }}" {{ $hospitalId == $h->id ? 'selected' : '' }}>{{ $h->label ?: $h->nom_direction_generale }}</option>
                    @endforeach
                </select>
            </div>

            @if(request('year') || request('hospital_id'))
                <a href="{{ route('ministere.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm" title="Réinitialiser les filtres">
                    <i class="fa fa-undo"></i>
                </a>
            @endif

            <a href="{{ route('ministere.live') }}" target="_blank" class="btn btn-sm btn-dark shadow-sm fw-bold" title="Ouvrir le mode plein écran pour projecteur ou grand écran TV (24/7)">
                <i class="fa fa-tv text-warning me-1"></i> Mode Projection Écran (24/7)
            </a>
        </form>
    </div>
</div>

<!-- Cartes KPI Statistiques Sobres & Épurées -->
<div class="row">
    <!-- Total Naissances de l'Année -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-white border border-1 border-light-subtle shadow-sm mb-4">
            <div class="box-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-12">Naissances ({{ $year }})</span>
                        <h2 class="fw-bold text-dark mb-1" id="kpiBirthsYear">{{ number_format($totalBirthsYear, 0, ',', ' ') }}</h2>
                        <span class="text-muted small">Total historique : <strong class="text-secondary" id="kpiBirthsAll">{{ number_format($totalBirthsAll, 0, ',', ' ') }}</strong></span>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fa fa-baby fs-22"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Décès de l'Année -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-white border border-1 border-light-subtle shadow-sm mb-4">
            <div class="box-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-12">Décès déclarés ({{ $year }})</span>
                        <h2 class="fw-bold text-dark mb-1" id="kpiDeathsYear">{{ number_format($totalDeathsYear, 0, ',', ' ') }}</h2>
                        <span class="text-muted small">Total historique : <strong class="text-secondary" id="kpiDeathsAll">{{ number_format($totalDeathsAll, 0, ',', ' ') }}</strong></span>
                    </div>
                    <div class="bg-danger-subtle text-danger rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fa fa-cross fs-22"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Décès Maternels -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-white border border-1 border-light-subtle shadow-sm mb-4">
            <div class="box-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-12">Décès Maternels ({{ $year }})</span>
                        <h2 class="fw-bold text-dark mb-1" id="kpiMaternalYear">{{ number_format($maternalDeathsYear, 0, ',', ' ') }}</h2>
                        <span class="text-muted small">Taux : <strong class="text-warning" id="kpiMaternalRate">{{ $totalBirthsYear > 0 ? round(($maternalDeathsYear / $totalBirthsYear) * 1000, 2) : 0 }} ‰</strong></span>
                    </div>
                    <div class="bg-warning-subtle text-warning rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fa fa-female fs-22"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hôpitaux Connectés -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-white border border-1 border-light-subtle shadow-sm mb-4">
            <div class="box-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold fs-12">Établissements Actifs</span>
                        <h2 class="fw-bold text-dark mb-1" id="kpiHospitalsCount">{{ $activeHospitalsCount }}</h2>
                        <span class="text-muted small">Réseau national GEMMA</span>
                    </div>
                    <div class="bg-info-subtle text-info rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fa fa-hospital-alt fs-22"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LIGNE 1 : Évolution Mensuelle Comparée (Naissances vs Décès) -->
<div class="row">
    <div class="col-xl-8 col-12">
        <div class="box shadow-sm mb-4">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title fw-bold text-dark">
                    <i class="fa fa-chart-area text-primary me-2"></i> Évolution Mensuelle : Naissances vs Décès ({{ $year }})
                </h4>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary px-2 py-1"><i class="fa fa-baby me-1"></i> Naissances</span>
                    <span class="badge bg-danger px-2 py-1"><i class="fa fa-cross me-1"></i> Décès</span>
                </div>
            </div>
            <div class="box-body">
                <div style="height: 340px; position: relative;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition par Genre (Naissances) -->
    <div class="col-xl-4 col-12">
        <div class="box shadow-sm mb-4">
            <div class="box-header with-border">
                <h4 class="box-title fw-bold text-dark">
                    <i class="fa fa-venus-mars text-info me-2"></i> Naissances par Genre ({{ $year }})
                </h4>
            </div>
            <div class="box-body">
                <div style="height: 250px; position: relative;">
                    <canvas id="genderBirthChart"></canvas>
                </div>
                <div class="row text-center mt-3 pt-2 border-top">
                    <div class="col-6 border-end">
                        <span class="text-muted fs-12 text-uppercase">Garçons</span>
                        <h4 class="fw-bold text-primary mb-0" id="genderMaleCount">{{ $birthsMale }}</h4>
                        <small class="text-muted" id="genderMalePct">{{ $totalBirthsYear > 0 ? round(($birthsMale / $totalBirthsYear)*100, 1) : 0 }}%</small>
                    </div>
                    <div class="col-6">
                        <span class="text-muted fs-12 text-uppercase">Filles</span>
                        <h4 class="fw-bold text-danger mb-0" id="genderFemaleCount">{{ $birthsFemale }}</h4>
                        <small class="text-muted" id="genderFemalePct">{{ $totalBirthsYear > 0 ? round(($birthsFemale / $totalBirthsYear)*100, 1) : 0 }}%</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LIGNE 2 : Décès par Tranches d'Âge & Top Hôpitaux -->
<div class="row">
    <!-- Décès par Tranche d'Âge -->
    <div class="col-xl-6 col-12">
        <div class="box shadow-sm mb-4">
            <div class="box-header with-border">
                <h4 class="box-title fw-bold text-dark">
                    <i class="fa fa-user-clock text-danger me-2"></i> Mortalité par Tranche d'Âge ({{ $year }})
                </h4>
            </div>
            <div class="box-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="ageDeathChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Établissements déclarants -->
    <div class="col-xl-6 col-12">
        <div class="box shadow-sm mb-4">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title fw-bold text-dark">
                    <i class="fa fa-hospital-user text-success me-2"></i> Activité Déclarative par Établissement ({{ $year }})
                </h4>
                <a href="{{ route('ministere.hopitaux') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="box-body">
                <div style="height: 280px; position: relative;">
                    <canvas id="hospitalsActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LIGNE 3 : Registres Récents -->
<div class="row">
    <!-- Dernières Naissances -->
    <div class="col-xl-6 col-12">
        <div class="box shadow-sm mb-4">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title fw-bold text-dark">
                    <i class="fa fa-baby text-primary me-2"></i> Dernières Déclarations de Naissance
                </h4>
                <a href="{{ route('ministere.naissances') }}" class="btn btn-sm btn-primary">Voir le registre complet</a>
            </div>
            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Enfant / Réf</th>
                                <th class="text-center">Établissement</th>
                                <th class="text-center">Genre</th>
                                <th class="text-center">Date & Heure</th>
                            </tr>
                        </thead>
                        <tbody id="recentBirthsTbody">
                            @forelse($recentBirths as $b)
                                <tr>
                                    <td class="text-center">
                                        <div class="fw-bold text-dark">{{ $b->enfant->user->name ?? 'Enfant' }} {{ $b->enfant->user->prenom ?? '' }}</div>
                                        <small class="text-muted">N°: {{ $b->numero_declaration ?: ($b->reference ?: '#'.$b->id) }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-dark">{{ $b->declaration->hospital->label ?? ($b->declaration->hospital->nom_direction_generale ?? 'Hôpital') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(in_array(strtolower($b->genre), ['m', 'masculin']))
                                            <span class="badge bg-primary-subtle text-primary">Garçon</span>
                                        @elseif(in_array(strtolower($b->genre), ['f', 'feminin', 'féminin']))
                                            <span class="badge bg-danger-subtle text-danger">Fille</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $b->genre ?: '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $b->date ? \Carbon\Carbon::parse($b->date)->format('d/m/Y') : ($b->created_at ? $b->created_at->format('d/m/Y') : '-') }} {{ $b->heure ? 'à '.$b->heure : '' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Aucune naissance enregistrée récemment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers Décès -->
    <div class="col-xl-6 col-12">
        <div class="box shadow-sm mb-4">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title fw-bold text-dark">
                    <i class="fa fa-cross text-danger me-2"></i> Dernières Déclarations de Décès
                </h4>
                <a href="{{ route('ministere.deces') }}" class="btn btn-sm btn-danger">Voir le registre complet</a>
            </div>
            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Défunt / Réf</th>
                                <th class="text-center">Établissement</th>
                                <th class="text-center">Type / Cause</th>
                                <th class="text-center">Date du décès</th>
                            </tr>
                        </thead>
                        <tbody id="recentDeathsTbody">
                            @forelse($recentDeaths as $d)
                                <tr>
                                    <td class="text-center">
                                        <div class="fw-bold text-dark">
                                            @if($d->person == 'enfant')
                                                Nouveau-né
                                            @else
                                                {{ $d->declaration->patient->user->name ?? 'Patient' }} {{ $d->declaration->patient->user->prenom ?? '' }}
                                            @endif
                                        </div>
                                        <small class="text-muted">N°: {{ $d->numero_declaration ?: ($d->reference ?: '#'.$d->id) }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-dark">{{ $d->declaration->hospital->label ?? ($d->declaration->hospital->nom_direction_generale ?? 'Hôpital') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($d->deces_maternel)
                                            <span class="badge bg-warning text-dark"><i class="fa fa-female me-1"></i> Maternel</span>
                                        @endif
                                        <div class="text-truncate d-inline-block" style="max-width: 180px;" title="{{ $d->cause_initiale ?: $d->cause_directe }}">
                                            <small class="text-muted">{{ $d->cause_initiale ?: ($d->cause_directe ?: 'Cause non précisée') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $d->date ? \Carbon\Carbon::parse($d->date)->format('d/m/Y') : ($d->created_at ? $d->created_at->format('d/m/Y') : '-') }} {{ $d->heure ? 'à '.$d->heure : '' }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Aucun décès enregistré récemment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulseGlow {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.3; transform: scale(0.85); }
    100% { opacity: 1; transform: scale(1); }
}
.pulse {
    display: inline-block;
    animation: pulseGlow 1.5s infinite ease-in-out;
}
</style>

<!-- SCRIPTS GRAPHIQUES CHART.JS & ACTUALISATION AUTOMATIQUE (30 SECONDES) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const monthsLabels = @json($monthsLabels);
    let birthsData = @json(array_values($birthsMonthly));
    let deathsData = @json(array_values($deathsMonthly));

    // 1. GRAPHE ÉVOLUTION MENSUELLE (Courbes pures : Naissances vs Décès)
    const ctxMonthly = document.getElementById('monthlyTrendChart').getContext('2d');
    const chartMonthly = new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: monthsLabels,
            datasets: [
                {
                    label: 'Naissances',
                    data: birthsData,
                    borderColor: '#1e88e5',
                    backgroundColor: 'rgba(30, 136, 229, 0.08)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#1e88e5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                },
                {
                    label: 'Décès',
                    data: deathsData,
                    borderColor: '#e53935',
                    backgroundColor: 'rgba(229, 57, 53, 0.08)',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#e53935',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { family: 'Segoe UI', size: 12 } }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // 2. GRAPHE GENRE NAISSANCES (Doughnut)
    const ctxGender = document.getElementById('genderBirthChart').getContext('2d');
    const chartGender = new Chart(ctxGender, {
        type: 'doughnut',
        data: {
            labels: ['Garçons', 'Filles', 'Autre / Non précisé'],
            datasets: [{
                data: [{{ $birthsMale }}, {{ $birthsFemale }}, {{ $birthsOther }}],
                backgroundColor: ['#2196f3', '#e91e63', '#9e9e9e'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 3. GRAPHE TRANCHES D'ÂGE DÉCÈS (Horizontal Bar)
    let ageCategories = @json($ageCategories);
    const ctxAge = document.getElementById('ageDeathChart').getContext('2d');
    const chartAge = new Chart(ctxAge, {
        type: 'bar',
        data: {
            labels: Object.keys(ageCategories),
            datasets: [{
                label: 'Nombre de Décès',
                data: Object.values(ageCategories),
                backgroundColor: [
                    '#ff7043',
                    '#ffa726',
                    '#ffca28',
                    '#ef5350',
                    '#ab47bc',
                    '#78909c'
                ],
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });

    // 4. GRAPHE TOP HÔPITAUX DÉCLARANTS
    @php
        $hospNames = [];
        $hospBirths = [];
        $hospDeaths = [];
        $allTopHospIds = $topHospitalsBirths->pluck('hospital_id')->merge($topHospitalsDeaths->pluck('hospital_id'))->unique()->take(6);
        foreach($allTopHospIds as $hId) {
            $hObj = $hospitals->firstWhere('id', $hId);
            $hName = $hObj ? ($hObj->label ?: $hObj->nom_direction_generale) : "Hôpital #$hId";
            $hospNames[] = \Illuminate\Support\Str::limit($hName, 18);
            $bCount = $topHospitalsBirths->firstWhere('hospital_id', $hId)->total ?? 0;
            $dCount = $topHospitalsDeaths->firstWhere('hospital_id', $hId)->total ?? 0;
            $hospBirths[] = $bCount;
            $hospDeaths[] = $dCount;
        }
    @endphp

    let hospLabels = @json($hospNames);
    let hospBirthsData = @json($hospBirths);
    let hospDeathsData = @json($hospDeaths);

    const ctxHosp = document.getElementById('hospitalsActivityChart').getContext('2d');
    const chartHosp = new Chart(ctxHosp, {
        type: 'bar',
        data: {
            labels: hospLabels.length ? hospLabels : ['Aucune donnée'],
            datasets: [
                {
                    label: 'Naissances',
                    data: hospBirthsData.length ? hospBirthsData : [0],
                    backgroundColor: 'rgba(33, 150, 243, 0.8)',
                    borderRadius: 4
                },
                {
                    label: 'Décès',
                    data: hospDeathsData.length ? hospDeathsData : [0],
                    backgroundColor: 'rgba(244, 67, 54, 0.8)',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // --- FONCTION DE RAFRAÎCHISSEMENT AUTOMATIQUE (CHAQUE 30 SECONDES) ---
    function refreshDashboardData() {
        const year = document.getElementById('selectYear').value;
        const hospitalId = document.getElementById('selectHospital').value;

        const url = `{{ route('ministere.ajax_stats') }}?year=${encodeURIComponent(year)}&hospital_id=${encodeURIComponent(hospitalId)}`;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Erreur réseau');
            return response.json();
        })
        .then(data => {
            // 1. Mise à jour des KPIs
            if (document.getElementById('kpiBirthsYear')) document.getElementById('kpiBirthsYear').textContent = data.totalBirthsYear;
            if (document.getElementById('kpiDeathsYear')) document.getElementById('kpiDeathsYear').textContent = data.totalDeathsYear;
            if (document.getElementById('kpiBirthsAll')) document.getElementById('kpiBirthsAll').textContent = data.totalBirthsAll;
            if (document.getElementById('kpiDeathsAll')) document.getElementById('kpiDeathsAll').textContent = data.totalDeathsAll;
            if (document.getElementById('kpiMaternalYear')) document.getElementById('kpiMaternalYear').textContent = data.maternalDeathsYear;
            if (document.getElementById('kpiMaternalRate')) document.getElementById('kpiMaternalRate').textContent = `${data.maternalRate} ‰`;
            if (document.getElementById('kpiHospitalsCount')) document.getElementById('kpiHospitalsCount').textContent = data.activeHospitalsCount;

            if (document.getElementById('genderMaleCount')) document.getElementById('genderMaleCount').textContent = data.birthsMale;
            if (document.getElementById('genderFemaleCount')) document.getElementById('genderFemaleCount').textContent = data.birthsFemale;
            if (document.getElementById('genderMalePct')) document.getElementById('genderMalePct').textContent = `${data.birthsMalePct}%`;
            if (document.getElementById('genderFemalePct')) document.getElementById('genderFemalePct').textContent = `${data.birthsFemalePct}%`;

            if (document.getElementById('lastUpdatedText')) document.getElementById('lastUpdatedText').textContent = `Dernière mise à jour : ${data.updated_at}`;

            // 2. Mise à jour du Graphe Mensuel (Courbes)
            chartMonthly.data.datasets[0].data = data.birthsMonthly;
            chartMonthly.data.datasets[1].data = data.deathsMonthly;
            chartMonthly.update();

            // 3. Mise à jour du Graphe Genre
            chartGender.data.datasets[0].data = [data.birthsMale, data.birthsFemale, data.birthsOther];
            chartGender.update();

            // 4. Mise à jour du Graphe Tranches d'Âge
            chartAge.data.labels = Object.keys(data.ageCategories);
            chartAge.data.datasets[0].data = Object.values(data.ageCategories);
            chartAge.update();

            // 5. Mise à jour du Graphe Top Hôpitaux
            if (data.hospLabels && data.hospLabels.length > 0) {
                chartHosp.data.labels = data.hospLabels;
                chartHosp.data.datasets[0].data = data.hospBirthsData;
                chartHosp.data.datasets[1].data = data.hospDeathsData;
                chartHosp.update();
            }

            // 6. Mise à jour du Tableau des Naissances Récentes
            const birthsTbody = document.getElementById('recentBirthsTbody');
            if (birthsTbody && data.recentBirths) {
                if (data.recentBirths.length === 0) {
                    birthsTbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">Aucune naissance enregistrée récemment.</td></tr>';
                } else {
                    let bHtml = '';
                    data.recentBirths.forEach(b => {
                        let genreBadge = '<span class="badge bg-secondary">-</span>';
                        if (b.genre && ['m', 'masculin'].includes(b.genre.toLowerCase())) {
                            genreBadge = '<span class="badge bg-primary-subtle text-primary">Garçon</span>';
                        } else if (b.genre && ['f', 'feminin', 'féminin'].includes(b.genre.toLowerCase())) {
                            genreBadge = '<span class="badge bg-danger-subtle text-danger">Fille</span>';
                        }
                        bHtml += `
                            <tr>
                                <td class="text-center">
                                    <div class="fw-bold text-dark">${b.nom}</div>
                                    <small class="text-muted">N°: ${b.numero}</small>
                                </td>
                                <td class="text-center">
                                    <span class="text-dark">${b.hopital}</span>
                                </td>
                                <td class="text-center">${genreBadge}</td>
                                <td class="text-center">
                                    <small class="text-muted">${b.date}</small>
                                </td>
                            </tr>
                        `;
                    });
                    birthsTbody.innerHTML = bHtml;
                }
            }

            // 7. Mise à jour du Tableau des Décès Récents
            const deathsTbody = document.getElementById('recentDeathsTbody');
            if (deathsTbody && data.recentDeaths) {
                if (data.recentDeaths.length === 0) {
                    deathsTbody.innerHTML = '<tr><td colspan="4" class="text-center py-3 text-muted">Aucun décès enregistré récemment.</td></tr>';
                } else {
                    let dHtml = '';
                    data.recentDeaths.forEach(d => {
                        let matBadge = d.deces_maternel ? '<span class="badge bg-warning text-dark me-1"><i class="fa fa-female me-1"></i> Maternel</span>' : '';
                        dHtml += `
                            <tr>
                                <td class="text-center">
                                    <div class="fw-bold text-dark">${d.nom}</div>
                                    <small class="text-muted">N°: ${d.numero}</small>
                                </td>
                                <td class="text-center">
                                    <span class="text-dark">${d.hopital}</span>
                                </td>
                                <td class="text-center">
                                    ${matBadge}
                                    <div class="text-truncate d-inline-block" style="max-width: 180px;" title="${d.cause}">
                                        <small class="text-muted">${d.cause}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">${d.date}</small>
                                </td>
                            </tr>
                        `;
                    });
                    deathsTbody.innerHTML = dHtml;
                }
            }
        })
        .catch(err => {
            console.warn('Erreur lors de l\'actualisation automatique :', err);
        });
    }

    // Intervalle de 30 secondes (30 000 ms)
    setInterval(refreshDashboardData, 30000);
});
</script>
@endsection
