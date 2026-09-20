<?php

namespace App\Http\Controllers\Infirmier;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Drug;
use App\Models\Infirmier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{

    public function today()
    {
        $infirmierId = optional(auth()->user()->infirmier)->id;
        $consultations = Consultation::where(function ($q) use ($infirmierId) {
            if ($infirmierId) {
                $q->where('infirmier_id', $infirmierId);
            }
        })->withCount('ordonnances', 'arret', 'examen')->where('date_consultation', date('Y-m-d'))->get();

        return view('users.infirmier.consultation.today', compact('consultations'));
    }

    public function allPatients()
    {
        $infirmierId = optional(auth()->user()->infirmier)->id;
        $consultations = Consultation::orderByDESC('created_at')
            ->where(function ($q) use ($infirmierId) {
                if ($infirmierId) {
                    $q->where('infirmier_id', $infirmierId)
                        ->orWhereNull('infirmier_id');
                } else {
                    $q->whereNull('infirmier_id');
                }
            })
            ->where('status_inf', 0)
            ->get();

        return view('users.infirmier.consultation.all', compact('consultations'));
    }

    public function history()
    {
        $infirmierId = optional(auth()->user()->infirmier)->id;
        $consultations = Consultation::orderByDESC("date_consultation")
            ->withCount(['ordonnances', 'arret', "examen", "declaration", "registre"])
            ->with("ordonnances", "arret", "examen", "declaration", "registre")
            ->where(function ($q) use ($infirmierId) {
                if ($infirmierId) {
                    $q->where('infirmier_id', $infirmierId);
                }
            })
            ->where(function ($q) {
                $q->where('date_consultation', '<', date('Y-m-d'))
                  ->orWhere('status_inf', 1);
            })
            ->get();

        return view('users.infirmier.consultation.history', compact('consultations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'consultation_id' => "required",
            'tension_arterielle' => 'nullable',
            'temperature' => 'nullable',
            'taille' => 'nullable',
            'poids' => 'nullable',
            'imc' => 'nullable',
            'pouls' => 'nullable',
            'issue_consultation' => 'nullable',
            'doctor_id' => 'nullable|integer',
            'observation_infirmiere' => 'nullable',
            'orientation_infirmier' => 'nullable|in:sortie,teleconsultation,urgence',
            'type_soins_infirmier' => 'nullable|string',
            'observation_soins' => 'nullable|string',
            'desired_date' => 'nullable|date',
            'desired_time' => 'nullable|string',
            'hospital_id' => 'nullable|integer',
            'teleconsultation_hospital_id' => 'nullable|integer',
        ]);

        $consultation = Consultation::findOrFail($data["consultation_id"]);
        $consultation->infirmier_id = auth()->user()->infirmier->id ?? null;
        $consultation->tension_arterielle = $data["tension_arterielle"] ?? $consultation->tension_arterielle;
        $consultation->temperature = $request->temperature ?? $consultation->temperature;
        $consultation->taille = $data["taille"] ?? $consultation->taille;
        $consultation->poids = $data["poids"] ?? $consultation->poids;
        $consultation->imc = $data["imc"] ?? $consultation->imc;
        $consultation->pouls = $data["pouls"] ?? $consultation->pouls;

        $orientation = $request->input('orientation_infirmier');
        $consultation->orientation_infirmier = $orientation;

        $message = 'Prise de constantes enregistrée. Votre patient est en attente chez le médecin.';

        $hasVitals = !empty($data['tension_arterielle']) || !empty($request->temperature) || !empty($data['poids']) || !empty($data['pouls'])
            || !empty($consultation->tension_arterielle) || !empty($consultation->temperature) || !empty($consultation->poids) || !empty($consultation->pouls);

        if ($orientation === 'sortie') {
            $consultation->type_soins_infirmier = $request->input('type_soins_infirmier');
            $consultation->observation_soins = $request->input('observation_soins');
            $consultation->observation_infirmiere = $request->input('observation_soins') ?? $request->input('observation_infirmiere');
            $consultation->status = 1; // Terminé / Libéré (n'apparaît pas chez le médecin)
            $consultation->status_inf = 1;
            $message = 'Soins administrés et consultation du patient enregistrée avec succès.';
        } elseif ($orientation === 'teleconsultation') {
            if (!$hasVitals) {
                return back()->with('error', "Veuillez renseigner au moins une constante physique du patient (Tension, Température, Poids, Pouls) avant de programmer la téléconsultation.")->withInput();
            }

            if ($request->filled('hospital_id')) {
                $consultation->hospital_id = $request->input('hospital_id');
            } elseif ($request->filled('teleconsultation_hospital_id')) {
                $consultation->hospital_id = $request->input('teleconsultation_hospital_id');
            }
            if ($request->filled('desired_date')) {
                $consultation->desired_date = $request->input('desired_date');
            }
            if ($request->filled('desired_time')) {
                $consultation->desired_time = $request->input('desired_time');
            }
            if ($request->filled('doctor_id')) {
                $consultation->doctor_id = $request->input('doctor_id');
            }
            if ($request->filled('observation_infirmiere')) {
                $consultation->observation_infirmiere = $request->input('observation_infirmiere');
            }
            if (empty($consultation->infirmier_id) && auth()->user() && auth()->user()->infirmier) {
                $consultation->infirmier_id = auth()->user()->infirmier->id;
            }
            if (empty($consultation->call_channel)) {
                $consultation->call_channel = 'consultation_' . $consultation->id . '_' . \Illuminate\Support\Str::random(10);
            }
            $consultation->call_status = 'pending';
            $consultation->is_call_active = false;
            $consultation->status = 0;
            $consultation->status_inf = 1;
            $message = 'Téléconsultation programmée avec succès.';
        } elseif ($orientation === 'urgence') {
            $consultation->is_urgence = 1;
            $consultation->status_inf = 1;
            $consultation->status = 0; // Toujours 0 en attente tant que le médecin n'a pas terminé
            if (empty($consultation->call_status)) {
                $consultation->call_status = 'calling';
            }
            $message = 'Appel d\'urgence enregistré pour ce patient.';
        } else {
            // Processus standard : Envoi chez le médecin dans sa file d'attente
            if (!$hasVitals) {
                return back()->with('error', "Veuillez renseigner les constantes physiques du patient avant de l'envoyer chez le médecin, ou sélectionner une orientation spécifique (Téléconsultation, Consultation ou Appel d'urgence).")->withInput();
            }

            if ($request->filled('doctor_id')) {
                $consultation->doctor_id = $request->input('doctor_id');
            }
            if ($request->filled('observation_infirmiere')) {
                $consultation->observation_infirmiere = $request->input('observation_infirmiere');
            }
            $consultation->status = 0; // En attente chez le médecin
            $consultation->status_inf = 1;
        }

        $consultation->save();

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function formulaire($id)
    {
        $consultation = Consultation::with(['patient.user', 'patient.lieuNaissance', 'patient.residenceActuelle', 'admission'])->findOrFail($id);
        $hospitalId = auth()->user()->infirmier->hospital_id ?? auth()->user()->hospital_id ?? 1;
        
        $doctors = Doctor::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', 1);
            })
            ->where('hospital_id', $hospitalId)
            ->get();

        $infHospital = (auth()->check() && auth()->user()->infirmier && auth()->user()->infirmier->hospital)
            ? auth()->user()->infirmier->hospital
            : \App\Models\Hospital::find($hospitalId);
        $isTeleconsultationActive = $infHospital ? (bool)$infHospital->is_teleconsultation_active : false;

        return view('users.infirmier.consultation.formulaire', [
            'consultation' => $consultation,
            'doctors' => $doctors,
            'isTeleconsultationActive' => $isTeleconsultationActive
        ]);
    }

    public function getTeleconsultationHospitals(Request $request)
    {
        $consultationId = $request->input('consultation_id');
        $dateStr = $request->input('date', date('Y-m-d'));
        $timeStr = $request->input('time');

        $consultation = null;
        $serviceName = null;
        if ($consultationId) {
            $consultation = Consultation::with([
                'admission.prestationHospital.serviceHospital.service',
                'admission.prestationHospital.prestationService.service',
                'prestationHospital.serviceHospital.service',
                'prestationHospital.prestationService.service'
            ])->find($consultationId);

            if ($consultation) {
                if ($consultation->prestationHospital && $consultation->prestationHospital->serviceHospital && $consultation->prestationHospital->serviceHospital->service) {
                    $serviceName = $consultation->prestationHospital->serviceHospital->service->libelle;
                } elseif ($consultation->prestationHospital && $consultation->prestationHospital->prestationService && $consultation->prestationHospital->prestationService->service) {
                    $serviceName = $consultation->prestationHospital->prestationService->service->libelle;
                } elseif ($consultation->admission && $consultation->admission->prestationHospital && $consultation->admission->prestationHospital->serviceHospital && $consultation->admission->prestationHospital->serviceHospital->service) {
                    $serviceName = $consultation->admission->prestationHospital->serviceHospital->service->libelle;
                } elseif ($consultation->admission && $consultation->admission->prestationHospital && $consultation->admission->prestationHospital->prestationService && $consultation->admission->prestationHospital->prestationService->service) {
                    $serviceName = $consultation->admission->prestationHospital->prestationService->service->libelle;
                }
            }
        }

        $currentHospitalId = auth()->user()->infirmier->hospital_id ?? auth()->user()->hospital_id ?? optional($consultation)->hospital_id ?? 1;

        $hospitals = \App\Models\Hospital::where(function($q) {
            $q->where('delete', 0)->orWhereNull('delete');
        })->where('is_teleconsultation_active', true)->get();
        if ($hospitals->isEmpty()) {
            $hospitals = \App\Models\Hospital::where('is_teleconsultation_active', true)->get();
        }

        $results = [];
        foreach ($hospitals as $h) {
            $doctorsQuery = Doctor::with([
                'user.availability',
                'serviceHospital.service',
                'typeDoctor',
                'prestationDoctors.prestationHospital.serviceHospital.service',
                'prestationDoctors.prestationHospital.prestationService.service'
            ])
            ->where('hospital_id', $h->id)
            ->has('user')
            ->get();

            $totalDoctors = $doctorsQuery->count();

            // Médecins disponibles pour la téléconsultation
            $availableDocs = $doctorsQuery->filter(function ($doc) use ($serviceName, $dateStr, $timeStr) {
                if (!$this->isDoctorEligibleForService($doc, $serviceName)) {
                    return false;
                }
                return $this->isDoctorAvailableOnDate($doc, $dateStr, $timeStr);
            });

            $availableCount = $availableDocs->count();

            $hName = $h->label ?: ($h->nom_direction_generale ?: ($h->reference ?: 'Hôpital #' . $h->id));
            $district = $h->district_sanitaire ?: optional($h->commune)->libelle ?: 'Côte d\'Ivoire';
            $contact = $h->contact ?: '';

            $results[] = [
                'id' => $h->id,
                'name' => $hName,
                'reference' => $h->reference ?: 'HO-' . str_pad($h->id, 4, '0', STR_PAD_LEFT),
                'district' => $district,
                'contact' => $contact,
                'total_doctors_count' => $totalDoctors,
                'available_doctors_count' => $availableCount,
                'is_current' => ($h->id == $currentHospitalId),
                'service_name' => $serviceName ?? 'Consultation Générale'
            ];
        }

        return response()->json([
            'status' => 'success',
            'hospitals' => $results,
            'current_hospital_id' => $currentHospitalId,
            'service_name' => $serviceName ?? 'Consultation Générale',
        ]);
    }

    public function getTeleconsultationDoctors(Request $request)
    {
        $consultationId = $request->input('consultation_id');
        $dateStr = $request->input('date');
        $timeStr = $request->input('time');
        $selectedHospitalId = $request->input('hospital_id');

        if (!$consultationId || !$dateStr) {
            return response()->json(['doctors' => [], 'service_name' => '']);
        }

        $consultation = Consultation::with([
            'admission.prestationHospital.serviceHospital.service',
            'admission.prestationHospital.prestationService.service',
            'prestationHospital.serviceHospital.service',
            'prestationHospital.prestationService.service'
        ])->find($consultationId);

        if (!$consultation) {
            return response()->json(['doctors' => [], 'service_name' => '']);
        }

        $serviceName = null;
        if ($consultation->prestationHospital && $consultation->prestationHospital->serviceHospital && $consultation->prestationHospital->serviceHospital->service) {
            $serviceName = $consultation->prestationHospital->serviceHospital->service->libelle;
        } elseif ($consultation->prestationHospital && $consultation->prestationHospital->prestationService && $consultation->prestationHospital->prestationService->service) {
            $serviceName = $consultation->prestationHospital->prestationService->service->libelle;
        } elseif ($consultation->admission && $consultation->admission->prestationHospital && $consultation->admission->prestationHospital->serviceHospital && $consultation->admission->prestationHospital->serviceHospital->service) {
            $serviceName = $consultation->admission->prestationHospital->serviceHospital->service->libelle;
        } elseif ($consultation->admission && $consultation->admission->prestationHospital && $consultation->admission->prestationHospital->prestationService && $consultation->admission->prestationHospital->prestationService->service) {
            $serviceName = $consultation->admission->prestationHospital->prestationService->service->libelle;
        }

        $hospitalId = $selectedHospitalId 
            ?: (auth()->user()->infirmier->hospital_id ?? auth()->user()->hospital_id ?? $consultation->hospital_id ?? null);

        $query = Doctor::with([
            'user.availability',
            'serviceHospital.service',
            'typeDoctor',
            'prestationDoctors.prestationHospital.serviceHospital.service',
            'prestationDoctors.prestationHospital.prestationService.service'
        ])->has('user');

        if ($hospitalId) {
            $query->where(function ($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId)->orWhereNull('hospital_id');
            });
        }

        $allDoctors = $query->get();

        $eligibleDoctors = $allDoctors->filter(function ($doctor) use ($serviceName, $dateStr, $timeStr) {
            // 1. Spécialité : Généraliste OU Spécialiste du service de l'affectation
            if (!$this->isDoctorEligibleForService($doctor, $serviceName)) {
                return false;
            }

            // 2. Disponibilité : Jour et créneau horaire
            return $this->isDoctorAvailableOnDate($doctor, $dateStr, $timeStr);
        })->values();

        $results = [];
        foreach ($eligibleDoctors as $doc) {
            $rawName = trim(($doc->user->name ?? '') . ' ' . ($doc->user->prenom ?? ''));
            $docName = preg_match('/^dr\.?\s+/i', $rawName) ? $rawName : ('Dr. ' . $rawName);
            $specialite = ($doc->type_name === 'generaliste' || empty($doc->type_doctor_id)) ? 'Généraliste' : ($doc->typeDoctor->label ?? ($doc->serviceHospital->service->libelle ?? 'Médecin'));
            $creneau = $this->getDoctorCreneauForDate($doc, $dateStr);
            $text = $docName . ' (' . $specialite . ($creneau ? ' - ' . $creneau : '') . ')';

            $results[] = [
                'id' => $doc->id,
                'name' => $docName,
                'specialite' => $specialite,
                'creneau' => $creneau,
                'text' => $text,
            ];
        }

        return response()->json([
            'doctors' => $results,
            'service_name' => $serviceName ?? 'Consultation Générale'
        ]);
    }

    private function isDoctorEligibleForService($doctor, $serviceName)
    {
        $typeName = strtolower(trim($doctor->type_name ?? ''));
        $typeDoctorLabel = strtolower(trim($doctor->typeDoctor->label ?? ''));
        $serviceDocLabel = strtolower(trim($doctor->serviceHospital->service->libelle ?? ''));

        // 1. Médecin Généraliste -> Éligible
        $isGeneraliste = ($typeName === 'generaliste')
            || str_contains($typeName, 'general')
            || str_contains($typeName, 'général')
            || str_contains($typeDoctorLabel, 'general')
            || str_contains($typeDoctorLabel, 'général')
            || str_contains($serviceDocLabel, 'general')
            || str_contains($serviceDocLabel, 'général')
            || empty($doctor->type_doctor_id);

        if ($isGeneraliste) {
            return true;
        }

        // 2. Médecin Spécialiste du service affecté -> Éligible
        if (!empty($serviceName)) {
            $svc = strtolower(trim($serviceName));
            if (!empty($serviceDocLabel) && (str_contains($serviceDocLabel, $svc) || str_contains($svc, $serviceDocLabel))) {
                return true;
            }
            if (!empty($typeDoctorLabel) && (str_contains($typeDoctorLabel, $svc) || str_contains($svc, $typeDoctorLabel))) {
                return true;
            }
            if ($doctor->prestationDoctors) {
                foreach ($doctor->prestationDoctors as $pd) {
                    $pSvc1 = strtolower(trim($pd->prestationHospital->serviceHospital->service->libelle ?? ''));
                    $pSvc2 = strtolower(trim($pd->prestationHospital->prestationService->service->libelle ?? ''));
                    if (!empty($pSvc1) && (str_contains($pSvc1, $svc) || str_contains($svc, $pSvc1))) {
                        return true;
                    }
                    if (!empty($pSvc2) && (str_contains($pSvc2, $svc) || str_contains($svc, $pSvc2))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function isDoctorAvailableOnDate($doctor, $dateStr, $timeStr = null)
    {
        if (!$doctor || !$doctor->user) {
            return false;
        }

        $availability = $doctor->user->availability;
        if (!$availability || empty($availability->days)) {
            return false;
        }

        $days = json_decode($availability->days, true);
        if (!is_array($days) || empty($days)) {
            return false;
        }

        $days = array_map('strval', $days);

        try {
            $targetDate = \Carbon\Carbon::parse($dateStr);
        } catch (\Exception $e) {
            return false;
        }

        $dayOfWeek = strval($targetDate->dayOfWeek); // 0 (Dimanche) à 6 (Samedi)
        $dayOfWeekIso = strval($targetDate->dayOfWeekIso); // 1 (Lundi) à 7 (Dimanche)
        $dayZeroMon = strval($targetDate->dayOfWeekIso - 1); // 0 (Lundi) à 6 (Dimanche)

        $isDayAvailable = in_array($dayZeroMon, $days, true)
            || in_array($dayOfWeek, $days, true)
            || in_array($dayOfWeekIso, $days, true);

        if (!$isDayAvailable) {
            return false;
        }

        if (!empty($timeStr)) {
            $dayIndex = intval($dayZeroMon);
            $startTimes = json_decode($availability->hour_start, true);
            $endTimes = json_decode($availability->hour_end, true);

            $startTime = '00:00';
            $endTime = '23:59';

            if (is_array($startTimes)) {
                if (isset($startTimes[$dayIndex]) && !empty($startTimes[$dayIndex])) {
                    $startTime = $startTimes[$dayIndex];
                } elseif (isset($startTimes[0])) {
                    $startTime = $startTimes[0];
                }
            } elseif (!empty($availability->hour_start)) {
                $startTime = $availability->hour_start;
            }

            if (is_array($endTimes)) {
                if (isset($endTimes[$dayIndex]) && !empty($endTimes[$dayIndex])) {
                    $endTime = $endTimes[$dayIndex];
                } elseif (isset($endTimes[0])) {
                    $endTime = $endTimes[0];
                }
            } elseif (!empty($availability->hour_end)) {
                $endTime = $availability->hour_end;
            }

            $startFormatted = strlen($startTime) >= 5 ? substr($startTime, 0, 5) : '00:00';
            $endFormatted = strlen($endTime) >= 5 ? substr($endTime, 0, 5) : '23:59';
            $timeFormatted = strlen($timeStr) >= 5 ? substr($timeStr, 0, 5) : $timeStr;

            if ($timeFormatted < $startFormatted || $timeFormatted > $endFormatted) {
                return false;
            }
        }

        return true;
    }

    private function getDoctorCreneauForDate($doctor, $dateStr)
    {
        if (!$doctor || !$doctor->user || !$doctor->user->availability) {
            return null;
        }

        $availability = $doctor->user->availability;
        try {
            $targetDate = \Carbon\Carbon::parse($dateStr);
        } catch (\Exception $e) {
            return null;
        }

        $dayZeroMon = intval($targetDate->dayOfWeekIso - 1);
        $startTimes = json_decode($availability->hour_start, true);
        $endTimes = json_decode($availability->hour_end, true);

        $start = '00:00';
        $end = '23:59';

        if (is_array($startTimes)) {
            if (isset($startTimes[$dayZeroMon]) && !empty($startTimes[$dayZeroMon])) {
                $start = $startTimes[$dayZeroMon];
            } elseif (isset($startTimes[0])) {
                $start = $startTimes[0];
            }
        } elseif (!empty($availability->hour_start)) {
            $start = $availability->hour_start;
        }

        if (is_array($endTimes)) {
            if (isset($endTimes[$dayZeroMon]) && !empty($endTimes[$dayZeroMon])) {
                $end = $endTimes[$dayZeroMon];
            } elseif (isset($endTimes[0])) {
                $end = $endTimes[0];
            }
        } elseif (!empty($availability->hour_end)) {
            $end = $availability->hour_end;
        }

        $startFormatted = strlen($start) >= 5 ? substr($start, 0, 5) : '00:00';
        $endFormatted = strlen($end) >= 5 ? substr($end, 0, 5) : '23:59';

        if ($startFormatted === '00:00' && $endFormatted === '23:59') {
            return 'Dispo toute la journée';
        }

        return $startFormatted . ' à ' . $endFormatted;
    }

    public function formulaireIssue($consultation, $title = 'Issue de la consultation')
    {
        $consultation = Consultation::find($consultation);

        $drugs = Drug::with('drugSale')->where('hospital_id', auth()->user()->infirmier->hospital_id)->get();

        return view('users.infirmier.consultation.issue', ['title' => $title, 'consultation' => $consultation, 'drugs' => $drugs]);
    }

    public function detail($id)
    {
        $consultation = Consultation::findOrFail($id);
        return view('users.infirmier.consultation.detail', compact('consultation'));
    }

    public function patientCard(Request $request, $id)
    {
        $patient = \App\Models\Patient::with(['user', 'hospital'])->findOrFail($id);
        if ($request->ajax()) {
            return view('users.secretariat.patient.card_inner', compact('patient'));
        }
        return view('users.secretariat.patient.card', compact('patient'));
    }

    public function getIncomingCall(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->infirmier) {
            return response()->json(['has_incoming' => false]);
        }

        $infirmier = $user->infirmier;
        $infHospital = $infirmier->hospital;
        if ($infHospital && !$infHospital->is_teleconsultation_active) {
            return response()->json(['has_incoming' => false]);
        }

        $hospitalId = $infirmier->hospital_id ?? $user->hospital_id;

        $incoming = Consultation::where('is_call_active', true)
            ->where('call_status', 'calling')
            ->where('status', 0)
            ->where(function ($q) use ($infirmier, $hospitalId) {
                $q->where('hospital_id', $hospitalId)
                  ->orWhereHas('infirmier', function ($sq) use ($hospitalId) {
                      $sq->where('hospital_id', $hospitalId);
                  })
                  ->orWhere('infirmier_id', $infirmier->id)
                  ->orWhereNull('hospital_id');
            })
            ->whereNotNull('doctor_id')
            ->whereNotNull('call_channel')
            ->with(['doctor.user', 'patient.user', 'prestationHospital.prestationService'])
            ->latest('call_started_at')
            ->first();

        if (!$incoming) {
            return response()->json(['has_incoming' => false]);
        }

        $docUser = optional(optional($incoming->doctor)->user);
        $doctorName = trim(($docUser->name ?? '') . ' ' . ($docUser->prenom ?? '')) ?: 'Médecin';

        $pUser = optional(optional($incoming->patient)->user);
        $patientName = trim(($pUser->name ?? '') . ' ' . ($pUser->prenom ?? '')) ?: 'Patient';

        $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
        $infirmierName = trim(($user->name ?? '') . ' ' . ($user->prenom ?? '')) ?: 'Infirmier(ère)';
        $token = \App\Services\LiveKitTokenService::generateToken(
            $incoming->call_channel,
            'infirmier_' . $user->id,
            'Inf. ' . $infirmierName
        );

        return response()->json([
            'has_incoming' => true,
            'consultation_id' => $incoming->id,
            'call_started_at' => $incoming->call_started_at ? (is_object($incoming->call_started_at) ? $incoming->call_started_at->timestamp : strtotime($incoming->call_started_at)) : ($incoming->updated_at ? (is_object($incoming->updated_at) ? $incoming->updated_at->timestamp : strtotime($incoming->updated_at)) : time()),
            'doctor_name' => 'Dr. ' . $doctorName,
            'doctor_photo' => ($docUser && $docUser->photo) ? asset('storage/'.$docUser->photo) : null,
            'patient_name' => $patientName,
            'patient_code' => optional($incoming->patient)->code_patient ?? '',
            'motif' => optional(optional($incoming->prestationHospital)->prestationService)->libelle ?? $incoming->motif_consultation ?? 'Téléconsultation',
            'channel' => $incoming->call_channel,
            'token' => $token,
            'livekit_url' => $livekitUrl,
            'call_status' => $incoming->call_status,
        ]);
    }

    public function pickupCall(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->infirmier) {
            return response()->json(['status' => 'error', 'message' => 'Non autorisé'], 403);
        }

        $consultation = Consultation::with(['doctor.user', 'patient.user', 'prestationHospital.prestationService'])->find($id);
        if (!$consultation) {
            return response()->json(['status' => 'error', 'message' => 'Consultation introuvable'], 404);
        }

        if (empty($consultation->call_channel)) {
            $consultation->call_channel = 'consultation_' . $consultation->id . '_' . \Illuminate\Support\Str::random(10);
        }

        $consultation->is_call_active = true;
        $consultation->call_status = 'in_call';
        if (!$consultation->call_started_at) {
            $consultation->call_started_at = now();
        }
        // Assigner l'infirmier connecté qui décroche l'appel
        $consultation->infirmier_id = $user->infirmier->id;
        $consultation->save();

        $docUser = optional(optional($consultation->doctor)->user);
        $doctorName = trim(($docUser->name ?? '') . ' ' . ($docUser->prenom ?? '')) ?: 'Médecin';

        $pUser = optional(optional($consultation->patient)->user);
        $patientName = trim(($pUser->name ?? '') . ' ' . ($pUser->prenom ?? '')) ?: 'Patient';

        $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
        $infirmierName = trim(($user->name ?? '') . ' ' . ($user->prenom ?? '')) ?: 'Infirmier(ère)';
        $token = \App\Services\LiveKitTokenService::generateToken(
            $consultation->call_channel,
            'infirmier_' . $user->id,
            'Inf. ' . $infirmierName
        );

        return response()->json([
            'status' => 'success',
            'consultation_id' => $consultation->id,
            'doctor_name' => 'Dr. ' . $doctorName,
            'patient_name' => $patientName,
            'patient_code' => optional($consultation->patient)->code_patient ?? '',
            'motif' => optional(optional($consultation->prestationHospital)->prestationService)->libelle ?? $consultation->motif_consultation ?? 'Téléconsultation',
            'channel' => $consultation->call_channel,
            'token' => $token,
            'livekit_url' => $livekitUrl,
            'call_status' => $consultation->call_status,
        ]);
    }

    public function endCall(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->infirmier) {
            return response()->json(['status' => 'error', 'message' => 'Non autorisé'], 403);
        }

        $consultation = Consultation::find($id);
        if ($consultation) {
            $consultation->update([
                'is_call_active' => false,
                'call_status' => 'ended',
                'call_ended_at' => now(),
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Appel raccroché avec succès']);
    }

    public function callStatus(Request $request, $id)
    {
        $consultation = Consultation::find($id);
        if (!$consultation) {
            return response()->json([
                'status' => 'error',
                'is_call_active' => false,
                'call_status' => 'ended'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'id' => $consultation->id,
            'is_call_active' => (bool)$consultation->is_call_active,
            'call_status' => $consultation->call_status ?? ($consultation->is_call_active ? 'in_call' : 'ended'),
            'channel' => $consultation->call_channel,
        ]);
    }

    public function startEmergencyCall(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->infirmier) {
            return response()->json(['status' => 'error', 'message' => 'Non autorisé'], 403);
        }

        $consultation = Consultation::with([
            'patient.user',
            'prestationHospital.serviceHospital.service',
            'prestationHospital.prestationService.service',
            'admission.prestationHospital.serviceHospital.service'
        ])->find($id);

        if (!$consultation) {
            return response()->json(['status' => 'error', 'message' => 'Consultation introuvable'], 404);
        }

        if (empty($consultation->call_channel)) {
            $consultation->call_channel = 'consultation_urgence_' . $consultation->id . '_' . \Illuminate\Support\Str::random(10);
        }

        $consultation->orientation_infirmier = 'urgence';
        $consultation->is_urgence = 1;
        $consultation->is_call_active = true;
        $consultation->call_status = 'calling';
        $consultation->call_started_at = now();
        $consultation->infirmier_id = $user->infirmier->id;
        $consultation->doctor_id = null; // Permet à tous les médecins éligibles (généralistes + spécialistes du service) de recevoir l'appel
        $consultation->status = 0; // Toujours 0 tant qu'un médecin n'a pas clôturé
        $consultation->status_inf = 1; // Constantes physiques prises par l'infirmier

        // Enregistrement automatique des constantes physiques
        if ($request->filled('poids')) $consultation->poids = $request->input('poids');
        if ($request->filled('taille')) $consultation->taille = $request->input('taille');
        if ($request->filled('imc')) $consultation->imc = $request->input('imc');
        if ($request->filled('temperature')) $consultation->temperature = $request->input('temperature');
        if ($request->filled('tension_arterielle')) $consultation->tension_arterielle = $request->input('tension_arterielle');
        if ($request->filled('pouls')) $consultation->pouls = $request->input('pouls');

        $consultation->save();

        // Synchroniser également dans le registre de consultation curative si associé
        if ($consultation->registre && $consultation->registre->registreConsultationCurative) {
            $regCurative = $consultation->registre->registreConsultationCurative;
            if ($request->filled('poids')) $regCurative->poids = $request->input('poids');
            if ($request->filled('taille')) $regCurative->taille = $request->input('taille');
            if ($request->filled('imc')) $regCurative->imc = $request->input('imc');
            if ($request->filled('temperature')) $regCurative->temperature = $request->input('temperature');
            if ($request->filled('tension_arterielle')) $regCurative->ta = $request->input('tension_arterielle');
            if ($request->filled('pouls')) $regCurative->pouls = $request->input('pouls');
            $regCurative->save();
        }

        // Récupérer les médecins généralistes et spécialistes correspondant au service du patient
        $hospitalId = $consultation->hospital_id ?? $user->infirmier->hospital_id ?? $user->hospital_id;
        $allDoctors = \App\Models\Doctor::has('user')
            ->when($hospitalId, function ($q) use ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            })
            ->with(['user', 'serviceHospital.service', 'typeDoctor', 'prestationDoctors'])
            ->get();

        $matchingDoctors = [];
        $doctorController = new \App\Http\Controllers\Doctor\ConsultationController();
        $reflection = new \ReflectionMethod($doctorController, 'isDoctorMatchingConsultation');
        $reflection->setAccessible(true);

        foreach ($allDoctors as $doc) {
            if ($reflection->invoke($doctorController, $doc, $consultation)) {
                $docName = optional($doc->user)->name . ' ' . optional($doc->user)->prenom;
                $matchingDoctors[] = trim($docName) ?: ('Dr. ' . $doc->id);
            }
        }

        $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
        $infirmierName = trim(($user->name ?? '') . ' ' . ($user->prenom ?? '')) ?: 'Infirmier(ère)';
        $token = \App\Services\LiveKitTokenService::generateToken(
            $consultation->call_channel,
            'infirmier_' . $user->id,
            'Inf. ' . $infirmierName
        );

        $pUser = optional(optional($consultation->patient)->user);
        $patientName = trim(($pUser->name ?? '') . ' ' . ($pUser->prenom ?? '')) ?: 'Patient';

        return response()->json([
            'status' => 'success',
            'message' => 'Appel d\'urgence déclenché vers tous les médecins généralistes et spécialistes du service en service.',
            'consultation_id' => $consultation->id,
            'channel' => $consultation->call_channel,
            'token' => $token,
            'livekit_url' => $livekitUrl,
            'patient_name' => $patientName,
            'doctors_alerted_count' => count($matchingDoctors),
            'doctors_list' => $matchingDoctors,
        ]);
    }

    public function getActiveTeleconsultations(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->infirmier) {
            return response()->json(['status' => 'success', 'teleconsultations' => []]);
        }

        $infHospital = $user->infirmier->hospital;
        if ($infHospital && !$infHospital->is_teleconsultation_active) {
            return response()->json(['status' => 'success', 'teleconsultations' => []]);
        }

        $hospitalId = $user->infirmier->hospital_id ?? $user->hospital_id;
        $teleconsultations = Consultation::where(function ($q) {
                $q->whereIn('orientation_infirmier', ['teleconsultation', 'urgence'])
                  ->orWhere('is_urgence', 1);
            })
            ->where(function ($q) use ($hospitalId, $user) {
                $q->where('hospital_id', $hospitalId)
                  ->orWhereHas('infirmier', function ($sq) use ($hospitalId) {
                      $sq->where('hospital_id', $hospitalId);
                  })
                  ->orWhere('infirmier_id', $user->infirmier->id)
                  ->orWhereNull('hospital_id');
            })
            ->where('status', 0)
            ->whereNotIn('call_status', ['completed', 'cancelled'])
            ->with(['doctor.user', 'patient.user', 'prestationHospital.prestationService'])
            ->latest('created_at')
            ->get()
            ->map(function ($c) use ($user) {
                $pUser = optional(optional($c->patient)->user);
                $pName = trim(($pUser->name ?? '') . ' ' . ($pUser->prenom ?? '')) ?: 'Patient';

                $dUser = optional(optional($c->doctor)->user);
                $dName = trim(($dUser->name ?? '') . ' ' . ($dUser->prenom ?? '')) ?: 'Médecin';

                $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
                $token = '';
                if (!empty($c->call_channel) && auth()->check()) {
                    $infName = trim(($user->name ?? '') . ' ' . ($user->prenom ?? ''));
                    $token = \App\Services\LiveKitTokenService::generateToken($c->call_channel, 'infirmier_' . $user->id, 'Inf. ' . $infName);
                }

                $hasDoc = !empty($c->doctor_id) || in_array($c->call_status, ['accepted', 'in_call']);

                return [
                    'id' => $c->id,
                    'patient_name' => $pName,
                    'patient_code' => optional($c->patient)->code_patient ?? '',
                    'doctor_name' => $hasDoc ? ('Dr. ' . $dName) : 'Médecins de garde',
                    'desired_date' => $c->desired_date ? date('d/m/Y', strtotime($c->desired_date)) : ($c->date_consultation ? date('d/m/Y', strtotime($c->date_consultation)) : 'Aujourd\'hui'),
                    'desired_time' => $c->desired_time ?: ($c->created_at ? $c->created_at->format('H:i') : ''),
                    'motif' => optional(optional($c->prestationHospital)->prestationService)->libelle ?? $c->motif_consultation ?? 'Téléconsultation',
                    'is_call_active' => (bool)$c->is_call_active,
                    'call_status' => $c->call_status,
                    'channel' => $c->call_channel,
                    'token' => $token,
                    'livekit_url' => $livekitUrl,
                    'has_doctor_accepted' => $hasDoc,
                ];
            });

        return response()->json(['status' => 'success', 'teleconsultations' => $teleconsultations]);
    }

    public function teleconsultations(Request $request)
    {
        $user = auth()->user();
        $hospitalId = $user->infirmier->hospital_id ?? $user->hospital_id ?? 1;

        $filter = $request->input('filter', 'all');

        $query = Consultation::where('hospital_id', $hospitalId)
            ->where(function ($q) {
                $q->where('orientation_infirmier', 'teleconsultation')
                  ->orWhereNotNull('desired_date');
            })
            ->with([
                'patient.user',
                'patient.lieuNaissance',
                'patient.residenceActuelle',
                'doctor.user',
                'doctor.hospital',
                'infirmier.user',
                'teleconsultationHospital',
                'prestationHospital.serviceHospital.service',
                'prestationHospital.prestationService.service',
                'admission.prestationHospital.serviceHospital.service',
            ]);

        $todayStr = date('Y-m-d');

        $countToday = (clone $query)->where('desired_date', $todayStr)->where('status', 0)->count();
        $countUpcoming = (clone $query)->where('desired_date', '>', $todayStr)->where('status', 0)->count();
        $countCompleted = (clone $query)->where('status', 1)->count();
        $countAll = (clone $query)->count();

        if ($filter === 'today') {
            $query->where('desired_date', $todayStr);
        } elseif ($filter === 'upcoming') {
            $query->where('desired_date', '>', $todayStr)->where('status', 0);
        } elseif ($filter === 'completed') {
            $query->where('status', 1);
        }

        $consultations = $query->orderBy('desired_date', 'DESC')
            ->orderBy('desired_time', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('users.infirmier.consultation.teleconsultations', compact(
            'consultations',
            'filter',
            'countToday',
            'countUpcoming',
            'countCompleted',
            'countAll'
        ));
    }

    public function startScheduledTeleconsultation(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user || !$user->infirmier) {
            return response()->json(['status' => 'error', 'message' => 'Non autorisé'], 403);
        }

        $consultation = Consultation::with([
            'patient.user',
            'doctor.user',
            'doctor.hospital',
            'teleconsultationHospital'
        ])->find($id);

        if (!$consultation) {
            return response()->json(['status' => 'error', 'message' => 'Consultation introuvable'], 404);
        }

        if (empty($consultation->call_channel)) {
            $consultation->call_channel = 'teleconsult_sch_' . $consultation->id . '_' . \Illuminate\Support\Str::random(10);
        }

        $consultation->is_call_active = true;
        $consultation->call_status = 'calling';
        $consultation->call_started_at = now();
        $consultation->save();

        $infName = trim(($user->name ?? '') . ' ' . ($user->prenom ?? ''));
        $token = \App\Services\LiveKitTokenService::generateToken(
            $consultation->call_channel,
            'infirmier_' . $user->id,
            'Inf. ' . $infName
        );
        $livekitUrl = config('services.livekit.ws_url', env('LIVEKIT_WS_URL', 'wss://sante-13e5q71k.livekit.cloud'));

        $doctorUser = optional(optional($consultation->doctor)->user);
        $doctorName = trim(($doctorUser->name ?? '') . ' ' . ($doctorUser->prenom ?? ''));
        if (empty($doctorName)) {
            $doctorName = 'Médecin assigné';
        }

        $patientUser = optional(optional($consultation->patient)->user);
        $patientName = trim(($patientUser->name ?? '') . ' ' . ($patientUser->prenom ?? ''));
        if (empty($patientName)) {
            $patientName = 'Patient';
        }

        return response()->json([
            'status' => 'success',
            'consultation_id' => $consultation->id,
            'channel' => $consultation->call_channel,
            'token' => $token,
            'livekit_url' => $livekitUrl,
            'doctor_name' => $doctorName,
            'patient_name' => $patientName,
            'message' => 'Appel vidéo démarré avec succès'
        ]);
    }
}