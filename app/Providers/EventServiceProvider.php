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
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
