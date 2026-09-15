<?php

namespace App\Http\Controllers\api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\PatientRequest;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\SubPrefecture;
use App\Models\User;
use App\Repositories\Patient\PatientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class DataController extends Controller
{
    public function createRendezVous(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'heure' => 'required|string',
            'motif' => 'required|string',
            'doctor_id' => 'required|integer|exists:doctors,id',
            'notes' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $patient = Auth::user()->patient;

            // Créer le rendez-vous
            $rendezVous = new RendezVous();
            $rendezVous->title = $request->title;
            $rendezVous->date = $request->date;
            $rendezVous->patient_id = $patient->id;
            $rendezVous->doctor_id = $request->doctor_id;
            $rendezVous->status = 'pending';

            // Gérer l'image si fournie
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/rendez-vous', $imageName);
                $rendezVous->image = $imageName;
            }

            // Sauvegarder les données supplémentaires dans un champ JSON
            $rendezVous->details = json_encode([
                'heure' => $request->heure,
                'motif' => $request->motif,
                'notes' => $request->notes,
                'duree' => $request->duree ?? '30 minutes',
            ]);

            $rendezVous->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Rendez-vous créé avec succès',
                'rendez_vous' => $rendezVous
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création du rendez-vous: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDoctors()
    {
        try {
            // Jointure entre users et doctors
            $doctors = User::select(
                'users.id',
                'users.name',
                'users.prenom',
                'users.email',
                'doctors.contact as telephone',
                'doctors.img_url as photo',
                'doctors.type_name as specialite',
                'doctors.service_hospital_id'
            )
                ->join('doctors', 'users.id', '=', 'doctors.user_id')
                ->where('users.role_as', 'doctor')
                ->orderBy('users.name')
                ->get()
                ->map(function ($doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => 'Dr. ' . trim($doctor->name . ' ' . $doctor->prenom),
                        'specialite' => $doctor->specialite ?? 'Médecin Généraliste',
                        'email' => $doctor->email ?? 'Non spécifié',
                        'telephone' => $doctor->telephone ?? 'Non spécifié',
                        'photo' => $doctor->photo ? asset('assets/uploads/doctor/' . $doctor->photo) : null,
                        'service_hospital_id' => $doctor->service_hospital_id,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'doctors' => $doctors,
                'total' => $doctors->count(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('ERREUR getDoctors: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Erreur serveur'], 500);
        }
    }
    public function instance()
    {
        return new PatientRepository();
    }

    public function show()
    {
        return response(['patient' => Patient::where('id', Auth::user()->patient->id)->with('user', 'habitualResidence', 'currentResidence', 'lieuNaissance', 'hospital')->first()], 200);
    }

    public function cities()
    {
        return response(['cities' => SubPrefecture::all()], 200);
    }

    public function update(PatientRequest $request)
    {
        $request->validated();


        $res = $this->instance()->update($request);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status'], 'message' => $res['message']], 400);

        return response()->json(['status' => $res['status'], 'message' => $res['message']], 200);
    }

    public function consultations()
    {
        return response(['consultations' => $this->instance()->consultations()], 200);
    }

    public function declarations()
    {
        return response(['declarations' => $this->instance()->declarations()], 200);
    }

    public function rendezVous()
    {
        try {
            Log::info('=== DEBUT rendezVous() ===');

            $authUser = Auth::user();

            if (!$authUser) {
                return response(['error' => 'Non authentifié'], 401);
            }

            $patient = $authUser->patient;

            if (!$patient) {
                return response(['error' => 'Patient non trouvé'], 404);
            }

            $patientId = $patient->id;
            Log::info("Patient ID récupéré: {$patientId}");

            // CORRECTION: Enlever 'd.specialite' qui n'existe pas
            $rendezVous = DB::table('rendez_vouses as r')
                ->select(
                    'r.id',
                    'r.title',
                    'r.date',
                    'r.heure',
                    'r.motif',
                    'r.image',
                    'r.status',
                    'r.details',
                    'r.doctor_id',
                    'r.created_at',
                    'r.updated_at',
                    // Infos du médecin depuis users
                    'u.name as doctor_name',
                    'u.prenom as doctor_prenom',
                    'u.email as doctor_email',
                    // Infos du médecin depuis doctors
                    'd.contact as doctor_telephone',
                    'd.img_url as doctor_photo',
                    'd.type_name as doctor_type',
                    'd.type_doctor_id', // si vous avez besoin
                    'd.service_hospital_id' // si vous avez besoin
                    // 'd.specialite' n'existe pas - supprimé
                )
                ->leftJoin('users as u', function ($join) {
                    $join->on('r.doctor_id', '=', 'u.id')
                        ->where('u.role_as', 'doctor');
                })
                ->leftJoin('doctors as d', 'u.id', '=', 'd.user_id')
                ->where('r.patient_id', $patientId)
                // ->where('r.delete', 0) // si vous avez cette colonne
                ->orderBy('r.date', 'desc')
                ->orderBy('r.heure', 'desc')
                ->get()
                ->map(function ($rdv) {
                    // Gérer les détails
                    $details = [];
                    if (is_string($rdv->details)) {
                        $details = json_decode($rdv->details, true) ?? [];
                    } elseif (is_array($rdv->details)) {
                        $details = $rdv->details;
                    }

                    // Nom complet du médecin
                    $doctorFullName = 'Médecin non spécifié';
                    if ($rdv->doctor_name && $rdv->doctor_prenom) {
                        $doctorFullName = 'Dr. ' . trim($rdv->doctor_name . ' ' . $rdv->doctor_prenom);
                    } elseif ($rdv->doctor_name) {
                        $doctorFullName = 'Dr. ' . $rdv->doctor_name;
                    }

                    // CORRECTION: Utiliser type_name comme spécialité
                    $specialite = $rdv->doctor_type ?? 'Médecin Généraliste';

                    // Photo
                    $photo = $rdv->doctor_photo ? asset('assets/uploads/doctor/' . $rdv->doctor_photo) : null;

                    return [
                        'id' => $rdv->id,
                        'title' => $rdv->title,
                        'date' => $rdv->date,
                        'heure' => $rdv->heure ?? ($details['heure'] ?? null),
                        'motif' => $rdv->motif ?? ($details['motif'] ?? 'Non spécifié'),
                        'notes' => $details['notes'] ?? null,
                        'image' => $rdv->image ? asset('storage/' . $rdv->image) : null,
                        'status' => $rdv->status,
                        'doctor_id' => $rdv->doctor_id,
                        'doctor_name' => $doctorFullName,
                        'doctor_specialite' => $specialite,
                        'doctor_photo' => $photo,
                        'doctor_email' => $rdv->doctor_email,
                        'doctor_telephone' => $rdv->doctor_telephone,
                        'doctor_type' => $rdv->doctor_type,
                        'service_hospital_id' => $rdv->service_hospital_id,
                        'created_at' => date('Y-m-d H:i:s', strtotime($rdv->created_at)),
                        'updated_at' => date('Y-m-d H:i:s', strtotime($rdv->updated_at)),
                        'details' => $details,
                    ];
                });

            Log::info('Nombre de rendez-vous trouvés: ' . $rendezVous->count());
            Log::info('=== FIN rendezVous() ===');

            return response([
                'success' => true,
                'rdv' => $rendezVous,
                'count' => $rendezVous->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('ERREUR dans rendezVous(): ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return response([
                'success' => false,
                'message' => 'Erreur lors de la récupération des rendez-vous',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne'
            ], 500);
        }
    }

    public function deleteRendezVous($id)
    {

        $rdv = RendezVous::find($id);
        if ($rdv)
            $rdv->delete();
        else
            return response(['message' => 'Rendez vous introuvable'], 403);

        return response(['message' => 'Rendez vous supprimé'], 200);
    }

    public function checkActiveCall()
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['has_active_call' => false], 200);
            }

            $activeConsultation = \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('is_call_active', 1)
                ->latest('updated_at')
                ->first();

            if (!$activeConsultation) {
                return response()->json(['has_active_call' => false], 200);
            }

            // Expiration automatique de l'appel après 30 secondes si aucun médecin n'a décroché
            if (is_null($activeConsultation->doctor_id)) {
                $startedAt = $activeConsultation->call_started_at ?? $activeConsultation->created_at;
                if ($startedAt && now()->diffInSeconds($startedAt) >= 30) {
                    $activeConsultation->update([
                        'is_call_active' => false,
                        'call_status' => 'missed',
                        'call_ended_at' => now(),
                        'missed_count' => DB::raw('COALESCE(missed_count, 0) + 1'),
                    ]);
                    return response()->json(['has_active_call' => false, 'message' => 'Aucun médecin disponible dans le délai de 30s.'], 200);
                }
            }

            $doctorUser = optional(optional($activeConsultation->doctor)->user);
            $docNameTrimmed = trim(($doctorUser->name ?? '') . ' ' . ($doctorUser->prenom ?? ''));
            $doctorName = $docNameTrimmed ? ('Dr. ' . $docNameTrimmed) : "Recherche d'un médecin disponible...";
            
            $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
            $patientName = optional(Auth::user())->name ?? 'Patient';
            $token = \App\Services\LiveKitTokenService::generateToken($activeConsultation->call_channel, 'patient_' . $patient->id, $patientName);

            return response()->json([
                'has_active_call' => true,
                'call' => [
                    'consultation_id' => $activeConsultation->id,
                    'channel' => $activeConsultation->call_channel,
                    'livekit_url' => $livekitUrl,
                    'token' => $token,
                    'doctor_name' => $doctorName,
                    'doctor_id' => $activeConsultation->doctor_id,
                    'call_status' => $activeConsultation->call_status,
                    'prestation' => optional(optional($activeConsultation->prestationHospital)->prestationService)->libelle ?? 'Consultation',
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['has_active_call' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function acceptCall(Request $request)
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Patient non trouvé'], 404);
            }

            $activeConsultation = \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('is_call_active', 1)
                ->first();

            if ($activeConsultation) {
                $activeConsultation->update(['call_status' => 'accepted']);
            }

            return response()->json(['status' => 'success', 'message' => 'Appel accepté']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function rejectCall(Request $request)
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Patient non trouvé'], 404);
            }

            $activeConsultation = \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('is_call_active', 1)
                ->first();

            if ($activeConsultation) {
                $activeConsultation->update([
                    'is_call_active' => false,
                    'call_status' => 'rejected'
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Appel refusé']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function endCall(Request $request)
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Patient non trouvé'], 404);
            }

            $reason = $request->input('reason', 'ended');
            $duration = (int) $request->input('duration', 0);

            $activeConsultation = \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('is_call_active', 1)
                ->first();

            if ($activeConsultation) {
                $updateData = [
                    'is_call_active' => false,
                    'call_status' => $reason,
                    'call_ended_at' => now(),
                ];
                if ($duration > 0) {
                    $updateData['call_duration'] = $duration;
                }
                $activeConsultation->update($updateData);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Appel terminé avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function callHistory()
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Patient non trouvé'], 404);
            }

            $calls = \App\Models\Consultation::where('patient_id', $patient->id)
                ->where(function ($q) {
                    $q->whereNotNull('call_started_at')
                      ->orWhereNotNull('call_ended_at')
                      ->orWhereIn('call_status', ['ended', 'rejected', 'patient_left', 'accepted']);
                })
                ->with(['doctor.user', 'prestationHospital.prestationService'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($consultation) {
                    $doctorUser = optional(optional($consultation->doctor)->user);
                    $doctorName = 'Dr. ' . trim(($doctorUser->name ?? '') . ' ' . ($doctorUser->prenom ?? ''));
                    $doctorPhoto = optional($consultation->doctor)->img_url 
                        ? asset('assets/uploads/doctor/' . $consultation->doctor->img_url) 
                        : null;
                    $specialite = optional($consultation->doctor)->type_name ?? 'Médecin Généraliste';

                    return [
                        'id' => $consultation->id,
                        'doctor_name' => $doctorName,
                        'doctor_specialite' => $specialite,
                        'doctor_photo' => $doctorPhoto,
                        'prestation' => optional(optional($consultation->prestationHospital)->prestationService)->libelle ?? 'Consultation',
                        'call_status' => $consultation->call_status ?? 'ended',
                        'call_duration' => (int) ($consultation->call_duration ?? 0),
                        'started_at' => $consultation->call_started_at ? date('Y-m-d H:i', strtotime($consultation->call_started_at)) : date('Y-m-d H:i', strtotime($consultation->created_at)),
                        'ended_at' => $consultation->call_ended_at ? date('Y-m-d H:i', strtotime($consultation->call_ended_at)) : null,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'calls' => $calls,
                'total' => $calls->count(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getServices()
    {
        try {
            // Récupérer les prestations/services disponibles pour téléconsultation
            $prestations = \App\Models\PrestationHospital::with(['prestationService.service', 'serviceHospital.service'])
                ->get()
                ->map(function ($ph) {
                    $libelle = optional($ph->prestationService)->libelle 
                        ?? optional(optional($ph->serviceHospital)->service)->libelle 
                        ?? 'Consultation Générale';

                    $serviceName = optional(optional(optional($ph->prestationService)->service))->libelle 
                        ?? optional(optional($ph->serviceHospital)->service)->libelle 
                        ?? 'Médecine Générale';

                    $prix = (int) ($ph->prix ?? 1000);

                    return [
                        'id' => $ph->id,
                        'prestation_hospital_id' => $ph->id,
                        'libelle' => $libelle,
                        'service' => $serviceName,
                        'prix' => $prix,
                        'montant' => $prix,
                    ];
                });

            // Si aucune prestation d'hôpital n'est encore configurée, utiliser la table Services
            if ($prestations->isEmpty()) {
                $prestations = \App\Models\Service::all()->map(function ($s) {
                    return [
                        'id' => $s->id,
                        'prestation_hospital_id' => $s->id,
                        'libelle' => $s->libelle,
                        'service' => $s->libelle,
                        'prix' => 1000,
                        'montant' => 1000,
                    ];
                });
            }

            return response()->json([
                'status' => 'success',
                'services' => $prestations,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getPendingPaidConsultation()
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['has_pending_paid' => false], 200);
            }

            $paidConsultation = \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('status', 0)
                ->where('montant', '>=', 100)
                ->where('call_status', '!=', 'payment_pending')
                ->whereNotIn('call_status', ['ended', 'completed', 'doctor_ended', 'patient_left'])
                ->latest('updated_at')
                ->first();

            if (!$paidConsultation) {
                return response()->json(['has_pending_paid' => false], 200);
            }

            return response()->json([
                'has_pending_paid' => true,
                'consultation' => [
                    'id' => $paidConsultation->id,
                    'motif' => $paidConsultation->motif_consultation ?? 'Consultation médicale',
                    'montant' => (int) $paidConsultation->montant,
                    'prestation_hospital_id' => $paidConsultation->prestation_hospital_id,
                    'created_at' => $paidConsultation->created_at ? $paidConsultation->created_at->format('Y-m-d H:i') : null,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['has_pending_paid' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function requestOnlineConsultation(Request $request)
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Patient non trouvé'], 404);
            }

            $livekitUrl = config('services.livekit.url', 'wss://gemma-14fckk2m.livekit.cloud');
            $patientName = trim((Auth::user()->name ?? '') . ' ' . (Auth::user()->prenom ?? '')) ?: 'Patient';

            // 1. Vérifier si le patient a DÉJÀ une consultation RÉELLEMENT payée (non en attente de paiement)
            $existingPaid = \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('status', 0)
                ->where('montant', '>=', 100)
                ->where('call_status', '!=', 'payment_pending')
                ->whereNotIn('call_status', ['ended', 'completed', 'doctor_ended', 'patient_left'])
                ->latest('updated_at')
                ->first();

            if ($existingPaid) {
                $channel = $existingPaid->call_channel ?: ('online_consultation_' . $patient->id . '_' . time());
                $existingPaid->update([
                    'is_call_active' => true,
                    'call_status' => 'calling',
                    'call_started_at' => now(),
                    'call_channel' => $channel,
                ]);

                $token = \App\Services\LiveKitTokenService::generateToken($channel, 'patient_' . $patient->id, $patientName);

                return response()->json([
                    'status' => 'success',
                    'already_paid' => true,
                    'consultation_id' => $existingPaid->id,
                    'channel' => $channel,
                    'livekit_url' => $livekitUrl,
                    'token' => $token,
                    'service' => $existingPaid->motif_consultation ?? 'Consultation médicale',
                    'prestation_hospital_id' => $existingPaid->prestation_hospital_id,
                    'montant' => (int) $existingPaid->montant,
                    'message' => 'Votre consultation a déjà été réglée. L\'appel vers les médecins a été relancé.'
                ], 200);
            }

            $prestationHospitalId = $request->input('prestation_hospital_id') 
                ?? $request->input('service_id') 
                ?? $request->input('prestation_id');

            $motif = $request->input('motif');
            $ph = null;
            if ($prestationHospitalId) {
                $ph = \App\Models\PrestationHospital::with(['prestationService.service', 'serviceHospital.service'])->find($prestationHospitalId);
                if ($ph && !$motif) {
                    $motif = optional($ph->prestationService)->libelle 
                        ?? optional(optional($ph->serviceHospital)->service)->libelle;
                } else if (!$motif) {
                    $serv = \App\Models\Service::find($prestationHospitalId);
                    if ($serv) {
                        $motif = $serv->libelle;
                    }
                }
            }

            if (!$motif) {
                $motif = 'Téléconsultation en ligne';
            }

            $servicePrice = $ph ? (int) ($ph->prix ?? 1000) : 1000;
            $amount = (int) $request->input('amount', $servicePrice);
            if ($amount <= 0) {
                $amount = $servicePrice;
            }

            // Nettoyer les anciens essais de paiement non aboutis (unpaid)
            \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('call_status', 'payment_pending')
                ->delete();

            // Clore tout ancien appel actif non terminé pour ce patient
            \App\Models\Consultation::where('patient_id', $patient->id)
                ->where('is_call_active', 1)
                ->update([
                    'is_call_active' => false,
                    'call_status' => 'cancelled',
                    'call_ended_at' => now(),
                ]);

            $channel = 'online_consultation_' . $patient->id . '_' . time();
            $consultation = \App\Models\Consultation::create([
                'patient_id' => $patient->id,
                'doctor_id' => null, // Non assigné pour le moment (tous les médecins disponibles recevront l'appel après paiement)
                'status' => 0,
                'is_call_active' => false, // Ne pas activer l'appel avant la validation effective du paiement
                'call_status' => 'payment_pending',
                'call_channel' => $channel,
                'call_started_at' => null,
                'date_consultation' => now(),
                'admission_id' => null,
                'prestation_hospital_id' => $prestationHospitalId,
                'motif_consultation' => $motif,
                'hospital_id' => $patient->hospital_id ?? 1,
                'montant' => $amount,
            ]);

            $token = \App\Services\LiveKitTokenService::generateToken($channel, 'patient_' . $patient->id, $patientName);

            // Génération de la session de paiement Wave Checkout API
            $waveLaunchUrl = null;
            $waveSessionId = null;
            $paymentMethod = strtolower($request->input('payment_method', 'wave'));
            $waveApiKey = env('WAVE_API_KEY') ?? config('services.wave.api_key');

            if ($paymentMethod === 'wave' && $waveApiKey) {
                try {
                    $client = new \GuzzleHttp\Client();
                    $appUrl = config('app.url', 'https://gemma.app');
                    if (!str_starts_with($appUrl, 'https://')) {
                        $appUrl = 'https://gemma.app';
                    }
                    $response = $client->post('https://api.wave.com/v1/checkout/sessions', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $waveApiKey,
                            'Content-Type'  => 'application/json',
                        ],
                        'json' => [
                            'amount'           => (string) $amount,
                            'currency'         => 'XOF',
                            'error_url'        => $appUrl . '/dashboard?payment=failed',
                            'success_url'      => $appUrl . '/dashboard?payment=success',
                            'client_reference' => 'CONSULTATION_' . $consultation->id,
                        ],
                        'verify' => false,
                        'http_errors' => false,
                        'timeout' => 10,
                    ]);

                    if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                        $waveData = json_decode($response->getBody()->getContents(), true);
                        $waveSessionId = $waveData['id'] ?? null;
                        $waveLaunchUrl = $waveData['wave_launch_url'] ?? $waveData['wave_checkout_url'] ?? null;
                    }
                } catch (\Throwable $wErr) {
                    \Illuminate\Support\Facades\Log::warning("Wave API Checkout Exception: " . $wErr->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'consultation_id' => $consultation->id,
                'channel' => $channel,
                'livekit_url' => $livekitUrl,
                'token' => $token,
                'service' => $motif,
                'wave_session_id' => $waveSessionId,
                'wave_launch_url' => $waveLaunchUrl,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function verifyWavePayment(Request $request, $id)
    {
        try {
            $consultation = \App\Models\Consultation::find($id);
            if (!$consultation) {
                return response()->json(['paid' => false, 'message' => 'Consultation non trouvée'], 404);
            }

            // Si déjà validée et active
            if ($consultation->is_call_active && $consultation->montant >= 100) {
                return response()->json(['paid' => true, 'status' => 'succeeded', 'consultation_id' => $consultation->id]);
            }

            $sessionId = $request->query('session_id');
            $waveApiKey = env('WAVE_API_KEY') ?? config('services.wave.api_key');

            if ($sessionId && $waveApiKey) {
                try {
                    $client = new \GuzzleHttp\Client();
                    $response = $client->get("https://api.wave.com/v1/checkout/sessions/{$sessionId}", [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $waveApiKey,
                            'Accept'        => 'application/json',
                        ],
                        'verify' => false,
                        'http_errors' => false,
                        'timeout' => 8,
                    ]);

                    if ($response->getStatusCode() === 200) {
                        $waveData = json_decode($response->getBody()->getContents(), true);
                        $paymentStatus = strtolower($waveData['payment_status'] ?? '');
                        $checkoutStatus = strtolower($waveData['checkout_status'] ?? '');

                        if ($paymentStatus === 'succeeded' || $checkoutStatus === 'complete') {
                            $consultation->update([
                                'montant' => (int) ($waveData['amount'] ?? $consultation->montant),
                                'is_call_active' => true,
                                'call_status' => 'calling',
                                'call_started_at' => now(),
                            ]);
                            return response()->json(['paid' => true, 'status' => 'succeeded', 'consultation_id' => $consultation->id]);
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Wave Payment Verification Exception: " . $e->getMessage());
                }
            }

            // Vérification fallback dans la base de données
            $isPaid = ($consultation->is_call_active && $consultation->montant > 0);
            return response()->json([
                'paid' => $isPaid,
                'status' => $isPaid ? 'succeeded' : 'pending_payment',
                'consultation_id' => $consultation->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['paid' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function waveWebhook(Request $request)
    {
        try {
            $data = $request->all();
            $event = $data['type'] ?? null;

            if ($event === 'checkout.session.completed') {
                $session = $data['data'] ?? [];
                $clientRef = $session['client_reference'] ?? '';
                if (str_starts_with($clientRef, 'CONSULTATION_')) {
                    $consultationId = (int) str_replace('CONSULTATION_', '', $clientRef);
                    $consultation = \App\Models\Consultation::find($consultationId);
                    if ($consultation) {
                        $consultation->update([
                            'montant' => (int) ($session['amount'] ?? 100),
                            'is_call_active' => true,
                            'call_status' => 'calling',
                            'call_started_at' => now(),
                        ]);
                    }
                }
            }
            return response()->json(['status' => 'received'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function checkOnlineConsultationStatus($id)
    {
        try {
            $consultation = \App\Models\Consultation::findOrFail($id);
            $assigned = !is_null($consultation->doctor_id);
            $doctorName = null;

            if ($assigned && $consultation->doctor) {
                $doctorUser = $consultation->doctor->user;
                $doctorName = 'Dr. ' . trim(($doctorUser->name ?? '') . ' ' . ($doctorUser->prenom ?? ''));
            }

            return response()->json([
                'consultation_id' => $consultation->id,
                'is_call_active' => (bool)$consultation->is_call_active,
                'call_status' => $consultation->call_status,
                'assigned' => $assigned,
                'doctor_name' => $doctorName,
                'channel' => $consultation->call_channel,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
