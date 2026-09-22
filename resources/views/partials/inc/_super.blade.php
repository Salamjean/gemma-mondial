<li class="treeview">
    <a href="#">
        <i class="fa fa-h-square">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Hôpital</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('super.hospital.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                Liste
            </a>
        </li>
        <li>
            <a href="{{ route('super.hospital.add') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>
                Ajouter
            </a>
        </li>
    </ul>
</li>
<li class="treeview">
    <a href="#">
        <i class="fa fa-cogs">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Réglages</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('super.setting.icon') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Icônes
            </a>
        </li>
    </ul>
<li class="treeview {{ request()->routeIs('super.logs.*') ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-shield-alt">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Logs & Audit</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li class="{{ request()->routeIs('super.logs.*') ? 'active' : '' }}">
            <a href="{{ route('super.logs.index') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Journal & Erreurs
            </a>
        </li>
    </ul>
</li>
</br></br></br></br></br>
<footer class="main-footer">
			  <div class="sidebar-widgets">
				<div class="copyright text-center m-25">
					<p><small><i> - GEMMA -</i></small>
				</div>
			  </div>
</footer>