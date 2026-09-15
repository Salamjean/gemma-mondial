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
        Schema::create('service_infirmiers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('infirmier_id')->index();
            $table->unsignedBigInteger('service_hospital_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_infirmiers');
    }
};
