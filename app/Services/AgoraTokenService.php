<?php

namespace App\Services;

class AgoraTokenService
{
    /**
     * Génère un Token RTC Agora.io AccessToken2 (v007) pour la visioconférence.
     */
    public static function generateToken(string $channelName, int $uid = 0, int $role = 1, int $expireTimeInSeconds = 86400): ?string
    {
        $appId = config('services.agora.app_id', 'f502a4bc7cd84daeba1072d3bf48b1f3');
        $appCertificate = config('services.agora.app_certificate', 'c6005f2c17ed47309f4f4b21356f6e37');

        if (empty($appId) || empty($appCertificate)) {
            return null;
        }

        return self::buildAccessToken2($appId, $appCertificate, $channelName, $uid, $expireTimeInSeconds);
    }

    private static function buildAccessToken2($appId, $appCertificate, $channelName, $uid, $expireInSeconds)
    {
        $issueTs = time();
        $expireTs = $issueTs + $expireInSeconds;
        $salt = rand(1, 99999999);

        // Si uid == 0, on met une chaîne vide "" pour permettre à n'importe quel UID (Médecin & Patient) de rejoindre
        $uidStr = ($uid === 0 || $uid === "0" || empty($uid)) ? "" : (string)$uid;

        // Privilèges RTC: 1 = Join, 2 = Publish Audio, 3 = Publish Video, 4 = Publish Data
        $privileges = [
            1 => $expireTs,
            2 => $expireTs,
            3 => $expireTs,
            4 => $expireTs,
        ];

        $privData = pack('v', count($privileges));
        foreach ($privileges as $k => $v) {
            $privData .= pack('v', $k) . pack('V', $v);
        }

        $rtcService = pack('v', strlen($channelName)) . $channelName
                    . pack('v', strlen($uidStr)) . $uidStr
                    . $privData;

        // Service Map: count=1, service_type=1 (RTC), len + rtcService
        $servicesMap = pack('v', 1) . pack('v', 1) . pack('v', strlen($rtcService)) . $rtcService;

        // Base payload: expireTs (4B), issueTs (4B), salt (4B), servicesMap
        $payload = pack('V', $expireTs) . pack('V', $issueTs) . pack('V', $salt) . $servicesMap;

        // Signature: HMAC-SHA256(appCertificate, appId + payload)
        $sig = hash_hmac('sha256', $appId . $payload, $appCertificate, true);

        // Content: signature (len + sig) + payload
        $content = pack('v', strlen($sig)) . $sig . $payload;

        return "007" . base64_encode($content);
    }
}
