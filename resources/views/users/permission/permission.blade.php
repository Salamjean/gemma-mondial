@extends('layouts.dashboard')

@section('content')
    <div class="box ">
        <div class="box-header">
            <div class="d-flex justify-content-between">
                <div class="">
                    <h4 class="box-title">Les permissions enregistrées</h4>
                </div>
                <div class="">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        <span class="fa-solid fa-plus"></span>
                    </button>
                </div>

            </div>
        </div>
        <br /><br />
        <div class="box-body">
            <div class="table-responsive">
                <table id="example2" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                    <thead>
                        <tr>
                            <th class="bb-2">Code</th>
                            <th class="bb-2">Date</th>
                            <th class="bb-2">Date fin</th>
                            <th class="bb-2">Status</th>
                            <th class="bb-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permissions as $item)
                            <tr>
                                <td><b>{{ $item->code }}</b></td>
                                <td>
                                    {{ dateNumberFr($item->beging_date) }}
                                </td>
                                <td class="">
                                    {{ dateNumberFr($item->end_date) }}

                                </td>

                                <td class="">
                                    @if ($item->status == 'pending')
                                        <span class="badge badge-warning">En attente</span>
                                    @elseif($item->status == 'agree')
                                        <span class="badge badge-success">Accordée</span> <br>
                                    @else
                                        <span class="badge badge-danger">Refusée</span> <br>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button type="button" id="menu" title="Consulter" data-bs-toggle="modal"
                                        class="btn btn-sm btn-info editBtn me-1" data-bs-target="#editModal"
                                        data-bs-users="{{ $item->user->name }}"
                                        data-bs-descriptions="{{ $item->description }}"
                                        data-bs-beging_dates="{{ $item->beging_date }}"
                                        data-bs-end_dates="{{ $item->end_date }}" data-bs-status="{{ $item->status }}"
                                        data-bs-_urls="{{ $item->_url }}">
                                        <span class="fa-solid fa-eye"></span>
                                    </button>
                                    @if ($item->status == 'pending')
                                        <button type="button" title="Modifier la demande" data-bs-toggle="modal"
                                            class="btn btn-sm btn-primary editUserPermissionBtn" data-bs-target="#editUserPermissionModal"
                                            data-action="{{ route('permission.userUpdate', $item->id) }}"
                                            data-beging_date="{{ $item->beging_date }}"
                                            data-end_date="{{ $item->end_date }}"
                                            data-description="{{ $item->description }}">
                                            <span class="fa-solid fa-pen-to-square"></span>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('permission.store') }}" method="post"enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Demande de permission</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="beging_date" class="form-label">Date début<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="date" id="beging_date" name="beging_date"
                                        class="form-control @error('label') is-invalid @enderror"
                                        value="{{ old('beging_date') }}" autocomplete="beging_date" autofocus required>
                                    @error('beging_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">Date fin<span
                                            class="text-danger fw-bold">*</span></label>
                                    <input type="date" id="end_date" name="end_date"
                                        class="form-control @error('label') is-invalid @enderror"
                                        value="{{ old('end_date') }}" autocomplete="end_date" autofocus required>
                                    @error('end_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description<span
                                            class="text-danger fw-bold">*</span></label>
                                    <textarea class="form-control" name="description" id="description" rows="10" required></textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">Pièce jointe<span
                                            class="text-danger fw-bold"></span></label>
                                    <input type="file" id="_url" name="_url"
                                        class="form-control @error('label') is-invalid @enderror"
                                        value="{{ old('_url') }}" autocomplete="_url" autofocus>
                                    @error('_url')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Faire ma demande</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-12">

                            <a href="#" class="btn btn-sm btn-info" id="download" download style="display:none;">
                                <span class="fa-solid fa-print"></span>
                            </a>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="beging_dates" class="form-label">Date début</label>
                                <input type="date" id="beging_dates" name="beging_dates"
                                    class="bg-white form-control @error('label') is-invalid @enderror"
                                    value="{{ old('beging_dates') }}" autocomplete="beging_dates" autofocus readonly>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_dates" class="form-label">Date fin<span
                                        class="text-danger fw-bold">*</span></label>
                                <input type="date" id="end_dates" name="end_dates"
                                    class="bg-white form-control @error('label') is-invalid @enderror"
                                    value="{{ old('end_dates') }}" autocomplete="end_dates" autofocus readonly>

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descriptions" class="form-label">Description<span
                                        class="text-danger fw-bold">*</span></label>
                                <textarea class="form-control bg-white" name="descriptions" id="descriptions" rows="10" readonly></textarea>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Modification pour la permission en attente -->
    <div class="modal fade" id="editUserPermissionModal" tabindex="-1" aria-labelledby="editUserPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editUserPermissionForm" action="" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="editUserPermissionModalLabel">Modifier ma demande de permission (En attente)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_user_beging_date" class="form-label">Date début <span class="text-danger fw-bold">*</span></label>
                                    <input type="date" id="edit_user_beging_date" name="beging_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_user_end_date" class="form-label">Date fin <span class="text-danger fw-bold">*</span></label>
                                    <input type="date" id="edit_user_end_date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_user_description" class="form-label">Description / Motif <span class="text-danger fw-bold">*</span></label>
                                    <textarea class="form-control" name="description" id="edit_user_description" rows="6" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="edit_user_url" class="form-label">Remplacer la pièce jointe (Optionnel)</label>
                                    <input type="file" id="edit_user_url" name="_url" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".editBtn").click(function() {
                var descriptions = $(this).data('bs-descriptions');
                var beging_dates = $(this).data('bs-beging_dates');
                var end_dates = $(this).data('bs-end_dates');
                var users = $(this).data('bs-users');
                var _urls = $(this).data('bs-_urls');

                if (_urls) {
                    $('#download').attr('href', '../assets/uploads/permission/' + _urls).show();
                } else {
                    $('#download').hide();
                }

                $('#descriptions').val(descriptions);
                $('#beging_dates').val(beging_dates);
                $('#end_dates').val(end_dates);
                $('#users').val(users);
            });

            $(".editUserPermissionBtn").click(function() {
                var action = $(this).data('action');
                var beging_date = $(this).data('beging_date');
                var end_date = $(this).data('end_date');
                var description = $(this).data('description');

                $('#editUserPermissionForm').attr('action', action);
                $('#edit_user_beging_date').val(beging_date);
                $('#edit_user_end_date').val(end_date);
                $('#edit_user_description').val(description);
            });
        });
    </script>
@endsection
