<li class="treeview">
    <a href="#" class="{{ routeActive(['cashier.admission.list', 'cashier.admission.all', 'cashier.admission.admission', 'cashier.admission.hospitalisation']) }}">
        <i class="fa fa-paypal {{ routeActive(['cashier.admission.list', 'cashier.admission.all', 'cashier.admission.admission', 'cashier.admission.hospitalisation']) }}">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Paiements</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('cashier.admission.all') }}" class="{{ routeActive('cashier.admission.all') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>En attente
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.admission.list') }}" class="{{ routeActive('cashier.admission.list') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique des paiements
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.admission.admission') }}" class="{{ routeActive('cashier.admission.admission') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Admission
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.admission.hospitalisation') }}" class="{{ routeActive('cashier.admission.hospitalisation') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Hospitalisation
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#"
        class="">
        <i
            class="fa-solid fa-id-card ">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Recette</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('cashier.recette.day', date('Y-m-d')) }}" class="{{ routeActive('cashier.recette.day') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Du jour
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.recette.list') }}" class="{{ routeActive('cashier.recette.list') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste
            </a>
        </li>
    </ul>
</li>
<li class="treeview">
    <a href="#" class="{{ routeActive(['cashier.assurance.today', 'cashier.assurance.history']) }}">
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
            <a href="{{ route('cashier.assurance.today') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Nouvelles
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.assurance.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historiques
            </a>
        </li>
    </ul>
</li>
<li class="treeview">
    <a href="#" class="{{ routeActive('cashier.planning') }}">
        <i class="fa-solid fa-calendar-days {{ routeActive('cashier.planning') }}">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Planning</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('cashier.planning') }}" class="{{ routeActive('cashier.planning') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Mon Planning
            </a>
        </li>
    </ul>
</li>