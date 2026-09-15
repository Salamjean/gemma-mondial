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
            $table->integer('admission_id')->nullable()->change();
            $table->integer('prestation_hospital_id')->nullable()->change();
            $table->integer('hospital_id')->nullable()->change();
            $table->integer('montant')->nullable()->change();
            $table->date('date_consultation')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->integer('admission_id')->nullable(false)->change();
            $table->integer('prestation_hospital_id')->nullable(false)->change();
            $table->integer('hospital_id')->nullable(false)->change();
            $table->integer('montant')->nullable(false)->change();
            $table->date('date_consultation')->nullable(false)->change();
        });
    }
};
