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
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'orientation_infirmier')) {
                $table->string('orientation_infirmier')->nullable()->after('status_inf');
            }
            if (!Schema::hasColumn('consultations', 'type_soins_infirmier')) {
                $table->string('type_soins_infirmier')->nullable()->after('orientation_infirmier');
            }
            if (!Schema::hasColumn('consultations', 'observation_soins')) {
                $table->text('observation_soins')->nullable()->after('type_soins_infirmier');
            }
            if (!Schema::hasColumn('consultations', 'is_urgence')) {
                $table->boolean('is_urgence')->default(0)->after('observation_soins');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'orientation_infirmier')) {
                $table->dropColumn('orientation_infirmier');
            }
            if (Schema::hasColumn('consultations', 'type_soins_infirmier')) {
                $table->dropColumn('type_soins_infirmier');
            }
            if (Schema::hasColumn('consultations', 'observation_soins')) {
                $table->dropColumn('observation_soins');
            }
            if (Schema::hasColumn('consultations', 'is_urgence')) {
                $table->dropColumn('is_urgence');
            }
        });
    }
};
