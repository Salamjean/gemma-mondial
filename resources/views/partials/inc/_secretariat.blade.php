<li>
    <a href="{{ route('secretariat.patient.list') }}">
        <i class="fa-solid fa-users">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Patients</span>

    </a>
</li>

<li class="treeview">
    <a href="#">
        <i class="fas fa-calendar-alt"></i>
        <span>Historique</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('secretariat.patient.list') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique des patients
            </a>
        </li>
        <li>
            <a href="{{ route('secretariat.admission.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique des affectations
            </a>
        </li>
    </ul>
</li>
<li>
    <a href="{{ route('secretariat.search_hospitalisation') }}">
        <i class="fa-solid fa-search">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Status patient</span>

    </a>
</li>
<li class="treeview">
    <a href="#" class="{{ routeActive('secretariat.availabilities') }}">
        <i class="fa-solid fa-hourglass {{ routeActive('secretariat.availabilities') }}">
            <span class="path1"></span><span class="path2"></span><span
                class="path3"></span>
        </i>
        <span>Agenda</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('secretariat.availabilities') }}" class="{{ routeActive('planning') }}">
                <i class="icon-Commit"><span class="path1"></span><span
                        class="path2"></span></i>Agenda du personnel
            </a>
        </li>

    </ul>
</li>
