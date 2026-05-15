<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AppointmentParticipant extends Model
{
    use HasCode;

    protected $fillable = [
        'appointment_code',
        'business_code',
        'user_code',
        'role',
        'status',
    ];

    protected static $codePrefix = 'APP';

    public function getRouteKeyName()
    {
        return 'code';
    }

    // Example relations for AppointmentParticipant

// Add belongsTo and hasMany relations
// according to your main Appointment, Service,
// User, Business and Charge models.
}
