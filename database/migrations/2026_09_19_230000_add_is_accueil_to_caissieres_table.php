<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('caissieres', function (Blueprint $table) {
            if (!Schema::hasColumn('caissieres', 'is_accueil')) {
                $table->boolean('is_accueil')->default(0)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caissieres', function (Blueprint $table) {
            if (Schema::hasColumn('caissieres', 'is_accueil')) {
                $table->dropColumn('is_accueil');
            }
        });
    }
};
