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
            if (!Schema::hasColumn('consultations', 'desired_date')) {
                $table->date('desired_date')->nullable()->after('date_consultation');
            }
            if (!Schema::hasColumn('consultations', 'desired_time')) {
                $table->string('desired_time')->nullable()->after('desired_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'desired_date')) {
                $table->dropColumn('desired_date');
            }
            if (Schema::hasColumn('consultations', 'desired_time')) {
                $table->dropColumn('desired_time');
            }
        });
    }
};
