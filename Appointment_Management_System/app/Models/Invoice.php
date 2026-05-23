<?php

namespace App\Models;

use App\Traits\HasCode;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasCode;

    protected $fillable = [
        'business_code',
        'appointment_code',
        'subtotal',
        'total',
        'status',
        'invoice_date',
        'updated_by_code',
    ];

    protected static $codePrefix = 'INV';

    public function getRouteKeyName()
    {
        return 'code';
    }
}
