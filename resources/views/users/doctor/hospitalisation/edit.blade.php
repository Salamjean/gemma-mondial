@extends('layouts.dashboard', ['title' => 'Suivie de mise en hospitalisation du patient ' . $hospitalisation->consultation->patient->code_patient])

@section('content')
    <main id="main" class="main">
        <section class="section profile">
            <div class="row">

                <div class="col-xl-12">
                    <div class="my-3 card">
                        @php
                            $diff = Carbon\Carbon::parse($hospitalisation->created_at)->diffInDays(Carbon\Carbon::now());
                        @endphp
                        <div class="row">
                            <div class="col-md-12">
                                @php
                                    $patient = $hospitalisation->consultation->patient ?? ($hospitalisation->consultation->admission->patient ?? null);
                                    $age = 'N/A';
                                    if ($patient && $patient->birth_date) {
                                        try {
                                            $agePatient = \Carbon\Carbon::createFromFormat('d/m/Y', $patient->birth_date);
                                            $age = $agePatient->diffInYears(\Carbon\Carbon::now()) . ' ans';
                                        } catch (\Exception $e) {
                                            $age = 'N/A';
                                        }
                                    }
                                @endphp
                                <div class="px-2">
                                    <div class="px-5 bg-color">

                                        <div class="d-flex justify-content-between mt-10">
                                            <div class="">
                                                <label class="form-label"><b>N° Dossier médical&nbsp;&nbsp;|&nbsp;&nbsp;
                                                    </b><span class="fw-bold fs-18"><span id="dm_patient"
                                                            style="color:red;">{{ $patient->code_patient ?? 'N/A' }}</span></span></label>
                                            </div>
                                            <div class="d-flex items-center pb-1">
                                                <a href="{{ route('doctor.hospitalisation.in_progress') }}" class="btn btn-sm btn-secondary mx-1" title="Retour à la liste">
                                                    <i class="fa-solid fa-arrow-left me-1"></i> Retour
                                                </a>
                                                <span class=" d-flex mt-1 text-success">
                                                    <pre>  </pre> <span> N°{{ $hospitalisation->code }}
                                                    </span>
                                                    <pre>  </pre>
                                                </span>
                                                <a href="" title="dossier medical"
                                                    class="btn btn-sm  btn-secondary mx-1" target="_blank"><i
                                                        class="fa-solid fa-print"></i></a>
                                                @if ($patient)
                                                    <a href="{{ route('doctor.patient.detail', $patient->id) }}"
                                                        title="info patient" class="btn btn-sm  btn-success mx-1"
                                                        target="_blank"><i class="fa-solid fa-info"></i></a>
                                                @endif
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="d-flex justify-content-end py-2">
                                            <a href="{{ route('doctor.hospitalisation.in_progress') }}"
                                                class="btn btn-secondary-light mx-2">
                                                <i class="fa-solid fa-arrow-left me-1"></i> Retour
                                            </a>
                                            @if (count($hospitalisation->consultation->ordonnances) > 0)
                                                <a href="{{ route('doctor.hospitalisation.ordonnances', $hospitalisation->id) }}"
                                                    class="col-md-2 mx-2 capitalize btn btn-warning-light">
                                                    Ordonnances
                                                </a>
                                            @endif
                                            @if ($hospitalisation->status == 'in_progress')
                                                <a href="{{ route('doctor.hospitalisation.edit.protocol', ['interne', $hospitalisation->id]) }}"
                                                    class="col-md-1 mx-2 capitalize btn btn-success-light">
                                                    Interne <i class="fa-solid fa-plus"></i>
                                                </a>


                                                <a href="{{ route('doctor.hospitalisation.edit.protocol', ['externe', $hospitalisation->id]) }}"
                                                    class="col-md-1 mx-2 capitalize btn btn-danger-light">
                                                    Externe <i class="fa-solid fa-plus"></i>
                                                </a>

                                                <div class="col-md-1 mx-2">
                                                    <button type="button" class="capitalize btn btn-primary-light"
                                                        data-bs-toggle="modal" data-bs-target="#motSortieModal"
                                                        class="btn btn-success">Terminée</button>
                                                </div>
                                            @endif
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="form-label"><b>Code</b></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $hospitalisation->code }}" readonly />
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label"><b>Nbre de jours</b></label>
                                                <input type="text" class="form-control" value="{{ $diff }} jours"
                                                    readonly />
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="name" class="form-label"> <b>Nom complet </b>
                                                    </label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        value="{{ ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '') }}"
                                                        disabled>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="birth_date" class="form-label"> <b>Né(e) le</b></label>
                                                    <input type="text" class="form-control" id="birth_date"
                                                        name="birth_date"
                                                        value="{{ $patient->birth_date ?? 'N/A' }}"
                                                        disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    <label for="age" class="form-label"> <b>Age </b></label>
                                                    <input type="text" class="form-control" id="age" name="age"
                                                        value="{{ $age }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><b>Résidence Actuelle</b></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $patient->residenceActuelle->name ?? 'N/A' }}"
                                                    readonly />
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label"><b>Contact</b></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $patient->telephone ?? '' }}"
                                                    readonly />
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body pt-3">


                            @if (count($hospitalisation->daysHospitalisation) == 0)
                                <div class="alert alert-warning p-20 text-center my-4">
                                    <h4 class="fw-bold mb-2"><i class="fa-solid fa-bed me-2"></i>Patient en attente d'attribution de chambre</h4>
                                    <p class="fs-16 mb-3">Aucune chambre ni protocole n'ont encore été attribués à cette hospitalisation.</p>
                                    <div>
                                        <a href="{{ route('doctor.hospitalisation.edit.protocol', ['interne', $hospitalisation->id]) }}" class="btn btn-primary mx-1">
                                            <i class="fa-solid fa-plus me-1"></i> Attribuer une chambre & Protocole Interne
                                        </a>
                                        <a href="{{ route('doctor.hospitalisation.edit.protocol', ['externe', $hospitalisation->id]) }}" class="btn btn-danger mx-1">
                                            <i class="fa-solid fa-plus me-1"></i> Attribuer une chambre & Protocole Externe
                                        </a>
                                    </div>
                                </div>
                            @else
                                <ul class="nav nav-tabs nav-tabs-bordered">

                                    @foreach ($hospitalisation->daysHospitalisation as $key => $day)
                                        <li class="nav-item">
                                            <button class="nav-link capitalize  {{ $key == 0 ? 'active' : '' }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#day{{ $key }}">{{ dateCompletFr($day->day) }}</button>
                                        </li>
                                    @endforeach

                                </ul>
                                <div class="tab-content pt-2">

                                    @foreach ($hospitalisation->daysHospitalisation as $key => $day)
                                    <div class="tab-pane fade show {{ $key == 0 ? 'active' : '' }} day{{ $key }}"
                                        id="day{{ $key }}">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <th>
                                                            Médecin en charge |&nbsp;&nbsp; <span
                                                                class="text-info">{{ $day->doctor->user->name }}</span>
                                                        </th>
                                                        <th>
                                                            Infirmier(e) en charge |&nbsp;&nbsp; <span
                                                                class="text-warning">{{ $day->infirmier->user->name }}</span>
                                                        </th>
                                                        <th>
                                                            Chambre <span
                                                                class="text-success">{{ $day->bed->bedroom->number }}
                                                            </span> - Lit n° <span
                                                                class="text-success">{{ $day->bed->number }}</span>
                                                        </th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Heure d&apos;ajout</th>
                                                        <th>Nom</th>
                                                        <th>Type</th>
                                                        <th style="width:20px;">Qte</th>
                                                        <th>Dosage</th>
                                                        <th style="width:20px;">Voie Admis</th>
                                                        <th>Heures d'application</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($day->therapeutiqueProtocols as $keyy => $therap)
                                                        <tr>
                                                            <td>
                                                                {{ heureFr($therap->created_at) }}
                                                            </td>
                                                            <td>
                                                                @if ($therap->protocol_type == 'internal')
                                                                    {{ $therap->drugHospital->drug->name }}
                                                                @else
                                                                    {{ $therap->drug->name }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $therap->protocol_type }}
                                                            </td>
                                                            <td class="text-center">{{ $therap->quantity }} </td>
                                                            <td>{{ $therap->dosage }} </td>
                                                            <td>{{ $therap->voie_admission }} </td>
                                                            <td>
                                                                <div class="row">
                                                                    @foreach ($therap->protocolHourApplications as $hour)
                                                                        <div class="col-md-6">
                                                                            <table
                                                                                class="table table-bordered  table-hover">
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="text-center">
                                                                                            {{ substr($hour->hour, 0, 5) }}
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            @if ($hour->status === 'pending')
                                                                                                <span
                                                                                                    class="text-warning">En
                                                                                                    attente</span>
                                                                                            @else
                                                                                                <span
                                                                                                    class="text-success">Appliqué</span>
                                                                                            @endif
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if (count($day->surveillances) != 0)
                                            <div class="d-flex justify-content-between">
                                                <h3>Surveillances</h3>

                                            </div>
                                            <div class="table-responsive">
                                                <table class="table b-1 border-success">
                                                    <thead class="bg-success">
                                                        <tr>
                                                            <th></th>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <th>{{ 'H' . $loop->iteration }} </th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>TA(mmHg)</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->ta }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>T°(°C)</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->t }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>POULS(bpm)</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->pouls }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>DIERESE(ml)</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->diurese }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>CONSCIENCE</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->conscience }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>GLYCEMIE(g/dl)</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->glycemie }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>SaO2</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->sao2 }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>Poids(kg)</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td>{{ $surveillance->poids }}</td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td>Heure</td>
                                                            @foreach ($day->surveillances as $surveillance)
                                                                <td><span class="badge badge-success"><i
                                                                            class="fa fa-clock"></i>
                                                                        {{ $surveillance->hour }}</span></td>
                                                            @endforeach
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                    </div>
                                @endforeach

                            </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <div class="modal fade" id="motSortieModal" tabindex="-1" aria-labelledby="motSortieModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('doctor.hospitalisation.validate', $hospitalisation->id) }}" method="get">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="motSortieModalLabel">Hospitalisation à son terme.</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Entrez votre mot de sortie" name="mot_sortie" id="mot_sortie"
                                    style="height: 100px"></textarea>
                                <label for="floatingTextarea2">Votre mot de sortie</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Valider</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </main>
@endsection
