<div class="container">
    <form class="form-horizontal" id="form-curative" action="{{ route('doctor.consultation.store.curative') }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}" />
        @if(request()->has('embed'))
            <input type="hidden" name="embed" value="1" />
        @endif
        <input type="hidden" name="patient_id" value="{{ $consultation->patient_id ?? optional($consultation->admission)->patient_id }}" />
        <div class="box bb-3 border-warning pe-60 pb-20 ps-60 pt-20 bg-color">

            <div class="box-body ribbon-box">
                <div class="box bb-3 border-success p-10">
                    <div class="row mb-5 pe-105 ps-105">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">N° ordre :</label>
                                <input type="text" class="form-control fw-bold" value="0{{ noOrdreConsultation() }}"
                                    disabled />
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label fw-bold">Date :</label>
                                <input type="text" class="form-control fw-bold"
                                    value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" disabled />
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title fw-bold">Motif de consultation <span class="danger">*</span></h6>
                        </div>
                        <div class="card-body">
                            <div>
                                <textarea type="text" class="form-control" name="motif_consultation"
                                    id="motif_consultation">{{ $consultation->motif_consultation ?? optional($consultation->admission)->motif_consultation ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title fw-bold">Antécédent médicaux </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mt-5 mb-5">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        @php
                                            $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->hta ?? '';
                                        @endphp
                                        <label class="form-label fw-bold"> HTA :</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" {{ $valeur == 'Oui HTA' ? 'checked' : '' }} id="ouiHTA"
                                                value="Oui HTA" name="hta">
                                            <label for="ouiHTA" class="me-30">Oui</label>
                                            <input type="radio" {{ $valeur == 'Non HTA' ? 'checked' : '' }} id="nonHTA"
                                                value="Non HTA" name="hta">
                                            <label for="nonHTA" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        @php
                                            $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->diabete ?? '';
                                        @endphp
                                        <label class="form-label fw-bold">DIABETE :</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" {{ $valeur == 'Oui Diabétique' ? 'checked' : '' }}
                                                id="ouiDiabetique" value="Oui Diabétique" name="diabete">
                                            <label for="ouiDiabetique" class="me-30">Oui</label>
                                            <input type="radio" {{ $valeur == 'Non Diabétique' ? 'checked' : '' }}
                                                id="nonDiabetique" value="Non Diabétique" name="diabete">
                                            <label for="nonDiabetique" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Alcool:</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouiAlcool" value="Oui" name="alcool">
                                            <label for="ouiAlcool" class="me-30">Oui</label>
                                            <input type="radio" id="nonAlcool" value="Non" name="alcool">
                                            <label for="nonAlcool" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold"> Tabac:</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouiTabac" value="Oui" name="tabac">
                                            <label for="ouiTabac" class="me-30">Oui</label>
                                            <input type="radio" id="nonTabac" value="Non" name="tabac">
                                            <label for="nonTabac" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-5 mb-5">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">UGD:</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouiUGD" value="Oui" name="UGD">
                                            <label for="ouiUGD" class="me-30">Oui</label>
                                            <input type="radio" id="nonUGD" value="Non" name="UGD">
                                            <label for="nonUGD" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label fw-bold"> Drépanocytaire:</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouiDrepanocytaire" value="Oui"
                                                name="drepanocytaire">
                                            <label for="ouiDrepanocytaire" class="me-30">Oui</label>
                                            <input type="radio" id="nonDrepanocytaire" value="Non"
                                                name="drepanocytaire">
                                            <label for="nonDrepanocytaire" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold"> Traitement médicaux antérieur/en
                                            cours:</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouitraitement" value="Oui"
                                                name="traitement_medicamenteux">
                                            <label for="ouitraitement" class="me-30">Oui</label>
                                            <input type="radio" id="nontraitement" value="Non"
                                                name="traitement_medicamenteux">
                                            <label for="nontraitement" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold"> Autres antécédents médicaux:</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouiAntecedent" value="Oui"
                                                name="antecedent_medical">
                                            <label for="ouiAntecedent" class="me-30">Oui</label>
                                            <input type="radio" id="nonAntecedent" value="Non"
                                                name="antecedent_medical">
                                            <label for="nonAntecedent" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4" id="formTraitement">
                                    <div class="form-group">
                                        <textarea class="form-control"
                                            placeholder="Traitement médicamenteux antérieur ou en cours"
                                            name="traitement_medicamenteux_anterieur"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4" id="autreAntecedent">
                                    <div class="form-group">
                                        <textarea class="form-control" placeholder="Autre antécédent médical"
                                            name="autre_antecedent_medical"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title fw-bold">Antécédent Chirurgicaux</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label fs-16 fw-500 mt-10">Opération ?</label>
                                    <div class="c-inputs-stacked form-group" id="operation">
                                        <input type="radio" name="antecedent_chirurgical" id="radio-8" value="Oui">
                                        <label class="me-30" for="radio-8">Oui</label>
                                        <input type="radio" name="antecedent_chirurgical" id="radio-9" value="Non">
                                        <label class="me-30" for="radio-9">Non</label>
                                    </div>
                                </div>
                                <div class="col-md-8 form-group" id="nomOperation">
                                    <label class="form-label fs-16 fw-500 mt-10">Préciser l'opération effectuée</label>
                                    <select class="form-select" id="nom_operation" name="nom_operation">
                                        <option value="" disabled selected>---selectionner---</option>
                                        <optgroup label="Chirurgie générale">
                                            <option value="Appendicectomie">Appendicectomie</option>
                                            <option value="Cholécystectomie (ablation de la vésicule biliaire)">
                                                Cholécystectomie (ablation de la vésicule biliaire)</option>
                                            <option value="Hernie réparatrice">Hernie réparatrice</option>
                                            <option
                                                value="Colectomie (ablation d'une partie ou de la totalité du côlon)">
                                                Colectomie (ablation d'une partie ou de la totalité du côlon)</option>
                                        </optgroup>

                                        <optgroup label="Chirurgie cardiothoracique">
                                            <option value="Pontage coronarien">Pontage coronarien</option>
                                            <option value="Remplacement de valve cardiaque">Remplacement de valve
                                                cardiaque</option>
                                            <option value="Réparation d'anévrisme aortique">Réparation d'anévrisme
                                                aortique</option>
                                            <option value="Lobectomie pulmonaire">Lobectomie pulmonaire</option>
                                        </optgroup>
                                        <optgroup label="Chirurgie orthopédique">
                                            <option value="Arthroplastie">Arthroplastie</option>
                                            <option value="Réparation de fracture">Réparation de fracture</option>
                                            <option value="Fusion vertébrale">Fusion vertébrale</option>
                                            <option value="Chirurgie du ligament croisé antérieur (LCA)">Chirurgie du
                                                ligament croisé antérieur (LCA)</option>
                                        </optgroup>

                                        <optgroup label="Chirurgie gynécologique">
                                            <option value="Hystérectomie">Hystérectomie</option>
                                            <option value="Ovariectomie (ablation des ovaires)">Ovariectomie (ablation
                                                des ovaires)</option>
                                            <option value="Réparation de prolapsus pelvien">Réparation de prolapsus
                                                pelvien</option>
                                            <option value="Stérilisation tubaire">Stérilisation tubaire</option>
                                        </optgroup>
                                        <optgroup label="Chirurgie urologique">
                                            <option value="Prostatectomie">Prostatectomie</option>
                                            <option value="Néphrectomie (ablation d'un rein)">Néphrectomie (ablation
                                                d'un rein)</option>
                                            <option value="Lithotomie (enlèvement de calculs rénaux ou vésicaux)">
                                                Lithotomie (enlèvement de calculs rénaux ou vésicaux)</option>
                                            <option value="Cystectomie (ablation de la vessie)">Cystectomie (ablation de
                                                la vessie)</option>
                                        </optgroup>

                                        <optgroup label="Chirurgie plastique et reconstructive">
                                            <option value="Augmentation mammaire">Augmentation mammaire</option>
                                            <option value="Rhinoplastie">Rhinoplastie</option>
                                            <option value="Liposuccion">Liposuccion</option>
                                            <option value="Reconstruction mammaire">Reconstruction mammaire</option>
                                        </optgroup>
                                        <optgroup label="Chirurgie neurologique">
                                            <option value="Craniotomie (ouverture du crâne)">Craniotomie (ouverture du
                                                crâne)</option>
                                            <option value="Décompression du nerf">Décompression du nerf</option>
                                            <option value="Résection de tumeur cérébrale">Résection de tumeur cérébrale
                                            </option>
                                            <option value="Chirurgie de la colonne vertébrale">Chirurgie de la colonne
                                                vertébrale</option>
                                        </optgroup>

                                        <optgroup label="Chirurgie ophtalmologique">
                                            <option value="Chirurgie de la cataracte">Chirurgie de la cataracte</option>
                                            <option value="Correction de la vision au laser (LASIK)">Correction de la
                                                vision au laser (LASIK)</option>
                                            <option value="Greffe de cornée">Greffe de cornée</option>
                                            <option value="Réparation de décollement de rétine">Réparation de
                                                décollement de rétine</option>
                                        </optgroup>

                                        <optgroup label="Chirurgie de l'oreille, du nez et de la gorge (ORL)">
                                            <option value="Tympanoplastie (réparation du tympan)">Tympanoplastie
                                                (réparation du tympan)</option>
                                            <option value="Adénoïdectomie">Adénoïdectomie</option>
                                            <option value="Amygdalectomie">Amygdalectomie</option>
                                            <option value="Septoplastie (correction de la cloison nasale)">Septoplastie
                                                (correction de la cloison nasale)</option>
                                        </optgroup>

                                        <optgroup label="Chirurgie gastro-intestinale">
                                            <option value="Gastrostomie (pose d'une sonde d'alimentation)">Gastrostomie
                                                (pose d'une sonde d'alimentation)</option>
                                            <option value="Résection intestinale">Résection intestinale</option>
                                            <option value="Proctectomie (ablation du rectum)">Proctectomie (ablation du
                                                rectum)</option>
                                            <option value="Chirurgie bariatrique (pour la perte de poids)">Chirurgie
                                                bariatrique (pour la perte de poids)</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-5">
                                @if (strtolower($consultation->admission->patient->gender ?? ($consultation->patient->gender ?? '')) != 'masculin')
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Grossesse en cours:</label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="ouigrossesse" value="Oui"
                                                    name="en_cours_de_grossesse" onclick="activerDDR()">
                                                <label for="ouigrossesse" class="me-30">Oui</label>
                                                <input type="radio" id="nongrossesse" value="Non"
                                                    name="en_cours_de_grossesse" onclick="desactiverDDR()">
                                                <label for="nongrossesse" class="me-30">Non</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">DDR:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="ddr" id="ddr" class="form-control"
                                                    data-inputmask="'alias': 'dd/mm/yyyy'" data-mask="" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Autres:</label>
                                        <div>
                                            <input type="text" class="form-control" name="autre_antecedent_chirurgical">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @if (strtolower($consultation->admission->patient->gender ?? ($consultation->patient->gender ?? '')) != 'masculin')
                                <div class="row mt-5" id="descGrossesse">
                                    <div class="col-md-8 mb-10">
                                        <textarea rows="5" class="form-control" placeholder="Description de la grossesse"
                                            name="description_grossesse"></textarea>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title fw-bold">Examen clinique et constantes physiques </h6>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                @php
                                    $regCur = optional(optional($consultation->registre)->registreConsultationCurative);
                                    $valPoids = $consultation->poids ?: ($regCur->poids ?? '');
                                    $valTaille = $consultation->taille ?: ($regCur->taille ?? '');
                                    $valImc = $consultation->imc ?: ($regCur->imc ?? '');
                                    $valTemp = $consultation->temperature ?: ($regCur->temperature ?? '');
                                    $valTA = $consultation->tension_arterielle ?: ($regCur->ta ?? '');
                                    $valPouls = $consultation->pouls ?: ($regCur->pouls ?? '');
                                @endphp
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="poids" class="form-label"> <b>Poids(kg)</b></label>
                                        <input type="text" class="form-control" id="poids" name="poids"
                                            value="{{ $valPoids }}" placeholder="____Kg" {{ !empty($valPoids) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="taille" class="form-label"> <b>Taille</b></label>
                                        <input type="text" class="form-control" id="taille" name="taille"
                                            value="{{ $valTaille }}" placeholder="____m" {{ !empty($valTaille) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="imc" class="form-label"><b>IMC</b></label>
                                        <input type="text" class="form-control" id="imc" name="imc"
                                            value="{{ $valImc }}" placeholder="_____Kg/m²" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="temperature" class="form-label"> <b>Température(°C)</b></label>
                                        <input type="text" class="form-control" id="temperature" name="temperature"
                                            value="{{ $valTemp }}" placeholder="______°C" {{ !empty($valTemp) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="ta" class="form-label"> <b>TA</b></label>
                                        <input type="text" class="form-control @error('ta') is-invalid @enderror"
                                            id="ta" value="{{ $valTA }}" name="ta"
                                            placeholder="_______mmHg" {{ !empty($valTA) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pouls" class="form-label"> <b>Pouls</b></label>
                                        <input type="text" class="form-control @error('pouls') is-invalid @enderror"
                                            id="pouls" name="pouls" value="{{ $valPouls }}"
                                            placeholder="______batt/mn" {{ !empty($valPouls) ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="glycemie_a_jeun" class="form-label"> <b>Glycémie(a jeûn)</b></label>
                                        <input type="text"
                                            class="form-control @error('glycemie_a_jeun') is-invalid @enderror"
                                            id="glycemie_a_jeun" name="glycemie_a_jeun"
                                            value="{{ $consultation->gly_a_jeun }}" placeholder="A jeûn(g/l)">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="glycemie_non_a_jeun" class="form-label"> <b>Glycémie(nn
                                                jeûn)</b></label>
                                        <input type="text"
                                            class="form-control @error('glycemie_non_a_jeun') is-invalid @enderror"
                                            id="glycemie_non_a_jeun" name="glycemie_non_a_jeun"
                                            value="{{ $consultation->gly_nn_jeun }}" placeholder="non-jeûn(g/l)">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="saturation_oxygene" class="form-label"> <b>Oxygène</b></label>
                                        <input type="text"
                                            class="form-control @error('saturation_oxygene') is-invalid @enderror"
                                            id="saturation_oxygene" value="{{ $consultation->saturation_oxygene }}"
                                            name="saturation_oxygene">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="perimetre_brachial" class="form-label"> <b>Périmètre
                                                Brachial:</b></label>
                                        <input type="text"
                                            class="form-control @error('perimetre_brachial') is-invalid @enderror"
                                            id="perimetre_brachial" name="perimetre_brachial"
                                            value="{{ $consultation->perimetre_brach }}" placeholder="______cm">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Périmètre Crânien:</label>
                                        <input class="form-control" type="text" placeholder=" _____cm"
                                            name="perimetre_cranien" id="perimetre_cranien" />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Zscore:</label>
                                        <input class="form-control" type="text" placeholder="Zscore" name="zscore"
                                            id="zscore" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Fréquence respiratoire:</label>
                                        <input class="form-control" type="text" placeholder="_____cycles/mn"
                                            name="frequence_respiratoire" id="zscore" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Recherche active de la tuberculose :</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouiTuberculose" value="Oui" name="tuberculose">
                                            <label for="ouiTuberculose" class="me-30">Oui</label>
                                            <input type="radio" id="nonTuberculose" value="Non" name="tuberculose">
                                            <label for="nonTuberculose" class="me-30">Non</label>
                                            <input type="radio" id="naTuberculose" value="NA" name="tuberculose">
                                            <label for="naTuberculose" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label fw-bold">Autre(s) examens ?:</label>
                                        <div class="c-inputs-stacked">
                                            <input type="radio" id="ouiautreExam" value="Oui" name="autre_examen">
                                            <label for="ouiautreExam" class="me-30">Oui</label>
                                            <input type="radio" id="nonautreExam" value="Non" name="autre_examen">
                                            <label for="nonautreExam" class="me-30">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8 mt-10 mb-10" id="InputAutreExam">
                                    <textarea rows="5" class="form-control" placeholder="autres examens clinique ici"
                                        name="autre_examen_clinique"></textarea>
                                </div>
                            </div>

                            <div>
                                <!--
                                    <div class="card-header fw-bold">Examen clinique</div>
                                    <div class="card-body">
                                        <p class="card-title">Vous avez le choix de <span class="fw-bold">saisir</span> les informations de l'examen clinique ou <span class="fw-bold">téléverser</span> l'image de d'examen clinique</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">saisissez ici :</label>
                                                    <textarea class="form-control" rows="4" type="text" name="examen_clinique" id="examen_clinique"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label fw-bold">Image de l'examen :</label>
                                                    <input type="file" accept=".jpeg,.png" id="fileInput" onchange="previewImage()" name="examen_clinique" multiple/>
                                                    <div class="previews"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Examen physique ?:</label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="ouiExamPhysiq" value="Oui"
                                                    name="examen_physique_q">
                                                <label for="ouiExamPhysiq" class="me-30">Oui</label>
                                                <input type="radio" id="nonExamPhysiq" value="Non"
                                                    name="examen_physique_q">
                                                <label for="nonExamPhysiq" class="me-30">Non</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Diagnostic retenu ?:</label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="ouiDiagnostic" value="Oui"
                                                    name="diagnostic_retenu">
                                                <label for="ouiDiagnostic" class="me-30">Oui</label>
                                                <input type="radio" id="nonDiagnostic" value="Non"
                                                    name="diagnostic_retenu">
                                                <label for="nonDiagnostic" class="me-30">Non</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">Autres pathologies associées ?:</label>
                                            <div class="c-inputs-stacked">
                                                <input type="radio" id="ouiPathologie" value="Oui"
                                                    name="autre_pathologie_associee">
                                                <label for="ouiPathologie" class="me-30">Oui</label>
                                                <input type="radio" id="nonPathologie" value="Non"
                                                    name="autre_pathologie_associee">
                                                <label for="nonPathologie" class="me-30">Non</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mt-10" id="InputExamPhysiq">
                                        <textarea rows="5" class="form-control" name="examen_physique"></textarea>
                                    </div>
                                    <div class="col-md-4 mt-10" id="InputDiagnostic">
                                        <textarea rows="5" class="form-control" name="diagnostic_retenu"></textarea>
                                    </div>
                                    <div class="col-md-4 mt-10" id="InputPathologie">
                                        <textarea rows="5" class="form-control"
                                            name="autre_pathologie_associee"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title fw-bold">Examen complémentaire </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <label class="form-label fw-bold col-3"> TDR Paludisme:</label>
                                        <div class="c-inputs-stacked col-9">
                                             @php
                                                $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->tdr_paludisme ?? '';
                                             @endphp
                                            <input type="radio" {{ $valeur == 'Positif' ? 'checked' : '' }}
                                                id="TDRpositif" value="Positif" name="tdr_paludisme">
                                            <label for="TDRpositif" class="me-30">Positif</label>
                                            <input type="radio" {{ $valeur == 'Negatif' ? 'checked' : '' }}
                                                id="TDRnegatif" value="Negatif" name="tdr_paludisme">
                                            <label for="TDRnegatif" class="me-30">Négatif</label>
                                            <input type="radio" {{ $valeur == 'nonrealise' ? 'checked' : '' }}
                                                id="TDRnonrealise" value="nonrealise" name="tdr_paludisme">
                                            <label for="TDRnonrealise" class="me-30">Non réalisé</label>
                                            <input type="radio" {{ $valeur == 'TDRna' ? 'checked' : '' }} id="TDRna"
                                                value="TDRna" name="tdr_paludisme">
                                            <label for="TDRna" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-5">
                                    <div class="form-group row">
                                        <label class="form-label fw-bold col-3"> Goutte Epaisse:</label>
                                        <div class="c-inputs-stacked col-9">
                                             @php
                                                $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->goutte_epaise ?? '';
                                             @endphp
                                            <input type="radio" {{ $valeur == 'Positive' ? 'checked' : '' }}
                                                id="GEpositif" value="Positive" name="goutte_epaise">
                                            <label for="GEpositif" class="me-30">Positive</label>
                                            <input type="radio" {{ $valeur == 'Negative' ? 'checked' : '' }}
                                                id="GEnegatif" value="Negative" name="goutte_epaise">
                                            <label for="GEnegatif" class="me-30">Négative</label>
                                            <input type="radio" {{ $valeur == 'non realise' ? 'checked' : '' }}
                                                id="GEnonrealise" value="non realise" name="goutte_epaise">
                                            <label for="GEnonrealise" class="me-30">Non réalisé</label>
                                            <input type="radio" {{ $valeur == 'NA' ? 'checked' : '' }} id="GEna"
                                                value="NA" name="goutte_epaise">
                                            <label for="GEna" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="form-group row">
                                        <label class="form-label fw-bold col-7"> MILDA Enfant de 12 à 59 mois :
                                            Eligible<span class="text-danger">*</span></label>
                                        <div class="c-inputs-stacked col-5">
                                             @php
                                                $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->milda_enfant_eligible ?? '';
                                             @endphp
                                            <input type="radio" {{ $valeur == 'Oui' ? 'checked' : '' }} id="ouiMILDA"
                                                value="Oui" name="milda_enfant_eligible">
                                            <label for="ouiMILDA" class="me-30">Oui</label>
                                            <input type="radio" {{ $valeur == 'Non' ? 'checked' : '' }} id="nonMILDA"
                                                value="Non" name="milda_enfant_eligible">
                                            <label for="nonMILDA" class="me-30">Non</label>
                                            <input type="radio" {{ $valeur == 'Na' ? 'checked' : '' }} id="naMILDA"
                                                value="Na" name="milda_enfant_eligible">
                                            <label for="naMILDA" class="me-30">NA</label>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="form-group row">
                                        <label class="form-label fw-bold col-7"> Remise MILDA Enfant de 12 - 59 mois
                                            :</label>
                                        <div class="c-inputs-stacked col-5">
                                             @php
                                                $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->remise_milda_enfant ?? '';
                                             @endphp
                                            <input type="radio" {{ $valeur == 'Oui' ? 'checked' : '' }}
                                                id="ouiremiseMILDA" value="Oui" name="remise_milda_enfant">
                                            <label for="ouiremiseMILDA" class="me-30">Oui</label>
                                            <input type="radio" {{ $valeur == 'Non' ? 'checked' : '' }}
                                                id="nonremiseMILDA" value="Non" name="remise_milda_enfant">
                                            <label for="nonremiseMILDA" class="me-30">Non</label>
                                            <input type="radio" {{ $valeur == 'Na' ? 'checked' : '' }} id="naremiseMILDA"
                                                value="Na" name="remise_milda_enfant">
                                            <label for="naremiseMILDA" class="me-30">NA</label>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="form-group row">
                                        <label class="form-label fw-bold col-4"> CDIP proposé :</label>
                                        <div class="c-inputs-stacked col-8">
                                             @php
                                                $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->cdip_propose ?? '';
                                             @endphp
                                            <input type="radio" {{ $valeur == 'Oui' ? 'checked' : '' }} id="ouiCDIP"
                                                value="Oui" name="cdip_propose">
                                            <label for="ouiCDIP" class="me-30">Oui</label>
                                            <input type="radio" {{ $valeur == 'Non' ? 'checked' : '' }} id="nonCDIP"
                                                value="Non" name="cdip_propose">
                                            <label for="nonCDIP" class="me-30">Non</label>
                                            <input type="radio" {{ $valeur == 'Na' ? 'checked' : '' }} id="naCDIP"
                                                value="Na" name="cdip_propose">
                                            <label for="naCDIP" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="form-group row">
                                        <label class="form-label fw-bold col-4"> CDIP réalisé :</label>
                                        <div class="c-inputs-stacked col-8">
                                             @php
                                                $valeur = optional(optional($consultation->registre)->registreConsultationCurative)->cdip_realise ?? '';
                                             @endphp
                                            <input type="radio" {{ $valeur == 'Oui' ? 'checked' : '' }}
                                                id="ouiCDIPrealise" value="Oui" name="cdip_realise">
                                            <label for="ouiCDIPrealise" class="me-30">Oui</label>
                                            <input type="radio" {{ $valeur == 'Non' ? 'checked' : '' }}
                                                id="nonCDIPrealise" value="Non" name="cdip_realise">
                                            <label for="nonCDIPrealise" class="me-30">Non</label>
                                            <input type="radio" {{ $valeur == 'Na' ? 'checked' : '' }} id="naCDIPrealise"
                                                value="Na" name="cdip_realise">
                                            <label for="naCDIPrealise" class="me-30">NA</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <div class="form-group row">
                                        <label class="form-label col-5"> Code dépistage client :</label>
                                        <div class="col-7">
                                             <input type="text" class="form-control" name="code_depistage_client"
                                                value="{{ optional(optional($consultation->registre)->registreConsultationCurative)->code_depistage_client ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mt-5">
                                    <p> <span class="fw-bold">Glycémie:</span> à jeûn : <input type="text"
                                            class="col-sm-2" name="glycemie_a_jeun"
                                            value="{{ optional(optional($consultation->registre)->registreConsultationCurative)->glycemie_a_jeun ?? '' }}">
                                        g/l, non à jeûn : <input type="text" class="col-sm-2" name="glycemie_non_a_jeun"
                                            value="{{ optional(optional($consultation->registre)->registreConsultationCurative)->glycemie_non_a_jeun ?? '' }}">
                                        g/l , <label for="naGlycemie me-30">NA</label><input type="radio"
                                            id="naGlycemeie" name="naglycemeie"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="row mt-10">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="issue_consultation" class="form-label"> <b>Issue de la consultation: </b>
                                        <span class="danger">*</span></label>
                                    <div class="c-inputs-stacked">
                                        @php
                                            $valeur = $consultation->registre == null ? '' : $consultation->registre->issue_consultation;
                                        @endphp
                                        <input type="radio" {{ $valeur == 'sortie' ? 'checked' : '' }} id="sortie"
                                            value="sortie" name="mode_sortie" required>
                                        <label for="sortie" class="me-30">Sortie</label>
                                        <input type="radio" {{ $valeur == 'hospitalisation' ? 'checked' : '' }}
                                            id="hospitalisation" value="hospitalisation" name="mode_sortie">
                                        <label for="hospitalisation" class="me-30">Hospitalisé(e)</label>
                                        <input type="radio" {{ $valeur == 'observation' ? 'checked' : '' }}
                                            id="mise_en_observation" value="observation" name="mode_sortie">
                                        <label for="mise_en_observation" class="me-30">Mise en observation (M.O)</label>
                                        <input type="radio" {{ $valeur == 'refere-interne' ? 'checked' : '' }}
                                            id="refere_en_interne" value="refere-interne" name="mode_sortie">
                                        <label for="refere_en_interne" class="me-30">Référé(e) en interne</label>
                                        <input type="radio" {{ $valeur == 'refere-externe' ? 'checked' : '' }}
                                            id="refere_en_externe" value="refere-externe" name="mode_sortie">
                                        <label for="refere_en_externe" class="me-30">Référé(e) en externe</label>
                                        <input type="radio" {{ $valeur == 'cas-presume-tb-resume' ? 'checked' : '' }}
                                            id="cas_presume_de_tb_refere" value="cas-presume-tb-resume" name="mode_sortie">
                                        <label for="cas_presume_de_tb_refere" class="me-30">Cas présumé de TB référé</label>
                                        <input type="radio" {{ $valeur == 'a-revoir' ? 'checked' : '' }} id="a_revoir"
                                            value="a-revoir" name="mode_sortie">
                                        <label for="a_revoir" class="me-30">A revoir</label>
                                        <input type="radio" {{ $valeur == 'declaration-deces-patient' ? 'checked' : '' }}
                                            id="decedee" value="declaration-deces-patient" name="mode_sortie">
                                        <label for="decedee" class="me-30">Décédé(e)</label>
                                    </div>
                                    @error('mode_sortie')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                </div>
                    <div class="box-footer">
                        <div class="float-end">
                            @if(!request()->has('embed'))
                                <a href="{{ route('doctor.consultation.today') }}" class="btn btn-warning me-1">
                                    <i class="ti-trash"></i> Annuler
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="ti-save-alt"></i> Enregister la consultation
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </form>
</div>
<script>
    const grossesse = document.querySelectorAll('input[type=radio][name="en_cours_de_grossesse"]');
    const inputGrossesse = document.getElementById('descGrossesse');

    if (inputGrossesse) {
        inputGrossesse.style.display = 'none';

        Array.prototype.forEach.call(grossesse, function (radio) {
            radio.addEventListener('change', changeHandlerAM);
        });

        function changeHandlerAM(event) {
            if (this.value == 'Oui') {
                inputGrossesse.style.display = 'block';
                inputGrossesse.required = true;
            } else {
                inputGrossesse.style.display = 'none';
                inputGrossesse.required = false;
            }
        }
    }

    function activerDDR() {
        const ddrEl = document.getElementById('ddr');
        if (ddrEl) ddrEl.removeAttribute('readonly');
    }

    function desactiverDDR() {
        const ddrEl = document.getElementById('ddr');
        if (ddrEl) ddrEl.setAttribute('readonly', 'readonly');
    }
</script>
<script>
    const operation = document.querySelectorAll('input[type=radio][name="antecedent_chirurgical"]');
    const selectOperation = document.getElementById('nomOperation')

    selectOperation.style.display = 'none';

    Array.prototype.forEach.call(operation, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            selectOperation.style.display = 'block';
            selectOperation.required = true;
        } else {
            selectOperation.style.display = 'none';
            selectOperation.required = false;
        }
    }
</script>
<script>
    const autre_examenPhy = document.querySelectorAll('input[type=radio][name="autre_examen"]');
    const InputAutreExam = document.getElementById('InputAutreExam')

    InputAutreExam.style.display = 'none';

    Array.prototype.forEach.call(autre_examenPhy, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            InputAutreExam.style.display = 'block';
            InputAutreExam.required = true;
        } else {
            InputAutreExam.style.display = 'none';
            InputAutreExam.required = false;
        }
    }
</script>
<script>
    const pathologieAssocie = document.querySelectorAll('input[type=radio][name="autre_pathologie_associee"]');
    const InputPathologie = document.getElementById('InputPathologie')

    InputPathologie.style.display = 'none';

    Array.prototype.forEach.call(pathologieAssocie, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            InputPathologie.style.display = 'block';
            InputPathologie.required = true;
        } else {
            InputPathologie.style.display = 'none';
            InputPathologie.required = false;
        }
    }
</script>
<script>
    const Diagnostic = document.querySelectorAll('input[type=radio][name="diagnostic_retenu"]');
    const InputDiagnostic = document.getElementById('InputDiagnostic')

    InputDiagnostic.style.display = 'none';

    Array.prototype.forEach.call(Diagnostic, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            InputDiagnostic.style.display = 'block';
            InputDiagnostic.required = true;
        } else {
            InputDiagnostic.style.display = 'none';
            InputDiagnostic.required = false;
        }
    }
</script>
<script>
    const ExamenPhysique = document.querySelectorAll('input[type=radio][name="examen_physique_q"]');
    const InputExamenPhysique = document.getElementById('InputExamPhysiq');

    InputExamenPhysique.style.display = 'none';

    Array.prototype.forEach.call(ExamenPhysique, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            InputExamenPhysique.style.display = 'block';
            InputExamenPhysique.required = true;
        } else {
            InputExamenPhysique.style.display = 'none';
            InputExamenPhysique.required = false;
        }
    }

</script>
<script>
    const traitementMedical = document.querySelectorAll('input[type=radio][name="traitement_medicamenteux"]');
    const InputTraitementMedical = document.getElementById('formTraitement');

    InputTraitementMedical.style.display = 'none';

    Array.prototype.forEach.call(traitementMedical, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            InputTraitementMedical.style.display = 'block';
            InputTraitementMedical.required = true;
        } else {
            InputTraitementMedical.style.display = 'none';
            InputTraitementMedical.required = false;
        }
    }
</script>
<script>
    function previewImage() {
        var fileInput = document.getElementById('fileInput');
        var previewsContainer = document.querySelector('.previews');
        previewsContainer.innerHTML = '';
        for (var i = 0; i < fileInput.files.length; i++) {
            var file = fileInput.files[i];
            if (file && file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = function () {
                    var img = document.createElement('img');
                    img.src = reader.result;
                    img.className = 'preview-image';
                    previewsContainer.appendChild(img);
                };
            }
        }
    }
</script>
<script>
    const AntecedentMedical = document.querySelectorAll('input[type=radio][name="antecedent_medical"]');
    const InputAntecedentMedical = document.getElementById('autreAntecedent');

    InputAntecedentMedical.style.display = 'none';

    Array.prototype.forEach.call(AntecedentMedical, function (radio) {
        radio.addEventListener('change', changeHandlerAM);
    });

    function changeHandlerAM(event) {
        if (this.value == 'Oui') {
            InputAntecedentMedical.style.display = 'block';
            InputAntecedentMedical.required = true;
        } else {
            InputAntecedentMedical.style.display = 'none';
            InputAntecedentMedical.required = false;
        }
    }

</script>