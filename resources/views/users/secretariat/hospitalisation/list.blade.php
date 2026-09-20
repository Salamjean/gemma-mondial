@extends('layouts.dashboard', ['title' => $pageTitle ?? "Liste des patients en Hospitalisation"])

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h4 class="box-title fw-bold text-primary mb-0">
                                <i class="fa-solid fa-bed me-2 text-danger"></i>
                                {{ $pageTitle ?? 'Hospitalisations' }}
                            </h4>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <ul class="nav nav-pills custom-pills">
                                <li class="nav-item">
                                    <a class="nav-link {{ ($activeTab ?? 'in_progress') === 'in_progress' ? 'active' : '' }}" 
                                       href="{{ route('secretariat.hospitalisation.in_progress') }}">
                                        <i class="fa-solid fa-user-clock me-1"></i> En cours
                                        <span class="badge {{ ($activeTab ?? 'in_progress') === 'in_progress' ? 'bg-white text-primary' : 'bg-primary text-white' }} ms-1">
                                            {{ $inProgressCount ?? 0 }}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ ($activeTab ?? '') === 'history' ? 'active' : '' }}" 
                                       href="{{ route('secretariat.hospitalisation.history') }}">
                                        <i class="fa-solid fa-clock-rotate-left me-1"></i> Historique
                                        <span class="badge {{ ($activeTab ?? '') === 'history' ? 'bg-white text-primary' : 'bg-secondary text-white' }} ms-1">
                                            {{ $historyCount ?? 0 }}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                            <thead class="bg-primary">
                                <tr>
                                    <th class="bb-2">N° Dossier</th>
                                    <th class="bb-2">Photo</th>
                                    <th class="bb-2">Nom & Prénom(s)</th>
                                    <th class="bb-2">Chambre & Lit</th>
                                    <th class="bb-2 text-center">Date début</th>
                                    @if (($activeTab ?? '') === 'history')
                                        <th class="bb-2 text-center">Date fin</th>
                                    @endif
                                    <th class="bb-2 text-center">Durée</th>
                                    <th class="bb-2">Médecin</th>
                                    <th class="bb-2 text-center">Statut</th>
                                    <th class="bb-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hospitalisations as $item)
                                    @php
                                        $patient = $item->consultation->patient ?? ($item->consultation->admission->patient ?? null);
                                        $patientUser = $patient ? $patient->user : null;
                                        $doctorUser = $item->doctor->user ?? ($item->consultation->doctor->user ?? null);

                                        $lastDay = $item->daysHospitalisation ? $item->daysHospitalisation->sortByDesc('id')->first() : null;
                                        $bed = $lastDay ? $lastDay->bed : null;
                                        $bedroom = $bed ? $bed->bedroom : null;
                                        $hasBed = $item->daysHospitalisation && $item->daysHospitalisation->whereNotNull('bed_id')->count() > 0;

                                        $startDate = $item->date ?? $item->created_at;
                                        $nbJours = $startDate ? \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::now()) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <b class="text-dark">{{ $patient->code_patient ?? 'N/A' }}</b>
                                        </td>
                                        <td>
                                            @if ($patient && $patient->img_url != null)
                                                <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}"
                                                    class="rounded-circle" alt="Photo de profil" style="width:40px; height:40px; object-fit: cover;" />
                                            @else
                                                @if ($patient && $patient->gender == 'masculin')
                                                    <img src="{{ asset('assets/images/avatar/6.png') }}"
                                                        class="avatar avatar-md rounded10" alt="Photo de profil" />
                                                @else
                                                    <img src="{{ asset('assets/images/avatar/2.png') }}"
                                                        class="avatar avatar-md rounded10" alt="Photo de profil" />
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $patientUser->name ?? '-' }} {{ $patientUser->prenom ?? '' }}</div>
                                            <small class="text-muted">{{ $patient->telephone ?? '' }}</small>
                                        </td>
                                        <td>
                                            @if ($bed && $bedroom)
                                                <span class="badge badge-info-light fs-12">
                                                    <i class="fa-solid fa-door-open me-1"></i> Ch. {{ $bedroom->name ?? $bedroom->number ?? 'N/A' }} - Lit {{ $bed->name ?? $bed->number ?? 'N/A' }}
                                                </span>
                                            @elseif ($item->status == 'in_progress')
                                                <span class="badge badge-warning-light fs-11">
                                                    <i class="fa-solid fa-hourglass-half me-1"></i> En attente d'affectation
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ dateNumberFr($item->created_at) }}
                                        </td>
                                        @if (($activeTab ?? '') === 'history')
                                            <td class="text-center">
                                                {{ $item->end_date ? dateNumberFr($item->end_date) : '-' }}
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            @if ($item->status != 'in_progress' && $item->end_date)
                                                <span class="badge badge-secondary p-1">
                                                    {{ $item->number_days > 0 ? $item->number_days : (\Carbon\Carbon::parse($startDate)->diffInDays($item->end_date) ?: 1) }} jour(s)
                                                </span>
                                            @else
                                                <span class="badge badge-primary-light p-1">
                                                    {{ $nbJours }} jour(s)
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($doctorUser)
                                                <i class="fa-solid fa-user-doctor text-primary me-1"></i>
                                                Dr. {{ $doctorUser->name }} {{ $doctorUser->prenom }}
                                            @else
                                                <span class="text-muted">Non assigné</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->status == 'in_progress')
                                                @if (!$hasBed)
                                                    <span class="badge badge-danger p-5"><i class="fa-solid fa-clock me-1"></i>Attente chambre</span>
                                                @else
                                                    <span class="badge badge-warning p-5"><i class="fa-solid fa-bed me-1"></i>En cours</span>
                                                @endif
                                            @else
                                                <span class="badge badge-success p-5"><i class="fa-solid fa-check me-1"></i>Terminée</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                @if ($patient)
                                                    <a href="{{ route('secretariat.patient.detail', $patient->id) }}"
                                                        class="btn btn-primary btn-sm shadow-sm" title="Voir la fiche du patient">
                                                        <i class="fa-solid fa-id-card me-1"></i> Fiche Patient
                                                    </a>
                                                @endif

                                                @if ($item->consultation_id)
                                                    <a href="{{ route('secretariat.patient.parcours', $item->consultation_id) }}"
                                                        class="btn btn-info btn-sm shadow-sm" title="Parcours de soins">
                                                        <i class="fa-solid fa-timeline me-1"></i> Parcours
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
