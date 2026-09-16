<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\ConsultationPostNataleRequest;
use App\Http\Requests\Doctor\ConsultationPreNataleRequest;
use App\Http\Requests\Doctor\ConsultationAccouchementRequest;
use App\Models\Consultation;
use App\Models\Drug;
use App\Models\DrugHospital;
use App\Models\Ordonnance;
use App\Models\Registre;
use App\Models\RegistreConsultationCurative;
use App\Repositories\Doctor\ConsultationRepository;
use App\Repositories\Doctor\IssueRepository;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{

    public function instance()
    {
        return new ConsultationRepository();
    }

    public function instanceIssue()
    {
        return new IssueRepository();
    }

    public function today()
    {
        return view('users.doctor.consultation.today', ['consultations' => $this->instance()->today()]);
    }

    public function allPatients()
    {
        $doctorId = auth()->user()->doctor->id;
        $consultations = Consultation::orderByDESC('created_at')
            ->where('doctor_id', $doctorId)
            ->where('status', 0)
            ->whereNull('call_channel')
            ->get();

        return view('users.doctor.consultation.all', compact('consultations'));
    }

    public function history()
    {
        return view('users.doctor.consultation.history', ['consultations' => $this->instance()->history()]);
    }

    //form generate
    public function formulaire($id)
    {
        $consultation = Consultation::with(['patient.user', 'patient.residenceActuelle', 'prestationHospital.prestationService', 'admission.patient.user'])->findOrFail($id);

        $prestationServiceId = optional($consultation->prestationHospital)->prestation_service_id ?? 1;
        try {
            $data = $this->instance()->formulaireMotif($prestationServiceId);
        } catch (\Throwable $e) {
            $data = ['consultation', 'Consultation Curative'];
        }

        return view('users.doctor.consultation.formulaire', [
            'title' => $data[1] ?? 'Consultation Curative',
            'consultation' => $consultation,
            'type' => $data[0] ?? 'consultation'
        ]);
    }

    /**
     * API JSON – données patient pour le formulaire en ligne intégré dans le modal.
     */
    public function onlinePatientInfo($id)
    {
        $consultation = Consultation::with([
            'patient.user', 'patient.residenceActuelle',
            'admission.patient.user', 'admission.infirmier.user',
            'infirmier.user', 'registre.registreConsultationCurative'
        ])->findOrFail($id);

        $patient = $consultation->patient ?? optional($consultation->admission)->patient;
        $patientUser = optional($patient)->user;

        $birthDate = optional($patient)->birth_date ?? '';
        $age = 'N/A';
        if ($birthDate) {
            try {
                $age = \Carbon\Carbon::createFromFormat('d/m/Y', $birthDate)->diffInYears(\Carbon\Carbon::now()) . ' ans';
            } catch (\Throwable $e) {
                $age = 'N/A';
            }
        }

        $name = trim(
            optional($patientUser)->name . ' ' .
            (optional($patientUser)->prenom ?? '')
        ) ?: 'Patient';

        $reg = optional($consultation->registre)->registreConsultationCurative;
        $gender = optional($patient)->gender ?? 'masculin';
        $avatar = optional($patient)->img_url 
            ? asset('assets/uploads/patient/' . $patient->img_url) 
            : ($gender == 'masculin' ? asset('assets/images/avatar/6.png') : asset('assets/images/avatar/2.png'));

        $infirmierName = optional(optional($consultation->infirmier)->user)->name 
            ?? optional(optional(optional($consultation->admission)->infirmier)->user)->name 
            ?? 'Non renseigné';

        return response()->json([
            'patient_id' => optional($patient)->id,
            'name'       => $name,
            'code'       => optional($patient)->code_patient ?? 'N/A',
            'avatar'     => $avatar,
            'birth_date' => $birthDate,
            'age'        => $age,
            'gender'     => $gender,
            'residence'  => optional(optional($patient)->residenceActuelle)->name ?? 'Non renseigné',
            'profession' => optional($patient)->profession ?? 'Non renseignée',
            'phone'      => optional($patient)->telephone ?? 'Non renseigné',
            'assurance'  => optional($patient)->no_assurance ?? 'Non renseigné',
            'infirmier'  => $infirmierName,
            'patient_detail_url' => optional($patient)->id ? route('doctor.patient.detail', $patient->id) : '',
            'motif'      => $consultation->motif_consultation
                            ?? optional($consultation->admission)->motif_consultation
                            ?? optional($reg)->motif_consultation ?? '',
            'poids'      => $consultation->poids ?? optional($reg)->poids ?? '',
            'taille'     => $consultation->taille ?? optional($reg)->taille ?? '',
            'imc'        => $consultation->imc ?? optional($reg)->imc ?? '',
            'ta'         => $consultation->tension_arterielle ?? optional($reg)->ta ?? '',
            'pouls'      => $consultation->pouls ?? optional($reg)->pouls ?? '',
            'temperature'=> $consultation->temperature ?? optional($reg)->temperature ?? '',
            'saturation_oxygene' => $consultation->saturation_oxygene ?? optional($reg)->saturation_oxygene ?? '',
            'frequence_respiratoire' => $consultation->frequence_respiratoire ?? optional($reg)->frequence_respiratoire ?? '',
            'perimetre_brachial' => $consultation->perimetre_brachial ?? optional($reg)->perimetre_brachial ?? '',
            'perimetre_cranien' => $consultation->perimetre_cranien ?? optional($reg)->perimetre_cranien ?? '',
            'zscore'     => $consultation->zscore ?? optional($reg)->zcore ?? '',
            'ordre_no'   => '0' . (function_exists('noOrdreConsultation') ? noOrdreConsultation() : '1'),
            'date_du_jour' => \Carbon\Carbon::now()->format('d/m/Y'),
            // Antécédents existants
            'hta'        => optional($reg)->hta ?? '',
            'diabete'    => optional($reg)->diabete ?? '',
            'tabac'      => optional($reg)->tabac ?? '',
            'alcool'     => optional($reg)->alcool ?? '',
            'ugd'        => optional($reg)->UGD ?? '',
            'drepanocytaire' => optional($reg)->drepanocytaire ?? '',
            'traitement_medicamenteux' => optional($reg)->traitement_medicamenteux ?? '',
            'traitement_medicamenteux_anterieur' => optional($reg)->traitement_medicamenteux_anterieur ?? '',
            'antecedent_medical' => optional($reg)->antecedent_medical ?? '',
            'autre_antecedent_medical' => optional($reg)->autre_antecedent_medical ?? '',
            'antecedent_chirurgical' => optional($reg)->antecedent_chirurgical ?? '',
            'nom_operation' => optional($reg)->nom_operation ?? '',
            'autre_antecedent_chirurgical' => optional($reg)->autre_antecedent_chirurgical ?? '',
            'en_cours_de_grossesse' => optional($reg)->en_cours_de_grossesse ?? '',
            'ddr'        => optional($reg)->ddr ?? '',
            'description_grossesse' => optional($reg)->description_grossesse ?? '',
            'tuberculose'=> optional($reg)->tuberculose ?? '',
            'autre_examen_clinique' => optional($reg)->autre_examen_clinique ?? '',
            'examen_physique' => optional($reg)->examen_physique ?? '',
            'diagnostic_retenu' => optional($reg)->diagnostic_retenu ?? '',
            'autre_pathologie_associee' => optional($reg)->autre_pathologie_associee ?? '',
            'tdr_paludisme' => optional($reg)->tdr_paludisme ?? '',
            'goutte_epaise' => optional($reg)->goutte_epaise ?? '',
            'milda_enfant_eligible' => optional($reg)->milda_enfant_eligible ?? '',
            'remise_milda_enfant' => optional($reg)->remise_milda_enfant ?? '',
            'cdip_propose' => optional($reg)->cdip_propose ?? '',
            'cdip_realise' => optional($reg)->cdip_realise ?? '',
            'code_depistage_client' => optional($reg)->code_depistage_client ?? '',
            'glycemie_a_jeun' => optional($reg)->glycemie_a_jeun ?? '',
            'glycemie_non_a_jeun' => optional($reg)->glycemie_non_a_jeun ?? '',
            'mode_sortie' => optional(optional($consultation->registre))->issue_consultation ?? '',
        ]);
    }


    public function formulaireIssue($title, $mode_sortie, $consultation)
    {
        $title = $this->instanceIssue()->formulaireIssue($mode_sortie);
        $drugsHospital = DrugHospital::where('hospital_id', auth()->user()->doctor->hospital_id)->get();
        $drugs = Drug::all();
        return view('users.doctor.consultation.formulaire.formulaire_issue', ['type' => $mode_sortie, 'title' => $title, 'drugsHospital' => $drugsHospital, 'drugs' => $drugs, 'consultation' => $this->instance()->show($consultation)]);
    }

    public function formulairePostConsultation($title, $mode_sortie, $consultation)
    {
        $title = $this->instanceIssue()->formulaireIssue($mode_sortie);
        return view('users.doctor.consultation.formulaire.formulaire_post_consultation', ['type' => $mode_sortie, 'title' => $title, 'consultation' => $this->instance()->show($consultation)]);
    }

    //store form

    public function storePostNatale(ConsultationPostNataleRequest $request)
    {
        // dd($request->mode_sortie);
        $request->validated();

        $res = $this->instance()->storePostNatales($request);

        if ($res['status'] == 'error')
            return redirect()->back()->withErrors($res['message']);

        if ($request->mode_sortie) {
            $title = $this->instanceIssue()->formulaireIssue($request->mode_sortie);
            $redirectParams = [$title, $request->mode_sortie, Consultation::find($request->consultation_id)];
            if ($request->has('embed')) {
                $redirectParams['embed'] = 1;
            }
            return redirect()->route('doctor.consultation.formulaire.issue', $redirectParams)->with('success', $res['message']);
        }

        if ($request->has('embed')) {
            return redirect()->route('doctor.consultation.formulaire', [$request->consultation_id, 'embed' => 1])->with('success', $res['message']);
        }

        return redirect()->route('doctor.consultation.today')->with('success', $res['message']);
    }

    public function storePreNatale(ConsultationPreNataleRequest $request)
    {

        $request->validated();

        $res = $this->instance()->storePreNatales($request);

        if ($res['status'] == 'error')
            return redirect()->back()->withErrors($res['message']);

        if ($request->mode_sortie) {
            $title = $this->instanceIssue()->formulaireIssue($request->mode_sortie);
            $redirectParams = [$title, $request->mode_sortie, $request->consultation_id];
            if ($request->has('embed')) {
                $redirectParams['embed'] = 1;
            }
            return redirect()->route('doctor.consultation.formulaire.issue', $redirectParams)->with('success', $res['message']);
        }

        if ($request->has('embed')) {
            return redirect()->route('doctor.consultation.formulaire', [$request->consultation_id, 'embed' => 1])->with('success', $res['message']);
        }

        return redirect()->route('doctor.consultation.today')->with('success', $res['message']);
    }

    public function storeAccouchement(ConsultationAccouchementRequest $request)
    {

        $request->validated();

        //dd($request);

        $res = $this->instance()->storeAccouchements($request);

        if ($res['status'] == 'error')
            return redirect()->back()->withErrors($res['message']);

        if ($request->mode_sortie) {
            $title = $this->instanceIssue()->formulaireIssue($request->mode_sortie);
            $redirectParams = [$title, $request->mode_sortie, $request->consultation_id];
            if ($request->has('embed')) {
                $redirectParams['embed'] = 1;
            }
            return redirect()->route('doctor.consultation.formulaire.issue', $redirectParams)->with('success', $res['message']);
        }

        if ($request->has('embed')) {
            return redirect()->route('doctor.consultation.formulaire', [$request->consultation_id, 'embed' => 1])->with('success', $res['message']);
        }

        return redirect()->route('doctor.consultation.today')->with('success', $res['message']);
    }

    public function storeConsultationCurative(Request $request)
    {
        if (!$request->has('mode_sortie') || empty($request->mode_sortie)) {
            $request->merge(['mode_sortie' => 'sortie']);
        }
        if (!$request->has('motif_consultation') || empty($request->motif_consultation)) {
            $request->merge(['motif_consultation' => 'Consultation curative']);
        }

        $request->validate([
            'mode_sortie' => 'required',
            'motif_consultation' => 'required',
        ]);


        $getStr = function ($key) use ($request) {
            $val = $request->input($key);
            if (is_array($val)) {
                return count($val) > 0 ? (string) reset($val) : null;
            }
            return $val;
        };

        $modeSortie = $getStr('mode_sortie');

        $consultationObj = Consultation::find($request->consultation_id);
        if ($consultationObj) {
            if ($request->filled('poids') && empty($consultationObj->poids)) {
                $consultationObj->poids = $getStr('poids');
            }
            if ($request->filled('taille') && empty($consultationObj->taille)) {
                $consultationObj->taille = $getStr('taille');
            }
            if ($request->filled('imc') && empty($consultationObj->imc)) {
                $consultationObj->imc = $getStr('imc');
            }
            if ($request->filled('temperature') && empty($consultationObj->temperature)) {
                $consultationObj->temperature = $getStr('temperature');
            }
            if ($request->filled('ta') && empty($consultationObj->tension_arterielle)) {
                $consultationObj->tension_arterielle = $getStr('ta');
            }
            if ($request->filled('pouls') && empty($consultationObj->pouls)) {
                $consultationObj->pouls = $getStr('pouls');
            }
            $consultationObj->save();
        }

        $consultation = Consultation::find($request->consultation_id);
        if ($consultation) {
            $consultation->status = 1;
            $consultation->save();
        }

        $curativeData = [
            "mode_entree" => $getStr("mode_entree"),
            "motif_consultation" => $getStr("motif_consultation"),
            "en_cours_de_scolarisation" => $getStr("en_cours_de_scolarisation"),
            "tdr_paludisme" => $getStr("tdr_paludisme"),
            "goutte_epaise" => $getStr("goutte_epaise"),
            "milda_enfant_eligible" => $getStr("milda_enfant_eligible"),
            "remise_milda_enfant" => $getStr("remise_milda_enfant"),
            "cdip_propose" => $getStr("cdip_propose"),
            "cdip_realise" => $getStr("cdip_realise"),
            "code_depistage_client" => $getStr("code_depistage_client"),
            "glycemie_a_jeun" => $getStr("glycemie_a_jeun"),
            "glycemie_non_a_jeun" => $getStr("glycemie_non_a_jeun"),
            "zcore" => $getStr("zscore"),
            "temperature" => $getStr("temperature"),
            "frequence_respiratoire" => $getStr("frequence_respiratoire"),
            "ta" => $getStr("ta"),
            "hta" => $getStr("hta"),
            "pouls" => $getStr("pouls"),
            "perimetre_brachial" => $getStr("perimetre_brachial"),
            "perimetre_cranien" => $getStr("perimetre_cranien"),
            "tuberculose" => $getStr("tuberculose"),
            "traitement_medicamenteux" => $getStr("traitement_medicamenteux"),
            "antecedent_medical" => $getStr("antecedent_medical"),
            "antecedent_chirurgical" => $getStr("antecedent_chirurgical"),
            "gyneco_obstetrico" => $getStr("gyneco_obstetrico"),
            "ddr" => $getStr("ddr"),
            "en_cours_de_grossesse" => $getStr("en_cours_de_grossesse"),
            "description_grossesse" => $getStr("description_grossesse"),
            "mode_de_vie" => $getStr("mode_de_vie"),
            "tabac" => $getStr("tabac"),
            "alcool" => $getStr("alcool"),
            "taille" => $getStr("taille"),
            "poids" => $getStr("poids"),
            "imc" => $getStr("imc"),
            "drepanocytaire" => $getStr("drepanocytaire"),
            "saturation_oxygene" => $getStr("saturation_oxygene"),
            "type_visite" => $getStr("type_visite"),
            "examen_physique" => $getStr("examen_physique"),
            "diagnostic_retenu" => $getStr("diagnostic_retenu"),
            "autre_pathologie_associee" => $getStr("autre_pathologie_associee"),
            "autre_antecedent_medical" => $getStr("autre_antecedent_medical"),
            "autre_antecedent_chirurgical" => $getStr("autre_antecedent_chirurgical"),
            "nom_operation" => $getStr("nom_operation"),
        ];

        foreach ($curativeData as $k => $v) {
            if (is_array($v)) {
                $curativeData[$k] = count($v) > 0 ? (string) reset($v) : null;
            }
        }

        if (Registre::where('consultation_id', $request->consultation_id)->exists()) {
            $registre = Registre::where('consultation_id', $request->consultation_id)->first();

            $registre->issue_consultation = $modeSortie;
            $registre->save();

            RegistreConsultationCurative::updateOrCreate(
                ['registre_id' => $registre->id],
                $curativeData
            );
        } else {
            //consultation
            $consultation = Consultation::findOrFail($request->consultation_id);

            $registre = Registre::create([
                "code" => codeRegistre($consultation->patient->code_patient, $consultation->patient->id),
                "type_consultation" => "consultation curative",
                "consultation_id" => $request->consultation_id,
                "issue_consultation" => $modeSortie,
            ]);

            $consultationcurative = RegistreConsultationCurative::create(array_merge(
                ["registre_id" => $registre->id],
                $curativeData
            ));
        }


        if ($request->mode_sortie) {
            $title = $this->instanceIssue()->formulaireIssue($request->mode_sortie);
            $redirectParams = [$title, $request->mode_sortie, Consultation::find($request->consultation_id)];
            if ($request->has('embed')) {
                $redirectParams['embed'] = 1;
            }
            return redirect()->route('doctor.consultation.formulaire.issue', $redirectParams)->with('success', 'Consultation effectuée avec succès !');
        }

        if ($request->has('embed')) {
            return redirect()->route('doctor.consultation.formulaire', [$request->consultation_id, 'embed' => 1])->with('success', 'Consultation effectuée avec succès !');
        }

        return redirect()->route('doctor.consultation.today')->with('success', 'Consultation effectuée avec succès !');
    }



    public function detailconsulation($id)
    {
        $consultation = Consultation::findOrFail($id);
        $ordonnance = Ordonnance::where('consultation_id', $consultation->id)->first();
        return view('users.doctor.consultation.detail', compact('consultation', 'ordonnance'));
    }

    public function detail($id)
    {
        $consultation = Consultation::findOrFail($id);
        $ordonnance = Ordonnance::where('consultation_id', $consultation->id)->first();
        return view('users.doctor.consultation.detail', compact('consultation', 'ordonnance'));
    }
    public function infoPatient($id)
    {
        $consultation = Consultation::findOrFail($id);
        return view('users.doctor.consultation.info', compact('consultation'));
    }

    public function patientCard(Request $request, $id)
    {
        $patient = \App\Models\Patient::with(['user', 'hospital'])->findOrFail($id);
        if ($request->ajax()) {
            return view('users.secretariat.patient.card_inner', compact('patient'));
        }
        return view('users.secretariat.patient.card', compact('patient'));
    }

    public function startCall(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        $channel = 'consultation_room_' . $consultation->id;
        $consultation->update([
            'is_call_active' => true,
            'call_status' => 'calling',
            'call_channel' => $channel,
            'call_started_at' => now(),
        ]);

        $doctorUser = auth()->user();
        $doctorName = $doctorUser ? trim($doctorUser->name . ' ' . ($doctorUser->prenom ?? '')) : 'Médecin';
        $doctorId = $doctorUser ? $doctorUser->id : rand(100, 999);

        $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
        $token = \App\Services\LiveKitTokenService::generateToken($channel, 'doctor_' . $doctorId, 'Dr. ' . $doctorName);

        return response()->json([
            'status' => 'success',
            'channel' => $channel,
            'livekit_url' => $livekitUrl,
            'token' => $token,
            'consultation_id' => $consultation->id,
            'doctor_name' => $doctorName,
            'patient_name' => optional(optional($consultation->patient)->user)->name . ' ' . optional(optional($consultation->patient)->user)->prenom,
        ]);
    }

    public function endCall(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        $duration = (int) $request->input('duration', 0);
        $updateData = [
            'is_call_active' => false,
            'call_status' => 'ended',
            'call_ended_at' => now(),
        ];
        if ($duration > 0) {
            $updateData['call_duration'] = $duration;
        }
        $consultation->update($updateData);

        return response()->json([
            'status' => 'success',
            'message' => 'Appel terminé',
        ]);
    }

    public function callStatus(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);
        return response()->json([
            'is_call_active' => (bool)$consultation->is_call_active,
            'call_status' => $consultation->call_status ?? ($consultation->is_call_active ? 'calling' : 'ended'),
            'channel' => $consultation->call_channel,
            'duration' => (int)($consultation->call_duration ?? 0),
        ]);
    }

    public function callHistory()
    {
        $doctor = auth()->user()->doctor;
        $consultations = Consultation::where('doctor_id', $doctor->id)
            ->where(function ($q) {
                $q->whereNotNull('call_started_at')
                  ->orWhereNotNull('call_ended_at')
                  ->orWhereIn('call_status', ['ended', 'rejected', 'patient_left', 'accepted', 'calling']);
            })
            ->with(['patient.user', 'prestationHospital.prestationService'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('users.doctor.call_history', compact('consultations'));
    }

    private function isDoctorInSchedule($user)
    {
        if (!$user) return false;

        // Si le médecin est actuellement connecté et actif sur la plateforme, il est considéré comme disponible
        if (auth()->check() && auth()->id() == $user->id) {
            return true;
        }

        $availability = $user->availability;
        if (!$availability) {
            // S'il n'y a pas de créneaux explicitement configurés, le médecin est considéré disponible par défaut
            return true;
        }

        $days = json_decode($availability->days, true);
        if ($days === null && !empty($availability->days)) {
            $days = explode(',', $availability->days);
        }
        $days = array_map('intval', (array)($days ?? []));

        if (empty($days)) {
            return true;
        }

        $now = \Carbon\Carbon::now();
        $currentDayOfWeek = $now->dayOfWeek; // 0 (Sun) à 6 (Sat)
        $currentDayOfWeekIso = $now->dayOfWeekIso; // 1 (Mon) à 7 (Sun)
        $currentDayZeroMon = ($currentDayOfWeekIso - 1); // 0 (Mon) à 6 (Sun)

        $isTodayInDays = in_array($currentDayOfWeek, $days, true) 
            || in_array($currentDayOfWeekIso, $days, true) 
            || in_array($currentDayZeroMon, $days, true);

        if (!$isTodayInDays) {
            return false;
        }

        $startTimes = json_decode($availability->hour_start, true);
        if (!is_array($startTimes)) {
            $startTimes = [$availability->hour_start ?? '00:00'];
        }

        $endTimes = json_decode($availability->hour_end, true);
        if (!is_array($endTimes)) {
            $endTimes = [$availability->hour_end ?? '23:59'];
        }

        $idx = array_search($currentDayZeroMon, $days);
        if ($idx === false) $idx = array_search($currentDayOfWeek, $days);
        if ($idx === false) $idx = array_search($currentDayOfWeekIso, $days);
        if ($idx === false) $idx = 0;

        $start = $startTimes[$idx] ?? $startTimes[0] ?? '00:00';
        $end = $endTimes[$idx] ?? $endTimes[0] ?? '23:59';

        if (empty($start) || $start === '00:00') return true;
        if (empty($end) || $end === '00:00') $end = '23:59';

        $currentTime = $now->format('H:i');

        // Permettre une marge de 30 minutes avant le début de créneau si le médecin est connecté
        $startCarbon = \Carbon\Carbon::createFromFormat('H:i', strlen($start) == 5 ? $start : '00:00')->subMinutes(30)->format('H:i');

        return ($currentTime >= $startCarbon && $currentTime <= $end);
    }

    private function isDoctorMatchingConsultation($doctor, $consultation)
    {
        if (!$doctor || !$consultation) return false;

        // Filtrer strictement par hôpital : s'assurer que la demande correspond à l'hôpital du médecin
        if ($consultation->hospital_id && $doctor->hospital_id && (int)$consultation->hospital_id !== (int)$doctor->hospital_id) {
            return false;
        }

        $typeName = strtolower(trim($doctor->type_name ?? ''));
        $docServiceName = strtolower(trim(optional(optional($doctor->serviceHospital)->service)->libelle ?? ''));

        // 1. Définition des Généralistes (type_name generaliste OU service Consultation Générale) -> Traitent TOUT !
        $isGeneralist = (
            str_contains($typeName, 'general') ||
            str_contains($typeName, 'général') ||
            str_contains($docServiceName, 'général') ||
            str_contains($docServiceName, 'general')
        );

        if ($isGeneralist) {
            return true;
        }

        // 2. Médecins Spécialistes : vérifier si leur spécialité / service correspond à la demande
        $ph = $consultation->prestationHospital;
        if (!$ph && $consultation->prestation_hospital_id) {
            $ph = \App\Models\PrestationHospital::with(['prestationService.service', 'serviceHospital.service'])->find($consultation->prestation_hospital_id);
        }

        // A. Correspondance par prestation_doctors
        $docPhIds = $doctor->prestationDoctors ? $doctor->prestationDoctors->pluck('prestation_hospital_id')->toArray() : [];
        if ($consultation->prestation_hospital_id && in_array($consultation->prestation_hospital_id, $docPhIds)) {
            return true;
        }

        // B. Correspondance par service_hospital_id
        if ($ph && $doctor->service_hospital_id && $ph->service_hospital_id == $doctor->service_hospital_id) {
            return true;
        }

        // C. Correspondance par mots-clés (service / motif / spécialité)
        $cMotif = strtolower(trim($consultation->motif_consultation ?? ''));
        $cPrestationName = strtolower(trim(optional(optional($ph)->prestationService)->libelle ?? ''));
        $cServiceName1 = strtolower(trim(optional(optional(optional($ph)->prestationService)->service)->libelle ?? ''));
        $cServiceName2 = strtolower(trim(optional(optional(optional($ph)->serviceHospital)->service)->libelle ?? ''));

        $consultationKeywords = array_filter([$cMotif, $cPrestationName, $cServiceName1, $cServiceName2]);
        $docSpecialtyName = strtolower(trim(optional($doctor->typeDoctor)->name ?? optional($doctor->typeDoctor)->libelle ?? ''));
        $doctorKeywords = array_filter([$docServiceName, $docSpecialtyName, $typeName]);

        foreach ($consultationKeywords as $ck) {
            if (str_contains($ck, 'général') || str_contains($ck, 'general')) {
                return true;
            }
            foreach ($doctorKeywords as $dk) {
                if (empty($dk) || $dk === 'specialiste' || $dk === 'spécialiste') continue;
                if (str_contains($ck, $dk) || str_contains($dk, $ck)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getIncomingCallRequests()
    {
        $user = auth()->user();
        if (!$user || !$user->doctor) {
            return response()->json(['has_incoming' => false]);
        }

        $activeCalls = Consultation::whereNull('doctor_id')
            ->where('is_call_active', 1)
            ->whereIn('call_status', ['calling', 'payment_pending', 'pending'])
            ->with(['patient.user', 'prestationHospital.prestationService.service', 'prestationHospital.serviceHospital.service'])
            ->latest('updated_at')
            ->get();

        $incoming = null;
        foreach ($activeCalls as $call) {
            if ($this->isDoctorMatchingConsultation($user->doctor, $call)) {
                $ignoredDoctors = \Illuminate\Support\Facades\Cache::get("consultation_{$call->id}_ignored_doctors", []);
                if (!is_array($ignoredDoctors) || !in_array($user->doctor->id, $ignoredDoctors)) {
                    $incoming = $call;
                    break;
                }
            }
        }

        if (!$incoming) {
            return response()->json(['has_incoming' => false]);
        }

        $patientUser = optional(optional($incoming->patient)->user);
        $patientName = trim(($patientUser->name ?? '') . ' ' . ($patientUser->prenom ?? ''));

        return response()->json([
            'has_incoming' => true,
            'consultation_id' => $incoming->id,
            'patient_name' => $patientName ?: 'Patient',
            'channel' => $incoming->call_channel,
            'created_at' => $incoming->created_at ? $incoming->created_at->format('H:i:s') : now()->format('H:i:s'),
        ]);
    }

    public function acceptPatientOnlineCall(Request $request, $id)
    {
        $doctorUser = auth()->user();
        if (!$doctorUser || !$doctorUser->doctor) {
            return response()->json(['status' => 'error', 'message' => 'Non autorisé'], 403);
        }

        $doctor = $doctorUser->doctor;

        $consultation = Consultation::where('id', $id)->first();
        if ($consultation && !$this->isDoctorMatchingConsultation($doctor, $consultation)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cette consultation ne correspond pas à votre spécialité.',
            ], 403);
        }

        // Transaction/Atomic update : premier médecin disponible à cliquer pour prendre en charge la consultation payée
        $affected = Consultation::where('id', $id)
            ->where(function ($q) use ($doctor) {
                $q->whereNull('doctor_id')->orWhere('doctor_id', $doctor->id);
            })
            ->where('status', 0)
            ->update([
                'doctor_id' => $doctor->id,
                'is_call_active' => true,
                'call_status' => 'accepted',
                'call_started_at' => now(),
            ]);

        if ($affected === 0) {
            return response()->json([
                'status' => 'taken',
                'message' => 'Un autre médecin a déjà décroché cette consultation.',
            ]);
        }

        $consultation = Consultation::findOrFail($id);
        $channel = $consultation->call_channel;
        $doctorName = trim($doctorUser->name . ' ' . ($doctorUser->prenom ?? ''));
        $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
        $token = \App\Services\LiveKitTokenService::generateToken($channel, 'doctor_' . $doctorUser->id, 'Dr. ' . $doctorName);

        $patientUser = optional(optional($consultation->patient)->user);
        $patientName = trim(($patientUser->name ?? '') . ' ' . ($patientUser->prenom ?? ''));

        return response()->json([
            'status' => 'success',
            'channel' => $channel,
            'livekit_url' => $livekitUrl,
            'token' => $token,
            'consultation_id' => $consultation->id,
            'patient_name' => $patientName ?: 'Patient',
        ]);
    }

    public function getPendingOnlineRequests()
    {
        $user = auth()->user();
        if (!$user || !$user->doctor) {
            return response()->json(['status' => 'success', 'requests' => []]);
        }

        if (!$this->isDoctorInSchedule($user)) {
            return response()->json(['status' => 'success', 'requests' => []]);
        }

        $doctor = $user->doctor;

        $pending = Consultation::whereNotNull('call_channel')
            ->where(function ($q) use ($doctor) {
                $q->whereNull('doctor_id')->orWhere('doctor_id', $doctor->id);
            })
            ->where('status', 0)
            ->where('montant', '>=', 100)
            ->whereNotIn('call_status', ['completed'])
            ->with(['patient.user', 'prestationHospital.prestationService.service', 'prestationHospital.serviceHospital.service'])
            ->latest('updated_at')
            ->get()
            ->filter(function ($c) use ($doctor) {
                return $this->isDoctorMatchingConsultation($doctor, $c);
            })
            ->values()
            ->map(function ($c) use ($doctor) {
                $pUser = optional(optional($c->patient)->user);
                $name = trim(($pUser->name ?? '') . ' ' . ($pUser->prenom ?? '')) ?: 'Patient';
                $nameParts = array_values(array_filter(explode(' ', $name)));
                $initials = '';
                if (count($nameParts) >= 2) {
                    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                } else {
                    $initials = strtoupper(substr($name, 0, 2));
                }

                $isTakenByMe = ($c->doctor_id == $doctor->id);
                $patientMissedCount = 0;
                if ($c->patient_id) {
                    $isCurrentMissed = ($c->call_status === 'missed' || !$c->is_call_active || in_array($c->call_status, ['cancelled', 'rejected', 'patient_left']));

                    $lastAnswered = Consultation::where('patient_id', $c->patient_id)
                        ->where('id', '!=', $c->id)
                        ->where(function ($q) {
                            $q->whereNotNull('doctor_id')
                              ->orWhereIn('call_status', ['accepted', 'ended', 'completed'])
                              ->orWhere('status', 1);
                        })
                        ->latest('created_at')
                        ->first();

                    $pastMissedQuery = Consultation::whereNotNull('call_channel')
                        ->where('patient_id', $c->patient_id)
                        ->where('id', '!=', $c->id)
                        ->whereNull('doctor_id')
                        ->where(function ($q) {
                            $q->where('is_call_active', 0)
                              ->orWhereIn('call_status', ['cancelled', 'rejected', 'patient_left', 'missed']);
                        });

                    if ($lastAnswered && $lastAnswered->created_at) {
                        $pastMissedQuery->where('created_at', '>', $lastAnswered->created_at);
                    }

                    $pastMissed = $pastMissedQuery->count();

                    $currentIgnored = \Illuminate\Support\Facades\Cache::get("consultation_{$c->id}_ignored_doctors", []);
                    $currentIgnoredCount = is_array($currentIgnored) ? count($currentIgnored) : 0;

                    $currentMissedCount = max((int)($c->missed_count ?? 0), $isCurrentMissed ? 1 : 0);
                    $patientMissedCount = $currentMissedCount + $pastMissed + $currentIgnoredCount;
                }

                $hObj = \App\Models\Hospital::find($c->hospital_id);
                $hName = $hObj ? ($hObj->label ?: ($hObj->nom_direction_generale ?: $hObj->reference)) : 'Hôpital';

                return [
                    'id' => $c->id,
                    'patient_id' => $c->patient_id,
                    'patient_name' => $name,
                    'patient_initials' => $initials ?: 'PT',
                    'patient_code' => optional($c->patient)->code_patient ?? '',
                    'patient_photo' => ($pUser && $pUser->photo) ? asset('storage/'.$pUser->photo) : null,
                    'motif' => optional(optional($c->prestationHospital)->prestationService)->libelle ?? $c->motif_consultation ?? 'Téléconsultation en ligne',
                    'hospital_name' => $hName,
                    'desired_date' => $c->desired_date ? date('d/m/Y', strtotime($c->desired_date)) : ($c->date_consultation ? date('d/m/Y', strtotime($c->date_consultation)) : 'Aujourd\'hui'),
                    'desired_time' => $c->desired_time ?: ($c->created_at ? $c->created_at->format('H:i') : ''),
                    'created_at' => $c->created_at ? $c->created_at->format('d/m/Y H:i') : '',
                    'time_formatted' => $c->created_at ? $c->created_at->format('H:i') : '',
                    'is_call_active' => (bool)$c->is_call_active,
                    'call_status' => $c->call_status,
                    'is_taken_by_me' => $isTakenByMe,
                    'patient_missed_count' => $patientMissedCount,
                ];
            });

        $missedCount = Consultation::whereNotNull('call_channel')
            ->whereNull('doctor_id')
            ->where(function ($q) {
                $q->where('is_call_active', 0)
                  ->orWhereIn('call_status', ['cancelled', 'rejected', 'patient_left', 'missed']);
            })
            ->whereDate('created_at', now()->toDateString())
            ->get()
            ->filter(function ($c) use ($doctor) {
                return $this->isDoctorMatchingConsultation($doctor, $c);
            })
            ->count();

        return response()->json([
            'status' => 'success',
            'requests' => $pending,
            'missed_count' => $missedCount,
        ]);
    }

    public function pickupPendingRequest(Request $request, $id)
    {
        $doctorUser = auth()->user();
        if (!$doctorUser || !$doctorUser->doctor) {
            return response()->json(['status' => 'error', 'message' => 'Non autorisé'], 403);
        }

        $consultation = Consultation::where('id', $id)->first();
        if (!$consultation) {
            return response()->json(['status' => 'error', 'message' => 'Demande introuvable.'], 404);
        }

        if (!$this->isDoctorMatchingConsultation($doctorUser->doctor, $consultation)) {
            return response()->json(['status' => 'error', 'message' => 'Cette consultation ne correspond pas à votre spécialité.'], 403);
        }

        return $this->acceptPatientOnlineCall($request, $id);
    }

    public function finishPendingRequest(Request $request, $id)
    {
        $doctorUser = auth()->user();
        if (!$doctorUser || !$doctorUser->doctor) {
            return response()->json(['status' => 'error', 'message' => 'Non autorisé'], 403);
        }

        $consultation = Consultation::where('id', $id)->first();
        if (!$consultation) {
            return response()->json(['status' => 'error', 'message' => 'Demande introuvable.'], 404);
        }

        $consultation->update([
            'status' => 1,
            'is_call_active' => false,
            'call_status' => 'completed',
            'call_ended_at' => now(),
        ]);

        // Mettre à jour aussi le rendez-vous lié si existant
        \App\Models\RendezVous::where('consultation_id', $consultation->id)->update([
            'status' => 'complete',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'La téléconsultation a été marquée comme terminée avec succès.',
            'consultation_id' => $consultation->id
        ]);
    }

    public function rejectIncomingCall(Request $request, $id)
    {
        $consultation = Consultation::find($id);
        if ($consultation && $consultation->is_call_active && is_null($consultation->doctor_id)) {
            $user = auth()->user();
            if ($user && $user->doctor) {
                $doctorId = $user->doctor->id;
                $key = "consultation_{$id}_ignored_doctors";
                $ignored = \Illuminate\Support\Facades\Cache::get($key, []);
                if (!is_array($ignored)) {
                    $ignored = [];
                }
                if (!in_array($doctorId, $ignored)) {
                    $ignored[] = $doctorId;
                    \Illuminate\Support\Facades\Cache::put($key, $ignored, now()->addDays(1));
                }
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Appel ignoré par ce médecin']);
    }
}
