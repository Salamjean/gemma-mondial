<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute l'organisation de rattachement (ex: UCP-FM) et la fonction aux comptes admins.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'organisation')) {
                $table->string('organisation')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('admins', 'fonction')) {
                $table->string('fonction')->nullable()->after('organisation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            foreach (['fonction', 'organisation'] as $column) {
                if (Schema::hasColumn('admins', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
