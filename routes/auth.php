<?php

use Illuminate\Support\Facades\Route;
use JarirAhmed\AuthMicroservice\Controllers\Auth\RegisterController;
use JarirAhmed\AuthMicroservice\Controllers\Auth\LoginController;
use JarirAhmed\AuthMicroservice\Controllers\Auth\LogoutController;
use JarirAhmed\AuthMicroservice\Controllers\Auth\MagicLinkController;
use JarirAhmed\AuthMicroservice\Controllers\Auth\SocialLoginController;
use JarirAhmed\AuthMicroservice\Controllers\Auth\TwoFactorController;
use JarirAhmed\AuthMicroservice\Controllers\Auth\PasswordResetController;
use JarirAhmed\AuthMicroservice\Controllers\ProfileController;
use JarirAhmed\AuthMicroservice\Controllers\TokenController;
use JarirAhmed\AuthMicroservice\Controllers\AuditController;
use JarirAhmed\AuthMicroservice\Controllers\ExportController;
use JarirAhmed\AuthMicroservice\Controllers\AdminController;

$prefix     = config('auth-microservice.routes.prefix', 'auth');
$middleware = config('auth-microservice.routes.middleware', ['web']);

Route::prefix($prefix)->middleware($middleware)->group(function () {

    // Registration
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/email/verify', [RegisterController::class, 'verify'])->name('auth.email.verify');
    Route::post('/email/resend', [RegisterController::class, 'resend']);

    // Login / Logout
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth');

    // Magic Link
    Route::post('/magic-link/send', [MagicLinkController::class, 'send']);
    Route::get('/magic-link/verify', [MagicLinkController::class, 'verify'])->name('auth.magic-link.verify');

    // Social Login
    Route::get('/social/{provider}/redirect', [SocialLoginController::class, 'redirect']);
    Route::get('/social/{provider}/callback', [SocialLoginController::class, 'callback']);

    // Password Reset
    Route::post('/password/forgot', [PasswordResetController::class, 'sendLink']);
    Route::post('/password/reset', [PasswordResetController::class, 'reset']);

    // Authenticated routes
    Route::middleware('auth')->group(function () {

        // 2FA
        Route::post('/2fa/enable', [TwoFactorController::class, 'enable']);
        Route::post('/2fa/disable', [TwoFactorController::class, 'disable']);
        Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);

        // Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/password', [ProfileController::class, 'changePassword']);
        Route::delete('/profile', [ProfileController::class, 'closeAccount']);
        Route::patch('/profile/notifications', [ProfileController::class, 'updateNotificationPreferences']);

        // API Tokens
        Route::get('/tokens', [TokenController::class, 'index']);
        Route::post('/tokens', [TokenController::class, 'store']);
        Route::delete('/tokens/{id}', [TokenController::class, 'destroy']);

        // Audit
        Route::get('/audit/login-history', [AuditController::class, 'loginHistory']);
        Route::get('/audit/logs', [AuditController::class, 'auditLogs']);

        // Data Export / GDPR
        Route::post('/export', [ExportController::class, 'request']);
        Route::get('/export/{id}', [ExportController::class, 'status']);

        // Admin
        Route::prefix('admin')->group(function () {
            Route::get('/users', [AdminController::class, 'index']);
            Route::post('/users/{id}/ban', [AdminController::class, 'ban']);
            Route::post('/users/{id}/unban', [AdminController::class, 'unban']);
            Route::post('/users/{id}/unlock', [AdminController::class, 'unlock']);
            Route::post('/users/{id}/impersonate', [AdminController::class, 'impersonate']);
            Route::post('/impersonate/stop', [AdminController::class, 'stopImpersonating']);
        });
    });
});
