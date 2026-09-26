<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            $user = $event->user;
            if ($user) {
                $userName = trim(($user->nom ?? '') . ' ' . ($user->prenom ?? $user->name ?? ''));
                \App\Services\AuditLogService::log(
                    'CONNEXION',
                    'AUTHENTIFICATION',
                    "Connexion réussie de l'utilisateur: {$userName} (" . strtoupper($user->role_as ?? 'utilisateur') . ")",
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role_as
                    ],
                    $user->id,
                    $user->hospital_id ?? null
                );
            }
        });

        Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if ($event->user) {
                $user = $event->user;
                $userName = trim(($user->nom ?? '') . ' ' . ($user->prenom ?? $user->name ?? ''));
                \App\Services\AuditLogService::log(
                    'DECONNEXION',
                    'AUTHENTIFICATION',
                    "Déconnexion de l'utilisateur: {$userName} (" . strtoupper($user->role_as ?? 'utilisateur') . ")",
                    [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'role' => $user->role_as
                    ],
                    $user->id,
                    $user->hospital_id ?? null
                );
            }
        });

        // -------------------------------------------------------------
        // DÉCLENCHEURS AUTOMATIQUES DES NOTIFICATIONS PATIENT (PUSH & IN-APP)
        // -------------------------------------------------------------

        // 1. Enregistrement d'un nouveau patient
        \App\Models\Patient::created(function ($patient) {
            if ($patient->id) {
                \App\Services\NotificationService::sendToPatient(
                    $patient,
                    'affectation',
                    'Bienvenue sur GEMMA',
                    "Votre dossier médical patient ({$patient->code_patient}) a été créé avec succès.",
                    ['patient_id' => $patient->id, 'code_patient' => $patient->code_patient]
                );
            }
        });

        // 2. Admission d'un patient
        \App\Models\Admission::created(function ($admission) {
            if ($admission->patient_id) {
                \App\Services\NotificationService::sendToPatient(
                    $admission->patient_id,
                    'admission',
                    'Nouvelle admission',
                    "Vous avez été admis dans l'établissement médical pour une prise en charge.",
                    ['admission_id' => $admission->id, 'hospital_id' => $admission->hospital_id]
                );
            }
        });

        // 3. Création d'un Rendez-vous
        \App\Models\RendezVous::created(function ($rdv) {
            if ($rdv->patient_id) {
                $timeInfo = $rdv->heure ? " à {$rdv->heure}" : "";
                \App\Services\NotificationService::sendToPatient(
                    $rdv->patient_id,
                    'rdv',
                    'Rendez-vous enregistré',
                    "Votre rendez-vous \"{$rdv->title}\" prévu le {$rdv->date}{$timeInfo} a été enregistré.",
                    ['rdv_id' => $rdv->id, 'screen' => 'RdvDetail']
                );
            }
        });

        // 4. Mise à jour de statut d'un Rendez-vous (ex: confirmé / annulé)
        \App\Models\RendezVous::updated(function ($rdv) {
            if ($rdv->patient_id && $rdv->isDirty('status')) {
                $statusLabel = $rdv->status === 'confirmed' ? 'confirmé' : ($rdv->status === 'cancelled' ? 'annulé' : $rdv->status);
                \App\Services\NotificationService::sendToPatient(
                    $rdv->patient_id,
                    'rdv',
                    "Rendez-vous {$statusLabel}",
                    "Votre rendez-vous du {$rdv->date} est désormais {$statusLabel}.",
                    ['rdv_id' => $rdv->id, 'status' => $rdv->status, 'screen' => 'RdvDetail']
                );
            }
        });

        // 5. Création d'une Consultation
        \App\Models\Consultation::created(function ($consultation) {
            if ($consultation->patient_id) {
                \App\Services\NotificationService::sendToPatient(
                    $consultation->patient_id,
                    'consultation',
                    'Nouvelle consultation initiée',
                    "Une nouvelle consultation a été ouverte dans votre dossier médical.",
                    ['consultation_id' => $consultation->id, 'screen' => 'ParcoursDetail']
                );
            }
        });

        // 6. Clôture d'une Consultation (Documents ordonnances, bilans prêts)
        \App\Models\Consultation::updated(function ($consultation) {
            if ($consultation->patient_id && $consultation->isDirty('status') && $consultation->status == 1) {
                \App\Services\NotificationService::sendToPatient(
                    $consultation->patient_id,
                    'consultation',
                    'Consultation terminée & Documents prêts',
                    "Votre consultation est terminée. Vos ordonnances, bilans et arrêts de travail sont consultables dans votre parcours.",
                    ['consultation_id' => $consultation->id, 'screen' => 'ParcoursDetail']
                );
            }
        });

        // 7. Création d'une Déclaration (Naissance / Décès)
        \App\Models\Declaration::created(function ($declaration) {
            if ($declaration->patient_id) {
                \App\Services\NotificationService::sendToPatient(
                    $declaration->patient_id,
                    'declaration',
                    'Nouvelle déclaration médicale',
                    "Un certificat médical / acte de déclaration a été enregistré dans votre dossier.",
                    ['declaration_id' => $declaration->id, 'screen' => 'DeclarationDetail']
                );
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
