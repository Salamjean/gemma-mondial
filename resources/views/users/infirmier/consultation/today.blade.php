@extends('layouts.dashboard', ['title' => 'Liste des consultations'])

@section('content')
    <div class="box">
        <div class="box-header with-border d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h4 class="box-title mb-0"><b>VOS CONSULTATIONS DU JOUR</b></h4>
            </div>
            <div>
                @php
                    $infirmierId = optional(\Illuminate\Support\Facades\Auth::user()->infirmier)->id;
                    $countAllInfConsultations = \App\Models\Consultation::where(function ($q) use ($infirmierId) {
                        if ($infirmierId) {
                            $q->where('infirmier_id', $infirmierId)
                              ->orWhereNull('infirmier_id');
                        } else {
                            $q->whereNull('infirmier_id');
                        }
                    })->where('status_inf', 0)->count();
                @endphp
                <a href="{{ route('infirmier.consultation.all') }}" class="btn btn-sm btn-primary rounded-10 fw-bold shadow-sm">
                    <i class="fa-solid fa-users me-1"></i> Tous les patients (Sans filtre)
                    <span class="badge bg-white text-primary ms-1 fs-12">{{ $countAllInfConsultations }}</span>
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
                            <tr>
                                <td><b>{{ $item->patient->code_patient }}</b></td>
                                <td>
                                    {{ $item->patient->user->name }}&nbsp; {{ $item->patient->user->prenom }}
                                </td>
                                <td class="" style="width: 200px;"> {{ $item->admission->motif_consultation}} </td>

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
                                        <a href="{{ route('infirmier.consultation.formulaire', $item->id) }}"
                                            class="btn btn-sm btn-info" id="menu"title="Menu">
                                            <span class="">Commencer la consultation</span>
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
    <script>
        (function($) {
            "use strict";

            $('#menu').on('click', function(e) {




            });




        })(jQuery);
    </script>
@endsection
