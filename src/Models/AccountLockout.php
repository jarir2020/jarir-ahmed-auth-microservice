<?php

namespace JarirAhmed\AuthMicroservice\Models;

use Illuminate\Database\Eloquent\Model;

class AccountLockout extends Model
{
    protected $fillable = ['user_id', 'failed_attempts', 'locked_until', 'unlocked_at', 'unlocked_by'];

    protected $casts = [
        'locked_until' => 'datetime',
        'unlocked_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(config('auth-microservice.user_model'));
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null
            && $this->locked_until->isFuture()
            && $this->unlocked_at === null;
    }
}
