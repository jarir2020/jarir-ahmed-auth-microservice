# jarir-ahmed/auth-microservice

A self-contained PHP auth package for Laravel. Ships with its own routes, controllers, migrations, config, and views — zero external runtime dependencies.

## Features

| Module | Description |
|---|---|
| Registration | Email/password registration, email verification, resend verification |
| Login | Email/password login, remember me, session management, last online tracking |
| Magic Link | Passwordless one-click sign-in via email |
| Social Login | Google, Facebook, GitHub, Twitter, LinkedIn — native OAuth 2.0 |
| Two-Factor Auth | Native TOTP (RFC 6238), backup codes, `otpauth://` URI for QR rendering |
| Password Reset | Token-based reset flow, expiry handling |
| API Tokens | 64-char random hex tokens, SHA-256 hashed, scoped, revocable |
| Account Lockout | Lock after N failed attempts, configurable timeout, admin unlock |
| Profile | Update profile, change password, close account |
| Tracking & Audit | IP, geolocation, device, OS, browser on every auth event |
| Security Notifications | Email alerts for new-device login, password change, 2FA toggle |
| Data Export / GDPR | Export user data as JSON/CSV, account deletion flow |
| Admin Tools | User listing, ban/unban, impersonation, admin unlock |

## Requirements

- PHP >= 8.0
- Extensions: `ext-hash`, `ext-json`, `ext-curl`, `ext-mbstring`, `ext-sodium`
- (Optional) Laravel >= 11.0 if using as a Laravel drop-in

## Installation

```bash
composer require jarir-ahmed/auth-microservice
```

### For Non-Laravel (Framework-Agnostic) Users

Since this package is completely framework-agnostic, you can integrate it into any PHP application (Symfony, Slim, vanilla PHP):

1. **Dependency Injection**: Use our provided `Container` (or your own PSR-11 container) to bind the required Repositories.
```php
use JarirAhmed\AuthMicroservice\Container;
use JarirAhmed\AuthMicroservice\Contracts\UserRepositoryInterface;
use App\Repositories\MyUserRepository;

$container = new Container();
$container->bind(UserRepositoryInterface::class, function () {
    return new MyUserRepository(); // Provide your own DB implementation
});
```
2. **Configuration**: Load the package configuration array and customize it for your needs.
3. **Database**: Use the provided schemas in `database/migrations` to create the required tables in your database using your preferred tool (Phinx, Doctrine, raw SQL).

### For Laravel Users (Drop-in)

If you are using Laravel, the package will auto-discover its Service Provider and act as a seamless drop-in.

Publish config and migrations:

```bash
php artisan vendor:publish --tag=auth-microservice-config
php artisan vendor:publish --tag=auth-microservice-migrations
php artisan migrate
```

Configuration:
```bash
php artisan vendor:publish --tag=auth-microservice-config
```

Key settings in `config/auth-microservice.php`:

```php
'user_model' => \App\Models\User::class,

'registration' => [
    'require_email_verification' => true,
],

'lockout' => [
    'max_attempts'    => 5,
    'lockout_minutes' => 15,
],

'two_factor' => [
    'issuer' => env('APP_NAME', 'AuthMicroservice'),
],

'oauth' => [
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
    ],
    // facebook, github, twitter, linkedin ...
],
```

## Routes

All routes are prefixed with `/auth` by default (configurable).

| Method | URI | Description |
|---|---|---|
| POST | `/auth/register` | Register |
| GET | `/auth/email/verify` | Verify email |
| POST | `/auth/email/resend` | Resend verification |
| POST | `/auth/login` | Login |
| POST | `/auth/logout` | Logout |
| POST | `/auth/magic-link/send` | Send magic link |
| GET | `/auth/magic-link/verify` | Verify magic link |
| GET | `/auth/social/{provider}/redirect` | OAuth redirect |
| GET | `/auth/social/{provider}/callback` | OAuth callback |
| POST | `/auth/password/forgot` | Send reset link |
| POST | `/auth/password/reset` | Reset password |
| POST | `/auth/2fa/enable` | Enable 2FA |
| POST | `/auth/2fa/disable` | Disable 2FA |
| POST | `/auth/2fa/verify` | Verify 2FA code |
| GET | `/auth/profile` | Get profile |
| PATCH | `/auth/profile` | Update profile |
| POST | `/auth/profile/password` | Change password |
| DELETE | `/auth/profile` | Close account |
| GET | `/auth/tokens` | List tokens |
| POST | `/auth/tokens` | Create token |
| DELETE | `/auth/tokens/{id}` | Revoke token |
| GET | `/auth/audit/login-history` | Login history |
| GET | `/auth/audit/logs` | Audit logs |
| POST | `/auth/export` | Request data export |
| GET | `/auth/export/{id}` | Export status |
| GET | `/auth/admin/users` | List users |
| POST | `/auth/admin/users/{id}/ban` | Ban user |
| POST | `/auth/admin/users/{id}/unban` | Unban user |
| POST | `/auth/admin/users/{id}/unlock` | Unlock account |
| POST | `/auth/admin/users/{id}/impersonate` | Impersonate user |
| POST | `/auth/impersonate/stop` | Stop impersonating |

## Middleware

Register in your `bootstrap/app.php` or `Http/Kernel.php`:

```php
use JarirAhmed\AuthMicroservice\Middleware\TokenAuthMiddleware;
use JarirAhmed\AuthMicroservice\Middleware\TwoFactorMiddleware;
use JarirAhmed\AuthMicroservice\Middleware\EmailVerifiedMiddleware;
use JarirAhmed\AuthMicroservice\Middleware\AccountLockoutMiddleware;
use JarirAhmed\AuthMicroservice\Middleware\LastOnlineMiddleware;
use JarirAhmed\AuthMicroservice\Middleware\TrackAuthMiddleware;
```

## Design Decisions

- **Zero external runtime dependencies** — TOTP, OAuth 2.0, backup codes all implemented natively using PHP 8.0+ built-in extensions only
- **SHA-256 token storage** — plaintext token returned once on creation, only the hash stored in DB (same pattern as GitHub, GitLab, Laravel Sanctum)
- **Native TOTP** — RFC 6238 compliant, HMAC-SHA1 + time-step truncation, base32 encoded secrets
- **QR codes deferred to frontend** — package returns the `otpauth://` URI; client-side JS renders it
- **Event-driven** — every auth action fires an event; listeners handle logging, notifications, and tracking decoupled from the main flow
- **Extendable User model** — set `auth-microservice.user_model` to your own model

## Testing

```bash
composer install
./vendor/bin/phpunit
```

## License

MIT
