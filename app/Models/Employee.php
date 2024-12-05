<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TimeLog;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_number',
        'department_id',
        'position',
        'manager_id',
        'start_date',
        'pin_code',
        'pin_changed',
        'status',
        'shift_pattern',
        'absence_extension_days',
    ];

    protected $casts = [
        'start_date' => 'date',
        'pin_changed' => 'boolean',
        'shift_pattern' => 'array',
        'absence_extension_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function isCurrentlyWorking()
    {
        return TimeLog::where('employee_id', $this->id)
            ->whereNull('clock_out')
            ->exists();
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function getLatestTimeLog()
    {
        return $this->timeLogs()->latest()->first();
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function managedEmployees()
    {
        return $this->hasMany(Employee::class, 'manager_id', 'user_id');
    }

    public function isManager()
    {
        return $this->managedEmployees()->exists();
    }

    /**
     * Get the shift timing for a specific day
     *
     * @param string $day The day of the week in lowercase (e.g., 'monday')
     * @return array|null Returns array with start and end times or null if no shift
     */
    public function getShiftForDay($day = null)
    {
        $day = $day ?? strtolower(now()->format('l'));
        return $this->shift_pattern[$day] ?? null;
    }

    public function setPinCodeAttribute($value)
    {
        $this->attributes['pin_code'] = bcrypt($value);
    }

    public function clockRecords()
    {
        return $this->timeLogs();
    }
}
