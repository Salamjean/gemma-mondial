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
        Schema::create('ministeres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('reference')->unique()->nullable();
            $table->string('nom_direction')->nullable(); // Ex: Direction Générale de la Santé Publique
            $table->string('contact')->nullable();
            $table->string('fonction')->nullable(); // Ex: Responsable Statistiques / Inspecteur
            $table->string('img_url')->nullable();
            $table->tinyInteger('status')->default(0); // 0: Actif, 1: Suspendu
            $table->tinyInteger('delete')->default(0); // 0: Non supprimé, 1: Supprimé
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministeres');
    }
};
