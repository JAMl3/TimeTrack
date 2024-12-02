<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('pin_changed')->default(false)->after('pin_code');
        });

        // Set existing employees' pin_changed to true
        DB::table('employees')->update(['pin_changed' => true]);
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('pin_changed');
        });
    }
};
