@extends('layouts.dashboard', ['title' => "Récapitulatif & Téléchargement PDF - Laboratoire"])

@section('content')
@php
    $patient = $consultation->patient ?? optional($consultation->admission)->patient;
    $patientUser = optional($patient)->user;
    $doctor = $consultation->doctor ?? optional(Auth::user())->doctor;
    $doctorUser = optional($doctor)->user ?? Auth::user();
    $hospital = $consultation->hospital ?? optional($doctor)->hospital ?? Auth::user()->hospital;
    $bulletin = $consultation->bulletinExamen;
    $registre = $consultation->registre;

    $patientFullName = trim((optional($patientUser)->name ?? '') . ' ' . (optional($patientUser)->prenom ?? '')) ?: 'Patient';
    $patientCode = optional($patient)->code_patient ?? ('#' . $consultation->id);
    $doctorFullName = 'Dr. ' . trim((optional($doctorUser)->name ?? '') . ' ' . (optional($doctorUser)->prenom ?? ''));
    $hospitalName = optional($hospital)->label ?: (optional($hospital)->nom_direction_generale ?: 'Hôpital GEMMA');
    $examCount = optional(optional($bulletin)->examens)->count() ?? 0;
    $issue = optional($registre)->issue_consultation ?? 'sortie';
    $justif = optional($registre)->issue_consultation_justification ?? 'Demande d\'analyses de laboratoire émise';
@endphp

<div class="content-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="page-title text-dark fw-bold mb-0">
                <i class="fa-solid fa-flask-vial text-primary me-2"></i> Récapitulatif de la Consultation Laboratoire
            </h4>
            <div class="text-muted fs-13 mt-1">
                Bulletin N° <strong>{{ optional($bulletin)->code_bulletin ?? ('BLAB-' . $consultation->id) }}</strong> &bull; Patient : <strong>{{ $patientFullName }}</strong> ({{ $patientCode }})
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary btn-sm px-3 fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour aux consultations
            </a>
            <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-primary btn-sm px-4 fw-bold">
                <i class="fa-solid fa-file-pdf me-1"></i> Télécharger le PDF
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- COLONNE GAUCHE : RECAPITULATIF COMPLET -->
    <div class="col-lg-8 col-12">

        <!-- BANNIERE SIMPLE DE CONFIRMATION (SANS DEGRADE) -->
        <div class="alert alert-success border border-success-subtle bg-light-subtle p-3 mb-4 rounded">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="text-success fs-24">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-15 text-dark">Demande de laboratoire enregistrée avec succès</div>
                        <div class="fs-12 text-muted">
                            Le bulletin d'examen <strong>{{ optional($bulletin)->code_bulletin ?? ('BLAB-' . $consultation->id) }}</strong> comportant <strong>{{ $examCount }}</strong> analyse(s) a été clôturé et validé.
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-success btn-sm fw-bold px-3 py-2">
                        <i class="fa-solid fa-download me-1"></i> Télécharger le PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- BLOC ISSUE DE CONSULTATION & ORIENTATION -->
        <div class="box mb-4 shadow-none border">
            <div class="box-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-arrows-split-up-and-left text-primary me-2"></i> Issue de Consultation &amp; Orientation
                </h5>
                @if($issue === 'refere-interne' || str_contains($justif, 'infirmier') || str_contains($justif, 'orienté') || str_contains($justif, 'référé'))
                    <span class="badge bg-info-subtle text-info border border-info-subtle fw-bold fs-11 px-2 py-1">Affectation / Référence</span>
                @elseif($issue === 'hospitalisation' || $issue === 'observation')
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold fs-11 px-2 py-1">Hospitalisation / Observation</span>
                @else
                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold fs-11 px-2 py-1">Sortie Domicile</span>
                @endif
            </div>
            <div class="box-body p-3">
                <div class="p-3 bg-light rounded border">
                    <div class="fw-bold fs-13 text-dark mb-1">
                        Mode retenu : 
                        @if($issue === 'refere-interne' || str_contains($justif, 'infirmier') || str_contains($justif, 'orienté') || str_contains($justif, 'référé'))
                            <span class="text-primary">Réaffectation / Prise en charge interne</span>
                        @elseif($issue === 'hospitalisation')
                            <span class="text-danger">Hospitalisation</span>
                        @elseif($issue === 'observation')
                            <span class="text-warning">Mise en observation (M.O)</span>
                        @else
                            <span class="text-success">Sortie autorisée vers le domicile</span>
                        @endif
                    </div>
                    <div class="fs-13 text-secondary">
                        <strong>Détails :</strong> {{ $justif }}
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLEAU DES EXAMENS ENREGISTRES -->
        <div class="box mb-4 shadow-none border">
            <div class="box-header bg-light d-flex justify-content-between align-items-center py-2 px-3">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-list-check text-primary me-2"></i> Analyses Demandées ({{ $examCount }})
                </h5>
                <span class="badge bg-light text-secondary border fs-11">Bulletin N° {{ optional($bulletin)->code_bulletin ?? ('BLAB-' . $consultation->id) }}</span>
            </div>
            <div class="box-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light text-muted fs-11 text-uppercase">
                            <tr>
                                <th class="ps-3" style="width: 45px;">#</th>
                                <th>Code Examen</th>
                                <th>Nature de l'Examen</th>
                                <th>Date</th>
                                <th class="text-center pe-3">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse(optional($bulletin)->examens ?? [] as $index => $exam)
                                <tr>
                                    <td class="ps-3 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $exam->code_examen ?? ('EX-' . $exam->id) }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $exam->nature_examen }}</div>
                                    </td>
                                    <td class="text-muted fs-12">
                                        {{ date('d/m/Y', strtotime($exam->date_examen ?? date('Y-m-d'))) }}
                                    </td>
                                    <td class="text-center pe-3">
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle fs-11">
                                            En attente
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        Aucun examen spécifique détaillé.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(!empty($consultation->motif_consultation))
            <!-- RENSEIGNEMENTS CLINIQUES -->
            <div class="box mb-4 shadow-none border">
                <div class="box-header bg-light py-2 px-3">
                    <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                        <i class="fa-solid fa-comment-medical text-primary me-2"></i> Renseignements Cliniques
                    </h5>
                </div>
                <div class="box-body p-3">
                    <div class="fs-13 text-secondary bg-light p-2.5 rounded border">
                        {{ $consultation->motif_consultation }}
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- COLONNE DROITE : DETAILS PATIENT & ACTIONS -->
    <div class="col-lg-4 col-12">

        <!-- CARTE D'ACTIONS PRINCIPALES -->
        <div class="box mb-4 shadow-none border">
            <div class="box-header bg-light py-2 px-3">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-file-arrow-down text-primary me-2"></i> Actions &amp; Téléchargements
                </h5>
            </div>
            <div class="box-body p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}" target="_blank" class="btn btn-primary fw-bold py-2">
                        <i class="fa-solid fa-file-pdf me-2"></i> Télécharger le PDF
                    </a>
                    <a href="{{ route('doctor.consultation.laboratoire.pdf', $consultation->id) }}?action=print" target="_blank" class="btn btn-outline-secondary fw-semibold py-2">
                        <i class="fa-solid fa-print me-2"></i> Imprimer la Demande
                    </a>
                    <hr class="my-2">
                    <a href="{{ route('doctor.consultation.today') }}" class="btn btn-outline-secondary fw-semibold py-2">
                        <i class="fa-solid fa-list me-2"></i> Liste des Consultations
                    </a>
                    @if(optional($patient)->id)
                        <a href="{{ route('doctor.consultation.patient.card', $patient->id) }}" class="btn btn-outline-secondary fw-semibold py-2">
                            <i class="fa-solid fa-folder-open me-2"></i> Dossier Médical Patient
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- CARTE DOSSIER PATIENT -->
        <div class="box mb-4 shadow-none border">
            <div class="box-header bg-light py-2 px-3">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-user text-primary me-2"></i> Informations Patient
                </h5>
            </div>
            <div class="box-body p-3 fs-13">
                <div class="mb-2">
                    <div class="fw-bold fs-14 text-dark">{{ $patientFullName }}</div>
                    <div class="text-muted fs-12 font-monospace">{{ $patientCode }}</div>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Genre :</span>
                    <strong class="text-dark">{{ ucfirst(optional($patient)->gender ?? 'Non précisé') }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Date de Naissance :</span>
                    <strong class="text-dark">{{ optional($patient)->birth_date ?: 'Non renseigné' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Téléphone :</span>
                    <strong class="text-dark">{{ optional($patient)->telephone ?: (optional($patientUser)->contact ?? 'Non renseigné') }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Résidence :</span>
                    <strong class="text-dark">{{ optional(optional($patient)->residenceActuelle)->name ?? (optional($patient)->adresse ?? 'Abidjan') }}</strong>
                </div>
            </div>
        </div>

        <!-- CARTE PRATICIEN & ETABLISSEMENT -->
        <div class="box shadow-none border">
            <div class="box-header bg-light py-2 px-3">
                <h5 class="box-title fs-14 fw-bold text-dark mb-0">
                    <i class="fa-solid fa-hospital text-primary me-2"></i> Établissement
                </h5>
            </div>
            <div class="box-body p-3 fs-13">
                <div class="fw-bold text-dark mb-1">{{ $hospitalName }}</div>
                <div class="text-muted fs-12 mb-2">{{ optional($hospital)->adresse ?: 'Abidjan' }}</div>
                <hr class="my-2">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Praticien :</span>
                    <strong class="text-dark">{{ $doctorFullName }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Service :</span>
                    <strong class="text-dark">{{ optional(optional(optional($doctor)->serviceHospital)->service)->libelle ?? 'Laboratoire' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Date :</span>
                    <strong class="text-dark">{{ date('d/m/Y H:i') }}</strong>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
