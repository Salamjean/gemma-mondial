<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientNotification;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Liste des diffusions groupées de notifications Push et In-App
     */
    public function index(Request $request)
    {
        $title = "Diffusions Push & In-App Mobiles";

        // Statistiques globales des appareils
        $totalPatients = Patient::count();
        $patientsWithToken = Patient::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->count();
        
        $androidDevices = Patient::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->whereRaw("LOWER(COALESCE(device_type, 'android')) = 'android'")
                        ->orWhereNull('device_type');
                })->whereRaw("LOWER(COALESCE(device_type, 'android')) NOT IN ('ios', 'iphone', 'ipad', 'apple')");
            })->count();

        $iosDevices = Patient::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->where(function($q) {
                $q->whereRaw("LOWER(device_type) IN ('ios', 'iphone', 'ipad', 'apple')")
                  ->orWhereRaw("LOWER(device_type) LIKE '%ios%'")
                  ->orWhereRaw("LOWER(device_type) LIKE '%iphone%'");
            })
            ->count();

        // Récupérer uniquement les diffusions groupées (Broadcast)
        $query = PatientNotification::with(['patient.user'])
            ->where(function ($q) {
                $q->where('type', 'broadcast')
                  ->orWhere('data->is_broadcast', true);
            });

        $allBroadcasts = $query->latest()->get();

        // Grouper les notifications par lot de diffusion (batch_id)
        $grouped = $allBroadcasts->groupBy(function ($item) {
            $data = is_array($item->data) ? $item->data : (json_decode($item->data ?? '[]', true) ?: []);
            return $data['batch_id'] ?? ('LEGACY-' . ($item->created_at ? $item->created_at->format('YmdHi') : $item->id) . '-' . $item->title);
        });

        // Filtrage par recherche (titre, message ou patient)
        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $grouped = $grouped->filter(function ($group) use ($search) {
                $first = $group->first();
                if (str_contains(strtolower($first->title), $search) || str_contains(strtolower($first->message), $search)) {
                    return true;
                }
                foreach ($group as $notif) {
                    $patient = $notif->patient;
                    if ($patient) {
                        if (str_contains(strtolower($patient->code_patient ?? ''), $search)) {
                            return true;
                        }
                        if ($patient->user) {
                            $fullName = strtolower(trim(($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '')));
                            if (str_contains($fullName, $search) || str_contains(strtolower($patient->user->email ?? ''), $search)) {
                                return true;
                            }
                        }
                    }
                }
                return false;
            });
        }

        // Filtrage par catégorie
        if ($request->filled('category')) {
            $cat = $request->category;
            $grouped = $grouped->filter(function ($group) use ($cat) {
                $first = $group->first();
                $data = is_array($first->data) ? $first->data : (json_decode($first->data ?? '[]', true) ?: []);
                return ($data['category'] ?? 'general') === $cat;
            });
        }

        $totalBroadcasts = $grouped->count();

        // Pagination de la collection groupée
        $page = (int) $request->get('page', 1);
        $perPage = 10;
        $campaigns = new LengthAwarePaginator(
            $grouped->forPage($page, $perPage),
            $grouped->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('users.super.notifications.index', compact(
            'title',
            'totalPatients',
            'patientsWithToken',
            'androidDevices',
            'iosDevices',
            'totalBroadcasts',
            'campaigns'
        ));
    }

    /**
     * Formulaire pour diffuser une nouvelle notification Push
     */
    public function add()
    {
        $title = "Nouvelle Diffusion de Notification Push";

        $patientsWithToken = Patient::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->count();
        $androidDevices = Patient::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->where(function($q) {
                $q->where(function($sub) {
                    $sub->whereRaw("LOWER(COALESCE(device_type, 'android')) = 'android'")
                        ->orWhereNull('device_type');
                })->whereRaw("LOWER(COALESCE(device_type, 'android')) NOT IN ('ios', 'iphone', 'ipad', 'apple')");
            })->count();

        $iosDevices = Patient::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->where(function($q) {
                $q->whereRaw("LOWER(device_type) IN ('ios', 'iphone', 'ipad', 'apple')")
                  ->orWhereRaw("LOWER(device_type) LIKE '%ios%'")
                  ->orWhereRaw("LOWER(device_type) LIKE '%iphone%'");
            })
            ->count();

        $patients = Patient::with('user')->latest()->get();

        return view('users.super.notifications.add', compact(
            'title',
            'patientsWithToken',
            'androidDevices',
            'iosDevices',
            'patients'
        ));
    }

    /**
     * Affiche la page complète avec le détail d'une notification groupée et tous ses destinataires
     */
    public function show($batchKey, Request $request)
    {
        $batchKey = trim($batchKey);
        $query = PatientNotification::with(['patient.user']);

        if (str_starts_with($batchKey, 'BRD-')) {
            $allRecipients = $query->where('data->batch_id', $batchKey)->get();
        } else {
            $allRecipients = $query->where('data->batch_id', $batchKey)->get();
            if ($allRecipients->isEmpty() && is_numeric($batchKey)) {
                $single = PatientNotification::with(['patient.user'])->find($batchKey);
                if ($single) {
                    $data = is_array($single->data) ? $single->data : (json_decode($single->data ?? '[]', true) ?: []);
                    $batchId = $data['batch_id'] ?? null;
                    if ($batchId) {
                        $allRecipients = PatientNotification::with(['patient.user'])->where('data->batch_id', $batchId)->get();
                    } else {
                        $allRecipients = collect([$single]);
                    }
                }
            }
        }

        if ($allRecipients->isEmpty()) {
            return redirect()->route('super.notifications.index')->with('warning', "Campagne de notification introuvable.");
        }

        $first = $allRecipients->first();
        $data = is_array($first->data) ? $first->data : (json_decode($first->data ?? '[]', true) ?: []);
        $title = "Détails de la Diffusion : " . $first->title;
        $category = $data['category'] ?? 'general';
        $target = $data['target'] ?? 'all';
        $sentByName = $data['sent_by_name'] ?? 'Super Admin';
        $sentAt = $first->created_at ? $first->created_at->format('d/m/Y - H:i:s') : '-';
        $totalRecipients = $allRecipients->count();
        $pushDeliveredCount = $allRecipients->filter(fn($item) => !empty(optional($item->patient)->fcm_token) || !empty(optional(optional($item->patient)->user)->fcm_token) || ($item->data['has_push'] ?? false))->count();
        $inAppOnlyCount = $totalRecipients - $pushDeliveredCount;
        $readCount = $allRecipients->filter(fn($item) => !is_null($item->read_at))->count();

        // Pagination des destinataires
        $page = (int) $request->get('page', 1);
        $perPage = 10;
        $recipients = new LengthAwarePaginator(
            $allRecipients->forPage($page, $perPage),
            $allRecipients->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('users.super.notifications.show', compact(
            'title',
            'batchKey',
            'first',
            'data',
            'category',
            'target',
            'sentByName',
            'sentAt',
            'totalRecipients',
            'pushDeliveredCount',
            'inAppOnlyCount',
            'readCount',
            'recipients'
        ));
    }

    /**
     * Diffuse une notification Push vers les appareils sélectionnés
     */
    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
            'target' => 'required|in:all,android,ios,specific',
            'category' => 'required|in:general,alert,reminder,update,info',
            'patient_id' => 'nullable|required_if:target,specific|exists:patients,id',
        ], [
            'title.required' => 'Le titre de la notification est obligatoire.',
            'title.max' => 'Le titre ne peut pas dépasser 150 caractères.',
            'message.required' => 'Le contenu du message est obligatoire.',
            'message.max' => 'Le message ne peut pas dépasser 1000 caractères.',
            'target.required' => 'Veuillez choisir une cible de destinataires.',
            'patient_id.required_if' => 'Veuillez sélectionner un patient spécifique.',
        ]);

        $title = trim($request->title);
        $message = trim($request->message);
        $target = $request->target;
        $category = $request->category;
        $patientId = $target === 'specific' ? (int)$request->patient_id : null;

        $extraData = [
            'category' => $category,
            'screen' => $request->screen ?: 'Notifications',
            'sent_by_user_id' => Auth::id(),
            'sent_by_name' => Auth::user() ? (Auth::user()->name . ' ' . Auth::user()->prenom) : 'Super Admin',
        ];

        // Lancement de la diffusion via le NotificationService
        $result = NotificationService::sendBroadcast($title, $message, $target, $patientId, $extraData);

        $pushCount = $result['push_sent'] ?? 0;
        $inAppCount = $result['in_app_created'] ?? 0;
        $batchId = $result['batch_id'] ?? 'BRD-N/A';

        // Journaliser l'action dans l'audit de sécurité
        AuditLogService::log(
            'DIFFUSION_PUSH',
            'NOTIFICATION',
            "Diffusion Push envoyée : '{$title}' ({$pushCount} push envoyés, {$inAppCount} patients ciblés) - Lot {$batchId}",
            [
                'batch_id' => $batchId,
                'target' => $target,
                'category' => $category,
                'title' => $title,
                'push_sent' => $pushCount,
                'in_app_created' => $inAppCount,
                'user_id' => Auth::id()
            ]
        );

        $targetLabel = match($target) {
            'all' => 'à tous les patients',
            'android' => 'aux utilisateurs Android',
            'ios' => 'aux utilisateurs iOS (Apple)',
            'specific' => 'au patient sélectionné',
            default => 'aux destinataires'
        };

        if ($pushCount > 0) {
            $flashMessage = "Notification Push diffusée avec succès {$targetLabel} ! ({$pushCount} appareil(s) notifié(s) en Push FCM direct, {$inAppCount} patient(s) avec notification In-App enregistrée).";
            return redirect()->route('super.notifications.index')->with('success', $flashMessage);
        } else {
            $flashWarning = "La notification a été enregistrée In-App pour {$inAppCount} patient(s), mais aucun appareil mobile actif avec un jeton FCM valide n'a pu recevoir le push instantané.";
            return redirect()->route('super.notifications.index')->with('warning', $flashWarning);
        }
    }

    /**
     * Relance la même notification Push à l'identique
     */
    public function resend($batchKey)
    {
        $batchKey = trim($batchKey);
        $query = PatientNotification::with(['patient.user']);

        if (str_starts_with($batchKey, 'BRD-')) {
            $allRecipients = $query->where('data->batch_id', $batchKey)->get();
        } else {
            $allRecipients = $query->where('data->batch_id', $batchKey)->get();
            if ($allRecipients->isEmpty() && is_numeric($batchKey)) {
                $single = PatientNotification::with(['patient.user'])->find($batchKey);
                if ($single) {
                    $data = is_array($single->data) ? $single->data : (json_decode($single->data ?? '[]', true) ?: []);
                    $batchId = $data['batch_id'] ?? null;
                    if ($batchId) {
                        $allRecipients = PatientNotification::with(['patient.user'])->where('data->batch_id', $batchId)->get();
                    } else {
                        $allRecipients = collect([$single]);
                    }
                }
            }
        }

        if ($allRecipients->isEmpty()) {
            return redirect()->route('super.notifications.index')->with('warning', "Notification introuvable pour relance.");
        }

        $first = $allRecipients->first();
        $data = is_array($first->data) ? $first->data : (json_decode($first->data ?? '[]', true) ?: []);

        $title = $first->title;
        $message = $first->message;
        $target = $data['target'] ?? 'all';
        $category = $data['category'] ?? 'general';
        $patientId = ($target === 'specific') ? ($data['patient_id'] ?? $first->patient_id) : null;

        $extraData = [
            'category' => $category,
            'screen' => $data['screen'] ?? 'Notifications',
            'sent_by_user_id' => Auth::id(),
            'sent_by_name' => Auth::user() ? (Auth::user()->name . ' ' . Auth::user()->prenom) : 'Super Admin',
            'is_resend_of' => $batchKey,
        ];

        // Lancement de la diffusion via NotificationService
        $result = NotificationService::sendBroadcast($title, $message, $target, $patientId, $extraData);

        $pushCount = $result['push_sent'] ?? 0;
        $inAppCount = $result['in_app_created'] ?? 0;
        $newBatchId = $result['batch_id'] ?? 'BRD-N/A';

        // Journaliser dans l'audit de sécurité
        AuditLogService::log(
            'RELANCE_PUSH',
            'NOTIFICATION',
            "Relance de la notification Push : '{$title}' ({$pushCount} push envoyés, {$inAppCount} patients ciblés) - Nouveau Lot {$newBatchId}",
            [
                'original_batch' => $batchKey,
                'new_batch_id' => $newBatchId,
                'target' => $target,
                'category' => $category,
                'title' => $title,
                'push_sent' => $pushCount,
                'in_app_created' => $inAppCount,
                'user_id' => Auth::id()
            ]
        );

        $targetLabel = match($target) {
            'all' => 'à tous les patients',
            'android' => 'aux utilisateurs Android',
            'ios' => 'aux utilisateurs iOS (Apple)',
            'specific' => 'au patient sélectionné',
            default => 'aux destinataires'
        };

        if ($pushCount > 0) {
            $flashMessage = "Notification Push '{$title}' relancée avec succès {$targetLabel} ! ({$pushCount} push FCM direct(s), {$inAppCount} notification(s) In-App).";
            return redirect()->route('super.notifications.index')->with('success', $flashMessage);
        } else {
            $flashWarning = "Notification '{$title}' relancée et enregistrée In-App pour {$inAppCount} patient(s), mais aucun push direct n'a pu être délivré (aucun jeton mobile actif trouvé).";
            return redirect()->route('super.notifications.index')->with('warning', $flashWarning);
        }
    }

    /**
     * Supprime toute une campagne de notification par batch_id ou par id
     */
    public function destroy($identifier)
    {
        if (str_starts_with($identifier, 'BRD-') || str_starts_with($identifier, 'LEGACY-')) {
            $count = PatientNotification::where('data->batch_id', $identifier)->delete();
        } else {
            $notif = PatientNotification::find($identifier);
            if ($notif) {
                $data = is_array($notif->data) ? $notif->data : (json_decode($notif->data ?? '[]', true) ?: []);
                $batchId = $data['batch_id'] ?? null;
                if (!empty($batchId)) {
                    $count = PatientNotification::where('data->batch_id', $batchId)->delete();
                } else {
                    $count = $notif->delete() ? 1 : 0;
                }
            } else {
                $count = 0;
            }
        }

        AuditLogService::log(
            'SUPPRESSION_NOTIFICATION',
            'NOTIFICATION',
            "Suppression de la campagne de notification : {$identifier} ({$count} entrées supprimées)",
            ['identifier' => $identifier, 'user_id' => Auth::id()]
        );

        return redirect()->route('super.notifications.index')->with('success', "Campagne de notification supprimée de l'historique.");
    }
}
