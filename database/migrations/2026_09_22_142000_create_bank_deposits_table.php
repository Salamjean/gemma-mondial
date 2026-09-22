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
        if (!Schema::hasTable('bank_deposits')) {
            Schema::create('bank_deposits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hospital_id')->index();
                $table->unsignedBigInteger('accountant_id')->nullable()->index();
                $table->date('deposit_date')->index();
                $table->string('bank_account_number', 20)->default('512100')->index();
                $table->string('bank_name')->nullable();
                $table->string('source_type', 50)->default('caisse')->index(); // caisse, mobile_money, cheque, assurance, apport_associe, subvention_don, autre
                $table->string('source_account_number', 20)->default('531100')->index();
                $table->string('mode_depot', 50)->default('espece'); // espece, cheque, virement, mobile_money
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('reference_piece')->nullable(); // N° bordereau de remise / référence reçu
                $table->string('depositor_name')->nullable(); // Nom de la personne ayant déposé les fonds
                $table->string('label');
                $table->text('description')->nullable();
                $table->string('justificatif_path')->nullable();
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
        Schema::dropIfExists('bank_deposits');
    }
};
