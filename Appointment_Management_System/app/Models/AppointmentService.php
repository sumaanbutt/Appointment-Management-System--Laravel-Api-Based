<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{
    use HasCode;

    protected $fillable = [
        'appointment_code',
        'service_code',
        'quantity',
        'price',
    ];

    protected static $codePrefix = 'APS';

    public function getRouteKeyName()
    {
        return 'code';
    }

    // Example relations for AppointmentService

// Add belongsTo and hasMany relations
// according to your main Appointment, Service,
// User, Business and Charge models.
}
