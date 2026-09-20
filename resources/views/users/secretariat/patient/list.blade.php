@extends('layouts.dashboard', ['title' => "Liste des patients"])

@push('css')
    @livewireStyles()
@endpush
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <div class="row align-items-center">
                        <div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6">
                            <h4 class="box-title">PATIENTS</h4>
                        </div>
                        <div class="col-xs-12 col-xl-6 col-lg-6 col-md-6 col-sm-6 text-end">
                            <a href="{{ route('secretariat.search_hospitalisation') }}"
                                class="btn btn-info btn-sm shadow me-2">
                                <i class="fa fa-search me-1"></i> Vérifier / Rechercher un patient
                            </a>
                            <a href="{{ route('secretariat.patient.create') }}"
                                class="btn btn-primary btn-sm shadow">
                                <i class="fa fa-plus-circle me-1"></i> Ajouter un Patient
                            </a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                            <thead class="bg-primary">
                                <tr>
                                    <th class="bb-2">N° Dossier médical</th>
                                    <th class="bb-2">Photo</th>
                                    <th class="bb-2">Nom & Prénom(s)</th>
                                    <th class="bb-2">Genre</th>
                                    <th class="bb-2">Age</th>
                                    <th class="bb-2">Pays de naissance</th>
                                    <th class="bb-2">N° d'identité</th>
                                    <th class="bb-2 text-center">Lieu d'habitation</th>
                                    <th class="bb-2">Inscrit le</th>
                                    <th class="bb-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($patients as $patient)
                                    @php
                                        $age = 'N/A';
                                        if (!empty($patient->birth_date)) {
                                            try {
                                                $dateNaissance = \Carbon\Carbon::hasFormat($patient->birth_date, 'd/m/Y')
                                                    ? \Carbon\Carbon::createFromFormat('d/m/Y', $patient->birth_date)
                                                    : \Carbon\Carbon::parse($patient->birth_date);
                                                $age = $dateNaissance->diffInYears(\Carbon\Carbon::now()) . ' ans';
                                            } catch (\Throwable $e) {
                                                $age = 'N/A';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td><b><i>{{ $patient->code_patient ?? 'N/A' }}</i></b></td>

                                        <td>
                                            @if ($patient->img_url != null)
                                                <img src="{{ asset('assets/uploads/patient/' . $patient->img_url) }}"
                                                    class="rounded-circle" alt="Photo de profil" style="width:48px; height:48px" />
                                            @else
                                                @if ($patient->gender == 'masculin')
                                                    <img src="{{ asset('assets/images/avatar/6.png') }}"
                                                        class="avatar avatar-lg rounded10" alt="Photo de profil" />
                                                @else
                                                    <img src="{{ asset('assets/images/avatar/2.png') }}"
                                                        class="avatar avatar-lg rounded10" alt="Photo de profil" />
                                                @endif
                                            @endif

                                        </td>
                                        <td><i>{{ $patient->user->name ?? '' }} {{ $patient->user->prenom ?? '' }}</i></td>
                                        <td>{{ $patient->gender ?? '-' }}</td>
                                        <td>{{ $age }}</td>
                                        <td>{{ $patient->lieuNaissance->name ?? '-' }}</td>
                                        <td>{{ $patient->numero_identite ?? '-' }}</td>
                                        <td>{{ $patient->residenceActuelle->name ?? '-' }}</td>
                                        <td>
                                            {{ $patient->created_at ? \Carbon\Carbon::parse($patient->created_at)->format('d/m/Y') . ' - ' . heureFr($patient->created_at) : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('secretariat.patient.detail', $patient->id) }}"
                                                class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                title="Détail"><i class="fa-solid fa-eye"></i></a>
                                            <a href="{{ route('secretariat.patient.edit', $patient->id) }}"
                                                class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Modifier"><i
                                                    class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="javascript:void(0)"
                                                onclick="openCardModal('{{ route('secretariat.patient.card', $patient->id) }}')"
                                                class="btn btn-sm btn-warning" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Carte numérique"><i
                                                    class="fa-solid fa-id-card"></i></a>
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

    </div>
@endsection
@push('js')
    @livewireScripts()
    <script src="{{ asset('assets/vendor_plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('assets/src/js/pages/advanced-form-element.js') }}"></script>
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

                            // Re-execute scripts (QR Code etc)
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