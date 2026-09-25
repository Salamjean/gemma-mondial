<header class="main-header">
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button & Mobile Logo -->
	  <div class="app-menu d-flex align-items-center">
		<ul class="header-megamenu nav">
			<li class="btn-group nav-item">
				<a href="#" class="waves-effect waves-light nav-link push-btn btn-success-light" data-toggle="push-menu" role="button" aria-label="Ouvrir le menu">
					<i class="icon-Menu"><span class="path1"></span><span class="path2"></span></i>
			    </a>
			</li>
		</ul>
        <!-- Logo visible uniquement sur mobile dans la navbar -->
        <a href="{{ route('dashboard') }}" class="mobile-header-logo d-md-none ms-2">
            <img src="{{ asset(iconsLoad()['logo']) }}" alt="logo" style="max-height: 32px; width: auto;">
        </a>
	  </div>
      <div class="navbar-custom-menu r-side">
        <ul class="nav navbar-nav">
			<li class="btn-group nav-item">
				<a href="{{ route('logout') }}" class="waves-effect waves-light nav-link bg-success btn-success btn-md w-auto fs-12 py-1 px-3 d-flex align-items-center" title="Se déconnecter" onclick="event.preventDefault();
                document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out me-1" aria-hidden="true"></i> 
                    <span class="d-none d-sm-inline">Se déconnecter</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
			</li>
        </ul>
      </div>
    </nav>
  </header>
