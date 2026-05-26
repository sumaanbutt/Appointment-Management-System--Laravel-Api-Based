<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasCode;
    protected static $codePrefix = 'BUS';
    protected $fillable = [
        'organization_code',
        'user_code',
        'name',
        'email',
        'phone',
        'description',
        'timezone',
        'status',
    ];

    public function getRouteKeyName()
    {
        return 'code';
    }


    public function organization()
    {
        return $this->belongsTo(
            Organization::class,
            'organization_code',
            'code'
        );
    }

    public function owner(){
        return $this->belongsTo
        (User::class,
        'owner_id',
        'code'
        );
    }

    public function locations()
    {
        return $this->hasMany(
            BusinessLocation::class,
            'business_code',
            'code'
        );
    }

    public function users()
    {
        return $this->hasMany(
            User::class,
            'business_code',
            'code'
        );
    }

    public function clients()
    {
        return $this->hasMany(
            Client::class,
            'business_code',
            'code'
        );
    }

    public function services()
    {
        return $this->hasMany(
            Service::class,
            'business_code',
            'code'
        );
    }

    public function charges()
    {
        return $this->hasMany(
            Charge::class,
            'business_code',
            'code'
        );
    }

    public function appointments()
    {
        return $this->hasMany(
            Appointment::class,
            'business_code',
            'code'
        );
    }

//    public function employees(){
//        return $this->hasMany(Employee::class);
//    }
}
/*Business belongsTo Organization
Business belongsTo Owner(User)

Business hasMany Users
Business hasMany Locations
Business hasMany Services
Business hasMany Appointments
Business hasMany Charges
Business hasMany Clients*/
