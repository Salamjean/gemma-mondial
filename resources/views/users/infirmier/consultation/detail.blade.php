@extends('layouts.dashboard', ['title' => 'Détail de la consultation'])
@section('content')
    <div class="box">
        <div class="content-header mb-20">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"><i class="fa fa-user"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <h4 class="page-title"><span class="fw-bold" style="color: blue;">{{ $consultation->patient->code_patient }}</span>
                                    <span class="fw-bold"> | {{ $consultation->patient->user->name }} {{ $consultation->patient->user->prenom }}</span>
                                    </h4>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-4 col-xlg-3 col-md-5">
                <div class="box">
                    <div class="profile-image mb-10" style="text-align: center">
                        @if ($consultation->patient->gender == 'masculin')
                            <img src="{{ asset('assets/images/avatar/6.png') }}" class="box-shadowed rounded-circle"
                                alt="Photo de profil" />
                        @elseif($consultation->patient->gender == 'feminin')
                            <img src="{{ asset('assets/images/avatar/2.png') }}" class="box-shadowed rounded-circle"
                                alt="Photo de profil" />
                        @else
                            <img src="{{ asset('assets/uploads/patient/$item->img_url') }}"
                                class="box-shadowed rounded-circle" alt="Photo de profil" />
                        @endif
                    </div>
                    <div class="box-body">
                        <div class="row text-center mt-10">
                            <div class="col-md-6 border-end">
                                <strong>Nom & prénom(s)</strong>
                                <p>{{ $consultation->patient->user->name }} {{ $consultation->patient->user->prenom }}</p>
                            </div>
                            <div class="col-md-6"><strong>Profession</strong>
                                <p>{{ $consultation->patient->profession }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center mt-10">
                            <div class="col-md-6 border-end"><strong>Email</strong>
                                <p>{{ $consultation->patient->user->email }}</p>
                            </div>
                            <div class="col-md-6"><strong>Téléphone</strong>
                                <p>{{ $consultation->patient->telephone }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center mt-10">
                            <div class="col-md-12"><strong>Residence actuelle</strong>
                                <p>{{ $consultation->patient->currentResidence->name }}
                                    <br> {{ $consultation->patient->habitualResidence->name }}
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center mt-10">
                            <div class="col-md-6 border-end"><strong>Date de naissance</strong>
                                <p>{{ $consultation->patient->birth_date }}</p>
                            </div>
                            <div class="col-md-6"><strong>Lieu de naissance</strong>
                                <p>{{ $consultation->patient->birthPlace->name }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row text-center mt-10">
                            <div class="col-md-12"><strong>Pièce d'identité</strong>
                                <p style="text-transform: uppercase"><span
                                        class="fw-400">{{ $consultation->patient->type_piece }}</span>
                                    <br> {{ $consultation->patient->numero_identite }}
                                </p>
                            </div>
                        </div>
                        <hr>
                        <br>

                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xlg-9 col-md-7">
                <div class="box">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 border-end"> <strong>Code consultation</strong>
                                <br>
                                <p class="text-muted">{{ $consultation->code_consultation }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 border-end"> <strong>Date consultation</strong>
                                <br>
                                <p class="text-muted">{{ DateFr($consultation->date_consultation) }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 border-end"> <strong>Type de visite</strong>
                                <br>
                                <p class="text-muted">{{ optional(optional($consultation->prestationHospital)->prestationService)->libelle ?? ($consultation->prestationHospital->serviceHospital->service->libelle ?? 'Consultation') }}</p>

                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Issue consultation</strong>
                                <br>
                                <p class="text-muted">{{ $consultation->issue_consultation }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12 col-xs-12"> <strong>Observation</strong>
                            <br>
                            <p class="mt-30">{{ $consultation->observation }}</p>
                        </div>

                        <!---Liste des ordonnances prescrites -->
                        <h4 class="box-title fw-500 py-20 border-bottom d-block">Ordonnance prescrite @if (App\Models\Ordonnance::where('consultation_id', $consultation->id)->exists())
                                : <span
                                    class="fw-bold">{{ App\Models\Ordonnance::where('consultation_id', $consultation->id)->first()->reference }}</span>
                            @endif
                        </h4>
                        @if (App\Models\Ordonnance::where('consultation_id', $consultation->id)->exists())
                            @foreach (App\Models\Ordonnance::where('consultation_id', $consultation->id)->first()->prescriptions as $item)
                                <div class="d-flex no-block fa fa-check-circle text-success">
                                    <h6 class="ms-10 text-dark">{{ $item->medicament }}</h6>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <h6 class="ms-10 text-dark fw-600">( {{ $item->frequence }} )</h6>
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex no-block fa fa-check-circle text-danger">
                                <h6 class="ms-10 text-danger">Pas d'ordonnance prescrite !</h6>
                            </div>
                        @endif
                        <!---Liste des examens prescris -->
                        <h4 class="box-title my-20 fw-500 py-20 border-bottom d-block">Examen(s) prescrit(s) @if (App\Models\BulletinExamen::where('consultation_id', $consultation->id)->exists())
                                : <span
                                    class="fw-bold">{{ App\Models\BulletinExamen::where('consultation_id', $consultation->id)->first()->code_bulletin }}</span>
                            @endif
                        </h4>
                        @if (App\Models\BulletinExamen::where('consultation_id', $consultation->id)->exists())
                            @foreach (App\Models\BulletinExamen::where('consultation_id', $consultation->id)->first()->examens as $item)
                                <div class="d-flex no-block fa fa-check-circle text-success">
                                    <h6 class="ms-10 text-dark">{{ $item->nature_examen }}</h6>
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex no-block fa fa-check-circle text-danger">
                                <h6 class="ms-10 text-danger">Liste d'examens vide !</h6>
                            </div>
                        @endif
                        <!---arret de travail déclaré -->
                        <h4 class="box-title my-20 fw-500 py-20 border-bottom d-block">Arret de travail</h4>
                        @if (App\Models\ArretTravail::where('consultation_id', $consultation->id)->exists())
                            @foreach (App\Models\ArretTravail::where('consultation_id', $consultation->id)->get() as $item)
                                <div class="d-flex no-block text-success">
                                    Code: <span class="ms-10 text-dark fw-bold">{{ $item->code }}</span>&nbsp;&nbsp;|
                                    Date début: <span
                                        class="ms-10 text-white badge badge-primary">{{ formatDate($item->date_debut) }}</span>&nbsp;&nbsp;|
                                    Date fin: <span
                                        class="ms-10 text-white badge badge-danger">{{ formatDate($item->date_fin) }}</span>&nbsp;&nbsp;|
                                    Nombre de jour: <span
                                        class="ms-10 text-white badge badge-info">{{ $item->nb_jour }}</span>&nbsp;&nbsp;|
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex no-block fa fa-check-circle text-danger">
                                <h6 class="ms-10 text-danger">Pas d'arret de travail déclaré !</h6>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
