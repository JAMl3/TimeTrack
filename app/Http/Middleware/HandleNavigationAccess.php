<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;

class HandleNavigationAccess
{
    protected $navigationItems = [
        'dashboard' => [
            'name' => 'Dashboard',
            'route' => 'dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'permission' => 'view-dashboard'
        ],
        'employees' => [
            'name' => 'Employees',
            'route' => 'employees.index',
            'icon' => 'fas fa-users',
            'permission' => 'manage-employees'
        ],
        'reports' => [
            'name' => 'Reports',
            'route' => 'reports.index',
            'icon' => 'fas fa-chart-bar',
            'permission' => 'view-reports',
            'children' => [
                'basic-clocking' => [
                    'name' => 'Basic Clocking',
                    'route' => 'reports.index',
                    'params' => ['type' => 'basic-clocking']
                ],
                'department-overview' => [
                    'name' => 'Department Overview',
                    'route' => 'reports.index',
                    'params' => ['type' => 'department-overview']
                ],
                'timesheet' => [
                    'name' => 'Timesheet',
                    'route' => 'reports.index',
                    'params' => ['type' => 'timesheet']
                ],
                'holiday-summary' => [
                    'name' => 'Holiday Summary',
                    'route' => 'reports.index',
                    'params' => ['type' => 'holiday-summary']
                ],
                'absence-patterns' => [
                    'name' => 'Absence Patterns',
                    'route' => 'reports.index',
                    'params' => ['type' => 'absence-patterns']
                ]
            ]
        ],
        'holidays' => [
            'name' => 'Holidays',
            'route' => 'holidays.dashboard',
            'icon' => 'fas fa-umbrella-beach',
            'permission' => 'view-holidays'
        ],
        'absences' => [
            'name' => 'Absences',
            'route' => 'absences.index',
            'icon' => 'fas fa-user-clock',
            'permission' => 'view-absences'
        ],
        'settings' => [
            'name' => 'Settings',
            'route' => 'settings.index',
            'icon' => 'fas fa-cog',
            'permission' => 'manage-settings'
        ]
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $accessibleNavigation = [];

        if ($user) {
            foreach ($this->navigationItems as $key => $item) {
                if (Gate::allows($item['permission'])) {
                    $accessibleNavigation[$key] = $item;

                    // Handle children if they exist
                    if (isset($item['children'])) {
                        foreach ($item['children'] as $childKey => $child) {
                            if (!Gate::allows($item['permission'])) {
                                unset($accessibleNavigation[$key]['children'][$childKey]);
                            }
                        }
                    }
                }
            }
        }

        // Share the navigation data with all views
        View::share('navigation', $accessibleNavigation);
        View::share('currentRoute', $request->route()->getName());

        return $next($request);
    }
}
