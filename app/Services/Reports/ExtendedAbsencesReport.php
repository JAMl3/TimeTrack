<?php

namespace App\Services\Reports;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExtendedAbsencesReport extends ReportService
{
    protected const EXTENDED_ABSENCE_THRESHOLD = 5; // 5 consecutive days

    protected function getData(): array
    {
        $query = Employee::with(['user', 'department', 'timeLogs' => function ($query) {
            $query->whereBetween('clock_in', [$this->startDate, $this->endDate])
                ->where('status', 'absent')
                ->orderBy('clock_in');
        }]);

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        $employees = $query->get();
        $extendedAbsences = [];

        foreach ($employees as $employee) {
            $absencePeriods = $this->findExtendedAbsencePeriods($employee->timeLogs);

            if (!empty($absencePeriods)) {
                $extendedAbsences[] = [
                    'employee_name' => $employee->user->name,
                    'employee_number' => $employee->employee_number,
                    'department' => $employee->department->name,
                    'absence_periods' => $absencePeriods,
                    'total_extended_days' => collect($absencePeriods)->sum('duration'),
                    'frequency' => count($absencePeriods),
                    'average_duration' => round(collect($absencePeriods)->avg('duration'), 1)
                ];
            }
        }

        return [
            'dateRange' => $this->getDateRange(),
            'threshold' => self::EXTENDED_ABSENCE_THRESHOLD,
            'absences' => $extendedAbsences,
            'summary' => $this->generateSummary($extendedAbsences)
        ];
    }

    protected function findExtendedAbsencePeriods(Collection $absences): array
    {
        if ($absences->isEmpty()) {
            return [];
        }

        $periods = [];
        $currentStart = null;
        $previousDate = null;
        $consecutiveDays = 1;

        foreach ($absences as $absence) {
            $currentDate = $absence->clock_in->startOfDay();

            if ($currentStart === null) {
                $currentStart = $currentDate;
            } elseif ($previousDate !== null) {
                if ($currentDate->diffInDays($previousDate) === 1) {
                    $consecutiveDays++;
                } else {
                    if ($consecutiveDays >= self::EXTENDED_ABSENCE_THRESHOLD) {
                        $periods[] = [
                            'start_date' => $currentStart->format('Y-m-d'),
                            'end_date' => $previousDate->format('Y-m-d'),
                            'duration' => $consecutiveDays,
                            'reason' => $absence->notes ?? 'No reason provided'
                        ];
                    }
                    $currentStart = $currentDate;
                    $consecutiveDays = 1;
                }
            }

            $previousDate = $currentDate;
        }

        // Check the last period
        if ($consecutiveDays >= self::EXTENDED_ABSENCE_THRESHOLD) {
            $periods[] = [
                'start_date' => $currentStart->format('Y-m-d'),
                'end_date' => $previousDate->format('Y-m-d'),
                'duration' => $consecutiveDays,
                'reason' => $absences->last()->notes ?? 'No reason provided'
            ];
        }

        return $periods;
    }

    protected function generateSummary(array $extendedAbsences): array
    {
        if (empty($extendedAbsences)) {
            return [
                'total_employees' => 0,
                'total_periods' => 0,
                'average_duration' => 0,
                'longest_absence' => 0
            ];
        }

        $allPeriods = collect($extendedAbsences)->pluck('absence_periods')->flatten(1);

        return [
            'total_employees' => count($extendedAbsences),
            'total_periods' => $allPeriods->count(),
            'average_duration' => round($allPeriods->avg('duration'), 1),
            'longest_absence' => $allPeriods->max('duration')
        ];
    }

    protected function getViewName(): string
    {
        return 'reports.extended-absences';
    }

    protected function getFileName(): string
    {
        return 'extended-absences-report';
    }

    protected function getHeaders(): array
    {
        return [
            'Employee',
            'Employee Number',
            'Department',
            'Start Date',
            'End Date',
            'Duration (Days)',
            'Reason'
        ];
    }
}
