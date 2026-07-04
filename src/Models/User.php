<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;
use JarirAhmed\AuthMicroservice\Traits\TwoFactorAuthenticatable;
use JarirAhmed\AuthMicroservice\Config;

class User extends Model
{
    protected static string $table = 'users';

    protected static array $casts = [
        'email_verified_at'          => 'datetime',
        'email_verification_sent_at' => 'datetime',
        'last_online_at'             => 'datetime',
        'two_factor_enabled'         => 'boolean',
        'is_banned'                  => 'boolean',
        'notification_preferences'   => 'json',
    ];

    use TwoFactorAuthenticatable;

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function wantsNotification(string $type): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return $prefs[$type] ?? Config::get("auth-microservice.notifications.{$type}", true);
    }

    public function loginHistories(): array
    {
        return LoginHistory::where('user_id', $this->getKey())->get();
    }

    public function auditLogs(): array
    {
        return AuditLog::where('user_id', $this->getKey())->get();
    }

    public function personalAccessTokens(): array
    {
        return PersonalAccessToken::where('user_id', $this->getKey())->get();
    }

    public function dataExportRequests(): array
    {
        return DataExportRequest::where('user_id', $this->getKey())->get();
    }
}
