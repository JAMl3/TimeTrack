<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Define a gate for each permission
        Gate::before(function (User $user) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });

        Gate::define('view reports', function (User $user) {
            return $user->hasPermission('view reports');
        });

        Gate::define('view holidays', function (User $user) {
            return $user->hasPermission('view holidays');
        });

        Gate::define('create holidays', function (User $user) {
            return $user->hasPermission('create holidays');
        });

        Gate::define('approve holidays', function (User $user) {
            return $user->hasPermission('approve holidays');
        });

        Gate::define('create entitlements', function (User $user) {
            return $user->hasPermission('create entitlements');
        });

        Gate::define('view absences', function (User $user) {
            return $user->hasPermission('view absences');
        });

        Gate::define('manage employees', function (User $user) {
            return $user->hasPermission('manage employees');
        });
    }
}
