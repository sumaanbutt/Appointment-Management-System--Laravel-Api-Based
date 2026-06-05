<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class UserShiftSchedule extends Model
{
    use HasCode;

    protected $fillable =[
        'business_code',
        'user_code',
        'location_code',
        'employee_type',
        'working_day',
        'shift_start_time',
        'shift_end_time',
        'status',
        //'is_available',
    ];
    protected static $codePrefix = 'USS';

    public function getRouteKeyName()
    {
        return 'code';
    }


    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_code',
            'code'
        );
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
}
