<?php

namespace JarirAhmed\AuthMicroservice\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JarirAhmed\AuthMicroservice\Traits\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password',
        'email_verified_at', 'email_verification_token', 'email_verification_sent_at',
        'two_factor_enabled', 'two_factor_secret',
        'last_online_at', 'is_banned', 'ban_reason', 'notification_preferences',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'email_verification_token'];

    protected $casts = [
        'email_verified_at'           => 'datetime',
        'email_verification_sent_at'  => 'datetime',
        'last_online_at'              => 'datetime',
        'two_factor_enabled'          => 'boolean',
        'is_banned'                   => 'boolean',
        'notification_preferences'    => 'array',
    ];

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function personalAccessTokens()
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function lockout()
    {
        return $this->hasOne(AccountLockout::class);
    }

    public function dataExportRequests()
    {
        return $this->hasMany(DataExportRequest::class);
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function wantsNotification(string $type): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return $prefs[$type] ?? config("auth-microservice.notifications.{$type}", true);
    }
}
