<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentRecurrence extends Model
{
    protected static $codePrefix = 'APR';

    public function getRouteKeyName()
    {
        return 'code';
    }
}
