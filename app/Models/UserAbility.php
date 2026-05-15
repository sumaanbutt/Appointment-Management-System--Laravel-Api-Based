<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAbility extends Model
{
    protected $fillable = [
        'business_code',
        'user_code',
        'ability',
        'status',
        'added_by_code',
    ];

    protected static $codePrefix = 'UAB';

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

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_code',
            'code'
        );
    }

    public function addedBy()
    {
        return $this->belongsTo(
            User::class,
            'added_by_code',
            'code'
        );
    }
}
