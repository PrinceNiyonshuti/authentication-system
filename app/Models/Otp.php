<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Otp extends Model
{
    use HasUuids;

    protected $fillable = [
        'temporary_user_id',
        'code',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(TemporaryUser::class, 'temporary_user_id');
    }
}
