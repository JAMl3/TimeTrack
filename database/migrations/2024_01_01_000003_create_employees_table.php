<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('employee_number')->unique();
                $table->string('phone')->nullable();
                $table->string('company')->nullable();
                $table->foreignId('department_id')->constrained();
                $table->string('position');
                $table->string('branch')->nullable();
                $table->date('start_date');
                $table->json('shift_pattern')->nullable();
                $table->string('pin_code');
                $table->string('status')->default('active');
                $table->string('employment_status')->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
