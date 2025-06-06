<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TemporaryUser extends Model
{
    //
    protected $table = 'temporary_users';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'honorific_title',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'email',
        'phone_number',
        'nationality',
        'profile_picture',
        'current_step',
        'country_of_residence',
        'city',
        'postal_code',
        'apartment_name',
        'room_number',
        'is_expatriate',
        'password',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function otps()
    {
        return $this->hasMany(Otp::class, 'temporary_user_id');
    }

}
