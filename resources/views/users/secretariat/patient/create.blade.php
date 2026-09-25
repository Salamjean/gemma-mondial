@extends('layouts.dashboard', ['title' => 'Ajouter un Patient'])

@section('content')

    <div class="row">
        <div class="col-12">
            <!-- Validation wizard -->
            <div class="box bb-3 border-danger">
                <div class="box-header with-border">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="box-title fw-bold fs-22 fs-md-26 text-dark"><i class="fa-solid fa-user-plus text-primary me-2"></i> Enregistrement d'un nouveau patient</h4>
                            <h6 class="box-subtitle mb-0 text-muted">Avant d'enregistrer un nouveau patient, vous pouvez vérifier si le dossier existe déjà via la recherche.</h6>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 w-100 w-md-auto">
                            <a href="{{ route('secretariat.search_hospitalisation') }}" class="btn btn-dark btn-sm rounded-10 flex-fill flex-md-grow-0 text-center shadow-xs">
                                <i class="fa fa-search me-1"></i> Rechercher Patient
                            </a>
                            <button class="btn btn-primary btn-sm rounded-10 flex-fill flex-md-grow-0 shadow-xs" id="seeForm">
                                <i class="fa fa-plus-circle me-1"></i> Formulaire
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form add patient-->
            @include('users.secretariat.patient._inc.add')
            <!-- End Form add patient-->

        </div>

        <script>
            $(document).ready(function () {
                // Gérer le clic sur le bouton
                $("#seeForm").click(function () {
                    // Afficher/masquer le formulaire en fonction de son état actuel
                    $("#formAdd").toggle();
                    $('#formUpdate').hide();
                });
            });
        </script>

    </div>
    <style>
        .f-right {
            float: right;
        }

        .p-search {
            background-image: url("{{ asset('assets/images/bg/bg-search.png') }}");
            border-radius: 10px;
            margin: 10px auto;
        }

        .btn-submit {
            float: right;
        }
    </style>
@endsection