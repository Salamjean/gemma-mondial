@extends('layouts.dashboard')

@push('css')
<!-- FullCalendar 6 CSS & Styling modernisé -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" />
<style>
    .fc-theme-standard {
        font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .agenda-stat-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .agenda-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    .agenda-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
        flex-wrap: wrap;
        gap: 10px;
    }
    .fc-toolbar-title {
        font-size: 1.4rem !important;
        font-weight: 700 !important;
        color: #1e293b;
    }
    .fc-button-primary {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 6px 14px !important;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.25);
    }
    .fc-button-primary:hover, .fc-button-primary:active, .fc-button-primary.fc-button-active {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
    }
    .fc-event {
        border-radius: 6px !important;
        padding: 3px 6px !important;
        cursor: pointer;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .fc-event:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    .fc-daygrid-day-number {
        font-weight: 600;
        color: #334155;
    }
    .fc-day-today {
        background-color: rgba(59, 130, 246, 0.05) !important;
    }
    .agenda-filter-bar {
        background: #ffffff;
        border-radius: 14px;
        padding: 15px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 20px;
    }
    .avatar-circle-lg {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête avec titre & fil d'Ariane -->
    <div class="row mb-20">
        <div class="col-12">
            <div class="box bb-4 border-primary shadow-sm">
                <div class="box-header with-border d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="box-title fw-bold text-dark fs-24 mb-5"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Agenda & Planning Général du Personnel</h3>
                        <h6 class="box-subtitle text-muted mb-0">Consultez en temps réel la disponibilité des médecins, infirmiers et les rendez-vous planifiés.</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de Statistiques / KPI -->
    <div class="row mb-20 mb-md-25">
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card agenda-stat-card shadow-sm border-0 bg-white p-15 p-md-20">
                <div class="d-flex align-items-center">
                    <div class="agenda-stat-icon bg-primary-light text-primary me-15">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalAvailabilities }}</h4>
                        <span class="text-muted fs-13 fs-md-14 fw-semibold">Disponibilités enregistrées</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card agenda-stat-card shadow-sm border-0 bg-white p-15 p-md-20">
                <div class="d-flex align-items-center">
                    <div class="agenda-stat-icon bg-indigo-light text-indigo me-15" style="background-color: #e0e7ff; color: #4f46e5;">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $doctorsCount }}</h4>
                        <span class="text-muted fs-13 fs-md-14 fw-semibold">Médecins actifs</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card agenda-stat-card shadow-sm border-0 bg-white p-15 p-md-20">
                <div class="d-flex align-items-center">
                    <div class="agenda-stat-icon bg-success-light text-success me-15">
                        <i class="fa-solid fa-user-nurse"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $infirmiersCount }}</h4>
                        <span class="text-muted fs-13 fs-md-14 fw-semibold">Infirmiers de service</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card agenda-stat-card shadow-sm border-0 bg-white p-15 p-md-20">
                <div class="d-flex align-items-center">
                    <div class="agenda-stat-icon bg-danger-light text-danger me-15">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $rdvCount }}</h4>
                        <span class="text-muted fs-13 fs-md-14 fw-semibold">Rendez-vous programmés</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de Filtrage Interactif -->
    <div class="agenda-filter-bar shadow-sm p-15 p-md-20">
        <div class="row align-items-center g-2 g-md-3">
            <div class="col-12 col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="calendarSearch" class="form-control bg-light border-0" placeholder="Rechercher...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center">
                    <label class="form-label mb-0 me-2 text-nowrap fw-semibold text-muted"><i class="fa-solid fa-filter me-1"></i> Filtrer par :</label>
                    <select id="roleFilter" class="form-select border-0 bg-light fw-semibold">
                        <option value="all">Tous les événements</option>
                        <option value="doctor">🟦 Médecins uniquement</option>
                        <option value="infirmier">🟩 Infirmiers uniquement</option>
                        <option value="rdv">🟥 Rendez-vous patients</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <span class="badge bg-primary-light text-primary px-3 py-2 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size: 9px; color: #4f46e5;"></i> Médecins</span>
                    <span class="badge bg-success-light text-success px-3 py-2 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size: 9px; color: #059669;"></i> Infirmiers</span>
                    <span class="badge bg-danger-light text-danger px-3 py-2 rounded-pill"><i class="fa-solid fa-circle me-1" style="font-size: 9px; color: #e11d48;"></i> Rendez-vous</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier Principal -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-16 p-20 bg-white">
                <div class="card-body p-0">
                    <div id="fullCalendarContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détail d'Événement / Disponibilité -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-16">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark fs-20" id="eventModalTitle">Détail du Créneau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-25">
                <div class="text-center mb-20">
                    <div id="modalAvatar" class="avatar-circle-lg mx-auto mb-15 bg-primary">
                        <span id="modalInitials">DR</span>
                    </div>
                    <h4 class="fw-bold mb-5" id="modalFullName">Nom complet</h4>
                    <span class="badge px-3 py-2 rounded-pill fs-14 fw-semibold" id="modalRoleBadge">Role</span>
                </div>

                <hr class="my-20">

                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-15 bg-light rounded-12">
                            <span class="text-muted fs-12 d-block fw-semibold uppercase"><i class="fa-regular fa-clock me-1 text-primary"></i> Heure de début</span>
                            <strong class="fs-16 text-dark" id="modalStartTime">08:00</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-15 bg-light rounded-12">
                            <span class="text-muted fs-12 d-block fw-semibold uppercase"><i class="fa-regular fa-clock me-1 text-danger"></i> Heure de fin</span>
                            <strong class="fs-16 text-dark" id="modalEndTime">17:00</strong>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-15 bg-light rounded-12">
                            <span class="text-muted fs-12 d-block fw-semibold uppercase"><i class="fa-solid fa-phone me-1 text-success"></i> Téléphone / Contact</span>
                            <span class="fs-15 text-dark fw-bold" id="modalPhone">--</span>
                        </div>
                    </div>

                    <div class="col-12" id="modalExtraContainer">
                        <div class="p-15 bg-light rounded-12">
                            <span class="text-muted fs-12 d-block fw-semibold uppercase"><i class="fa-solid fa-circle-info me-1 text-info"></i> Information complémentaire</span>
                            <span class="fs-15 text-dark fw-medium" id="modalExtraText">--</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary w-100 fw-semibold rounded-12" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/fr.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rawEvents = @json($events);
        const calendarEl = document.getElementById('fullCalendarContainer');
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'fr',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: "Aujourd'hui",
                month: 'Mois',
                week: 'Semaine',
                day: 'Jour',
                list: 'Liste'
            },
            themeSystem: 'standard',
            events: rawEvents,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            eventClick: function(info) {
                const props = info.event.extendedProps || {};
                const fullName = props.full_name || info.event.title;
                const initials = fullName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() || 'PM';

                document.getElementById('modalFullName').textContent = fullName;
                document.getElementById('modalInitials').textContent = initials;
                document.getElementById('modalStartTime').textContent = props.start_time || '08:00';
                document.getElementById('modalEndTime').textContent = props.end_time || '17:00';
                document.getElementById('modalPhone').textContent = props.telephone || 'Non renseigné';

                const badgeEl = document.getElementById('modalRoleBadge');
                badgeEl.textContent = props.role || 'Créneau';
                badgeEl.className = 'badge px-3 py-2 rounded-pill fs-14 fw-semibold ' + (props.badge_class || 'bg-primary');

                const extraContainer = document.getElementById('modalExtraContainer');
                const extraText = document.getElementById('modalExtraText');

                if (props.type === 'rdv') {
                    extraContainer.style.display = 'block';
                    extraText.textContent = "RDV avec Docteur : " + (props.doctor_name || 'Non spécifié') + " — Motif: " + (props.motif || 'Consultation');
                } else {
                    extraContainer.style.display = 'block';
                    extraText.textContent = "Créneau de garde / consultation régulière enregistrée dans le planning.";
                }

                const avatarEl = document.getElementById('modalAvatar');
                if (props.role_key === 'doctor') {
                    avatarEl.style.backgroundColor = '#4f46e5';
                } else if (props.role_key === 'infirmier') {
                    avatarEl.style.backgroundColor = '#059669';
                } else {
                    avatarEl.style.backgroundColor = '#e11d48';
                }

                eventModal.show();
            }
        });

        calendar.render();

        // Filtrage par rôle
        document.getElementById('roleFilter').addEventListener('change', function() {
            const filterValue = this.value;
            calendar.removeAllEvents();

            if (filterValue === 'all') {
                calendar.addEventSource(rawEvents);
            } else {
                const filtered = rawEvents.filter(ev => {
                    return ev.extendedProps && ev.extendedProps.role_key === filterValue;
                });
                calendar.addEventSource(filtered);
            }
        });

        // Recherche textuelle
        document.getElementById('calendarSearch').addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            const filterValue = document.getElementById('roleFilter').value;

            calendar.removeAllEvents();

            const filtered = rawEvents.filter(ev => {
                const matchesRole = (filterValue === 'all') || (ev.extendedProps && ev.extendedProps.role_key === filterValue);
                const titleMatch = ev.title && ev.title.toLowerCase().includes(term);
                const roleMatch = ev.extendedProps && ev.extendedProps.role && ev.extendedProps.role.toLowerCase().includes(term);
                return matchesRole && (titleMatch || roleMatch);
            });

            calendar.addEventSource(filtered);
        });
    });
</script>
@endpush
