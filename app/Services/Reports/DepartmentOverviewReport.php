<?php

namespace App\Services\Reports;

use App\Models\Department;
use Carbon\Carbon;

class DepartmentOverviewReport extends ReportService
{
    protected function getData(): array
    {
        $query = Department::with(['employees.user', 'employees.timeLogs' => function ($query) {
            $query->whereBetween('clock_in', [$this->startDate, $this->endDate]);
        }]);

        if ($this->departmentId) {
            $query->where('id', $this->departmentId);
        }

        $departments = $query->get();
        $departmentStats = [];

        foreach ($departments as $department) {
            $totalEmployees = $department->employees->count();
            $totalPresent = 0;
            $totalLate = 0;
            $totalEarly = 0;
            $totalHours = 0;

            foreach ($department->employees as $employee) {
                $logs = $employee->timeLogs->filter(function ($log) {
                    return $log->status === 'present';
                });

                $totalPresent += $logs->count();
                $totalLate += $logs->where('is_late', true)->count();
                $totalEarly += $logs->where('left_early', true)->count();
                $totalHours += $logs->sum('duration') / 60;
            }

            $workingDays = $this->startDate->diffInDays($this->endDate) + 1;

            $departmentStats[] = [
                'department' => $department->name,
                'total_employees' => $totalEmployees,
                'attendance_rate' => $totalEmployees > 0 ?
                    round(($totalPresent / ($totalEmployees * $workingDays)) * 100, 2) : 0,
                'late_rate' => $totalPresent > 0 ?
                    round(($totalLate / $totalPresent) * 100, 2) : 0,
                'early_departure_rate' => $totalPresent > 0 ?
                    round(($totalEarly / $totalPresent) * 100, 2) : 0,
                'average_hours' => $totalPresent > 0 ?
                    round($totalHours / $totalPresent, 2) : 0
            ];
        }

        return [
            'dateRange' => $this->getDateRange(),
            'departments' => $departmentStats
        ];
    }

    protected function getViewName(): string
    {
        return 'reports.department-overview';
    }

    protected function getFileName(): string
    {
        return 'department-overview-report';
    }

    protected function getHeaders(): array
    {
        return [
            'Department',
            'Total Employees',
            'Attendance Rate (%)',
            'Late Rate (%)',
            'Early Departure Rate (%)',
            'Average Hours/Day'
        ];
    }
}
