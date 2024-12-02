<?php

namespace App\Console\Commands;

use App\Models\Department;
use Illuminate\Console\Command;

class CreateITDepartment extends Command
{
    protected $signature = 'department:create-it';
    protected $description = 'Create the IT department';

    public function handle()
    {
        Department::create([
            'name' => 'Information Technology',
            'code' => 'IT',
            'description' => 'Information Technology Department',
        ]);

        $this->info('IT Department created successfully!');
    }
}
