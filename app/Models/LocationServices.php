<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class LocationServices extends Model
{
    use HasCode;

    protected $fillable = [
        'business_code',
        'location_code',
        'service_code',
        'status',
    ];

    protected static $codePrefix = 'LSR';

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
}
