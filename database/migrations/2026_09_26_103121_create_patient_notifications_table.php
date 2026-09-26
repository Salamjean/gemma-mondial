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
        Schema::create('patient_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('type')->default('general')->index(); // rdv, consultation, declaration, admission, affectation, general
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // metadata, ex: {"rdv_id": 1, "screen": "RdvDetail"}
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_notifications');
    }
};
