@extends('layouts.dashboard')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10 col-12">
        <div class="box">
            <div class="box-header with-border d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="box-title fw-bold text-dark"><i class="fa fa-user-edit text-primary me-2"></i> Modifier un administrateur UCP-FM</h3>
                    <p class="text-muted mb-0 small">{{ $admin->user->name ?? '' }} {{ $admin->user->prenom ?? '' }}</p>
                </div>
                <div>
                    <a href="{{ route('super.ucpfm.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fa fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                </div>
            </div>

            <form action="{{ route('super.ucpfm.update', $admin->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="box-body">
                    @include('users.super.ucpfm._form')
                </div>

                <div class="box-footer text-end">
                    <a href="{{ route('super.ucpfm.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary px-4 shadow">
                        <i class="fa fa-save me-1"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
