<div class="row mb-20">
    <div class="col-12">
        <div class="box box-body py-10 px-15 mb-0 shadow-sm border-0">
            <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2">
                <li class="nav-item">
                    <a class="nav-link py-10 px-15 {{ request()->routeIs('accountant.accounting.dashboard') ? 'active bg-primary fw-600' : 'bg-light text-dark' }}" 
                       href="{{ route('accountant.accounting.dashboard') }}">
                        <i class="ti-dashboard me-2"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-10 px-15 {{ request()->routeIs('accountant.accounting.journaux') ? 'active bg-primary fw-600' : 'bg-light text-dark' }}" 
                       href="{{ route('accountant.accounting.journaux') }}">
                        <i class="ti-book me-2"></i> Journaux d'écritures
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-10 px-15 {{ request()->routeIs('accountant.accounting.balance') ? 'active bg-primary fw-600' : 'bg-light text-dark' }}" 
                       href="{{ route('accountant.accounting.balance') }}">
                        <i class="ti-stats-up me-2"></i> Balance Générale
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-10 px-15 {{ request()->routeIs('accountant.accounting.grand_livre') ? 'active bg-primary fw-600' : 'bg-light text-dark' }}" 
                       href="{{ route('accountant.accounting.grand_livre') }}">
                        <i class="ti-agenda me-2"></i> Grand Livre
                    </a>
                </li>
                @if(auth()->user()->role_as == 'accountant')
                <li class="nav-item">
                    <a class="nav-link py-10 px-15 {{ request()->routeIs('accountant.accounting.plan_comptable') ? 'active bg-primary fw-600' : 'bg-light text-dark' }}" 
                       href="{{ route('accountant.accounting.plan_comptable') }}">
                        <i class="ti-list me-2"></i> Plan Comptable
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-10 px-15 {{ request()->routeIs('accountant.accounting.export_sage') ? 'active bg-primary text-white fw-bold' : 'bg-light text-primary fw-600' }}" 
                       href="{{ route('accountant.accounting.export_sage') }}">
                        <i class="ti-export me-2 "></i> Passerelle Sage SAARI
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
