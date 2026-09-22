@extends('layouts.dashboard', ['title' => 'Logs Système & Audit Trail'])

@section('content')
<div class="row">
    <!-- Cartes KPI Statistiques -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-primary-light">
            <div class="box-body d-flex align-items-center">
                <div class="me-15 bg-primary text-white w-50 h-50 rounded10 d-flex align-items-center justify-content-center fs-24">
                    <i class="fa fa-history"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalAudits, 0, ',', ' ') }}</h3>
                    <span class="text-muted fs-13">Actions Traçées</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-success-light">
            <div class="box-body d-flex align-items-center">
                <div class="me-15 bg-success text-white w-50 h-50 rounded10 d-flex align-items-center justify-content-center fs-24">
                    <i class="fa fa-download"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalDownloads, 0, ',', ' ') }}</h3>
                    <span class="text-muted fs-13">Téléchargements & Exports</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-info-light">
            <div class="box-body d-flex align-items-center">
                <div class="me-15 bg-info text-white w-50 h-50 rounded10 d-flex align-items-center justify-content-center fs-24">
                    <i class="fa fa-desktop"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($uniqueMachines, 0, ',', ' ') }}</h3>
                    <span class="text-muted fs-13">Postes & Adresses MAC</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="box bg-danger-light">
            <div class="box-body d-flex align-items-center">
                <div class="me-15 bg-danger text-white w-50 h-50 rounded10 d-flex align-items-center justify-content-center fs-24">
                    <i class="fa fa-server"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $laravelLogSize }}</h3>
                    <span class="text-muted fs-13">Taille Log Laravel</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border">
                <ul class="nav nav-tabs customtab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'audit' ? 'active' : '' }}" href="{{ route('super.logs.index', ['tab' => 'audit']) }}" role="tab">
                            <i class="fa-solid fa-user-shield me-2 text-primary"></i>
                            <span class="fw-bold">Journal d'Audit Trail & Traçabilité</span>
                            <span class="badge badge-primary ms-2">{{ $auditLogs->total() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'laravel' ? 'active' : '' }}" href="{{ route('super.logs.index', ['tab' => 'laravel']) }}" role="tab">
                            <i class="fa-solid fa-terminal me-2 text-danger"></i>
                            <span class="fw-bold">Logs Système Laravel</span>
                            <span class="badge badge-danger ms-2">{{ count($laravelLogs) }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="box-body">
                @if($activeTab === 'audit')
                    <!-- ================= ONGLET 1 : AUDIT TRAIL ================= -->
                    <form action="{{ route('super.logs.index') }}" method="GET" class="mb-20">
                        <input type="hidden" name="tab" value="audit">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2 col-6">
                                <label class="form-label fs-12 fw-bold">Date Début</label>
                                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fs-12 fw-bold">Date Fin</label>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fs-12 fw-bold">Hôpital</label>
                                <select name="hospital_id" class="form-select form-select-sm">
                                    <option value="ALL">Tous les hôpitaux</option>
                                    @foreach($hospitals as $hosp)
                                        <option value="{{ $hosp->id }}" {{ $selectedHospital == $hosp->id ? 'selected' : '' }}>{{ $hosp->label ?? $hosp->nom_direction_generale ?? 'Hôpital #' . $hosp->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label fs-12 fw-bold">Type d'Action</label>
                                <select name="action_type" class="form-select form-select-sm">
                                    <option value="ALL">Toutes les actions</option>
                                    <option value="ENREGISTREMENT" {{ $selectedAction === 'ENREGISTREMENT' ? 'selected' : '' }}>Enregistrement</option>
                                    <option value="MODIFICATION" {{ $selectedAction === 'MODIFICATION' ? 'selected' : '' }}>Modification</option>
                                    <option value="SUPPRESSION" {{ $selectedAction === 'SUPPRESSION' ? 'selected' : '' }}>Suppression</option>
                                    <option value="TELECHARGEMENT_PDF" {{ $selectedAction === 'TELECHARGEMENT_PDF' ? 'selected' : '' }}>Téléchargement PDF</option>
                                    <option value="EXPORT_EXCEL" {{ $selectedAction === 'EXPORT_EXCEL' ? 'selected' : '' }}>Export Excel</option>
                                    <option value="EXPORT_SAGE" {{ $selectedAction === 'EXPORT_SAGE' ? 'selected' : '' }}>Export Sage (.txt)</option>
                                    <option value="CONNEXION" {{ $selectedAction === 'CONNEXION' ? 'selected' : '' }}>Connexion</option>
                                </select>
                            </div>
                            <div class="col-md-3 col-8">
                                <label class="form-label fs-12 fw-bold">Recherche (IP, MAC, Utilisateur...)</label>
                                <input type="text" name="search_audit" value="{{ $searchAudit }}" class="form-control form-control-sm" placeholder="ex: 192.168, E4:5F, nom...">
                            </div>
                            <div class="col-md-1 col-4 d-flex gap-1">
                                <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold" title="Filtrer">
                                    <i class="fa fa-search"></i>
                                </button>
                                <a href="{{ route('super.logs.export_audit_excel', request()->all()) }}" class="btn btn-sm btn-success fw-bold" title="Exporter en Excel">
                                    <i class="fa-solid fa-file-excel"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 140px;">Date & Heure</th>
                                    <th>Utilisateur</th>
                                    <th>Hôpital</th>
                                    <th class="text-center" style="width: 130px;">Action</th>
                                    <th>Module</th>
                                    <th>Description & Objet</th>
                                    <th style="width: 120px;">Adresse IP</th>
                                    <th style="width: 150px;">Adresse MAC</th>
                                    <th>Poste / Navigateur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($auditLogs as $log)
                                    <tr>
                                        <td class="fs-12 text-muted fw-semibold">
                                            <i class="fa fa-clock text-secondary me-1"></i>
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $log->user_name }}</div>
                                            <span class="badge badge-sm badge-secondary">{{ strtoupper($log->user_role) }}</span>
                                        </td>
                                        <td>
                                            @if($log->hospital)
                                                <span class="fw-semibold text-primary">{{ $log->hospital->label ?? $log->hospital->nom_direction_generale ?? 'Hôpital' }}</span>
                                            @else
                                                <span class="badge badge-light text-muted">Système / Admin</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(str_contains($log->action_type, 'TELECHARGEMENT') || str_contains($log->action_type, 'EXPORT'))
                                                <span class="badge bg-success"><i class="fa fa-download me-1"></i> {{ $log->action_type }}</span>
                                            @elseif($log->action_type === 'ENREGISTREMENT')
                                                <span class="badge bg-primary"><i class="fa fa-plus-circle me-1"></i> ENREGISTREMENT</span>
                                            @elseif($log->action_type === 'SUPPRESSION')
                                                <span class="badge bg-danger"><i class="fa fa-trash me-1"></i> SUPPRESSION</span>
                                            @elseif($log->action_type === 'MODIFICATION')
                                                <span class="badge bg-warning text-dark"><i class="fa fa-edit me-1"></i> MODIFICATION</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $log->action_type }}</span>
                                            @endif
                                        </td>
                                        <td><span class="badge badge-outline badge-primary">{{ $log->module ?: 'GENERAL' }}</span></td>
                                        <td class="fs-13">{{ $log->description }}</td>
                                        <td>
                                            <code><i class="fa fa-network-wired text-muted me-1"></i>{{ $log->ip_address }}</code>
                                        </td>
                                        <td>
                                            <code class="fw-bold text-dark"><i class="fa fa-laptop text-primary me-1"></i>{{ $log->mac_address }}</code>
                                        </td>
                                        <td class="fs-11 text-muted" title="{{ $log->device_info }}">
                                            {{ \Illuminate\Support\Str::limit($log->device_info, 30) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-40 text-muted">
                                            <i class="fa fa-info-circle fa-2x mb-2 d-block text-secondary"></i>
                                            Aucune trace d'audit enregistrée sur cette période.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-20 d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-13">Affichage de {{ $auditLogs->firstItem() ?? 0 }} à {{ $auditLogs->lastItem() ?? 0 }} sur {{ $auditLogs->total() }} actions</span>
                        {{ $auditLogs->appends(request()->all())->links() }}
                    </div>

                @else
                    <!-- ================= ONGLET 2 : LOGS LARAVEL ================= -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-20">
                        <form action="{{ route('super.logs.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                            <input type="hidden" name="tab" value="laravel">
                            
                            <select name="log_level" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
                                <option value="ALL" {{ $levelFilter === 'ALL' ? 'selected' : '' }}>Tous les niveaux</option>
                                <option value="ERROR" {{ $levelFilter === 'ERROR' ? 'selected' : '' }}>🔴 ERROR / CRITICAL</option>
                                <option value="WARNING" {{ $levelFilter === 'WARNING' ? 'selected' : '' }}>🟡 WARNING</option>
                                <option value="INFO" {{ $levelFilter === 'INFO' ? 'selected' : '' }}>🔵 INFO</option>
                                <option value="DEBUG" {{ $levelFilter === 'DEBUG' ? 'selected' : '' }}>⚪ DEBUG</option>
                            </select>

                            <input type="text" name="search_log" value="{{ $searchLog }}" class="form-control form-control-sm" placeholder="Rechercher dans les erreurs..." style="width: 250px;">
                            <button type="submit" class="btn btn-sm btn-primary fw-bold"><i class="fa fa-search me-1"></i> Filtrer</button>
                        </form>

                        <div class="d-flex gap-2">
                            <a href="{{ route('super.logs.download_laravel') }}" class="btn btn-sm btn-info fw-bold">
                                <i class="fa fa-download me-1"></i> Télécharger laravel.log
                            </a>
                            <form action="{{ route('super.logs.clear_laravel') }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir vider le fichier des logs Laravel ?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger fw-bold">
                                    <i class="fa fa-trash me-1"></i> Vider les Logs
                                </button>
                            </form>
                        </div>
                    </div>

                    @if(empty($laravelLogs))
                        <div class="alert alert-success text-center py-30 mb-0">
                            <i class="fa fa-check-circle fa-2x mb-2 d-block"></i>
                            <strong>Aucune erreur ou log correspondant trouvé.</strong> Le système fonctionne sans accroc !
                        </div>
                    @else
                        <div class="accordion" id="accordionLaravelLogs">
                            @foreach($laravelLogs as $index => $logItem)
                                @php
                                    $badgeClass = match($logItem['level']) {
                                        'ERROR', 'CRITICAL', 'EMERGENCY', 'ALERT' => 'bg-danger text-white',
                                        'WARNING' => 'bg-warning text-dark',
                                        'INFO', 'NOTICE' => 'bg-info text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <div class="accordion-item mb-10 border rounded">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button collapsed py-12 px-15 fs-13" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                            <span class="badge {{ $badgeClass }} me-2">{{ $logItem['level'] }}</span>
                                            <span class="text-muted fs-12 me-3"><i class="fa fa-clock me-1"></i> {{ $logItem['date'] }}</span>
                                            <strong class="text-dark text-truncate me-auto">{{ \Illuminate\Support\Str::limit($logItem['message'], 100) }}</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionLaravelLogs">
                                        <div class="accordion-body bg-dark text-light p-15 rounded-bottom font-monospace fs-12" style="overflow-x: auto;">
                                            <div class="text-danger fw-bold mb-10">{{ $logItem['message'] }}</div>
                                            @if(!empty($logItem['stack_trace']))
                                                <pre class="text-secondary mb-0" style="white-space: pre-wrap; word-break: break-all; max-height: 400px; overflow-y: auto;">{{ $logItem['stack_trace'] }}</pre>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
