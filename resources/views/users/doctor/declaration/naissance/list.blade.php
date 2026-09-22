@extends('layouts.dashboard', ['title' => 'Liste des declarations de naissance'])

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <div style="display:flex; justify-content: space-between;">
                        <div class="">
                            <h4 class="box-title">DECLARATION DE NAISSANCE</h4>
                        </div>
                        <div class="">
                            <a href="{{ route('doctor.declaration.naissance.add') }}"
                                class="btn btn-primary btn-sm shadow">Ajouter une declaration de naissance</a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                            <thead>
                                <tr>
                                    <th class="bb-2">N° Certificat (CMN)</th>
                                    <th class="bb-2">Photo Mère</th>
                                    <th class="bb-2">DM Enfant (Nouveau-né)</th>
                                    <th class="bb-2">Mère (DM & Nom)</th>
                                    <th class="bb-2">Genre Enfant</th>
                                    <th class="bb-2">Nombre</th>
                                    <th class="bb-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($declarations as $item)
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">{{ $item->reference }}</span>
                                        </td>
                                        <td>

                                                @if ($item->patient->img_url != null)
                                                    <img src="{{ asset('assets/uploads/patient/' . $item->patient->img_url) }}"
                                                        class="avatar avatar-lg rounded10" alt="Photo de profil" />
                                                @elseif ($item->patient->gender == 'masculin')
                                                    <img src="{{ asset('assets/images/avatar/6.png') }}"
                                                        class="avatar avatar-lg rounded10" alt="Photo de profil" />
                                                @elseif($item->patient->gender == 'feminin')
                                                    <img src="{{ asset('assets/images/avatar/2.png') }}"
                                                        class="avatar avatar-lg rounded10" alt="Photo de profil" />
                                                @endif

                                        </td>
                                        <td>
                                            <span class="badge badge-success fw-bold fs-7">
                                                {{ optional(optional($item->naissance)->enfant)->code_patient ?? '—' }}
                                            </span>
                                        </td>

                                        <td>
                                            <b>{{ optional(optional($item->patient)->user)->name }} {{ optional(optional($item->patient)->user)->prenom }}</b><br>
                                            <small class="text-muted">{{ optional($item->patient)->code_patient }}</small>
                                        </td>
                                        <td>

                                            {{ optional($item->naissance)->genre ?? 'N/A' }}

                                        </td>
                                        <td>{{ $item->naissance->nombre }} enfant (s)</td>
                                        <td class="text-center">
                                            <a href="{{ route('doctor.declaration.naissance.show', $item->id) }}"
                                                class="btn btn-sm btn-info" title="clique">Voir détails</a>
                                            <a target="_blank" href="{{ route('doctor.declaration.certificat.naissance', $item->id) }}" class="btn btn-sm btn-danger" style="cursor: pointer" title="imprimer"><i
                                                    class="fa-solid fa-print"></i></a>

                                        </td>
                                    </tr>
                                @empty
                                    <div>Vide...</div>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
