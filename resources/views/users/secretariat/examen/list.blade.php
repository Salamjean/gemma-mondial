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
            <br /><br />
            <div class="box-body">
              <div class="table-responsive">
                  <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                      <thead>
                          <tr>
                              <th class="bb-2">N° Patient</th>
                              <th class="bb-2">Nom du Patient</th>
                              <th class="bb-2">Medecin en charge</th>
                              <th class="bb-2">Type d'admission</th>
                              <th class="bb-2">Mode d'entrée</th>
                              <th class="bb-2">Type de consultation</th>
                              <th class="bb-2">Motif de consultation</th>
                              <th class="bb-2">Montant Consultation</th>
                              <th class="bb-2 text-center">Status</th>
                              <th class="bb-2 text-center">Actions</th>
                          </tr>
                      </thead>
                      <tbody>

                        @foreach($admissions as $item)
                            <tr>
                                <td><b>{{ $item->patient->code_patient }}</b></td>
                                <td>
                                    <i>{{ $item->patient->user->name }}&nbsp; {{ $item->patient->user->prenom }}</i>
                                </td>
                                <td>
                                    <i>{{ $item->doctor->user->name }}</i>
                                </td>
                                <td>
                                    {{ $item->type_admission }}
                                </td>
                                <td>
                                    {{ $item->mode_entree }}
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $item->prestationHospital->service->libelle }}</span>
                                </td>
                                <td>
                                    <i>{{ $item->motif_consultation }} </i>
                                </td>
                                <td class="text-danger fw-bold fs-6"> <b>{{ $item->montant }} XOF</b> </td>
                                <td class="text-dark fw-bold fs-6">
                                    @if ($item->statut_paiement == 1)
                                        <span class="badge badge-success">Consultation payé</span>
                                    @else
                                        <span class="badge badge-warning">Paiement en attente</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('secretariat.admission.detail', $item->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="voir détails">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Imprimer">
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
