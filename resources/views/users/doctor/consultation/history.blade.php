@extends('layouts.dashboard', ['title' => 'Historique de consultations'])

@section('content')
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
                <table id="example" class="table table-striped table-hover display nowrap margin-top-10 w-p100">
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

                                // Une consultation est véritablement terminée si status == 1 ET qu'au moins une étape post-consultation / issue / ordonnance / hospitalisation est enregistrée
                                $isTerminee = ($item->status == 1) && ($hasJustification || $hasOrdonnance || $hasExamen || $hasArret || $hasDeclaration || $hasHospitalisation);
                            @endphp
                            <tr>
                                <td>
                                    @if ($item->registre)
                                        {{ \Carbon\Carbon::parse($item->registre->updated_at)->format('d/m/Y') }} -
                                        {{ heureFr($item->registre->updated_at) }}
                                    @else
                                        {{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y') }} -
                                        {{ heureFr($item->updated_at) }}
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
                                    @if (!$isTerminee)
                                        <span class="badge badge-warning"><i class="fa-solid fa-clock me-1"></i> Non terminée</span>
                                    @else
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
                                    @endif
                                </td>
                                <td class="text-center">
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
                                            class="btn btn-sm btn-primary me-1" title="Poursuivre la consultation">
                                            <i class="fa-solid fa-play me-1"></i> <span class="">Poursuivre</span>
                                        </a>
                                    @else
                                        <a href="{{ route('doctor.consultation.detail', $item->id) }}" class="btn btn-sm btn-info me-1"
                                            title="Détail de la consultation">
                                            <i class="fa-solid fa-eye me-1"></i> <span class="">Détail</span>
                                        </a>
                                        <a href="{{ route('doctor.consultation.formulaire', $item->id) }}"
                                            class="btn btn-sm btn-warning me-1" title="Modifier la consultation">
                                            <i class="fa-solid fa-pen-to-square me-1"></i> <span class="">Modifier</span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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