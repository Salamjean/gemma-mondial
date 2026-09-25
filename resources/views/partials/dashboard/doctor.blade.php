<div class="row">
    <!-- Consultations du Jour -->
    <div class="col-xl-12 col-lg-12 col-12" id="doctorTodayConsultationsBox">
        <div class="box shadow-sm border-0 rounded-12" style="background: rgba(255, 145, 0, 0.12); border-left: 4px solid #ff9100 !important; padding: 15px 20px;">
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
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="badge badge-dark fs-15 py-2 px-3 fw-bold rounded-8">
                        <i class="fa-solid fa-stethoscope me-1 text-warning"></i> CONSULTATIONS DU JOUR
                    </div>
                    <span class="badge bg-success-light text-success fs-12 fw-bold d-none d-sm-inline-flex align-items-center gap-1" title="Actualisation automatique toutes les 10s">
                        <i class="fa-solid fa-rotate fa-spin-pulse"></i> 10s
                    </span>
                </div>
                <a href="{{ route('doctor.consultation.all') }}" class="btn btn-sm btn-primary fw-bold shadow-sm rounded-8 d-inline-flex align-items-center">
                    <i class="fa-solid fa-layer-group me-1"></i> Toutes les consultations
                    <span class="badge bg-white text-primary ms-2 fs-12 fw-bolder">{{ $countAllConsultations }}</span>
                </a>
            </div>
            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0 bg-white rounded-10 overflow-hidden">
                        <thead class="bg-light">
                            <tr class="text-dark fw-bold fs-12 text-uppercase">
                                <th class="ps-3 py-3" style="text-align: left;">Heure</th>
                                <th class="py-3 text-center">Code</th>
                                <th class="py-3 text-center">N° Dossier</th>
                                <th class="py-3">Nom & prénom(s)</th>
                                <th class="py-3 text-center">Type de visite</th>
                                <th class="py-3 text-center">Dossier médical</th>
                                <th class="pe-3 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse (\App\Models\Consultation::orderByDESC('created_at')->where('doctor_id', \Illuminate\Support\Facades\Auth::user()->doctor->id)->where('status_inf', 1)->whereNull('call_channel')->where(function($q) { $q->whereDate('created_at', date('Y-m-d'))->orWhereDate('date_consultation', date('Y-m-d')); })->where('status', '0')->where(function($q) { $q->whereNull('orientation_infirmier')->orWhere('orientation_infirmier', ''); })->get() as $item)
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted fs-12" style="text-align: left;">
                                        {{ $item->created_at ? $item->created_at->format('H:i:s') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <b class="text-dark">{{ $item->code_consultation ?? 'CONS-'.$item->id }}</b>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ optional($item->patient)->code_patient ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark text-capitalize fs-14">
                                            {{ optional(optional($item->patient)->user)->name }} {{ optional(optional($item->patient)->user)->prenom }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-light text-primary fw-semibold">
                                            {{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? 'Consultation' }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if (optional($item->patient)->id)
                                            <a href="{{ route('doctor.patient.dossier_medical', $item->patient->id) }}"
                                                class="btn btn-xs btn-sm btn-outline-info rounded-pill fw-bold shadow-xs px-2.5 py-1 text-nowrap d-inline-flex align-items-center gap-1" title="Ouvrir le dossier médical complet">
                                                <i class="fa-solid fa-folder-open fs-12"></i>
                                                <span>Dossier médical</span>
                                            </a>
                                        @else
                                            <span class="text-muted fs-11">-</span>
                                        @endif
                                        <div class="mt-1">
                                            @if ($item->status_inf == 1)
                                                <span class="badge bg-light-success text-success fs-10"><i class="fa-solid fa-check me-1"></i> Constantes prises</span>
                                            @else
                                                <span class="badge bg-light-warning text-warning fs-10"><i class="fa-solid fa-clock me-1"></i> Constantes en attente</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="pe-3 text-center">
                                        <a href="{{ route('doctor.consultation.formulaire', $item->id) }}" class="btn btn-sm btn-warning text-dark fw-bold rounded-8 px-3 py-1 shadow-sm" data-bs-toggle="tooltip"
                                            data-bs-placement="bottom" title="Faire la consultation">
                                            <i class="fa-solid fa-play me-1"></i> <span>Commencer</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted fs-13">
                                        Aucune consultation en attente pour aujourd'hui.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Demandes de téléconsultation en ligne en attente -->
    @if(optional(optional(auth()->user()->doctor)->hospital)->is_teleconsultation_active ?? true)
        <div class="col-xl-12 col-lg-12 col-12 mt-3">
            @include('partials.doctor_pending_teleconsultations')
        </div>
    @endif
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

    // Auto-actualisation du tableau des consultations du jour toutes les 10 secondes
    (function() {
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
                    const newEl = doc.getElementById('doctorTodayConsultationsBox');
                    const targetEl = document.getElementById('doctorTodayConsultationsBox');
                    if (newEl && targetEl) {
                        targetEl.innerHTML = newEl.innerHTML;
                    }
                })
                .catch(err => console.warn('Auto-refresh dashboard docteur :', err));
            }
        }, 10000);
    })();
</script>
