@extends('layouts.dashboard', ['title' => 'Liste des consultations'])

@section('content')
    <!-- 1. Vos consultations du jour en cours (EN HAUT) -->
    <div class="box border-0 shadow-sm rounded-20 bg-white mb-4" id="doctorTodayConsultationsPageBox">
        <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="box-title mb-0 fw-bold text-dark fs-16 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-stethoscope text-primary me-1"></i><b>VOS CONSULTATIONS DU JOUR EN COURS</b>
                    <span class="badge bg-success-light text-success fs-12 fw-bold d-none d-sm-inline-flex align-items-center gap-1" title="Actualisation automatique toutes les 10s">
                        <i class="fa-solid fa-rotate fa-spin-pulse"></i> 10s
                    </span>
                </h4>
            </div>
            @php
                $doctorId = \Illuminate\Support\Facades\Auth::user()->doctor->id;
                $countAllConsultations = \App\Models\Consultation::where('doctor_id', $doctorId)
                    ->where('status_inf', 1)
                    ->where('status', 0)
                    ->whereNull('call_channel')
                    ->where(function($q) {
                        $q->whereNull('orientation_infirmier')->orWhere('orientation_infirmier', '');
                    })
                    ->count();
            @endphp
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary rounded-10 fw-bold shadow-sm me-1">
                    <i class="fa-solid fa-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('doctor.consultation.all') }}" class="btn btn-sm btn-primary rounded-10 fw-bold shadow-sm">
                    <i class="fa-solid fa-users me-1"></i> Toutes les consultations
                    <span class="badge bg-white text-primary ms-1 fs-12">{{ $countAllConsultations }}</span>
                </a>
            </div>
        </div>
        <div class="box-body p-20">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100 align-middle border">
                    <thead style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                        <tr class="text-dark fw-bold fs-13">
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 120px;">Référence</th>
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 180px;">Nom du Patient</th>
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 160px;">Motif</th>
                            <th class="py-3 px-3 text-dark fw-bold text-center" style="min-width: 150px;">Dossier médical</th>
                            <th class="py-3 px-3 text-dark fw-bold" style="min-width: 120px;">Statut</th>
                            <th class="py-3 px-3 text-dark fw-bold text-center" style="min-width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consultations as $item)
                            @php
                                $srvLibelle = optional(optional(optional($item->prestationHospital)->prestationService)->service)->libelle ?? '';
                                $prestLibelle = optional(optional($item->prestationHospital)->prestationService)->libelle ?? '';
                                $isConsultationService = (stripos($srvLibelle, 'consultation') !== false) || (stripos($prestLibelle, 'consultation') !== false);
                            @endphp
                            <tr>
                                <td><b>{{ optional($item->patient)->code_patient }}</b></td>
                                <td>
                                    {{ optional(optional($item->patient)->user)->name }}&nbsp; {{ optional(optional($item->patient)->user)->prenom }}
                                </td>
                                <td class="" style="width: 200px;">
                                    {{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? 'Consultation' }}
                                </td>

                                <td class="text-center">
                                    @if (optional($item->patient)->id)
                                        <a href="{{ route('doctor.patient.dossier_medical', $item->patient->id) }}"
                                            class="btn btn-sm btn-outline-info rounded-pill fw-bold shadow-xs px-3 py-1.5 text-nowrap d-inline-flex align-items-center gap-1.5" title="Ouvrir le dossier médical complet">
                                            <i class="fa-solid fa-folder-open fs-13"></i>
                                            <span>Dossier médical</span>
                                        </a>
                                    @else
                                        <span class="text-muted fs-11">-</span>
                                    @endif
                                </td>

                                <td class="">
                                    @if ($item->status == 0)
                                        <span class="badge badge-danger">En attente</span>
                                    @else
                                        <span class="badge badge-success">Terminée</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ($item->status == 0)
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button type="button" 
                                                class="btn btn-sm btn-outline-primary fw-bold px-2 py-1.5 rounded-8 btn-call-patient" 
                                                data-id="{{ $item->id }}" 
                                                data-name="{{ optional(optional($item->patient)->user)->name }} {{ optional(optional($item->patient)->user)->prenom }}"
                                                title="Appeler le patient sur l'écran de la salle d'attente">
                                                <i class="fa-solid fa-bullhorn me-1"></i> Appeler
                                            </button>
                                            <a href="{{ route('doctor.consultation.formulaire', $item->id) }}"
                                                class="btn btn-sm btn-info text-white fw-bold px-3 py-1.5 rounded-8" id="menu" title="Commencer">
                                                <i class="fa-solid fa-play me-1"></i> <span>Commencer</span>
                                            </a>
                                        </div>
                                    @else
                                        <a href="{{ route('doctor.consultation.detail', $item->id) }}" class="btn btn-sm btn-info text-white fw-bold px-3 py-1.5 rounded-8"
                                            title="detail consultation">
                                            <span>Détail</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Section Demandes de Téléconsultation en ligne en attente (EN BAS) -->
    @if(optional(optional(auth()->user()->doctor)->hospital)->is_teleconsultation_active ?? true)
        <div class="mt-4">
            @include('partials.doctor_pending_teleconsultations')
        </div>
    @endif
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.btn-call-patient', function(e) {
                e.preventDefault();
                const btn = $(this);
                const consultationId = btn.data('id');
                const patientName = btn.data('name') || 'le patient';
                const originalHtml = btn.html();

                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Appel...');

                $.ajax({
                    url: "{{ url('doctor/consultation/call-patient') }}/" + consultationId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html(originalHtml);
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Patient appelé !',
                                text: response.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Attention',
                                text: response.message || 'Erreur lors de l\'appel'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(originalHtml);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible d\'appeler le patient. Veuillez réessayer.'
                        });
                    }
                });
            });

            // Auto-actualisation 10s
            setInterval(function() {
                if (!document.hidden && !document.querySelector('.modal.show') && !document.querySelector('.swal2-container')) {
                    fetch(window.location.href, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => {
                        if (!response.ok) return null;
                        return response.text();
                    })
                    .then(html => {
                        if (!html) return;
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newEl = doc.getElementById('doctorTodayConsultationsPageBox');
                        const targetEl = document.getElementById('doctorTodayConsultationsPageBox');
                        if (newEl && targetEl) {
                            targetEl.innerHTML = newEl.innerHTML;
                        }
                    })
                    .catch(err => console.warn('Auto-refresh consultations du jour :', err));
                }
            }, 10000);
        });
    </script>
@endpush