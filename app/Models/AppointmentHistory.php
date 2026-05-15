<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentHistory extends Model
{
    protected static $codePrefix = 'APH';

    public function getRouteKeyName()
    {
        return 'code';
    }
}
