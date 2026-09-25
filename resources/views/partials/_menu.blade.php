<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <!-- Logo Desktop -->
        <div class="d-flex align-items-center logo-box justify-content-start d-md-block d-none">
            <a href="{{ route('dashboard') }}" class="logo">
                <div class="logo">
                    <span class="light-logo">
                        <img src="{{ asset(iconsLoad()['logo']) }}" alt="logo">
                    </span>
                </div>
            </a>
        </div>
        <!-- En-tête Sidebar Mobile avec Logo et bouton Fermer -->
        <div class="sidebar-mobile-header d-flex d-md-none align-items-center justify-content-between px-3 py-2 border-bottom bg-white">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset(iconsLoad()['logo']) }}" alt="logo" style="max-height: 32px; width: auto;">
            </a>
            <button type="button" class="btn btn-sm btn-light sidebar-close-btn p-0" data-toggle="push-menu" aria-label="Fermer le menu" style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fa fa-times text-secondary"></i>
            </button>
        </div>
        <div class="user-profile my-15 px-20 py-10 b-1 rounded10 mx-15" style="background-color: orange;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="image d-flex align-items-center">
                    @if (Illuminate\Support\Facades\Auth::user()->role_as == 'super')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->admin)->img_url != null)
                            <img src="{{ asset('assets/uploads/super/' . optional(Illuminate\Support\Facades\Auth::user()->admin)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/root.png') }}" class="rounded-0 me-10" alt="User Image">
                        @endif
                    @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'hospital')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->hospital)->img_url != null)
                            <img src="{{ asset('assets/uploads/hospital/' . optional(Illuminate\Support\Facades\Auth::user()->hospital)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/hospital.gif') }}" class="rounded-0 me-10"
                                alt="User Image">
                        @endif
                    @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'doctor')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->doctor)->img_url != null)
                            <img src="{{ asset('assets/uploads/doctor/' . optional(Illuminate\Support\Facades\Auth::user()->doctor)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/doctor.png') }}" class="rounded-0 me-10"
                                alt="User Image">
                        @endif
                    @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'infirmier')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->infirmier)->img_url != null)
                            <img src="{{ asset('assets/uploads/infirmier/' . optional(Illuminate\Support\Facades\Auth::user()->infirmier)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/doctor.png') }}" class="rounded-0 me-10"
                                alt="User Image">
                        @endif
                    @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'pharmacy')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->pharmacy)->img_url != null)
                            <img src="{{ asset('assets/uploads/pharmacy/' . optional(Illuminate\Support\Facades\Auth::user()->pharmacy)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/doctor.png') }}" class="rounded-0 me-10"
                                alt="User Image">
                        @endif
                    @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'secretariat')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->secretariat)->img_url != null)
                            <img src="{{ asset('assets/uploads/secretariat/' . optional(Illuminate\Support\Facades\Auth::user()->secretariat)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/secretary.png') }}" class="rounded-0 me-10"
                                alt="User Image">
                        @endif
                    @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'accountant')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->accountant)->img_url != null)
                            <img src="{{ asset('assets/uploads/accountant/' . optional(Illuminate\Support\Facades\Auth::user()->accountant)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/secretary.png') }}" class="rounded-0 me-10"
                                alt="User Image">
                        @endif
                    @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'cashier')
                        @if (optional(Illuminate\Support\Facades\Auth::user()->cashier)->img_url != null)
                            <img src="{{ asset('assets/uploads/cashier/' . optional(Illuminate\Support\Facades\Auth::user()->cashier)->img_url) }}"
                                class="rounded-0 me-10" alt="User Image">
                        @else
                            <img src="{{ asset('assets/uploads/cashier.png') }}" class="rounded-0 me-10"
                                alt="User Image">
                        @endif
                    @endif
                    <div>
                        @if (Illuminate\Support\Facades\Auth::user()->role_as != 'hospital')
                            <h6 class="mb-0 fw-600">{{ Illuminate\Support\Facades\Auth::user()->name }}
                                {{ Illuminate\Support\Facades\Auth::user()->prenom }}</h6>
                        @else
                            <h6 class="mb-0 fw-600">{{ Illuminate\Support\Facades\Auth::user()->hospital->label }}</h6>
                        @endif
                        <p class="mb-0">
                            @if (Illuminate\Support\Facades\Auth::user()->role_as == 'doctor')
                                @if (Illuminate\Support\Facades\Auth::user()->doctor->typeAgent->libelle == 'Médecin')
                                    Medécin {{ Illuminate\Support\Facades\Auth::user()->doctor->type_name }}
                                @elseif (Illuminate\Support\Facades\Auth::user()->doctor->typeAgent->libelle == 'Sage femme')
                                    Sage femme
                                @elseif (Illuminate\Support\Facades\Auth::user()->doctor->typeAgent->libelle == 'Infirmier')
                                    Infirmier
                                @endif
                            @else
                                {{ roleFr(Illuminate\Support\Facades\Auth::user()->role_as) }}
                            @endif
                        </p>

                    </div>
                </div>
                <div class="info">
                    <a class="dropdown-toggle p-15 d-grid" data-bs-toggle="dropdown" href="#"></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        @if (Illuminate\Support\Facades\Auth::user()->role_as == 'super')
                            <a class="dropdown-item" href="{{ route('super.profile') }}"><i class="ti-user"></i>
                                Profile</a>
                        @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'hospital')
                            <a class="dropdown-item" href="{{ route('hospital.profile') }}"><i class="ti-user"></i>
                                Profile</a>
                        @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'doctor')
                            <a class="dropdown-item" href="{{ route('doctor.profile') }}"><i class="ti-user"></i>
                                Profile</a>
                        @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'secretariat')
                            <a class="dropdown-item" href="{{ route('secretariat.profile') }}"><i class="ti-user"></i>
                                Profile</a>
                        @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'accountant')
                            <a class="dropdown-item" href="{{ route('accountant.profile') }}"><i class="ti-user"></i>
                                Profile</a>
                        @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'cashier')
                            <a class="dropdown-item" href="{{ route('cashier.profile') }}"><i class="ti-user"></i>
                                Profile</a>
                        @elseif(Illuminate\Support\Facades\Auth::user()->role_as == 'infirmier')
                            <a class="dropdown-item" href="{{ route('infirmier.profile') }}"><i class="ti-user"></i>
                                Profile</a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i
                                class="ti-lock"></i> Quitter</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="multinav">
            <div class="multinav-scroll" style="height: 97%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ routeActive('dashboard') }}">
                            <i class="{{ routeActive('dashboard') }} icon-Layout-4-blocks">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            <span>Tableau de bord</span>
                        </a>
                    </li>
                    @if (auth()->user()->role_as == 'super')
                        @include('partials.inc._super')
                    @endif

                    @if (auth()->user()->role_as == 'hospital')
                        @include('partials.inc._hospital')
                    @endif

                    @if (auth()->user()->role_as == 'secretariat')
                        @include('partials.inc._secretariat')
                    @endif

                    @if (auth()->user()->role_as == 'accountant')
                        @include('partials.inc._accountant')
                    @endif


                    @if (auth()->user()->role_as == 'cashier')
                        @include('partials.inc._cashier')
                    @endif

                    @if (auth()->user()->role_as == 'doctor')
                        @include('partials.inc._doctor')
                    @endif

                    @if (auth()->user()->role_as == 'infirmier')
                        @include('partials.inc._infirmier')
                    @endif

                    @if (auth()->user()->role_as === 'pharmacy')
                        @include('partials.inc._pharmacist')
                    @endif

                    @if (auth()->user()->role_as === 'ministere')
                        @include('partials.inc._ministere')
                    @endif

                    @if (auth()->user()->role_as !== 'super' && auth()->user()->role_as !== 'ministere')
                    <li class="treeview">
                        <a href="#" class="{{ routeActive('permission.status') }}">
                            <i class="fa-solid fa-shield {{ routeActive('permission') }}">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            </i>
                            <span>Permission</span>
                            <span class="pull-right-container">
                                <i class="fa-solid fa-angle-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (auth()->user()->role_as !== 'super' &&
                                    auth()->user()->role_as !== 'hospital')
                                <li>
                                    <a href="{{ route('permission') }}" class="{{ routeActive('permission') }}">
                                        <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i>Permission
                                    </a>
                                </li>
                            @endif

                            @if (auth()->user()->role_as === 'hospital')
                                <li>
                                    <a href="{{ route('permission.status', 'pending') }}"> <i
                                            class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i>Permission en attente
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('permission.status', 'agree') }}"> <i class="icon-Commit"><span
                                                class="path1"></span><span class="path2"></span></i>Permission
                                        succès
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('permission.status', 'reject') }}"> <i
                                            class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i>Permission annulées
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if (auth()->user()->role_as !== 'super' &&
                            auth()->user()->role_as !== 'hospital' &&
                            auth()->user()->role_as !== 'secretariat' &&
                            auth()->user()->role_as !== 'ministere')
                        <li class="treeview">
                            <a href="#" class="{{ routeActive('planning') }}">
                                <i class="fa-solid fa-hourglass {{ routeActive('planning') }}">
                                    <span class="path1"></span><span class="path2"></span><span
                                        class="path3"></span>
                                </i>
                                <span>Planning</span>
                                <span class="pull-right-container">
                                    <i class="fa-solid fa-angle-right"></i>
                                </span>
                            </a>


                            <ul class="treeview-menu">
                                <li>
                                    <a href="{{ route('planning') }}" class="{{ routeActive('planning') }}">
                                        <i class="icon-Commit"><span class="path1"></span><span
                                                class="path2"></span></i>Planning
                                    </a>
                                </li>

                            </ul>
                        </li>
                    @endif
                    @if (auth()->user()->role_as === 'hospital')
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                    @endif


                </ul>
            </div>
        </div>
    </section>
</aside>
