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
        Schema::table('time_logs', function (Blueprint $table) {
            $table->boolean('is_late')->default(false)->after('clock_out');
            $table->boolean('left_early')->default(false)->after('is_late');
            $table->text('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_logs', function (Blueprint $table) {
            $table->dropColumn(['is_late', 'left_early']);
            $table->string('notes')->nullable()->change();
        });
    }
};
