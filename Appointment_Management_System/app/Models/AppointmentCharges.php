<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AppointmentCharges extends Model
{
    use HasCode;

    protected $fillable = [
        'appointment_code',
        'charge_code',
        'charge_value',
    ];

    protected static $codePrefix = 'APC';
    public function getRouteKeyName()
    {
        return 'code';
    }

    // Example relations for AppointmentCharge

// Add belongsTo and hasMany relations
// according to your main Appointment, Service,
// User, Business and Charge models.
}
