<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        // Create departments table if it doesn't exist
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $departments = [
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'HR department'],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'IT department'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Finance department'],
            ['name' => 'Marketing', 'code' => 'MKT', 'description' => 'Marketing department'],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Operations department'],
            ['name' => 'Sales', 'code' => 'SLS', 'description' => 'Sales department'],
            ['name' => 'Customer Service', 'code' => 'CS', 'description' => 'Customer service department'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['code' => $department['code']],
                [
                    'name' => $department['name'],
                    'description' => $department['description']
                ]
            );
        }
    }
}
