<?php

namespace App\Repositories;


class SmsRepository
{
    private $apiKey;
    private $senderId;
    private $baseUrl;

    protected $message;
    protected $phone;

    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;

        // Récupération des identifiants Yéllika via config() pour supporter php artisan config:cache
        $this->apiKey = trim(config('services.yellika.api_key', '811|BjTyHTb8CdJnYA6nBDhiaX9R0f3gtXG9FTKaZpGKf035957f'));
        $this->senderId = config('services.yellika.sender_id', 'GestPatient');
        $this->baseUrl = rtrim(config('services.yellika.api_url', 'https://app.1smsafrica.com/api/v3'), '/');

        // Log de vérification de la clé (sans l'afficher entièrement pour sécurité)
        $len = strlen($this->apiKey);
        $maskedKey = substr($this->apiKey, 0, 4) . '...' . substr($this->apiKey, -4);
        \Illuminate\Support\Facades\Log::info("Vérification clé API: $maskedKey (Lon: $len)");
    }

    public function send()
    {
        // Nettoyer le numéro (garder chiffres)
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);

        // Formatage pour 1smsafrica (souvent 225xxxxxxxxx)
        if (!str_starts_with($cleanPhone, '225')) {
            if (str_starts_with($cleanPhone, '00225')) {
                $cleanPhone = substr($cleanPhone, 2);
            } else {
                $cleanPhone = '225' . $cleanPhone;
            }
        }

        // Endpoint V3 standard pour l'envoi de SMS
        $url = $this->baseUrl . "/sms/send";

        // Nettoyer les accents du message pour assurer la compatibilité GSM 7-bit maximale
        $cleanMessage = str_replace(
            ['é', 'è', 'ê', 'ë', 'É', 'È', 'Ê', 'Ë', 'à', 'â', 'ä', 'À', 'Â', 'Ä', 'î', 'ï', 'Î', 'Ï', 'ô', 'ö', 'Ô', 'Ö', 'ù', 'û', 'ü', 'Ù', 'Û', 'Ü', 'ç', 'Ç'],
            ['e', 'e', 'e', 'e', 'E', 'E', 'E', 'E', 'a', 'a', 'a', 'A', 'A', 'A', 'i', 'i', 'I', 'I', 'o', 'o', 'O', 'O', 'u', 'u', 'u', 'U', 'U', 'U', 'c', 'C'],
            $this->message
        );

        // Paramètres pour l'API V3 (POST JSON)
        $data = [
            'recipient' => $cleanPhone,
            'sender_id' => $this->senderId, // Doit correspondre à un Sender ID validé
            'message' => $cleanMessage,
            'type' => 'plain' // Parfois 'text' ou 'plain'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Important: Authentification Bearer Token
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->apiKey,
            "Content-Type: application/json",
            "Accept: application/json",
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

        curl_close($ch);

        if ($error) {
            \Illuminate\Support\Facades\Log::error("1smsafrica Curl Error: $error");
            return ['success' => false, 'error' => $error];
        }

        $isSuccess = ($httpCode >= 200 && $httpCode < 300);
        if (!$isSuccess) {
            \Illuminate\Support\Facades\Log::warning("1smsafrica Response Non-2xx ($httpCode) at $effectiveUrl: $response");
            return ['success' => false, 'http_code' => $httpCode, 'response' => $response];
        }

        \Illuminate\Support\Facades\Log::info("1smsafrica Response ($httpCode) at $effectiveUrl (Redirect: $redirectUrl): $response");
        return ['success' => true, 'http_code' => $httpCode, 'response' => $response];
    }
}
