@extends('layouts.dashboard')



@section('content')

    @include('users.doctor.consultation.formulaire.consultation.entete')

    @if ($type == 'consultation')
        @include('users.doctor.consultation.formulaire.consultation.currative')
    @elseif ($type == 'consultation-pre-natale')
        @include('users.doctor.consultation.formulaire.consultation.pre-natale')
    @elseif ($type == 'consultation-post-natale')
        @include('users.doctor.consultation.formulaire.consultation.post-natale')
    @elseif ($type == 'accouchement')
        @include('users.doctor.consultation.formulaire.consultation.accouchement')
    @else
        @include('users.doctor.consultation.formulaire.consultation.currative')
    @endif

@endsection
