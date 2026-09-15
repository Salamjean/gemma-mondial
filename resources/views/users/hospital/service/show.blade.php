@extends('layouts.dashboard')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-10 col-sm-10 col-10">
            <div class="box">
                <div class="box-header with-border">
                    <div class="row">


                        <div class="d-flex justify-content-end" style="gap: 10px">
                            <a href="{{ route('hospital.service.index') }}" class="btn btn-primary btn-md shadow">Retour à
                                la liste</a>
                        </div>
                    </div>
                </div>

                <div class="box-body fs-14">
                    <h4 class="box-title text-primary mb-0"><i class="fa-solid fa-building-user"></i> Informations | <span
                            class="text-lowercase" style="color:brown;">{{ $service->service?->libelle ?? 'Service non spécifié' }}</span></h4>
                    <hr class="my-6">
                    <div class="row">
                        <div class="col-md-12 fs-3 py-10">
                            <div class="form-label"><strong>Nom du service :</strong>
                                {{ $service->service?->libelle ?? 'Service non spécifié' }}</div><br>
                            <h4 class="box-title text-success pt-10"><i class="ti-pencil me-15"></i> Modifier les données
                            </h4>
                            <hr class="my-0"><br>
                            <form class="form" action="{{ route('hospital.service.update', $service->id) }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12" id="services__container">



                                        <div class="table-responsive fs-6">
                                            <table class="table table-bordered  table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Acte medical</th>
                                                        <th>Prix</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="">
                                                    @foreach ($service->prestationHospitals as $item)
                                                        <tr>
                                                            <td>
                                                                {{ $item->prestationService->libelle }}
                                                                <input type="hidden" name="serviceupdate[]"
                                                                    value="{{ $item->id }}" />
                                                                <span class="mx-4">
                                                                    <a
                                                                        href="{{ route('hospital.service.service.delete', $item->id) }}"><span
                                                                            class="fa-solid fa-trash fs-5 "></span></a>
                                                                </span>
                                                            </td>
                                                            <td><input type="number" class="form-control"
                                                                    value="{{ $item->prix }}" name="prixupdate[]" />
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control"
                                                                    name="descriptionupdate[]">{{ $item->description }}</textarea>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        @php
                                            $idPService = [];
                                            foreach ($service->prestationHospitals as $key => $value) {
                                                $idPService[$key] = $value->prestation_service_id;
                                            }
                                        @endphp
                                        @if (count($service->prestationHospitals) > 0)
                                            <div class="col-md-12" id="services__container">
                                                <div class="text-danger fs-5 py-3"> <i class="me-15 ti-plus"></i> Ajouter
                                                    des
                                                    actes medicaux</div>
                                                <div class="table-responsive fs-6">
                                                    <table class="table table-bordered  table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>Acte medical</th>
                                                                <th>Prix</th>
                                                                <th>Description</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="services__body__container">
                                                            @foreach ($servicei->prestationServices as $key => $item)
                                                                @if (array_search($item->id, $idPService) === false)
                                                                    <tr>
                                                                        <td>
                                                                            {{ $item->libelle }}
                                                                            <input type="hidden" name="service[]"
                                                                                value="{{ $item->id }}" />
                                                                        </td>
                                                                        <td><input type="number" class="form-control" name="prix[]" />
                                                                        </td>
                                                                        <td>
                                                                            <textarea class="form-control"
                                                                                name="description[]"></textarea>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach

                                                        </tbody>
                                                    </table>
                                                </div>

                                                {{-- Section pour ajouter de nouveaux actes personnalisés --}}
                                                <div class="col-md-12 mt-4 pt-3 border-top">
                                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                                        <div>
                                                            <h5 class="fw-bold text-dark mb-0">
                                                                <i class="fa-solid fa-plus-circle text-success me-2"></i> Ajouter de nouveaux actes médicaux
                                                            </h5>
                                                            <small class="text-muted">Définissez de nouveaux actes ou prestations pour ce service</small>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 shadow-sm fw-bold" onclick="addShowActeRow()">
                                                            <i class="fa-solid fa-plus me-1"></i> Ajouter une ligne
                                                        </button>
                                                    </div>
                                                    <div class="table-responsive fs-6">
                                                        <table class="table table-bordered table-hover align-middle">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 40%;">Intitulé de l'acte</th>
                                                                    <th style="width: 25%;">Prix (FCFA)</th>
                                                                    <th style="width: 30%;">Description</th>
                                                                    <th style="width: 5%;" class="text-center">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="new__actes__container">
                                                                {{-- Lignes ajoutées dynamiquement --}}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        @endif
                                    </div>

                                </div>
                                <div class="box-footer text-end mt-3">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">
                                        <i class="ti-save-alt me-1"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </form>

                            <script>
                                function addShowActeRow() {
                                    const container = document.getElementById('new__actes__container');
                                    if (!container) return;
                                    const rowId = 'show_row_' + Date.now();
                                    const tr = document.createElement('tr');
                                    tr.id = rowId;
                                    tr.innerHTML = `
                                        <td>
                                            <input type="text" class="form-control" name="nouveau_acte_libelle[]" placeholder="Ex: Échographie, Soins..." required />
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" min="0" step="100" class="form-control" name="nouveau_acte_prix[]" placeholder="Ex: 5000" required />
                                                <span class="input-group-text fs-12">FCFA</span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="nouveau_acte_description[]" placeholder="Précisions facultatives..." />
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="document.getElementById('${rowId}').remove()" title="Supprimer">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </td>
                                    `;
                                    container.appendChild(tr);
                                }
                            </script>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
@endsection