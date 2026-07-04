<?php

namespace JarirAhmed\AuthMicroservice\Bridge\Laravel;

use Illuminate\Support\ServiceProvider;
use JarirAhmed\AuthMicroservice\Config;
use JarirAhmed\AuthMicroservice\EventDispatcher;
use JarirAhmed\AuthMicroservice\Events\UserRegistered;
use JarirAhmed\AuthMicroservice\Events\UserLoggedIn;
use JarirAhmed\AuthMicroservice\Events\UserLoggedOut;
use JarirAhmed\AuthMicroservice\Events\PasswordChanged;
use JarirAhmed\AuthMicroservice\Events\TwoFactorToggled;
use JarirAhmed\AuthMicroservice\Events\SuspiciousLoginDetected;
use JarirAhmed\AuthMicroservice\Events\AccountDeleted;
use JarirAhmed\AuthMicroservice\Listeners\SendWelcomeNotification;
use JarirAhmed\AuthMicroservice\Listeners\SendSecurityAlertNotification;
use JarirAhmed\AuthMicroservice\Listeners\RecordLoginHistory;
use JarirAhmed\AuthMicroservice\Listeners\RecordAuditLog;

class AuthMicroserviceServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        Config::loadFile(__DIR__ . '/../config/auth-microservice.php');
    }

    public function boot(): void
    {
        $this->registerEvents();
    }

    protected function registerEvents(): void
    {
        EventDispatcher::listen(UserRegistered::class,         SendWelcomeNotification::class);
        EventDispatcher::listen(UserLoggedIn::class,           RecordLoginHistory::class);
        EventDispatcher::listen(UserLoggedIn::class,           RecordAuditLog::class);
        EventDispatcher::listen(UserLoggedOut::class,          RecordAuditLog::class);
        EventDispatcher::listen(PasswordChanged::class,        SendSecurityAlertNotification::class);
        EventDispatcher::listen(PasswordChanged::class,        RecordAuditLog::class);
        EventDispatcher::listen(TwoFactorToggled::class,       SendSecurityAlertNotification::class);
        EventDispatcher::listen(TwoFactorToggled::class,       RecordAuditLog::class);
        EventDispatcher::listen(SuspiciousLoginDetected::class, SendSecurityAlertNotification::class);
        EventDispatcher::listen(AccountDeleted::class,         RecordAuditLog::class);
    }

    public function getMigrationPath(): string
    {
        return __DIR__ . '/../database/migrations';
    }

    public function getConfigPath(): string
    {
        return __DIR__ . '/../config/auth-microservice.php';
    }

    public function getViewPath(): string
    {
        return __DIR__ . '/../resources/views';
    }
}
