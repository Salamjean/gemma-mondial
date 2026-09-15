@extends('layouts.dashboard')



@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10 col-md-10 col-sm-10 col-10">
            <div class="box">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <h4 class="box-title">ENREGISTREMENT D'UN INFIRMIER</h4>
                        </div>
                        <div class="">
                            <a href="{{ route('hospital.infirmier.index') }}"
                                class="btn btn-success btn-md shadow">Liste</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <form class="form" action="{{ route('hospital.infirmier.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <h4 class="box-title text-success mb-0"><i class="ti-user me-15"></i> Infos</h4>
                        <hr class="my-15">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="matricule" class="form-label">Matricule<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="matricule" name="matricule"
                                        class="form-control @error('matricule') is-invalid @enderror"
                                        value="{{ old('matricule') }}" required autocomplete="matricule" autofocus
                                        placeholder="Matricule">
                                    @error('matricule')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom & Prénom(s)<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        required autocomplete="name" autofocus placeholder="Nom et prénoms">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
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
                                            value="{{ old('contact') }}" required autocomplete="contact" autofocus
                                            placeholder="0101010101"
                                            data-inputmask="'mask': ['9999999999', '99 99 99 99 99']" data-mask="">
                                    </div>
                                    @error('contact')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="services" class="form-label">Services attribués<span class="text-danger fw-bold">*</span></label>
                                    <select class="form-select select2 text-uppercase" id="services" name="services[]" multiple required style="width: 100%;">
                                        @foreach ($service as $item)
                                            <option value="{{ $item->id }}">{{ $item->service?->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address" class="form-label">Adresse<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="address" name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        value="{{ old('address') }}" required autocomplete="address" autofocus
                                        placeholder="Adresse de l'infirmier">
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

                        @include('users.hospital.planning', ['status' => 'store'])

                        <h4 class="box-title text-success mb-0 mt-20"><i class="ti-lock  me-15"></i> Infos de connexion</h4>
                        <hr class="my-15">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">E-mail<span class="text-danger fw-bold">*</span></label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                        required autocomplete="email" placeholder="Adresse email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="password" class="form-label">Mot de passe<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" required
                                        autocomplete="password" placeholder="Entrez le mot de passe">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirmation du mot de passe<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control @error('confirmation_password') is-invalid @enderror" required
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