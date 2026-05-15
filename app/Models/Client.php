<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasCode;

    protected $fillable = [
        'business_code',
        'user_code',
        'address',
        'city',
        'state',
        'country',
    ];
    protected static $codePrefix = 'CLT';

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

    public function appointments()
    {
        return $this->hasMany(
            Appointment::class,
            'client_code',
            'code'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_code',
            'code'
        );
    }
}
