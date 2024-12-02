<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ClockRecord extends Model
{
    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getHoursWorkedAttribute(): float|string
    {
        if (!$this->clock_out) {
            return 'N/A';
        }

        return round($this->clock_in->diffInMinutes($this->clock_out) / 60, 2);
    }

    public function getMinutesLateAttribute(): int
    {
        $startTime = Carbon::parse('09:00:00');
        return max(0, $this->clock_in->diffInMinutes($startTime));
    }
}
