@php
    $status = '';
    if ($hospitalisation->status == 'in_progress') {
        $status = ' - <span class="btn-primary">En cours</span>';
    } else {
        $status = ' - <span style="background-color: red;" class="btn-success">Terminée</span>';
    }

    $cleanedStatus = strip_tags($status);

@endphp

@extends('layouts.dashboard', ['title' => 'Hospitalisation n° ' . $hospitalisation->id . $cleanedStatus])

@section('content')
    <div class="container-fuild card p-1">
        <div class=" card-body">
            @php
                $diff = Carbon\Carbon::parse($hospitalisation->created_at)->diffInDays(Carbon\Carbon::now()) + 1;
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
            <div class="row">
                <div class="col-md-12">
                    <div class="px-2">
                        <div class="px-5 bg-color">

                            <div class="d-flex justify-content-between mt-10">
                                <div class="">
                                    <label class="form-label">N° Dossier médical | <span class="fw-bold fs-18"><span
                                                id="dm_patient"
                                                style="color:red;">{{ $patient->code_patient ?? 'N/A' }}</span></span></label>
                                </div>
                                <div class="d-flex items-center pb-1">

                                    <span class=" d-flex mt-1 text-success">
                                        <pre>  </pre> <span> N°{{ $hospitalisation->code }}
                                        </span>
                                        <pre>  </pre>
                                    </span>
                                    <a href="{{ route('doctor.hospitalisation.history') }}"
                                        title="Retour à la liste" class="btn btn-sm btn-secondary mx-1">
                                        <i class="fa-solid fa-arrow-left me-1"></i> Retour
                                    </a>
                                    <a href="{{ route('doctor.hospitalisation.download_facture', $hospitalisation->id) }}"
                                        title="Télécharger la facture PDF" class="btn btn-sm btn-danger mx-1">
                                        <i class="fa-solid fa-file-pdf me-1"></i> Télécharger Facture PDF
                                    </a>
                                    @if ($patient)
                                        <a href="{{ route('doctor.patient.detail', $patient->id) }}"
                                            title="info patient" class="btn btn-sm btn-success mx-1"><i
                                                class="fa-solid fa-info"></i></a>
                                    @endif
                                </div>
                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-md-3">
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
                                        <input type="text" class="form-control" id="birth_date" name="birth_date"
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
                                    <div class="form-group">
                                        <label for="gender" class="form-label"> <b>Sexe </b></label>
                                        <input type="text" class="form-control" id="gender" name="gender"
                                            value="{{ $patient->gender ?? 'N/A' }}"
                                            disabled>
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
        <div class="card-body">
            <div class="row">
                <div class="col-3">
                    <nav id="navbar-example3" class="h-100 flex-column align-items-stretch pe-4 border-end">
                        <nav class="nav nav-pills flex-column">
                            @foreach ($hospitalisation->daysHospitalisation as $key => $day)
                                <a class="" href="#{{ $day->id . $key }}">&bull; {{ dateNumberFr($day->day) }}</a>
                                @if (count($day->therapeutiqueProtocols) > 0 || count($day->surveillances) > 0)
                                    <nav class="nav nav-pills flex-column mb-5">
                                        @if (count($day->therapeutiqueProtocols) > 0)
                                            <a class="nav-link ms-3 my-1" href="#pt{{ $day->id . $key }}">Protocole
                                                Thérapeutique</a>
                                        @endif
                                        @if (count($day->surveillances) > 0)
                                            <a class="nav-link ms-3 my-1" href="#s{{ $day->id . $key }}">Surveillances</a>
                                        @endif
                                    </nav>
                                @endif
                            @endforeach
                        </nav>
                    </nav>
                </div>

                <div class="col-9">
                    <div data-bs-spy="scroll" data-bs-target="#navbar-example3" data-bs-smooth-scroll="true"
                        class="scrollspy-example-2" tabindex="0">
                        @foreach ($hospitalisation->daysHospitalisation as $key => $day)
                            <div id="{{ $day->id . $key }}" class="card-body border my-2 "
                                style="background-color: rgba(1, 86, 107, 0.075); text-align:center;">
                                <h4>{{ dateCompletFr($day->day) }} |
                                    @if (\Carbon\Carbon::createFromFormat('Y-m-d', $day->day)->diffInDays($day->end_date) == 0)
                                        @if ($hospitalisation->status != 'in_progress')
                                            1 jours
                                        @else
                                            <span style="color:green;">En cours</span>
                                        @endif
                                    @else
                                        {{ $day->number_days }} jours
                                    @endif

                                </h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <th>
                                                    Médecin en charge | <span
                                                        class="text-info">{{ $day->doctor->user->name }}</span>
                                                </th>
                                                <th>
                                                    Infirmier(e) en charge | <span
                                                        class="text-warning">{{ $day->infirmier->user->name }}</span>
                                                </th>
                                                <th>
                                                    Chambre <span class="text-success">{{ $day->bed->bedroom->number }}
                                                    </span>- Lit n° <span class="text-success">{{ $day->bed->number }}</span><br> Montant/Jr |
                                                    <span class="text-success">{{ $day->bed->price }} FCFA</span>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if (count($day->therapeutiqueProtocols) > 0 || count($day->surveillances) > 0)
                                    <p>
                                        @if (count($day->therapeutiqueProtocols) > 0)
                                            <div class="card-body" id="pt{{ $day->id . $key }}">
                                                <h4>Protocoles thérapeutiques</h4>

                                                <div class="">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
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
                                            </div>
                                        @endif

                                        @if (count($day->surveillances) > 0)
                                            <div class="card-body table-responsive" id="s{{ $day->id . $key }}">
                                                <h4>Surveillances</h4>
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
                                    </p>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
