<div class="row">
    <!-- Consultations du Jour -->
    <div class="col-xl-12 col-lg-12 col-12">
        <div class="box" style="background: rgba(255, 145, 0, 0.288); padding : 10px 20px;">
            @php
                $doctorId = \Illuminate\Support\Facades\Auth::user()->doctor->id;
                $countAllConsultations = \App\Models\Consultation::where('doctor_id', $doctorId)
                    ->where('status', 0)
                    ->whereNull('call_channel')
                    ->count();
            @endphp
            <div class="d-flex justify-content-between align-items-center mb-10">
                <div class="badge badge-dark" style="font-size: 20px;">CONSULTATIONS DU JOUR</div>
                <a href="{{ route('doctor.consultation.all') }}" class="btn btn-primary fw-bold shadow-sm" style="font-size: 14px; border-radius: 8px;">
                    <i class="fa-solid fa-layer-group me-1"></i> Toutes les consultations
                    <span class="badge bg-white text-primary ms-2 fs-14 fw-bolder">{{ $countAllConsultations }}</span>
                </a>
            </div>
            <div class="box-body text-center">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="bb-2" style="text-align: left;">Heure</th>
                                <th class="bb-2">Code</th>
                                <th class="bb-2">N° dossier médical</th>
                                <th class="bb-2">Nom & prénom(s)</th>
                                <th class="bb-2">Type de la visite</th>
                                <th class="bb-2 text-center">Dossier médical</th>
                                <th class="bb-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\Consultation::orderByDESC('created_at')->where('doctor_id', \Illuminate\Support\Facades\Auth::user()->doctor->id)->whereNull('call_channel')->where(function($q) { $q->whereDate('created_at', date('Y-m-d'))->orWhereDate('date_consultation', date('Y-m-d')); })->where('status', '0')->get() as $item)
                                <tr>
                                    <td style="text-align: left;">{{ $item->created_at ? $item->created_at->format('H:i:s') : '-' }}</td>
                                    <td style="text-align: center;"><b>{{ $item->code_consultation ?? 'CONS-'.$item->id }}</b></td>
                                    <td style="text-align: center;"><span class="fw-bold">{{ optional($item->patient)->code_patient ?? 'N/A' }}</span></td>
                                    <td style="text-align: center; text-transform: capitalize; width: 300px;">
                                        <span class="badge text-dark fw-900 fs-14">{{ optional(optional($item->patient)->user)->name }} {{ optional(optional($item->patient)->user)->prenom }}</span>
                                    </td>
                                    <td style="text-align: center;"><span class="badge badge-info">{{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? 'Consultation' }}</span></td>

                                    <td class="text-center" style="width: 220px;">
                                        @if (optional($item->patient)->id)
                                            <a href="{{ route('doctor.patient.dossier_medical', $item->patient->id) }}"
                                                class="btn btn-sm btn-outline-info rounded-pill fw-bold shadow-xs px-3 py-1.5 text-nowrap d-inline-flex align-items-center gap-1.5" title="Ouvrir le dossier médical complet">
                                                <i class="fa-solid fa-folder-open fs-13"></i>
                                                <span>Dossier médical</span>
                                            </a>
                                        @else
                                            <span class="text-muted fs-11">-</span>
                                        @endif
                                        <br>
                                        @if ($item->status_inf == 1)
                                            <span class="badge bg-light-success text-success fs-11 mt-1"><i class="fa-solid fa-check me-1"></i> Constantes prises</span>
                                        @else
                                            <span class="badge bg-light-warning text-warning fs-11 mt-1"><i class="fa-solid fa-clock me-1"></i> Constantes en attente</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('doctor.consultation.formulaire', $item->id) }}" class="btn btn-sm me-1" style="background: rgba(214, 110, 62, 0.452);" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" title="Faire la consultation">
                                            <span>Commencer</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Demandes de téléconsultation en ligne en attente (Remplaçant Liste de vos consultations) -->
    <div class="col-xl-12 col-lg-12 col-12 mt-3">
        @include('partials.doctor_pending_teleconsultations')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    if (typeof window.openCardModal !== 'function') {
        window.openCardModal = function(url) {
            if (typeof Swal === 'undefined') {
                window.open(url, '_blank');
                return;
            }
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
        };
    }
</script>
