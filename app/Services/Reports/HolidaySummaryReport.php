<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\TimeLog;
use Illuminate\Support\Facades\Config;

class HolidaySummaryReport extends ReportService
{
    protected function getData(): array
    {
        $query = Employee::with(['user', 'department', 'timeLogs' => function ($query) {
            $query->whereBetween('clock_in', [$this->startDate, $this->endDate])
                ->where('status', 'leave');
        }]);

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        $employees = $query->get();
        $records = [];

        $defaultEntitlement = Config::get('company.default_holiday_entitlement', 25);

        foreach ($employees as $employee) {
            $leaveDays = $employee->timeLogs->count();
            $pendingRequests = $employee->timeLogs()
                ->where('status', 'leave')
                ->whereNull('clock_out')
                ->count();

            $records[] = [
                'employee_name' => $employee->user->name,
                'department' => $employee->department->name,
                'total_entitlement' => $defaultEntitlement,
                'days_taken' => $leaveDays,
                'days_remaining' => $defaultEntitlement - $leaveDays,
                'pending_requests' => $pendingRequests,
                'leave_pattern' => $this->getLeavePattern($employee->timeLogs)
            ];
        }

        return [
            'dateRange' => $this->getDateRange(),
            'records' => $records,
            'summary' => [
                'total_employees' => count($records),
                'total_days_taken' => collect($records)->sum('days_taken'),
                'total_pending' => collect($records)->sum('pending_requests')
            ]
        ];
    }

    protected function getLeavePattern($timeLogs): array
    {
        return $timeLogs->groupBy(function ($log) {
            return $log->clock_in->format('F');
        })->map->count()->toArray();
    }

    protected function getViewName(): string
    {
        return 'reports.holiday-summary';
    }

    protected function getFileName(): string
    {
        return 'holiday-summary-report';
    }

    protected function getHeaders(): array
    {
        return [
            'Employee',
            'Department',
            'Total Entitlement',
            'Days Taken',
            'Days Remaining',
            'Pending Requests',
            'Monthly Pattern'
        ];
    }
}
