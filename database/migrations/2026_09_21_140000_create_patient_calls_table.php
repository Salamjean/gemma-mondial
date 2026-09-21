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
        Schema::create('patient_calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id')->index();
            $table->unsignedBigInteger('consultation_id')->nullable()->index();
            $table->unsignedBigInteger('doctor_id')->nullable()->index();
            $table->unsignedBigInteger('patient_id')->nullable()->index();
            $table->string('patient_name');
            $table->string('doctor_name');
            $table->string('cabinet')->nullable();
            $table->string('service_name')->nullable();
            $table->integer('call_count')->default(1);
            $table->string('status')->default('calling'); // calling, in_consultation, completed, cancelled
            $table->timestamp('called_at')->useCurrent();
            $table->timestamps();

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_calls');
    }
};
