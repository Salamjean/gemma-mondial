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
        Schema::table('rendez_vouses', function (Blueprint $table) {
            if (!Schema::hasColumn('rendez_vouses', 'heure')) {
                $table->string('heure')->nullable()->after('date');
            }
            if (!Schema::hasColumn('rendez_vouses', 'motif')) {
                $table->text('motif')->nullable()->after('heure');
            }
            if (!Schema::hasColumn('rendez_vouses', 'image')) {
                $table->string('image')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendez_vouses', function (Blueprint $table) {
            if (Schema::hasColumn('rendez_vouses', 'heure')) {
                $table->dropColumn('heure');
            }
            if (Schema::hasColumn('rendez_vouses', 'motif')) {
                $table->dropColumn('motif');
            }
        });
    }
};
