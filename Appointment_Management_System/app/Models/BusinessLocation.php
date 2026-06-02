<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class BusinessLocation extends Model
{

    use HasCode;
    protected static $codePrefix = 'BSL';

    protected $fillable =[

        'business_code',
        'location_type',

        'address',
        'apartment',
        'street',

        'city',
        'state',
        'postal_code',
        'country',

        'status'
    ];

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

    public function locationServices()
    {
        return $this->hasMany(
            LocationServices::class,
            'location_code',
            'code'
        );
    }

    public function employeeShifts()
    {
        return $this->hasMany(
            UserShiftSchedule::class,
            'location_code',
            'code'
        );
    }

    public function appointments()
    {
        return $this->hasMany(
            Appointment::class,
            'location_code',
            'code'
        );
    }

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'location_services',
            'location_code',
            'service_code',
            'code',
            'code'
        )->withPivot('status')
            ->withTimestamps();
    }
}
