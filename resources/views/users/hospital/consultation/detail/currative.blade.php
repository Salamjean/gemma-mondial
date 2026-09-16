@php
    $regCur = optional(optional($consultation->registre)->registreConsultationCurative);
@endphp
<div class="row bg-gray-300 container">
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label fw-bold">DATE :</label>
            <input type="text" class="form-control" value="{{ Carbon\Carbon::now()->format('d/m/Y') }}" readonly />
        </div>
    </div>

</div>
<br />

<section class="box box-body">

    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="title-section">
                <h4 class="box-title">Données administratives</h4>
            </div>
            <div class="box section-content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_gestante" class="form-label"> <b>Mode d'entrée: </b>
                            </label>
                            <input type="text" class="form-control" value="Autre" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name" class="form-label"> <b>Nom: </b> </label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="ti-user"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="Nom"
                                    value="{{ $consultation->patient->user->name }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="prenom" class="form-label"> <b>Prénom(s): </b></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="ti-user"></i></span>
                                <input type="text" name="prenom" class="form-control" placeholder="Prénom(s)"
                                    value="{{ $consultation->patient->user->prenom }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name" class="form-label"> <b>Profession: </b> </label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="ti-briefcase"></i></span>
                                <input type="text" name="profession" class="form-control" placeholder="Profession"
                                    value="{{ $consultation->patient->profession }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="prenom" class="form-label"> <b>Pays: </b></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" name="nationalite" class="form-control" placeholder="Pays"
                                    value="{{ $consultation->patient->country }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Lieu de naissance: </b></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->patient->birthPlace->name }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Sexe: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->patient->gender }}" readonly>
                            </div>

                        </div>
                    </div>
                    @php
                        $agePatient = \Carbon\Carbon::createFromFormat('d/m/Y', $consultation->patient->birth_date);
                        $age = $agePatient->diffInYears(Carbon\Carbon::now());
                    @endphp
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Age: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $age }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Résidence
                                    habituelle: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->patient->habitualResidence->name }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Résidence actuelle: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->patient->currentResidence->name }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Status conjugal: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->patient->situation_matrimoniale }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Contacts Téléphoniques: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->patient->telephone }}" readonly>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b>Protection sociale: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->patient->assurer }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="title-section">
                        <h4 class="box-title">Examen clinique et constantes physiques du patient</h4>
                    </div>
                    <div class="col-12 mb-5">
                        <label class="form-label fw-bold">Motif de consultation :</label>
                        <textarea name="motif_consultation" class="form-control" placeholder="Motif consultation" rows="4" readonly
                            value="{{ $consultation->admission->motif_consultation }}">{{ $consultation->admission->motif_consultation }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Poids: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->admission->patient->poids }} kg" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Taille: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $consultation->admission->patient->taille }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> IMC: </b></label>
                            <div class="input-group mb-3">

                                 <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->imc }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Zscore: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->zcore }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Température: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->temperature }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Fréquence respiratoire:
                                </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->frequence_respiratoire }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> TA: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->ta }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Pouls: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->pouls }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Périmètre Brachial: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->perimetre_brachial }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Périmètre Crânien: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->perimetre_cranien }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Recherche active de la tuberculose:
                                </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->tuberculose }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"> <b>Examen physique :</b> </label>
                            <div class="input-group">
                                <textarea name="observation" class="form-control" id="observation" cols="10" rows="5" disabled>
                                                {{ $regCur->examen_physique }}
                                            </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"> <b>Diagnostic retenu :</b> </label>
                            <div class="input-group">
                                <textarea name="observation" class="form-control" id="observation" cols="10" rows="5" disabled>
                                                {{ $regCur->diagnostic_retenu }}
                                            </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"> <b>Autres pathologies associées :</b> </label>
                            <div class="input-group">
                                <textarea name="observation" class="form-control" id="observation" cols="10" rows="5" disabled>
                                                {{ $regCur->autre_pathologie_associee }}
                                            </textarea>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="title-section">
                <h4 class="box-title">Antécédents et autres informations</h4>
            </div>
            <div class="box section-content">
                <div class="row">
                    <div class="col-md-12">
                        <div>
                            <label class="form-label fw-bold">Médicaux :</label>
                        </div>
                        @if ($consultation->patient->type_antecedent_medical != '')
                            <textarea class="form-control bg-gray-100" rows="5">{{ $consultation->patient->type_allergie_medicamenteuse }}</textarea>
                        @else
                            <span class="text-null">Pas d'allergie médicale</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> HTA: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->hta ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> DIABETE: </b></label>
                            <div class="input-group mb-3">

                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->diabete ?? '' }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Si autre antecedant précisez:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="lieu_de_naissance" class="form-control"
                                    value="{{ $regCur->antecedent_medical }}"
                                    readonly>
                            </div>

                        </div>

                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"> <b>Chirurgicaux :</b> </label>
                            <div class="input-group">
                                <textarea name="observation" class="form-control" id="observation" cols="10" rows="5" disabled>
                                                {{ $regCur->antecedent_chirurgical }}
                                            </textarea>
                            </div>
                        </div>
                    </div>
                    @if ($consultation->admission->patient->gender == 'feminin')
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lieu_de_naissance" class="form-label"> <b> Gynéco-Obstétricaux:
                                    </b></label>
                                <div class="input-group mb-3">
                                    <input type="text" name="gyneco_obstetrico" class="form-control"
                                        value="{{ $regCur->gyneco_obstetrico }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lieu_de_naissance" class="form-label"> <b> Grossesse en cours:
                                    </b></label>
                                <div class="input-group mb-3">
                                    <input type="text" name="gyneco_obstetrico" class="form-control"
                                        value="{{ $regCur->en_cours_de_grossesse }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lieu_de_naissance" class="form-label"> <b> Description grossesse en cours:
                                    </b></label>
                                <div class="input-group mb-3">
                                    <input type="text" name="gyneco_obstetrico" class="form-control"
                                        value="{{ $regCur->description_grossesse }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Tabac:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->tabac }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Alcool:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->alcool }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Type visite:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->type_visite }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="title-section">
                <h4 class="box-title">Examens complémentaires</h4>
            </div>
            <div class="box box-body">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> TDR Paludisme:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->tdr_paludisme }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Goutte Epaisse:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->goutte_epaise }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> MILDA Enfant de 12 à 59 mois :
                                    Eligible:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->milda_enfant_eligible }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Remise MILDA Enfant de 12 - 59
                                    mois:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->remise_milda_enfant }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> CDIP proposé:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->cdip_propose }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> CDIP réalisé:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->cdip_realise }}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="lieu_de_naissance" class="form-label"> <b> Code dépistage client:
                                </b></label>
                            <div class="input-group mb-3">
                                <input type="text" name="gyneco_obstetrico" class="form-control"
                                    value="{{ $regCur->code_depistage_client }}"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_gestante" class="form-label"> <b>à jeûn (g/l): </b>
                            </label>
                            <input type="text" class="form-control"
                                value="{{ $regCur->glycemie_a_jeun }}"
                                disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_gestante" class="form-label"> <b>non à jeûn (g/l): </b>
                            </label>
                            <input type="text" class="form-control"
                                value="{{ $regCur->glycemie_non_a_jeun }}"
                                disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="numero_gestante" class="form-label"> <b>Glycémie NA: </b>
                            </label>
                            <input type="text" class="form-control"
                                value="{{ $regCur->naglycemeie }}"
                                disabled>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"> <b>Autres examens :</b> </label>
                            <div class="input-group">
                                <textarea name="observation" class="form-control" id="observation" cols="10" rows="5" disabled>
                                                {{ $regCur->autre_examen }}
                                            </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="row">
            <div class="col-12">
                <div class="title-section">
                    <label class="fs-24"> CONDUITE A TENIR Traitement</label>
                    <p class="text-dark fw-bold">Médicaments, posologie, voie d'administration, durée; Conseil
                        hygiéno-diététique</p>
                </div>
                <label class="fs-18">Traitement :</label>
                <textarea class="form-control" rows="10" disabled></textarea>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label"> <b>Issue de cosultation :</b> </label>
                    <div class="input-group">
                        <textarea name="observation" class="form-control" id="observation" cols="10" rows="5" disabled>
                                                {{ $consultation->registre->issue_consultation_justification }}
                                            </textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<style>
    .title-section {
        /*border-color: red !important;*/
        background: #DBDBDB;
        text-align: center;
        /*border-radius: 3px solid red;*/
        border-radius: 10px !important;
    }

    .box.section-content {
        padding: 10px;
    }

    .text-null {
        color: rgb(215, 73, 73);
    }
</style>
