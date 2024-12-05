<?php

namespace App\Services\Reports;

use App\Models\Employee;
use App\Models\AbsenceRecord;
use App\Models\User;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\GenerateReportRequest;

class AbsencePatternsReport extends ReportService
{
    public function __construct(GenerateReportRequest $request)
    {
        parent::__construct($request);

        // Ensure dates are properly parsed
        $this->startDate = Carbon::parse($request->start_date)->startOfDay();
        $this->endDate = Carbon::parse($request->end_date)->endOfDay();
        $this->departmentId = $request->department_id;

        // Debug logging
        Log::info('AbsencePatternsReport initialized', [
            'start_date' => $this->startDate->format('Y-m-d H:i:s'),
            'end_date' => $this->endDate->format('Y-m-d H:i:s'),
            'department_id' => $this->departmentId
        ]);
    }

    protected function getData(): array
    {
        $query = User::with(['employee.department', 'absenceRecords' => function ($query) {
            $query->whereBetween('date', [$this->startDate, $this->endDate])
                ->orderBy('date')
                ->with(['extensions', 'parentAbsence']);
        }])
            ->whereHas('employee');

        if ($this->departmentId) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        $users = $query->get();
        $patterns = [];
        $totalWorkingDays = $this->calculateWorkingDays();

        foreach ($users as $user) {
            // Get all absences including extensions
            $absences = $user->absenceRecords->filter(function ($absence) {
                return !$absence->isExtension();
            });

            if ($absences->isEmpty()) continue;

            // Analyze day of week patterns
            $dayPatterns = $absences->groupBy(function ($absence) {
                return Carbon::parse($absence->date)->format('l');
            })->map->count();

            // Analyze month patterns
            $monthPatterns = $absences->groupBy(function ($absence) {
                return Carbon::parse($absence->date)->format('F');
            })->map->count();

            // Analyze absence types
            $typePatterns = $absences->groupBy('type')->map->count();

            // Analyze reasons
            $reasonPatterns = $absences->groupBy('reason')
                ->map->count()
                ->sortDesc();

            // Calculate total days including extensions
            $totalDays = $absences->sum(function ($absence) {
                return 1 + $absence->extensions->count();
            });

            // Analyze consecutive absences
            $consecutiveInfo = $this->analyzeConsecutiveAbsences($absences);

            // Calculate frequencies
            $weeklyFrequency = round($totalDays / max(1, $this->startDate->diffInWeeks($this->endDate)), 1);
            $absenceRate = round(($totalDays / $totalWorkingDays) * 100, 1);

            $patterns[] = [
                'employee_name' => $user->name,
                'employee_number' => $user->employee->employee_number,
                'department' => $user->employee->department->name,
                'total_absences' => $absences->count(),
                'total_days' => $totalDays,
                'absence_rate' => $absenceRate,
                'most_common_day' => $dayPatterns->sortDesc()->keys()->first() ?? 'N/A',
                'day_frequency' => $dayPatterns->sortDesc()->first() ?? 0,
                'max_consecutive' => $consecutiveInfo['max_consecutive'],
                'consecutive_occurrences' => $consecutiveInfo['occurrences'],
                'weekly_frequency' => $weeklyFrequency,
                'most_common_type' => $typePatterns->sortDesc()->keys()->first() ?? 'N/A',
                'type_breakdown' => $typePatterns->toArray(),
                'most_common_reason' => $reasonPatterns->keys()->first() ?? 'No reason provided',
                'reason_frequency' => $reasonPatterns->first() ?? 0,
                'first_absence_date' => $absences->min('date'),
                'last_absence_date' => $absences->max('date'),
                'monday_rate' => ($dayPatterns['Monday'] ?? 0) / $totalWorkingDays * 100,
                'friday_rate' => ($dayPatterns['Friday'] ?? 0) / $totalWorkingDays * 100
            ];
        }

        // Sort patterns by total absences in descending order
        $patterns = collect($patterns)->sortByDesc('total_days')->values()->all();

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
        $dates = collect();

        // Flatten absences including extensions into individual dates
        foreach ($absences as $absence) {
            $dates->push($absence->date);
            foreach ($absence->extensions as $extension) {
                $dates->push($extension->date);
            }
        }

        $dates = $dates->sort();
        $consecutiveDays = 0;
        $maxConsecutive = 0;
        $occurrences = 0;
        $currentStreak = [];

        for ($i = 1; $i < $dates->count(); $i++) {
            $currentDate = Carbon::parse($dates[$i]);
            $previousDate = Carbon::parse($dates[$i - 1]);

            if ($currentDate->diffInDays($previousDate) === 1) {
                if ($consecutiveDays === 0) {
                    $currentStreak = [$previousDate];
                }
                $consecutiveDays++;
                $currentStreak[] = $currentDate;
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
                'monday_friday_pattern' => 0,
                'type_breakdown' => []
            ];
        }

        // Aggregate type breakdown across all employees
        $typeBreakdown = [];
        foreach ($patterns as $pattern) {
            foreach ($pattern['type_breakdown'] as $type => $count) {
                $typeBreakdown[$type] = ($typeBreakdown[$type] ?? 0) + $count;
            }
        }

        // Calculate employees with Monday/Friday patterns
        $mondayFridayPattern = collect($patterns)->filter(function ($pattern) {
            return ($pattern['monday_rate'] > 20 || $pattern['friday_rate'] > 20);
        })->count();

        return [
            'total_employees_with_absences' => $totalEmployees,
            'total_absence_days' => collect($patterns)->sum('total_days'),
            'average_absences' => round(collect($patterns)->avg('total_absences'), 1),
            'high_frequency_employees' => collect($patterns)->where('weekly_frequency', '>=', 0.5)->count(),
            'most_common_day' => collect($patterns)->pluck('most_common_day')->countBy()->sortDesc()->keys()->first() ?? 'N/A',
            'highest_absence_rate' => collect($patterns)->max('absence_rate'),
            'total_consecutive_patterns' => collect($patterns)->sum('consecutive_occurrences'),
            'monday_friday_pattern' => $mondayFridayPattern,
            'working_days_in_period' => $totalWorkingDays,
            'type_breakdown' => $typeBreakdown
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
            'Total Days (inc. Extensions)',
            'Absence Rate (%)',
            'Most Common Day',
            'Day Frequency',
            'Max Consecutive Days',
            'Consecutive Occurrences',
            'Weekly Frequency',
            'Most Common Type',
            'Most Common Reason',
            'Reason Frequency',
            'First Absence',
            'Last Absence',
            'Monday Rate (%)',
            'Friday Rate (%)'
        ];
    }
}
