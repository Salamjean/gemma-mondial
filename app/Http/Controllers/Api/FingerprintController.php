<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\FingerprintCaptureEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FingerprintController extends Controller
{
    public function authenticate(Request $request)
    {
        $request->validate([
            'socket_id' => 'required|string',
            'channel_name' => 'required|string'
        ]);

        // Vérifier que l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Autoriser uniquement les canaux fingerprint
        if (!str_starts_with($request->channel_name, 'private-fingerprint.user.')) {
            return response()->json(['error' => 'Invalid channel'], 403);
        }

        // Utiliser l'authentification intégrée de Laravel
        return response()->json([
            'auth' => $request->channel_name . ':' . $request->socket_id,
        ]);
    }

    public function capture(Request $request)
    {
        $request->validate([
            'finger' => 'required|in:left_index,right_index',
        ]);

        $user = Auth::user();
        $finger = $request->finger;

        try {
            // Événement: Début de capture
            broadcast(new FingerprintCaptureEvent('capture_started', [
                'finger' => $finger,
                'user_name' => $user->name,
                'message' => 'Placez votre doigt sur le scanner'
            ]))->toOthers();

            // Simuler le processus de capture
            $this->simulateCaptureProcess($finger);

            return response()->json([
                'success' => true,
                'message' => 'Capture démarrée',
                'finger' => $finger
            ]);

        } catch (\Exception $e) {
            Log::error('Fingerprint capture error: ' . $e->getMessage());
            return response()->json(['error' => 'Capture failed'], 500);
        }
    }

    private function simulateCaptureProcess($finger)
    {
        // Simulation étape par étape
        sleep(1);
        
        broadcast(new FingerprintCaptureEvent('finger_detected', [
            'finger' => $finger,
            'message' => 'Doigt détecté'
        ]))->toOthers();

        sleep(1);

        $quality = rand(70, 100);
        broadcast(new FingerprintCaptureEvent('quality_update', [
            'finger' => $finger,
            'quality' => $quality,
            'message' => 'Analyse de la qualité'
        ]))->toOthers();

        sleep(1);

        // Générer un template simulé
        $template = base64_encode(json_encode([
            'finger' => $finger,
            'timestamp' => now()->timestamp,
            'features' => $this->generateFakeFeatures(),
            'quality' => $quality
        ]));

        broadcast(new FingerprintCaptureEvent('capture_complete', [
            'finger' => $finger,
            'template' => $template,
            'image' => $this->generateFakeImage($finger, $quality),
            'quality' => $quality,
            'device' => 'Pusher Simulation',
            'message' => 'Capture terminée avec succès'
        ]))->toOthers();
    }

    private function generateFakeFeatures()
    {
        $features = [];
        for ($i = 0; $i < 50; $i++) {
            $features[] = [
                'x' => rand(0, 1000) / 10,
                'y' => rand(0, 1000) / 10,
                'type' => rand(0, 1) ? 'ridge_ending' : 'bifurcation',
                'angle' => rand(0, 360)
            ];
        }
        return $features;
    }

    private function generateFakeImage($finger, $quality)
    {
        // Générer une image SVG simulée
        $svg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="200" height="200" fill="#f8f9fa"/>';
        
        // Dessiner un motif d'empreinte
        for ($i = 0; $i < 8; $i++) {
            $angle = ($i * 45) * pi() / 180;
            $radius = 40 + ($i * 5);
            
            $svg .= '<circle cx="100" cy="100" r="' . $radius . '" fill="none" stroke="#666" stroke-width="1"/>';
            
            // Ajouter des arcs
            for ($j = 0; $j < 4; $j++) {
                $startAngle = rand(0, 360) * pi() / 180;
                $endAngle = $startAngle + (rand(20, 60) * pi() / 180);
                
                $svg .= '<path d="M ' . (100 + cos($startAngle) * $radius) . ' ' . (100 + sin($startAngle) * $radius) . 
                        ' A ' . $radius . ' ' . $radius . ' 0 0 1 ' . 
                        (100 + cos($endAngle) * $radius) . ' ' . (100 + sin($endAngle) * $radius) . 
                        '" fill="none" stroke="#333" stroke-width="2"/>';
            }
        }
        
        // Points aléatoires
        for ($i = 0; $i < 30; $i++) {
            $x = rand(30, 170);
            $y = rand(30, 170);
            $r = rand(1, 3);
            $svg .= '<circle cx="' . $x . '" cy="' . $y . '" r="' . $r . '" fill="#333"/>';
        }
        
        // Texte de qualité
        $color = $quality >= 90 ? '#28a745' : ($quality >= 70 ? '#17a2b8' : ($quality >= 50 ? '#ffc107' : '#dc3545'));
        $svg .= '<text x="100" y="190" text-anchor="middle" font-family="Arial" font-size="14" fill="' . $color . '">';
        $svg .= $finger . ' - ' . $quality . '%';
        $svg .= '</text>';
        
        $svg .= '</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function getConfig()
    {
        return response()->json([
            'pusher' => [
                'key' => config('broadcasting.connections.pusher.key'),
                'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                'encrypted' => true,
                'authEndpoint' => '/api/fingerprint/auth'
            ],
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
            ],
            'session' => session()->getId(),
            'timestamp' => now()->toISOString()
        ]);
    }

    public function testConnection()
    {
        try {
            broadcast(new FingerprintCaptureEvent('test', [
                'message' => 'Test de connexion Pusher',
                'status' => 'success'
            ]))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Test event sent to Pusher'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

