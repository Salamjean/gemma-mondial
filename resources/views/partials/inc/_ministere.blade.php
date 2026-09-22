<li class="treeview {{ request()->routeIs('ministere.naissances') || request()->routeIs('ministere.deces') ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-book-medical">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Registres Déclaratifs</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ request()->routeIs('ministere.naissances') ? 'active' : '' }}">
            <a href="{{ route('ministere.naissances') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                <i class="fa fa-baby text-info me-1"></i> Naissances
            </a>
        </li>
        <li class="{{ request()->routeIs('ministere.deces') ? 'active' : '' }}">
            <a href="{{ route('ministere.deces') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                <i class="fa fa-cross text-danger me-1"></i> Décès
            </a>
        </li>
    </ul>
</li>

<li class="{{ request()->routeIs('ministere.hopitaux') ? 'active' : '' }}">
    <a href="{{ route('ministere.hopitaux') }}">
        <i class="fa fa-hospital-alt">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Hôpitaux & Centres</span>
    </a>
</li>

<li>
    <a href="{{ route('ministere.live') }}" target="_blank" class="text-warning">
        <i class="fa fa-tv text-warning">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Projection Écran 24/7</span>
    </a>
</li>
