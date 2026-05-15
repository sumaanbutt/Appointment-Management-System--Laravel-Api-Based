<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasCode
{
    public static function bootHasCode()
    {
        static::creating(function ($model) {
            if ($model->code) return;
            $prefix = $model::$codePrefix ?? '';
            $remainingLength = 9 - strlen($prefix);
            $model->code = $prefix . strtoupper(Str::random($remainingLength));
        });
    }
}
