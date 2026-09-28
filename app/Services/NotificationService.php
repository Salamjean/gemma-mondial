<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\PatientNotification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Envoyer et enregistrer une notification pour un patient
     *
     * @param int|Patient $patient
     * @param string $type ('rdv', 'consultation', 'declaration', 'admission', 'affectation', 'general')
     * @param string $title
     * @param string $message
     * @param array $data Métadonnées supplémentaires (ex: ["rdv_id" => 12, "screen" => "RdvDetail"])
     * @return PatientNotification|null
     */
    public static function sendToPatient($patient, string $type, string $title, string $message, array $data = []): ?PatientNotification
    {
        try {
            if (!$patient instanceof Patient) {
                $patient = Patient::with('user')->find($patient);
            }

            if (!$patient) {
                Log::warning("NotificationService: Patient introuvable pour notification [{$title}]");
                return null;
            }

            $userId = optional($patient->user)->id ?? $patient->user_id;

            // Déterminer l'ID de référence selon le type ou les métadonnées fournies
            $referenceId = $data['reference_id']
                ?? ($data['rdv_id']
                ?? ($data['rendez_vous_id']
                ?? ($data['consultation_id']
                ?? ($data['declaration_id']
                ?? ($data['admission_id']
                ?? ($data['patient_id'] ?? null))))));

            // 1. Enregistrement en base de données (In-App)
            $notification = PatientNotification::create([
                'patient_id' => $patient->id,
                'user_id' => $userId,
                'reference_id' => $referenceId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);

            // 2. Envoi Push Notification FCM si token disponible
            $fcmToken = $patient->fcm_token ?: optional($patient->user)->fcm_token;
            if ($fcmToken) {
                $deviceType = $patient->device_type ?: (optional($patient->user)->device_type ?: 'android');
                self::sendFcmPush($fcmToken, $title, $message, array_merge($data, [
                    'notification_id' => $notification->id,
                    'type' => $type,
                    'device_type' => $deviceType,
                ]));
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error("Erreur NotificationService::sendToPatient : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Diffuser une notification Push à tous les utilisateurs ou à une cible ciblée
     *
     * @param string $title
     * @param string $message
     * @param string $target ('all', 'android', 'ios', 'specific')
     * @param int|null $specificPatientId
     * @param array $extraData
     * @return array
     */
    public static function sendBroadcast(string $title, string $message, string $target = 'all', ?int $specificPatientId = null, array $extraData = []): array
    {
        $batchId = 'BRD-' . date('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        $pushSentCount = 0;
        $inAppCreatedCount = 0;

        try {
            $query = Patient::with('user');

            if ($target === 'specific' && $specificPatientId) {
                $query->where('id', $specificPatientId);
            } elseif ($target === 'android') {
                $query->where(function($q) {
                    $q->whereRaw("LOWER(COALESCE(device_type, 'android')) = 'android'")
                      ->orWhereNull('device_type');
                });
            } elseif ($target === 'ios') {
                $query->whereRaw("LOWER(device_type) = 'ios'");
            }

            $patients = $query->get();

            foreach ($patients as $patient) {
                $fcmToken = $patient->fcm_token ?: optional($patient->user)->fcm_token;
                $deviceType = strtolower($patient->device_type ?: (optional($patient->user)->device_type ?: 'android'));
                $userId = optional($patient->user)->id ?? $patient->user_id;
                $hasPushToken = !empty($fcmToken);

                $notificationData = array_merge($extraData, [
                    'is_broadcast' => true,
                    'batch_id' => $batchId,
                    'target' => $target,
                    'device_type' => $deviceType,
                    'delivery_channel' => $hasPushToken ? 'push_fcm' : 'in_app',
                    'has_push' => $hasPushToken,
                    'sent_at' => now()->toIso8601String(),
                ]);

                // 1. Créer la notification In-App pour le patient
                $notification = PatientNotification::create([
                    'patient_id' => $patient->id,
                    'user_id' => $userId,
                    'type' => 'broadcast',
                    'title' => $title,
                    'message' => $message,
                    'data' => $notificationData,
                ]);

                $inAppCreatedCount++;

                // 2. Envoyer le push FCM si token disponible
                if ($hasPushToken) {
                    $pushSuccess = self::sendFcmPush($fcmToken, $title, $message, array_merge($notificationData, [
                        'notification_id' => $notification->id,
                        'type' => 'broadcast',
                    ]));

                    if ($pushSuccess) {
                        $pushSentCount++;
                    }
                }
            }

            Log::info("NotificationService::sendBroadcast - Lot {$batchId}: {$inAppCreatedCount} in-app créées, {$pushSentCount} push envoyés");

            return [
                'batch_id' => $batchId,
                'total_targets' => count($patients),
                'in_app_created' => $inAppCreatedCount,
                'push_sent' => $pushSentCount,
            ];
        } catch (\Exception $e) {
            Log::error("Erreur NotificationService::sendBroadcast : " . $e->getMessage());
            return [
                'batch_id' => $batchId,
                'total_targets' => 0,
                'in_app_created' => $inAppCreatedCount,
                'push_sent' => $pushSentCount,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Envoi du Push via Firebase Cloud Messaging (Supporte FCM HTTP v1 et Legacy Server Key)
     */
    public static function sendFcmPush(string $fcmToken, string $title, string $body, array $customData = []): bool
    {
        try {
            // 1. Essayer d'abord la méthode moderne FCM HTTP v1 (Service Account JSON)
            $serviceAccountPath = env('FIREBASE_CREDENTIALS', storage_path('app/firebase-service-account.json'));
            if (!file_exists($serviceAccountPath) && file_exists(base_path($serviceAccountPath))) {
                $serviceAccountPath = base_path($serviceAccountPath);
            }

            if (file_exists($serviceAccountPath)) {
                $credentials = json_decode(file_get_contents($serviceAccountPath), true);
                if ($credentials && isset($credentials['project_id'], $credentials['client_email'], $credentials['private_key'])) {
                    $accessToken = self::getGoogleAccessToken($credentials);
                    if ($accessToken) {
                        $projectId = $credentials['project_id'];
                        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

                        // Formater les custom data en chaînes de caractères (requis par FCM v1)
                        $stringData = [];
                        foreach ($customData as $k => $v) {
                            $stringData[(string)$k] = is_scalar($v) ? (string)$v : json_encode($v);
                        }

                        $messagePayload = [
                            'token' => $fcmToken,
                            'notification' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'data' => $stringData,
                            'android' => [
                                'priority' => 'high',
                                'notification' => [
                                    'sound' => 'default',
                                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                ],
                            ],
                        ];

                        // Inclure APNs seulement si l'appareil est explicitement iOS
                        $deviceType = strtolower($customData['device_type'] ?? '');
                        if ($deviceType === 'ios') {
                            $messagePayload['apns'] = [
                                'headers' => [
                                    'apns-priority' => '10',
                                ],
                                'payload' => [
                                    'aps' => [
                                        'sound' => 'default',
                                        'badge' => 1,
                                    ],
                                ],
                            ];
                        }

                        $payload = [
                            'message' => $messagePayload
                        ];

                        $response = Http::withoutVerifying()->withHeaders([
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ])->post($url, $payload);

                        if ($response->successful()) {
                            Log::info("Push FCM v1 envoyé avec succès pour: {$title}");
                            return true;
                        }

                        $responseBody = $response->body();
                        if (str_contains($responseBody, 'UNREGISTERED') || str_contains($responseBody, 'NotRegistered')) {
                            Log::info("Token FCM expiré ou désinstallé (UNREGISTERED). Nettoyage du token en base pour éviter de futures erreurs.");
                            try {
                                Patient::where('fcm_token', $fcmToken)->update(['fcm_token' => null]);
                                User::where('fcm_token', $fcmToken)->update(['fcm_token' => null]);
                            } catch (\Throwable $e) {}
                        } elseif (str_contains($responseBody, 'Invalid APNs credential')) {
                            Log::warning("FCM APNs Warning: La clé APNs Apple (.p8) n'est pas configurée dans la console Firebase pour iOS. (Titre: {$title})");
                        } else {
                            Log::warning("FCM v1 response error: " . $responseBody);
                        }

                        return false;
                    }
                }
            }

            // 2. Fallback sur l'ancienne clé serveur (FCM_SERVER_KEY) si configurée
            $fcmServerKey = env('FCM_SERVER_KEY');
            if (!empty($fcmServerKey)) {
                $response = Http::withoutVerifying()->withHeaders([
                    'Authorization' => 'key=' . $fcmServerKey,
                    'Content-Type' => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'sound' => 'default',
                        'badge' => 1,
                    ],
                    'data' => $customData,
                    'priority' => 'high',
                ]);

                return $response->successful();
            }

            Log::info("Push FCM skipped (aucun fichier FIREBASE_CREDENTIALS ni FCM_SERVER_KEY valide trouvé). Titre: {$title}");
            return false;
        } catch (\Exception $e) {
            Log::warning("Erreur lors de l'envoi push FCM: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un Token OAuth2 Google via le fichier Service Account JSON
     */
    private static function getGoogleAccessToken(array $credentials): ?string
    {
        try {
            $now = time();
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $claimSet = json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]);

            $base64UrlHeader = self::base64UrlEncode($header);
            $base64UrlClaimSet = self::base64UrlEncode($claimSet);
            $signatureInput = $base64UrlHeader . '.' . $base64UrlClaimSet;

            $signature = '';
            openssl_sign($signatureInput, $signature, $credentials['private_key'], 'SHA256');
            $base64UrlSignature = self::base64UrlEncode($signature);

            $jwt = $signatureInput . '.' . $base64UrlSignature;

            $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error("Erreur génération access_token Google: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Exception génération access_token Google: " . $e->getMessage());
            return null;
        }
    }

    private static function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}
