@extends('layouts.patient', ['title' => 'Liste de vos consultations'])

@section('content')
    <div class="max-w-5xl min-w-max mx-auto bg-white py-8 px-6 md:px-10 border-t-4 border-orange-400 rounded-lg shadow-sm w-full md:w-10/12">

        <div class="mb-6 flex items-center justify-between border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <span>Vos Consultations & Dossier Médical</span>
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Consultez l'historique complet de vos rendez-vous, constatations, constantes et ordonnances.
                </p>
            </div>
            <span class="bg-blue-50 text-blue-700 font-semibold px-4 py-2 rounded-full text-xs border border-blue-200">
                {{ count($consultations) }} Consultation(s)
            </span>
        </div>

        <div class="space-y-6">
            @forelse ($consultations as $item)
                @php
                    $hospitalLabel = optional($item->hospital)->label 
                        ?: (optional(optional($item->hospital)->user)->name 
                        ?: 'Hôpital Général');

                    $doctorName = trim(optional(optional($item->doctor)->user)->name . ' ' . optional(optional($item->doctor)->user)->prenom)
                        ?: 'Médecin traitant';

                    $serviceName = optional(optional($item->prestationHospital)->prestationService)->libelle 
                        ?? 'Consultation médicale';

                    $reg = $item->registre;
                    $regCur = optional($reg)->registreConsultationCurative;

                    $valTA = $item->tension_arterielle ?: (optional($regCur)->ta ?? 'N/A');
                    $valTemp = $item->temperature ?: (optional($regCur)->temperature ?? 'N/A');
                    $valPoids = $item->poids ?: (optional($regCur)->poids ?? 'N/A');
                    $valTaille = $item->taille ?: (optional($regCur)->taille ?? 'N/A');
                    $valImc = $item->imc ?: (optional($regCur)->imc ?? 'N/A');
                    $valPouls = $item->pouls ?: (optional($regCur)->pouls ?? 'N/A');
                    $valSat = $item->saturation_oxygene ?: (optional($regCur)->saturation_oxygene ?? 'N/A');
                    $valGlyA = $item->gly_a_jeun ?: (optional($regCur)->glycemie_a_jeun ?? 'N/A');

                    $motifStr = $item->motif_consultation ?? optional($item->admission)->motif_consultation ?? (optional($regCur)->motif_consultation ?? 'Non renseigné');
                    $diagStr = optional($regCur)->diagnostic_retenu ?? optional($item->hospitalisation)->diagnostic ?? null;
                    $issueMsg = motSortie(optional($reg)->issue_consultation);
                @endphp

                <div x-data="{ showDetails: false }" class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition duration-200 overflow-hidden">
                    <!-- En-tête de la carte consultation -->
                    <div class="p-5 md:p-6 bg-gray-50 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="bg-blue-600 text-white font-bold text-xs px-3 py-1 rounded-md">
                                    {{ dateCompletFr($item->created_at) }} à {{ heureFr($item->created_at) }}
                                </span>
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-1 rounded-md border border-emerald-200">
                                    {{ $hospitalLabel }}
                                </span>
                                @if(!empty($item->call_channel))
                                    <span class="bg-teal-100 text-teal-800 text-xs font-semibold px-2.5 py-1 rounded-md border border-teal-200">
                                        Téléconsultation
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mt-2">
                                Service : {{ $serviceName }}
                            </h3>
                            <p class="text-sm text-gray-600 flex items-center gap-1">
                                <span class="font-medium text-gray-700">Médecin :</span> Dr. {{ $doctorName }}
                            </p>
                        </div>

                        <!-- Boutons d'Action principales (Détails & Parcours) -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <button @click="showDetails = !showDetails" type="button" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg shadow-sm transition duration-150 gap-1.5 cursor-pointer">
                                <span x-text="showDetails ? 'Masquer détails' : 'Détails'">Détails</span>
                            </button>

                            <a href="{{ route('patient.parcours', $item->id) }}" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition duration-150 gap-1.5">
                                <span>Parcours</span>
                            </a>
                        </div>
                    </div>

                    <!-- Résumé rapide -->
                    <div class="p-5 md:p-6 bg-white space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Motif de consultation :</span>
                                <p class="text-sm text-gray-800 font-medium mt-0.5">{{ $motifStr }}</p>
                            </div>
                            @if($diagStr)
                                <div>
                                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider block">Diagnostic retenu :</span>
                                    <p class="text-sm text-emerald-700 font-bold mt-0.5">{{ $diagStr }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Documents téléchargeables PDF -->
                        <div class="pt-3 border-t border-gray-100">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-2">Documents délivrés :</span>
                            <div class="flex flex-wrap gap-2">
                                @if ($item->ordonnance || count($item->ordonnances ?? []) > 0)
                                    @php $ordObj = $item->ordonnance ?? optional($item->ordonnances)->first(); @endphp
                                    @if($ordObj)
                                        <a target="_blank" href="{{ route('patient.impression', ['ordonnance', $ordObj->id]) }}" 
                                            class="inline-flex items-center border border-red-200 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md text-xs font-semibold text-red-700 transition">
                                            📄 Ordonnance Médicale (PDF)
                                        </a>
                                    @endif
                                @endif
                                @if ($item->examen)
                                    <a target="_blank" href="{{ route('patient.impression', ['examen', $item->examen->id]) }}" 
                                        class="inline-flex items-center border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-md text-xs font-semibold text-emerald-700 transition">
                                        🔬 Bulletin d'examen (PDF)
                                    </a>
                                @endif
                                @if ($item->arret)
                                    <a target="_blank" href="{{ route('patient.impression', ['arret', $item->arret->id]) }}" 
                                        class="inline-flex items-center border border-amber-200 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded-md text-xs font-semibold text-amber-700 transition">
                                        📜 Arrêt de travail (PDF)
                                    </a>
                                @endif
                                @if(!$item->ordonnance && count($item->ordonnances ?? []) == 0 && !$item->examen && !$item->arret)
                                    <span class="text-xs text-gray-400 italic">Aucun document téléchargeable pour cette consultation.</span>
                                @endif
                            </div>
                        </div>

                        <!-- VOLET DÉPLIABLE DÉTAILS COMPLET (Alpine.js) -->
                        <div x-show="showDetails" x-transition.duration.300ms class="mt-4 pt-4 border-t border-gray-200 space-y-4 bg-gray-50 p-4 rounded-xl">
                            
                            <!-- Constantes Physiques -->
                            <div>
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Prise de constantes physiques :</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">Tension</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valTA }} {{ $valTA != 'N/A' ? 'mmHg' : '' }}</span>
                                    </div>
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">Température</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valTemp }} {{ $valTemp != 'N/A' ? '°C' : '' }}</span>
                                    </div>
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">Poids</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valPoids }} {{ $valPoids != 'N/A' ? 'kg' : '' }}</span>
                                    </div>
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">Pouls</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valPouls }} {{ $valPouls != 'N/A' ? 'bpm' : '' }}</span>
                                    </div>
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">Saturation O₂</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valSat }} {{ $valSat != 'N/A' ? '%' : '' }}</span>
                                    </div>
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">Glycémie</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valGlyA }} {{ $valGlyA != 'N/A' ? 'g/l' : '' }}</span>
                                    </div>
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">Taille</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valTaille }} {{ $valTaille != 'N/A' ? 'cm' : '' }}</span>
                                    </div>
                                    <div class="bg-white p-2.5 rounded-lg border border-gray-200 text-center">
                                        <span class="text-xs text-gray-500 block">IMC</span>
                                        <span class="text-sm font-bold text-gray-800">{{ $valImc }} {{ $valImc != 'N/A' ? 'kg/m²' : '' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Médicaments Prescrits -->
                            @if(count($item->ordonnances ?? []) > 0 || $item->ordonnance)
                                <div>
                                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Traitements & Médicaments prescrits :</h4>
                                    <div class="bg-white p-3 rounded-lg border border-gray-200 space-y-2">
                                        @php
                                            $allOrds = count($item->ordonnances ?? []) > 0 ? $item->ordonnances : collect([$item->ordonnance])->filter();
                                        @endphp
                                        @foreach($allOrds as $ord)
                                            @foreach($ord->prescriptions ?? [] as $p)
                                                @php
                                                    $drugName = optional($p->drug)->name ?? optional(optional($p->drugHospital)->drug)->name ?? 'Médicament';
                                                @endphp
                                                <div class="text-xs text-gray-800 flex items-center justify-between border-b pb-1 last:border-0 last:pb-0">
                                                    <span class="font-bold text-blue-900">• {{ $drugName }}</span>
                                                    <span class="text-gray-600">Posologie : {{ $p->dosage ?? 'Selon ordonnance' }} (Qté : {{ $p->quantity }})</span>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($issueMsg)
                                <div>
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block">Remarque du médecin :</span>
                                    <p class="text-xs text-gray-700 bg-white p-2.5 rounded-lg border border-gray-200 mt-1">{{ $issueMsg }}</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-gray-500 text-base font-semibold">Vous n'avez encore aucune consultation enregistrée.</p>
                </div>
            @endforelse
        </div>

    </div>
@endsection
