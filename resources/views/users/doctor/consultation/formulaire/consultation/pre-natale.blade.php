<form action="{{ route('doctor.consultation.store.pre.natale')}}" method="POST">
    @csrf
     <!-- /.BLOC 01 -->
 <section class="content">
    <div class="row">
<div class="col-lg-12 col-12">
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-dark">Renseignements Administratifs de la Patiente</div>
                    <section>
                        <br/><br/><br/><br/>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_consultation" class="form-label"> <b>Date de consultation : </b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="hidden" name="consultation_id" value="{{  $consultation->id }}" />
                                        <input type="text" name="date_consultation" id="date_consultation"
                                            class="form-control" data-inputmask="'alias': 'dd/mm/yyyy'"
                                            data-mask="" style="color: blue;" value="{{ date('d/m/Y') }}" required autofocus>
                                    </div>
                                </div>
                            </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="num_gest_prec" class="form-label"> <b>Report numéro gestante précédent :</b> <span class="danger">*</span> </label>
                                        <input type="text"
                                        value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->num_gest_prec : '' }}"
                                         class="form-control" name="num_gest_prec" id="num_gest_prec" placeholder="08/B/2020/CPN2/R3/P15">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="num_gest_visite" class="form-label"> <b>Numéro Gestante de la visite</b> <span class="danger">*</span> </label>
                                        <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->num_gest_visite : '' }}" class="form-control" name="num_gest_visite" id="num_gest_visite" placeholder="08/B/2020/CPN2/R3/P15">
                                    </div>
                                </div>
                                <br>

                        </div>
                        <div class="row">
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
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPreNatale->preciser_centre }}" class="form-control" id="preciser" name="preciser_centre" placeholder="Préciser" style="display:none;">
                                    <input type="text" class="form-control" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPreNatale->preciser_autre }}" id="preciser" name="preciser_autre" placeholder="Préciser autre" style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="preciser_autre" class="form-label"><b>&nbsp;</b></label>
                                    <input type="text" class="form-control" id="preciser_autre" name="preciser_autre"
                                        placeholder="Préciser autre" style="visibility:hidden;">
                                </div>
                            </div>
                        </div>  
                        <br>
                        <br/>
                    </section>
                </div>
            </div>
        </div>
     <!-- /.BLOC 02 -->
        <div class="col-lg-6 col-12">
            <!-- Basic Forms -->
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-dark"> Renseignements Médicales de la Patiente</div>
                    <section>
                        <br /><br /><br />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="antecedents" class="form-label"><b>Antécédents :</b></label>
                                    <div class="c-inputs-stacked">
                                        <label class="form-label"><i>Médicaux HTA</i></label>
                                        <input type="radio" id="hta_oui" value="oui" name="hta"/>
                                        <label for="hta_oui">Oui</label>
                                        <input type="radio" id="hta_non" value="non" name="hta"/>
                                        <label for="hta_non">Non</label><br>
                                        <label class="form-label"><i>Diabète</i></label>
                                        <input type="radio" id="diabete_oui" value="oui" name="diabete"/>
                                        <label for="diabete_oui">Oui</label>
                                        <input type="radio" id="diabete_non" value="non" name="diabete"/>
                                        <label for="diabete_non">Non</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="chirurgicaux" class="form-label"> <b>Chirurgicaux :</b></label>
                                    <input type="text" class="form-control" id="chirurgicaux" name="chirurgicaux"
                                    value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->chirurgicaux : '' }}"
                                        placeholder="Préciser...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="obstetricaux" class="form-label"> <b>Obstétricaux :</b></label>
                                    <input type="text" class="form-control" id="obstetricaux" name="obstetricaux"
                                    value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->obstetricaux : '' }}"
                                        placeholder="Préciser...">
                                </div>
                            </div>
                        </div><br><br>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="gestite" class="form-label"> <b>Gestité</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPreNatale->gestite }}" class="form-control" id="gestite" name="gestite"
                                        placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="parite" class="form-label"> <b>Parité</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPreNatale->parite }}" class="form-control" id="parite" name="parite"
                                        placeholder="0">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="enfant_vivant" class="form-label"> <b>Enft viv.</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPreNatale->enfant_vivant }}" class="form-control" id="enfant_vivant" name="enfant_vivant"
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
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="toxemie" class="form-label"> <b>Tox. gravid.:</b></label>
                                    <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreAccouchement->toxemie }}" class="form-control" id="toxemie" name="toxemie"
                                        placeholder="0">
                                </div>
                            </div>
                        </div><br/>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ddr" class="form-label"> <b>DDR : </b>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="ddr" class="form-control"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->ddr : '' }}"
                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" placeholder="DDR">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="terme_prevu" class="form-label"> <b>T.P. : </b>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="terme_prevu" class="form-control"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->terme_prevu : '' }}"
                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" placeholder="Terme Prévu">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="derniere_cpn" class="form-label"> <b>Date : </b>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" name="derniere_cpn" class="form-control"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->derniere_cpn : '' }}"
                                                data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" placeholder="dernière CPN">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="semaine_amenor" class="form-label"> <b>Semaines d'aménor. : </b> </label>
                                        <input type="text" class="form-control" id="semaine_amenor" name="semaine_amenor"
                                        value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->semaine_amenor : '' }}"
                                            placeholder="0">
                                    </div>
                                </div>
                            </div>
                        <br><br>
                        <div class="row">
                            <div class="col-md-10" id="type_visite">
                                <div class="form-group">
                                    <label class="form-label"><b>Type de visite :</b></label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="cpn1_1erT" name="type_visite" value="cpn1_1erT"/>
                                        <label for="cpn1_1erT" class="me-30">CPN1 au 1er trimestre de la grossesse</label>
                                        <input type="radio" id="cpn1_autreT" name="type_visite" value="cpn1_autreT"/>
                                        <label for="cpn1_autreT" class="me-30">CPN1 autre trimestre</label>
                                        <input type="radio" id="cpn2" name="type_visite" value="cpn2"/>
                                        <label for="cpn2" class="me-30">CPN2</label>
                                        <input type="radio" id="cpn3" name="type_visite" value="cpn3"/>
                                        <label for="cpn3" class="me-30">CPN3</label>
                                        <input type="radio" id="cpn4_9e_mois" name="type_visite" value="cpn4_9e_mois"/>
                                        <label for="cpn4_9e_mois" class="me-30">CPN4 au 9e mois de grossesse</label>
                                        <input type="radio" id="cpn4_autreT" name="type_visite" value="cpn4_autreT"/>
                                        <label for="cpn4_autreT" class="me-30">CPN4 autre trimestre</label>
                                        <input type="radio" id="cpn5_plus" name="type_visite" value="cpn5_plus"/>
                                        <label for="cpn5_plus" class="me-30">CPN5 et plus</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="form-label"> <b>Préciser</b></b></label>
                                    <input type="text" class="form-control" id="preciser_cpn" name="preciser_cpn"
                                    value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->preciser_cpn : '' }}"
                                        placeholder="CPN">
                                </div>
                            </div>
                        </div><br />
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_vat1" class="form-label"> <b>Statut VAT : </b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date_vat1" class="form-control"
                                        value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->date_vat1 : '' }}"
                                            data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" placeholder="Date VAT1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_vat2" class="form-label"><b>&nbsp;</b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date_vat2" class="form-control"
                                        value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->date_vat2 : '' }}"
                                            data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" placeholder="Date VAT2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_vatR" class="form-label"> <b>&nbsp;</b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" name="date_vatR" class="form-control"
                                        value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->date_vatR : '' }}"
                                            data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" placeholder="Date VAT Rappel">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" id="status_vat">
                                <div class="form-group">
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="non_vaccine" name="status_vat" value="non_vaccine"/>
                                        <label for="non_vaccine" class="form-check-label">Non Vacciné&nbsp;&nbsp;&nbsp;</label>
                                        <input type="radio" id="incompletement_vaccine" name="status_vat" value="incompletement_vaccine"/>
                                        <label for="incompletement_vaccine" class="form-check-label">Incomplètement vacciné&nbsp;&nbsp;&nbsp;</label>
                                        <input type="radio" id="correctement_vaccine" name="status_vat" value="correctement_vaccine"/>
                                        <label for="correctement_vaccine" class="form-check-label">Correctement vacciné&nbsp;&nbsp;&nbsp;</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-md-6" id="status_vih">
                                <div class="form-group">
                                    <label class="form-label"> <b>Statut VIH à l'accueil : </b></label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="positif" value="Oui" name="status_vih">
                                        <label for="positif" class="me-30">Positif</label>
                                        <input type="radio" id="negatif" value="Non" name="status_vih">
                                        <label for="negatif" class="me-30">Négatif</label>
                                        <input type="radio" id="inconnu" value="Non" name="status_vih">
                                        <label for="inconnu" class="me-30">Inconnu</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="num_pec" class="form-label"><b>Préciser N° PEC :</b></label>
                                    <input type="text" class="form-control" id="num_pec" name="num_pec"
                                    value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->num_pec : '' }}"
                                        placeholder="N° PEC" @readonly(true)>
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> <b>Proposition de test VIH : </b></label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="oui_vih" value="Oui" name="prop_test">
                                        <label for="oui_vih" class="me-30">Oui</label>
                                        <input type="radio" id="non_vih" value="Non" name="prop_test">
                                        <label for="non_vih" class="me-30">Non</label>
                                        <input type="radio" id="na_vih" value="NA" name="prop_test">
                                        <label for="na_vih" class="me-30">NA</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"> <b>Retesting : </b></label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="r_oui_vih" value="Oui" name="retesting">
                                        <label for="r_oui_vih" class="me-30">Oui</label>
                                        <input type="radio" id="r_non_vih" value="Non" name="retesting">
                                        <label for="r_non_vih" class="me-30">Non</label>
                                        <input type="radio" id="r_na_vih" value="NA" name="retesting">
                                        <label for="r_na_vih" class="me-30">NA</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="num_depistage" class="form-label"><b>N° de Dépistage VIH :</b></label>
                                    <input type="text" class="form-control" id="num_depistage" name="num_depistage"
                                    value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->num_depistage : '' }}"
                                        placeholder="Préciser">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
     <!-- /.BLOC 04 -->

        <div class="col-lg-6 col-12">
            <!-- Basic Forms -->
            <div class="box">
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-dark">Examen général</div>
                    <div class="mb-0">
                        <section>
                            <br /><br /><br />
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="conjonctives" class="form-label"> <b>Conjonc. : </b> </label>
                                        <input type="text" value="{{ $consultation->registre == null ? '' : $consultation->registre->registreConsultationPreNatale->conjonctives }}" class="form-control" id="conjonctives" name="conjonctives"
                                            placeholder="...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="seins" class="form-label"> <b>seins : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="seins"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->seins : '' }}" class="form-control" placeholder="...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="vergetures" class="form-label"> <b>Verget. : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="vergetures" class="form-control"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->vergetures : '' }}" placeholder="...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="varices" class="form-label"> <b>Varices : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="varices" class="form-control"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->varices : '' }}" placeholder="...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="oedemes" class="form-label"> <b>Oedèmes : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="oedemes" class="form-control"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->oedemes : '' }}" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="autres" class="form-label"> <b>Autres : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="autres" class="form-control"
                                            value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->autres : '' }}" placeholder="Préciser">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                        </section>
                    </div>
                </div>
                <div class="box-body ribbon-box">
                    <div class="ribbon ribbon-dark">Examen Obstétrical</div>
                    <div class="mb-0">
                        <section>
                            <br /><br /><br />
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="po" class="form-label"> <b>PO : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="po" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->po : '' }}" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="hu" class="form-label"> <b>HU : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="hu" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->hu : '' }}" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="age_gest" class="form-label"> <b>Age Gest. : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="age_gest" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->age_gest : '' }}" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="bdc" class="form-label"> <b>BDC : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="bdc" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->bdc : '' }}" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="presentation" class="form-label"> <b>Présent.: </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="presentation" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->presentation : '' }}" class="form-control" placeholder="...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tv" class="form-label"> <b>TV : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" name="tv" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->tv : '' }}" class="form-control" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="pathologie" class="form-label"> <b>Pathologies associées : </b> </label>
                                        <div class="input-group mb-3">
                                            <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->pathologie : '' }}" name="pathologie" class="form-control" placeholder="...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Anémie Clinique : </b> </label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="anemie_clinique_oui" value="Oui" name="Anemie">
                                            <label for="anemie_clinique_oui" class="me-30">Oui</label>
                                            <input type="radio" id="anemie_clinique_non" value="Non" name="Anemie">
                                            <label for="anemie_clinique_non" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <br /><br /><br /> <br /><br /><br />
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Etat nutritionnel :</b> </label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="en_normal" value="Normal" name="etat_nutri">
                                            <label for="en_normal" class="me-30">Normal</label>
                                            <input type="radio" id="en_mam" value="Malnutrition aigue modérée" name="etat_nutri">
                                            <label for="en_mam" class="me-30">Malnutrition aigue modérée</label>
                                            <input type="radio" id="mas" value="Malnutrition aigue sévère" name="etat_nutri">
                                            <label for="mas" class="me-30">Malnutrition aigue sévère</label>
                                            <input type="radio" id="en_surpoids" value="Surpoids" name="etat_nutri">
                                            <label for="en_surpoids" class="me-30">Surpoids</label>
                                            <input type="radio" id="obesite" value="Obésité" name="etat_nutri">
                                            <label for="obesite" class="me-30">Obésité</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br /><br />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><b>Proposition d'offre de service Planification Familiale dans le PPI :</b></label>
                                        <div class="c-inputs-stacked">
                                            <label class="form-label"><i>Conseil PF :</i></label>
                                            <input type="radio" id="cpf_oui" name="conseil_pf" value="oui"/>
                                            <label for="cpf_oui" class="me-30">Oui</label>
                                            <input type="radio" id="cpf_non" name="conseil_pf" value="non"/>
                                            <label for="cpf_non" class="me-30">Non</label><br />
                                            <label class="form-label"><i>Méthode souhaitée :</i></label>
                                            <input type="radio" id="orale" name="methode" value="orale"/>
                                            <label for="orale" class="me-30">Orale</label>
                                            <input type="radio" id="injectable" name="methode" value="injectable"/>
                                            <label for="injectable" class="me-30">Injectable</label>
                                            <input type="radio" id="implant" name="methode" value="implant"/>
                                            <label for="implant" class="me-30">Implant</label>
                                            <input type="radio" id="dui" name="methode" value="diu"/>
                                            <label for="dui" class="me-30">DIU</label>
                                        </div>
                                    </div>
                                </div>

                                <br /><br /><br /> <br /><br /><br />
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><b>Issue de la consultation :</b></label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="grossesse_normale" name="resultat_consultation" value="grossesse-normale"/>
                                            <label for="grossesse_normale" class="me-30">Grossesse normale</label>

                                            <input type="radio" id="grossesse_a_risque" name="resultat_consultation" value="grossesse-risque"/>

                                            <label for="grossesse_a_risque" class="me-30">Grossesse à risque</label><br />

                                            <label class="form-label"><i>SI grossesse à risque, Préciser :</i></label>

                                            <input type="radio" id="a_revoir" name="mode_sortie" value="a-revoir" disabled/>
                                            <label for="a_revoir" class="me-30">A Revoir</label>

                                            <input type="radio" id="a_referer" name="mode_sortie" value="refere-interne" disabled/>
                                            <label for="a_referer" class="me-30">A référer</label>

                                            <input type="radio" id="a_hospitaliser" name="mode_sortie" value="hospitalisation" disabled/>
                                            <label for="a_hospitaliser" class="me-30">A hospitaliser</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <br />
                        </section>
                    </div>
                </div>

            </div>
        </div>
     <!-- /.BLOC 05 -->
     <div class="col-lg-12 col-12">
        <div class="box">
            <div class="box-body ribbon-box">
                <div class="ribbon ribbon-dark">Examens Biologiques</div>
                    <div class="mb-0">
                        <section>
                             <br /><br /><br />
                             <div class="row">
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label for="groupe_sanguin" class="form-label"> <b>GS Rhésus : </b></label>
                                        <select class="form-select" id="group_sanguin" name="groupe_rhesus">
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
                                    <div class="col-md-1">
                                         <div class="form-group">
                                                 <label for="taux_hemoglo" class="form-label"> <b>Taux Hémo.: </b> </label>
                                                 <div class="input-group mb-3">
                                                 <input type="text" name="taux_hemoglo" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->taux_hemoglo : '' }}" class="form-control" placeholder="0%">
                                                 </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                                    <label for="electrophese" class="form-label"> <b>E.Hémoglo.: </b></label>
                                                <div class="input-group mb-3">
                                                     <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->electrophèse : '' }}" name="electrophèse" class="form-control" placeholder="">
                                                </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                         <div class="form-group">
                                                    <label class="form-label"> <b>Analyse d'urine :</b> </label>
                                                <div class="c-inputs-stacked">
                                                    <label class="form_label"><i>Albumine :</i> </label>
                                                    <input type="radio" id="alb_positif" value="positif" name="albumine">
                                                    <label for="alb_positif" class="me-30">Positif</label>
                                                    <input type="radio" id="alb_negatif" value="negatif" name="albumine">
                                                    <label for="alb_negatif" class="me-30">Négatif</label>|&nbsp;&nbsp;
                                                    <label class="form-label"> <i>Sucre :</i> </label>
                                                    <input type="radio" id="sucre_positif" value="positif" name="sucre">
                                                    <label for="sucre_positif" class="me-30">Positif</label>
                                                    <input type="radio" id="sucre_negatif" value="negatif" name="sucre">
                                                    <label for="sucre_negatif" class="me-30">Négatif</label>
                                                </div>
                                        </div>
                                    </div>
                                </div><hr>
                            <div class="row">
                                <br>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Recherche de nitrite dans les urines/ECBU :</b> </label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ecbu_positif" name="ecbu" value="positif"/>
                                            <label for="ecbu_positif" class="me-30">Positif</label>
                                            <input type="radio" id="ecbu_negatif" name="ecbu" value="negatif"/>
                                            <label for="ecbu_negatif" class="me-30">Négatif</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label class="form-label"><b>Glycémie :</b></label>
                                        <div class="c-inputs-stacked">
                                            <label class="form-label"><i>Examen demandé :</i></label>
                                            <input type="radio" id="glyc_exa_oui" name="glyc_exa" value="oui"/>
                                            <label for="glyc_exa_oui" class="me-30">Oui</label>
                                            <input type="radio" id="glyc_exa_non" name="glyc_exa" value="non"/>
                                            <label for="glyc_exa_non" class="me-30">Non</label>
                                            <input type="radio" id="glyc_exa_na" name="glyc_exa" value="na"/>
                                            <label for="glyc_exa_na" class="me-30">NA</label>|&nbsp;&nbsp;
                                            <label class="form-label"><i>Résultat reçu :</i></label>
                                            <input type="radio" id="glyc_recu_oui" name="glyc_recu" value="oui"/>
                                            <label for="glyc_recu_oui" class="me-30">Oui</label>
                                            <input type="radio" id="glyc_recu_non" name="glyc_recu" value="non"/>
                                            <label for="glyc_recu_non" class="me-30">Non</label>
                                            <input type="radio" id="glyc_recu_na" name="glyc_recu" value="na"/>
                                            <label for="glyc_recu_na" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="resul_ajeun" class="form-label"> <b>Résultat : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->resul_ajeun : '' }}" name="resul_ajeun" class="form-control" placeholder="Glycémie à jeûn (g/l)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="resul_najeun" class="form-label"> <b>&nbsp;</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->resul_najeun : '' }}" name="resul_najeun" class="form-control" placeholder="Glycémie non à jeûn (g/l)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label"> <b>&nbsp;</b></label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="glyc_resul_na" value="na" name="glyc_resul_na">
                                                <label for="glyc_resul_na" class="me-30">NA</label>
                                            </div>
                                        </div>
                                    </div>
                            </div><hr>
                             <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label"><b>Test de Syphilis :</b></label>
                                            <div class="c-inputs-stacked">
                                                <label class="form-label"><i>Examen demandé :</i></label>
                                                <input type="radio" id="syph_exa_oui" name="syph_exa" value="oui"/>
                                                <label for="syph_exa_oui" class="me-30">Oui</label>
                                                <input type="radio" id="syph_exa_non" name="syph_exa" value="non"/>
                                                <label for="syph_exa_non" class="me-30">Non</label>|&nbsp;&nbsp;
                                                <label class="form-label"><i>Résultat reçu :</i></label>
                                                <input type="radio" id="syph_recu_oui" name="syph_recu" value="oui"/>
                                                <label for="syph_recu_oui" class="me-30">Oui</label>
                                                <input type="radio" id="syph_recu_non" name="syph_recu" value="non"/>
                                                <label for="syph_recu_non" class="me-30">Non</label>|&nbsp;&nbsp;
                                                <label class="form-label"><b>Résultat :</b></label>
                                                <input type="radio" id="resul_syph_negatif" name="resul_syph" value="negatif"/>
                                                <label for="resul_syph_negatif" class="me-30">Négatif</label>
                                                <input type="radio" id="resul_syph_positif" name="resul_syph" value="positif"/>
                                                <label for="resul_syph_positif" class="me-30">Positif</label>
                                                <input type="radio" id="resul_syph_na" name="resul_syph" value="na"/>
                                                <label for="resul_syph_na" class="me-30">NA</label>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="st_igG" class="form-label"> <b>Sérologie Toxoplasmose : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->st_igG : '' }}" name="st_igG" class="form-control" placeholder="IgG">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="st_igM" class="form-label"> <b>&nbsp;</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->st_igM : '' }}" name="st_igM" class="form-control" placeholder="IgM">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sr_igG" class="form-label"> <b>Sérologie Rubéole : </b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->sr_igG : '' }}" name="sr_igG" class="form-control" placeholder="IgG">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sr_igM" class="form-label"> <b>&nbsp;</b> </label>
                                            <div class="input-group mb-3">
                                                <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->sr_igM : '' }}" name="sr_igM" class="form-control" placeholder="IgM">
                                            </div>
                                        </div>
                                    </div>
                            </div><hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><b>Test de AgHBs :</b></label>
                                        <div class="c-inputs-stacked">
                                            <label class="form-label"><i>Examen demandé :</i></label>
                                            <input type="radio" id="agHBs_exa_oui" name="agHBs_exa" value="oui"/>
                                            <label for="agHBs_exa_oui" class="me-30">Oui</label>
                                            <input type="radio" id="agHBs_exa_non" name="agHBs_exa" value="non"/>
                                            <label for="agHBs_exa_non" class="me-30">Non</label>
                                            <input type="radio" id="agHBs_exa_na" name="agHBs_exa" value="na"/>
                                            <label for="agHBs_exa_na" class="me-30">NA</label>|&nbsp;&nbsp;
                                            <label class="form-label"><i>Résultat reçu :</i></label>
                                            <input type="radio" id="agHBs_recu_oui" name="agHBs_recu" value="oui"/>
                                            <label for="agHBs_recu_oui" class="me-30">Oui</label>
                                            <input type="radio" id="agHBs_recu_non" name="agHBs_recu" value="non"/>
                                            <label for="agHBs_recu_non" class="me-30">Non</label>
                                            <input type="radio" id="agHBs_recu_na" name="agHBs_recu" value="na"/>
                                            <label for="agHBs_recu_na" class="me-30">NA</label>|&nbsp;&nbsp;
                                            <label class="form-label"><b>Résultat :</b></label>
                                            <input type="radio" id="agHBs_resul_negatif" name="agHBs_resul" value="negatif"/>
                                            <label for="agHBs_resul_negatif" class="me-30">Négatif</label>
                                            <input type="radio" id="agHBs_resul_positif" name="agHBs_resul" value="positif"/>
                                            <label for="agHBs_resul_positif" class="me-30">Positif</label>
                                            <input type="radio" id="agHBs_resul_na" name="agHBs_resul" value="na"/>
                                            <label for="agHBs_resul_na" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"><b>Résultat Test de Dépistage VIH :</b></label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="resultat_vih_negatif" name="resultat_vih" value="negatif"/>
                                            <label for="resultat_vih_negatif" class="me-30">Négatif</label>
                                            <input type="radio" id="resultat_vih_positif" name="resultat_vih" value="positif"/>
                                            <label for="resultat_vih_positif" class="me-30">Positif</label>
                                            <input type="radio" id="resultat_vih_na" name="resultat_vih" value="na"/>
                                            <label for="resultat_vih_na" class="me-30">NA</label>|&nbsp;&nbsp;
                                            <label class="form-label"><i>Annonce du Résultat VIH :</i></label>
                                            <input type="radio" id="annonce_vih_oui" name="annonce_vih" value="oui"/>
                                            <label for="annonce_vih_oui" class="me-30">Oui</label>
                                            <input type="radio" id="annonce_vih_non" name="annonce_vih" value="non"/>
                                            <label for="annonce_vih_non" class="me-30">Non</label>
                                            <input type="radio" id="annonce_vih_na" name="annonce_vih" value="na"/>
                                            <label for="annonce_vih_na" class="me-30">NA</label><br>
                                            <label class="form-label"><b>Dépistage VIH du Conjoint :</b></label>
                                            <input type="radio" id="conjoint_vih_oui" name="conjoint_vih" value="oui"/>
                                            <label for="conjoint_vih_oui" class="me-30">Oui</label>
                                            <input type="radio" id="conjoint_vih_non" name="conjoint_vih" value="non"/>
                                            <label for="conjoint_vih_non" class="me-30">Non</label>
                                            <input type="radio" id="conjoint_vih_na" name="conjoint_vih" value="na"/>
                                            <label for="conjoint_vih_na" class="me-30">NA</label>|&nbsp;&nbsp;
                                            <label class="form-label"><i>Statut Sérologique VIH du Conjoint :</i></label>
                                            <input type="radio" id="status_vih_negatif" name="status_vih_conjoint" value="negatif"/>
                                            <label for="status_vih_negatif" class="me-30">Négatif</label>
                                            <input type="radio" id="status_vih_positif" name="status_vih_conjoint" value="positif"/>
                                            <label for="status_vih_positif" class="me-30">Positif</label>
                                            <input type="radio" id="status_vih_na" name="status_vih_conjoint" value="na"/>
                                            <label for="status_vih_na" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label"> <b>Prélèvement pour la charge virale  :</b> </label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="Prel_charge_oui" name="Prel_charge" value="oui"/>
                                            <label for="Prel_charge_oui" class="me-30">Oui</label>
                                            <input type="radio" id="Prel_charge_non" name="Prel_charge" value="non"/>
                                            <label for="Prel_charge_non" class="me-30">Non</label>
                                            <input type="radio" id="Prel_charge_na" name="Prel_charge" value="na"/>
                                            <label for="Prel_charge_na" class="me-30">NA</label>
                                            <label class="form-label"> <b>Charge virale &le; à 1000 copies/ml</b>(dernier trimestre de la grossesse) :</label>
                                            <input type="radio" id="cv_inf1000_oui" name="cv_inf1000" value="oui"/>
                                            <label for="cv_inf1000_oui" class="me-30">Oui</label>
                                            <input type="radio" id="cv_inf1000_non" name="cv_inf1000" value="non"/>
                                            <label for="cv_inf1000_non" class="me-30">Non</label>
                                            <input type="radio" id="cv_inf1000_na" name="cv_inf1000" value="na"/>
                                            <label for="cv_inf1000_na" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="autres_exa" class="form-label"> <b>Autres Examens Biologiques : </b> </label>
                                    <div class="input-group mb-3">
                                        <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->autres_exa : '' }}" name="autres_exa" class="form-control" placeholder="Préciser...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exa_echo" class="form-label"> <b>Examens Echographiques ou Radiologiques : </b> </label>
                                    <div class="input-group mb-3">
                                        <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->exa_echo : '' }}" name="exa_echo" class="form-control" placeholder="Préciser...">
                                    </div>
                                </div>
                            </div>
                        </div><hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label"> <b>Prescriptions  :</b> </label>
                                    <div class="c-inputs-stacked">
                                        <input type="checkbox" id="fer" value="Fer" name="prescription"/>
                                        <label for="fer" class="me-30">Fer</label>
                                        <input type="checkbox" id="folates" value="Folates" name="prescription"/>
                                        <label for="folates" class="me-30">Folates</label>
                                        <input type="checkbox" id="sp1" value="SP1" name="prescription"/>
                                        <label for="sp1" class="me-30">SP1</label>
                                        <input type="checkbox" id="sp2" value="SP2" name="prescription"/>
                                        <label for="sp2" class="me-30">SP2</label>
                                        <input type="checkbox" id="sp3" value="SP3" name="prescription"/>
                                        <label for="sp3" class="me-30">SP3</label>
                                        <input type="checkbox" id="sp4" value="SP4" name="prescription"/>
                                        <label for="sp4" class="me-30">SP4</label>
                                        <input type="checkbox" id="sp5_plus" value="SP5+" name="prescription"/>
                                        <label for="sp5_plus" class="me-30">SP5 et plus</label>
                                        <input type="checkbox" id="fluor" value="Fluor" name="prescription"/>
                                        <label for="fluor" class="me-30">Fluor</label>
                                        <input type="checkbox" id="milda" value="Milda" name="prescription"/>
                                        <label for="milda" class="me-30">MILDA</label>
                                        <input type="checkbox" id="deparasitant" value="Déparasitant" name="prescription"/>
                                        <label for="deparasitant" class="me-30">Déparasitant</label>
                                        <input type="checkbox" id="ctx" value="CTX" name="prescription"/>
                                        <label for="ctx" class="me-30">CTX</label>
                                        <input type="checkbox" id="arv" value="ARV" name="prescription"/>
                                        <label for="arv" class="me-30">Initiation ARV</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label"><b>Conseil nutritionnel :</b></label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="radio_conseil_nutritionnel_oui" name="conseil_nutri" value="Oui"/>
                                        <label for="radio_conseil_nutritionnel_oui" class="me-30">Oui</label>

                                        <input type="radio" id="radio_conseil_nutritionnel_non" name="conseil_nutri" value="Non"/>
                                        <label for="radio_conseil_nutritionnel_non" class="me-30">Non</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" id="service_nutritionnel">
                                <div class="form-group">
                                    <label class="form-label"><b>Service nutritionnel :</b></label>
                                    <div class="c-inputs-stacked">
                                        <input type="radio" id="radio_service_nutritionnel_oui" name="service_nutri" value="Oui"/>
                                        <label for="radio_service_nutritionnel_oui" class="me-30">Oui</label>

                                        <input type="radio" id="radio_service_nutritionnel_non" name="service_nutri" value="Non"/>
                                        <label for="radio_service_nutritionnel_non" class="me-30">Non</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="prec_sn" class="form-label"> <b>Préciser :</b> </label>
                                    <div class="input-group mb-3">
                                        <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->prec_sn : '' }}" id="prec_sn" name="prec_sn" class="form-control" placeholder="Préciser service" @readonly(true)>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="autre_medic" class="form-label"> <b>Autres médicaments :</b> </label>
                                    <div class="input-group mb-3">
                                        <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->autre_medic : '' }}" name="autre_medic" class="form-control" placeholder="Préciser">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_prochain_rdv" class="form-label"> <b>Date du prochain RDV : </b>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" value="{{ $consultation->registre && $consultation->registre->registreConsultationPreNatale ? $consultation->registre->registreConsultationPreNatale->date_prochain_rdv : '' }}" name="date_prochain_rdv" class="form-control"
                                            data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

        <hr />
    </div>

            <a href="{{ route('doctor.consultation.today') }}" class="btn btn-warning me-1"><i class="ti-trash"></i> Annuler</a>
            <input class="btn btn-danger btn-submit" type="submit" value="Enregister" />
</section>
</form>
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




var otherRadioButtons = document.querySelectorAll('input[type="radio"][name="type_visite"]');
for (var j = 0; j < otherRadioButtons.length; j++) {
    otherRadioButtons[j].addEventListener("click", function() {
        if (this !== typeVisiteRadio) {
            precisercpnInput.readOnly = true;
        }
    });
}

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

var serviceNutriRadios = document.getElementById("radio_service_nutritionnel_oui");
var preciserServInput = document.getElementById("prec_sn");

serviceNutriRadios.addEventListener("click", function() {
    preciserServInput.readOnly = false;
});

var otherRadioButtons = document.querySelectorAll('input[type="radio"][name="service_nutri"]');
for (var j = 0; j < otherRadioButtons.length; j++) {
    otherRadioButtons[j].addEventListener("click", function() {
        if (this !== serviceNutriRadios) {
            preciserServInput.readOnly = true;
        }
    });
}

    var grossesseARisqueRadios = document.getElementById("grossesse_a_risque");
    var presicerRisqueInput1 = document.getElementById("a_revoir");
    var presicerRisqueInput2 = document.getElementById("a_referer");
    var presicerRisqueInput3 = document.getElementById("a_hospitaliser");

    function activerPresicerRisqueInputs() {
        presicerRisqueInput1.disabled = false;
        presicerRisqueInput2.disabled = false;
        presicerRisqueInput3.disabled = false;
    }

    function desactiverPresicerRisqueInputs() {
        presicerRisqueInput1.disabled = true;
        presicerRisqueInput2.disabled = true;
        presicerRisqueInput3.disabled = true;
    }

    grossesseARisqueRadios.addEventListener("click", function() {
        if (grossesseARisqueRadios.checked) {
            activerPresicerRisqueInputs();
        } else {
            desactiverPresicerRisqueInputs();
        }
    });

    var otherRadioButtons = document.querySelectorAll('input[type="radio"][name="resultat_consultation"]');
    for (var j = 0; j < otherRadioButtons.length; j++) {
        otherRadioButtons[j].addEventListener("click", function() {
            if (this === grossesseARisqueRadios) {
                activerPresicerRisqueInputs();
            } else {
                desactiverPresicerRisqueInputs();
            }
        });
    }

</script>
