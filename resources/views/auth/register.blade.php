@extends('layouts.auth',['title' => "S'incrire"])

@section('content')

<div class="col-12">
    <div class="row justify-content-center g-0">
        <div class="col-lg-5 col-md-5 col-12">
            <div class="bg-white rounded10 shadow-lg">
                <div class="content-top-agile p-20 pb-0">
                    <h2 class="text-primary">Formulaire d'incription</h2>
                    <p class="mb-0">Créer un administrateur</p>
                </div>
                <div class="p-40">
                    <form action="{{ route('register') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror ps-15 bg-transparent" name="name" id="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Nom et prénoms">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-transparent"><i class="ti-email"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror ps-15 bg-transparent" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-transparent"><i class="ti-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror ps-15 bg-transparent" name="password" id="password" required autocomplete="password" placeholder="Mot de passe">
                                <span class="input-group-text bg-transparent text-muted toggle-pwd" data-target="password" style="cursor: pointer;" title="Afficher / Masquer">
                                    <i class="fa-solid fa-eye-slash"></i>
                                </span>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <span class="input-group-text bg-transparent"><i class="ti-lock"></i></span>
                                <input type="password" class="form-control ps-15 bg-transparent" name="password_confirmation" id="password-confirm" required autocomplete="new-password" placeholder="Confirmer le mot de passe">
                                <span class="input-group-text bg-transparent text-muted toggle-pwd" data-target="password-confirm" style="cursor: pointer;" title="Afficher / Masquer">
                                    <i class="fa-solid fa-eye-slash"></i>
                                </span>
                            </div>
                        </div>
                          <div class="row">
                            <div class="col-12">
                              <div class="checkbox">
                                <input type="checkbox" id="basic_checkbox_1" >
                                <label for="basic_checkbox_1">J'accepte les <a href="#" class="text-warning"><b>Conditions</b></a></label>
                              </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-12 text-center">
                              <button type="submit" class="btn btn-info margin-top-10">S'incrire</button>
                            </div>
                            <!-- /.col -->
                          </div>
                    </form>
                    <div class="text-center">
                        <p class="mt-15 mb-0">Vous avez déjà un compte ?<a href="{{ route('login') }}" class="text-danger ms-5"> Se connecter</a></p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="mt-20 text-white">- S'incrire avec -</p>
                <p class="gap-items-2 mb-20">
                    <a class="btn btn-social-icon btn-round btn-facebook" href="#"><i class="ti-facebook"></i></a>
                    <a class="btn btn-social-icon btn-round btn-twitter" href="#"><i class="ti-twitter"></i></a>
                    <a class="btn btn-social-icon btn-round btn-instagram" href="#"><i class="ti-instagram"></i></a>
                  </p>
              </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-pwd').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (input && icon) {
                    const isPwd = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPwd ? 'text' : 'password');
                    if (isPwd) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                }
            });
        });
    });
</script>
@endpush
