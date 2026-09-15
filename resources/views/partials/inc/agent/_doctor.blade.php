<li class="treeview">
    <a href="#"
        class="{{ routeActive(['doctor.consultation.today', 'doctor.consultation.all', 'doctor.consultation.cours', 'doctor.consultation.history']) }}">
        <i
            class="fa-solid {{ routeActive(['doctor.consultation.today', 'doctor.consultation.all', 'doctor.consultation.cours', 'doctor.consultation.history']) }} fa-thermometer-half">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Consultations</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('doctor.consultation.today') }}" class="{{ routeActive('doctor.consultation.today') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Nouvelles
            </a>
        </li>
        <li>
            <a href="{{ route('doctor.consultation.all') }}" class="{{ routeActive('doctor.consultation.all') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Tous les patients
            </a>
        </li>

        <li>
            <a href="{{ route('doctor.consultation.history') }}"
                class="{{ routeActive('doctor.consultation.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
        <li>
            <a href="{{ route('doctor.consultation.call.history') }}"
                class="{{ routeActive('doctor.consultation.call.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Appels Vidéo HD
            </a>
        </li>
    </ul>
</li>
@if (auth()->user()->doctor->chief == 1)
    <li class="treeview">
        <a href="#"
            class="{{ routeActive(['doctor.declaration.deces.list', 'doctor.declaration.naissance.list', 'doctor.declaration.deces.add', 'doctor.declaration.naissance.add']) }}">
            <i
                class="fa-solid fa-registered {{ routeActive(['doctor.declaration.deces.list', 'doctor.declaration.naissance.list', 'doctor.declaration.deces.add', 'doctor.declaration.naissance.add']) }}">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
            </i>
            <span>Declarations</span>
            <span class="pull-right-container">
                <i class="fa-solid fa-angle-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li>
                <a href="{{ route('doctor.declaration.naissance.list') }}"
                    class="{{ routeActive(['doctor.declaration.naissance.list', 'doctor.declaration.naissance.add']) }}">
                    <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste des
                    naissance
                </a>
            </li>
            <li>
                <a href="{{ route('doctor.declaration.deces.list') }}"
                    class="{{ routeActive(['doctor.declaration.deces.list', 'doctor.declaration.naissance.add']) }}">
                    <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste des décès
                </a>
            </li>
        </ul>
    </li>
@endif
<li class="treeview">
    <a href="#"
        class="{{ routeActive(['doctor.hospitalisation.pending_room', 'doctor.hospitalisation.in_progress', 'doctor.hospitalisation.history']) }}">
        <i
            class="fa-solid fa-bed {{ routeActive(['doctor.hospitalisation.pending_room', 'doctor.hospitalisation.in_progress', 'doctor.hospitalisation.history']) }}">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Hospitalisation</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('doctor.hospitalisation.pending_room') }}"
                class="{{ routeActive('doctor.hospitalisation.pending_room') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>En attente
            </a>
        </li>
        <li>
            <a href="{{ route('doctor.hospitalisation.in_progress') }}"
                class="{{ routeActive('doctor.hospitalisation.in_progress') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>En cours
            </a>
        </li>
        <li>
            <a href="{{ route('doctor.hospitalisation.history') }}" class="{{ routeActive('doctor.hospitalisation.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
    </ul>
</li>
