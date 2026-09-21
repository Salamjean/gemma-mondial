<li class="treeview">
    <a href="#" class="{{ routeActive(['accountant.recette.today','accountant.recette.historique']) }}">
        <i class="fa-solid {{ routeActive(['accountant.recette.today','accountant.recette.historique']) }} fa-id-card">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Recette</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('accountant.recette.du_jour') }}" class="{{ routeActive('accountant.recette.du_jour') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Recette du jour
            </a>
        </li>
        <li>
            <a href="{{ route('accountant.recette.historique') }}" class="{{ routeActive('accountant.recette.historique') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
    </ul>
</li>

<li class="{{ routeActive('accountant.accounting.expenses') }}">
    <a href="{{ route('accountant.accounting.expenses') }}">
        <i class="fa-solid fa-money-bill-wave">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Dépenses & Charges</span>
    </a>
</li>

<li class="{{ routeActive('accountant.accounting.assurances_suivi') }}">
    <a href="{{ route('accountant.accounting.assurances_suivi') }}">
        <i class="fa-solid fa-hand-holding-dollar">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Assurances & Recouv.</span>
    </a>
</li>

<li class="{{ routeActive('accountant.accounting.journaux') }}">
    <a href="{{ route('accountant.accounting.journaux') }}">
        <i class="fa-solid fa-book-journal-whills">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Journaux d'écritures</span>
    </a>
</li>

<li class="{{ routeActive('accountant.accounting.balance') }}">
    <a href="{{ route('accountant.accounting.balance') }}">
        <i class="fa-solid fa-scale-balanced">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Balance Générale</span>
    </a>
</li>

<li class="{{ routeActive('accountant.accounting.grand_livre') }}">
    <a href="{{ route('accountant.accounting.grand_livre') }}">
        <i class="fa-solid fa-book-open">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Grand Livre</span>
    </a>
</li>

<li class="{{ routeActive('accountant.accounting.plan_comptable') }}">
    <a href="{{ route('accountant.accounting.plan_comptable') }}">
        <i class="fa-solid fa-list-ol">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Plan Comptable</span>
    </a>
</li>

<li class="{{ routeActive('accountant.accounting.export_sage') }}">
    <a href="{{ route('accountant.accounting.export_sage') }}">
        <i class="fa-solid fa-file-export text-success">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span><strong>Passerelle Sage SAARI</strong></span>
    </a>
</li>
