@extends('layouts.dashboard',['title' => $title])
@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="box">
                <div class="box-header with-border">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h4 class="box-title text-dark fw-bold mb-0"><i class="ti-user text-primary me-2"></i> Profil Secrétaire</h4>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm rounded-10 shadow-xs">
                            <i class="fa-solid fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="box-body fs-14">
                    <h4 class="box-title text-primary mb-0"><i class="ti-user me-15"></i> Informations</h4>
                    <hr class="my-15">
                    <div class="row g-3">
                        <div class="col-12 col-md-4 col-lg-3 text-center">
                            <div class="form-group">
                                <div class="image-upload">
                                    <div class="avatar-preview">
                                    <div class="profilePicPreview" style="max-height:200px;">
                                            <img src="{{ asset("assets/uploads/secretariat/$secretaire->img_url")}}" alt="Image de profil" class="img-thumbnail mt-2" style="max-width: 160px;">
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8 col-lg-9 py-10">
                            <div class="row g-2">
                                <div class="col-12 col-sm-6">
                                    <div class="form-label"><strong>Matricule : </strong><span style="color:red;">{{ $secretaire->matricule }}</span></div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-label"><strong>Nom & Prenoms :</strong> {{$secretaire->user->name }}</div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-label"><strong>Email :</strong> {{
                                        $secretaire->user->email }}</div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-label"><strong>Contact :</strong> {{ $secretaire->contact }}
                                    </div>
                                </div>
                            </div>
                            <h4 class="box-title text-success pt-25 mb-0"><i class="ti-user me-15"></i> Modifier les données
                            </h4>
                            <hr class="my-15">   
                            <form class="form" action="{{ route('secretariat.update') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-2 g-md-3">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="label" class="form-label">Nom <span
                                                    class="text-danger fw-bold">*</span></label>
                                            <input type="text" id="label" name="name"
                                                class="form-control @error('label') is-invalid @enderror"
                                                value="{{$secretaire->user->name}}"  autocomplete="label"
                                                autofocus placeholder="Nom&Prenoms Sécretaire" >
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
                                                    value="{{ $secretaire->contact }}" 
                                                    autocomplete="contact" autofocus placeholder="Contact">
                                            </div>
                                            @error('contact')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <h4 class="box-title text-success mb-0 mt-20"><i class="ti-lock  me-15"></i> Infos de connexion</h4>
                                <hr class="my-15">
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">E-mail<span class="text-danger fw-bold">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $secretaire->user->email }}" disabled autocomplete="email" placeholder="Adresse email">
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="password" class="form-label">Mot de passe</label>
                                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="password" placeholder="Entrez le mot de passe">
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="password_confirmation" class="form-label">Confirmation du mot de passe</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('confirmation_password') is-invalid @enderror" autocomplete="confirmation_password" placeholder="Entrez le mot de passe de confirmation">
                                            @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                             </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="text-dark fw-bold"><span class="text-danger fw-bold">*</span>Obligatoires</p>
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
        </div>
        <!-- /.box -->
    </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/src/js/app.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/bootstrap-select/dist/js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/select2/dist/js/select2.full.js') }}"></script>
    <script src="{{ asset('assets/src/js/pages/advanced-form-element.js') }}"></script>
@endpush