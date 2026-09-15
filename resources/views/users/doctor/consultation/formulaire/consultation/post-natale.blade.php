<form action="{{ route('doctor.consultation.store.post.natale') }}" method="post">
    @csrf
    <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">

    <section class="container">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-body ribbon-box">
                        <div class="ribbon ribbon-dark">Renseignements Administratifs de la patiente</div>
                        <section>
                            <br /><br /><br /> <br />
                            <div class="row">
                                @if ($consultation->admission->mode_entree != null && $consultation->admission->mode_entree != '')
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label"><b>Mode d'entrée :</b></label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" name="mode_entree" id="mode_entree"
                                                    class="filled-in"
                                                    value="{{ $consultation->admission->mode_entree }}" checked />
                                                <label
                                                    for="mode_entree">{{ $consultation->admission->mode_entree }}</label>

                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="numero_gestante" class="form-label"> <b>Numéro Gestante de la
                                                visite</b> </label>
                                        <input type="text" class="form-control" name="numero_gestante"
                                            id="numero_gestante"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->numero_gestante }}"
                                            placeholder="08/B/2020/CPN2/R3/P15">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fonction" class="form-label"> <b>En cours de scolarisation:
                                            </b>
                                        </label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->eleve;
                                            @endphp
                                            <select class="form-select text-uppercase" id="eleve" name="eleve"
                                                style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>

                                                <option value="oui" {{ $valeur == 'oui' ? 'selected' : '' }}> Oui
                                                </option>
                                                <option value="non" {{ $valeur == 'non' ? 'selected' : '' }}> Non
                                                </option>
                                                <option value="na" {{ $valeur == 'na' ? 'selected' : '' }}> NA
                                                </option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Type population :</b> </label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->type_population;
                                            @endphp
                                            <select class="form-select text-uppercase" id="type_population"
                                                name="type_population" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>

                                                <option value="Population général"
                                                    {{ $valeur == 'population_generale' ? 'selected' : '' }}> Population
                                                    générale </option>
                                                <option value="TS" {{ $valeur == 'ts' ? 'selected' : '' }}> TS
                                                </option>
                                                <option value="UD" {{ $valeur == 'ud' ? 'selected' : '' }}> UD
                                                </option>
                                                <option value="HSH" {{ $valeur == 'hsh' ? 'selected' : '' }}> HSH
                                                </option>
                                                <option value="PC" {{ $valeur == 'pc' ? 'selected' : '' }}> PC
                                                </option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Type de consultation postnatale :</b>
                                        </label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->type_consultation_postnatale;
                                            @endphp
                                            <select class="form-select text-uppercase" id="type_consultation_postnatale"
                                                name="type_consultation_postnatale" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="06 à 72 heures"
                                                    {{ $valeur == '06 à 72 heures' ? 'selected' : '' }}> 06 à 72 heures
                                                </option>
                                                <option value="06 à 10 jours"
                                                    {{ $valeur == '06 à 10 jours' ? 'selected' : '' }}> 06 à 10 jours
                                                </option>
                                                <option value="06 à 08 semaines"
                                                    {{ $valeur == '06 à 08 semaines' ? 'selected' : '' }}> 06 à 08
                                                    semaines </option>
                                                <option value="Autre" {{ $valeur == 'Autre' ? 'selected' : '' }}>
                                                    Autre </option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <div class="box-body ribbon-box">
                        <div class="ribbon ribbon-dark">Examen de la patiente</div>
                        <div class="ribbon ribbon-dark" style="margin-left: 10px; background-color:red;">Examen
                            général</div>
                        <div class="mb-0">
                            <section>
                                <br /><br /><br /><br /><br />
                                <div class="row">


                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="pb" class="form-label"> <b>PB: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->pb }}"
                                                class="form-control" id="pb" name="pb" placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Oedemes" class="form-label"> <b>Oedèmes: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->oedemes }}"
                                                class="form-control" id="Oedemes" name="oedemes" placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="Varics" class="form-label"> <b>Varics: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->varics }}"
                                                class="form-control" id="Varics" name="varics"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="etat_conjonctives" class="form-label"> <b>Etat des
                                                    conjonctives: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->etat_conjonctives }}"
                                                class="form-control" id="etat_conjonctives" name="etat_conjonctives"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="conscience" class="form-label"> <b>Conscience: </b>
                                            </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->conscience }}"
                                                class="form-control" id="conscience" name="conscience"
                                                placeholder="...">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="seins" class="form-label"> <b>Seins: </b> </label>
                                            <input type="text" class="form-control" id="seins" name="seins"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->seins }}"
                                                placeholder="...">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="abdomen" class="form-label"> <b>Abdomen: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->abdomen }}"
                                                class="form-control" id="abdomen" name="abdomen"
                                                placeholder="...">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="globe_uterin" class="form-label"> <b>Globe uterin: </b>
                                            </label>
                                            <input type="text" class="form-control" id="globe_uterin"
                                                name="globe_uterin"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->globe_uterin }}"
                                                placeholder="...">
                                        </div>
                                    </div>


                                </div>

                                <div class="ribbon ribbon-dark"
                                    style="margin-left: 10px; margin-top: 10px; background-color:red;">Examen
                                    gynécologique</div>
                                <br />
                                <br />
                                <br />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="examen_perine" class="form-label"> <b>Examen du périnée:
                                                </b>
                                            </label>
                                            <textarea class="form-control" id="examen_perine" name="examen_perine" cols="30" rows="5">{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->examen_perine }}</textarea>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vulve" class="form-label"> <b>Vulve: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->vulve }}"
                                                class="form-control" id="vulve" name="vulve"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="uterus" class="form-label"> <b>Uterus: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->uterus }}"
                                                class="form-control" id="uterus" name="uterus"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="vessie" class="form-label"> <b>Vessie: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->vessie }}"
                                                class="form-control" id="vessie" name="vessie"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lochies" class="form-label"> <b>Lochies: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->lochies }}"
                                                class="form-control" id="lochies" name="lochies"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="examen_speculum" class="form-label"> <b>Examen au
                                                    spéculum:
                                                </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->examen_speculum }}"
                                                class="form-control" id="examen_speculum" name="examen_speculum"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="test_acide_acetique" class="form-label"> <b>Test à l'acide
                                                    acétique: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->test_acide_acetique }}"
                                                class="form-control" id="test_acide_acetique"
                                                name="test_acide_acetique" placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tv" class="form-label"> <b>T.V: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->tv }}"
                                                class="form-control" id="tv" name="tv"
                                                placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="enfant_vivant">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Enfant vivant : </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->enfant_vivant;
                                                @endphp
                                                <select class="form-select text-uppercase" id="enfant_vivant"
                                                    name="enfant_vivant" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                        Oui
                                                    </option>
                                                    <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                        Non
                                                    </option>

                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="enfant_jour_vaccin">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Enfant à jour de ses vaccins :
                                                </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->enfant_jour_vaccin;
                                                @endphp
                                                <select class="form-select text-uppercase" id="enfant_jour_vaccin"
                                                    name="enfant_jour_vaccin" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                        Oui
                                                    </option>
                                                    <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                        Non
                                                    </option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="femme_allaitante">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Femme allaitante : </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->femme_allaitante;
                                                @endphp
                                                <select class="form-select text-uppercase" id="femme_allaitante"
                                                    name="femme_allaitante" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                        Oui
                                                    </option>
                                                    <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                        Non
                                                    </option>

                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="allaitement_exlusif">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Allaitement exlusif : </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->allaitement_exlusif;
                                                @endphp
                                                <select class="form-select text-uppercase" id="allaitement_exlusif"
                                                    name="allaitement_exlusif" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                        Oui
                                                    </option>
                                                    <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                        Non
                                                    </option>

                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="autre_type_allaitement" class="form-label"> <b>Autres type
                                                    d'allaitement: </b> </label>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->autre_type_allaitement }}"
                                                class="form-control" id="autre_type_allaitement"
                                                name="autre_type_allaitement" placeholder="...">
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="conseil_alimentation_mere">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Conseil en alimentation de la mère :
                                                </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->conseil_alimentation_mere;
                                                @endphp
                                                <select class="form-select text-uppercase"
                                                    id="conseil_alimentation_mere" name="conseil_alimentation_mere"
                                                    style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                        Oui
                                                    </option>
                                                    <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                        Non
                                                    </option>

                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="conseil_alimentation_enfant">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Conseil en alimentation de l'enfant :
                                                </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->conseil_alimentation_enfant;
                                                @endphp
                                                <select class="form-select text-uppercase"
                                                    id="conseil_alimentation_enfant"
                                                    name="conseil_alimentation_enfant" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                        Oui
                                                    </option>
                                                    <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                        Non
                                                    </option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="etat_nutritionnel">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Etat nutritionnel : </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->etat_nutritionnel;
                                                @endphp
                                                <select class="form-select text-uppercase" id="etat_nutritionnel"
                                                    name="etat_nutritionnel" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Normal"
                                                        {{ $valeur == 'Normal' ? 'selected' : '' }}>
                                                        Normal
                                                    </option>
                                                    <option value="Malnutrition aigue modérée"
                                                        {{ $valeur == 'Malnutrition aigue modérée' ? 'selected' : '' }}>
                                                        Malnutrition aigue modérée
                                                    </option>
                                                    <option value="Malnutrition"
                                                        {{ $valeur == 'Malnutrition' ? 'selected' : '' }}>
                                                        Malnutrition
                                                    </option>
                                                    <option value="Aigue sévère"
                                                        {{ $valeur == 'Aigue sévère' ? 'selected' : '' }}>
                                                        Aigue sévère
                                                    </option>
                                                    <option value="Surpoids"
                                                        {{ $valeur == 'Surpoids' ? 'selected' : '' }}>
                                                        Surpoids
                                                    </option>
                                                    <option value="Obésité"
                                                        {{ $valeur == 'Obésité' ? 'selected' : '' }}>
                                                        Obésité
                                                    </option>

                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3" id="retour_couche">
                                        <div class="form-group">
                                            <label class="form-label"> <b>Retour de couches: :
                                                </b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->retour_couche;
                                                @endphp
                                                <select class="form-select text-uppercase" id="retour_couche"
                                                    name="retour_couche" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                        Oui
                                                    </option>
                                                    <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                        Non
                                                    </option>

                                                </select>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="patologies_associees" class="form-label"> <b>Patologies
                                                    associées: </b> </label>
                                            <textarea id="patologies_associees" name="patologies_associees" class="form-control" cols="30" rows="3">{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->patologies_associees }}</textarea>

                                        </div>
                                    </div>
                                </div>

                            </section>
                        </div>
                    </div>



                    <div class="box-body ribbon-box">
                        <div class="ribbon ribbon-dark"> Antécédents</div>
                        <section>
                            <br /><br /><br />

                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <label for="antecedents" class="form-label"><b>Antécédents :</b></label>
                                        <div class="c-inputs-stacked">

                                            <div class="row px-4">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->antecedent_hta;
                                                @endphp
                                                <div class="col-md-3">
                                                    <label class="form-label"><i>Médicaux HTA</i></label>
                                                    <select class="form-select text-uppercase" id="antecedent_hta"
                                                        name="antecedent_hta" style="width: 100%;">
                                                        <option value="" disabled selected>Selectionner</option>
                                                        <option value="Oui"
                                                            {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                            Oui
                                                        </option>
                                                        <option value="Non"
                                                            {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                            Non
                                                        </option>

                                                    </select>
                                                </div>
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->antecedent_diabete;
                                                @endphp
                                                <div class="col-md-3">
                                                    <label class="form-label"><i>Diabète</i></label>
                                                    <select class="form-select text-uppercase" id="antecedent_diabete"
                                                        name="antecedent_diabete" style="width: 100%;">
                                                        <option value="" disabled selected>Selectionner</option>
                                                        <option value="Oui"
                                                            {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                            Oui
                                                        </option>
                                                        <option value="Non"
                                                            {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                            Non
                                                        </option>

                                                    </select>
                                                </div>

                                                <div class="col-md-12 pt-2">
                                                    <div class="form-group">
                                                        <label for="antecedent_autre" class="form-label">
                                                            Autre:</label>
                                                        <textarea id="antecedent_autre" name="antecedent_autre" class="form-control" cols="30" rows="3">{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->antecedent_autre }}</textarea>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="chirurgicaux" class="form-label"> <b>Chirurgicaux
                                                :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->chirugicaux }}"
                                            class="form-control" id="chirurgicaux" name="chirurgicaux"
                                            placeholder="Préciser...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="obstetricaux" class="form-label"> <b>Obstétricaux
                                                :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->obsetricaux }}"
                                            class="form-control" id="obstetricaux" name="obstetricaux"
                                            placeholder="Préciser...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="gestite" class="form-label"> <b>Gestité :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->gestile }}"
                                            class="form-control" id="gestite" name="gestite"
                                            placeholder="Préciser...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="parite" class="form-label"> <b>Parité :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->parite }}"
                                            class="form-control" id="parite" name="parite"
                                            placeholder="Préciser...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nb_enfant_vivant" class="form-label"> <b>Enfants vivants
                                                :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->nb_enfant_vivant }}"
                                            class="form-control" id="nb_enfant_vivant" name="nb_enfant_vivant"
                                            placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="enfant_decede" class="form-label"> <b>Enfants décédés
                                                :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->enfant_decede }}"
                                            class="form-control" id="enfant_decede" name="enfant_decede"
                                            placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cesarienne" class="form-label"> <b>Césarienne :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->cesarienne }}"
                                            class="form-control" id="cesarienne" name="cesarienne"
                                            placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="Avortement" class="form-label"> <b>Avortement :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->Avortement }}"
                                            class="form-control" id="Avortement" name="Avortement"
                                            placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="toxemie" class="form-label"> <b>Toxémie gravidique
                                                :</b></label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->toxemie }}"
                                            class="form-control" id="toxemie" name="toxemie" placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_consultation" class="form-label"> <b>Date de
                                                l'accouchement :
                                            </b>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->date_accouchement }}"
                                                name="date_accouchement" id="date_accouchement" class="form-control"
                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask=""
                                                style="color: rgb(0, 0, 0);">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Lieu d'accouchement : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->antecedent_lieu_accouch;
                                            @endphp
                                            <select class="form-select text-uppercase" id="antecedent_lieu_accouch"
                                                name="antecedent_lieu_accouch" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="En établissement de soins"
                                                    {{ $valeur == 'En établissement de soins' ? 'selected' : '' }}>
                                                    En établissement de soins
                                                </option>
                                                <option value="A domicile"
                                                    {{ $valeur == 'A domicile' ? 'selected' : '' }}>
                                                    A domicile
                                                </option>

                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Mode d'accouchement : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->antecedent_mode_accouch;
                                            @endphp
                                            <select class="form-select text-uppercase" id="antecedent_mode_accouch"
                                                name="antecedent_mode_accouch" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Voie basse"
                                                    {{ $valeur == 'Voie basse' ? 'selected' : '' }}>
                                                    Voie basse
                                                </option>
                                                <option value="Césarienne"
                                                    {{ $valeur == 'Césarienne' ? 'selected' : '' }}>
                                                    Césarienne
                                                </option>
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="date_vat1" class="form-label"> <b>Statut VAT : </b>
                                        </label>
                                        <div class="row px-4">
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text"
                                                        value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->date_vat1 }}"
                                                        name="date_vat1" class="form-control"
                                                        data-inputmask="'alias': 'dd/mm/yyyy'" data-mask=""
                                                        placeholder="Date VAT1">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text"
                                                        value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->date_vat2 }}"
                                                        name="date_vat2" class="form-control"
                                                        data-inputmask="'alias': 'dd/mm/yyyy'" data-mask=""
                                                        placeholder="Date VAT2">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <i class="fa fa-calendar"></i>
                                                    </div>
                                                    <input type="text" name="date_vat_rappel"
                                                        value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->date_vat_rappel }}"
                                                        class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'"
                                                        data-mask="" placeholder="Date VAT Rappel">
                                                </div>
                                            </div>
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->etat_vaccination;
                                            @endphp
                                            <div class="col-md-3">
                                                <select class="form-select text-uppercase" id="etat_vaccination"
                                                    name="etat_vaccination" style="width: 100%;">
                                                    <option value="" disabled selected>Selectionner</option>
                                                    <option value="Non Vacciné"
                                                        {{ $valeur == 'Non Vacciné' ? 'selected' : '' }}>
                                                        Non Vacciné
                                                    </option>
                                                    <option value="Incomplètement vacciné"
                                                        {{ $valeur == 'Incomplètement vacciné' ? 'selected' : '' }}>
                                                        Incomplètement vacciné
                                                    </option>
                                                    <option value="Correctement vacciné"
                                                        {{ $valeur == 'Correctement vacciné' ? 'selected' : '' }}>
                                                        Correctement vacciné
                                                    </option>

                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="col-md-3" id="status_vih">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Statut VIH à l'accueil : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->status_vih;
                                            @endphp
                                            <select class="form-select text-uppercase" id="status_vih"
                                                name="status_vih" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Positif" {{ $valeur == 'Positif' ? 'selected' : '' }}>
                                                    Positif
                                                </option>
                                                <option value="Négatif" {{ $valeur == 'Négatif' ? 'selected' : '' }}>
                                                    Négatif
                                                </option>
                                                <option value="Inconnu" {{ $valeur == 'Inconnu' ? 'selected' : '' }}>
                                                    Inconnu
                                                </option>

                                            </select>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Méthode adoptée : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->methode_adoptee;
                                            @endphp
                                            <select class="form-select text-uppercase" id="methode_adoptee"
                                                name="methode_adoptee" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Orale" {{ $valeur == 'Orale' ? 'selected' : '' }}>
                                                    Orale
                                                </option>
                                                <option value="Injectable"
                                                    {{ $valeur == 'Injectable' ? 'selected' : '' }}>
                                                    Injectable
                                                </option>
                                                <option value="Implant" {{ $valeur == 'Implant' ? 'selected' : '' }}>
                                                    Implant
                                                </option>
                                                <option value="DIU" {{ $valeur == 'DIU' ? 'selected' : '' }}>
                                                    DIU
                                                </option>
                                                <option value="Permanente"
                                                    {{ $valeur == 'Permanente' ? 'selected' : '' }}>
                                                    Permanente
                                                </option>
                                                <option value="Aucune" {{ $valeur == 'Aucune' ? 'selected' : '' }}>
                                                    Aucune
                                                </option>
                                            </select>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <label for="" class="form-label"> <b>Offre de service planification
                                            familiale
                                        </b></label>
                                    <div class="row">
                                        @php
                                            $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->conseil_pf_pp1;
                                        @endphp
                                        <div class="col-md-12">


                                            <label class="me-30">Conseil PF-PP1 (<= 48 heures à 6 semaines après
                                                    l'accouchement) : </label><br>
                                                    <input type="radio" {{ $valeur == 'Oui' ? 'checked' : '' }}
                                                        id="pf-pp1-oui" value="Oui" name="conseil_pf_pp1">
                                                    <label for="pf-pp1-oui" class="me-30">Oui</label>
                                                    <input type="radio" {{ $valeur == 'Non' ? 'checked' : '' }}
                                                        id="pf-pp1-non" value="Non" name="conseil_pf_pp1">
                                                    <label for="pf-pp1-non" class="me-30">Non</label>
                                        </div>
                                    </div>
                                    @php
                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->conseil_pf_ppt;
                                    @endphp
                                    <div class="col-md-12">

                                        <label class="me-30">Conseil PF-PP tardif (de 48 heures à 6
                                            semaines
                                            après
                                            l'accouchement) : </label><br>
                                        <input type="radio" {{ $valeur == 'Oui' ? 'checked' : '' }} id="pf-ppt-oui"
                                            value="Oui" name="conseil_pf_ppt">
                                        <label for="pf-ppt-oui" class="me-30">Oui</label>
                                        <input type="radio" {{ $valeur == 'Non' ? 'checked' : '' }} id="pf-ppt-non"
                                            value="Non" name="conseil_pf_ppt">
                                        <label for="pf-ppt-non" class="me-30">Non</label>
                                    </div>
                                    @php
                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->conseil_pf_ppp;
                                    @endphp
                                    <div class="col-md-12">

                                        <label class="me-30">Conseil PF-PP prolongé (de 6 semaines à
                                            un an
                                            après
                                            l'accouchement) : </label> <br>
                                        <input type="radio" {{ $valeur == 'Oui' ? 'checked' : '' }} id="pf-ppp-oui"
                                            value="Oui" name="conseil_pf_ppp">
                                        <label for="pf-ppp-oui" class="me-30">Oui</label>
                                        <input type="radio" {{ $valeur == 'Non' ? 'checked' : '' }} id="pf-ppp-non"
                                            value="Non" name="conseil_pf_ppp">
                                        <label for="pf-ppp-non" class="me-30">Non</label>
                                    </div>

                                </div>

                            </div>

                        </section>
                    </div>

                    <div class="box-body ribbon-box">
                        <div class="ribbon ribbon-dark"> Conduite à tenir</div>
                        <section>
                            <br /><br /><br />
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Proposition de test VIH : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->proposition_test_vih;
                                            @endphp
                                            <select class="form-select text-uppercase" id="proposition_test_vih"
                                                name="proposition_test_vih" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                    Oui
                                                </option>
                                                <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                    Non
                                                </option>
                                                <option value="NA" {{ $valeur == 'NA' ? 'selected' : '' }}>
                                                    NA
                                                </option>

                                            </select>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Retesting : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->retesting;
                                            @endphp
                                            <select class="form-select text-uppercase" id="retesting"
                                                name="retesting" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                    Oui
                                                </option>
                                                <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                    Non
                                                </option>
                                                <option value="NA" {{ $valeur == 'NA' ? 'selected' : '' }}>
                                                    NA
                                                </option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="numero_depistage_vih" class="form-label"> <b>Numéro de
                                                dépistage
                                                VIH: </b> </label>
                                        <input type="text"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->numero_depistage_vih }}"
                                            class="form-control" id="numero_depistage_vih"
                                            name="numero_depistage_vih" placeholder="78">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Resultat du test de depistage VIH : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->resultat_test_depistage_vih;
                                            @endphp
                                            <select class="form-select text-uppercase"
                                                id="resultat_test_depistage_vih" name="resultat_test_depistage_vih"
                                                style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Négatif" {{ $valeur == 'Négatif' ? 'selected' : '' }}>
                                                    Négatif
                                                </option>
                                                <option value="Positif" {{ $valeur == 'Positif' ? 'selected' : '' }}>
                                                    Positif
                                                </option>
                                                <option value="NA" {{ $valeur == 'NA' ? 'selected' : '' }}>
                                                    NA
                                                </option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Annonce du résultat : </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->annonce_resultat;
                                            @endphp
                                            <select class="form-select text-uppercase" id="annonce_resultat"
                                                name="annonce_resultat" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                    Oui
                                                </option>
                                                <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                    Non
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Prophylaxie ARV pour l'enfant: </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->annonce_resultat;
                                            @endphp
                                            <select class="form-select text-uppercase" id="prophylaxie"
                                                name="prophylaxie" style="width: 100%;">
                                                <option value="" disabled selected>Selectionner</option>
                                                <option value="Oui" {{ $valeur == 'Oui' ? 'selected' : '' }}>
                                                    Oui
                                                </option>
                                                <option value="Non" {{ $valeur == 'Non' ? 'selected' : '' }}>
                                                    Non
                                                </option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="ribbon ribbon-dark" style="margin-left: 10px; background-color:red;">
                                Examens
                                biologiques</div>
                            <br />
                            <br />
                            <br />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Analyse d'urine :</b> </label>
                                        <div class="c-inputs-stacked">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @php
                                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->analyse_urine_albumine;
                                                    @endphp
                                                    <div class="me-30"><i>Albumine :</i> </div>

                                                    <div class="c-inputs-stacked">
                                                        <select class="form-select text-uppercase"
                                                            id="analyse_urine_albumine" name="analyse_urine_albumine"
                                                            style="width: 100%;">
                                                            <option value="" disabled selected>Selectionner</option>
                                                            <option value="Positif"
                                                                {{ $valeur == 'Positif' ? 'selected' : '' }}>
                                                                Positif
                                                            </option>
                                                            <option value="Négatif"
                                                                {{ $valeur == 'Négatif' ? 'selected' : '' }}>
                                                                Négatif
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <br />
                                                </div>
                                                <div class="col-md-6">
                                                    @php
                                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->analyse_urine_sucre;
                                                    @endphp

                                                    <div class="me-30"><i>Sucre :</i> </div>
                                                    <div class="c-inputs-stacked">
                                                        <select class="form-select text-uppercase"
                                                            id="analyse_urine_sucre" name="analyse_urine_sucre"
                                                            style="width: 100%;">
                                                            <option value="" disabled selected>Selectionner</option>
                                                            <option value="Positif"
                                                                {{ $valeur == 'Positif' ? 'selected' : '' }}>
                                                                Positif
                                                            </option>
                                                            <option value="Négatif"
                                                                {{ $valeur == 'Négatif' ? 'selected' : '' }}>
                                                                Négatif
                                                            </option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label"><b>Analyse de sange :</b></label>
                                            <div class="c-inputs-stacked">
                                                @php
                                                    $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->glycemie;
                                                @endphp
                                                <div class="me-30"><i>Glycémie :</i> </div>

                                                <div class="c-inputs-stacked">
                                                    <select class="form-select text-uppercase" id="glycemie"
                                                        name="glycemie" style="width: 100%;">
                                                        <option value="" disabled selected>Selectionner</option>
                                                        <option value="Non réaliser"
                                                            {{ $valeur == 'Non réaliser' ? 'selected' : '' }}>
                                                            Non réaliser
                                                        </option>
                                                        <option value="Réaliser"
                                                            {{ $valeur == 'Réaliser' ? 'selected' : '' }}>
                                                            Réaliser
                                                        </option>
                                                    </select>
                                                </div>
                                                <br />

                                            </div>
                                        </div>
                                    </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Offre de service planification familiale
                                                :</b>
                                        </label>
                                        <div class="c-inputs-stacked">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    @php
                                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->offre_service_pf_conseil_pf_ppp1;
                                                    @endphp
                                                    <div class="me-30"><i>Conseil PF-PP1 :</i> </div>

                                                    <input type="radio" {{ $valeur == 'oui' ? 'checked' : '' }}
                                                        id="ospfcpp1_oui" value="oui"
                                                        name="offre_service_pf_conseil_pf_ppp1">
                                                    <label for="ospfcpp1_oui" class="me-30">Oui</label>
                                                    <input type="radio" {{ $valeur == 'non' ? 'checked' : '' }}
                                                        id="ospfcpp1_non" value="non"
                                                        name="offre_service_pf_conseil_pf_ppp1">
                                                    <label for="ospfcpp1_non" class="me-30">Non</label>
                                                </div>
                                                <div class="col-md-6">
                                                    @php
                                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->offre_service_pf_conseil_pf_ppp;
                                                    @endphp
                                                    <div class="me-30"><i>Conseil PF-PP :</i> </div>

                                                    <input type="radio" {{ $valeur == 'oui' ? 'checked' : '' }}
                                                        id="ospfcpp_oui" value="oui"
                                                        name="offre_service_pf_conseil_pf_ppp">
                                                    <label for="ospfcpp_oui" class="me-30">Oui</label>
                                                    <input type="radio" {{ $valeur == 'non' ? 'checked' : '' }}
                                                        id="ospfcpp_non" value="non"
                                                        name="offre_service_pf_conseil_pf_ppp">
                                                    <label for="ospfcpp_non" class="me-30">Non</label>
                                                    <br /><br />
                                                </div>
                                                <div class="col-md-6">
                                                    @php
                                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->acceptation_methodes_contraceptives;
                                                    @endphp
                                                    <div class="me-30"><i>Acceptation de méthodes
                                                            contraceptives
                                                            :</i> </div>
                                                    <input type="radio" {{ $valeur == 'oui' ? 'checked' : '' }}
                                                        id="amc_oui" value="oui"
                                                        name="acceptation_methodes_contraceptives">
                                                    <label for="amc_oui" class="me-30">Oui</label>
                                                    <input type="radio" {{ $valeur == 'non' ? 'checked' : '' }}
                                                        id="amc_non" value="non"
                                                        name="acceptation_methodes_contraceptives">
                                                    <label for="amc_non" class="me-30">Non</label>
                                                    <input type="radio" {{ $valeur == 'na' ? 'checked' : '' }}
                                                        id="amc_na" value="na"
                                                        name="acceptation_methodes_contraceptives">
                                                    <label for="amc_na" class="me-30">NA</label> <br />
                                                </div>
                                                <div class="col-md-6">
                                                    @php
                                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->acceptation_methodes_contraceptives_precision;
                                                    @endphp
                                                    <div class="me-30"><i>Si oui type :</i> </div>

                                                    <input type="radio" {{ $valeur == 'oral' ? 'checked' : '' }}
                                                        id="amco_1" value="oral"
                                                        name="acceptation_methodes_contraceptives_precision">
                                                    <label for="amco_1" class="me-30">Oral</label>

                                                    <input type="radio"
                                                        {{ $valeur == 'injectable' ? 'checked' : '' }} id="amco_2"
                                                        value="injectable"
                                                        name="acceptation_methodes_contraceptives_precision">
                                                    <label for="amco_2" class="me-30">Injectable</label>

                                                    <input type="radio" {{ $valeur == 'implant' ? 'checked' : '' }}
                                                        id="amco_3" value="implant"
                                                        name="acceptation_methodes_contraceptives_precision">
                                                    <label for="amco_3" class="me-30">Implant</label>

                                                    <input type="radio" {{ $valeur == 'diu' ? 'checked' : '' }}
                                                        id="amco_4" value="diu"
                                                        name="acceptation_methodes_contraceptives_precision">
                                                    <label for="amco_4" class="me-30">DIU</label>

                                                </div>
                                                <div class="col-md-12   ">
                                                    @php
                                                        $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->acceptation_methodes_contraceptives_methode;
                                                    @endphp
                                                    <div class="me-30"><i>Méthode adoptée :</i> </div>

                                                    <input type="radio" {{ $valeur == 'orale' ? 'checked' : '' }}
                                                        id="amcoma_1" value="orale"
                                                        name="acceptation_methodes_contraceptives_methode">
                                                    <label for="amcoma_1" class="me-30">Orale</label>

                                                    <input type="radio"
                                                        {{ $valeur == 'injectable' ? 'checked' : '' }} id="amcoma_2"
                                                        value="injectable"
                                                        name="acceptation_methodes_contraceptives_methode">
                                                    <label for="amcoma_2" class="me-30">Injectable</label>

                                                    <input type="radio" {{ $valeur == 'implant' ? 'checked' : '' }}
                                                        id="amcoma_3" value="implant"
                                                        name="acceptation_methodes_contraceptives_methode">
                                                    <label for="amcoma_3" class="me-30">Implant</label>

                                                    <input type="radio" {{ $valeur == 'diu' ? 'checked' : '' }}
                                                        id="amcoma_4" value="diu"
                                                        name="acceptation_methodes_contraceptives_methode">
                                                    <label for="amcoma_4" class="me-30">DIU</label>

                                                    <input type="radio" {{ $valeur == 'aucune' ? 'checked' : '' }}
                                                        id="amcoma_5" value="aucune"
                                                        name="acceptation_methodes_contraceptives_methode">
                                                    <label for="amcoma_5" class="me-30">Aucune</label>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Prescriptions :</b> </label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->prescription;
                                            @endphp
                                            <input type="checkbox" name="prescription[]" id="fer"
                                                value="fer" />
                                            <label for="fer" class="me-30">Fer</label>
                                            <input type="checkbox" name="prescription[]" id="folates"
                                                value="folates" />
                                            <label for="folates" class="me-30">Folates</label>
                                            <input type="checkbox" name="prescription[]" id="sp1"
                                                value="folates" />
                                            <label for="sp1" class="me-30">SP1</label>
                                            <input type="checkbox" name="prescription[]" id="sp2"
                                                value="sp2" />
                                            <label for="sp2" class="me-30">SP2</label>
                                            <input type="checkbox" name="prescription[]" id="sp3"
                                                value="sp3" />
                                            <label for="sp3" class="me-30">SP3</label>
                                            <input type="checkbox" name="prescription[]" id="sp4"
                                                value="sp4" />
                                            <label for="sp4" class="me-30">SP4</label>
                                            <input type="checkbox" name="prescription[]" id="sp5_plus"
                                                value="sp5_plus" />
                                            <label for="sp5_plus" class="me-30">SP5 et plus</label>
                                            <input type="checkbox" name="prescription[]" id="fluor"
                                                value="fluor" />
                                            <label for="fluor" class="me-30">Fluor</label>
                                            <input type="checkbox" name="prescription[]" id="milda"
                                                value="milda" />
                                            <label for="milda" class="me-30">MILDA</label>
                                            <input type="checkbox" name="prescription[]" id="deparasitant"
                                                value="deparasitant" />
                                            <label for="deparasitant" class="me-30">Déparasitant</label>
                                            <input type="checkbox" name="prescription[]" id="ctx"
                                                value="ctx" />
                                            <label for="ctx" class="me-30">CTX</label>
                                            <input type="checkbox" name="prescription[]" id="arv"
                                                value="arv" />
                                            <label for="arv" class="me-30">Initiation ARV</label>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Offre de service nutritionnel :
                                            </b></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->offre_service_nutritionnel;
                                            @endphp
                                            <input type="radio" {{ $valeur == 'oui' ? 'checked' : '' }}
                                                id="osn_oui" value="oui" name="offre_service_nutritionnel">
                                            <label for="osn_oui" class="me-30">Oui</label>
                                            <input type="radio" {{ $valeur == 'non' ? 'checked' : '' }}
                                                id="osn_non" value="non" name="offre_service_nutritionnel">
                                            <label for="osn_non" class="me-30">Non</label>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="osnp" class="form-label"> Si oui, Précisez </label>
                                        <input type="text" class="form-control"
                                            value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->offre_service_nutritionnel_oui_precision }}"
                                            name="offre_service_nutritionnel_oui_precision" id="osnp">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Observation :</b> </label>
                                        <div class="input-group">
                                            <textarea name="observation"
                                                value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->observation }}"
                                                class="form-control" id="observation" cols="10" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Résultat de la consultation :</b>
                                        </label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->resultat_consultation;
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="me-30"><i>Complication :</i> </div>

                                                    <input type="radio"
                                                        {{ $valeur == 'visite post natale normale' ? 'checked' : '' }}
                                                        id="rc_vn" value="visite post natale normale"
                                                        name="resultat_consultation">
                                                    <label for="rc_vn" class="me-30">Visite post natale
                                                        normale</label>
                                                    <input type="radio"
                                                        {{ $valeur == 'visite post natale avec complication' ? 'checked' : '' }}
                                                        id="rc_vn" value="visite post natale avec complication"
                                                        name="resultat_consultation">
                                                    <label for="rc_vn" class="me-30">Visite post natale
                                                        avec
                                                        complication</label><br /><br />
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="form-label"> <b>Si oui, préciser :</b>
                                                        </label>
                                                        <div class="input-group">
                                                            <textarea name="resultat_consultation_oui_precision" class="form-control" id="rcop" cols="10"
                                                                rows="5">{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPostNatale->offre_service_nutritionnel_oui_precision }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Mode de sortie : </b><span
                                                class="danger">*</span></label>
                                        <div class="c-inputs-stacked">
                                            @php
                                                $valeur = $consultation->registre == null ? '' : $consultation->registre->issue_consultation;
                                            @endphp
                                            <input type="radio" {{ $valeur == 'sortie' ? 'checked' : '' }}
                                                id="mode_sortie_1" value="sortie" name="mode_sortie" required>
                                            <label for="mode_sortie_1" class="me-30">Domicile</label>
                                            <input type="radio" {{ $valeur == 'a-revoir' ? 'checked' : '' }}
                                                id="mode_sortie_2" value="a-revoir" name="mode_sortie">
                                            <label for="mode_sortie_2" class="me-30">Revoir</label>
                                            <input type="radio" {{ $valeur == 'refere-interne' ? 'checked' : '' }}
                                                id="mode_sortie_3" value="refere-interne" name="mode_sortie">
                                            <label for="mode_sortie_3" class="me-30">Référée</label>
                                            <input type="radio" {{ $valeur == 'hospitalisation' ? 'checked' : '' }}
                                                id="mode_sortie_4" value="hospitalisation" name="mode_sortie">
                                            <label for="mode_sortie_4" class="me-30">Hospitalisé</label>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_prochain_rdv" class="form-label"> <b>Date du prochain
                                                rendez-vous :
                                            </b>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="date" name="date_prochain_rdv" id="date_consultation"
                                                class="form-control"    
                                                data-mask="">
                                        </div>
                                    </div>
                                </div>

                        </section>
                    </div>
                </div>
            </div>

            <hr />
        </div>
        <a href="{{ route('doctor.consultation.today') }}" class="btn btn-warning me-1"><i class="ti-trash"></i> Annuler</a>
        <button id="addButton" type="submit" class="btn btn-primary btn-submit">Enregister</button>
    </section>


</form>
