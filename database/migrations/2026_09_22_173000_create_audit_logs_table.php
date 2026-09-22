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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('hospital_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action_type'); // ENREGISTREMENT, MODIFICATION, SUPPRESSION, TELECHARGEMENT_PDF, EXPORT_EXCEL, EXPORT_SAGE, CONNEXION, etc.
            $table->string('module')->nullable(); // COMPTABILITE, CONSULTATION, PATIENT, CAISSE, PHARMACIE, etc.
            $table->string('description');
            $table->json('details_json')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('mac_address', 50)->nullable();
            $table->text('device_info')->nullable();
            $table->timestamps();

            $table->index(['hospital_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
