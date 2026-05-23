<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasCode;
    protected $fillable = [
        'business_code',
        'location_code',
        'client_code',
        'service_code',
        //'availability_slot_code',
        'appointment_start_date',
        'appointment_end_date',
        'start_time',
        'end_time',
        'status',
        'created_by_code',
        'notes',
        ];
    protected static $codePrefix = 'APT';

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

    public function client()
    {
        return $this->belongsTo(
            Client::class,
            'client_code',
            'code'
        );
    }

//    public function user() CreatedBY and ApprovedBY  2 Relations Pending
//    {
//        return $this->belongsTo(User::class);
//    }

    public function participants()
    {
        return $this->hasMany(
            AppointmentParticipant::class,
            'appointment_code',
            'code'
        );
    }

    public function histories()
    {
        return $this->hasMany(
            AppointmentHistory::class,
            'appointment_code',
            'code'
        );
    }

    public function appointment_charges()
    {
        return $this->hasMany(
            AppointmentCharges::class,
            'appointment_code',
            'code'
        );
    }

    public function invoice()
    {
        return $this->hasOne(
            Invoice::class,
            'appointment_code',
            'code'
        );
    }

    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by_code',
            'code'
        );
    }

    public function approvedBy()
    {
        return $this->belongsTo(
            User::class,
            'approved_by_code',
            'code'
        );
    }
}
