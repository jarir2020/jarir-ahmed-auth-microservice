<?php

namespace JarirAhmed\AuthMicroservice\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    protected $fillable = ['user_id', 'name', 'token', 'scopes', 'expires_at', 'last_used_at', 'revoked_at'];

    protected $casts = [
        'scopes'       => 'array',
        'expires_at'   => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(config('auth-microservice.user_model'));
    }

    public function isValid(): bool
    {
        return $this->revoked_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function hasScope(string $scope): bool
    {
        return empty($this->scopes) || in_array($scope, $this->scopes, true);
    }
}
