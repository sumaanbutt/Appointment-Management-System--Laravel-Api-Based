<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{

    protected static $codePrefix = 'APS';

    public function getRouteKeyName()
    {
        return 'code';
    }
}
