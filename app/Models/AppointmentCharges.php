<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentCharges extends Model
{

    protected static $codePrefix = 'APC';
    public function getRouteKeyName()
    {
        return 'code';
    }
}
