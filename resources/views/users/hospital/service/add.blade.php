@extends('layouts.dashboard')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-11 col-12">
            <div class="box shadow-sm border rounded-4">
                <div class="box-header with-border bg-white py-3 px-4 rounded-top-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="box-title fw-bold text-dark mb-0">
                                <i class="fa-solid fa-square-plus text-success me-2"></i> ENREGISTREMENT D'UN SERVICE
                            </h4>
                            <small class="text-muted">Configurez un nouveau département ou service médical pour votre hôpital</small>
                        </div>
                        <div>
                            <a href="{{ route('hospital.service.index') }}" class="btn btn-outline-success btn-md rounded-pill shadow-sm">
                                <i class="fa-solid fa-list me-1"></i> Liste des services
                            </a>
                        </div>
                    </div>
                </div>

                <form class="form" action="{{ route('hospital.service.store') }}" method="post" enctype="multipart/form-data" id="formAddService">
                    @csrf
                    <div class="box-body p-4">

                        {{-- Sélecteur de mode (si des services existants sont disponibles) --}}
                        @if($hasAvailableServices)
                            <div class="mb-4">
                                <label class="form-label fw-bold fs-13 text-secondary mb-2">Type d'ajout de service :</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="mode_service" id="mode_existant" value="existant" checked onchange="toggleServiceMode('existant')">
                                        <label class="form-check-label fw-bold text-dark" for="mode_existant">
                                            <i class="fa-solid fa-list-check text-primary me-1"></i> Choisir un service existant dans le catalogue ({{ count($services) }} disponible{{ count($services) > 1 ? 's' : '' }})
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="mode_service" id="mode_nouveau" value="nouveau" onchange="toggleServiceMode('nouveau')">
                                        <label class="form-check-label fw-bold text-dark" for="mode_nouveau">
                                            <i class="fa-solid fa-plus-circle text-success me-1"></i> Créer un nouveau service personnalisé
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="mode_service" value="nouveau">
                        @endif

                        <div class="row g-3">
                            {{-- Champ Service existant (si disponible) --}}
                            @if($hasAvailableServices)
                                <div class="col-md-12" id="blocServiceExistant">
                                    <div class="form-group">
                                        <label for="department" class="form-label fw-bold">Sélectionner le service <span class="text-danger">*</span></label>
                                        <select class="form-select" id="department" name="department" style="height: 42px;">
                                            <option value="">-- Choisissez un service --</option>
                                            @foreach ($services as $item)
                                                <option value="{{ $item->id }}">{{ $item->libelle }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            {{-- Champ Nouveau Service personnalisé --}}
                            <div class="col-md-12" id="blocNouveauService" style="{{ $hasAvailableServices ? 'display: none;' : '' }}">
                                <div class="form-group">
                                    <label for="nom_nouveau_service" class="form-label fw-bold">Nom du nouveau service <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nom_nouveau_service" name="nom_nouveau_service" placeholder="Ex: Cardiologie, Ophtalmologie, Radiologie, Odontologie..." style="height: 42px;">
                                    <small class="text-muted">Saisissez l'intitulé exact de votre nouveau service hospitalier.</small>
                                </div>
                            </div>

                            {{-- Description facultative du service --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="service_description" class="form-label fw-bold">Description du service <small class="text-muted">(Facultatif)</small></label>
                                    <textarea class="form-control" id="service_description" name="service_description" rows="2" placeholder="Brève description ou informations pratiques du service..."></textarea>
                                </div>
                            </div>

                            {{-- Section des actes médicaux & tarifs --}}
                            <div class="col-md-12 mt-4" id="services__container">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2 border-bottom">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-0">
                                            <i class="fa-solid fa-file-invoice-dollar text-teal me-2" style="color: #0d9488;"></i> Actes médicaux &amp; Tarification
                                        </h5>
                                        <small class="text-muted">Indiquez les prix en FCFA des actes et consultations pratiqués dans ce service.</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold" onclick="addCustomActeRow()">
                                        <i class="fa-solid fa-plus me-1"></i> Ajouter un acte médical
                                    </button>
                                </div>

                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40%;">Acte médical / Prestation <span class="text-danger">*</span></th>
                                                <th style="width: 25%;">Prix (FCFA) <span class="text-danger">*</span></th>
                                                <th style="width: 30%;">Description / Précision</th>
                                                <th style="width: 5%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="services__body__container">
                                            {{-- Lignes pré-chargées ou personnalisées --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="box-footer text-end bg-light p-3 rounded-bottom-4 border-top">
                        <a href="{{ route('hospital.service.index') }}" class="btn btn-secondary me-2 rounded-pill px-4">
                            <i class="fa-solid fa-xmark me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); border: none;">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer le service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const hasAvailable = {{ $hasAvailableServices ? 'true' : 'false' }};

        function toggleServiceMode(mode) {
            const blocExistant = document.getElementById('blocServiceExistant');
            const blocNouveau = document.getElementById('blocNouveauService');
            const selectDep = document.getElementById('department');
            const inputNouveau = document.getElementById('nom_nouveau_service');

            if (mode === 'nouveau') {
                if (blocExistant) blocExistant.style.display = 'none';
                if (blocNouveau) blocNouveau.style.display = 'block';
                if (selectDep) selectDep.value = '';
                if (inputNouveau) inputNouveau.focus();

                // Si aucune ligne d'acte n'est présente, en ajouter une par défaut
                const body = document.getElementById('services__body__container');
                if (body && body.children.length === 0) {
                    addCustomActeRow("Consultation standard", "");
                }
            } else {
                if (blocExistant) blocExistant.style.display = 'block';
                if (blocNouveau) blocNouveau.style.display = 'none';
                if (inputNouveau) inputNouveau.value = '';
            }
        }

        // Ajouter une nouvelle ligne d'acte médical dynamique
        function addCustomActeRow(defaultLibelle = '', defaultPrix = '') {
            const container = document.getElementById('services__body__container');
            if (!container) return;

            const rowId = 'custom_row_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.innerHTML = `
                <td>
                    <input type="text" class="form-control" name="nouveau_acte_libelle[]" value="${defaultLibelle}" placeholder="Ex: Consultation spécialisée, Échographie..." required />
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" min="0" step="100" class="form-control" name="nouveau_acte_prix[]" value="${defaultPrix}" placeholder="Ex: 5000" required />
                        <span class="input-group-text fs-12">FCFA</span>
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control" name="nouveau_acte_description[]" placeholder="Détails ou conditions..." />
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="removeActeRow('${rowId}')" title="Supprimer cet acte">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </td>
            `;
            container.appendChild(tr);
        }

        function removeActeRow(rowId) {
            const row = document.getElementById(rowId);
            if (row) row.remove();
        }

        $(document).ready(function () {
            // Initialisation si aucun service existant disponible
            if (!hasAvailable) {
                toggleServiceMode('nouveau');
            }

            // Chargement des actes existants si choix d'un service existant
            $('#department').change(function () {
                var department = $(this).val();
                var servicesContainer = $('#services__body__container');
                servicesContainer.empty();

                if (department) {
                    $.ajax({
                        url: '{{ route("hospital.service.service.search", ":id") }}'.replace(':id', department),
                        type: 'GET',
                        success: function (response) {
                            if (response && response.length > 0) {
                                $.each(response, function (key, service) {
                                    servicesContainer.append(
                                        `<tr id="predef_row_${service.id}">
                                            <td>
                                                <span class="fw-bold text-dark">${service.libelle}</span>
                                                <input type="hidden" name="service[]" value="${service.id}" />
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" min="0" step="100" class="form-control" name="prix[]" placeholder="Prix en FCFA" />
                                                    <span class="input-group-text fs-12">FCFA</span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="description[]" placeholder="Description facultative" />
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" onclick="removeActeRow('predef_row_${service.id}')" title="Retirer cet acte">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </td>
                                        </tr>`
                                    );
                                });
                            } else {
                                // Si le service existant n'a pas encore de prestations définies
                                addCustomActeRow("Consultation " + $('#department option:selected').text(), "");
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Erreur chargement actes:", error);
                            addCustomActeRow("Consultation", "");
                        }
                    });
                }
            });
        });
    </script>
@endsection