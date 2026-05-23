<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class AvailabilitySlot extends Model
{
    use HasCode;

    protected static $codePrefix = 'AVS';

    public function getRouteKeyName()
    {
        return 'code';
    }


    public function location()
    {
        return $this->belongsTo(
            BusinessLocation::class,
            'location_code',
            'code'
        );
    }

    public function service()
    {
        return $this->belongsTo(
            Service::class,
            'service_code',
            'code'
        );
    }
}

/*
AppointmentParticipant
AppointmentCharges
AppointmentRecurrence
AppointmentService
Charge
LocationServices
Invoice

These models have no relations even no fillable except below

Invoice
Charge
AppointmentParticipant

these have fillables but no relation
