@extends('layouts.dashboard', ['title' => 'Ajouter un Patient'])

@section('content')

    <div class="row">
        <div class="col-12">
            <!-- Validation wizard -->
            <div class="box bb-3 border-danger">
                <div class="box-header with-border">
                    <div>
                        <h4 class="box-title fw-bold fs-28">Enregistrement d'un nouveau patient</h4>
                        <h6 class="box-subtitle">Avant d'enregistrer un nouveau patient, vous pouvez vérifier si le patient
                            est déjà enregistré en cliquant sur le bouton <i class="fw-bold">"Rechercher Patient"</i> et
                            faire la mise à jour .</h6>
                        <div class="float-end">
                            <a href="{{ route('dashboard')}}">
                                <button class="btn btn-dark"><i class="fa fa-search"></i>&nbsp;&nbsp;Rechercher
                                    Patient</button>
                            </a>
                            <button class="btn btn-primary" id="seeForm"><i class="fa fa-plus-circle"
                                    aria-hidden="true"></i>&nbsp;&nbsp;Ajouter un nouveau</button>
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