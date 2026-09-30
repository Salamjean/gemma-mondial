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

            <button type="button" onclick="exportDashboardChartsPDF()" class="btn btn-sm btn-danger shadow-sm fw-bold">
                <i class="fa fa-file-pdf me-1"></i> Exporter Graphiques PDF
            </button>

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
        })
        .catch(err => {
            console.warn('Erreur lors de l\'actualisation automatique :', err);
        });
    }

    // Intervalle de 30 secondes (30 000 ms)
    setInterval(refreshDashboardData, 30000);

    // --- FONCTION D'EXPORTATION EN PDF DES GRAPHIQUES ---
    window.exportDashboardChartsPDF = function () {
        const year = document.getElementById('selectYear') ? document.getElementById('selectYear').value : '{{ $year }}';
        const hospitalSelect = document.getElementById('selectHospital');
        const hospitalText = hospitalSelect && hospitalSelect.selectedIndex >= 0 ? hospitalSelect.options[hospitalSelect.selectedIndex].text : 'Tous les établissements';

        // Capturer les graphiques sous forme d'images Base64
        const imgMonthly = chartMonthly.toBase64Image('image/png', 1.0);
        const imgGender = chartGender.toBase64Image('image/png', 1.0);
        const imgAge = chartAge.toBase64Image('image/png', 1.0);
        const imgHosp = chartHosp.toBase64Image('image/png', 1.0);

        // Capturer les KPIs actuels
        const kpiBirths = document.getElementById('kpiBirthsYear') ? document.getElementById('kpiBirthsYear').textContent.trim() : '{{ $totalBirthsYear }}';
        const kpiDeaths = document.getElementById('kpiDeathsYear') ? document.getElementById('kpiDeathsYear').textContent.trim() : '{{ $totalDeathsYear }}';
        const kpiMaternal = document.getElementById('kpiMaternalYear') ? document.getElementById('kpiMaternalYear').textContent.trim() : '{{ $maternalDeathsYear }}';
        const kpiMaternalRate = document.getElementById('kpiMaternalRate') ? document.getElementById('kpiMaternalRate').textContent.trim() : '{{ $maternalRate }} ‰';
        const kpiHospitals = document.getElementById('kpiHospitalsCount') ? document.getElementById('kpiHospitalsCount').textContent.trim() : '{{ $activeHospitalsCount }}';

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Veuillez autoriser les fenêtres pop-up pour générer le PDF.');
            return;
        }

        const dateNow = new Date().toLocaleString('fr-FR', {
            day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });

        printWindow.document.write(`
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Rapport Statistique Graphique ${year} - Ministère de la Santé</title>
                <style>
                    @page {
                        size: A4 portrait;
                        margin: 10mm;
                    }
                    body {
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                        color: #1e293b;
                        background: #fff;
                        margin: 0;
                        padding: 0;
                        font-size: 11px;
                        line-height: 1.3;
                    }
                    .header-box {
                        border-bottom: 2px solid #0d5c3a;
                        padding-bottom: 8px;
                        margin-bottom: 12px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }
                    .header-left {
                        font-size: 9px;
                        text-transform: uppercase;
                        font-weight: bold;
                        color: #334155;
                    }
                    .header-title {
                        text-align: center;
                    }
                    .header-title h1 {
                        margin: 0;
                        font-size: 15px;
                        color: #0d5c3a;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .header-title h2 {
                        margin: 2px 0 0 0;
                        font-size: 11px;
                        color: #16a34a;
                        font-weight: normal;
                    }
                    .meta-banner {
                        background: #f8fafc;
                        border: 1px solid #e2e8f0;
                        border-radius: 6px;
                        padding: 6px 12px;
                        margin-bottom: 12px;
                        display: flex;
                        justify-content: space-between;
                        font-size: 10px;
                    }
                    .kpi-grid {
                        display: grid;
                        grid-template-columns: repeat(5, 1fr);
                        gap: 8px;
                        margin-bottom: 14px;
                    }
                    .kpi-card {
                        background: #f8fafc;
                        border: 1px solid #cbd5e1;
                        border-radius: 6px;
                        padding: 6px 8px;
                        text-align: center;
                    }
                    .kpi-card .value {
                        font-size: 15px;
                        font-weight: bold;
                        color: #0f172a;
                    }
                    .kpi-card .label {
                        font-size: 8.5px;
                        color: #64748b;
                        text-transform: uppercase;
                        margin-top: 2px;
                    }
                    .charts-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 12px;
                    }
                    .chart-card {
                        background: #fff;
                        border: 1px solid #e2e8f0;
                        border-radius: 6px;
                        padding: 8px;
                        text-align: center;
                    }
                    .chart-card.full-width {
                        grid-column: span 2;
                    }
                    .chart-card h3 {
                        margin: 0 0 6px 0;
                        font-size: 11px;
                        color: #0d5c3a;
                        font-weight: bold;
                        text-align: left;
                        border-bottom: 1px solid #f1f5f9;
                        padding-bottom: 4px;
                    }
                    .chart-card img {
                        width: 100%;
                        max-height: 200px;
                        object-fit: contain;
                    }
                    .footer {
                        margin-top: 14px;
                        border-top: 1px solid #cbd5e1;
                        padding-top: 6px;
                        display: flex;
                        justify-content: space-between;
                        font-size: 8px;
                        color: #64748b;
                    }
                    @media print {
                        body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header-box">
                    <div class="header-left">
                        <div>RÉPUBLIQUE DE CÔTE D'IVOIRE</div>
                        <div style="font-size: 7.5px; color: #64748b; font-weight: normal;">Union - Discipline - Travail</div>
                        <div style="color: #0d5c3a; font-size: 8px; margin-top: 2px;">MINISTÈRE DE LA SANTÉ</div>
                    </div>
                    <div class="header-title">
                        <h1>Rapport Statistique Graphique</h1>
                        <h2>Surveillance Démographique et Sanitaire (${year})</h2>
                    </div>
                    <div style="text-align: right; font-size: 8.5px; color: #475569;">
                        <div><strong>Édition :</strong> ${dateNow}</div>
                        <div style="color: #0d5c3a; font-weight: bold;">Document Officiel</div>
                    </div>
                </div>

                <div class="meta-banner">
                    <div><strong>Année analysée :</strong> ${year}</div>
                    <div><strong>Périmètre :</strong> ${hospitalText}</div>
                    <div><strong>Statut :</strong> Données nationales certifiées</div>
                </div>

                <!-- Indicateurs clés -->
                <div class="kpi-grid">
                    <div class="kpi-card" style="border-top: 3px solid #1e88e5;">
                        <div class="value" style="color: #1e88e5;">${kpiBirths}</div>
                        <div class="label">Naissances (${year})</div>
                    </div>
                    <div class="kpi-card" style="border-top: 3px solid #ef5350;">
                        <div class="value" style="color: #ef5350;">${kpiDeaths}</div>
                        <div class="label">Décès (${year})</div>
                    </div>
                    <div class="kpi-card" style="border-top: 3px solid #f59e0b;">
                        <div class="value" style="color: #b45309;">${kpiMaternal}</div>
                        <div class="label">Décès Maternels</div>
                    </div>
                    <div class="kpi-card" style="border-top: 3px solid #8b5cf6;">
                        <div class="value" style="color: #6d28d9;">${kpiMaternalRate}</div>
                        <div class="label">Taux Mortalité Mat.</div>
                    </div>
                    <div class="kpi-card" style="border-top: 3px solid #10b981;">
                        <div class="value" style="color: #059669;">${kpiHospitals}</div>
                        <div class="label">Centres Déclarants</div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="charts-grid">
                    <!-- Graphe 1 : Évolution Mensuelle -->
                    <div class="chart-card full-width">
                        <h3>1. Évolution Mensuelle Comparative (Naissances vs Décès ${year})</h3>
                        <img src="${imgMonthly}" alt="Évolution Mensuelle" style="max-height: 180px;">
                    </div>

                    <!-- Graphe 2 : Répartition par Genre -->
                    <div class="chart-card">
                        <h3>2. Répartition par Genre (Naissances)</h3>
                        <img src="${imgGender}" alt="Répartition Genre" style="max-height: 160px;">
                    </div>

                    <!-- Graphe 3 : Mortalité par Tranche d'Âge -->
                    <div class="chart-card">
                        <h3>3. Mortalité par Tranche d'Âge</h3>
                        <img src="${imgAge}" alt="Mortalité Âge" style="max-height: 160px;">
                    </div>

                    <!-- Graphe 4 : Activité Déclarative Établissements -->
                    <div class="chart-card full-width">
                        <h3>4. Activité Déclarative par Établissement de Santé (${year})</h3>
                        <img src="${imgHosp}" alt="Activité Établissements" style="max-height: 170px;">
                    </div>
                </div>

                <div class="footer">
                    <div>Plateforme Nationale Gemma Santé - Direction Générale de la Santé Publique</div>
                    <div>Généré le ${dateNow} - Page 1/1</div>
                </div>
            </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();
        setTimeout(function () {
            printWindow.print();
        }, 500);
    };
});
</script>
@endsection
