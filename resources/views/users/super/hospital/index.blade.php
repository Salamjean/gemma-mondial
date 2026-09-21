@extends('layouts.dashboard')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box">
                <div class="box-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <h4 class="box-title">Hopital</h4>
                        </div>
                        <div class="">
                            <a href="{{ route('super.hospital.add') }}" class="btn btn-success btn-md shadow">Ajouter un hopital</a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                            <thead>
                            <tr>
                                <th class="bb-2">N°</th>
                                <th class="bb-2">Photo</th>
                                <th class="bb-2">Reference</th>
                                <th class="bb-2">Hopital</th>
                                <th class="bb-2">Contact</th>
                                <th class="bb-2">Crée le</th>
                                <th class="bb-2 text-center">Statut</th>
                                <th class="bb-2 text-center">Téléconsultation</th>
                                <th class="bb-2 text-center">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($hospitals as $item)
                                <tr>
                                    <td class="text-dark fw-bold fs-6">#{{ $loop->index + 1 }}</td>
                                    <td>
                                        @if($item->img_url == null)
                                            <img src="{{ asset('assets/uploads/hospital.gif') }}" class="avatar avatar-lg rounded10" alt="Photo de profil"/>
                                        @else
                                            <img src="{{ asset("assets/uploads/hospital/$item->img_url")}}" class="avatar avatar-lg rounded10" alt="Photo de profil"/>
                                        @endif
                                    </td>
                                    <td>{{ $item->reference }}</td>
                                    <td>
                                        {{ $item->label }}
                                    </td>
                                    <td>
                                        {{ $item->contact }}
                                    </td>
                                    <td>
                                        <span class="btn btn-sm btn-primary">{{ $item->created_at->format('d/m/Y - H:i:s') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 0)
                                            <span class="badge bg-success">Activé</span>
                                        @else
                                            <span class="badge bg-danger">Désactivé</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item->is_teleconsultation_active ?? true)
                                            <span class="badge bg-light-success text-success border border-success fw-bold px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Activée</span>
                                        @else
                                            <span class="badge bg-light-danger text-danger border border-danger fw-bold px-2 py-1"><i class="fa-solid fa-ban me-1"></i> Désactivée</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('super.hospital.report', $item->id) }}" 
                                           class="btn btn-sm btn-primary shadow-sm" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="bottom" 
                                           title="Ouvrir et superviser cet établissement">
                                            <i class="fa-solid fa-hospital me-1"></i> <strong>Ouvrir</strong>
                                        </a>
                                        <a href="javascript:void(0);" 
                                           class="btn btn-sm btn-toggle-teleconsultation {{ ($item->is_teleconsultation_active ?? true) ? 'btn-teal' : 'btn-secondary' }}" 
                                           style="{{ ($item->is_teleconsultation_active ?? true) ? 'background-color: #0d9488; color: white;' : '' }}" 
                                           data-url="{{ route('super.hospital.toggle_teleconsultation', $item->id) }}"
                                           data-name="{{ addslashes($item->label ?? 'Hôpital') }}"
                                           data-active="{{ ($item->is_teleconsultation_active ?? true) ? '1' : '0' }}"
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="bottom" 
                                           title="{{ ($item->is_teleconsultation_active ?? true) ? 'Désactiver la téléconsultation' : 'Activer la téléconsultation' }}">
                                            <i class="fa-solid fa-headset"></i>
                                        </a>
                                        <a href="{{ route('super.hospital.status',$item->id) }}" class="btn btn-sm {{ $item->status == 0 ? 'btn-success' : 'btn-danger' }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Activer/Désactiver l'hôpital"><i class="fa-solid {{ $item->status == 0 ? 'fa-eye-slash' : 'fa-eye' }}"></i></a>
                                        <a href="{{ route('super.hospital.show',$item->id) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Modifier les coordonnées"><i class="fa-solid fa-pen-to-square"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ $empty }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 Script pour la confirmation de l'activation/désactivation de la téléconsultation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('click', '.btn-toggle-teleconsultation', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var name = $(this).data('name') || 'cet établissement';
            var isActive = $(this).data('active') == '1';

            var title = isActive ? 'Désactiver la téléconsultation ?' : 'Activer la téléconsultation ?';
            var text = isActive 
                ? `Voulez-vous vraiment désactiver la téléconsultation pour l'établissement <b>${name}</b> ?<br><small class="text-muted mt-2 d-block">Les infirmiers et médecins de cet hôpital n'auront plus accès au module de téléconsultation.</small>`
                : `Voulez-vous activer la téléconsultation pour l'établissement <b>${name}</b> ?<br><small class="text-muted mt-2 d-block">Le personnel de santé aura accès aux consultations vidéo et aux fonctionnalités associées.</small>`;
            var confirmText = isActive ? '<i class="fa-solid fa-power-off me-1"></i> Oui, désactiver' : '<i class="fa-solid fa-circle-check me-1"></i> Oui, activer';
            var confirmColor = isActive ? '#dc3545' : '#0d9488';
            var icon = isActive ? 'warning' : 'question';

            Swal.fire({
                title: title,
                html: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmText,
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                focusCancel: isActive,
                customClass: {
                    popup: 'rounded-16 shadow-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endsection

