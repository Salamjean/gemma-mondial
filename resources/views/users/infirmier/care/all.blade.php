@extends('layouts.dashboard', ['title' => 'Tous les soins en attente'])

@section('content')
    <div class="box border-0 shadow-sm rounded-20 bg-white">
        <div class="box-header with-border p-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h3 class="box-title fw-bold text-dark mb-1 fs-20">
                    <i class="fa-solid fa-notes-medical text-success me-2"></i> Tous les Soins Infirmier en Attente
                </h3>
                <p class="text-muted mb-0 fs-13">
                    Liste intégrale de tous les soins demandés en attente d'exécution, sans filtrage par date.
                </p>
            </div>
            <div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-10 px-15 py-8 fw-semibold">
                    <i class="fa-solid fa-house me-1"></i> Tableau de bord
                </a>
            </div>
        </div>

        <div class="box-body p-20">
            <div class="table-responsive">
                <table id="example1" class="table table-striped table-hover display nowrap margin-top-10 w-p100 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="bb-2">Date & Heure</th>
                            <th class="bb-2">Code Patient</th>
                            <th class="bb-2">Nom du Patient</th>
                            <th class="bb-2">Acte Médical</th>
                            <th class="bb-2">Motif</th>
                            <th class="bb-2 text-center">Statut</th>
                            <th class="bb-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cares as $item)
                            @php
                                $isToday = \Carbon\Carbon::parse($item->created_at)->isToday();
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge {{ $isToday ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} fw-bold fs-12 px-2 py-1 rounded-pill">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <b>{{ $item->admission->patient->code_patient ?? 'N/A' }}</b>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-14">
                                        {{ $item->admission->patient->user->name ?? '' }} {{ $item->admission->patient->user->prenom ?? '' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light-primary text-primary fw-semibold fs-12">
                                        {{ $item->admission->prestationHospital->prestationService->libelle ?? 'Soin Infirmier' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark fs-13"><i>{{ $item->admission->motif_consultation ?? 'Non renseigné' }}</i></span>
                                </td>
                                <td class="text-center">
                                    @if ($item->status === 'pending')
                                        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">En attente</span>
                                    @elseif ($item->status === 'payment_pending')
                                        <span class="badge bg-info text-white fw-bold px-3 py-2 rounded-pill">Attente paiement</span>
                                    @elseif ($item->status === 'payment_success')
                                        <span class="badge bg-success fw-bold px-3 py-2 rounded-pill">Paiement effectué</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($item->status === 'pending')
                                        <a href="{{ route('infirmier.care.formulaire', $item->id) }}" class="btn btn-sm btn-primary rounded-8 fw-semibold px-15 shadow-sm">
                                            <i class="fa-solid fa-play me-1"></i> Commencer
                                        </a>
                                    @elseif ($item->status === 'payment_pending')
                                        <a href="{{ route('infirmier.care.detail', $item->id) }}" class="btn btn-sm btn-info text-white rounded-8 fw-semibold px-15">
                                            <i class="fa-solid fa-eye me-1"></i> Détail
                                        </a>
                                    @elseif ($item->status === 'payment_success')
                                        <a href="{{ route('infirmier.care.payment_success', $item->id) }}" class="btn btn-sm btn-success rounded-8 fw-semibold px-15">
                                            <i class="fa-solid fa-forward me-1"></i> Continuer
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted fs-14">
                                    <i class="fa-solid fa-check-circle fs-24 text-success d-block mb-2"></i>
                                    Aucun soin en attente dans la file globale.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
