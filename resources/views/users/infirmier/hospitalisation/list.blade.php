@extends('layouts.dashboard', ['title' => $pageTitle ?? "Liste des patients en Hospitalisation"])

@section('content')
    <div class="box">
        <div class="box-header with-border">
            <h4 class="box-title fw-bold text-primary">{{ $pageTitle ?? 'Liste des patients en Hospitalisation' }}</h4>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                    <thead class="bg-danger">
                        <tr>
                            <th class="bb-2">N° Dossier médical</th>
                            <th class="bb-2">Nom & Prénoms</th>
                            <th class="bb-2">Issue consultation</th>
                            <th class="bb-2">Date début</th>
                            @if (count($hospitalisations) != 0 && $hospitalisations[0]->status != 'in_progress')
                                <th class="bb-2">Date fin</th>
                            @endif
                            <th class="bb-2">Nombre de jours</th>
                            <th class="bb-2">Status</th>
                            <th class="bb-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hospitalisations as $item)
                            @php
                                $hasBed = $item->daysHospitalisation && $item->daysHospitalisation->whereNotNull('bed_id')->count() > 0;
                                $startDate = $item->date ?? $item->created_at;
                                $nbJours = $startDate ? \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::now()) : 0;
                                $issue = $item->consultation->registre->issue_consultation ?? 'Hospitalisation';
                            @endphp
                            <tr>
                                <td><b>{{ $item->consultation->patient->code_patient ?? ($item->consultation->admission->patient->code_patient ?? 'N/A') }}</b></td>
                                <td class="text-center">
                                    {{ $item->consultation->patient->user->name ?? ($item->consultation->admission->patient->user->name ?? '') }}
                                    {{ $item->consultation->patient->user->prenom ?? ($item->consultation->admission->patient->user->prenom ?? '') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info p-5">{{ $issue }}</span>
                                </td>
                                <td class="text-center">
                                    {{ dateNumberFr($item->created_at) }}
                                </td>
                                @if (count($hospitalisations) != 0 && $item->status != 'in_progress')
                                    <td class="text-center">
                                        {{ dateNumberFr($item->end_date) }}
                                    </td>
                                @endif
                                <td class="text-center">
                                    @if ($item->status != 'in_progress' && $item->end_date)
                                        {{ $item->number_days > 0 ? $item->number_days : (\Carbon\Carbon::parse($startDate)->diffInDays($item->end_date) ?: 1) }}
                                    @else
                                        {{ $nbJours }}
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ($item->status == 'in_progress')
                                        @if (!$hasBed)
                                            <span class="badge badge-danger p-5"><i class="fa-solid fa-clock me-1"></i>En attente de chambre</span>
                                        @else
                                            <span class="badge badge-warning p-5">En cours</span>
                                        @endif
                                    @else
                                        <span class="badge badge-primary p-5">Terminé</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('infirmier.suivi.hospitalisation', $item->id) }}"
                                        class="btn btn-primary btn-sm me-1" title="Fiche de suivi et surveillance">
                                        <i class="fa-solid fa-notes-medical me-1"></i> Suivi & Surveillance
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
