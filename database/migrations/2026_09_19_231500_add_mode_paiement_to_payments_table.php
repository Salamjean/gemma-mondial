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
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'mode_paiement')) {
                    $table->string('mode_paiement')->default('espece')->nullable()->after('status');
                }
                if (!Schema::hasColumn('payments', 'operateur_mobile')) {
                    $table->string('operateur_mobile')->nullable()->after('mode_paiement');
                }
                if (!Schema::hasColumn('payments', 'reference_paiement')) {
                    $table->string('reference_paiement')->nullable()->after('operateur_mobile');
                }
            });
        }

        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (!Schema::hasColumn('admissions', 'mode_paiement')) {
                    $table->string('mode_paiement')->default('espece')->nullable()->after('statut_validation');
                }
                if (!Schema::hasColumn('admissions', 'operateur_mobile')) {
                    $table->string('operateur_mobile')->nullable()->after('mode_paiement');
                }
                if (!Schema::hasColumn('admissions', 'reference_paiement')) {
                    $table->string('reference_paiement')->nullable()->after('operateur_mobile');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'reference_paiement')) {
                    $table->dropColumn('reference_paiement');
                }
                if (Schema::hasColumn('payments', 'operateur_mobile')) {
                    $table->dropColumn('operateur_mobile');
                }
                if (Schema::hasColumn('payments', 'mode_paiement')) {
                    $table->dropColumn('mode_paiement');
                }
            });
        }

        if (Schema::hasTable('admissions')) {
            Schema::table('admissions', function (Blueprint $table) {
                if (Schema::hasColumn('admissions', 'reference_paiement')) {
                    $table->dropColumn('reference_paiement');
                }
                if (Schema::hasColumn('admissions', 'operateur_mobile')) {
                    $table->dropColumn('operateur_mobile');
                }
                if (Schema::hasColumn('admissions', 'mode_paiement')) {
                    $table->dropColumn('mode_paiement');
                }
            });
        }
    }
};
