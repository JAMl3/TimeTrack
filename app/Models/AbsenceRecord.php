<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsenceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'recorded_by',
        'date',
        'type',
        'reason',
        'notes',
        'parent_absence_id'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function parentAbsence()
    {
        return $this->belongsTo(AbsenceRecord::class, 'parent_absence_id');
    }

    public function extensions()
    {
        return $this->hasMany(AbsenceRecord::class, 'parent_absence_id');
    }

    public function isExtension(): bool
    {
        return !is_null($this->parent_absence_id);
    }

    public function hasExtensions(): bool
    {
        return $this->extensions()->exists();
    }
}
