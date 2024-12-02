<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TimeLog extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LEAVE = 'leave';

    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'is_late',
        'left_early',
        'notes',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'is_late' => 'boolean',
        'left_early' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('clock_out');
    }

    public function getDurationAttribute()
    {
        if (!$this->clock_out) {
            return null;
        }

        return $this->clock_in->diffInMinutes($this->clock_out);
    }

    public function getFormattedDurationAttribute()
    {
        $duration = $this->duration;
        if ($duration === null) {
            return 'Still clocked in';
        }

        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function checkShiftTiming()
    {
        $day = strtolower($this->clock_in->format('l')); // get day name in lowercase
        $shift = $this->employee->shift_pattern[$day] ?? null;

        if (!$shift) {
            return; // No shift defined for this day
        }

        // Check for late arrival
        $shift_start = Carbon::createFromFormat('H:i', $shift['start_time']);
        $today_start = Carbon::createFromFormat('H:i', $this->clock_in->format('H:i'));

        if ($today_start->greaterThan($shift_start)) {
            $this->is_late = true;
            $this->save();
        }

        // Check for early departure if clocked out
        if ($this->clock_out) {
            $shift_end = Carbon::createFromFormat('H:i', $shift['end_time']);
            $today_end = Carbon::createFromFormat('H:i', $this->clock_out->format('H:i'));

            if ($today_end->lessThan($shift_end)) {
                $this->left_early = true;
                $this->save();
            }
        }
    }

    public function getMinutesLate()
    {
        $day = strtolower($this->clock_in->format('l'));
        $shift = $this->employee->shift_pattern[$day] ?? null;

        if (!$shift) {
            return 0;
        }

        $shift_start = Carbon::createFromFormat('H:i', $shift['start_time']);
        $clock_in = Carbon::createFromFormat('H:i', $this->clock_in->format('H:i'));

        return $shift_start->diffInMinutes($clock_in);
    }
}
