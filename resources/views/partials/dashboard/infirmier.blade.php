<div class="row">
    @if (\Illuminate\Support\Facades\Auth::user()->infirmier->serviceHospital->service->id == 5)
        <div class="col-xl-12 col-lg-12 col-12">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-12">
                    <div class="box bg-success-light">
                        <div class="box-body text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="p-5 w-100 h-100">
                                    <img src="{{ asset('assets/icons/admission_history.png') }}" class=""
                                        alt="icon">
                                </div>
                                <div class="text-end">
                                    <h2 class="mb-0 fw-600 text-success">
                                        {{ $count['pending_count'] }}
                                    </h2>

                                    <p class="text-fade mt-5 mb-0 text-success">Soins en attentes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-12">
                    <div class="box bg-success-light">
                        <div class="box-body text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="p-5 w-100 h-100">
                                    <img src="{{ asset('assets/icons/admission_valid.png') }}" class=""
                                        alt="aa">
                                </div>
                                <div class="text-end">
                                    <h2 class="mb-0 fw-600 text-success">
                                        {{ $count['success_count'] }}
                                    </h2>
                                    <p class="text-fade mt-5 mb-0 text-success">Soins effectués</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-12">
                    <div class="box bg-success-light">
                        <div class="box-body text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="p-5 w-100 h-100">
                                    <img src="{{ asset('assets/icons/admission_money.png') }}" class=""
                                        alt="aa">
                                </div>
                                <div class="text-end">
                                    <h2 class="mb-0 fw-600 text-success">
                                        {{ $count['payment_pending_count'] }}
                                    </h2>
                                    <p class="text-fade mt-5 mb-0 text-success">Paiements en attentes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-12">
                    <div class="box bg-success-light">
                        <div class="box-body text-center">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="p-5 w-100 h-100">
                                    <img src="{{ asset('assets/icons/admission_money.png') }}" class=""
                                        alt="aa">
                                </div>
                                <div class="text-end">
                                    <h2 class="mb-0 fw-600 text-success">
                                        {{ $count['payment_success_count'] }}
                                    </h2>
                                    <p class="text-fade mt-5 mb-0 text-success">Paiements effectués</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-xl-12 col-lg-12 col-12">

                    <div class="box">
                        <div class="box-header">
                            <div class="row align-items-center">
                                <div class="col-xs-12 col-xl-8 col-lg-8 col-md-8 col-sm-8">
                                    <div class="badge badge-warning" style="font-size: 15px;">LISTE DE VOS SOINS NOUVEAUX SOINS
                                    </div>
                                </div>
                                <div class="col-xs-12 col-xl-4 col-lg-4 col-md-4 col-sm-4 text-end">
                                    @php
                                        $infirmierId = \Illuminate\Support\Facades\Auth::user()->infirmier->id;
                                        $countAllInfCares = \App\Models\CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
                                            $query->where('infirmier_id', $infirmierId);
                                        })->where('status', '!=', 'success')->count();
                                    @endphp
                                    <a href="{{ route('infirmier.care.all') }}" class="btn btn-sm btn-primary rounded-10 fw-bold shadow-sm">
                                        <i class="fa-solid fa-list-check me-1"></i> Tous les soins (Sans filtre)
                                        <span class="badge bg-white text-primary ms-1 fs-12">{{ $countAllInfCares }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="bb-2" style="width: 140px;">Code patient</th>
                                            <th class="bb-2" style="width: 200px;">Nom du Patient</th>
                                            <th class="bb-2">Acte medicale</th>
                                            <th class="bb-2">Motif</th>
                                            <th class="bb-2">Status</th>
                                            <th class="bb-2 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cares as $key => $item)
                                            @if ($item->status === 'pending')
                                                <tr>
                                                    <td>
                                                        <b>{{ $item->admission->patient->code_patient }}</b>
                                                    </td>
                                                    <td style="width: 0px;">
                                                        {{ $item->admission->patient->user->name }}&nbsp;
                                                        {{ $item->admission->patient->user->prenom }}
                                                    </td>
                                                    <td class="" style="width: 200px;">
                                                        {{ $item->admission->prestationHospital->prestationService->libelle }}
                                                    </td>
                                                    <td class="" style="width: 200px;">
                                                        {{ $item->admission->motif_consultation }} </td>

                                                    <td class="">
                                                        @if ($item->status === 'pending')
                                                            <span class="badge badge-warning">En attente</span>
                                                        @endif
                                                    </td>

                                                    <td class="text-center">

                                                        @if ($item->status === 'pending')
                                                            <a href="{{ route('infirmier.care.formulaire', $item->id) }}"
                                                                class="btn btn-sm btn-info" title="formulaire consultation">
                                                                <span class="">Commencer</span>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>



                    </div>
                </div>
                <div class="col-xl-12 col-lg-12 col-12">

                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-xs-12  col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                    <div class="badge badge-warning" style="font-size: 15px;">LISTE DE VOS SOINS EN COURS</div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <thead class="bg-info">
                                        <tr>
                                            <th class="bb-2" style="width: 140px;">Code patient</th>
                                            <th class="bb-2" style="width: 200px;">Nom du Patient</th>
                                            <th class="bb-2">Acte medicale</th>
                                            <th class="bb-2">Motif</th>
                                            <th class="bb-2">Status</th>
                                            <th class="bb-2 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cares as $key => $item)
                                            @if ($item->status !== 'pending')
                                                <tr>
                                                    <td>
                                                        <b>{{ $item->admission->patient->code_patient }}</b>
                                                    </td>
                                                    <td style="width: 0px;">
                                                        {{ $item->admission->patient->user->name }}&nbsp;
                                                        {{ $item->admission->patient->user->prenom }}
                                                    </td>
                                                    <td class="" style="width: 200px;">
                                                        {{ $item->admission->prestationHospital->prestationService->libelle }}
                                                    </td>
                                                    <td class="" style="width: 200px;">
                                                        {{ $item->admission->motif_consultation }} </td>

                                                    <td class="">
                                                        @if ($item->status === 'payment_pending')
                                                            <span class="badge badge-info">En attente de paiement</span>
                                                        @elseif ($item->status === 'payment_success')
                                                            <span class="badge badge-success">Paiment effectué</span>
                                                        @endif
                                                    </td>

                                                    <td class="text-center">

                                                        @if ($item->status === 'payment_pending')
                                                            <a href="{{ route('infirmier.care.detail', $item->id) }}" class="btn btn-sm btn-info"
                                                                title="formulaire consultation">
                                                                <span class="">Détail</span>
                                                            </a>
                                                        @elseif ($item->status === 'payment_success')
                                                            <a href="{{ route('infirmier.care.payment_success', $item->id) }}" class="btn btn-sm btn-info"
                                                                title="formulaire consultation">
                                                                <span class="">Continuer</span>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-xl-12 col-lg-12 col-12">
            <div class="box" style="background: rgba(9, 187, 134, 0.288); padding : 10px 20px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="badge badge-dark" style="font-size: 20px;">PATIENTS A CONSULTER POUR CE JOUR</div>
                    @php
                        $infirmierId = \Illuminate\Support\Facades\Auth::user()->infirmier->id;
                        $countAllInfConsultations = \App\Models\Consultation::where(function ($q) use ($infirmierId) {
                            $q->where('infirmier_id', $infirmierId)
                              ->orWhereNull('infirmier_id');
                        })->where('status_inf', 0)->count();
                    @endphp
                    <a href="{{ route('infirmier.consultation.all') }}" class="btn btn-sm btn-primary fw-bold shadow-sm">
                        <i class="fa-solid fa-users me-1"></i> Toutes les consultations
                        <span class="badge bg-white text-primary ms-1 fs-12">{{ $countAllInfConsultations }}</span>
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
                                @foreach ($data as $item)
                                    <tr>
                                        <td style="text-align: left;">{{ $item->created_at->format('H:i:s') }}</td>
                                        <td style="text-align: center;"><b>{{ $item->code_consultation }}</b></td>
                                        <td style="text-align: center;"><span
                                                class="fw-bold">{{ $item->patient->code_patient }}</span></td>
                                        <td style="text-align: center; text-transform: capitalize; width: 300px;">
                                            <span
                                                class="badge text-dark fw-900 fs-14">{{ $item->patient->user->name }}
                                                {{ $item->patient->user->prenom }}</span>
                                        </td>
                                        <td style="text-align: center;"><span
                                                class="badge badge-primary">{{ $item->prestationHospital->prestationService->libelle }}</span>
                                        </td>

                                        <td style="width: 300px;"> <span
                                                class="badge text-dark fw-900 fs-14">{{ $item->admission->motif_consultation }}</span>
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ route('infirmier.consultation.formulaire', $item->id) }}"
                                                class="btn btn-sm" style="background: rgba(214, 110, 62, 0.452);"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Faire la consultation">
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
        <div class="row">
            <div class="col-md-9">
                <div class="col-xl-12 col-lg-12 col-12">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-xs-12  col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                    <div class="badge badge-primary" style="font-size: 20px;">LISTE DE VOS CONSULTATIONS</div>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                                    <thead>
                                        <tr class="bg-primary">
                                            <th class="bb-2">Date et heure</th>
                                            <th class="bb-2">Reference</th>
                                            <th class="bb-2">Motif</th>
                                            <th class="bb-2">Statut</th>
                                            <th class="bb-2 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (\App\Models\Consultation::orderByDESC('created_at')->withCount('ordonnances', 'arret', 'examen')->where('infirmier_id', \Illuminate\Support\Facades\Auth::user()->infirmier->id)->where('status_inf', 1)->get() as $item)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($item->date_consultation)->format('d/m/Y') }} -
                                                    {{ $item->created_at->format('H:i:s') }}
                                                </td>
                                                <td><b>{{ $item->patient->code_patient }}</b></td>

                                                <td class="" style="width: 200px;">
                                                    {{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? ($item->prestationHospital->serviceHospital->service->libelle ?? 'Consultation') }} </td>

                                                <td class="">
                                                    @if ($item->status_inf == 0)
                                                        <span class="badge badge-warning">En attente</span>
                                                    @else
                                                        <span class="badge badge-success">Terminée</span> <br>
                                                        <div style="padding-top: 5px;">
                                                            @if ($item->ordonnances_count > 0)
                                                                @foreach ($item->ordonnances as $ordonnan)
                                                                    <a target="_blank"
                                                                        href="{{ route('impression', ['ordonnance', $ordonnan->id]) }}"><span
                                                                            class="badge badge-warning">Ordonnance
                                                                            {{ $ordonnan->type }}</span></a>
                                                                @endforeach
                                                            @endif
                                                            @if ($item->arret_count > 0)
                                                                <a style="padding-bottom: 5px;" target="_blank"
                                                                    href="{{ route('consultation.imprimer.post', ['arret', $item->arret->id]) }}"><span
                                                                        class="badge badge-primary">Arret de travail</span></a>
                                                            @endif
                                                            @if ($item->examen_count > 0)
                                                                <a target="_blank"
                                                                    href="{{ route('consultation.imprimer.post', ['examen', $item->examen->id]) }}"><span
                                                                        class="badge badge-secondary">Bulletin d'examen</span></a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->status == 0)
                                                        <a href="{{ route('doctor.consultation.info', $item->id) }}"
                                                            class="btn btn-sm btn-info" title="info">
                                                            <span class="">Info patient</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('infirmier.consultation.detail', $item->id) }}"
                                                            class="btn btn-sm btn-info" title="detail consultation">
                                                            <span class="">Détail</span>
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
            <div class="col-md-3">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title"><i class="fa-solid fa-bed-pulse"></i> Patient(s) Hospitalisé(s)</h4>
                        <h4 class="mb-0 pull-right box-title"><i class="fa-solid fa-user-nurse"></i> Suivi</h4>
                    </div>
                    <div class="box-body">
                        <div class="slimScrollDiv" style="position: relative; overflow: auto; width: auto; height: 235px;">
                            <div class="inner-user-div3" style="overflow: auto; width: auto; height: 335px;">
                                @foreach (\App\Models\DayHospitalisation::orderByDESC('created_at')->where('infirmier_id', \Illuminate\Support\Facades\Auth::user()->infirmier->id)->where('status', 'en_cours')->whereHas('hospitalisation', function($q) { $q->where('status', 'in_progress'); })->get() as $item)
                                <div class="d-flex align-items-center mb-10">
                                    <div class="me-15">
                                        @if ($item->hospitalisation->consultation->patient->img_url != null)
                                            <img src="{{ asset('assets/uploads/patient/'. $item->hospitalisation->consultation->patient->img_url) }}"
                                                class="avatar avatar-lg rounded10 bg-primary-light" alt="Photo de profil" style="width:128px; height:128px" />
                                        @else
                                            @if ($item->hospitalisation->consultation->patient->gender == 'masculin')
                                            <img src="{{ asset('assets/images/avatar/6.png') }}" class="avatar avatar-lg rounded10 bg-primary-light"
                                                alt="Photo de profil" />
                                            @else
                                                <img src="{{ asset('assets/images/avatar/2.png') }}" class="avatar avatar-lg rounded10 bg-primary-light"
                                                    alt="Photo de profil" />
                                            @endif
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1 fw-500">
                                        <a href="#" class="text-dark hover-primary mb-1 fs-16">{{ $item->hospitalisation->consultation->patient->user->name }} {{ $item->hospitalisation->consultation->patient->user->prenom }}</a>
                                        <span class="text-fade">{{ $item->doctor->user->name }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('infirmier.suivi.hospitalisation',['id' => $item->hospitalisation->id]) }}" class="waves-effect waves-circle btn btn-circle btn-primary-light btn-sm"><i class="fa-solid fa-stethoscope"></i></a>
                                        <br/>
                                        <span class="badge badge-warning mt-5">{{ $item->status }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-end mb-5 py-0 bb-dashed border-bottom">
                                    <p class="mb-0 text-muted"><i class="fa fa-clock-o me-5"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} <span class="mx-10">{{ $item->price }} FCFA</span> ||  <span class="badge badge-info-light">{{ $item->hospitalisation->type }}</span></p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
