<?php

namespace App\Services\Reports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AbsencePatternsReport extends ReportService
{
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
        $patterns = [];
        $totalWorkingDays = $this->calculateWorkingDays();

        foreach ($employees as $employee) {
            $absences = $employee->timeLogs;
            if ($absences->isEmpty()) continue;

            // Analyze day of week patterns
            $dayPatterns = $absences->groupBy(function ($absence) {
                return $absence->clock_in->format('l');
            })->map->count();

            // Analyze month patterns
            $monthPatterns = $absences->groupBy(function ($absence) {
                return $absence->clock_in->format('F');
            })->map->count();

            // Analyze consecutive absences
            $consecutiveInfo = $this->analyzeConsecutiveAbsences($absences);

            // Calculate frequencies
            $weeklyFrequency = round($absences->count() / max(1, $this->startDate->diffInWeeks($this->endDate)), 1);
            $absenceRate = round(($absences->count() / $totalWorkingDays) * 100, 1);

            // Analyze absence reasons
            $reasonPatterns = $absences->groupBy('notes')
                ->map->count()
                ->sortDesc();

            $patterns[] = [
                'employee_name' => $employee->user->name,
                'employee_number' => $employee->employee_number,
                'department' => $employee->department->name,
                'total_absences' => $absences->count(),
                'absence_rate' => $absenceRate,
                'most_common_day' => $dayPatterns->sortDesc()->keys()->first() ?? 'N/A',
                'day_frequency' => $dayPatterns->sortDesc()->first() ?? 0,
                'max_consecutive' => $consecutiveInfo['max_consecutive'],
                'consecutive_occurrences' => $consecutiveInfo['occurrences'],
                'weekly_frequency' => $weeklyFrequency,
                'day_distribution' => $dayPatterns->toArray(),
                'month_distribution' => $monthPatterns->toArray(),
                'most_common_reason' => $reasonPatterns->keys()->first() ?? 'No reason provided',
                'reason_frequency' => $reasonPatterns->first() ?? 0,
                'first_absence_date' => $absences->first()->clock_in->format('Y-m-d'),
                'last_absence_date' => $absences->last()->clock_in->format('Y-m-d'),
                'monday_rate' => ($dayPatterns['Monday'] ?? 0) / $totalWorkingDays * 100,
                'friday_rate' => ($dayPatterns['Friday'] ?? 0) / $totalWorkingDays * 100
            ];
        }

        // Sort patterns by total absences in descending order
        $patterns = collect($patterns)->sortByDesc('total_absences')->values()->all();

        return [
            'dateRange' => $this->getDateRange(),
            'patterns' => $patterns,
            'summary' => $this->generateSummary($patterns, $totalWorkingDays)
        ];
    }

    protected function calculateWorkingDays(): int
    {
        $workingDays = 0;
        $current = $this->startDate->copy();

        while ($current <= $this->endDate) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    protected function analyzeConsecutiveAbsences(Collection $absences): array
    {
        $dates = $absences->pluck('clock_in')->sort();
        $consecutiveDays = 0;
        $maxConsecutive = 0;
        $occurrences = 0;
        $currentStreak = [];

        for ($i = 1; $i < $dates->count(); $i++) {
            if ($dates[$i]->diffInDays($dates[$i - 1]) === 1) {
                if ($consecutiveDays === 0) {
                    $currentStreak = [$dates[$i - 1]];
                }
                $consecutiveDays++;
                $currentStreak[] = $dates[$i];
                $maxConsecutive = max($maxConsecutive, $consecutiveDays + 1);
            } else {
                if ($consecutiveDays > 0) {
                    $occurrences++;
                }
                $consecutiveDays = 0;
                $currentStreak = [];
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

    protected function generateSummary(array $patterns, int $totalWorkingDays): array
    {
        $totalEmployees = count($patterns);
        if ($totalEmployees === 0) {
            return [
                'total_employees_with_absences' => 0,
                'average_absences' => 0,
                'high_frequency_employees' => 0,
                'most_common_day' => 'N/A',
                'highest_absence_rate' => 0,
                'total_consecutive_patterns' => 0,
                'monday_friday_pattern' => 0
            ];
        }

        $allDays = collect($patterns)->pluck('day_distribution')->reduce(function ($carry, $item) {
            foreach ($item as $day => $count) {
                $carry[$day] = ($carry[$day] ?? 0) + $count;
            }
            return $carry;
        }, []);

        // Calculate employees with Monday/Friday patterns
        $mondayFridayPattern = collect($patterns)->filter(function ($pattern) {
            return ($pattern['monday_rate'] > 20 || $pattern['friday_rate'] > 20);
        })->count();

        return [
            'total_employees_with_absences' => $totalEmployees,
            'average_absences' => round(collect($patterns)->avg('total_absences'), 1),
            'high_frequency_employees' => collect($patterns)->where('weekly_frequency', '>=', 0.5)->count(),
            'most_common_day' => collect($allDays)->sortDesc()->keys()->first() ?? 'N/A',
            'highest_absence_rate' => collect($patterns)->max('absence_rate'),
            'total_consecutive_patterns' => collect($patterns)->sum('consecutive_occurrences'),
            'monday_friday_pattern' => $mondayFridayPattern,
            'working_days_in_period' => $totalWorkingDays
        ];
    }

    protected function getViewName(): string
    {
        return 'reports.absence-patterns';
    }

    protected function getFileName(): string
    {
        return 'absence-patterns-report-' . $this->startDate->format('Y-m-d') . '-to-' . $this->endDate->format('Y-m-d');
    }

    protected function getHeaders(): array
    {
        return [
            'Employee Name',
            'Employee Number',
            'Department',
            'Total Absences',
            'Absence Rate (%)',
            'Most Common Day',
            'Day Frequency',
            'Max Consecutive Days',
            'Consecutive Occurrences',
            'Weekly Frequency',
            'Most Common Reason',
            'Reason Frequency',
            'First Absence',
            'Last Absence',
            'Monday Rate (%)',
            'Friday Rate (%)'
        ];
    }
}
