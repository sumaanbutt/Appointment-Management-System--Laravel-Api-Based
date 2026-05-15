<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentParticipant extends Model
{
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
}
