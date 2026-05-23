<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AppointmentRecurrence extends Model
{
    use HasCode;

    protected $fillable = [
        'business_code',
        'service_code',
        'location_code',
        'recurrence_uom',
        'recurrence_value',
    ];

    protected static $codePrefix = 'APR';

    public function getRouteKeyName()
    {
        return 'code';
    }

    // Example relations for AppointmentRecurrence

// Add belongsTo and hasMany relations
// according to your main Appointment, Service,
// User, Business and Charge models.
}
