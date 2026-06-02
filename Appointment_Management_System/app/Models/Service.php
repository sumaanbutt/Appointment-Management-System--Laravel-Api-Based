<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{

protected $fillable =
    ['business_code',
    'service_name',
    'description',
    'time_duration',
    'charges',
    'cost',
    'currency',
    'status',
    'duration_uom',];

    use HasCode;
    protected static $codePrefix = 'SER';

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

    public function locations()
    {
        return $this->belongsToMany(
            BusinessLocation::class,
            'location_services',
            'service_code',
            'location_code',
            'code',
            'code'
        )->withPivot('status')
            ->withTimestamps();
    }

    public function locationServices()
    {
        return $this->hasMany(
            LocationServices::class,
            'service_code',
            'code'
        );
    }

    public function appointments()
    {
        return $this->belongsToMany(
            Appointment::class,
            'appointment_services', // pivot table
            'service_code',
            'appointment_code',
            'code',
            'code'
        );
    }

    public function appointmentRecurrences()
    {
        return $this->hasMany(
            AppointmentRecurrence::class,
            'service_code',
            'code'
        );
    }

    // public function appointmentDiscounts()
    // {
    //     return $this->hasMany(
    //         AppointmentDiscountController::class,
    //         'service_code',
    //         'code'
    //     );
    // }
}

