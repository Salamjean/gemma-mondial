@extends('layouts.dashboard')



@section('content')

    @if ($type != 'laboratoire' && $type != 'examen-laboratoire')
        @include('users.doctor.consultation.formulaire.consultation.entete')
    @endif

    @if ($type == 'laboratoire' || $type == 'examen-laboratoire')
        @include('users.doctor.consultation.formulaire.consultation.laboratoire')
    @elseif ($type == 'consultation' || $type == 'currative')
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
