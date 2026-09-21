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
        // 1. Table des Dépenses (Charges)
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hospital_id')->index();
                $table->unsignedBigInteger('accountant_id')->nullable()->index();
                $table->date('expense_date')->index();
                $table->string('account_number', 20)->default('601100')->index();
                $table->string('label');
                $table->string('beneficiaire')->nullable();
                $table->string('mode_paiement')->default('espece'); // espece, banque, virement, cheque, wave, orange_money, mtn
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('piece_number')->nullable();
                $table->text('description')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        // 2. Table des Règlements / Recouvrements d'Assurances
        if (!Schema::hasTable('insurance_settlements')) {
            Schema::create('insurance_settlements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hospital_id')->index();
                $table->unsignedBigInteger('type_assurance_id')->index();
                $table->unsignedBigInteger('accountant_id')->nullable()->index();
                $table->date('settlement_date')->index();
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('mode_paiement')->default('virement'); // virement, cheque, espece, mobile_money
                $table->string('reference_piece')->nullable();
                $table->text('note')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_settlements');
        Schema::dropIfExists('expenses');
    }
};
