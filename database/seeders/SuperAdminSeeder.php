<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds for Super Admin only.
     */
    public function run(): void
    {
        // 1. Initialiser les tables et données SQL (Régions, Départements, Sous-préfectures / Communes, Médicaments)
        if (!Schema::hasTable('regions')) {
            $regionPath = database_path('tables/sql/regions.sql');
            if (file_exists($regionPath)) {
                DB::unprepared(file_get_contents($regionPath));
            }
        }

        if (!Schema::hasTable('departments')) {
            $departmentPath = database_path('tables/sql/departments.sql');
            if (file_exists($departmentPath)) {
                DB::unprepared(file_get_contents($departmentPath));
            }
        }

        if (!Schema::hasTable('sub_prefectures')) {
            $subPrefecturePath = database_path('tables/sql/sub_prefectures.sql');
            if (file_exists($subPrefecturePath)) {
                DB::unprepared(file_get_contents($subPrefecturePath));
            }
        }

        if (Schema::hasTable('drugs') && DB::table('drugs')->count() === 0) {
            $drugPath = database_path('tables/sql/drugs.sql');
            if (file_exists($drugPath)) {
                DB::unprepared(file_get_contents($drugPath));
            }
        }

        // 2. Créer ou mettre à jour le Super Admin
        $superAdminEmail = 'super@gmail.com';

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
            $this->command->info("Super Admin et données SQL initialisés avec succès ! (Email: {$superAdminEmail})");
        }
    }
}

