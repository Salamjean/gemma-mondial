@extends('layouts.dashboard', ['title' => 'Historique de consultations'])

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border border-success-subtle p-3 mb-3 rounded-10 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-3">
                <i class="fa-solid fa-circle-check fs-20 text-success"></i>
                <div class="fs-13 fw-semibold text-dark">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="box">
        <div class="box-header d-flex align-items-center justify-content-between p-20 flex-wrap gap-2">
            <h4 class="box-title mb-0 fw-bold text-dark">
                <i class="fa-solid fa-history me-2 text-primary"></i><b>Historique de toutes les consultations</b>
            </h4>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary rounded-10 fw-bold shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour
            </a>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="consultationHistoryTable" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
                    <thead style="background: #f1f5f9; border-bottom: 2px solid #cbd5e1;">
                        <tr class="text-dark fw-bold fs-13">
                            <th class="py-3 px-3 text-dark fw-bold">Date et heure</th>
                            <th class="py-3 px-3 text-dark fw-bold">Référence</th>
                            <th class="py-3 px-3 text-dark fw-bold">Motif & Mode</th>
                            <th class="py-3 px-3 text-dark fw-bold text-center">Dossier médical</th>
                            <th class="py-3 px-3 text-dark fw-bold">Statut</th>
                            <th class="py-3 px-3 text-dark fw-bold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consultations as $item)
                            @php
                                $hasJustification = !empty(optional($item->registre)->issue_consultation_justification);
                                $hasOrdonnance = ($item->ordonnances_count > 0);
                                $hasExamen = ($item->examen_count > 0);
                                $hasArret = ($item->arret_count > 0);
                                $hasDeclaration = ($item->declaration_count > 0);
                                $hasHospitalisation = \App\Models\Hospitalisation::where('consultation_id', $item->id)->exists();

                                // Détection d'une consultation de laboratoire
                                $serviceLibelle = strtolower(optional(optional(optional($item->prestationHospital)->prestationService)->service)->libelle ?? optional(optional($item->prestationHospital)->prestationService)->libelle ?? $item->motif_consultation ?? '');
                                $isLaboratoire = (optional($item->registre)->type_consultation === 'Laboratoire')
                                    || str_contains($serviceLibelle, 'laboratoire')
                                    || str_contains($serviceLibelle, 'labo')
                                    || str_contains($serviceLibelle, 'analyse')
                                    || (!empty($item->observation_infirmiere) && (str_contains($item->observation_infirmiere, 'date_reception') || str_contains($item->observation_infirmiere, 'echantillons')));

                                // Recherche du fichier de résultat d'analyse
                                $resultatFile = null;
                                if ($item->bulletinExamen && $item->bulletinExamen->examens) {
                                    foreach ($item->bulletinExamen->examens as $ex) {
                                        if (!empty($ex->resultat)) {
                                            $resultatFile = $ex->resultat;
                                            break;
                                        }
                                    }
                                }

                                // Documents médicaux délivrés
                                $hasDocumentsDelivres = $hasOrdonnance || $hasExamen || $hasArret || $hasDeclaration || $hasJustification || $hasHospitalisation;

                                // Statut de fin de consultation
                                if ($isLaboratoire) {
                                    $isTerminee = !empty($resultatFile);
                                } else {
                                    $isTerminee = $hasDocumentsDelivres;
                                }
                            @endphp
                            <tr>
                                @php
                                    $rawDate = $item->created_at ?? ($item->registre ? $item->registre->created_at : null);
                                    $orderVal = $item->created_at ? $item->created_at->format('YmdHis') : ($item->date_consultation ? date('Ymd000000', strtotime($item->date_consultation)) : str_pad($item->id, 14, '0', STR_PAD_LEFT));
                                @endphp
                                <td data-order="{{ $orderVal }}">
                                    @if ($item->created_at)
                                        {{ $item->created_at->format('d/m/Y') }} - {{ $item->created_at->format('H:i') }}
                                    @elseif ($item->registre && $item->registre->updated_at)
                                        {{ \Carbon\Carbon::parse($item->registre->updated_at)->format('d/m/Y') }} -
                                        {{ heureFr($item->registre->updated_at) }}
                                    @elseif ($item->date_consultation)
                                        {{ \Carbon\Carbon::parse($item->date_consultation)->format('d/m/Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($item->updated_at ?? now())->format('d/m/Y') }}
                                    @endif
                                </td>
                                <td><b>{{ optional($item->patient)->code_patient ?? 'N/A' }}</b></td>

                                <td class="" style="width: 220px;">
                                    <span class="fw-bold text-dark fs-13">{{ optional(optional($item->prestationHospital)->prestationService)->libelle ?? $item->motif_consultation ?? 'Consultation' }}</span>
                                    <br>
                                    @if(!empty($item->call_channel))
                                        <span class="badge" style="background-color: #0d9488; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 6px; display: inline-block; margin-top: 4px;"><i class="fa-solid fa-video me-1"></i> Téléconsultation</span>
                                    @else
                                        <span class="badge" style="background-color: #475569; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 6px; display: inline-block; margin-top: 4px;"><i class="fa-solid fa-hospital me-1"></i> Présentiel</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if (optional($item->patient)->id)
                                        <a href="{{ route('doctor.patient.dossier_medical', $item->patient->id) }}"
                                            class="btn btn-sm btn-outline-info rounded-pill fw-bold shadow-xs px-3 py-1.5 text-nowrap d-inline-flex align-items-center gap-1.5" title="Ouvrir le dossier médical complet">
                                            <i class="fa-solid fa-folder-open fs-13"></i>
                                            <span>Dossier médical</span>
                                        </a>
                                    @else
                                        <span class="text-muted fs-11">-</span>
                                    @endif
                                </td>

                                <td class="">
                                    @if ($isLaboratoire)
                                        @if ($resultatFile)
                                            <span class="badge badge-success"><i class="fa-solid fa-check-circle me-1"></i> Terminée</span> <br>
                                            <div style="padding-top: 5px;">
                                                <a target="_blank" href="{{ asset('assets/uploads/analyses/' . $resultatFile) }}"><span
                                                        class="badge badge-success"><i class="fa-solid fa-file-circle-check me-1"></i> Résultat dispo</span></a>
                                            </div>
                                        @else
                                            <span class="badge badge-warning"><i class="fa-solid fa-clock me-1"></i> Résultat en attente</span>
                                        @endif
                                    @else
                                        @if ($isTerminee)
                                            <span class="badge badge-success"><i class="fa-solid fa-check-circle me-1"></i> Terminée</span> <br>
                                            <div style="padding-top: 5px;">
                                                @if ($item->ordonnances_count > 0)
                                                    @foreach ($item->ordonnances as $ordonnan)
                                                        <a target="_blank" href="{{ route('impression', ['ordonnance', $ordonnan->id]) }}"><span
                                                                class="badge badge-warning">Ordonnance
                                                                {{ $ordonnan->type }}</span></a>
                                                    @endforeach
                                                @endif
                                                @if ($item->arret_count > 0)
                                                    <a style="padding-bottom: 5px;" target="_blank"
                                                        href="{{ route('impression', ['arret', $item->arret->id]) }}"><span
                                                             class="badge badge-primary">Arret de travail</span></a>
                                                @endif
                                                @if ($item->examen_count > 0)
                                                    <a target="_blank" href="{{ route('impression', ['examen', $item->examen->id]) }}"><span
                                                            class="badge badge-secondary">Bulletin d'examen</span></a>
                                                @endif

                                                @if ($item->declaration_count > 0)
                                                    <a target="_blank" href="{{ route('impression', $item->declaration->id) }}"><span
                                                            class="badge badge-danger">Deces</span></a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge badge-warning"><i class="fa-solid fa-clock me-1"></i> Non terminée</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($isLaboratoire)
                                        <!-- BOUTONS LABORATOIRE : CONSULTER LE RESULTAT OU UPLOADER -->
                                        @if ($resultatFile)
                                            <a href="{{ asset('assets/uploads/analyses/' . $resultatFile) }}" target="_blank" 
                                                class="btn btn-sm btn-success me-1 shadow-xs" title="Consulter le résultat d'analyse">
                                                <i class="fa-solid fa-file-circle-check me-1"></i> <span class="">Consulter résultat</span>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-sm btn-primary me-1 shadow-xs" 
                                                onclick="openUploadLabModal('{{ $item->id }}', '{{ optional($item->patient)->code_patient ?? ('#' . $item->id) }} - {{ addslashes(trim((optional(optional($item->patient)->user)->name ?? '') . ' ' . (optional(optional($item->patient)->user)->prenom ?? ''))) }}', '{{ route('doctor.consultation.laboratoire.upload_resultat', $item->id) }}')" 
                                                title="Enregistrer le résultat d'analyse">
                                                <i class="fa-solid fa-file-arrow-up me-1"></i> <span class="">Résultat</span>
                                            </button>
                                        @endif

                                        <a href="{{ route('doctor.consultation.laboratoire.recap', $item->id) }}" class="btn btn-sm btn-info me-1 shadow-xs"
                                            title="Voir le récapitulatif du laboratoire">
                                            <i class="fa-solid fa-flask-vial me-1"></i> <span class="">Fiche Labo</span>
                                        </a>
                                        @if (!$resultatFile)
                                            <a href="{{ route('doctor.consultation.formulaire', $item->id) }}"
                                                class="btn btn-sm btn-warning me-1 shadow-xs" title="Modifier la demande d'analyse">
                                                <i class="fa-solid fa-pen-to-square me-1"></i> <span class="">Modifier</span>
                                            </a>
                                        @endif
                                    @else
                                        @if (!$isTerminee)
                                            @php
                                                $poursuivreUrl = route('doctor.consultation.formulaire', $item->id);
                                                if ($item->registre && !empty($item->registre->issue_consultation)) {
                                                    $poursuivreUrl = route('doctor.consultation.formulaire.issue', [
                                                        'title' => 'Formulaire issue',
                                                        'issue' => $item->registre->issue_consultation,
                                                        'id' => $item->id
                                                    ]);
                                                }
                                            @endphp
                                            <a href="{{ $poursuivreUrl }}"
                                                class="btn btn-sm btn-primary me-1 shadow-xs fw-bold" title="Poursuivre la consultation où vous vous êtes arrêté">
                                                <i class="fa-solid fa-play me-1"></i> <span class="">Poursuivre</span>
                                            </a>
                                            <a href="{{ route('doctor.consultation.formulaire', $item->id) }}"
                                                class="btn btn-sm btn-warning me-1 shadow-xs" title="Modifier la consultation">
                                                <i class="fa-solid fa-pen-to-square me-1"></i> <span class="">Modifier</span>
                                            </a>
                                        @else
                                            <a href="{{ route('doctor.consultation.detail', $item->id) }}" class="btn btn-sm btn-info me-1 shadow-xs fw-bold"
                                                title="Voir les détails complets de la consultation">
                                                <i class="fa-solid fa-eye me-1"></i> <span class="">Détail</span>
                                            </a>
                                            @php
                                                $hasDocs = ($item->ordonnances_count > 0) || ($item->examen_count > 0) || ($item->arret_count > 0) || ($item->declaration_count > 0) || (!empty($resultatFile));
                                                if ($hasDocs) {
                                                    $documentsUrl = route('doctor.consultation.documents', $item->id);
                                                    $docTitle = "Télécharger et imprimer les documents médicaux délivrés (ordonnances, examens, arrêts)";
                                                    $btnText = "Documents";
                                                } else {
                                                    $issueType = !empty(optional($item->registre)->issue_consultation) ? $item->registre->issue_consultation : 'sortie';
                                                    $documentsUrl = route('doctor.consultation.formulaire.issue', [
                                                        'title' => 'Formulaire issue',
                                                        'issue' => $issueType,
                                                        'id' => $item->id
                                                    ]);
                                                    $docTitle = "Accéder à la page pour fournir les documents médicaux (ordonnances, examens, arrêts)";
                                                    $btnText = "Fournir documents";
                                                }
                                            @endphp
                                            <a href="{{ $documentsUrl }}" class="btn btn-sm btn-primary me-1 shadow-xs fw-bold"
                                                title="{{ $docTitle }}">
                                                <i class="fa-solid fa-file-pdf me-1"></i> <span class="">{{ $btnText }}</span>
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DE TÉLÉVERSEMENT DU RÉSULTAT D'ANALYSE DE LABORATOIRE -->
    <div class="modal fade" id="uploadLabModal" tabindex="-1" aria-labelledby="uploadLabModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-primary text-white py-3 px-4">
                    <h5 class="modal-title fw-bold fs-15 text-white mb-0" id="uploadLabModalLabel">
                        <i class="fa-solid fa-file-arrow-up me-2"></i> Téléverser le Résultat d'Analyse
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadLabForm" action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="p-3 bg-light rounded border mb-3">
                            <div class="text-muted fs-11 fw-bold text-uppercase mb-1">
                                <i class="fa-solid fa-user text-primary me-1"></i> Patient concerné :
                            </div>
                            <div class="fw-bold fs-13.5 text-dark" id="modalPatientInfo">-</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fs-12.5 fw-bold text-dark mb-1">
                                Fichier du résultat (PDF, JPG, PNG, DOCX) <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="fichier_resultat" id="modalFileInput" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <div class="form-text fs-11 text-muted">Formats acceptés : PDF, JPG, PNG, DOCX (Max 20 Mo)</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fs-12.5 fw-bold text-dark mb-1">
                                Conclusion ou note médicale d'interprétation
                            </label>
                            <textarea name="commentaire_resultat" id="modalCommentInput" rows="3" class="form-control fs-12.5" placeholder="Ex: Norme biologique respectée, présence de germes, recommandations... (optionnel)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2.5 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">
                            <i class="fa-solid fa-times me-1"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" id="btnSubmitLabUpload">
                            <i class="fa-solid fa-floppy-disk me-1.5"></i> Enregistrer le résultat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#consultationHistoryTable')) {
                $('#consultationHistoryTable').DataTable().destroy();
            }
            $('#consultationHistoryTable').DataTable({
                dom: 'Blfrtip',
                buttons: [
                    { extend: 'csv', text: '<i class="fa-solid fa-file-csv me-1"></i> CSV' },
                    { extend: 'excel', text: '<i class="fa-solid fa-file-excel me-1"></i> Excel' },
                    { extend: 'print', text: '<i class="fa-solid fa-print me-1"></i> Imprimer' }
                ],
                order: [[0, 'desc']],
                ordering: true,
                stateSave: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });

            // Gestion de la confirmation avant enregistrement du résultat
            $('#uploadLabForm').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                const fileInput = document.getElementById('modalFileInput');

                if (!fileInput.files || fileInput.files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Fichier requis',
                        text: 'Veuillez sélectionner le fichier du résultat d\'analyse avant de valider.',
                        confirmButtonColor: '#3596f7'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Confirmer l\'enregistrement ?',
                    text: 'Voulez-vous vraiment enregistrer et valider ce résultat d\'analyse pour ce patient ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3596f7',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Oui, enregistrer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Enregistrement en cours...',
                            text: 'Veuillez patienter pendant le téléversement du document.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                });
            });
        });

        function openUploadLabModal(consultationId, patientInfo, uploadUrl) {
            document.getElementById('uploadLabForm').action = uploadUrl;
            document.getElementById('modalPatientInfo').innerText = patientInfo || ('Consultation #' + consultationId);
            document.getElementById('modalFileInput').value = '';
            document.getElementById('modalCommentInput').value = '';

            const modal = new bootstrap.Modal(document.getElementById('uploadLabModal'));
            modal.show();
        }

        function openCardModal(url) {
            Swal.fire({
                html: '<div id="swal-card-container" style="min-height: 600px; overflow: hidden;"></div>',
                width: '1500px',
                maxWidth: '95vw',
                padding: '2em',
                background: 'transparent',
                showConfirmButton: false,
                showCloseButton: true,
                didOpen: () => {
                    Swal.getPopup().style.overflow = 'hidden';
                    Swal.showLoading();
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => {
                            if (!response.ok) throw new Error("Network response was not ok");
                            return response.text();
                        })
                        .then(html => {
                            Swal.hideLoading();
                            const container = document.getElementById('swal-card-container');
                            container.innerHTML = html;

                            const scripts = container.querySelectorAll("script");
                            scripts.forEach(oldScript => {
                                const newScript = document.createElement("script");
                                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                oldScript.parentNode.replaceChild(newScript, oldScript);
                            });
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Impossible de charger la carte.',
                                confirmButtonColor: '#3596f7'
                            });
                            console.error(err);
                        });
                }
            });
        }
    </script>
@endpush