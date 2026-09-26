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
        Schema::table('patient_notifications', function (Blueprint $table) {
            $table->dropColumn(['admission_id', 'consultation_id', 'rdv_id', 'declaration_id']);
            $table->unsignedBigInteger('reference_id')->nullable()->after('user_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_notifications', function (Blueprint $table) {
            $table->dropColumn('reference_id');
            $table->unsignedBigInteger('admission_id')->nullable()->after('user_id')->index();
            $table->unsignedBigInteger('consultation_id')->nullable()->after('admission_id')->index();
            $table->unsignedBigInteger('rdv_id')->nullable()->after('consultation_id')->index();
            $table->unsignedBigInteger('declaration_id')->nullable()->after('rdv_id')->index();
        });
    }
};
