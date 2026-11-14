<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute l'indicateur d'appartenance au UCP-FM.
     */
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (!Schema::hasColumn('hospitals', 'is_ucp_fm')) {
                $table->boolean('is_ucp_fm')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            if (Schema::hasColumn('hospitals', 'is_ucp_fm')) {
                $table->dropColumn('is_ucp_fm');
            }
        });
    }
};
