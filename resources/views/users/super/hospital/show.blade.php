@extends('layouts.dashboard')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="row">


                        <div class="d-flex justify-content-end" style="gap: 10px">
                            <a href="{{ route('super.hospital.index') }}" class="btn btn-primary btn-md shadow">Retour à la
                                liste</a>
                        </div>
                    </div>
                </div>

                <div class="box-body fs-14">
                    <h4 class="box-title text-primary mb-0"><i class="ti-user me-15"></i> Informations | <span
                            class="text-uppercase" style="color: brown;">{{ $hospital->label }}</span></h4>
                    <hr class="my-15">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="{{ asset("assets/uploads/hospital/$hospital->img_url") }}" alt="Image de profil"
                                class="img-thumbnail mt-3">
                        </div>
                        <div class="col-md-9 py-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Reference :</strong> {{ $hospital->reference }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Responsable :</strong> {{ $hospital->user->name }}
                                        {{ $hospital->user->prenom }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Contact :</strong> {{ $hospital->contact }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>E-mail :</strong> {{ $hospital->user->email }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Adresse :</strong> {{ $hospital->localiteH->name ?? null}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label">
                                        <strong>Téléconsultation :</strong> 
                                        @if($hospital->is_teleconsultation_active)
                                            <span class="badge bg-success-light text-success fw-bold px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i> Activée</span>
                                        @else
                                            <span class="badge bg-danger-light text-danger fw-bold px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i> Désactivée</span>
                                        @endif
                                        <a href="javascript:void(0);" 
                                           class="btn btn-xs btn-toggle-teleconsultation {{ $hospital->is_teleconsultation_active ? 'btn-outline-danger' : 'btn-outline-success' }} ms-2 rounded-pill"
                                           data-url="{{ route('super.hospital.toggle_teleconsultation', $hospital->id) }}"
                                           data-name="{{ addslashes($hospital->label ?? 'cet établissement') }}"
                                           data-active="{{ $hospital->is_teleconsultation_active ? '1' : '0' }}">
                                            <i class="fa-solid fa-power-off me-1"></i> {{ $hospital->is_teleconsultation_active ? 'Désactiver' : 'Activer' }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <h4 class="box-title text-success pt-25"><i class="ti-user me-15"></i> Modifier les données
                            </h4>
                            <hr class="my-0">
                            <form class="form pt-20" action="{{ route('super.hospital.update', $hospital->id) }}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="label" class="form-label">Nom Hopital <span
                                                    class="text-danger fw-bold">*</span></label>
                                            <input type="text" id="label" name="label"
                                                class="form-control @error('label') is-invalid @enderror"
                                                value="{{ $hospital->label }}" required autocomplete="label" autofocus
                                                placeholder="Nom de l'hopital">
                                            @error('label')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact" class="form-label">Contact<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <div class="d-flex">
                                                <span class="form-control w-80 text-center align-center"
                                                    style="border-top-right-radius: 0; border-bottom-right-radius: 0;">+225</span>
                                                <input type="text" id="contact"
                                                    style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;"
                                                    min="10" max="10" name="contact"
                                                    class="form-control @error('contact') is-invalid @enderror"
                                                    value="{{ $hospital->contact }}" required autocomplete="contact"
                                                    autofocus placeholder="Contact">
                                            </div>
                                            @error('contact')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="direction_generale" class="form-label">Direction Générale<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <input type="text" id="direction_generale" name="direction_generale"
                                                class="form-control @error('direction_generale') is-invalid @enderror"
                                                value="{{ $hospital->nom_direction_generale }}" required
                                                autocomplete="name" autofocus placeholder="Direction Générale">
                                            @error('direction_generale')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="district" class="form-label">District<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <input type="text" id="district" name="district"
                                                class="form-control @error('district') is-invalid @enderror"
                                                value="{{ $hospital->district_sanitaire }}" required
                                                autocomplete="district" autofocus placeholder="District Sanitaire">
                                            @error('district')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address" class="form-label">localité<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <select class="form-select text-uppercase" id="address" name="address"
                                                required style="width: 100%;">
                                                <option value="">----</option>
                                                @foreach ($cities as $item)
                                                    <option value="{{ $item->id }}" @if($hospital->localite == $item->id) selected @endif>{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Service Téléconsultation</label>
                                            <div class="form-check form-switch d-flex align-items-center gap-3 p-10 bg-light rounded-10 border">
                                                <input class="form-check-input ms-0" type="checkbox" role="switch" id="is_teleconsultation_active_edit" name="is_teleconsultation_active" value="1" {{ old('is_teleconsultation_active', $hospital->is_teleconsultation_active) ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                                                <label class="form-check-label fw-bold text-dark mb-0 ms-2" for="is_teleconsultation_active_edit">
                                                    <i class="fa-solid fa-headset text-success me-1"></i> Téléconsultation active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h4 class="box-title text-success mb-0 mt-20"><i class="ti-lock  me-15"></i> Infos de
                                    connexion</h4>
                                <hr class="my-15">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">E-mail<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ $hospital->user->email }}" autocomplete="email"
                                                placeholder="Adresse email">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="image"
                                                class="form-label @error('image') is-invalid @enderror">Image</label>
                                            <input class="form-control" type="file" id="img_url" name="image">
                                            @error('image')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="form-label">Mot de passe</label>
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                autocomplete="password" placeholder="Entrez le mot de passe">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation" class="form-label">Confirmation du mot de
                                                passe</label>
                                            <input type="password" id="password_confirmation"
                                                name="password_confirmation"
                                                class="form-control @error('confirmation_password') is-invalid @enderror"
                                                autocomplete="confirmation_password"
                                                placeholder="Entrez le mot de passe de confirmation">
                                            @error('password_confirmation')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-dark fw-bold"><span
                                                class="text-danger fw-bold">*</span>Obligatoires</p>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                                <div class="box-footer text-end">

                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti-save-alt"></i> Modifier
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- /.box -->
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
