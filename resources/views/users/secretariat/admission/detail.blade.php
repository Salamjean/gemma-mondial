@extends('layouts.dashboard',['title' => "Détail Admission"])

@section('content')
      <!-- Main content -->
      <section class="content">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ back()->getTargetUrl() }}" class="btn btn-primary float-end mb-10"><i class="fa fa-arrow-left me-1"></i> Retour en arrière</a>
            </div>
        </div>
          <div class="row clearfix">
              <div class="col-lg-4 col-md-4">
                  <div class="box profile-header">
                      <div class="box-body text-center">
                          <div class="profile-image mb-10">
                              @if ($admission->patient && $admission->patient->img_url != null && file_exists(public_path('assets/uploads/patient/' . $admission->patient->img_url)))
    								<img src="{{ asset('assets/uploads/patient/'. $admission->patient->img_url) }}"
    									class="rounded-circle" alt="Photo de profil" style="width:128px; height:128px; object-fit: cover;" />
    							@else
    							    @if ($admission->patient && (strtolower($admission->patient->gender) == 'masculin' || strtolower($admission->patient->gender) == 'm'))
    								<img src="{{ asset('assets/images/avatar/6.png') }}" class="avatar avatar-lg rounded10"
    									alt="Photo de profil" />
        							@else
        								<img src="{{ asset('assets/images/avatar/2.png') }}" class="avatar avatar-lg rounded10"
        									alt="Photo de profil" />
        							@endif
    							@endif

                            </div>
                          <div>
                              <h3 class="mb-0"><strong>{{ $admission->patient->user->name ?? '' }}</strong> {{ $admission->patient->user->prenom ?? '' }}</h3>
                              <span class="job_post">{{ $admission->patient->profession ?? '' }}</span>
                              <p class="mb-0">{{ $admission->patient->currentResidence->name ?? ($admission->patient->residenceActuelle->name ?? ($admission->patient->residence ?? '')) }}<br></p>
                          </div>
                      </div>
                      <div class="p-10">
                        <hr/>
                        <h4>N° dossier médical : <span class="fw-bold"> {{ $admission->patient->code_patient ?? 'N/A' }}</span></h4>
                        <h4>N° Téléphone : <span class="fw-bold"> {{ $admission->patient->telephone ?? 'N/A' }}</span></h4>
                      </div>
                        <div class="box-body p-10">
                            <hr>
                            <div class="workingtime">
                                <h4 class="fw-500">Date d'admission</h4>
                                <h5 class="text-muted fs-18">
                                  @php
                                      $dateAdmission = $admission->date_admission ?? $admission->created_at;
                                      $jour = \Carbon\Carbon::parse($dateAdmission)->locale('fr')->format('l');
                                      if (function_exists('jourFr')) {
                                          echo jourFr($jour);
                                      } else {
                                          echo ucfirst($jour);
                                      }
                                  @endphp
                                </h5>
                                <p>
                                  @php
                                      $dateObj = \Carbon\Carbon::parse($dateAdmission);
                                      echo $dateObj->format('d-m-Y à H:i:s');
                                  @endphp
                                </p>
                                <hr>
                                <div style="font-size:18px; padding:10px;">
                                    <a href="{{ route('secretariat.admission.imprimer', $admission->id) }}" target="_blank" class="btn btn-primary" style="color:white">
                                        Imprimer l'admission <span class="fa fa-print ms-1"></span>
                                    </a>
                                </div>
                            </div>

                        </div>
                  </div>
              </div>
              <div class="col-lg-8 col-md-8">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="box">
                            <div class="box-body">
                              @if ($admission->doctor && $admission->infirmier)
                                <div class="workingtime">
                                    <h5 class="fw-500 text-primary"><i class="fa fa-user-md me-1"></i>Médecin en Charge</h5>
                                    <h4 class="text-muted"><strong>Dr. {{ $admission->doctor->user->name ?? '' }} {{ $admission->doctor->user->prenom ?? '' }}</strong></h4>
                                    <p class="mb-2">{{ $admission->doctor->matricule ?? '-' }} | {{ $admission->doctor->typeDoctor->label ?? ($admission->doctor->typeAgent->libelle ?? 'Médecin') }} | {{ $admission->doctor->contact ?? '-' }}</p>
                                    <hr class="my-2">
                                    <h5 class="fw-500 text-info"><i class="fa fa-user-nurse me-1"></i>Infirmier(ère) en Charge</h5>
                                    <h4 class="text-muted"><strong>Inf. {{ $admission->infirmier->user->name ?? '' }} {{ $admission->infirmier->user->prenom ?? '' }}</strong></h4>
                                    <p class="mb-0">{{ $admission->infirmier->matricule ?? '-' }} | {{ $admission->infirmier->libelle ?? 'Infirmier(ère)' }} | {{ $admission->infirmier->contact ?? '-' }}</p>
                                </div>
                              @elseif ($admission->doctor)
                                <div class="workingtime">
                                    <h5 class="fw-500 text-primary"><i class="fa fa-user-md me-1"></i>Médecin en Charge</h5><br>
                                    <h4 class="text-muted"><strong>Dr. {{ $admission->doctor->user->name ?? '' }} {{ $admission->doctor->user->prenom ?? '' }}</strong></h4>
                                    <p>{{ $admission->doctor->matricule ?? '-' }} | {{ $admission->doctor->typeDoctor->label ?? ($admission->doctor->typeAgent->libelle ?? 'Médecin') }} | {{ $admission->doctor->contact ?? '-' }}</p>
                                </div>
                              @elseif ($admission->infirmier)
                                <div class="workingtime">
                                    <h5 class="fw-500 text-info"><i class="fa fa-user-nurse me-1"></i>Infirmier(ère) en Charge</h5><br>
                                    <h4 class="text-muted"><strong>Inf. {{ $admission->infirmier->user->name ?? '' }} {{ $admission->infirmier->user->prenom ?? '' }}</strong></h4>
                                    <p>{{ $admission->infirmier->matricule ?? '-' }} | {{ $admission->infirmier->libelle ?? 'Infirmier(ère)' }} | {{ $admission->infirmier->contact ?? '-' }}</p>
                                </div>
                              @else
                                <div class="workingtime">
                                    <h5 class="fw-500">Praticien en Charge</h5><br>
                                    <h4 class="text-muted"><i>Non assigné</i></h4>
                                </div>
                              @endif
                            </div>
                        </div>
                      </div>
                      <div class="col-lg-6 col-md-6">
                        <div class="box">
                            <div class="box-body">
                                <div class="workingtime">
                                    <p class="fw-500"><b>Montant à payer : </b> @if($admission->montant == 0)<span class="badge badge-danger">Gratuit</span>@else <span class="badge badge-danger">{{ $admission->montant }} F CFA</span>@endif</p>
                                    <p class="fw-500"><b>Statut : </b>@if ($admission->statut_paiement == 1)<span class="badge badge-success">Consultation payée</span>@else<span class="badge badge-warning">Paiement en attente</span>@endif </p>
                                    <p class="fw-500"><b>Approuvé par : </b>@if ($admission->statut_validation == 1) <span class="badge badge-info">{{ $admission->cashier->user->name ?? ($admission->caissiere->user->name ?? 'Validé') }}</span> @else  <span class="badge badge-info">Pas encore approuvé</span> @endif</p>
                                </div>
                            </div>
                        </div>
                      </div>
                  </div>
                  <div class="box box-body">
                      <ul class="nav nav-tabs">
                          <li class="nav-admission"><a class="nav-link active show" data-bs-toggle="tab" href="#about">Information sur l'admission</a></li>
                      </ul><br>
                      <div class="tab-content">
                          <div class="tab-pane active show" id="about">
                            <div class="box-body">
                                <div class="table-responsive">
                                  <table class="table table-hover mb-0">

                                      <tbody>
                                        <tr>
                                          <th scope="row"><h5><strong> Type d'admission : </strong> </h5></th>
                                          <td><h5>{{ $admission->type_admission ?? 'Consultation' }}</h5></td>
                                        </tr>
                                        <tr>
                                          <th scope="row"><h5><strong> Prestation / Acte : </strong> </h5></th>
                                          <td>
                                            <h5>
                                              <span class="badge badge-primary">{{ $admission->prestationHospital->prestationService->libelle ?? ($admission->typeExamen->libelle ?? ($admission->serviceHopital->prestationHospital->service->libelle ?? $admission->type_admission)) }} </span>
                                            </h5>
                                          </td>

                                        </tr>
                                        <tr>
                                            <th scope="row"><h5><strong> Protection sociale : </strong></h5></th>
                                            <td><h5>@if($admission->type_assurance != null || $admission->typeAssurance != null) {{ $admission->type_assurance->libelle ?? ($admission->typeAssurance->libelle ?? '') }}, {{ $admission->no_assurance }} @else Pas de protection sociale @endif</h5></td>
                                          </tr>
                                          <tr>
                                            <th scope="row"><h5><strong> Mode d'entrée : </strong>  </h5></th>
                                            <td><h5>{{ $admission->mode_entree ?? 'Venue lui-même' }}</h5></td>
                                          </tr>
                                      </tbody>
                                    </table>
                                </div>
                            </div>
                            <br />
                              <h4><strong>Motif de la consultation</strong></h4>
                              <textarea rows="5" class="form-control" readonly>{{ $admission->motif_consultation }}</textarea>

                          </div>

                      </div>
                  </div>
              </div>
          </div>
      </section>
      <!-- /.content -->

@endsection
