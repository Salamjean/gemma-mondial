
<li class="treeview">
    <a href="#" class="{{ routeActive(['doctor.consultation.today', 'doctor.consultation.cours', 'doctor.consultation.history']) }}">
        <i class="fa-solid {{ routeActive(['doctor.consultation.today', 'doctor.consultation.cours', 'doctor.consultation.history']) }} fa-thermometer-half">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Consultations</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('doctor.consultation.today') }}"  class="{{routeActive('doctor.consultation.today')}}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Nouvelles
            </a>
        </li>

        <li>
            <a href="{{ route('doctor.consultation.history') }}" class="{{routeActive('doctor.consultation.history')}}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
    </ul>
</li>

<li>
    <a href="{{ route('doctor.suivie.suivie')}}" class="{{routeActive('doctor.suivie.suivie')}}">
        <i class="fa-solid fa-person-pregnant"></i>
        <span>Suivie mère enfant</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
</li>

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['doctor.declaration.deces.list', 'doctor.declaration.deces.direct', 'doctor.declaration.naissance.list', 'doctor.declaration.deces.add', 'doctor.declaration.naissance.add']) }}">
        <i
            class="fa-solid fa-file-medical {{ routeActive(['doctor.declaration.deces.list', 'doctor.declaration.deces.direct', 'doctor.declaration.naissance.list', 'doctor.declaration.deces.add', 'doctor.declaration.naissance.add']) }}">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Déclarations</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('doctor.declaration.deces.direct') }}"
                class="{{ routeActive('doctor.declaration.deces.direct') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i><span class="text-danger fw-bold"><i class="fa-solid fa-circle-plus me-1"></i>Enregistrer un décès</span>
            </a>
        </li>
        <li>
            <a href="{{ route('doctor.declaration.deces.list') }}"
                class="{{ routeActive(['doctor.declaration.deces.list', 'doctor.declaration.deces.add']) }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste des décès
            </a>
        </li>
        <li>
            <a href="{{ route('doctor.declaration.naissance.list') }}"
                class="{{ routeActive(['doctor.declaration.naissance.list', 'doctor.declaration.naissance.add']) }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste des naissances
            </a>
        </li>
    </ul>
</li>


