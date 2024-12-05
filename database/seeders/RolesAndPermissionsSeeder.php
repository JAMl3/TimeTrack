<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'view reports',
            'view holidays',
            'create holidays',
            'approve holidays',
            'create entitlements',
            'view absences',
            'manage employees',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $hrRole = Role::create(['name' => 'hr']);
        $managerRole = Role::create(['name' => 'manager']);
        $employeeRole = Role::create(['name' => 'employee']);

        // Assign permissions to roles
        $adminRole->permissions()->attach(Permission::all());

        // HR role gets all permissions except some admin-specific ones
        $hrRole->permissions()->attach(Permission::all());

        $managerRole->permissions()->attach(Permission::whereIn('name', [
            'view reports',
            'view holidays',
            'approve holidays',
            'view absences',
            'manage employees'
        ])->get());

        $employeeRole->permissions()->attach(Permission::whereIn('name', [
            'view holidays',
            'create holidays',
        ])->get());

        // Assign admin role to the first user (assuming it's the admin)
        $adminUser = User::first();
        if ($adminUser) {
            $adminUser->roles()->attach($adminRole);
        }
    }
}
