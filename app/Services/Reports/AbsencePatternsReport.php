<?php

namespace App\Services\Reports;

use App\Models\Employee;
use Illuminate\Support\Collection;

class AbsencePatternsReport extends ReportService
{
    protected function getData(): array
    {
        $query = Employee::with(['user', 'department', 'timeLogs' => function ($query) {
            $query->whereBetween('clock_in', [$this->startDate, $this->endDate])
                ->where('status', 'absent');
        }]);

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        $employees = $query->get();
        $patterns = [];

        foreach ($employees as $employee) {
            $absences = $employee->timeLogs;
            if ($absences->isEmpty()) continue;

            // Analyze day of week patterns
            $dayPatterns = $absences->groupBy(function ($absence) {
                return $absence->clock_in->format('l');
            })->map->count();

            // Analyze consecutive absences
            $consecutiveInfo = $this->analyzeConsecutiveAbsences($absences);

            // Calculate frequency
            $weeklyFrequency = round($absences->count() / max(1, $this->startDate->diffInWeeks($this->endDate)), 1);

            $patterns[] = [
                'employee_name' => $employee->user->name,
                'department' => $employee->department->name,
                'total_absences' => $absences->count(),
                'most_common_day' => $dayPatterns->sortDesc()->keys()->first() ?? 'N/A',
                'day_frequency' => $dayPatterns->sortDesc()->first() ?? 0,
                'max_consecutive' => $consecutiveInfo['max_consecutive'],
                'consecutive_occurrences' => $consecutiveInfo['occurrences'],
                'weekly_frequency' => $weeklyFrequency,
                'day_distribution' => $dayPatterns->toArray()
            ];
        }

        return [
            'dateRange' => $this->getDateRange(),
            'patterns' => $patterns,
            'summary' => $this->generateSummary($patterns)
        ];
    }

    protected function analyzeConsecutiveAbsences(Collection $absences): array
    {
        $dates = $absences->pluck('clock_in')->sort();
        $consecutiveDays = 0;
        $maxConsecutive = 0;
        $occurrences = 0;

        for ($i = 1; $i < $dates->count(); $i++) {
            if ($dates[$i]->diffInDays($dates[$i - 1]) === 1) {
                $consecutiveDays++;
                $maxConsecutive = max($maxConsecutive, $consecutiveDays + 1);
            } else {
                if ($consecutiveDays > 0) {
                    $occurrences++;
                }
                $consecutiveDays = 0;
            }
        }

        if ($consecutiveDays > 0) {
            $occurrences++;
        }

        return [
            'max_consecutive' => $maxConsecutive,
            'occurrences' => $occurrences
        ];
    }

    protected function generateSummary(array $patterns): array
    {
        $totalEmployees = count($patterns);
        if ($totalEmployees === 0) {
            return [
                'average_absences' => 0,
                'high_frequency_employees' => 0,
                'most_common_day' => 'N/A'
            ];
        }

        $allDays = collect($patterns)->pluck('day_distribution')->reduce(function ($carry, $item) {
            foreach ($item as $day => $count) {
                $carry[$day] = ($carry[$day] ?? 0) + $count;
            }
            return $carry;
        }, []);

        return [
            'average_absences' => round(collect($patterns)->avg('total_absences'), 1),
            'high_frequency_employees' => collect($patterns)->where('weekly_frequency', '>=', 0.5)->count(),
            'most_common_day' => collect($allDays)->sortDesc()->keys()->first() ?? 'N/A'
        ];
    }

    protected function getViewName(): string
    {
        return 'reports.absence-patterns';
    }

    protected function getFileName(): string
    {
        return 'absence-patterns-report';
    }

    protected function getHeaders(): array
    {
        return [
            'Employee',
            'Department',
            'Total Absences',
            'Most Common Day',
            'Day Frequency',
            'Max Consecutive Days',
            'Consecutive Occurrences',
            'Weekly Frequency'
        ];
    }
}
