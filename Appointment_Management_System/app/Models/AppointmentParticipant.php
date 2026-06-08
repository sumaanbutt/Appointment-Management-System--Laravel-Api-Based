<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AppointmentParticipant extends Model
{
//    use HasCode;

    protected $fillable = [
        'appointment_code',
        'business_code',
        'user_code',
        'user_role',
        'user_type',
        'status',
    ];

//    protected static $codePrefix = 'APP';
//
//    public function getRouteKeyName()
//    {
//        return 'code';
//    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_code',
            'code'
        );
    }

    // Example relations for AppointmentParticipant

// Add belongsTo and hasMany relations
// according to your main Appointment, Service,
// User, Business and Charge models.
}
