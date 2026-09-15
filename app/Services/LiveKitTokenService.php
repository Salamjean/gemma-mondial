<?php

namespace App\Services;

use Firebase\JWT\JWT;

class LiveKitTokenService
{
    public static function generateToken(string $roomName, string $identity = 'user', string $displayName = 'Utilisateur'): string
    {
        $apiKey = config('services.livekit.api_key', 'APIVdAJ5HfTwJEi');
        $apiSecret = config('services.livekit.api_secret', 'yFs8UoilwvwcuMeYUi6coYMADMSEfaVJnpfNlY1X7HUB');

        $now = time();

        $payload = [
            'iss' => $apiKey,
            'sub' => $identity,
            'name' => $displayName,
            'nbf' => $now - 60,
            'exp' => $now + (3600 * 24),
            'video' => [
                'room' => $roomName,
                'roomJoin' => true,
                'canPublish' => true,
                'canSubscribe' => true,
                'canPublishData' => true,
            ],
        ];

        return JWT::encode($payload, $apiSecret, 'HS256');
    }
}
