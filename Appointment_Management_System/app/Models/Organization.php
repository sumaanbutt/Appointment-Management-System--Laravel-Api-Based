<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasCode;

//class Organization extends Model
class Organization extends Model
{
    use HasCode;

    protected static $codePrefix = 'ORG';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(
            Business::class,
            'organization_code',
            'code'
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class,
            'organization_code',
            'code'
        );
    }
}
