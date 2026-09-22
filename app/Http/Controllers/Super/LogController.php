<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Hospital;
use App\Models\User;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class LogController extends Controller
{
    /**
     * Page principale des Logs et du Journal d'Audit
     */
    public function index(Request $request)
    {
        $title = 'Logs Système & Journal d\'Audit';
        $activeTab = $request->get('tab', 'audit'); // 'audit' ou 'laravel'

        // Filtres pour Audit Trail
        $searchAudit = trim($request->get('search_audit', ''));
        $selectedHospital = $request->get('hospital_id');
        $selectedAction = $request->get('action_type');
        $selectedRole = $request->get('user_role');
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $queryAudit = AuditLog::with(['user', 'hospital'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($selectedHospital && $selectedHospital !== 'ALL') {
            $queryAudit->where('hospital_id', $selectedHospital);
        }

        if ($selectedAction && $selectedAction !== 'ALL') {
            $queryAudit->where('action_type', $selectedAction);
        }

        if ($selectedRole && $selectedRole !== 'ALL') {
            $queryAudit->where('user_role', $selectedRole);
        }

        if ($searchAudit !== '') {
            $queryAudit->where(function ($q) use ($searchAudit) {
                $q->where('description', 'like', "%{$searchAudit}%")
                  ->orWhere('user_name', 'like', "%{$searchAudit}%")
                  ->orWhere('ip_address', 'like', "%{$searchAudit}%")
                  ->orWhere('mac_address', 'like', "%{$searchAudit}%")
                  ->orWhere('module', 'like', "%{$searchAudit}%")
                  ->orWhere('device_info', 'like', "%{$searchAudit}%");
            });
        }

        $auditLogs = $queryAudit->orderBy('created_at', 'desc')->paginate(20, ['*'], 'page_audit');

        // Filtres pour Laravel Logs
        $levelFilter = $request->get('log_level', 'ALL');
        $searchLog = trim($request->get('search_log', ''));
        $laravelLogs = AuditLogService::getLaravelLogs(200, $levelFilter, $searchLog);
        $laravelLogSize = AuditLogService::getLaravelLogSize();

        // Statistiques globales
        $hospitals = Hospital::orderBy('label', 'asc')->get();
        $totalAudits = AuditLog::count();
        $totalDownloads = AuditLog::whereIn('action_type', ['TELECHARGEMENT_PDF', 'EXPORT_EXCEL', 'EXPORT_SAGE'])->count();
        $totalCreations = AuditLog::where('action_type', 'ENREGISTREMENT')->count();
        $uniqueMachines = AuditLog::distinct('mac_address')->count('mac_address');

        return view('users.super.logs.index', compact(
            'title', 'activeTab', 'auditLogs', 'laravelLogs', 'laravelLogSize',
            'hospitals', 'totalAudits', 'totalDownloads', 'totalCreations', 'uniqueMachines',
            'searchAudit', 'selectedHospital', 'selectedAction', 'selectedRole', 'startDate', 'endDate',
            'levelFilter', 'searchLog'
        ));
    }

    /**
     * Vider le fichier de log Laravel
     */
    public function clearLaravelLogs()
    {
        AuditLogService::clearLaravelLogs();
        AuditLogService::log('SUPPRESSION', 'SYSTEME', 'Le Super Administrateur a vidé le fichier des logs système Laravel.');

        return redirect()->route('super.logs.index', ['tab' => 'laravel'])->with('success', 'Les logs Laravel ont été purgés avec succès.');
    }

    /**
     * Télécharger le fichier brut laravel.log
     */
    public function downloadLaravelLog()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            return redirect()->back()->with('error', 'Le fichier de log est introuvable.');
        }

        AuditLogService::log('TELECHARGEMENT_LOG', 'SYSTEME', 'Téléchargement du fichier brut de log Laravel (laravel.log)');

        return response()->download($logFile, 'laravel_' . Carbon::now()->format('Ymd_His') . '.log');
    }

    /**
     * Exporter le Journal d'Audit en Excel
     */
    public function exportAuditExcel(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $selectedHospital = $request->get('hospital_id');
        $selectedAction = $request->get('action_type');

        $query = AuditLog::with(['user', 'hospital'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($selectedHospital && $selectedHospital !== 'ALL') {
            $query->where('hospital_id', $selectedHospital);
        }
        if ($selectedAction && $selectedAction !== 'ALL') {
            $query->where('action_type', $selectedAction);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        AuditLogService::log('EXPORT_EXCEL', 'SYSTEME', "Exportation Excel du Journal d'Audit ({$logs->count()} lignes)");

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Calibri, Arial, sans-serif; }';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th { background-color: #0f172a; color: #ffffff; font-weight: bold; border: 1px solid #cbd5e1; padding: 10px; text-align: center; }';
        $html .= 'td { border: 1px solid #e2e8f0; padding: 8px; font-size: 13px; }';
        $html .= '.text-center { text-align: center; }';
        $html .= '.fw-bold { font-weight: bold; }';
        $html .= '</style></head><body>';

        $html .= '<h2>JOURNAL D\'AUDIT TRAIL & TRACABILITE DES OPERATIONS</h2>';
        $html .= '<p><strong>Periode :</strong> Du ' . Carbon::parse($startDate)->format('d/m/Y') . ' au ' . Carbon::parse($endDate)->format('d/m/Y') . ' | <strong>Total enregistrements :</strong> ' . count($logs) . '</p>';

        $html .= '<table><thead><tr>';
        $html .= '<th>Date & Heure</th>';
        $html .= '<th>Utilisateur</th>';
        $html .= '<th>Role</th>';
        $html .= '<th>Hopital</th>';
        $html .= '<th>Action</th>';
        $html .= '<th>Module</th>';
        $html .= '<th>Description de l\'operation</th>';
        $html .= '<th>Adresse IP</th>';
        $html .= '<th>Adresse MAC</th>';
        $html .= '<th>Appareil / Navigateur</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($logs as $log) {
            $hospitalName = $log->hospital ? ($log->hospital->label ?? $log->hospital->nom_direction_generale ?? 'Hôpital') : 'Global / Admin';
            $html .= '<tr>';
            $html .= '<td class="text-center">' . $log->created_at->format('d/m/Y H:i:s') . '</td>';
            $html .= '<td class="fw-bold">' . htmlspecialchars($log->user_name) . '</td>';
            $html .= '<td class="text-center">' . htmlspecialchars($log->user_role) . '</td>';
            $html .= '<td>' . htmlspecialchars($hospitalName) . '</td>';
            $html .= '<td class="text-center fw-bold">' . htmlspecialchars($log->action_type) . '</td>';
            $html .= '<td class="text-center">' . htmlspecialchars($log->module) . '</td>';
            $html .= '<td>' . htmlspecialchars($log->description) . '</td>';
            $html .= '<td class="text-center" style="mso-number-format:\'@\';">' . htmlspecialchars($log->ip_address) . '</td>';
            $html .= '<td class="text-center fw-bold" style="mso-number-format:\'@\';">' . htmlspecialchars($log->mac_address) . '</td>';
            $html .= '<td>' . htmlspecialchars($log->device_info) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        $filename = 'JOURNAL_AUDIT_TRAIL_' . Carbon::now()->format('Ymd_His') . '.xls';

        return Response::make($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
