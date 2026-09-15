@extends('layouts.dashboard', ['title' => 'Historique de consultations'])

@section('content')
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-xs-12  col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <h4 class="box-title">Toutes les consultations</h4>
                </div>
            </div>
        </div>
        <br /><br />
        <div class="box-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                    <thead>
                        <tr>
                            <th class="bb-2">Date et heure</th>
                            <th class="bb-2">Reference</th>
                            <th class="bb-2">Motif</th>
                            <th class="bb-2">Status</th>
                            <th class="bb-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consultations as $item)
                            <tr>
                                <td>
                                    @if ($item->registre)
                                        {{ \Carbon\Carbon::parse($item->registre->updated_at)->format('d/m/Y') }} -
                                        {{ heureFr($item->registre->updated_at) }}
                                    @else
                                        {{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }} -
                                        {{ heureFr($item->updated_at) }}
                                    @endif

                                </td>
                                <td><b>{{ $item->patient->code_patient }}</b></td>

                                <td class="" style="width: 200px;"> {{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? ($item->prestationHospital->serviceHospital->service->libelle ?? 'Consultation') }}
                                </td>

                                <td class="">
                                    @if ($item->status_inf == 0)
                                        <span class="badge badge-warning">En attente</span>
                                    @else
                                        <span class="badge badge-success">Terminée</span> <br>
                                        <div style="padding-top: 5px;">
                                            @if ($item->ordonnances_count > 0)
                                                @foreach ($item->ordonnances as $ordonnan)
                                                    <a target="_blank" href="{{ route('impression', ['ordonnance', $ordonnan->id]) }}"><span
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
                                    @php
                                        $dateConsult = \Carbon\Carbon::parse($item->registre ? $item->registre->updated_at : $item->created_at)->addMinutes(15);
                                        $delaie = \Carbon\Carbon::now()->addMinutes(15);

                                        $interval = $dateConsult->diffInMinutes($delaie);

                                    @endphp
                                    @if ($item->status == 0)
                                        <a href="#" class="btn btn-sm btn-info" title="info">
                                            <span class="">Info patient</span>
                                        </a>
                                    @else
                                        <a href="{{ route('infirmier.consultation.detail', $item->id) }}"
                                            class="btn btn-sm btn-info" title="detail consultation">
                                            <span class="">Détail</span>
                                        </a>
                                    @endif

                                    <a href="javascript:void(0)"
                                        onclick="openCardModal('{{ route('infirmier.consultation.patient.card', $item->patient->id) }}')"
                                        class="btn btn-sm btn-warning" title="Carte numérique"><i
                                            class="fa-solid fa-id-card"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function openCardModal(url) {
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
        }
    </script>
@endpush