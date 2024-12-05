<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absence_records', function (Blueprint $table) {
            $table->foreignId('parent_absence_id')->nullable()->after('id')
                ->references('id')->on('absence_records')
                ->onDelete('set null');
            $table->index(['user_id', 'date']); // Add index for faster pattern analysis
        });
    }

    public function down(): void
    {
        Schema::table('absence_records', function (Blueprint $table) {
            $table->dropForeign(['parent_absence_id']);
            $table->dropColumn('parent_absence_id');
            $table->dropIndex(['user_id', 'date']);
        });
    }
};
