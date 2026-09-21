


<li class="treeview">
    <a href="#" class="{{ routeActive(['hospital.service.index', 'hospital.service.add']) }}">
        <i class="fa-solid {{ routeActive(['hospital.service.index', 'hospital.service.add']) }} fa-building-user">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Services</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.service.index') }}" class="{{ routeActive('hospital.service.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.service.add') }}" class="{{ routeActive('hospital.service.add') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Ajouter
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#" class="{{ routeActive(['hospital.type.assurance.index', 'hospital.type.assurance.create']) }}">
        <i class="fa fa-superpowers">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Assurances</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.type.assurance.index') }}"
                class="{{ routeActive('hospital.type.assurance.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.type.assurance.create') }}"
                class="{{ routeActive('hospital.type.assurance.create') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Ajouter
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#" class="{{ routeActive(['hospital.type.doctor.index', 'hospital.type.doctor.add']) }}">
        <i class="fa-solid fa-address-book">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Specialités Medicale</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.type.doctor.index') }}"
                class="{{ routeActive('hospital.type.doctor.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.type.doctor.add') }}" class="{{ routeActive('hospital.type.doctor.add') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Ajouter
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['hospital.doctor.index.medecin', 'hospital.doctor.index.sage', 'hospital.infirmier.index', 'hospital.pharmacy.index']) }}">
        <i
            class="fa-solid {{ routeActive(['hospital.doctor.index.medecin', 'hospital.doctor.index.sage', 'hospital.infirmier.index', 'hospital.pharmacy.index']) }} fa-user-md">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Personnel de santé</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.doctor.index.medecin') }}"
                class="{{ routeActive('hospital.doctor.index.medecin') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Medecin
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.doctor.index.sage') }}"
                class="{{ routeActive('hospital.doctor.index.sage') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Sage femme
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.infirmier.index') }}" class="{{ routeActive('hospital.infirmier.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Infirmier
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.pharmacy.index') }}" class="{{ routeActive('hospital.pharmacy.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Pharmacie
            </a>
        </li>

    </ul>
</li>

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['hospital.cashier.index', 'hospital.secretariat.index', 'hospital.accountant.index']) }}">
        <i
            class="fa-solid {{ routeActive(['hospital.cashier.index', 'hospital.secretariat.index', 'hospital.accountant.index']) }} fa-user">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Personnel adminis</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">


        <li>
            <a href="{{ route('hospital.secretariat.index') }}"
                class="{{ routeActive('hospital.secretariat.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Secretaire
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.cashier.index') }}" class="{{ routeActive('hospital.cashier.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Caissière
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.accountant.index') }}"
                class="{{ routeActive('hospital.accountant.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Comptable
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#" class="{{ routeActive(['hospital.bedroom.index', 'hospital.hospitalisation.list']) }}">
        <i class="fa-solid fa-bed">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Hospitalisation</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.bedroom.index') }}" class="{{ routeActive('hospital.bedroom.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Gestion des
                chambres
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.hospitalisation.list') }}"
                class="{{ routeActive('hospital.hospitalisation.list') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Hospitalisation
            </a>
        </li>
    </ul>
</li>

<li>
    <a href="{{ route('hospital.observation.list') }}" class="{{ routeActive(['hospital.observation.list']) }}">
        <i class="{{ routeActive(['hospital.observation.list']) }} fa-solid fa-bed">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Observation</span>
    </a>
</li>

{{-- <li>
    <a href="{{ route('hospital.grille') }}" class="{{ routeActive(['hospital.grille']) }}">
        <i class="{{ routeActive(['hospital.grille']) }} fa-solid fa-money">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Grille des prix</span>
    </a>
</li> --}}

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['hospital.recette.list', 'hospital.recette.detail', 'hospital.recette.day']) }}">
        <i
            class="fa-solid fa-id-card {{ routeActive(['hospital.recette.list', 'hospital.recette.detail', 'hospital.recette.day']) }}">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Recette</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.recette.day', date('Y-m-d')) }}"
                class="{{ routeActive('hospital.recette.day') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Du jour
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.recette.list') }}" class="{{ routeActive('hospital.recette.list') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#" class="{{ routeActive(['accountant.accounting.*']) }}">
        <i class="fa-solid fa-calculator">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Comptabilité</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('accountant.accounting.dashboard') }}" class="{{ routeActive('accountant.accounting.dashboard') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Tableau de bord
            </a>
        </li>
        <li>
            <a href="{{ route('accountant.accounting.expenses') }}" class="{{ routeActive('accountant.accounting.expenses') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Dépenses & Charges
            </a>
        </li>
        <li>
            <a href="{{ route('accountant.accounting.assurances_suivi') }}" class="{{ routeActive('accountant.accounting.assurances_suivi') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Assurances & Recouv.
            </a>
        </li>
        <li>
            <a href="{{ route('accountant.accounting.journaux') }}" class="{{ routeActive('accountant.accounting.journaux') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Journaux d'écritures
            </a>
        </li>
        <li>
            <a href="{{ route('accountant.accounting.balance') }}" class="{{ routeActive('accountant.accounting.balance') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Balance Générale
            </a>
        </li>
        <li>
            <a href="{{ route('accountant.accounting.grand_livre') }}" class="{{ routeActive('accountant.accounting.grand_livre') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Grand Livre
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#" class="{{ routeActive('hospital.patient.index') }}">
        <i class="fa-solid {{ routeActive('hospital.patient.index') }} fa fa-users">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Patients</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.patient.index') }}" class="{{ routeActive('hospital.patient.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['hospital.consultation.today', 'hospital.consultation.cours', 'hospital.consultation.history']) }}">
        <i
            class="fa-solid {{ routeActive(['hospital.consultation.today', 'hospital.consultation.cours', 'hospital.consultation.history']) }} fa-thermometer-half">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Consultations</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.consultation.today') }}"
                class="{{ routeActive('hospital.consultation.today') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Nouvelles
            </a>
        </li>

        <li>
            <a href="{{ route('hospital.consultation.history') }}"
                class="{{ routeActive('hospital.consultation.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['hospital.declaration.deces.list', 'hospital.declaration.naissance.list', 'hospital.declaration.deces.add', 'hospital.declaration.naissance.add']) }}">
        <i
            class="fa-solid fa-children {{ routeActive(['hospital.declaration.deces.list', 'hospital.declaration.naissance.list', 'hospital.declaration.deces.add', 'hospital.declaration.naissance.add']) }}">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Déclarations</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('hospital.declaration.naissance.list') }}"
                class="{{ routeActive(['hospital.declaration.naissance.list', 'hospital.declaration.naissance.add']) }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste des
                naissance
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.declaration.deces.list') }}"
                class="{{ routeActive(['hospital.declaration.deces.list', 'hospital.declaration.naissance.add']) }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste des décès
            </a>
        </li>
    </ul>
</li>


