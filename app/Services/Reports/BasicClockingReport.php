<?php

namespace App\Services\Reports;

use App\Models\TimeLog;

class BasicClockingReport extends ReportService
{
    protected function getData(): array
    {
        $query = TimeLog::with(['employee.user', 'employee.department'])
            ->whereBetween('clock_in', [$this->startDate, $this->endDate]);

        if ($this->departmentId) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        $timeLogs = $query->get()->map(function ($log) {
            return [
                'employee_name' => $log->employee->user->name,
                'department' => $log->employee->department->name,
                'date' => $log->clock_in->format('Y-m-d'),
                'clock_in' => $log->clock_in->format('H:i'),
                'clock_out' => $log->clock_out ? $log->clock_out->format('H:i') : '-',
                'duration' => $log->formatted_duration,
                'is_late' => $log->is_late,
                'left_early' => $log->left_early,
                'status' => $log->status,
                'notes' => $log->notes
            ];
        });

        return [
            'dateRange' => $this->getDateRange(),
            'timeLogs' => $timeLogs
        ];
    }

    protected function getViewName(): string
    {
        return 'reports.basic-clocking';
    }

    protected function getFileName(): string
    {
        return 'basic-clocking-report';
    }

    protected function getHeaders(): array
    {
        return [
            'Employee',
            'Department',
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
