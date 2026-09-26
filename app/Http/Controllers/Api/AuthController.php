<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OneSignalToken;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\SendPatientOtpNotification;
use App\Repositories\SmsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private static $ind = "225";
    //login
    public function login(Request $request)
    {
        Log::info('Tentative de login', ['code' => $request->code]);

        $request->validate([
            'code' => 'required',
        ]);

        $patient = Patient::where('code_patient', $request->code)
            ->with('user')
            ->first();

        Log::info('Recherche patient par code_patient', ['patient' => $patient ? $patient->id : null]);

        if (!$patient) {
            $patientT = Patient::where('telephone', $request->code)
                ->with('user')
                ->first();

            Log::info('Recherche patient par téléphone', ['patientT' => $patientT ? $patientT->id : null]);

            if (!$patientT) {
                Log::warning('Patient introuvable', ['code' => $request->code]);
                return response([
                    'message' => 'Patient non trouvé!'
                ], 404);
            }
            $patient = $patientT;
        }

        $user = User::where('id', $patient->user->id)
            ->where('role_as', 'patient')
            ->first();

        Log::info('Recherche utilisateur associé', ['user' => $user ? $user->id : null]);

        if (!$user) {
            Log::error('Utilisateur introuvable pour le patient', ['patient_id' => $patient->id]);
            return response(['message' => 'Utilisateur introuvable!'], 403);
        }

        // Vérifier si l'utilisateur a un email
        if (!$user->email) {
            Log::warning('Patient sans email', ['patient_id' => $patient->id]);
            return response([
                'message' => 'Veuillez contacter l\'administration pour ajouter votre adresse email.'
            ], 403);
        }

        // Générer un code OTP aléatoire de 6 chiffres
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Définir la date d'expiration (10 minutes)
        $otpExpiresAt = now()->addMinutes(10);

        // Sauvegarder l'OTP dans la table patient
        $patient->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt
        ]);

        // Envoyer l'OTP par email
        try {
            $user->notify(new SendPatientOtpNotification($otpCode, $user->name . ' ' . $user->prenom));
            Log::info('OTP envoyé par email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'otp_code' => $otpCode
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'OTP par email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            // On ne retourne pas d'erreur ici pour laisser une chance au SMS
        }

        // Envoyer l'OTP par SMS
        if ($patient->telephone) {
            try {
                $message = "GEMMA : Votre code de connexion sécurisé est {$otpCode}. Valable 10 min. Pour votre sécurité, ne le partagez jamais.";
                // Instancier le SmsRepository avec le numéro et le message
                // Attention : SmsRepository ajoute déjà +225, assurez-vous que $patient->telephone est au bon format (sans indicatif ou adapté)
                $sms = new SmsRepository($patient->telephone, $message);
                $response = $sms->send();

                Log::info('Tentative d\'envoi OTP par SMS', [
                    'patient_id' => $patient->id,
                    'telephone' => $patient->telephone,
                    'response' => $response
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'OTP par SMS', [
                    'patient_id' => $patient->id,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::warning('Pas de numéro de téléphone pour envoyer le SMS', ['patient_id' => $patient->id]);
        }

        return response([
            'message' => 'Code OTP envoyé avec succès par SMS et email.',
            'code' => $request->code // Retourner l'identifiant pour la confirmation
        ], 200);
    }


    public function confirmLogin(Request $request)
    {
        Log::info('=== DÉBUT CONFIRMATION LOGIN PATIENT ===');
        Log::info('Données reçues:', $request->all());

        // Validation
        try {
            $request->validate([
                'code' => 'required',
                'password' => 'required|digits:6',
            ]);
            Log::info('Validation réussie');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation:', [
                'erreurs' => $e->errors(),
                'donnees' => $request->all()
            ]);
            return response([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        }

        // Déterminer quel champ utiliser pour l'OTP
        $otp = $request->password;
        Log::info('OTP extrait:', ['otp' => $otp]);

        // Recherche patient par code_patient
        Log::info('Recherche patient par code_patient:', ['code' => $request->code]);
        $patient = Patient::where('code_patient', $request->code)
            ->with('user')
            ->first();

        if (!$patient) {
            Log::info('Patient non trouvé par code_patient, recherche par téléphone');
            $patient = Patient::where('telephone', $request->code)
                ->with('user')
                ->first();

            if (!$patient) {
                Log::warning('Patient introuvable:', [
                    'identifiant' => $request->code,
                    'type' => 'code_patient/telephone'
                ]);
                return response([
                    'message' => 'Identifiant incorrect!'
                ], 403);
            }
            Log::info('Patient trouvé par téléphone:', ['patient_id' => $patient->id]);
        } else {
            Log::info('Patient trouvé par code_patient:', ['patient_id' => $patient->id]);
        }

        Log::info('Informations patient:', [
            'id' => $patient->id,
            'code_patient' => $patient->code_patient,
            'telephone' => $patient->telephone,
            'otp_code' => $patient->otp_code,
            'otp_expires_at' => $patient->otp_expires_at,
            'user_id' => $patient->user_id
        ]);

        // Vérifier l'utilisateur
        if (!$patient->user) {
            Log::error('Utilisateur non trouvé pour le patient:', ['patient_id' => $patient->id]);
            return response([
                'message' => 'Utilisateur non trouvé!'
            ], 403);
        }

        $user = User::where('id', $patient->user->id)
            ->where('role_as', 'patient')
            ->first();

        if (!$user) {
            Log::error('Utilisateur patient non trouvé:', [
                'user_id' => $patient->user->id,
                'role_attendu' => 'patient'
            ]);
            return response([
                'message' => 'Utilisateur non trouvé!'
            ], 403);
        }

        Log::info('Informations utilisateur:', [
            'user_id' => $user->id,
            'nom' => $user->name,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'role' => $user->role_as
        ]);

        // Vérifications OTP
        if (!$patient->otp_code) {
            Log::warning('Aucun OTP stocké pour le patient:', ['patient_id' => $patient->id]);
            return response([
                'message' => 'Aucun code OTP actif. Veuillez en demander un nouveau.'
            ], 403);
        }

        if (!$patient->otp_expires_at) {
            Log::warning('Pas de date d\'expiration OTP:', ['patient_id' => $patient->id]);
            return response([
                'message' => 'Erreur de configuration OTP. Veuillez en demander un nouveau.'
            ], 403);
        }

        // Vérifier l'expiration
        $now = now();
        $expiresAt = $patient->otp_expires_at;

        Log::info('Vérification expiration OTP:', [
            'maintenant' => $now->format('Y-m-d H:i:s'),
            'expire_le' => $expiresAt,
            'expire_dans' => $now->diffInMinutes($expiresAt) . ' minutes'
        ]);

        if ($now->gt($expiresAt)) {
            Log::warning('OTP expiré:', [
                'patient_id' => $patient->id,
                'otp_code' => $patient->otp_code,
                'expire_le' => $expiresAt
            ]);
            return response([
                'message' => 'Le code OTP a expiré. Veuillez en demander un nouveau.'
            ], 403);
        }

        // Vérifier la correspondance OTP
        Log::info('Comparaison OTP:', [
            'otp_reçu' => $otp,
            'otp_stocké' => $patient->otp_code,
            'correspondance' => $patient->otp_code === $otp
        ]);

        if ($patient->otp_code !== $otp) {
            Log::warning('OTP incorrect:', [
                'patient_id' => $patient->id,
                'otp_reçu' => $otp,
                'otp_attendu' => $patient->otp_code
            ]);
            return response([
                'message' => 'Code OTP incorrect!'
            ], 403);
        }

        Log::info('OTP validé avec succès');

        // Effacer l'OTP après utilisation
        try {
            $patient->update([
                'otp_code' => null,
                'otp_expires_at' => null
            ]);
            Log::info('OTP effacé de la base de données');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'effacement OTP:', [
                'patient_id' => $patient->id,
                'erreur' => $e->getMessage()
            ]);
            // Continuer malgré l'erreur
        }

        // Authentifier l'utilisateur
        try {
            Auth::login($user);
            Log::info('Utilisateur authentifié avec succès:', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'authentification:', [
                'user_id' => $user->id,
                'erreur' => $e->getMessage()
            ]);
            return response([
                'message' => 'Erreur d\'authentification'
            ], 500);
        }

        // Créer le token
        try {
            $token = $user->createToken('patient_token')->plainTextToken;
            Log::info('Token créé avec succès');
        } catch (\Exception $e) {
            Log::error('Erreur création token:', [
                'user_id' => $user->id,
                'erreur' => $e->getMessage()
            ]);
            return response([
                'message' => 'Erreur lors de la création du token'
            ], 500);
        }

        // Récupérer les données complètes du patient
        try {
            $patientData = Patient::where('id', $patient->id)
                ->with([
                    'user' => function ($query) {
                        $query->select('id', 'name', 'prenom', 'email');
                    }
                ])
                ->first();
        } catch (\Exception $e) {
            Log::error('Erreur récupération données patient:', [
                'patient_id' => $patient->id,
                'erreur' => $e->getMessage()
            ]);
            $patientData = $patient;
        }

        Log::info('=== CONFIRMATION LOGIN RÉUSSIE ===', [
            'patient_id' => $patient->id,
            'user_id' => $user->id
        ]);

        return response()->json([
            'patient' => $patientData,
            'token' => $token,
            'message' => 'Connexion réussie!'
        ], 200);
    }

    public function logout()
    {
        OneSignalToken::firstWhere('patient_id', auth()->user()->patient->id)->delete();
        auth()->user()->tokens()->delete();

        return response([
            'message' => 'deconnexion...',
        ], 200);
    }
}


