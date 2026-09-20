@php
    $infServiceId = optional(optional(optional(\Illuminate\Support\Facades\Auth::user()->infirmier)->serviceHospital)->service)->id;
@endphp
@if ($infServiceId == 4)

<li class="treeview">

    <a href="#"
        class="{{ routeActive(['infirmier.care.new', 'infirmier.care.all', 'infirmier.care.cours', 'infirmier.care.history']) }}">
        <i
            class="fa-solid {{ routeActive(['infirmier.care.new', 'infirmier.care.all', 'infirmier.care.cours', 'infirmier.care.history']) }} fa-thermometer-half">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Soins infirmier</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('infirmier.care.new') }}" class="{{ routeActive('infirmier.care.new') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Nouvelles
            </a>
        </li>

        <li>
            <a href="{{ route('infirmier.care.all') }}" class="{{ routeActive('infirmier.care.all') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Tous les soins (Sans filtre)
            </a>
        </li>

        <li>
            <a href="{{ route('infirmier.care.history') }}"
                class="{{ routeActive('infirmier.care.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
    </ul>
</li>

@else
<li class="treeview">

    <a href="#"
        class="{{ routeActive(['infirmier.consultation.today', 'infirmier.consultation.all', 'infirmier.consultation.cours', 'infirmier.consultation.history']) }}">
        <i
            class="fa-solid {{ routeActive(['infirmier.consultation.today', 'infirmier.consultation.all', 'infirmier.consultation.cours', 'infirmier.consultation.history']) }} fa-thermometer-half">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Consultations</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('infirmier.consultation.today') }}" class="{{ routeActive('infirmier.consultation.today') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Nouvelles
            </a>
        </li>

        <li>
            <a href="{{ route('infirmier.consultation.all') }}" class="{{ routeActive('infirmier.consultation.all') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Toutes les consultations
            </a>
        </li>

        <li>
            <a href="{{ route('infirmier.consultation.history') }}"
                class="{{ routeActive('infirmier.consultation.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
    </ul>
</li>

@endif

@php
    $infHosp = optional(auth()->user()->infirmier)->hospital ?? \App\Models\Hospital::find(optional(auth()->user()->infirmier)->hospital_id ?? auth()->user()->hospital_id);
    $isTeleconsultActive = $infHosp ? ($infHosp->is_teleconsultation_active ?? true) : true;
@endphp
@if($isTeleconsultActive)
<li>
    <a href="{{ route('infirmier.consultation.teleconsultations') }}"
        class="{{ routeActive('infirmier.consultation.teleconsultations') }}">
        <i class="fa-solid fa-video {{ routeActive('infirmier.consultation.teleconsultations') }}">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Téléconsultations</span>
        @php
            $hospId = optional(auth()->user()->infirmier)->hospital_id ?? auth()->user()->hospital_id ?? 1;
            $pendingTeleconsultCount = \App\Models\Consultation::where('hospital_id', $hospId)
                ->where(function($q) {
                    $q->where('orientation_infirmier', 'teleconsultation')
                      ->orWhereNotNull('desired_date');
                })
                ->where('status', 0)
                ->whereDate('desired_date', date('Y-m-d'))
                ->count();
        @endphp
        @if($pendingTeleconsultCount > 0)
            <span class="pull-right-container">
                <span class="badge bg-danger fs-10 px-2 py-0.5 rounded-pill">{{ $pendingTeleconsultCount }}</span>
            </span>
        @endif
    </a>
</li>
@endif

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['infirmier.hospitalisation.in_progress', 'infirmier.hospitalisation.history']) }}">
        <i
            class="fa-solid {{ routeActive(['infirmier.hospitalisation.in_progress', 'infirmier.hospitalisation.history']) }} fa-bed">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <span>Hospitalisations</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('infirmier.hospitalisation.in_progress') }}"
                class="{{ routeActive('infirmier.hospitalisation.in_progress') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>En cours
            </a>
        </li>
        <li>
            <a href="{{ route('infirmier.hospitalisation.history') }}"
                class="{{ routeActive('infirmier.hospitalisation.history') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Historique
            </a>
        </li>
    </ul>
</li>

<li class="treeview">
    <a href="#"
        class="{{ routeActive(['secretariat.search_hospitalisation', 'secretariat.patient.list']) }}">
        <i class="fa-solid fa-hospital-user">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <span>Gestion Patients</span>
        <span class="pull-right-container">
            <i class="fa-solid fa-angle-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
        <li>
            <a href="{{ route('secretariat.search_hospitalisation') }}" class="{{ routeActive('secretariat.search_hospitalisation') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Vérifier / Rechercher
            </a>
        </li>
        <li>
            <a href="{{ route('secretariat.patient.list') }}" class="{{ routeActive('secretariat.patient.list') }}">
                <i class="icon-Commit"><span class="path1"></span><span class="path2"></span></i>Liste des patients
            </a>
        </li>
    </ul>
</li>
