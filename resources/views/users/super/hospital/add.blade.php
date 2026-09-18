@extends('layouts.dashboard')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="">
                            <h4 class="box-title">ENREGISTREMENT D'UN HOPITAL</h4>
                        </div>
                        <div class="">
                            <a href="{{ route('super.hospital.index') }}" class="btn btn-success btn-md shadow">Liste des
                                Hôpitaux</a>
                        </div>
                    </div>
                </div>
                <!-- /.box-header -->
                <form class="form" action="{{ route('super.hospital.store') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <h4 class="box-title text-success mb-0"><i class="ti-user me-15"></i> Infos sur le l'hôpital</h4>
                        <hr class="my-15">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="label" class="form-label">Hôpital<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="label" name="label"
                                        class="form-control @error('label') is-invalid @enderror"
                                        value="{{ old('label') }}" required autocomplete="label" autofocus
                                        placeholder="Nom de l'hopital">
                                    @error('label')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="reference" class="form-label">Code<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="reference" name="reference"
                                        class="form-control @error('reference') is-invalid @enderror"
                                        value="{{ old('reference') }}" required autocomplete="reference" autofocus
                                        placeholder="Code de l'etablissement">
                                    @error('reference')
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
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="address" class="form-label">localité<span
                                            class="text-danger fw-bold">*</span></label>
                                    <select class="form-select text-uppercase" id="address" name="address" required
                                        style="width: 100%;">
                                        <option value="">----</option>
                                        @foreach ($city as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="district" class="form-label">District sanitaire<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="text" id="district" name="district"
                                        class="form-control @error('district') is-invalid @enderror" required
                                        autocomplete="district" autofocus placeholder="District sanitaire">
                                    @error('district')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="direction_generale" class="form-label">Direction générale</label>
                                    <input type="text" id="direction_generale" name="direction_generale"
                                        class="form-control @error('direction_generale') is-invalid @enderror" autofocus
                                        placeholder="Nom de la direction générale">
                                    @error('direction_generale')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="image"
                                        class="form-label @error('image') is-invalid @enderror">Logo</label>
                                    <input class="form-control" type="file" id="img_url" name="image">
                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row mt-10">
                            <div class="col-12">
                                <div class="form-group">
                                    <div class="form-check form-switch d-flex align-items-center gap-3 p-10 bg-light rounded-10 border">
                                        <input class="form-check-input ms-0" type="checkbox" role="switch" id="is_teleconsultation_active" name="is_teleconsultation_active" value="1" {{ old('is_teleconsultation_active', '1') == '1' ? 'checked' : '' }} style="width: 2.5em; height: 1.3em; cursor: pointer;">
                                        <label class="form-check-label fw-bold text-dark mb-0 ms-2" for="is_teleconsultation_active" style="cursor: pointer;">
                                            <i class="fa-solid fa-headset text-success me-1"></i> Activer le service de Téléconsultation
                                            <span class="text-muted fw-normal ms-2">- Permet aux médecins et infirmiers de cet hôpital d'utiliser la téléconsultation.</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h4 class="box-title text-success mb-0 mt-20"><i class="ti-lock  me-15"></i> Infos de connexion
                        </h4>
                        <hr class="my-15">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">E-mail<span class="text-danger fw-bold">*</span></label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required autocomplete="email"
                                        placeholder="Adresse email">
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
                                    <label for="password_confirmation" class="form-label">Confirmation du mot de
                                        passe<span class="text-danger fw-bold">*</span></label>
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
