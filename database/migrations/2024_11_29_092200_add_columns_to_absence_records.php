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
        Schema::table('absence_records', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->enum('type', ['sick', 'personal', 'other']);
            $table->string('reason');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absence_records', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['recorded_by']);
            $table->dropColumn(['user_id', 'recorded_by', 'date', 'type', 'reason', 'notes']);
        });
    }
};
