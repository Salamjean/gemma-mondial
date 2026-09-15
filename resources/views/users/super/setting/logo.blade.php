@extends('layouts.dashboard')

@section('content')
    <div class="card">
        <div class="card-header">
            Changer le logo et favicon
        </div>
        <div class="card-body">
            <div class="fs-24" style="text-decoration: underline;">Informations actuelles</div>
            <div class="row pt-15">
                <div class="col-md-4 ">
                    <div class="border-icon">

                    <div class="fs-18">Icone de la page</div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset(iconsLoad()['logo']) }}?v={{ time() }}" alt="logo" height="150" />
                    </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-icon">

                    <div class="fs-18">Icone pendant le chargement des pages</div>
                    <div class="d-flex justify-content-center">
                        <img src="{{ asset(iconsLoad()['loading']) }}?v={{ time() }}" alt="loading" height="150" />
                    </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-icon">
                        <div class="fs-18">Favicon</div>
                        <div class="d-flex justify-content-center">
                            <img src="{{ asset(iconsLoad()['favicon']) }}?v={{ time() }}" alt="fav" height="150" />
                        </div>
                    </div>
                </div>

            </div>
            <div class="fs-24 pt-25" style="text-decoration: underline;">Modifier les données</div>
            <form method="post" action="{{ route('super.setting.updateIcon') }}" enctype="multipart/form-data">
                @csrf
                <div class="row pt-15">
                    <div class="col-md-4 ">
                        <div class="border-icon">

                            <div class="fs-18">Icone de la page</div>
                            <div class="py-25">
                                <input class="form-control" type="file" name="logo">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-icon">

                            <div class="fs-18">Icone pendant le chargement des pages</div>
                            <div class="py-25">
                                <input class="form-control" type="file" name="loading">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-icon">
                            <div class="fs-18">Favicon</div>
                            <div class="py-25">
                                <input class="form-control" type="file" name="favicon">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti-save-alt"></i> Enregister
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection

@push('js')
    <script>

    </script>
@endpush

@push('css')
    <style>
        .image-upload .thumb .profilePicUpload {
            font-size: 0;
            opacity: 0;
        }
        .border-icon{
            border: 1px solid rgba(0, 0, 0, 0.24);
            border-radius: 8px;
            padding: 10px;
        }
    </style>
@endpush
