<div class="row">
    <div class="col-xl-4 col-lg-4 col-12">
        <div class="box bg-success-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5 w-100 h-100">
                        <img src="{{ asset('assets/icons/admission_money.png') }}" class="" alt="icon">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-900 text-success fs-24">
                            {{ \App\Models\Payment::where('caissiere_id', \Illuminate\Support\Facades\Auth::user()->cashier->id)->whereDate('created_at', date('Y-m-d'))->where('status', 'success')->sum('prix') }}
                            FCFA</h2>

                        <p class="text-fade mt-5 mb-0 text-success">Somme perçue (journée)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-12">
        <div class="box bg-success-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5 w-100 h-100">
                        <img src="{{ asset('assets/icons/protect.png') }}" class="" alt="icon">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-900 text-success fs-24">
                            {{ \App\Models\Assurance::where('caissiere_id', \Illuminate\Support\Facades\Auth::user()->cashier->id)->whereDate('created_at', date('Y-m-d'))->sum('prix') }}
                            FCFA</h2>

                        <p class="text-fade mt-5 mb-0 text-success">Assurance (journée)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-4 col-lg-4 col-12">
        <div class="box bg-success-light">
            <div class="box-body text-center">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5 w-100 h-100">
                        <img src="{{ asset('assets/icons/admission_valid.png') }}" class="" alt="secretariat">
                    </div>
                    <div class="text-end">
                        <h2 class="mb-0 fw-600 text-success">
                            {{ \App\Models\Payment::where('hospital_id', \Illuminate\Support\Facades\Auth::user()->cashier->hospital_id)->whereDate('created_at', date('Y-m-d'))->where('status', 'pending')->count() }}
                        </h2>
                        <p class="text-fade mt-5 mb-0 text-success">Admissions en Attente</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row" id="cashierTodayPaymentsBox">
    <div class="col-12  ">
        <div class="box">
            <div class="box-header">
                <div class="row">
                    <div class="col-xs-12 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="box-title mb-0"><b>PAIEMENTS EN ATTENTE DU JOUR</b></h4>
                            <span class="badge bg-success-light text-success fs-12 fw-bold d-none d-sm-inline-flex align-items-center gap-1" title="Actualisation automatique toutes les 10s">
                                <i class="fa-solid fa-rotate fa-spin-pulse"></i> 10s
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @php
                                $hospitalIdCashier = \Illuminate\Support\Facades\Auth::user()->cashier->hospital_id;
                                $countAllCashierPending = \App\Models\Payment::where('status', 'pending')
                                    ->where('hospital_id', $hospitalIdCashier)
                                    ->count();
                            @endphp
                            <a href="{{ route('cashier.admission.all') }}" class="btn btn-sm btn-primary rounded-10 fw-bold shadow-sm me-2">
                                <i class="fa-solid fa-list me-1"></i> Tous les paiements en attente
                                <span class="badge bg-white text-primary ms-1 fs-12">{{ $countAllCashierPending }}</span>
                            </a>
                            <a href="{{ route('cashier.admission.indicate', 'day') }}" target="_blank" title="Imprimer">
                                <span class="fa-solid fa-print fa-2x"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example5" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                        <thead class="bg-primary">
                            <tr>
                                <th class="bb-2">Code Patient</th>
                                <th class="bb-2">Type</th>
                                <th class="bb-2">Motif</th>
                                <th class="bb-2">Nom du Patient</th>
                                <th class="bb-2">Montant</th>
                                <th class="bb-2 text-center">Status</th>
                                <th class="bb-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach (App\Models\Payment::where('status', 'pending')->where('hospital_id', \Illuminate\Support\Facades\Auth::user()->cashier->hospital_id)->whereDate('created_at', date('Y-m-d'))->get() as $item)
                                <tr>
                                    <td>
                                        @if ($item->type == 'hospitalisation')
                                            <b>{{ $item->hospitalisation->consultation->patient->code_patient }}</b>
                                        @elseif ($item->type == 'admission')
                                            <b>{{ $item->admission->patient->code_patient }}</b>
                                        @endif
                                    </td>
                                    <td>{{ $item->type }}</td>
                                    <td>
                                        @if ($item->type == 'hospitalisation')
                                            Hospitalisation
                                        @elseif ($item->type == 'admission')
                                            {{ $item->admission->prestationHospital->prestationService->libelle }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->type == 'hospitalisation')
                                            <i>{{ $item->hospitalisation->consultation->patient->user->name }}&nbsp;
                                                {{ $item->hospitalisation->consultation->patient->user->prenom }}</i>
                                        @elseif ($item->type == 'admission')
                                            <i>{{ $item->admission->patient->user->name }}&nbsp;
                                                {{ $item->admission->patient->user->prenom }}</i>
                                        @endif
                                    </td>
                                    <td>
                                        <i>{{ $item->prix }} FCFA</i>

                                    </td>
                                    <td class="text-center">
                                        <span class="p-1 badge-warning ">
                                            En attente
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->status == 'pending')
                                            <a class="btn btn-sm btn-info"
                                                href="{{ route('cashier.admission.show', $item->id) }}"
                                                style="cursor: pointer" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Valider">
                                                Procéder au paiement
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
    </div>
</div>

<div class="row">
    <div class="col-12  ">
        <div class="box">
            <div class="box-header">
                <div class="row">
                    <div class="col-xs-12  d-flex justify-content-between">
                        <h4 class="box-title"><b>PAIEMENTS VALIDES POUR CE JOUR &bull; <span style="color:darkgreen;">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span></b></h4>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example7" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                        <thead class="bg-secondary">
                            <tr>
                                <th class="bb-2">Code Patient</th>
                                <th class="bb-2">Type</th>
                                <th class="bb-2">Motif</th>
                                <th class="bb-2">Nom du Patient</th>
                                <th class="bb-2">Montant</th>
                                <th class="bb-2 text-center">Status</th>
                                <th class="bb-2 text-center">Actions</th>
                            </tr>
                            @php
                                $hospitalIdCashier = \Illuminate\Support\Facades\Auth::user()->cashier->hospital_id;
                                $todaySuccessPayments = App\Models\Payment::where('status', 'success')
                                    ->where('hospital_id', $hospitalIdCashier)
                                    ->whereDate('created_at', date('Y-m-d'))
                                    ->with(['admission.patient.user', 'admission.prestationHospital.prestationService', 'hospitalisation.consultation.patient.user'])
                                    ->get();
                            @endphp
                            @foreach ($todaySuccessPayments as $item)
                                <tr>
                                    <td>
                                        @if ($item->type == 'hospitalisation')
                                            <b>{{ optional(optional(optional($item->hospitalisation)->consultation)->patient)->code_patient }}</b>
                                        @elseif ($item->type == 'admission')
                                            <b>{{ optional(optional($item->admission)->patient)->code_patient }}</b>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->mode_paiement == 'gtc')
                                            <span class="badge bg-success-light text-success fw-bold">GTC (Gratuité)</span>
                                        @else
                                            {{ ucfirst($item->type) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->type == 'hospitalisation')
                                            Hospitalisation
                                        @elseif ($item->type == 'admission')
                                            {{ optional(optional(optional($item->admission)->prestationHospital)->prestationService)->libelle ?? 'Prestation Médicale' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->type == 'hospitalisation')
                                            <i>{{ optional(optional(optional(optional($item->hospitalisation)->consultation)->patient)->user)->name }}&nbsp;
                                                {{ optional(optional(optional(optional($item->hospitalisation)->consultation)->patient)->user)->prenom }}</i>
                                        @elseif ($item->type == 'admission')
                                            <i>{{ optional(optional(optional($item->admission)->patient)->user)->name }}&nbsp;
                                                {{ optional(optional(optional($item->admission)->patient)->user)->prenom }}</i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->mode_paiement == 'gtc' || ($item->admission && $item->admission->mode_paiement == 'gtc'))
                                            <span class="text-success fw-bold">0 FCFA</span>
                                            <small class="text-muted d-block fs-11">(Prise en charge GTC: {{ number_format($item->prix_normal ?? 0, 0, ',', ' ') }} F)</small>
                                        @else
                                            <b>{{ number_format($item->prix ?? 0, 0, ',', ' ') }} FCFA</b>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="p-1 badge-success ">
                                            Succès
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->status == 'success')
                                             <a href="{{ route('cashier.payment.imprimer', $item->id) }}"
                                                    class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                                    data-bs-placement="bottom" title="Imprimer">
                                                    <i class="fa-solid fa-print"></i>
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
{{-- <div class="row pt-35">
    <div class="" style="width: 50%;">
        <canvas id="admission"></canvas>
    </div>
</div>
 --}}

<script>
    // Auto-actualisation du tableau de bord caissier toutes les 10 secondes
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
                    const newEl = doc.getElementById('cashierTodayPaymentsBox');
                    const targetEl = document.getElementById('cashierTodayPaymentsBox');
                    if (newEl && targetEl) {
                        targetEl.innerHTML = newEl.innerHTML;
                    }
                })
                .catch(err => console.warn('Auto-refresh dashboard caissier :', err));
            }
        }, 10000);
    })();
</script>
