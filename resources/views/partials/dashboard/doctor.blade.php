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
                                <th class="bb-2">Motif de la visite</th>
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

                                    <td style="width: 300px;">
                                        <span class="badge text-dark fw-900 fs-14">{{ optional($item->admission)->motif_consultation ?? 'Consultation en ligne' }}</span>
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
