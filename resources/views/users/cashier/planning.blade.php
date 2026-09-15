@extends('layouts.dashboard', ['title' => $title ?? 'Planning & Disponibilités'])

@section('content')
<!-- Header Banner -->
<div class="box mb-30" style="background: linear-gradient(135deg, #1b3d6d 0%, #2e599b 100%); color: white; border-radius: 10px; padding: 20px 25px;">
    <div class="d-md-flex align-items-center justify-content-between">
        <div>
            <h3 class="mb-5 text-white font-weight-bold" style="font-size: 22px;">
                <i class="fa-solid fa-calendar-days me-10" style="color: #64b5f6;"></i>Mon Planning & Horaires de Caisse
            </h3>
            <span class="text-white-50" style="font-size: 14px;">Consultez vos jours de garde, vos tranches horaires et votre calendrier de permanence.</span>
        </div>
        <div class="mt-15 mt-md-0">
            <span class="badge bg-white text-primary px-15 py-10 fs-14 rounded-pill font-weight-bold shadow-sm me-10">
                <i class="fa-solid fa-user-clock me-5"></i> {{ $user->name ?? auth()->user()->name }} {{ $user->prenom ?? auth()->user()->prenom }} (Caisse)
            </span>
            <button onclick="window.print()" class="btn btn-outline-light btn-sm rounded-pill font-weight-bold">
                <i class="fa-solid fa-print me-5"></i> Imprimer
            </button>
        </div>
    </div>
</div>

@php
    $todayDayOfWeekIso = \Carbon\Carbon::now()->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
    $todayDayIndex = $todayDayOfWeekIso - 1; // 0 (Mon) to 6 (Sun)
    $isWorkingToday = in_array($todayDayIndex, $availableDays);
    $todayStart = $hourStart[$todayDayIndex] ?? '08:00';
    $todayEnd = $hourEnd[$todayDayIndex] ?? '17:00';
@endphp

<!-- KPI Cards Summary Row -->
<div class="row">
    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box bg-primary-light" style="border-left: 5px solid #3860ce; border-radius: 8px;">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <i class="fa-solid fa-briefcase text-primary" style="font-size: 36px;"></i>
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 font-weight-bold text-primary fs-24">{{ count($availableDays) }} / 7</h2>
                        <p class="text-fade mt-5 mb-0 text-primary font-weight-bold">Jours de Service / sem.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box bg-success-light" style="border-left: 5px solid #09bb86; border-radius: 8px;">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <i class="fa-solid {{ $isWorkingToday ? 'fa-cash-register' : 'fa-moon' }} text-success" style="font-size: 36px;"></i>
                    </div>
                    <div class="text-end">
                        @if ($isWorkingToday)
                            <h2 class="mb-0 font-weight-bold text-success fs-20">En Service</h2>
                            <p class="text-fade mt-5 mb-0 text-success font-weight-bold">{{ $todayStart }} - {{ $todayEnd }}</p>
                        @else
                            <h2 class="mb-0 font-weight-bold text-secondary fs-20">Jour de Repos</h2>
                            <p class="text-fade mt-5 mb-0 text-muted font-weight-bold">Pas de garde aujourd'hui</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box bg-info-light" style="border-left: 5px solid #17a2b8; border-radius: 8px;">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <i class="fa-solid fa-business-time text-info" style="font-size: 36px;"></i>
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 font-weight-bold text-info fs-22">{{ $todayStart }}</h2>
                        <p class="text-fade mt-5 mb-0 text-info font-weight-bold">Prise de poste habituelle</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-12">
        <div class="box bg-warning-light" style="border-left: 5px solid #ffc107; border-radius: 8px;">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <i class="fa-solid fa-shield-halved text-warning" style="font-size: 36px;"></i>
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 font-weight-bold text-warning fs-20">Fond de Caisse</h2>
                        <p class="text-fade mt-5 mb-0 text-warning font-weight-bold">Comptage obligatoire</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Schedule Breakdown -->
<div class="box">
    <div class="box-header" style="background-color: #f4f6f9; border-bottom: 2px solid #e9ecef; padding: 15px 20px;">
        <h4 class="box-title text-dark font-weight-bold mb-0">
            <i class="fa-solid fa-calendar-week me-10 text-primary"></i>Planning Hebdomadaire de Disponibilité Caisse
        </h4>
    </div>
    <div class="box-body">
        <div class="row">
            @foreach ($daysMap as $index => $dayName)
                @php
                    $isWorking = in_array($index, $availableDays);
                    $start = $hourStart[$index] ?? '08:00';
                    $end = $hourEnd[$index] ?? '17:00';
                    $isTodayDay = ($index === $todayDayIndex);
                @endphp
                <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-20">
                    <div class="box mb-0" style="border: 1px solid {{ $isTodayDay ? '#3860ce' : '#e4e6ef' }}; border-radius: 8px; box-shadow: {{ $isTodayDay ? '0 4px 12px rgba(56,96,206,0.15)' : 'none' }};">
                        <div class="box-body p-15">
                            <div class="d-flex justify-content-between align-items-center mb-10">
                                <span class="font-weight-bold fs-16 text-dark">
                                    {{ $dayName }}
                                    @if ($isTodayDay)
                                        <span class="badge badge-primary ms-5 fs-11">Aujourd'hui</span>
                                    @endif
                                </span>
                                @if ($isWorking)
                                    <span class="badge badge-success font-weight-bold">
                                        <i class="fa-solid fa-check me-5"></i>En Service
                                    </span>
                                @else
                                    <span class="badge badge-secondary font-weight-bold">
                                        <i class="fa-solid fa-minus me-5"></i>Repos
                                    </span>
                                @endif
                            </div>

                            @if ($isWorking)
                                <div class="p-10 bg-light rounded text-center my-10" style="background: #f8f9fa; border-radius: 6px;">
                                    <span class="fs-15 font-weight-bold text-dark">
                                        <i class="fa-regular fa-clock me-5 text-primary"></i> {{ $start }}
                                    </span>
                                    @if($end)
                                        <span class="fs-13 text-muted"> à {{ $end }}</span>
                                    @endif
                                </div>
                                <small class="text-muted fs-12 d-block text-center">
                                    <i class="fa-solid fa-location-dot me-5 text-danger"></i> Guichet Caisse Principale
                                </small>
                            @else
                                <div class="p-10 bg-light rounded text-center my-10 opacity-50" style="background: #f8f9fa; border-radius: 6px;">
                                    <span class="fs-13 text-muted">Pas de permanence</span>
                                </div>
                                <small class="text-muted fs-12 d-block text-center">
                                    Jour de repos
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Calendar Interactive Section -->
<div class="box">
    <div class="box-header" style="background-color: #f4f6f9; border-bottom: 2px solid #e9ecef; padding: 15px 20px;">
        <div class="d-md-flex justify-content-between align-items-center">
            <h4 class="box-title text-dark font-weight-bold mb-0">
                <i class="fa-solid fa-calendar-check me-10 text-primary"></i>Calendrier Mensuel Interactif
            </h4>
            <div class="mt-10 mt-md-0 fs-13">
                <span class="me-15">
                    <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: #09bb86;"></span> Jour de Garde Caisse
                </span>
                <span>
                    <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background-color: #3860ce;"></span> Aujourd'hui
                </span>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div id="calendar" style="min-height: 500px;"></div>
    </div>
</div>

<!-- Directives Card -->
<div class="box bg-primary-light border border-primary-light rounded-3 mt-4">
    <div class="box-body">
        <div class="d-flex align-items-start gap-3">
            <div class="p-3 bg-primary text-white rounded-circle d-none d-md-flex">
                <i class="fa-solid fa-circle-info fs-24"></i>
            </div>
            <div>
                <h5 class="fw-bold text-primary mb-1">Rappel des Directives de Caisse</h5>
                <ul class="mb-0 text-muted fs-14 ps-3">
                    <li>Veuillez procéder à l'ouverture de caisse 15 minutes avant le début de votre tranche horaire.</li>
                    <li>Chaque encaissement doit faire l'objet d'une validation immédiate dans le système.</li>
                    <li>En fin de journée, effectuez le rapport journalier de la recette et imprimez l'état récapitulatif pour la comptabilité.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var initialLocaleCode = 'fr';
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            locale: initialLocaleCode,
            buttonText: {
                today: "Aujourd'hui",
                month: 'Mois',
                week: 'Semaine'
            },
            events: [
                @foreach ($availableDays as $day)
                    @for ($i = -2; $i < 8; $i++)
                        {
                            title: 'Permanence Caisse',
                            start: '{{ date('Y-m-d', strtotime('this Monday +' . ($i * 7 + $day) . ' days')) }}',
                            display: 'block',
                            backgroundColor: '#09bb86',
                            borderColor: '#079d70',
                            textColor: '#ffffff'
                        },
                    @endfor
                @endforeach
            ],
            eventDidMount: function(info) {
                info.el.style.borderRadius = '6px';
                info.el.style.padding = '4px 8px';
                info.el.style.fontWeight = 'bold';
                info.el.style.fontSize = '12px';
                info.el.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
            }
        });
        calendar.render();
    });
</script>

<style>
    .fc-toolbar {
        background-color: #f8f9fa !important;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px !important;
    }
    .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #2b3a4a !important;
        text-transform: capitalize;
    }
    .fc-button-primary {
        background-color: #3860ce !important;
        border-color: #3860ce !important;
        color: #ffffff !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        text-transform: capitalize !important;
    }
    .fc-button-primary:hover {
        background-color: #2c4eb2 !important;
        border-color: #2c4eb2 !important;
    }
    .fc-col-header-cell {
        background-color: #3860ce !important;
        color: #ffffff !important;
        padding: 10px 0 !important;
        font-weight: 600 !important;
    }
    .fc-col-header-cell-cushion {
        color: #ffffff !important;
        text-decoration: none !important;
    }
    .fc-daygrid-day-number {
        color: #455a64 !important;
        font-weight: 600 !important;
        text-decoration: none !important;
    }
    .fc-day-today {
        background-color: rgba(56, 96, 206, 0.08) !important;
    }
</style>
@endsection
