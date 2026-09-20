@extends('layouts.dashboard')


@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-10 col-sm-10 col-10">
            <div class="box">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <h4 class="box-title">ENREGISTREMENT D'UNE CAISSIERE</h4>
                        </div>
                        <div class="">
                            <a href="{{ route('hospital.cashier.index') }}" class="btn btn-success btn-md shadow">Liste des caissieres</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <form class="form" action="{{ route('hospital.cashier.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <h4 class="box-title text-success mb-0"><i class="ti-user me-15"></i> Infos Caissière</h4>
                        <hr class="my-15">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="matricule" class="form-label">Matricule<span class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="matricule" name="matricule" class="form-control @error('matricule') is-invalid @enderror" value="{{ old('matricule') }}" required autocomplete="matricule" autofocus placeholder="Matricule du caissière">
                                    @error('matricule')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom & Prénom(s)<span class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nom et prénoms caissière">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact" class="form-label">Contact<span class="text-danger fw-bold">*</span></label>
                                    <div class="d-flex">
                                        <span class="form-control w-80 text-center align-center" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">+225</span>
                                        <input
                                            type="text"
                                            id="contact"
                                            style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: none;"
                                            min="10"
                                            max="10"
                                            name="contact"
                                            class="form-control @error('contact') is-invalid @enderror"
                                            value="{{ old('contact') }}"
                                            required autocomplete="contact"
                                            autofocus placeholder="0101010101" data-inputmask="'mask': ['9999999999', '99 99 99 99 99']" data-mask=""
                                        >
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

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address" class="form-label">Adresse<span class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required autocomplete="address" autofocus placeholder="Adresse de la caissière">
                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image" class="form-label @error('image') is-invalid @enderror">Image</label>
                                    <input class="form-control" type="file" id="img_url" name="image">
                                    @error('image')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h4 class="box-title text-success mb-0 mt-20"><i class="ti-settings me-15"></i> Rôle & Fonctionnalités</h4>
                        <hr class="my-15">
                        <div class="row mb-15">
                            <div class="col-md-12">
                                <div class="p-3 bg-light rounded border">
                                    <div class="form-check form-switch ps-0">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" name="is_accueil" id="is_accueil" value="1" {{ old('is_accueil') ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                                            <label class="form-check-label fw-bold text-dark fs-15 mb-0" for="is_accueil">
                                                <i class="fa-solid fa-hospital-user text-primary me-1"></i>
                                                Gérer également l'accueil et l'admission des patients
                                            </label>
                                        </div>
                                    </div>
                                    <div class="text-muted fs-12 mt-1 ps-5">
                                        Si cette option est activée, la caissière aura accès à l'onglet <b>Patients & Accueil</b> dans son espace pour pouvoir enregistrer de nouveaux patients et effectuer des affectations en consultation / examen.
                                    </div>
                                </div>
                            </div>
                        </div>

                        @include('users.hospital.planning',['status' => 'store'])

                        <h4 class="box-title text-success mb-0 mt-20"><i class="ti-lock  me-15"></i> Infos de connexion</h4>
                        <hr class="my-15">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">E-mail<span class="text-danger fw-bold">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" placeholder="Adresse email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="password" class="form-label">Mot de passe<span class="text-danger fw-bold">*</span></label>
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="password" placeholder="Entrez le mot de passe">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirmation du mot de passe<span class="text-danger fw-bold">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('confirmation_password') is-invalid @enderror" required autocomplete="confirmation_password" placeholder="Entrez le mot de passe de confirmation">
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
                                <p class="text-dark fw-bold"><span class="text-danger fw-bold">*</span>Obligatoires</p>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-end">
                        <button type="button" class="btn btn-warning me-1" onclick="history.back()">
                            <i class="ti-arrow-left"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti-save-alt"></i> Enregister
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.box -->
        </div>
    </div>
@endsection

