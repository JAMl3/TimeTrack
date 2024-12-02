<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HolidayEntitlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'total_days',
        'days_taken',
        'days_remaining',
        'year',
        'carry_over_expiry',
        'carry_over_days'
    ];

    protected $casts = [
        'year' => 'integer',
        'carry_over_expiry' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
