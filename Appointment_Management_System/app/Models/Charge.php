<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCode;

class Charge extends Model
{
    use HasCode;

    protected $fillable = [
        'business_code',
        'name',
        'description',
        'charge_uom',
        'charge_value',
        'status',
        'auto_apply',
    ];

    protected $casts = [
        'auto_apply' => 'boolean',
    ];

    protected static $codePrefix = 'CHG';

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
}
