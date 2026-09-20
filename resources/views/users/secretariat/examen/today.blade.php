@extends('layouts.dashboard',['title' => "Liste des Patients pour une consultation"])

@section('content')
    <div class="row">
        <div class="col-12  ">
          <div class="box">
            <div class="box-header">
              <div class="row">
                  <div class="col-xs-12  col-xl-9 col-lg-9 col-md-9 col-sm-9">
                      <h4 class="box-title">Liste des consultations du jour</h4>
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
                              <th class="bb-2">Type de Consultation</th>
                              <th class="bb-2">Département (Services)</th>
                              <th class="bb-2">Motif de consultation</th>
                              <th class="bb-2 text-center">Status</th>
                              <th class="bb-2 text-center">Actions</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr>
                              <td class="text-dark fw-bold fs-6">#PAT-5893</td>
                              <td>
                                  Ivan blue Christophe
                              </td>
                              <td>
                                  Consultation générale
                              </td>
                              <td><span class="badge badge-primary">Medecine Interne</span></td>
                              <td>Venue pour des problèmes de migraine, fièvre et toux </td>
                              <td>
                                  <span class="badge badge-success">Consultation payé</span>
                              </td>
                              <td class="text-center">
                                  <a href="#" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Détail"><i class="fa-solid fa-eye"></i></a>
                                  <a href="#" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Modifier"><i class="fa-solid fa-pen-to-square"></i></a>
                                  <a class="btn btn-sm btn-danger" style="cursor: pointer" onclick="document.getElementById('delete-form').submit()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Supprimer"><i class="fa-solid fa-trash-can"></i></a>
                                  <form id="delete-form" action="#"
                                      method="post">
                                      @csrf
                                      @method("DELETE")
                                  </form>
                              </td>
                          </tr>
                          <tr>
                              <td class="text-dark fw-bold fs-6">#PAT-8900</td>
                              <td>
                                  Coulibaly Fatoumata
                              </td>
                              <td>
                                  Consultation pédiatrique
                              </td>
                              <td><span class="badge badge-primary">Département de pédiatrie</span></td>
                              <td>Mon enfant a le corps chaud et a des bourdonnement de ventre</td>
                              <td>
                                  <span class="badge badge-warning">Attente de paiement</span>
                              </td>
                              <td class="text-center">
                                  <a href="#" class="btn btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Détail"><i class="fa-solid fa-eye"></i></a>
                                  <a href="#" class="btn btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Modifier"><i class="fa-solid fa-pen-to-square"></i></a>
                                  <a class="btn btn-sm btn-danger" style="cursor: pointer" onclick="document.getElementById('delete-form').submit()" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Supprimer"><i class="fa-solid fa-trash-can"></i></a>
                                  <form id="delete-form" action="#"
                                      method="post">
                                      @csrf
                                      @method("DELETE")
                                  </form>
                              </td>
                          </tr>
                      </tbody>
                    </table>
              </div>
            </div>
          </div>
        </div>
    </div>
@endsection
