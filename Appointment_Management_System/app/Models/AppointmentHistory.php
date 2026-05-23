<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AppointmentHistory extends Model
{
    use HasCode;

    protected $fillable = [
        'appointment_code',
        'business_code',
        'action',
        'changed_by_code',
        'old_values',
        'new_values',
    ];

    protected static $codePrefix = 'APH';

    public function getRouteKeyName()
    {
        return 'code';
    }

    // Example relations for AppointmentHistory

// Add belongsTo and hasMany relations
// according to your main Appointment, Service,
// User, Business and Charge models.
}
