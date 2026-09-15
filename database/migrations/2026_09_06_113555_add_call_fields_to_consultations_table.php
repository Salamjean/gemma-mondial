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
            $table->boolean('is_call_active')->default(false)->after('status');
            $table->string('call_channel')->nullable()->after('is_call_active');
            $table->timestamp('call_started_at')->nullable()->after('call_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['is_call_active', 'call_channel', 'call_started_at']);
        });
    }
};
