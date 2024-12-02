<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\TimeLog;

class TimesheetReport extends ReportService
{
    protected function getData(): array
    {
        $employee = Employee::with(['user', 'department'])
            ->findOrFail($this->employeeId);

        $timeLogs = TimeLog::where('employee_id', $employee->id)
            ->whereBetween('clock_in', [$this->startDate, $this->endDate])
            ->get()
            ->map(function ($log) {
                return [
                    'date' => $log->clock_in->format('Y-m-d'),
                    'clock_in' => $log->clock_in->format('H:i'),
                    'clock_out' => $log->clock_out ? $log->clock_out->format('H:i') : '-',
                    'duration' => $log->formatted_duration,
                    'status' => $log->status,
                    'is_late' => $log->is_late,
                    'left_early' => $log->left_early,
                    'notes' => $log->notes
                ];
            });

        return [
            'dateRange' => $this->getDateRange(),
            'employee' => [
                'name' => $employee->user->name,
                'number' => $employee->employee_number,
                'department' => $employee->department->name
            ],
            'timeLogs' => $timeLogs,
            'summary' => [
                'total_days' => $timeLogs->count(),
                'late_days' => $timeLogs->where('is_late', true)->count(),
                'early_departures' => $timeLogs->where('left_early', true)->count(),
                'total_hours' => round($timeLogs->sum(function ($log) {
                    return $log['duration'] ? explode(':', $log['duration'])[0] : 0;
                }), 1)
            ]
        ];
    }

    protected function getViewName(): string
    {
        return 'reports.timesheet';
    }

    protected function getFileName(): string
    {
        $employee = Employee::find($this->employeeId);
        return 'timesheet_' . $employee->employee_number;
    }

    protected function getHeaders(): array
    {
        return [
            'Date',
            'Clock In',
            'Clock Out',
            'Duration',
            'Status',
            'Late',
            'Left Early',
            'Notes'
        ];
    }
}
