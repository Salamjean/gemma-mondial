@extends('layouts.dashboard')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-10 col-sm-10 col-10">
            <div class="box">
                <div class="box-header with-border">
                    <div class="row">


                        <div class="d-flex justify-content-end" style="gap: 10px">
                            <a href="{{ route('hospital.cashier.index') }}" class="btn btn-primary btn-md shadow">Retour à la
                                liste</a>
                        </div>
                    </div>
                </div>

                <div class="box-body fs-14">
                    <h4 class="box-title text-primary mb-0"><i class="ti-list me-15"></i> Informations </h4>
                    <hr class="my-15">
                    <div class="row">
                        <div class="col-md-3">
                            <img src="{{ asset("assets/uploads/cashier/$caissiere->img_url") }}" alt="Image de profil"
                                class="rounded-circle">
                        </div>
                        <div class="col-md-9 py-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Matricule :</strong> <span
                                            style="color:red;">{{ $caissiere->matricule }}</span></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Nom & Prénom :</strong> {{ $caissiere->user->name }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Contact :</strong> {{ $caissiere->contact }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>E-mail :</strong> {{ $caissiere->user->email }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Adresse :</strong> {{ $caissiere->address }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label"><strong>Disponibilité :</strong> {{ dayIndexNameString(json_decode($caissiere->user->availability->days)) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-label">
                                        <strong>Gestion Accueil :</strong>
                                        @if ($caissiere->is_accueil)
                                            <span class="badge badge-success"><i class="fa-solid fa-check-circle me-1"></i> Activée (Accueil & Patients)</span>
                                        @else
                                            <span class="badge badge-secondary"><i class="fa-solid fa-times-circle me-1"></i> Désactivée (Caisse seule)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <h4 class="box-title text-success pt-25"><i class="ti-user me-15"></i> Modifier les données
                            </h4>
                            <hr class="my-0">
                            <form class="form pt-20" action="{{ route('hospital.cashier.update', $caissiere->id) }}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nom & Prénom(s)<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <input type="text" id="name" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ $caissiere->user->name }}" required autocomplete="name" autofocus
                                                placeholder="Nom et prénoms du docteur">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="address" class="form-label">Adresse<span
                                                    class="text-danger fw-bold">*</span></label>
                                            <input type="text" id="address" name="address"
                                                class="form-control @error('address') is-invalid @enderror"
                                                value="{{ $caissiere->address }}" required autocomplete="address" autofocus
                                                placeholder="Adresse du docteur">
                                            @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="contact" class="form-label">Contact<span
                                                class="text-danger fw-bold">*</span></label>
                                        <div class="d-flex">
                                            <span class="form-control w-80 text-center align-center"
                                                style="border-top-right-radius: 0; border-bottom-right-radius: 0;">+225</span>
                                            <input type="text" id="contact"
                                                style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;"
                                                min="10" max="10" name="contact"
                                                class="form-control @error('contact') is-invalid @enderror"
                                                value="{{ $caissiere->contact }}" required autocomplete="contact" autofocus
                                                placeholder="Contact">
                                        </div>
                                        @error('contact')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                @include('users.hospital.planning', ['status' => 'update', 'planning' => $caissiere->user->availability])
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
                                                value="{{ $caissiere->user->email }}" disabled autocomplete="email"
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
                                <div class="row mb-15">
                                    <div class="col-md-12">
                                        <div class="p-3 bg-light rounded border">
                                            <div class="form-check form-switch ps-0">
                                                <div class="d-flex align-items-center">
                                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="is_accueil" id="is_accueil_up" value="1" {{ old('is_accueil', $caissiere->is_accueil) ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                                                    <label class="form-check-label fw-bold text-dark fs-15 mb-0" for="is_accueil_up">
                                                        <i class="fa-solid fa-hospital-user text-primary me-1"></i>
                                                        Gérer également l'accueil et l'admission des patients
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="text-muted fs-12 mt-1 ps-5">
                                                Si cette option est activée, la caissière aura accès à l'onglet <b>Patients & Accueil</b> dans son espace pour enregistrer des patients et effectuer des affectations.
                                            </div>
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
@endsection
