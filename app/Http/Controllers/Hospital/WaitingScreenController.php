<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\PatientCall;
use App\Models\Hospital;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaitingScreenController extends Controller
{
    /**
     * Obtenir l'hôpital de l'utilisateur connecté
     */
    protected function getHospital()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        if ($user->hospital) {
            return $user->hospital;
        }

        if ($user->hospital_id) {
            return Hospital::find($user->hospital_id);
        }

        if ($user->doctor && $user->doctor->hospital_id) {
            return Hospital::find($user->doctor->hospital_id);
        }

        return null;
    }

    /**
     * Trouver un hôpital par son TV Token
     */
    protected function getHospitalByToken($token)
    {
        if (empty($token)) {
            return null;
        }
        return Hospital::where('tv_token', $token)->first();
    }

    /**
     * Affichage plein écran de la salle d'attente (Session connectée)
     */
    public function screen(Request $request)
    {
        $hospital = $this->getHospital();
        if (!$hospital) {
            abort(403, 'Hôpital non identifié.');
        }

        $token = $hospital->getOrGenerateTvToken();

        $recentCalls = PatientCall::where('hospital_id', $hospital->id)
            ->whereDate('called_at', Carbon::today())
            ->orderByDesc('called_at')
            ->take(6)
            ->get();

        // Le patient affiché en grand est celui qui est actuellement appelé (status = calling)
        $currentCall = PatientCall::where('hospital_id', $hospital->id)
            ->whereDate('called_at', Carbon::today())
            ->where('status', 'calling')
            ->orderByDesc('called_at')
            ->first();

        $isPublic = false;

        return view('users.hospital.waiting_screen', compact('hospital', 'recentCalls', 'currentCall', 'token', 'isPublic'));
    }

    /**
     * Affichage public de la salle d'attente pour TV Android (Sans mot de passe)
     */
    public function publicScreen($token)
    {
        $hospital = $this->getHospitalByToken($token);
        if (!$hospital) {
            abort(404, 'Écran de salle d\'attente introuvable ou lien expiré.');
        }

        $recentCalls = PatientCall::where('hospital_id', $hospital->id)
            ->whereDate('called_at', Carbon::today())
            ->orderByDesc('called_at')
            ->take(6)
            ->get();

        // Le patient affiché en grand est celui qui est actuellement appelé (status = calling)
        $currentCall = PatientCall::where('hospital_id', $hospital->id)
            ->whereDate('called_at', Carbon::today())
            ->where('status', 'calling')
            ->orderByDesc('called_at')
            ->first();

        $isPublic = true;

        return view('users.hospital.waiting_screen', compact('hospital', 'recentCalls', 'currentCall', 'token', 'isPublic'));
    }

    /**
     * Endpoint API de mise à jour temps réel (Session connectée)
     */
    public function getUpdates(Request $request)
    {
        $hospital = $this->getHospital();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hôpital non trouvé.'], 403);
        }

        return $this->formatUpdatesResponse($hospital, (int) $request->input('last_id', 0));
    }

    /**
     * Endpoint API de mise à jour temps réel (Public TV Android via token)
     */
    public function getPublicUpdates(Request $request, $token)
    {
        $hospital = $this->getHospitalByToken($token);
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hôpital non trouvé.'], 404);
        }

        return $this->formatUpdatesResponse($hospital, (int) $request->input('last_id', 0));
    }

    /**
     * Formater la réponse des mises à jour
     */
    protected function formatUpdatesResponse(Hospital $hospital, int $lastId)
    {
        $today = Carbon::today();

        // Récupérer UNIQUEMENT les nouveaux appels survenus après lastId
        if ($lastId > 0) {
            $newCalls = PatientCall::where('hospital_id', $hospital->id)
                ->whereDate('called_at', $today)
                ->where('status', 'calling')
                ->where('id', '>', $lastId)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            // Au premier chargement de la page (lastId = 0), ne pas rejouer les anciens appels passés
            $newCalls = collect([]);
        }

        // Dernier appel actif en cours (uniquement status calling)
        $currentCall = PatientCall::where('hospital_id', $hospital->id)
            ->whereDate('called_at', $today)
            ->where('status', 'calling')
            ->orderByDesc('called_at')
            ->first();

        // Dernier ID enregistré de la journée pour le suivi du polling
        $latestRecord = PatientCall::where('hospital_id', $hospital->id)
            ->whereDate('called_at', $today)
            ->orderByDesc('id')
            ->first();

        // Liste des 6 derniers appels du jour
        $recentCalls = PatientCall::where('hospital_id', $hospital->id)
            ->whereDate('called_at', $today)
            ->orderByDesc('called_at')
            ->take(6)
            ->get()
            ->map(function ($call) {
                return [
                    'id' => $call->id,
                    'patient_name' => $call->patient_name,
                    'doctor_name' => $call->doctor_name,
                    'cabinet' => $call->cabinet ?? 'Cabinet de consultation',
                    'service_name' => $call->service_name ?? 'Consultation',
                    'status' => $call->status,
                    'time' => $call->called_at ? $call->called_at->format('H:i') : $call->created_at->format('H:i'),
                ];
            });

        // Formater les nouveaux appels pour la synthèse vocale
        $formattedNewCalls = $newCalls->map(function ($call) {
            return [
                'id' => $call->id,
                'patient_name' => $call->patient_name,
                'doctor_name' => $call->doctor_name,
                'cabinet' => $call->cabinet ?? 'Cabinet de consultation',
                'service_name' => $call->service_name ?? 'Consultation',
                'status' => $call->status,
                'time' => $call->called_at ? $call->called_at->format('H:i') : $call->created_at->format('H:i'),
                'spoken_text' => 'Le patient ' . $call->patient_name . ' est attendu par ' . $call->doctor_name . ' au ' . ($call->cabinet ?: 'cabinet de consultation') . '.',
            ];
        });

        return response()->json([
            'success' => true,
            'hospital_name' => $hospital->label ?? 'Hôpital',
            'latest_id' => $latestRecord ? $latestRecord->id : $lastId,
            'new_calls' => $formattedNewCalls,
            'current_call' => $currentCall ? [
                'id' => $currentCall->id,
                'patient_name' => $currentCall->patient_name,
                'doctor_name' => $currentCall->doctor_name,
                'cabinet' => $currentCall->cabinet ?? 'Cabinet de consultation',
                'service_name' => $currentCall->service_name ?? 'Consultation',
                'status' => $currentCall->status,
                'time' => $currentCall->called_at ? $currentCall->called_at->format('H:i') : $currentCall->created_at->format('H:i'),
            ] : null,
            'recent_calls' => $recentCalls,
            'server_time' => Carbon::now()->format('H:i:s'),
            'server_date' => Carbon::now()->translatedFormat('l d F Y'),
        ]);
    }

    /**
     * Test d'appel direct depuis la page
     */
    public function testCall(Request $request)
    {
        $hospital = $this->getHospital();
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hôpital non trouvé.'], 403);
        }

        return $this->createTestCallRecord($hospital, $request);
    }

    /**
     * Test d'appel direct public via Token
     */
    public function publicTestCall(Request $request, $token)
    {
        $hospital = $this->getHospitalByToken($token);
        if (!$hospital) {
            return response()->json(['success' => false, 'message' => 'Hôpital non trouvé.'], 404);
        }

        return $this->createTestCallRecord($hospital, $request);
    }

    /**
     * Créer un enregistrement de test
     */
    protected function createTestCallRecord(Hospital $hospital, Request $request)
    {
        // Clôturer d'anciens appels de test précédents
        PatientCall::where('hospital_id', $hospital->id)
            ->whereNull('consultation_id')
            ->where('status', 'calling')
            ->update(['status' => 'completed']);

        $patientName = $request->input('patient_name', 'M. KOUASSI YAO JEAN');
        $doctorName = $request->input('doctor_name', 'Dr. KONAN KOUASSI');
        $cabinet = $request->input('cabinet', 'Cabinet 01 - Consultation Générale');

        $call = PatientCall::create([
            'hospital_id' => $hospital->id,
            'patient_name' => $patientName,
            'doctor_name' => $doctorName,
            'cabinet' => $cabinet,
            'service_name' => 'Consultation Médicale (Test)',
            'status' => 'calling',
            'called_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appel de test généré avec succès.',
            'call' => $call,
        ]);
    }

    /**
     * Génération / Proxy de flux audio MP3 TTS français naturel (Haute Disponibilité)
     */
    public function getTtsAudio(Request $request)
    {
        $text = trim($request->input('text', ''));
        if (empty($text)) {
            return response()->json(['error' => 'Texte manquant'], 400);
        }

        $text = mb_substr($text, 0, 300);

        try {
            $url = "https://translate.google.com/translate_tts?ie=UTF-8&client=tw-ob&tl=fr-FR&q=" . urlencode($text);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Referer: https://translate.google.com/'
            ]);
            $audioData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && !empty($audioData)) {
                return response($audioData, 200, [
                    'Content-Type' => 'audio/mpeg',
                    'Content-Length' => strlen($audioData),
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        } catch (\Exception $e) {
            // En cas d'erreur réseau, renvoyer 500 pour bascule SpeechSynthesis client
        }

        return response()->json(['error' => 'TTS non disponible'], 500);
    }
}
