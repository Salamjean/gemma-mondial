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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'fcm_token')) {
                    $table->text('fcm_token')->nullable()->after('password');
                }
                if (!Schema::hasColumn('users', 'device_type')) {
                    $table->string('device_type')->nullable()->after('fcm_token'); // android, ios, web
                }
            });
        }

        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (!Schema::hasColumn('patients', 'fcm_token')) {
                    $table->text('fcm_token')->nullable()->after('telephone');
                }
                if (!Schema::hasColumn('patients', 'device_type')) {
                    $table->string('device_type')->nullable()->after('fcm_token');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'device_type')) {
                    $table->dropColumn('device_type');
                }
                if (Schema::hasColumn('users', 'fcm_token')) {
                    $table->dropColumn('fcm_token');
                }
            });
        }

        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (Schema::hasColumn('patients', 'device_type')) {
                    $table->dropColumn('device_type');
                }
                if (Schema::hasColumn('patients', 'fcm_token')) {
                    $table->dropColumn('fcm_token');
                }
            });
        }
    }
};
