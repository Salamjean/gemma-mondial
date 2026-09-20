@extends('layouts.dashboard',['title' => "Liste des Patients enregistré pour une consultation"])

@section('content')

    <div class="row">
        <div class="col-12  ">
          <div class="box">
            <div class="box-header">
              <div class="row">
                  <div class="col-xs-12  col-xl-9 col-lg-9 col-md-9 col-sm-9">
                      <h4 class="box-title"><b>Admissions du jour</b></h4>
                  </div>
              </div>
            </div>
            
            <div class="box-body">
                <div class="table-responsive">
                    <table id="example1" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                        <thead>
                            <tr class="bg-info-light">
                                <th class="bb-2">N° Dossier médical</th>
                                 <th class="bb-2">Date et heure</th>
                                <th class="bb-2">Photo</th>
                                <th class="bb-2">Nom & prénom(s)</th>
                                <th class="bb-2">Description de la visite</th>
                                <th class="bb-2">Médecin/Infirmier(re) en charge</th>
                                <th class="bb-2">Montant</th>
                                <th class="bb-2 text-center">Statut</th>
                                <th class="bb-2 text-center">Effectuée</th>
                                <th class="bb-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($admissions as $item)
                                <tr>
                                  <td><b>{{ $item->patient->code_patient }}</b></td>
                                  <td>
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }} -
                                    {{ heureFr($item->created_at) }}
                                  </td>
                                  <td>
                                    @if ($item->patient->img_url != null)
        								<img src="{{ asset('assets/uploads/patient/'. $item->patient->img_url) }}"
        									class="rounded-circle" alt="Photo de profil" style="width:48px; height:48px" />
        							@else
        							    @if ($item->patient->gender == 'masculin')
        								<img src="{{ asset('assets/images/avatar/6.png') }}" class="avatar avatar-lg rounded10"
        									alt="Photo de profil" />
            							@else
            								<img src="{{ asset('assets/images/avatar/2.png') }}" class="avatar avatar-lg rounded10"
            									alt="Photo de profil" />
            							@endif
        							@endif
        							
                                    </td>
                                    <td>
                                      <i>{{ $item->patient->user->name }} {{ $item->patient->user->prenom }}</i>
                                    </td>
                                    <td>
                                        <i>{{ $item->motif_consultation }} </i>
                                    </td>
                                
                                    <td>
                                        <i>{{ $item->doctor->user->name }}</i>
                                    </td>
                                  
                                    <td class="text-danger fw-bold fs-6">
                                        @if ($item->montant == 0)
                                            <span class="badge badge-dark">Gratuit</span>
                                        @else
                                            <span class="badge badge-dark">{{ $item->montant }} Frs CFA</span>
                                        @endif
                                    </td>
                                    <td class="text-dark fw-bold fs-6">
                                        @if ($item->statut_paiement == 1)
                                             <span class="badge badge-success">Payé</span>
                                        <a class="btn btn-sm btn-none" data-bs-toggle="tooltip" data-bs-placement="bottom" title="approuvé">
                                            <i class="d-flex no-block fa fa-check-circle text-success"></i>
                                        </a>
                                        @else
                                            <span class="badge badge-warning">Paiement en attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $item->created_at->diffForHumans() }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('secretariat.admission.detail', $item->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="voir détails">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('secretariat.admission.imprimer', $item->id) }}" target="_blank" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Imprimer">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                          @endforeach
                        </tbody>
                      </table>
                </div>
            </div>
          </div>
        </div>
    </div>


@endsection