<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AuditLogService
{
    /**
     * Tente de récupérer l'adresse MAC du client (via table ARP locale sur Windows/Linux)
     */
    public static function getClientMacAddress($ip = null)
    {
        if (!$ip) {
            $ip = request()->ip();
        }

        // 1. Vérifier si un Device UUID persistant ou MAC est transmis (Header, Cookie ou Form)
        $deviceUuid = request()->header('X-Client-Device-UUID')
            ?: request()->header('X-Client-Mac')
            ?: request()->cookie('gemma_device_uuid')
            ?: request()->input('client_device_uuid');

        if (!empty($deviceUuid) && is_string($deviceUuid)) {
            $cleanedUuid = trim($deviceUuid);
            if (str_starts_with(strtoupper($cleanedUuid), 'DEV-') || preg_match('/^[0-9a-f]{2}(:[0-9a-f]{2}){5}$/i', $cleanedUuid)) {
                return strtoupper($cleanedUuid);
            }
        }

        // 2. Si localhost / développement local
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $localMac = self::getLocalServerMac();
            return $localMac ? $localMac : 'DEV-LOCAL-SERVER';
        }

        // 3. Exécuter arp sur le système pour trouver l'adresse MAC si sur le même LAN
        try {
            $mac = null;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $output = @shell_exec('arp -a ' . escapeshellarg($ip));
                if ($output && preg_match('/([0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2})/i', $output, $matches)) {
                    $mac = strtoupper(str_replace('-', ':', $matches[1]));
                }
            } else {
                $output = @shell_exec('arp -n ' . escapeshellarg($ip) . ' 2>/dev/null');
                if ($output && preg_match('/([0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2})/i', $output, $matches)) {
                    $mac = strtoupper($matches[1]);
                }
            }
            if ($mac) {
                return $mac;
            }
        } catch (\Throwable $e) {}

        // 4. Générer une empreinte de secours basée sur l'IP + UserAgent
        $ua = request()->userAgent() ?: 'GEMMA';
        $prefix = 'WIN';
        if (preg_match('/mac/i', $ua)) $prefix = 'MAC';
        elseif (preg_match('/android/i', $ua)) $prefix = 'AND';
        elseif (preg_match('/iphone|ipad/i', $ua)) $prefix = 'IOS';
        elseif (preg_match('/linux/i', $ua)) $prefix = 'LNX';

        $fallbackHash = strtoupper(substr(md5($ip . $ua), 0, 8));
        return 'DEV-' . $prefix . '-' . substr($fallbackHash, 0, 4) . '-' . substr($fallbackHash, 4, 4);
    }

    /**
     * Récupère l'adresse MAC du serveur / hôte
     */
    protected static function getLocalServerMac()
    {
        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $output = @shell_exec('getmac');
                if ($output && preg_match('/([0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2})/i', $output, $matches)) {
                    return strtoupper(str_replace('-', ':', $matches[1]));
                }
            } else {
                $output = @shell_exec("ip link show | awk '/ether/ {print $2}' | head -n 1");
                if ($output && preg_match('/([0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2}[:-][0-9a-f]{2})/i', trim($output), $matches)) {
                    return strtoupper($matches[1]);
                }
            }
        } catch (\Throwable $e) {}
        return null;
    }

    /**
     * Informations sur le navigateur / appareil
     */
    public static function getDeviceInfo()
    {
        $userAgent = request()->userAgent() ?: 'Inconnu';
        
        $os = 'Inconnu';
        if (preg_match('/windows|win32/i', $userAgent)) $os = 'Windows';
        elseif (preg_match('/macintosh|mac os x/i', $userAgent)) $os = 'Mac OS';
        elseif (preg_match('/linux/i', $userAgent)) $os = 'Linux / Android';
        elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) $os = 'iOS';

        $browser = 'Inconnu';
        if (preg_match('/edg/i', $userAgent)) $browser = 'Edge';
        elseif (preg_match('/chrome|crios/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/firefox|fxios/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/safari/i', $userAgent)) $browser = 'Safari';
        elseif (preg_match('/opera|opr/i', $userAgent)) $browser = 'Opera';

        return "{$os} | {$browser} (" . substr($userAgent, 0, 80) . ")";
    }

    /**
     * Enregistrer une entrée d'audit trail
     */
    public static function log($actionType, $module, $description, array $details = [], $userId = null, $hospitalId = null)
    {
        try {
            $user = Auth::user();
            $uId = $userId ?: ($user ? $user->id : null);
            $uName = $user ? trim(($user->nom ?? '') . ' ' . ($user->prenom ?? $user->name ?? '')) : 'Système';
            $uRole = $user ? ($user->role_as ?? 'visiteur') : 'system';

            if (!$hospitalId && $user) {
                $hospitalId = $user->hospital_id ?? null;
            }

            $ip = request()->ip() ?: '127.0.0.1';
            $mac = self::getClientMacAddress($ip);
            $deviceInfo = self::getDeviceInfo();

            return AuditLog::create([
                'user_id' => $uId,
                'hospital_id' => $hospitalId,
                'user_name' => $uName,
                'user_role' => $uRole,
                'action_type' => strtoupper($actionType),
                'module' => strtoupper($module),
                'description' => $description,
                'details_json' => $details,
                'ip_address' => $ip,
                'mac_address' => $mac,
                'device_info' => $deviceInfo,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Erreur enregistrement AuditLog: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lire et parser les logs Laravel depuis storage/logs/laravel.log
     */
    public static function getLaravelLogs($limit = 150, $levelFilter = null, $search = null)
    {
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            return [];
        }

        $content = File::get($logFile);
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] ([a-zA-Z0-9_]+)\.([A-Z]+): (.*?)(?=(\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$))/s';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $parsedLogs = [];

        foreach (array_reverse($matches) as $match) {
            $date = $match[1];
            $env = $match[2];
            $level = strtoupper($match[3]);
            $messageBlock = trim($match[4]);

            // Séparer le message principal de la stack trace
            $lines = explode("\n", $messageBlock);
            $mainMessage = $lines[0] ?? '';
            $stackTrace = count($lines) > 1 ? implode("\n", array_slice($lines, 1)) : '';

            // Filtrage par niveau
            if ($levelFilter && $levelFilter !== 'ALL' && $level !== strtoupper($levelFilter)) {
                continue;
            }

            // Filtrage par recherche
            if ($search && !str_contains(strtolower($mainMessage . ' ' . $stackTrace), strtolower($search))) {
                continue;
            }

            $parsedLogs[] = [
                'date' => $date,
                'env' => $env,
                'level' => $level,
                'message' => $mainMessage,
                'stack_trace' => $stackTrace,
            ];

            if (count($parsedLogs) >= $limit) {
                break;
            }
        }

        return $parsedLogs;
    }

    /**
     * Taille du fichier de log Laravel
     */
    public static function getLaravelLogSize()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            return '0 Ko';
        }
        $bytes = File::size($logFile);
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', ' ') . ' Mo';
        }
        return number_format($bytes / 1024, 2, ',', ' ') . ' Ko';
    }

    /**
     * Vider les logs Laravel
     */
    public static function clearLaravelLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
            return true;
        }
        return false;
    }
}
