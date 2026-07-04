<?php

namespace JarirAhmed\AuthMicroservice\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorBackupCode extends Model
{
    protected $fillable = ['user_id', 'code', 'used_at'];

    protected $casts = ['used_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(config('auth-microservice.user_model'));
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }
}
