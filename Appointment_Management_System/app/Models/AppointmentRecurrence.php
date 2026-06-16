<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AppointmentRecurrence extends Model
{
    use HasCode;

    protected $fillable = [
        'business_code',
        'appointment_code',
        'recurrence_uom',
        'recurrence_value',
        'auto_cancel_after_days',
        'reschedule_after_days',
        'status'
    ];

    protected static $codePrefix = 'APR';

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function business()
    {
        return $this->belongsTo(
            Business::class,
            'business_code',
            'code'
        );
    }

    // Example relations for AppointmentRecurrence

// Add belongsTo and hasMany relations
// according to your main Appointment, Service,
// User, Business and Charge models.
}
