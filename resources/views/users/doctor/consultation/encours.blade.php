@extends('layouts.dashboard', ['title' => 'Liste des consultations en cours'])

@section('content')
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-xs-12  col-xl-9 col-lg-9 col-md-9 col-sm-9">
                    <h4 class="box-title">Vos consultations du jour en cours</h4>
                </div>
            </div>
        </div>
        <br /><br />
        <div class="box-body">
            <div class="table-responsive">
                <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                    <thead>
                        <tr>
                            <th class="bb-2">Reference</th>
                            <th class="bb-2">Nom du Patient</th>
                            <th class="bb-2">Motif</th>
                            <th class="bb-2 text-center">Dossier médical</th>
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
                                <td class="" style="width: 200px;"> {{ $item->prestationHospital->service->libelle }} </td>

                                <td class="text-center">
                                    @if (optional($item->patient)->id)
                                        <a href="{{ route('doctor.patient.dossier_medical', $item->patient->id) }}"
                                            class="btn btn-sm btn-outline-info rounded-pill fw-bold shadow-xs px-3 py-1.5 text-nowrap d-inline-flex align-items-center gap-1.5" title="Ouvrir le dossier médical complet">
                                            <i class="fa-solid fa-folder-open fs-13"></i>
                                            <span>Dossier médical</span>
                                        </a>
                                    @else
                                        <span class="text-muted fs-11">-</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Beging">
                                        <span class="">Poursuivre la consultation</span>

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
