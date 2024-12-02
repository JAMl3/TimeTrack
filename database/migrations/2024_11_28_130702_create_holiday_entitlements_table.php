<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('holiday_entitlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_days');
            $table->integer('days_taken')->default(0);
            $table->integer('days_remaining')->default(0);
            $table->year('year');
            $table->date('carry_over_expiry')->nullable();
            $table->integer('carry_over_days')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Ensure each user has only one entitlement record per year
            $table->unique(['user_id', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('holiday_entitlements');
    }
};
