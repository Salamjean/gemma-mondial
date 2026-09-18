<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds for Super Admin only.
     */
    public function run(): void
    {
        $superAdminEmail = 'super@gmail.com';

        // Créer ou mettre à jour l'utilisateur Super Admin
        $user = User::updateOrCreate(
            ['email' => $superAdminEmail],
            [
                'name' => 'Super',
                'prenom' => 'GEMMA',
                'role_as' => 'super',
                'password' => Hash::make('KKStechnologies2022@'),
                'email_verified_at' => now(),
            ]
        );

        // Créer ou mettre à jour le profil Admin associé
        Admin::updateOrCreate(
            ['user_id' => $user->id],
            [
                'contact' => '0711117979',
                'address' => 'Abidjan Cocody',
                'img_url' => null,
                'status' => '0',
            ]
        );

        if (isset($this->command)) {
            $this->command->info("Super Admin initialisé avec succès ! (Email: {$superAdminEmail} ");
        }
    }
}

