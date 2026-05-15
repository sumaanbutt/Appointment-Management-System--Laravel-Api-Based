<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasCode;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens, HasCode;

    protected $fillable = [
        'code',
        'name',
        'email',
        'password',
        'organization_code',
        'business_code',
        'phone',
        'user_type',
        'status',
    ];
    protected static $codePrefix = 'USR';

    public function getRouteKeyName()
    {
        return 'code';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

//    public function employee(){
//        return $this->hasOne(Employee::class);
//    }
//
//    public function client(){
//        return $this->hasOne(Client::class);
//    }

    public function organization()
    {
        return $this->belongsTo(
            Organization::class,
            'organization_code',
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

    public function appointmentParticipants()
    {
        return $this->hasMany(
            AppointmentParticipant::class,
            'user_code',
            'code'
        );
    }

    public function appointmentHistories()
    {
        return $this->hasMany(
            AppointmentHistory::class,
            'user_code',
            'code'
        );
    }

    public function employeeShifts()
    {
        return $this->hasMany(
            UserShiftSchedule::class,
            'user_code',
            'code'
        );
    }

    public function userAbilities()
    {
        return $this->hasMany(
            UserAbility::class,
            'user_code',
            'code'
        );
    }
}
