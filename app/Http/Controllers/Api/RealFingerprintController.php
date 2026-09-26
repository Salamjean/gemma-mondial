<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\RealTimeFingerprintCapture;
use App\Services\FingerprintScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RealFingerprintController extends Controller
{
    protected $scannerService;

    public function __construct(FingerprintScannerService $scannerService)
    {
        $this->scannerService = $scannerService;
        $this->middleware('auth:sanctum');
    }

    public function startCapture(Request $request)
    {
        $request->validate([
            'finger' => 'required|in:left_index,right_index',
            'patient_id' => 'nullable|exists:patients,id'
        ]);

        $user = Auth::user();
        $finger = $request->finger;
        $patientId = $request->patient_id;

        try {
            // Événement 1: Début de capture
            broadcast(new RealTimeFingerprintCapture('capture_started', [
                'finger' => $finger,
                'message' => 'Placez votre doigt sur le scanner',
                'patient_id' => $patientId,
                'user_name' => $user->name
            ], $user->id))->toOthers();

            // Vérifier la connexion du scanner
            if (!$this->scannerService->isConnected()) {
                throw new \Exception('Scanner physique non détecté. Vérifiez la connexion USB.');
            }

            // Événement 2: Scanner prêt
            broadcast(new RealTimeFingerprintCapture('scanner_ready', [
                'finger' => $finger,
                'message' => 'Scanner prêt - attente du doigt',
                'scanner_type' => $this->scannerService->getScannerType()
            ], $user->id))->toOthers();

            // Délai pour laisser l'utilisateur placer son doigt
            sleep(2);

            // Événement 3: Doigt détecté
            broadcast(new RealTimeFingerprintCapture('finger_detected', [
                'finger' => $finger,
                'message' => 'Doigt détecté - capture en cours'
            ], $user->id))->toOthers();

            // Capture réelle
            $result = $this->scannerService->capture($finger, $user->id);
            
            // Ajouter l'ID patient si fourni
            if ($patientId) {
                $result['patient_id'] = $patientId;
            }

            // Événement 4: Capture complète
            broadcast(new RealTimeFingerprintCapture('capture_complete', $result, $user->id))->toOthers();

            // Sauvegarder dans la session pour le formulaire
            if ($patientId) {
                session()->put("fingerprint_{$finger}_patient_{$patientId}", $result);
            }

            return response()->json([
                'success' => true,
                'message' => 'Capture réussie',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Real capture failed: ' . $e->getMessage());
            
            // Événement d'erreur
            broadcast(new RealTimeFingerprintCapture('capture_error', [
                'finger' => $finger,
                'message' => 'Erreur: ' . $e->getMessage(),
                'error' => true
            ], $user->id))->toOthers();

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'fallback_to_simulation' => true
            ], 500);
        }
    }

    public function checkScannerStatus()
    {
        $connected = $this->scannerService->isConnected();
        $scannerType = $this->scannerService->getScannerType();
        
        return response()->json([
            'connected' => $connected,
            'scanner_type' => $scannerType,
            'platform' => PHP_OS,
            'timestamp' => now()->toISOString()
        ]);
    }

    public function getPusherConfig()
    {
        $user = Auth::user();
        
        return response()->json([
            'pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'encrypted' => true,
                'authEndpoint' => '/broadcasting/auth'
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'channel' => 'private-fingerprint.realtime.' . $user->id
            ],
            'scanner' => [
                'connected' => $this->scannerService->isConnected(),
                'type' => $this->scannerService->getScannerType()
            ]
        ]);
    }

    public function saveToPatient(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'fingerprint_data' => 'required|array',
            'finger' => 'required|in:left_index,right_index'
        ]);

        $patient = \App\Models\Patient::find($request->patient_id);
        
        // Mettre à jour les champs d'empreintes
        $finger = $request->finger;
        $data = $request->fingerprint_data;
        
        $updateData = [
            'fingerprint_' . $finger . '_template' => $data['template'],
            'fingerprint_' . $finger . '_image' => $data['image'],
            'fingerprint_device' => $data['scanner'] ?? 'DigitalPersona U.are.U',
            'fingerprint_captured_at' => now(),
            'fingerprint_verified' => true,
            'fingerprint_quality_' . $finger => $data['quality'] ?? 0
        ];
        
        $patient->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Empreinte sauvegardée',
            'patient' => $patient->only(['id', 'code_patient', 'name', 'prenom'])
        ]);
    }
}

