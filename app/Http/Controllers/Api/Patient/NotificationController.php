<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Models\PatientNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Liste des notifications du patient authentifié
     */
    public function index(Request $request)
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Profil patient introuvable.'], 404);
            }

            $query = PatientNotification::where('patient_id', $patient->id)
                ->orderByDesc('created_at');

            // Filtrage optionnel (ex: unread=true)
            if ($request->boolean('unread_only')) {
                $query->whereNull('read_at');
            }

            $total = PatientNotification::where('patient_id', $patient->id)->count();
            $unreadCount = PatientNotification::where('patient_id', $patient->id)->whereNull('read_at')->count();

            $limit = $request->get('limit', 50);
            $notifications = $query->take($limit)->get();

            return response()->json([
                'status' => 'success',
                'total' => $total,
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Marquer une notification spécifique comme lue
     */
    public function markAsRead($id)
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Profil patient introuvable.'], 404);
            }

            $notification = PatientNotification::where('patient_id', $patient->id)->find($id);

            if (!$notification) {
                return response()->json(['status' => 'error', 'message' => 'Notification introuvable.'], 404);
            }

            if (is_null($notification->read_at)) {
                $notification->read_at = now();
                $notification->save();
            }

            $unreadCount = PatientNotification::where('patient_id', $patient->id)->whereNull('read_at')->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Notification marquée comme lue.',
                'unread_count' => $unreadCount,
                'notification' => $notification,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Marquer toutes les notifications du patient comme lues
     */
    public function markAllAsRead()
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Profil patient introuvable.'], 404);
            }

            $updatedCount = PatientNotification::where('patient_id', $patient->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'status' => 'success',
                'message' => "Toutes les notifications ont été marquées comme lues ({$updatedCount} mises à jour).",
                'unread_count' => 0,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer une notification spécifique
     */
    public function destroy($id)
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Profil patient introuvable.'], 404);
            }

            $notification = PatientNotification::where('patient_id', $patient->id)->find($id);

            if (!$notification) {
                return response()->json(['status' => 'error', 'message' => 'Notification introuvable.'], 404);
            }

            $notification->delete();

            $unreadCount = PatientNotification::where('patient_id', $patient->id)->whereNull('read_at')->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Notification supprimée avec succès.',
                'unread_count' => $unreadCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer toutes les notifications du patient
     */
    public function destroyAll()
    {
        try {
            $patient = Auth::user()->patient;
            if (!$patient) {
                return response()->json(['status' => 'error', 'message' => 'Profil patient introuvable.'], 404);
            }

            $deletedCount = PatientNotification::where('patient_id', $patient->id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => "Toutes les notifications ont été supprimées ({$deletedCount} supprimées).",
                'unread_count' => 0,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}


