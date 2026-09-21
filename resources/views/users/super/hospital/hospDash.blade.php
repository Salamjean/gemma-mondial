@extends('layouts.dashboard')

@section('content')
<!-- Bannière d'en-tête de l'établissement -->
<div class="row mb-20">
    <div class="col-12">
        <div class="box bg-primary-light border-0 shadow-sm">
            <div class="box-body py-20 px-25">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        @if($hospital->img_url)
                            <img src="{{ asset("assets/uploads/hospital/{$hospital->img_url}") }}" class="avatar avatar-xxl rounded-circle shadow-sm border border-2 border-white" alt="{{ $hospital->label }}">
                        @else
                            <img src="{{ asset('assets/uploads/hospital.gif') }}" class="avatar avatar-xxl rounded-circle shadow-sm border border-2 border-white" alt="{{ $hospital->label }}">
                        @endif
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h3 class="fw-700 text-dark mb-1">{{ $hospital->label }}</h3>
                                <span class="badge {{ $hospital->status == 0 ? 'badge-success' : 'badge-danger' }} fs-12">
                                    {{ $hospital->status == 0 ? 'Hôpital Actif' : 'Hôpital Désactivé' }}
                                </span>
                                @if($hospital->is_teleconsultation_active ?? true)
                                    <span class="badge bg-light-success text-success border border-success fs-12">
                                        <i class="fa-solid fa-headset me-1"></i> Téléconsultation Active
                                    </span>
                                @endif
                            </div>
                            <div class="text-muted fs-13 mt-1 d-flex flex-wrap gap-3">
                                <span><i class="ti-bookmark text-primary me-1"></i> Réf: <strong>{{ $hospital->reference }}</strong></span>
                                <span><i class="ti-location-pin text-primary me-1"></i> {{ $hospital->localite ?? 'Non renseigné' }} ({{ $hospital->district_sanitaire ?? 'District' }})</span>
                                <span><i class="ti-mobile text-primary me-1"></i> {{ $hospital->contact ?? 'Aucun contact' }}</span>
                                @if($hospital->nom_direction_generale)
                                    <span><i class="ti-user text-primary me-1"></i> Dir: {{ $hospital->nom_direction_generale }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('super.hospital.show', $hospital->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Modifier
                        </a>
                        <a href="{{ route('super.hospital.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                            <i class="ti-arrow-left me-1"></i> Retour aux Hôpitaux
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cartes de Synthèse Globale -->
<div class="row">
    <div class="col-xxl-2 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-success-light">
            <div class="box-body text-center py-15">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/icons/money.png') }}" class="avatar avatar-md" alt="recettes">
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0 fw-700 text-success">{{ number_format($totalRecettesGlobales, 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Recettes (FCFA)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-primary-light">
            <div class="box-body text-center py-15">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/icons/team.png') }}" class="avatar avatar-md" alt="patients">
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0 fw-700 text-primary">{{ number_format($totalPatient, 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Patients Inscrits</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-info-light">
            <div class="box-body text-center py-15">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/icons/consult.png') }}" class="avatar avatar-md" alt="consultations">
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0 fw-700 text-info">{{ number_format($totalConsult, 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Consultations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-danger-light">
            <div class="box-body text-center py-15">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/uploads/agent.png') }}" class="avatar avatar-md" alt="personnel">
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0 fw-700 text-danger">{{ $totalStaff }}</h4>
                        <p class="text-muted mb-0 fs-12">Personnel Total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-warning-light">
            <div class="box-body text-center py-15">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/icons/admission.png') }}" class="avatar avatar-md" alt="admissions">
                    </div>
                    <div class="text-end">
                        <h4 class="mb-0 fw-700 text-warning">{{ number_format($totalAdmission, 0, ',', ' ') }}</h4>
                        <p class="text-muted mb-0 fs-12">Admissions Total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-2 col-xl-4 col-md-4 col-sm-6 col-12">
        <div class="box bg-secondary-light">
            <div class="box-body text-center py-15">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="p-5">
                        <img src="{{ asset('assets/uploads/baby.png') }}" class="avatar avatar-md" alt="etat civil">
                    </div>
                    <div class="text-end">
                        <h5 class="mb-0 fw-700 text-dark">{{ $totalDN }} <span class="text-muted fs-11">Nais.</span> / {{ $totalDD }} <span class="text-muted fs-11">Déc.</span></h5>
                        <p class="text-muted mb-0 fs-12">État Civil</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation par Onglets de Supervision -->
<div class="row mb-20">
    <div class="col-12">
        <div class="box box-body py-10 px-15 mb-0 shadow-sm border-0">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row gap-2" id="hospitalTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-10 px-15 fw-600" id="services-tab" data-bs-toggle="tab" data-bs-target="#tab-services" type="button" role="tab">
                        <i class="ti-layout-grid2 me-2"></i> Services & Prestations ({{ count($services) }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-10 px-15 fw-600" id="staff-tab" data-bs-toggle="tab" data-bs-target="#tab-staff" type="button" role="tab">
                        <i class="ti-user me-2"></i> Personnel de Santé & Admin ({{ $totalStaff }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-10 px-15 fw-600" id="admissions-tab" data-bs-toggle="tab" data-bs-target="#tab-admissions" type="button" role="tab">
                        <i class="ti-receipt me-2"></i> Admissions & Consultations
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-10 px-15 fw-600" id="patients-tab" data-bs-toggle="tab" data-bs-target="#tab-patients" type="button" role="tab">
                        <i class="ti-id-badge me-2"></i> Patients ({{ $totalPatient }})
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-10 px-15 fw-600" id="finances-tab" data-bs-toggle="tab" data-bs-target="#tab-finances" type="button" role="tab">
                        <i class="ti-wallet me-2"></i> Synthèse Financière
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Contenu des Onglets -->
<div class="tab-content" id="hospitalTabsContent">

    <!-- ONGLET 1: SERVICES & PRESTATIONS -->
    <div class="tab-pane fade show active" id="tab-services" role="tabpanel">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="ti-layout-grid2 text-primary me-2"></i> Services Médicaux & Prestations de l'Établissement</h4>
                <span class="badge badge-primary fs-13">{{ count($services) }} services configurés</span>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px;">N°</th>
                                <th>Service Médical</th>
                                <th class="text-center">Prestations Associées</th>
                                <th class="text-center" style="width: 120px;">Statut</th>
                                <th class="text-center" style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $item)
                                <tr>
                                    <td class="text-center fw-bold">#{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->service->libelle ?? 'Service Médical' }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info-light fs-12 px-3 py-1">
                                            {{ count($item->prestationHospitals) }} prestation(s)
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 0)
                                            <span class="badge badge-success"><i class="fa fa-check me-1"></i> Activé</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fa fa-ban me-1"></i> Désactivé</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('super.hospital.statusSce', $item->id) }}" 
                                           class="btn btn-xs {{ $item->status == 0 ? 'btn-danger' : 'btn-success' }}" 
                                           title="{{ $item->status == 0 ? 'Désactiver ce service' : 'Activer ce service' }}">
                                            <i class="fa-solid {{ $item->status == 0 ? 'fa-ban' : 'fa-check' }}"></i>
                                            {{ $item->status == 0 ? 'Désactiver' : 'Activer' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-20">Aucun service médical configuré pour cet hôpital.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ONGLET 2: PERSONNEL DE SANTÉ & ADMIN -->
    <div class="tab-pane fade" id="tab-staff" role="tabpanel">
        <!-- Résumé synthétique par fonction -->
        <div class="row g-2 mb-20">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="box bg-primary-light mb-0 p-15 text-center rounded-3 border-0">
                    <i class="fa-solid fa-user-md fs-24 text-primary mb-1"></i>
                    <h4 class="fw-700 text-dark mb-0">{{ $totalDoctor }}</h4>
                    <span class="fs-12 text-muted fw-600">Médecins</span>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="box bg-success-light mb-0 p-15 text-center rounded-3 border-0">
                    <i class="fa-solid fa-user-nurse fs-24 text-success mb-1"></i>
                    <h4 class="fw-700 text-dark mb-0">{{ $totalInfirmier }}</h4>
                    <span class="fs-12 text-muted fw-600">Infirmiers</span>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="box bg-warning-light mb-0 p-15 text-center rounded-3 border-0">
                    <i class="fa-solid fa-user-tie fs-24 text-warning mb-1"></i>
                    <h4 class="fw-700 text-dark mb-0">{{ $totalSecretariat }}</h4>
                    <span class="fs-12 text-muted fw-600">Secrétaires</span>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="box bg-info-light mb-0 p-15 text-center rounded-3 border-0">
                    <i class="fa-solid fa-cash-register fs-24 text-info mb-1"></i>
                    <h4 class="fw-700 text-dark mb-0">{{ $totalCashier }}</h4>
                    <span class="fs-12 text-muted fw-600">Caissières</span>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="box bg-danger-light mb-0 p-15 text-center rounded-3 border-0">
                    <i class="fa-solid fa-calculator fs-24 text-danger mb-1"></i>
                    <h4 class="fw-700 text-dark mb-0">{{ $totalAccountant }}</h4>
                    <span class="fs-12 text-muted fw-600">Comptables</span>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="box bg-secondary mb-0 p-15 text-center rounded-3 border-0">
                    <i class="fa-solid fa-pills fs-24 text-success mb-1"></i>
                    <h4 class="fw-700 text-dark mb-0">{{ $totalPharmacy }}</h4>
                    <span class="fs-12 text-muted fw-600">Pharmaciens</span>
                </div>
            </div>
        </div>

        <!-- Navigation des Rôles du Personnel -->
        <div class="box">
            <div class="box-header with-border py-10">
                <ul class="nav nav-tabs customtab2" id="staffSubTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active fw-600" id="subtab-all-link" data-bs-toggle="tab" href="#subtab-all" role="tab">
                            <i class="fa-solid fa-users me-1 text-primary"></i> Tout le Personnel ({{ $totalStaff }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-600" id="subtab-doctors-link" data-bs-toggle="tab" href="#subtab-doctors" role="tab">
                            <i class="fa-solid fa-user-md me-1 text-primary"></i> Médecins ({{ $totalDoctor }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-600" id="subtab-nurses-link" data-bs-toggle="tab" href="#subtab-nurses" role="tab">
                            <i class="fa-solid fa-user-nurse me-1 text-success"></i> Infirmiers ({{ $totalInfirmier }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-600" id="subtab-secretaries-link" data-bs-toggle="tab" href="#subtab-secretaries" role="tab">
                            <i class="fa-solid fa-user-tie me-1 text-warning"></i> Secrétariat ({{ $totalSecretariat }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-600" id="subtab-cashiers-link" data-bs-toggle="tab" href="#subtab-cashiers" role="tab">
                            <i class="fa-solid fa-cash-register me-1 text-info"></i> Caissières ({{ $totalCashier }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-600" id="subtab-accountants-link" data-bs-toggle="tab" href="#subtab-accountants" role="tab">
                            <i class="fa-solid fa-calculator me-1 text-danger"></i> Comptables ({{ $totalAccountant }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-600" id="subtab-pharmacies-link" data-bs-toggle="tab" href="#subtab-pharmacies" role="tab">
                            <i class="fa-solid fa-pills me-1 text-success"></i> Pharmaciens ({{ $totalPharmacy }})
                        </a>
                    </li>
                </ul>
            </div>
            <div class="box-body p-0">
                <div class="tab-content" id="staffSubTabsContent">
                    <!-- SOUS-ONGLET 1: TOUT LE PERSONNEL -->
                    <div class="tab-pane fade show active" id="subtab-all" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">N°</th>
                                        <th>Nom & Prénoms</th>
                                        <th>Rôle / Fonction</th>
                                        <th>Service / Spécialité</th>
                                        <th>Téléphone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $countAll = 1; @endphp
                                    @foreach($doctors as $doc)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $countAll++ }}</td>
                                            <td><strong>Dr. {{ $doc->user->name ?? '' }} {{ $doc->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-primary"><i class="fa-solid fa-user-md me-1"></i> Médecin</span></td>
                                            <td><span class="badge badge-info-light">{{ $doc->serviceHospital->service->libelle ?? 'Médecine Générale' }}</span></td>
                                            <td>{{ $doc->user->telephone ?? ($doc->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $doc->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                    @foreach($infirmiers as $inf)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $countAll++ }}</td>
                                            <td><strong>{{ $inf->user->name ?? '' }} {{ $inf->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-success"><i class="fa-solid fa-user-nurse me-1"></i> Infirmier</span></td>
                                            <td><span class="badge badge-success-light">{{ $inf->serviceHospital->service->libelle ?? 'Soins Infirmiers' }}</span></td>
                                            <td>{{ $inf->user->telephone ?? ($inf->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $inf->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                    @foreach($secretariats as $sec)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $countAll++ }}</td>
                                            <td><strong>{{ $sec->user->name ?? '' }} {{ $sec->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-warning"><i class="fa-solid fa-user-tie me-1"></i> Secrétaire</span></td>
                                            <td><span class="text-muted">Accueil & Admissions</span></td>
                                            <td>{{ $sec->user->telephone ?? ($sec->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $sec->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                    @foreach($cashiers as $csh)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $countAll++ }}</td>
                                            <td><strong>{{ $csh->user->name ?? '' }} {{ $csh->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-info"><i class="fa-solid fa-cash-register me-1"></i> Caissière</span></td>
                                            <td><span class="text-muted">Caisse & Règlements</span></td>
                                            <td>{{ $csh->user->telephone ?? ($csh->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $csh->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                    @foreach($accountants as $acc)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $countAll++ }}</td>
                                            <td><strong>{{ $acc->user->name ?? '' }} {{ $acc->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-danger"><i class="fa-solid fa-calculator me-1"></i> Comptable</span></td>
                                            <td><span class="text-muted">Gestion Financière</span></td>
                                            <td>{{ $acc->user->telephone ?? '-' }}</td>
                                            <td><small class="text-muted">{{ $acc->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                    @foreach($pharmacies as $ph)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $countAll++ }}</td>
                                            <td><strong>{{ $ph->user->name ?? '' }} {{ $ph->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-success"><i class="fa-solid fa-pills me-1"></i> Pharmacien</span></td>
                                            <td><span class="text-muted">Pharmacie & Médicaments</span></td>
                                            <td>{{ $ph->user->telephone ?? '-' }}</td>
                                            <td><small class="text-muted">{{ $ph->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @endforeach
                                    @if($totalStaff == 0)
                                        <tr><td colspan="6" class="text-center text-muted py-25">Aucun membre du personnel enregistré pour cet établissement.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SOUS-ONGLET 2: MÉDECINS -->
                    <div class="tab-pane fade" id="subtab-doctors" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">N°</th>
                                        <th>Matricule</th>
                                        <th>Nom & Prénoms</th>
                                        <th>Spécialité / Service</th>
                                        <th>Contact Téléphone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($doctors as $doc)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $loop->iteration }}</td>
                                            <td><code>{{ $doc->matricule ?? "DOC-{$doc->id}" }}</code></td>
                                            <td><strong>Dr. {{ $doc->user->name ?? '' }} {{ $doc->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-info-light">{{ $doc->serviceHospital->service->libelle ?? 'Médecine Générale' }}</span></td>
                                            <td>{{ $doc->user->telephone ?? ($doc->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $doc->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-25">Aucun médecin enregistré.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SOUS-ONGLET 3: INFIRMIERS -->
                    <div class="tab-pane fade" id="subtab-nurses" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">N°</th>
                                        <th>Matricule</th>
                                        <th>Nom & Prénoms</th>
                                        <th>Service Affecté</th>
                                        <th>Contact Téléphone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($infirmiers as $inf)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $loop->iteration }}</td>
                                            <td><code>{{ $inf->matricule ?? "INF-{$inf->id}" }}</code></td>
                                            <td><strong>{{ $inf->user->name ?? '' }} {{ $inf->user->prenom ?? '' }}</strong></td>
                                            <td><span class="badge badge-success-light">{{ $inf->serviceHospital->service->libelle ?? 'Soins Infirmiers' }}</span></td>
                                            <td>{{ $inf->user->telephone ?? ($inf->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $inf->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-25">Aucun infirmier enregistré.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SOUS-ONGLET 4: SECRÉTARIAT -->
                    <div class="tab-pane fade" id="subtab-secretaries" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">N°</th>
                                        <th>Matricule</th>
                                        <th>Nom & Prénoms</th>
                                        <th>Contact Téléphone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($secretariats as $sec)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $loop->iteration }}</td>
                                            <td><code>{{ $sec->matricule ?? "SEC-{$sec->id}" }}</code></td>
                                            <td><strong>{{ $sec->user->name ?? '' }} {{ $sec->user->prenom ?? '' }}</strong></td>
                                            <td>{{ $sec->user->telephone ?? ($sec->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $sec->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-25">Aucun secrétaire enregistré.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SOUS-ONGLET 5: CAISSIÈRES -->
                    <div class="tab-pane fade" id="subtab-cashiers" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">N°</th>
                                        <th>Matricule</th>
                                        <th>Nom & Prénoms</th>
                                        <th>Contact Téléphone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cashiers as $csh)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $loop->iteration }}</td>
                                            <td><code>{{ $csh->matricule ?? "CSH-{$csh->id}" }}</code></td>
                                            <td><strong>{{ $csh->user->name ?? '' }} {{ $csh->user->prenom ?? '' }}</strong></td>
                                            <td>{{ $csh->user->telephone ?? ($csh->contact ?? '-') }}</td>
                                            <td><small class="text-muted">{{ $csh->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-25">Aucune caissière enregistrée.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SOUS-ONGLET 6: COMPTABLES -->
                    <div class="tab-pane fade" id="subtab-accountants" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">N°</th>
                                        <th>Nom & Prénoms</th>
                                        <th>Contact Téléphone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($accountants as $acc)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $loop->iteration }}</td>
                                            <td><strong>{{ $acc->user->name ?? '' }} {{ $acc->user->prenom ?? '' }}</strong></td>
                                            <td>{{ $acc->user->telephone ?? '-' }}</td>
                                            <td><small class="text-muted">{{ $acc->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-25">Aucun comptable enregistré.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SOUS-ONGLET 7: PHARMACIENS -->
                    <div class="tab-pane fade" id="subtab-pharmacies" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">N°</th>
                                        <th>Nom & Prénoms</th>
                                        <th>Contact Téléphone</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pharmacies as $ph)
                                        <tr>
                                            <td class="text-muted fw-bold">#{{ $loop->iteration }}</td>
                                            <td><strong>{{ $ph->user->name ?? '' }} {{ $ph->user->prenom ?? '' }}</strong></td>
                                            <td>{{ $ph->user->telephone ?? '-' }}</td>
                                            <td><small class="text-muted">{{ $ph->user->email ?? '-' }}</small></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted py-25">Aucun pharmacien enregistré.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ONGLET 3: ADMISSIONS & CONSULTATIONS -->
    <div class="tab-pane fade" id="tab-admissions" role="tabpanel">
        <div class="row">
            <!-- Dernières Admissions -->
            <div class="col-12 mb-20">
                <div class="box">
                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                        <h4 class="box-title"><i class="ti-receipt text-warning me-2"></i> Dernières Admissions Enregistrées</h4>
                        <span class="badge badge-warning">{{ count($recentAdmissions) }} récentes</span>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 95px;">Date</th>
                                        <th>N° Facture / Réf</th>
                                        <th>Patient</th>
                                        <th>Prestation / Motif</th>
                                        <th class="text-end">Montant</th>
                                        <th class="text-center">Paiement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentAdmissions as $adm)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($adm->created_at)->format('d/m/Y H:i') }}</td>
                                            <td><code>{{ $adm->num_facture ?? ($adm->code_admission ?? "ADM-{$adm->id}") }}</code></td>
                                            <td><strong>{{ $adm->patient->user->name ?? ($adm->patient->nom ?? 'Patient') }} {{ $adm->patient->user->prenom ?? '' }}</strong></td>
                                            <td>
                                                {{ $adm->motif_consultation ?? ($adm->prestationHospital->prestationService->nom ?? 'Consultation') }}
                                                @if($adm->typeAssurance)
                                                    <small class="badge badge-info-light d-block mt-1">{{ $adm->typeAssurance->nom }} ({{ $adm->taux_couverture }}%)</small>
                                                @endif
                                            </td>
                                            <td class="text-end fw-600 text-dark">{{ number_format($adm->montant, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-center">
                                                @if($adm->statut_paiement == '1')
                                                    <span class="badge badge-success"><i class="fa fa-check me-1"></i> Payé</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fa fa-clock-o me-1"></i> En attente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted py-20">Aucune admission enregistrée pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dernières Consultations Médicales -->
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                        <h4 class="box-title"><i class="fa-solid fa-stethoscope text-info me-2"></i> Dernières Consultations Médicales</h4>
                        <span class="badge badge-info">{{ count($recentConsultations) }} récentes (Total: {{ $totalConsult }})</span>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 95px;">Date</th>
                                        <th>Patient</th>
                                        <th>Médecin Consultant</th>
                                        <th>Diagnostic / Motif</th>
                                        <th>Constantes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentConsultations as $cst)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($cst->created_at)->format('d/m/Y H:i') }}</td>
                                            <td><strong>{{ $cst->patient->user->name ?? ($cst->patient->nom ?? 'Patient') }} {{ $cst->patient->user->prenom ?? '' }}</strong></td>
                                            <td>Dr. {{ $cst->doctor->user->name ?? ($cst->doctor->nom ?? '-') }} {{ $cst->doctor->user->prenom ?? '' }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($cst->diagnostic ?? ($cst->motif ?? 'Consultation médicale'), 60) }}</td>
                                            <td>
                                                @if($cst->tension || $cst->poids || $cst->temperature)
                                                    <small class="text-muted">
                                                        @if($cst->tension) TA: {{ $cst->tension }} | @endif
                                                        @if($cst->temperature) T°: {{ $cst->temperature }}°C | @endif
                                                        @if($cst->poids) Pds: {{ $cst->poids }}kg @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted fs-12">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center text-muted py-20">Aucune consultation médicale enregistrée pour le moment.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ONGLET 4: PATIENTS -->
    <div class="tab-pane fade" id="tab-patients" role="tabpanel">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <h4 class="box-title"><i class="ti-id-badge text-primary me-2"></i> Patients Rattachés à cet Établissement</h4>
                <span class="badge badge-primary">{{ $totalPatient }} patient(s)</span>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Matricule</th>
                                <th>Nom & Prénoms</th>
                                <th>Contact</th>
                                <th>Sexe / Âge</th>
                                <th>Groupe Sanguin</th>
                                <th>Inscrit le</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPatients as $pat)
                                <tr>
                                    <td><code>{{ $pat->code_patient ?? "PAT-{$pat->id}" }}</code></td>
                                    <td><strong>{{ $pat->user->name ?? ($pat->nom ?? 'Patient') }} {{ $pat->user->prenom ?? '' }}</strong></td>
                                    <td>{{ $pat->user->telephone ?? ($pat->contact ?? '-') }}</td>
                                    <td>{{ $pat->genre ?? ($pat->sexe ?? '-') }}</td>
                                    <td>
                                        @if($pat->groupe_sanguin)
                                            <span class="badge badge-danger-light">{{ $pat->groupe_sanguin }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pat->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-20">Aucun patient inscrit dans cet établissement.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ONGLET 5: SYNTHÈSE FINANCIÈRE -->
    <div class="tab-pane fade" id="tab-finances" role="tabpanel">
        <div class="row">
            <div class="col-xl-4 col-12">
                <div class="box bg-primary-light">
                    <div class="box-body">
                        <h4 class="box-title text-dark">Recettes des Prestations & Admissions</h4>
                        <h2 class="text-primary fw-700 mt-15 mb-0">{{ number_format($recetteAdmissions, 0, ',', ' ') }} FCFA</h2>
                        <small class="text-muted">Total payé par les patients au guichet</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="box bg-success-light">
                    <div class="box-body">
                        <h4 class="box-title text-dark">Recettes Ventes Pharmacie</h4>
                        <h2 class="text-success fw-700 mt-15 mb-0">{{ number_format($recettePharmacie, 0, ',', ' ') }} FCFA</h2>
                        <small class="text-muted">Total encaissé sur les ventes de médicaments</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-12">
                <div class="box bg-info-light">
                    <div class="box-body">
                        <h4 class="box-title text-dark">Recouvrements Assurances</h4>
                        <h2 class="text-info fw-700 mt-15 mb-0">{{ number_format($recetteAssurances, 0, ',', ' ') }} FCFA</h2>
                        <small class="text-muted">Règlements reçus des compagnies d'assurance</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-6 col-12">
                <div class="box bg-danger-light">
                    <div class="box-body">
                        <h4 class="box-title text-dark">Dépenses & Charges Enregistrées</h4>
                        <h2 class="text-danger fw-700 mt-15 mb-0">{{ number_format($totalDepenses, 0, ',', ' ') }} FCFA</h2>
                        <small class="text-muted">Total des frais et charges de fonctionnement</small>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-12">
                <div class="box {{ $soldeFinancier >= 0 ? 'bg-success-light' : 'bg-danger-light' }}">
                    <div class="box-body">
                        <h4 class="box-title text-dark">Solde Net Global</h4>
                        <h2 class="{{ $soldeFinancier >= 0 ? 'text-success' : 'text-danger' }} fw-700 mt-15 mb-0">{{ number_format($soldeFinancier, 0, ',', ' ') }} FCFA</h2>
                        <small class="text-muted">Recettes totales - Dépenses totales</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection 