<?php

namespace JarirAhmed\AuthMicroservice\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'country', 'city',
        'device', 'os', 'browser', 'user_agent', 'is_suspicious',
    ];

    protected $casts = ['is_suspicious' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(config('auth-microservice.user_model'));
    }
}
