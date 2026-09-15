@extends('layouts.dashboard', ['title' => 'Toutes les consultations en attente'])

@section('content')
    <!-- Section Demandes de Téléconsultation en ligne en attente -->
    @include('partials.doctor_pending_teleconsultations')

    <div class="box border-0 shadow-sm rounded-20 bg-white">
        <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h3 class="box-title fw-bold text-dark mb-1 fs-20">
                    <i class="fa-solid fa-users-viewfinder text-primary me-2"></i> File d'Attente Globale Médecin - Tous les Patients
                </h3>
                <p class="text-muted mb-0 fs-13">
                    Liste intégrale de tous les patients en attente de consultation médicale, sans restriction sur la date.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary rounded-10 px-15 py-8 fw-semibold me-2">
                    <i class="fa-solid fa-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary rounded-10 px-15 py-8 fw-semibold">
                    <i class="fa-solid fa-calendar-day me-1"></i> Consultations du Jour
                </a>
            </div>
        </div>

        <div class="box-body p-20">
            <div class="table-responsive">
                <table id="example1" class="table table-striped table-hover display nowrap margin-top-10 w-p100 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="bb-2">Date & Heure</th>
                            <th class="bb-2">Code Patient</th>
                            <th class="bb-2">Nom & Prénom(s)</th>
                            <th class="bb-2">Prestation / Service</th>
                            <th class="bb-2">Statut Soins Infirmier</th>
                            <th class="bb-2 text-center">Statut Médecin</th>
                            <th class="bb-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($consultations as $item)
                            @php
                                $isToday = \Carbon\Carbon::parse($item->created_at)->isToday();
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge {{ $isToday ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} fw-bold fs-12 px-2 py-1 rounded-pill">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-13">{{ optional($item->patient)->code_patient ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-14">
                                        {{ optional(optional($item->patient)->user)->name ?? '' }} {{ optional(optional($item->patient)->user)->prenom ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light-primary text-primary fw-semibold fs-12">
                                        {{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? 'Consultation' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->status_inf == 1)
                                        <span class="badge bg-light-success text-success fw-bold px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-check me-1"></i> Constantes prises
                                        </span>
                                    @else
                                        <span class="badge bg-light-warning text-warning fw-bold px-2 py-1 rounded-pill">
                                            <i class="fa-solid fa-clock me-1"></i> En attente soins
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->status == 0)
                                        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">En attente</span>
                                    @else
                                        <span class="badge bg-success fw-bold px-3 py-2 rounded-pill">Terminée</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->status == 0)
                                        @php
                                            $srvLibelle = optional(optional(optional($item->prestationHospital)->prestationService)->service)->libelle ?? '';
                                            $prestLibelle = optional(optional($item->prestationHospital)->prestationService)->libelle ?? '';
                                            $isConsultationService = (stripos($srvLibelle, 'consultation') !== false) || (stripos($prestLibelle, 'consultation') !== false);
                                        @endphp
                                        <a href="{{ route('doctor.consultation.formulaire', $item->id) }}" class="btn btn-sm btn-primary rounded-8 fw-semibold shadow-sm px-15 me-1">
                                            <i class="fa-solid fa-stethoscope me-1"></i> Commencer
                                        </a>
                                    @else
                                        <a href="{{ route('doctor.consultation.detail', $item->id) }}" class="btn btn-sm btn-info text-white rounded-8 fw-semibold px-15 me-1">
                                            <i class="fa-solid fa-eye me-1"></i> Détail
                                        </a>
                                    @endif
                                    <a href="javascript:void(0)"
                                        onclick="openCardModal('{{ route('doctor.consultation.patient.card', optional($item->patient)->id ?? 0) }}')"
                                        class="btn btn-sm btn-warning text-dark rounded-8 fw-semibold" title="Carte numérique">
                                        <i class="fa-solid fa-id-card"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted fs-14">
                                    <i class="fa-solid fa-check-circle fs-24 text-success d-block mb-2"></i>
                                    Aucune consultation en attente dans la file d'attente globale.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openCardModal(url) {
            Swal.fire({
                html: '<div id="swal-card-container" style="min-height: 600px; overflow: hidden;"></div>',
                width: '1500px',
                maxWidth: '95vw',
                padding: '2em',
                background: 'transparent',
                showConfirmButton: false,
                showCloseButton: true,
                didOpen: () => {
                    Swal.getPopup().style.overflow = 'hidden';
                    Swal.showLoading();
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => {
                            if (!response.ok) throw new Error("Network response was not ok");
                            return response.text();
                        })
                        .then(html => {
                            Swal.hideLoading();
                            const container = document.getElementById('swal-card-container');
                            container.innerHTML = html;

                            const scripts = container.querySelectorAll("script");
                            scripts.forEach(oldScript => {
                                const newScript = document.createElement("script");
                                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                oldScript.parentNode.replaceChild(newScript, oldScript);
                            });
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Impossible de charger la carte.',
                                confirmButtonColor: '#3596f7'
                            });
                            console.error(err);
                        });
                }
            });
        }
    </script>
@endpush
