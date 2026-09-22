<style>
    .apgar-input {
        width: 75px; 
    }
</style>
<form action="{{ route('doctor.consultation.store.accouchement')}}" method="POST">
    @csrf
<!-- /.BLOC 01 -->
<section class="content">
    <div class="row">
        <div class="col-lg-12 col-12">
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-info">Identité de la mère</div>

                    <div class="mb-0">
                        <section>
                            <br /><br /><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="numero_gestante" class="form-label"> <b>Report N° Gestante :</b> <span class="danger">*</span> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->numero_gestante }}" class="form-control" name="numero_gestante" id="numero_gestante" placeholder="08/B/2020/CPN2/R3/P15" style="color: red;" autofocus>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="mode_entree" class="form-label"><b>Mode d'entrée :</b></label>
									  <select class="form-select" id="mode_entree" name="mode_entree">
                                        <option value="" selected="selected">---</option>
                                        <option value="Venue lui-même">Venue d'elle-même</option>
                                        <option value="Référée par un centre de santé">Référée / Centre de santé</option>
                                        <option value="Référée par un tradipraticien">Référée tradipraticien</option>
                                        <option value="autre">Autre</option>
									  </select>
									</div>
								</div>  
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="preciser" class="form-label"><b>&nbsp;</b></label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->preciser_centre }}" class="form-control" id="preciser" name="preciser_centre" placeholder="Préciser" style="display:none;">
                                        <input type="text" class="form-control" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->preciser_autre }}" id="preciser" name="preciser_autre" placeholder="Préciser autre" style="display:none;">
                                    </div>
                                </div>
                                    
                            </div>
                            <div class="row">

                            </div>
                            <div class="row">
                                  <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_accouchement" class="form-label"> <b>Accouchement le : </b>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="datetime-local" name="date_accouchement" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->date_accouchement }}" id="date_accouchement" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="num_accouchement" class="form-label"> <b>N° Accouchement : </b> </label>
                                        <input type="text" class="form-control" name="num_accouchement" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->num_accouchement }}" placeholder="N° d'ordre annuel">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="accouch_dom" class="form-label"> <b>Accouchement à domicile : </b>
                                        </label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="accouch_dom_oui" value="Oui" name="accouch_dom">
                                            <label for="accouch_dom_oui" >Oui</label>
                                            <input type="radio" id="accouch_dom_non" value="Non" name="accouch_dom">
                                            <label for="accouch_dom_non" >Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="scolarisation" class="form-label"> <b>En cours de scolarisation: </b>
                                        </label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="eleve" value="oui" name="en_scolarisation">
                                            <label for="eleve" >Oui</label>
                                            <input type="radio" id="non_eleve" value="non" name="en_scolarisation">
                                            <label for="non_eleve" >Non</label>
                                            <input type="radio" id="na_eleve" value="na" name="en_scolarisation">
                                            <label for="na_eleve" >NA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-info">Arrivée</div>
                    <section>
                        <br />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_consultation" class="form-label"> <b>Date <span class="danger">*</span>: </b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="hidden" name="consultation_id" value="{{  $consultation->id }}" />
                                        <input type="text" name="date_consultation" id="date_consultation"
                                            class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'"
                                            data-mask="" style="color: blue;" value="{{ date('d/m/Y') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mode_admission" class="form-label"> <b>Motif d'admission :</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->mode_admission }}" class="form-control" name="mode_admission" id="mode_admission" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="en_travail" class="form-label"> <b>En travail : </b>
                                    </label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="en_travail_oui" value="Oui" name="en_travail">
                                        <label for="en_travail_oui" >Oui</label>
                                        <input type="radio" id="en_travail_non" value="Non" name="en_travail">
                                        <label for="en_travail_non" >Non</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contractions" class="form-label"><b>Contractions :</b></label>
                                  <select class="form-select" id="contractions" name="contractions">
                                    <option value="" @readonly(true)>---</option>
                                    <option value="Présentes">Présentes</option>
                                    <option value="Regulières">Regulières</option>
                                    <option value="Non">Non</option>
                                  </select>
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="poche_intacte" class="form-label"> <b>Poche des eaux intacte : </b>
                                    </label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="poche_intacte_oui" value="Oui" name="poche_intacte">
                                        <label for="poche_intacte_oui" >Oui</label>
                                        <input type="radio" id="poche_intacte_non" value="Non" name="poche_intacte">
                                        <label for="poche_intacte_non" >Non</label>
                                    </div>
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nbre_heure" class="form-label"> <b>Temps écoulés (heure) : </b> </label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->nbre_heure_ecoule }}" class="form-control" name="nbre_heure_ecoule" placeholder="depuis la rupture des membranes">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="aspect_amnio" class="form-label"> <b>Aspect du liquide amniotique : </b> </label>
                                  <select class="form-select" id="aspect_amnio" name="aspect_amnio">
                                    <option value="" selected="selected">---</option>
                                    <option value="Clair">Clair</option>
                                    <option value="teinté">Teinté</option>
                                    <option value="Meconial">Meconial</option>
                                  </select>
                                </div>
                            </div> 
                        </div>
                        <div class="box-body ribbon-box">
                            <div class="ribbon ribbon-info">Antécédents</div><br /><br />
                            <div class="mb-0">
                                <section>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="antecedents" class="form-label"><b>Antécédents :</b></label>
                                                <div class="c-inputs-stacked">
                                                    <label class="form-label"><i>Médicaux HTA</i></label>
                                                    <input type="radio" id="oui_hta" value="Oui" name="hta"/>
                                                    <label for="oui_hta">Oui</label>
                                                    <input type="radio" id="non_hta" value="Non" name="hta"/>
                                                    <label for="non_hta">Non</label><br />
                                                    <label class="form-label"><i>Diabète</i></label>
                                                    <input type="radio" id="oui_diabete" value="Oui" name="diabete"/>
                                                    <label for="oui_diabete">Oui</label>
                                                    <input type="radio" id="non_diabete" value="Non" name="diabete"/>
                                                    <label for="non_diabete">Non</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="autre_antecedent" class="form-label"> <b>Autre :</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->autre_antecedent }}" class="form-control" id="autre_antecedent" name="autre_antecedent"
                                                    placeholder="Préciser">
                                            </div>
                                        </div>
                                    </div><br><br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="gestite" class="form-label"> <b>Gestité</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->gestite }}" class="form-control" id="gestite" name="gestite"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="parite" class="form-label"> <b>Parité</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->parite }}" class="form-control" id="parite" name="parite"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="parite" class="form-label"> <b>Gémellité</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->gemellite }}" class="form-control" id="gemellite" name="gemellite"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="parite" class="form-label"> <b>Prématuré</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->premature }}" class="form-control" id="premature" name="premature"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="enfant_vivant" class="form-label"> <b>Enft viv.</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->enfant_vivant }}" class="form-control" id="enfant_vivant" name="enfant_vivant"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="enfant_decede" class="form-label"> <b>Enft déc.</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->enfant_decede }}" class="form-control" id="enfant_decede" name="enfant_decede"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="cesarienne" class="form-label"> <b>César.</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->cesarienne }}" class="form-control" id="cesarienne" name="cesarienne"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="Avortement" class="form-label"> <b>Avort.</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->avortement }}" class="form-control" id="Avortement" name="avortement"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="toxemie" class="form-label"> <b>Tox. gravid.:</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->toxemie }}" class="form-control" id="toxemie" name="toxemie"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status_vih" class="form-label"> <b>Statut VIH à l'accueil : </b></label>
                                              <select class="form-select" id="status_vih" name="status_vih">
                                                <option value="" selected="selected">---</option>
                                                <option value="positif">Positif</option>
                                                <option value="negatif">Négatif</option>
                                                <option value="inconnu">Inconnu</option>
                                              </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="num_pec" class="form-label"><b>&nbsp;</b></label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->preciser_num_pec }}" class="form-control" id="num_pec" name="preciser_num_pec"
                                                    placeholder="Préciser N° PEC" style="display:none;">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="form-label"><b>Sous TARV :</b></label>
                                                <div class="c-inputs-stacked">
                                                    <input type="radio" id="cpf_oui" name="tarv" value="oui"/>
                                                    <label for="cpf_oui" class="me-30">Oui</label>
                                                    <input type="radio" id="cpf_non" name="tarv" value="non"/>
                                                    <label for="cpf_non" class="me-30">Non</label><br />
                                                </div>
                                            </div>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <label for="age_cpn" class="form-label"> <b>PMI </b></label>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="age_cpn" class="form-label">Age 1e CPN :</label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->age_1e_cpn }}" class="form-control" id="age_cpn" name="age_1e_cpn"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="nombre_cpn" class="form-label">Nbre CPN :</label>
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->nombre_cpn }}" class="form-control" id="nombre_cpn" name="nombre_cpn"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="status_vaccination_vat" class="form-label">Statut vaccinal VAT :</label>
                                              <select class="form-select" id="status_vaccination_vat" name="status_vaccination_vat">
                                                <option value="" selected="selected">---</option>
                                                <option value="non_vaccine">Non Vacciné</option>
                                                <option value="incompletement_vaccine">Incomplètement vacciné</option>
                                                <option value="correctement_vaccine">Correctement vacciné</option>
                                              </select>
                                            </div>
                                        </div>
                                        <br><br><br>
                                        <br><br><br>
                                    </div>
                                </section>
                            </div>
                       </div>
                    </section>
                </div>
            </div>
              <!-- /.box -->
        </div>
        <div class="col-lg-6 col-12">
            <!-- Basic Forms -->
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-info"> Enfant</div>
                    <section>
                        <br /><br /><br />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> <b>Sexe : </b></label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="sexe_enf_m" value="Masculin" name="sexe_enfant">
                                        <label for="sexe_enf_m" class="me-30">M</label>
                                        <input type="radio" id="sexe_enf_f" value="Feminin" name="sexe_enfant">
                                        <label for="sexe_enf_f" class="me-30">F</label>
                                        <input type="radio" id="ND" value="Inconnu" name="sexe_enfant">
                                        <label for="ND" class="me-30">ND</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="taille_enfant" class="form-label"> <b>Taille : </b> </label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->taille_enfant }}" class="form-control" id="taille_enfant" name="taille_enfant"
                                        placeholder="0 cm">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="poids_enfant" class="form-label"> <b>Poids : </b> </label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->poids_enfant }}" class="form-control" id="poids_enfant" name="poids_enfant"
                                        placeholder="0 g">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="perimetre_cranien" class="form-label"> <b>P.C : </b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->perimetre_cranien }}" class="form-control" id="perimetre_cranien" name="perimetre_cranien"
                                        placeholder="0 cm">
                                </div>
                            </div>
                            <div class="col-md-6 mt-4">
                                <b>INDICE D'APGAR</b>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Coeur</th>
                                            <th>Respiration</th>
                                            <th>Tonus</th>
                                            <th>Couleur</th>
                                            <th>Réflexes</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                               <!-- Ligne pour la première minute -->
                                            <tr>
                                                <td>1mn</td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(1)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(1)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(1)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(1)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(1)"></td>
                                                <td id="total1" name='apgar_1mn'></td>
                                            </tr>
                                            <!-- Ligne pour la cinquième minute -->
                                            <tr>
                                                <td>5mn</td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(2)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(2)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(2)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(2)"></td>
                                                <td><input type="text" class="apgar-input" maxlength="1" oninput="validateInput(this)" onchange="calculateTotal(2)"></td>
                                                <td id="total2" name='apgar_5mn'></td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br /><br />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label"><b>Etat du nouveau-né à la naissance :</b></label>
                                    <div class="c-inputs-stacked">
                                        
                                        <label class="form-label"><i>Terme :</i></label>
                                        <input type="radio" id="terme" name="etat_nouveau_ne" value="A terme" />
                                        <label for="terme">A terme</label>
                                        <input type="radio" id="enf_premature" name="etat_nouveau_ne" value="Prematuré" />
                                        <label for="enf_premature">Prématuré</label>
                                        <input type="radio" id="post_terme" name="etat_nouveau_ne" value="Post-terme" />
                                        <label for="post_terme">Post-terme</label>
                                        <input type="radio" id="vivant" name="etat_nouveau_ne" value="Vivant" />
                                        <label for="vivant">Vivant</label><br>
                                        <label class="form-label"><i>Mort-né :</i></label>
                                        <input type="radio" id="frais" name="mort_ne" value="Frais" />
                                        <label for="frais">Frais</label>
                                        <input type="radio" id="macere" name="mort_ne" value="Macéré" />
                                        <label for="macere">Macéré</label><br>
                                        <label class="form-label"><i>Malformation :</i></label>
                                        <input type="radio" id="malformation_oui" name="malformation" value="Oui" />
                                        <label for="malformation_oui">Oui</label>
                                        <input type="radio" id="malformation_non" name="malformation" value="Non" />
                                        <label for="malformation_non">Non</label> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->type_malformation }}" class="form-control" id="type_malformation" name="type_malformation" placeholder="Préciser le type de malformation" style="display: none;">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_mise_sein" class="form-label"> <b>Allaitement (Date et heure mise au sein) : </b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="datetime-local" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->date_mise_sein }}" name="date_mise_sein" id="date_mise_sein" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="c-inputs-stacked">
                                        <label class="form-label"><b>Conseils pour l'allaitement exclusif :</b></label>
                                        <input type="radio" id="conseil_allaitement" name="conseil_allaitement" value="Oui"/>
                                        <label for="conseil_allaitement" class="me-30"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="c-inputs-stacked">
                                        <label class="form-label"><b>Soins Mère(SMK) :</b></label>
                                        <input type="radio" id="skm_oui" name="skm" value="Oui"/>
                                        <label for="skm_oui" class="me-30">Oui</label>
                                        <input type="radio" id="skm_non" name="skm" value="Non"/>
                                        <label for="skm_non" class="me-30">Non</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prec_traitement" class="form-label"><b>Traitement :</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->preciser_traitement }}" class="form-control" id="prec_traitement" name="preciser_traitement"
                                        placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="sat" name="sat" value="SAT"/>
                                        <label for="sat" class="form-label"><i>S.A.T</i></label>
                                        <input type="radio" id="vit_k1" name="sat" value="Vitamine K1"/>
                                        <label for="vit_k1">Vit. K1</label>
                                        <input type="radio" id="collyre" name="sat" value="Collyre"/>
                                        <label for="collyre">Collyre</label>
                                        <input type="radio" id="soin" name="sat" value="CHX gel 7.1%"/>
                                        <label for="soin" class="me-30">Soins ombili. CHX gel 7.1%</label></br>
                                        <label class="form-label"><i>Prophylaxie ARV :</i></label>
                                        <input type="radio" id="prophylaxie_oui" name="prophylaxie" value="Oui"/>
                                        <label for="prophylaxie_oui" >Oui</label>
                                        <input type="radio" id="prophylaxie_non" name="prophylaxie" value="Non"/>
                                        <label for="prophylaxie_non" >Non</label>
                                        <input type="radio" id="na" name="prophylaxie" value="NA"/>
                                        <label for="na" >Non Applicable</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="evacue_pour" class="form-label"><b>Evacué pour :</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->raison_evacuation }}" class="form-control" id="evacue_pour" name="raison_evacuation"
                                        placeholder="Préciser les raisons">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lieu_evacuation" class="form-label"><b>Lieu :</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->lieu_evacuation }}" class="form-control" id="lieu_evacuation" name="lieu_evacuation"
                                        placeholder="Préciser le lieu">
                                </div>
                            </div>
                        </div>
                        <br /><br />
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_evacuation" class="form-label"> <b>Date d'évacuation : </b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="datetime-local" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->date_evacuation }}" name="date_evacuation" id="date_evacuation" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="c-inputs-stacked">
                                        <label class="form-label"><b>Décédée à la maternité :</b></label>
                                        <input type="radio" id="deces_oui" name="deces_oui" value="Oui"/>
                                        <label for="deces_oui" class="me-30"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_deces_maternite" class="form-label"> <b>Date décès : </b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="datetime-local" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->date_deces_maternite }}" name="date_deces_maternite" id="date_deces_maternite" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <!-- Basic Forms -->
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-info">Offre de Service</div>
                    <div class="mb-0">
                        <section>
                            <br /><br /><br /> <br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"><b>Proposition de test VIH :</b></label>
									  <select class="form-select" id="proposition_test_vih" name="proposition_test_vih">
                                        <option value="" selected="selected">---</option>
                                        <option value="Oui">Oui</option>
                                        <option value="Non">Non</option>
                                        <option value="NA">NA</option>
									  </select>
									</div>
								</div>
                   
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="numero_depistage" class="form-label"> <b>N° Dépistage :</b></label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->numero_depistage }}" class="form-control" id="numero_depistage" name="numero_depistage"
                                            placeholder="Préciser...">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"><b>Resultat de test VIH :</b></label>
									  <select class="form-select" id="resultat_test_vih" name="resultat_test_vih">
                                        <option value="" {{ ($consultation->registre && optional($consultation->registre->registreAccouchement)->resultat_test_vih == '') ? 'selected' : '' }}>---</option>
                                        <option value="Positif" {{ ($consultation->registre && optional($consultation->registre->registreAccouchement)->resultat_test_vih == 'Positif') ? 'selected' : '' }}>Positif</option>
                                        <option value="Négatif" {{ ($consultation->registre && optional($consultation->registre->registreAccouchement)->resultat_test_vih == 'Négatif') ? 'selected' : '' }}>Négatif</option>
                                        <option value="NA" {{ ($consultation->registre && optional($consultation->registre->registreAccouchement)->resultat_test_vih == 'NA') ? 'selected' : '' }}>NA</option>
									  </select>
									</div>
								</div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><b>Annonce du résultat :</b></label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="annonce_oui" name="annonce_resultat_vih" value="Oui"/>
                                            <label for="annonce_oui" class="me-30">Oui</label>
                                            <input type="radio" id="annonce_non" name="annonce_resultat_vih" value="Non"/>
                                            <label for="annonce_non" class="me-30">Non</label>
                                            <input type="radio" id="annonce_na" name="annonce_resultat_vih" value="NA"/>
                                            <label for="annonce_na" class="me-30">NA</label><br />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><b>Initiation ARV :</b></label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="arv_oui" name="init_arv" value="Oui"/>
                                            <label for="arv_oui" class="me-30">Oui</label>
                                            <input type="radio" id="arv_non" name="init_arv" value="Non"/>
                                            <label for="arv_non" class="me-30">Non</label>
                                            <input type="radio" id="arv_na" name="init_arv" value="NA"/>
                                            <label for="arv_na" class="me-30">NA</label><br />
                                        </div>
                                    </div>
                                </div>
                            </div>  <br />
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label"> <b>Offre de service Planification Familiale :</b> </label>
                                    <div class="form-group">
                                        <label class="form-label"> <i>Conseil PF-PPI ( &le; à 48h après accouchement )</i> :</label>
                                        <input type="radio"  id="conseil_oui" name="conseil_pf" value="Oui"/>
                                        <label for="conseil_oui" class="me-30">Oui</label>

                                        <input type="radio" id="conseil_non" name="conseil_pf" value="Non"/>
                                        <label for="conseil_non" class="me-30">Non</label><br />
                                        <div class="c-inputs-stacked">


                                            <label for="radio_charge_virale_na"><i>Méthode adoptée :</i></label>

                                            <input type="radio" id="methode_orale" name="methode_adoptee" value="Orale"/>
                                            <label for="methode_orale">Orale</label>

                                            <input type="radio" id="methode_injectable" name="methode_adoptee" value="Injectable"/>
                                            <label for="methode_injectable">Injectable</label>

                                            <input type="radio" id="methode_implant" name="methode_adoptee" value="Implant"/>
                                            <label for="methode_implant">Implant</label>

                                            <input type="radio" id="methode_diu" name="methode_adoptee" value="DIU"/>
                                            <label for="methode_diu">DIU</label>

                                            <input type="radio" id="methode_permanente" name="methode_adoptee" value="Permanente"/>
                                            <label for="methode_permanente">Permanente</label>

                                            <input type="radio" id="methode_aucune" name="methode_adoptee" value="Aucune"/>
                                            <label for="methode_aucune">Aucune</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                        </section>
                    </div>
                </div>
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-info">Informations Cliniques et Examen Obstétrical</div><br />
                    <div class="mb-0">
                        <section>
                            <br /><br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="groupe_sanguin" class="form-label"> <b>GS Rhésus : </b></label>
									  <select class="form-select" id="group_sanguin" name="group_rhesus">
                                        <option value="" selected="selected">---</option>
                                        <option value="A+">A+</option>
                                        <option value="AB+">AB+</option>
                                        <option value="O+">O+</option>
                                        <option value="B+">B+</option>
                                        <option value="A-">A-</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O-">O-</option>
                                        <option value="B-">B-</option>
									  </select>
									</div>
								</div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ddr" class="form-label"> <b>D.D.R : </b>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->ddr }}" name="ddr" class="form-control"
                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="terme_prevu" class="form-label"> <b>TP : </b>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->terme_prevu }}" name="terme_prevu" class="form-control"
                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">
                                        </div>
                                    </div>
                                </div>
                            </div><br><br>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="hu" class="form-label"> <b>H.U : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->hu }}" class="form-control" id="hu" name="HU"
                                            placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="po" class="form-label"> <b>P.O : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->po }}" class="form-control" id="po" name="PO"
                                            placeholder="0">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="presentation" class="form-label"> <b>Présentation </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->presentation }}" class="form-control" id="presentation" name="presentation"
                                            placeholder="...">
                                    </div>
                                </div>
                            </div><br><br><br>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="rcf" class="form-label"> <b>R.C.F : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->RCF }}" class="form-control" id="rcf" name="RCF"
                                            placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="Excision" class="form-label"> <b>Excision : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->Excision }}" class="form-control" id="Excision" name="Excision"
                                            placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="Oedemes" class="form-label"> <b>Oedemes : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->Oedemes }}" class="form-control" id="Oedemes" name="Oedemes"
                                            placeholder="...">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="varices" class="form-label"> <b>Varices : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->Varices }}" class="form-control" id="varices" name="Varices"
                                            placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bassin" class="form-label"> <b>Bassin : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->Bassin }}" class="form-control" id="bassin" name="Bassin"
                                            placeholder="...">
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="tv" class="form-label"> <b>T.V : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->TV }}" class="form-control" id="tv" name="TV"
                                            placeholder="...">
                                    </div>
                                </div>
                            </div>
                            <br /><br />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><b>Interventions :</b></label>
                                        <input type="text" value="{{ Illuminate\Support\Facades\Auth::user()->name }}" class="form-control" id="interventions" name="interventions" style="color:red;" readonly>
                                    </div>
                                </div><br>
                            </div>
                                <br><br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="actes" class="form-label"> <b>Actes : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->Actes }}" class="form-control" id="actes" name="Actes"
                                            placeholder="Préciser...">
                                    </div>
                                </div>
                            </div>
                            <br />
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-info">Réanimation du Nouveau-né</div>
                    <div class="mb-0">
                        <section>
                            <br /><br /><br />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="c-inputs-stacked">
                                                <label class="form-label"><b>A-t-il respiré/pleuré dans la 1ere minute de vie ? :</b></label>
                                                <input type="radio" id="pleure_oui" name="reaction_bebe" value="Oui"/>
                                                <label for="pleure_oui" class="me-30">Oui</label>

                                                <input type="radio" id="pleure_non" name="reaction_bebe" value="Non"/>
                                                <label for="pleure_non" class="me-30">Non</label><br />

                                                <label class="form-label"><i>Si oui :</i></label>
                                                <input type="radio" id="action_aspiration" name="action_si" value="Aspiration"/>
                                                <label for="action_aspiration" class="me-30">Aspiration</label>

                                                <input type="radio" id="action_ventilattion" name="action_si" value="ballon auto-gonflable"/>
                                                <label for="action_ventilattion" class="me-30">Ventilation avec ballon autogonflable</label>

                                                <input type="radio" id="action_oxygène" name="action_si" value="Oxygène"/>
                                                <label for="action_oxygène" class="me-30">Oxygène</label><br />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="reanimation" class="form-label"> <b>Si réanimer :</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->reanimation }}" name="reanimation" name="reanimation" class="form-control" placeholder="Préciser la durée">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="autre_medoc" class="form-label"> <b>Autres Médicaments administrés :</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->autre_medicament }}" id="autre_medoc" name="autre_medicament" class="form-control" placeholder="Préciser">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-body ribbon-box">
                                    <div class="ribbon ribbon-info">Accouchement et délivrance</div><br /><br><br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="c-inputs-stacked">
                                                    <label class="form-label"><I>Mode d'accouchement :</I></label>
                                                    <input type="radio" id="mode_accouchement_vb" name="mode_accouchement" value="Voie basse"/>
                                                    <label for="mode_accouchement_vb">Voie Basse</label>
                                                    <input type="radio" id="mode_accouchement_cs" name="mode_accouchement" value="Césarienne"/>
                                                    <label for="mode_accouchement_cs">Césarienne</label>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="mode_expulsion" class="form-label"> <b>Mode d'expulsion :</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->mode_expulsion }}" id="mode_expulsion" name="mode_expulsion" class="form-control" placeholder="Préciser le mode">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <div class="c-inputs-stacked">
                                                <label class="form-label"><i>Mode de délivrance :</i></label>
                                                <input type="radio" id="mode_tcc" name="mode_delivrance" value="TCC"/>
                                                <label for="mode_tcc">TCC</label>
                                                <input type="radio" id="mode_normal" name="mode_delivrance" value="Normale"/>
                                                <label for="mode_normal">Normale</label>
                                                <input type="radio" id="mode_artificielle" name="mode_delivrance" value="Artificielle"/>
                                                <label for="mode_artificielle">Artificielle</label><br />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="example-time-input" class="form-label"><b>Délivré à :</b></label>
                                            <div class="input-group mb-3">
                                                <input class="form-control" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->heure_delivrance }}" name="heure_delivrance" type="time" value="" id="example-time-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="Placenta" class="form-label"> <b>Placenta : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->placenta }}" name="placenta" id="placenta" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="examen" class="form-label"> <b>Examen : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->examen }}" name="examen" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="membrane" class="form-label"> <b>Membr. : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->membrane }}" name="membrane" id="membrane" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="cordon" class="form-label"> <b>Cordon : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->cordon }}" name="cordon" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="particularite" class="form-label"> <b>Particu. : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->particularite }}" name="particularite" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="Nbre_vo" class="form-label"> <b>Nbre V.O : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->Nbre_vo }}" name="Nbre_vo" class="form-control" placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div><br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"><b>Révision utérine :</b></label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="revision_uterine_oui" name="revision_uterine" value="Oui"/>
                                                <label for="revision_uterine_oui" class="me-30">Oui</label>

                                                <input type="radio" id="revision_uterine_non" name="revision_uterine" value="Non"/>
                                                <label for="revision_uterine_non" class="me-30">Non</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="ta_apres_accouchement" class="form-label"> <b>TA <small>(après accouchement)</small> :</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->ta_apres_accouchement }}" id="ta_apres_accouchement" name="ta_apres_accouchement" class="form-control" placeholder="Préciser service">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="pouls_apres_accouchement" class="form-label"> <b>Pouls <small>(après accouchement)</small> :</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->pouls_apres_accouchement }}" id="pouls_apres_accouchement" name="pouls_apres_accouchement" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="conscience" class="form-label"> <b>Conscience :</b></label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->conscience }}" id="conscience" name="conscience" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="globule_uterin" class="form-label"> <b>Globe utérin :</b></label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->globule_uterin }}" id="globule_uterin" name="globule_uterin" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="saignement_vulvaire" class="form-label"> <b>Saignem. vulv. :</b></label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->saignement_vulvaire }}" id="saignement_vulvaire" name="saignement_vulvaire" class="form-control" placeholder="...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label"><b>Compli. obsté. :</b></label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="complication_obs_oui" name="complication_obstetricales" value="Oui"/>
                                                <label for="complication_obs_oui" class="me-30">Oui</label>

                                                <input type="radio" id="complication_obs_non" name="complication_obstetricales" value="Non"/>
                                                <label for="complication_obs_non" class="me-30">Non</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="type_complication" class="form-label"> <b>Type de Complications :</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->type_complication }}" id="type_complication" name="type_complication" class="form-control" placeholder="Préciser...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label"><b>Prise en charge des HPPI :</b></label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="prise_en_charge_ubt" name="prise_en_charge_hppi" value="UBT"/>
                                                <label for="prise_en_charge_ubt" class="me-30">UBT</label>

                                                <input type="radio" id="prise_en_charge_oui" name="prise_en_charge_hppi" value="Oui"/>
                                                <label for="prise_en_charge_oui" class="me-30">Oui</label>

                                                <input type="radio" id="prise_en_charge_non" name="prise_en_charge_hppi" value="Non"/>
                                                <label for="prise_en_charge_non" class="me-30">Non</label>

                                                <input type="radio" id="prise_en_charge_autre" name="prise_en_charge_hppi" value="Autre"/>
                                                <label for="prise_en_charge_autre" class="me-30">Autres méthodes</label>

                                                <input type="radio" id="prise_en_charge_referee" name="prise_en_charge_hppi" value="Référées"/>
                                                <label for="prise_en_charge_referee" class="me-30">Référées</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br />
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Ordonnance</label>
                                            <textarea rows="4"  class="form-control" name="ordonnane_mere" placeholder="Sortie de la mère">{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->ordonnane_mere }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Ordonnance</label>
                                            <textarea rows="4" class="form-control" name="ordonnance_enfant" placeholder="Sortie de l'enfant">{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->ordonnance_enfant }}</textarea>
                                        </div>
                                    </div>
                                </div>
                        </div>
                            <br />
                        </section>
                        <div class="col-lg-12 col-12">
                            <div class="box">
                                <div class="box-body ribbon-box">
                                    <div class="ribbon ribbon-info">Sortie de la mère</div>

                                    <div class="mb-0">
                                        <section>
                                            <br /><br /><br />

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="date_sortie" class="form-label"> <b>Date de Sortie : </b>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="datetime-local" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->date_sortie }}" name="date_sortie" id="date_sortie" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="issue_consultation" class="form-label"> <b>Mode de sortie : </b></label>
                                                        <div class="c-inputs-stacked">
                                                            <input type="radio" id="sortie_couche" value="suite-couche" name="mode_sortie" checked required>
                                                            <label for="sortie_couche">Suites des couches</label>
                                                            <input type="radio" id="sortie_domicile" value="sortie" name="mode_sortie">
                                                            <label for="sortie_domicile">Domicile (Sortie)</label>
                                                            <input type="radio" id="sortie_deces" value="declaration-deces-patient" name="mode_sortie">
                                                            <label for="sortie_deces">Décédée</label>
                                                        </div>
                                                    </div>                                                    
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="cause_deces" class="form-label"><b>Cause Décès</b></label>
                                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->cause_deces }}" class="form-control" name="cause_deces" placeholder="Préciser">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="fonction" class="form-label"> <b>Evacuée : </b>
                                                        </label>
                                                        <div class="c-inputs-stacked">
                                                            <input type="radio" id="evacuation_mere" value="Oui" name="evacuation_mere">
                                                            <label for="evacuation_mere" >Oui</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="Motif_evacuation_mere" class="form-label"> <b>Motif d'évacuation : </b> </label>
                                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->Motif_evacuation_mere }}" class="form-control" name="Motif_evacuation_mere" placeholder="Préciser">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="example-time-input" class="form-label"><b>H décision :</b></label>
                                                        <div class="input-group mb-3">
                                                            <input class="form-control" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->heure_decision }}" type="time" name="heure_decision" id="example-time-input">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <label for="heure_depart" class="form-label"><b>H dép. eff. :</b></label>
                                                        <div class="input-group mb-3">
                                                            <input class="form-control" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->heure_depart }}" type="time" value="" name="heure_depart">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="nom_etablissement" class="form-label"> <b>Nom Etablissement  : </b> </label>
                                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->nom_etablissement }}" class="form-control" name="nom_etablissement" placeholder="Ou a eu lieu l'évavuation">
                                                    </div>
                                                </div>
                                            </div>
                                            <br />
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label"><b>Mode d'évacuation :</b></label>
                                                        <div class="c-inputs-stacked">
                                                            <label class="form-label"><i>Ambulance :</i></label>
                                                            <input type="radio" id="ambulance_oui" name="Ambulance" value="Oui"/>
                                                            <label for="ambulance_oui">Oui</label>
                                                            <input type="radio" id="ambulance_non" name="Ambulance" value="Non"/>
                                                            <label for="ambulance_non">Non</label><br />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="preciser_evacuation" class="form-label"> <b>Si non : </b> </label>
                                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->preciser_evacuation }}" class="form-control" name="preciser_evacuation" placeholder="Préciser">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="vit_A_1ere_dose" class="form-label"> <b>Supplémen. VIT A/(1ere dose) : </b>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->vit_A_1ere_dose }}" name="vit_A_1ere_dose" class="form-control"
                                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="vit_A_2e_dose" class="form-label"> <b>Date(2e dose) : </b>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->vit_A_2e_dose }}" name="vit_A_2e_dose" class="form-control"
                                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="date_prochain_rdv" class="form-label"> <b>Date RDV consult. postnatale : </b>
                                                        </label>
                                                        <div class="input-group">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->date_prochain_rdv }}" name="date_prochain_rdv" class="form-control"
                                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="responsable_accouchement" class="form-label"> <b>Responsable de l'accouchement : </b> </label>
                                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->responsable_accouchement }}" class="form-control" name="responsable_accouchement" placeholder="Nom&Prénoms-Fonction-Contact">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label"><b>Fiche de déclaration de naissance renseignée ? :</b></label>
                                                        <div class="c-inputs-stacked">
                                                            <input type="radio" id="fiche_dn_oui" name="fiche_renseignee" value="Oui"/>
                                                            <label for="fiche_dn_oui" class="me-30">Oui</label>
                                                            <input type="radio" id="fiche_dn_non" name="fiche_renseignee" value="Non"/>
                                                            <label for="fiche_dn_non" class="me-30">Non</label>
                                                            <input type="radio" id="fiche_dn_na" name="fiche_renseignee" value="NA"/>
                                                            <label for="fiche_dn_na" class="me-30">NA</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="form-label"><b>Fiche renseignée avec les "infos nécessaires" ? :</b></label>
                                                        <div class="c-inputs-stacked">
                                                            <input type="radio" id="fiche_rens_oui" name="fiche_rens_complet" value="Oui"/>
                                                            <label for="fiche_rens_oui" class="me-30">Oui</label>
                                                            <input type="radio" id="fiche_rens_non" name="fiche_rens_complet" value="Non"/>
                                                            <label for="fiche_rens_non" class="me-30">Non</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
        <hr />
        <a href="{{ route('doctor.consultation.today') }}" class="btn btn-warning me-1"><i class="ti-trash"></i> Annuler</a>
        <input class="btn btn-danger btn-submit" type="submit" value="Enregister" />
    </section>
</form>
<script>
var statutVIHRadios = document.querySelectorAll('input[name="status_vih"]');
var preciserPecInput = document.getElementById("num_pec");

for (var i = 0; i < statutVIHRadios.length; i++) {
    statutVIHRadios[i].addEventListener("click", function() {
        if (this.id === "positif") {
            preciserPecInput.readOnly = false;
        } else {
            preciserPecInput.readOnly = true;
        }
    });
}
</script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
  function togglePreciserChamps() {
    var modeEntreeSelect = $("#mode_entree");
    var preciserInput = $("#preciser");

    if (modeEntreeSelect.val() === "Référée par un centre de santé") {
      preciserInput.attr('placeholder', 'Préciser Centre');
      preciserInput.show();
    } else if (modeEntreeSelect.val() === "autre") {
      preciserInput.attr('placeholder', 'Préciser autre');
      preciserInput.show();
    } else {
      preciserInput.hide();
    }
  }

  $(document).ready(function () {
    togglePreciserChamps();
    $("#mode_entree").change(togglePreciserChamps);
  });
</script>


<script>
    function togglePreciserNumPec() {
      var statusVihSelect = $("#status_vih");
      var preciserNumPecInput = $("#num_pec");
  
      if (statusVihSelect.val() === "positif") {
        preciserNumPecInput.show();
      } else {
        preciserNumPecInput.hide();
      }
    }
  
    $(document).ready(function () {
      togglePreciserNumPec(); 
      $("#status_vih").change(togglePreciserNumPec);
    });
  </script>
<script>
    function calculateTotal(rowNumber) {
        var total = 0;
        var inputs = document.querySelectorAll('tr:nth-child(' + rowNumber + ') .apgar-input');
        for (var i = 0; i < inputs.length; i++) {
            var inputValue = parseInt(inputs[i].value) || 0;
            total += inputValue;
        }
        document.getElementById('total' + rowNumber).innerText = total;

        // Envoyez une requête AJAX pour mettre à jour la base de données
        updateDatabase(rowNumber, total);
    }

    function validateInput(input) {
        input.value = input.value.replace(/[^0-2]/g, '');
    }

    function updateDatabase(rowNumber, total) {
        $.ajax({
            type: 'POST',
            url: '/votre-route-de-stockage', // Remplacez par la route appropriée dans votre application Laravel
            data: {
                apgar_1mn: rowNumber === 1 ? total : 0,
                apgar_5mn: rowNumber === 2 ? total : 0,
                _token: '{{ csrf_token() }}' // Ajoutez le jeton CSRF pour Laravel
            },
            success: function(response) {
                console.log(response);
            },
            error: function(error) {
                console.error('Erreur lors de la mise à jour de la base de données:', error);
            }
        });
    }
</script>
<!-- Ajoutez ce script à votre page HTML -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionnez les boutons radio et le champ de texte
        var malformationOui = document.getElementById('malformation_oui');
        var malformationNon = document.getElementById('malformation_non');
        var typeMalformation = document.getElementById('type_malformation');

        // Ajoutez un écouteur d'événements pour le clic sur le bouton radio "Oui"
        malformationOui.addEventListener('click', function () {
            // Si le bouton radio "Oui" est sélectionné, affichez le champ de texte
            if (malformationOui.checked) {
                typeMalformation.style.display = 'block';
            } else {
                // Sinon, masquez le champ de texte
                typeMalformation.style.display = 'none';
            }
        });

        // Ajoutez un écouteur d'événements pour le clic sur le bouton radio "Non"
        malformationNon.addEventListener('click', function () {
            // Si le bouton radio "Non" est sélectionné, masquez le champ de texte
            if (malformationNon.checked) {
                typeMalformation.style.display = 'none';
            }
        });
    });
</script>
{{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    // Sélectionnez les boutons radio et le champ de texte
    var sortieCouche = document.getElementById('sortie_couche');
    var sortieDomicile = document.getElementById('sortie_domicile');
    var sortieDeces = document.getElementById('sortie_deces');
    var causeDeces = document.getElementById('cause_deces');

    sortieDeces.addEventListener('click', function () {
        if (sortieDeces.checked) {
            causeDeces.style.display = 'block';
        } else {
            causeDeces.style.display = 'none';
        }
    });

    sortieDomicile.addEventListener('click', function () {
        if (sortieDomicile.checked) {
            causeDeces.style.display = 'none';
        }
    });

    sortieCouche.addEventListener('click', function () {
        // Ajoutez votre logique ici pour le bouton "Suites des couches"
        // Par exemple, vous pourriez masquer le champ cause_deces
        causeDeces.style.display = 'none';
    });
});
<script> --}}