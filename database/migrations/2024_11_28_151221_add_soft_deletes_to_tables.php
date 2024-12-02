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
        // Add soft deletes to holiday entitlements
        Schema::table('holiday_entitlements', function (Blueprint $table) {
            if (!Schema::hasColumn('holiday_entitlements', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Add soft deletes to holiday requests
        Schema::table('holiday_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('holiday_requests', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Add soft deletes to absence records
        Schema::table('absence_records', function (Blueprint $table) {
            if (!Schema::hasColumn('absence_records', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // Add soft deletes to time logs
        Schema::table('time_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('time_logs', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holiday_entitlements', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('holiday_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('absence_records', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('time_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
