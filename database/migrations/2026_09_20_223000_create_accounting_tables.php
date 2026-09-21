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
        // 1. Plan comptable
        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hospital_id')->nullable()->index();
                $table->string('account_number', 20)->index();
                $table->string('label');
                $table->string('type')->default('general'); // produit, charge, client, tresorerie, tiers, general
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Journaux comptables
        if (!Schema::hasTable('accounting_journals')) {
            Schema::create('accounting_journals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hospital_id')->nullable()->index();
                $table->string('code', 10)->index(); // VTE, CAI, BQ, OD
                $table->string('label');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Écritures comptables
        if (!Schema::hasTable('accounting_entries')) {
            Schema::create('accounting_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hospital_id')->index();
                $table->unsignedBigInteger('journal_id')->nullable()->index();
                $table->string('journal_code', 10)->nullable()->index();
                $table->date('entry_date')->index();
                $table->string('piece_number')->nullable()->index();
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('libelle');
                $table->string('status')->default('valide'); // brouillon, valide, cloture
                $table->boolean('is_exported_sage')->default(false)->index();
                $table->timestamp('exported_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Lignes d'écritures (Partie double : Débit / Crédit)
        if (!Schema::hasTable('accounting_entry_lines')) {
            Schema::create('accounting_entry_lines', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('accounting_entry_id')->index();
                $table->string('account_number', 20)->index();
                $table->string('account_label')->nullable();
                $table->string('third_party_code', 50)->nullable()->index(); // Code tiers assurance ou patient
                $table->string('libelle')->nullable();
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->string('lettering', 10)->nullable();
                $table->timestamps();

                $table->foreign('accounting_entry_id')
                    ->references('id')
                    ->on('accounting_entries')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_entry_lines');
        Schema::dropIfExists('accounting_entries');
        Schema::dropIfExists('accounting_journals');
        Schema::dropIfExists('chart_of_accounts');
    }
};
