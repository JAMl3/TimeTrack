<?php

namespace App\Services;

use App\Models\AbsenceRecord;
use App\Models\HolidayRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AbsencePatternDetector
{
    protected $absences;
    protected $holidays;
    protected $patterns = [];
    protected $recommendations = [];

    public function __construct(Collection $absences, Collection $holidays = null)
    {
        $this->absences = $absences->sortBy('date');
        $this->holidays = $holidays ?? collect();
    }

    public function analyze(): array
    {
        $this->detectDayOfWeekPatterns()
            ->detectHolidayProximity()
            ->detectFrequencyPatterns()
            ->detectConsecutiveDays()
            ->generateRecommendations();

        return [
            'patterns' => $this->patterns,
            'recommendations' => $this->recommendations
        ];
    }

    protected function detectDayOfWeekPatterns(): self
    {
        $dayCount = $this->absences->groupBy(function ($absence) {
            return Carbon::parse($absence->date)->format('l');
        })->map->count();

        $totalAbsences = $this->absences->count();
        if ($totalAbsences >= 3) {
            foreach ($dayCount as $day => $count) {
                $percentage = ($count / $totalAbsences) * 100;
                if ($percentage >= 30) { // Lowered threshold to catch more patterns
                    $this->patterns['day_of_week'][] = [
                        'day' => $day,
                        'count' => $count,
                        'percentage' => round($percentage, 1),
                        'severity' => $this->calculateSeverity($percentage, 30, 50),
                        'last_occurrence' => $this->absences
                            ->where('date', 'like', "%{$day}%")
                            ->sortByDesc('date')
                            ->first()
                            ->date ?? null
                    ];
                }
            }
        }

        return $this;
    }

    protected function detectHolidayProximity(): self
    {
        $nearHolidays = $this->absences->filter(function ($absence) {
            $absenceDate = Carbon::parse($absence->date);

            return $this->holidays->some(function ($holiday) use ($absenceDate) {
                $holidayStart = Carbon::parse($holiday->start_date);
                $holidayEnd = Carbon::parse($holiday->end_date);

                return $absenceDate->copy()->addDay()->eq($holidayStart) ||
                    $absenceDate->copy()->subDay()->eq($holidayEnd);
            });
        });

        if ($nearHolidays->isNotEmpty()) {
            $this->patterns['holiday_proximity'] = [
                'count' => $nearHolidays->count(),
                'percentage' => round(($nearHolidays->count() / $this->absences->count()) * 100, 1),
                'severity' => $this->calculateSeverity($nearHolidays->count(), 2, 4),
                'dates' => $nearHolidays->pluck('date')->toArray()
            ];
        }

        return $this;
    }

    protected function detectFrequencyPatterns(): self
    {
        // Check for high frequency in different time periods
        $periods = [
            '7' => 'week',
            '30' => 'month',
            '90' => 'quarter',
            '180' => 'half year'
        ];

        foreach ($periods as $days => $period) {
            $periodAbsences = $this->absences->filter(function ($absence) use ($days) {
                return Carbon::parse($absence->date)->isAfter(now()->subDays($days));
            });

            if ($periodAbsences->count() >= 2) {
                $this->patterns['frequency'][$period] = [
                    'period' => $period,
                    'days_analyzed' => $days,
                    'count' => $periodAbsences->count(),
                    'severity' => $this->calculateSeverity($periodAbsences->count(), 2, 4),
                    'dates' => $periodAbsences->pluck('date')->toArray()
                ];
            }
        }

        return $this;
    }

    protected function detectConsecutiveDays(): self
    {
        $dates = $this->absences->pluck('date')
            ->map(fn($date) => Carbon::parse($date))
            ->sort()
            ->values();

        $consecutiveGroups = collect();
        $currentGroup = collect([$dates->first()]);

        for ($i = 1; $i < $dates->count(); $i++) {
            if ($dates[$i]->copy()->subDay()->eq($dates[$i - 1])) {
                $currentGroup->push($dates[$i]);
            } else {
                if ($currentGroup->count() > 1) {
                    $consecutiveGroups->push($currentGroup);
                }
                $currentGroup = collect([$dates[$i]]);
            }
        }

        if ($currentGroup->count() > 1) {
            $consecutiveGroups->push($currentGroup);
        }

        if ($consecutiveGroups->isNotEmpty()) {
            $this->patterns['consecutive_days'] = $consecutiveGroups->map(function ($group) {
                return [
                    'start_date' => $group->first()->format('Y-m-d'),
                    'end_date' => $group->last()->format('Y-m-d'),
                    'days_count' => $group->count(),
                    'severity' => $this->calculateSeverity($group->count(), 2, 4)
                ];
            })->toArray();
        }

        return $this;
    }

    protected function generateRecommendations(): self
    {
        // Day of week patterns
        if (isset($this->patterns['day_of_week'])) {
            foreach ($this->patterns['day_of_week'] as $pattern) {
                if ($pattern['severity'] === 'high') {
                    $this->recommendations[] = [
                        'type' => 'extend_monitoring',
                        'description' => "High frequency of absences on {$pattern['day']}s ({$pattern['percentage']}%). Consider extending absence duration for future {$pattern['day']} absences.",
                        'severity' => 'high',
                        'action_required' => true
                    ];
                }
            }
        }

        // Consecutive days
        if (isset($this->patterns['consecutive_days'])) {
            foreach ($this->patterns['consecutive_days'] as $pattern) {
                if ($pattern['days_count'] >= 3) {
                    $this->recommendations[] = [
                        'type' => 'medical_review',
                        'description' => "Multiple consecutive absences detected ({$pattern['days_count']} days from {$pattern['start_date']} to {$pattern['end_date']}). Consider requesting medical documentation.",
                        'severity' => 'high',
                        'action_required' => true
                    ];
                }
            }
        }

        // Frequency patterns
        if (isset($this->patterns['frequency'])) {
            foreach ($this->patterns['frequency'] as $period => $pattern) {
                if ($pattern['severity'] === 'high') {
                    $this->recommendations[] = [
                        'type' => 'frequency_alert',
                        'description' => "High absence frequency in the last {$pattern['days_analyzed']} days ({$pattern['count']} absences). Consider scheduling a review meeting.",
                        'severity' => 'high',
                        'action_required' => true
                    ];
                }
            }
        }

        return $this;
    }

    protected function calculateSeverity(float $value, float $lowThreshold, float $highThreshold): string
    {
        if ($value >= $highThreshold) return 'high';
        if ($value >= $lowThreshold) return 'medium';
        return 'low';
    }
}
