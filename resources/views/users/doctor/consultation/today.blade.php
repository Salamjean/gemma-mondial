@extends('layouts.dashboard', ['title' => 'Liste des consultations'])

@section('content')
    <!-- Section Demandes de Téléconsultation en ligne en attente -->
    @include('partials.doctor_pending_teleconsultations')

    <div class="box border-0 shadow-sm rounded-20 bg-white">
        <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="box-title mb-0 fw-bold text-dark fs-16">
                    <i class="fa-solid fa-stethoscope text-primary me-2"></i><b>VOS CONSULTATIONS DU JOUR EN COURS</b>
                </h4>
            </div>
            @php
                $doctorId = \Illuminate\Support\Facades\Auth::user()->doctor->id;
                $countAllConsultations = \App\Models\Consultation::where('doctor_id', $doctorId)
                    ->where('status', 0)
                    ->whereNull('call_channel')
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
        <div class="box-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                    <thead>
                        <tr>
                            <th class="bb-2">Reference</th>
                            <th class="bb-2">Nom du Patient</th>
                            <th class="bb-2">Motif</th>
                            <th class="bb-2">Status</th>
                            <th class="bb-2 text-center">Actions</th>
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
                                <td><b>{{ $item->patient->code_patient }}</b></td>
                                <td>
                                    {{ $item->patient->user->name }}&nbsp; {{ $item->patient->user->prenom }}
                                </td>
                                <td class="" style="width: 200px;">
                                    {{ $item->prestationHospital->prestationService->libelle }}

                                </td>

                                <td class="">
                                    @if ($item->status == 0)
                                        <span class="badge badge-danger">En attente</span>
                                    @else
                                        <span class="badge badge-success">Terminée</span> <br>

                                    @endif
                                </td>

                                <td class="text-center">

                                    @if ($item->status == 0)
                                        <a href="{{ route('doctor.consultation.formulaire', $item->id) }}"
                                            class="btn btn-sm btn-info" id="menu" title="Menu">
                                            <span class="">Commencer la consultation</span>

                                        </a>
                                    @else
                                        <a href="{{ route('doctor.consultation.detail', $item->id) }}" class="btn btn-sm btn-info"
                                            title="detail consultation">
                                            <span class="">Détail</span>
                                        </a>
                                    @endif
                                    <a href="javascript:void(0)"
                                        onclick="openCardModal('{{ route('doctor.consultation.patient.card', $item->patient->id) }}')"
                                        class="btn btn-sm btn-warning" title="Carte numérique"><i
                                            class="fa-solid fa-id-card"></i></a>
                                </td>
                            </tr>
                        @endforeach
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