<?php

namespace JarirAhmed\AuthMicroservice;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
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
        $this->mergeConfigFrom(__DIR__ . '/../config/auth-microservice.php', 'auth-microservice');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/auth.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'auth-microservice');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'auth-microservice');

        $this->publishes([
            __DIR__ . '/../config/auth-microservice.php' => config_path('auth-microservice.php'),
        ], 'auth-microservice-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'auth-microservice-migrations');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/auth-microservice'),
        ], 'auth-microservice-views');

        $this->registerEvents();
    }

    protected function registerEvents(): void
    {
        Event::listen(UserRegistered::class, SendWelcomeNotification::class);
        Event::listen(UserLoggedIn::class, RecordLoginHistory::class);
        Event::listen(UserLoggedIn::class, RecordAuditLog::class);
        Event::listen(UserLoggedOut::class, RecordAuditLog::class);
        Event::listen(PasswordChanged::class, SendSecurityAlertNotification::class);
        Event::listen(PasswordChanged::class, RecordAuditLog::class);
        Event::listen(TwoFactorToggled::class, SendSecurityAlertNotification::class);
        Event::listen(TwoFactorToggled::class, RecordAuditLog::class);
        Event::listen(SuspiciousLoginDetected::class, SendSecurityAlertNotification::class);
        Event::listen(AccountDeleted::class, RecordAuditLog::class);
    }
}
