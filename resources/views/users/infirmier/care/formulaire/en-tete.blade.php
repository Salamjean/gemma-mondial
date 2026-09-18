<div class="container">
    <div class="box bb-3 border-dark pe-5 pb-20 px-20 ps-10 pt-20 bg-color">
        <div class="row">
            <div class="col-md-2">
                @if ($care->admission->patient->img_url != null)
                    <img src="{{ asset('assets/uploads/patient/' . $care->admission->patient->img_url) }}"
                        class="rounded-circle" alt="Photo de profil" style="width:128px; height:128px" />
                @else
                    @if ($care->admission->patient->gender == 'masculin')
                        <img src="{{ asset('assets/images/avatar/6.png') }}" class="rounded-circle"
                            alt="Photo de profil" />
                    @else
                        <img src="{{ asset('assets/images/avatar/2.png') }}" class="rounded-circle"
                            alt="Photo de profil" />
                    @endif
                @endif
            </div>
            <div class="col-md-10">
                <div class="px-2">
                    <div class="px-5 bg-color">
                        <div class="row">
                            <div class="d-flex justify-content-between mt-10">
                                <div class="">
                                    <label class="form-label">N° Dossier médical | <span class="fw-bold fs-18"><span id="dm_patient"
                                                style="color:red;">{{ $care->admission->patient->code_patient }}</span></span></label>
                                </div>
                                <div class="d-flex items-center pb-1">

                                    <span class=" d-flex mt-1 text-success">
                                        <span>Admission du {{ Carbon\Carbon::now()->format('d/m/Y') }}</span>
                                        <pre>  </pre> |
                                        <pre>  </pre> <span>Ordre
                                            N°</span>
                                        <pre>  </pre>
                                    </span>
                                    <a href="" title="dossier medical" class="btn btn-sm  btn-secondary mx-1"
                                        target="_blank"><i class="fa-solid fa-print"></i></a>
                                    <a href="#" title="info patient"
                                        class="btn btn-sm  btn-success mx-1" target="_blank"><i class="fa-solid fa-info"></i></a>
                                </div>
                            </div>
                            <hr>
                            @php
                                $agePatient = \Carbon\Carbon::createFromFormat('d/m/Y', $care->admission->patient->birth_date);
                                $age = $agePatient->diffInYears(Carbon\Carbon::now());
                            @endphp
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name" class="form-label"> <b>Nom complet </b> </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $care->admission->patient->user->name }} {{ $care->admission->patient->user->prenom }}"
                                        disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label"> <b>Né(e) le</b></label>
                                    <input type="text" class="form-control" id="birth_date" name="birth_date"
                                        value="{{ $care->admission->patient->birth_date }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="age" class="form-label"> <b>Age </b></label>
                                    <input type="text" class="form-control" id="age" name="age"
                                        value="{{ $age }} ans" disabled>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="gender" class="form-label"> <b>Sexe </b></label>
                                    <input type="text" class="form-control" id="gender" name="gender"
                                        value="{{ $care->admission->patient->gender }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><b>Résidence Actuelle</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $care->admission->patient->residenceActuelle->name ?? 'N/A' }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><b>Contact</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $care->admission->patient->telephone }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><b>N° CMU</b></label>
                                <input type="text" class="form-control text-primary font-bold"
                                    value="{{ $care->admission->patient->num_cmu ?? 'N/A' }}" readonly />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"><b>N° Assurance</b></label>
                                <input type="text" class="form-control"
                                    value="{{ $care->admission->patient->no_assurance }}" readonly />
                            </div>
                            <div class="col-md-12">
                                <label class="form-label"><b>Motif de la consultation</b></label>
                                <textarea type="text" class="form-control" name="motif_consultation" id="motif_consultation"
                                    value="{{ $care->admission->motif_consultation }}" readonly>{{ $care->admission->motif_consultation }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
