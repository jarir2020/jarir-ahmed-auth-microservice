# jarir-ahmed/auth-microservice — Full Auth Standalone Package Plan

## Overview
A self-contained PHP auth package under namespace `JarirAhmed\AuthMicroservice` (vendor `jarir-ahmed`). Ships with its own routes, controllers, migrations, config, and views — works standalone or as a Laravel drop-in.

## Module Architecture

| Module | Key Features |
|---|---|
| **1. Registration** | Register with email/password, email verification, resend verification |
| **2. Login** | Email/password login, "remember me", session management, last online tracking |
| **3. Magic Link / Passwordless** | Email-based one-click sign-in tokens, expiry handling |
| **4. Social Login (OAuth)** | Google, Facebook, GitHub, Twitter, LinkedIn — native OAuth 2.0 client |
| **5. Two-Factor Auth (2FA)** | Native TOTP (RFC 6238), backup codes, QR URI for frontend rendering |
| **6. Password Reset** | Token-based reset flow, email notifications, expiry handling |
| **7. API Token Management** | JWT generation/verification, personal access tokens, refresh tokens, token scoping/revocation |
| **8. Account Lockout** | Lock after N failed attempts, configurable timeout, admin unlock |
| **9. Profile/Account** | Update profile, change password, close account |
| **10. Session Management** | List active sessions, revoke sessions, device fingerprinting, last online timestamp |
| **11. User Tracking & Audit** | IP, geolocation, device, OS, browser on every auth event — full audit trail |
| **12. Security Notifications** | Email alerts for new-device login, password change, 2FA toggle, suspicious activity |
| **13. Data Export / GDPR** | Export user data as JSON/CSV, account deletion request flow |
| **14. Admin Tools** | User listing, ban/unban, impersonation, role/permission scaffolding |

## Directory Structure
```
jarir-ahmed-auth-microservice/
├── src/
│   ├── AuthMicroserviceServiceProvider.php
│   ├── Commands/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── RegisterController.php
│   │   │   ├── LoginController.php
│   │   │   ├── MagicLinkController.php
│   │   │   ├── SocialLoginController.php
│   │   │   ├── TwoFactorController.php
│   │   │   ├── PasswordResetController.php
│   │   │   └── LogoutController.php
│   │   ├── ProfileController.php
│   │   ├── AdminController.php
│   │   ├── AuditController.php               # View login history, audit logs
│   │   ├── TokenController.php               # Manage personal access tokens
│   │   ├── ExportController.php              # Data export / GDPR
│   ├── Events/
│   │   ├── UserRegistered.php
│   │   ├── UserLoggedIn.php
│   │   ├── UserLoggedOut.php
│   │   ├── UserLockedOut.php
│   │   ├── PasswordChanged.php
│   │   ├── TwoFactorToggled.php
│   │   ├── SuspiciousLoginDetected.php
│   │   └── AccountDeleted.php
│   ├── Listeners/
│   │   ├── SendWelcomeNotification.php
│   │   ├── SendSecurityAlertNotification.php
│   │   ├── RecordLoginHistory.php
│   │   └── RecordAuditLog.php
│   ├── Mail/
│   │   ├── WelcomeMail.php
│   │   ├── PasswordResetMail.php
│   │   ├── MagicLinkMail.php
│   │   ├── EmailVerificationMail.php
│   │   └── SecurityAlertMail.php
│   ├── Middleware/
│   │   ├── TwoFactorMiddleware.php
│   │   ├── ThrottleMiddleware.php
│   │   ├── TrackAuthMiddleware.php           # Captures device fingerprint on every auth request
│   │   ├── EmailVerifiedMiddleware.php
│   │   ├── AccountLockoutMiddleware.php
│   │   ├── TokenAuthMiddleware.php           # Validates bearer token (SHA-256 lookup)
│   │   ├── LastOnlineMiddleware.php          # Updates last_online_at on authenticated requests
│   ├── Models/
│   │   ├── User.php (extendable)
│   │   ├── PasswordReset.php
│   │   ├── TwoFactorBackupCode.php
│   │   ├── LoginHistory.php
│   │   ├── AuditLog.php
│   │   ├── PersonalAccessToken.php
│   │   ├── AccountLockout.php
│   │   └── DataExportRequest.php
│   ├── Requests/
│   ├── Rules/
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── SocialLoginService.php
│   │   ├── TwoFactorService.php
│   │   ├── PasswordResetService.php
│   │   ├── TokenService.php                  # Inlined auth-token generation logic (originally jarir-ahmed/auth-token-maker)
│   │   ├── MagicLinkService.php
│   │   ├── LockoutService.php
│   │   ├── NotificationService.php
│   │   ├── ExportService.php                 # Data export + account deletion
│   │   ├── OAuth2/
│   │   │   ├── OAuth2Client.php              # Generic OAuth 2.0 client
│   │   │   └── Providers/
│   │   │       ├── GoogleProvider.php
│   │   │       ├── FacebookProvider.php
│   │   │       ├── GitHubProvider.php
│   │   │       ├── TwitterProvider.php
│   │   │       └── LinkedInProvider.php
│   │   ├── TokenManager.php                  # Generate 64-char random tokens, hash & store, issue/refresh/revoke
│   │   ├── TOTP/
│   │   │   ├── TOTP.php                      # TOTP generation + verification
│   │   │   └── BackupCodeManager.php
│   │   └── Tracking/
│   │       ├── UserTracker.php               # Inlined user-info-capture logic (IP, UA, device, OS, browser, geolocation)
│   │       ├── LoginHistoryRecorder.php
│   │       ├── AuditLogger.php
│   │       └── SuspiciousLoginDetector.php
│   ├── Traits/
│   │   └── TwoFactorAuthenticatable.php
│   ├── Contracts/
│   └── Exceptions/
├── config/
│   └── auth-microservice.php
├── database/
│   └── migrations/
├── resources/
│   ├── views/
│   └── lang/
├── routes/
│   └── auth.php
├── composer.json
├── phpunit.xml
└── tests/
```

## composer.json
```json
{
    "name": "jarir-ahmed/auth-microservice",
    "description": "Standalone auth microservice package — Registration, Login, 2FA, Password Reset & more.",
    "require": {
        "php": ">=8.0"
    }
}
```

## Implementation Phases

### Phase 1 — Bootstrap
Service provider, config, migrations, routes, base contracts.

### Phase 2 — Registration + Email Verification
Register users, send verification email, verify email flow, resend verification endpoint.

### Phase 3 — Login + Throttling + Session Management
Authenticate users, rate-limit attempts, track/manage sessions. `LastOnlineMiddleware` updates `last_online_at` on the User model with every authenticated request. Session list shows last active timestamp.

### Phase 4 — Magic Link / Passwordless Login
Generate time-limited one-click sign-in tokens sent via email. Verify token and authenticate user on click. Auto-expire after use or timeout.

### Phase 5 — Social Login (OAuth)
Native OAuth 2.0 client implementation. Generic `OAuth2Client` handles authorization code flow, state validation, token exchange. Provider-specific classes (`GoogleProvider`, `FacebookProvider`, `GitHubProvider`, etc.) provide endpoints + user mapping.

### Phase 6 — Password Reset Flow
Request reset via email, token verification, password update.

### Phase 7 — Two-Factor Auth (2FA)
Native TOTP (RFC 6238) — HMAC-SHA1 + time-step truncation. Returns `otpauth://` URI for frontend QR rendering. Backup codes via `sodium` CSPRNG.

### Phase 8 — API Token Management (Random Token)
`TokenManager` generates 64-char random hex tokens via `bin2hex(random_bytes(32))`. Stores SHA-256 hash in DB, returns plaintext token only once on creation. Supports expiry, scoping, and revocation. No JWT complexity needed — same pattern used by GitHub, GitLab, and Laravel Sanctum.

### Phase 9 — Account Lockout
`LockoutService` tracks failed attempts. Locks account after N failures. Configurable lockout duration, admin unlock endpoint, `AccountLockoutMiddleware` blocks requests for locked accounts.

### Phase 10 — Profile / Account Management
Update name/email, change password with current password confirmation, close account.

### Phase 11 — User Tracking & Audit
Inlined user-info-capture logic. Records IP, geolocation, device, OS, browser, referer on every auth event. `TrackAuthMiddleware` auto-fingerprints. `SuspiciousLoginDetector` flags unusual locations/devices. `AuditLogger` captures before/after state on sensitive actions.

### Phase 12 — Security Notifications
Event-driven email alerts. `NotificationService` listens to auth events (new device login, password change, 2FA toggle, suspicious login) and dispatches `SecurityAlertMail`. Users can opt out per notification type.

### Phase 13 — Data Export / GDPR
`ExportService` generates JSON/CSV export of all user data (profile, login history, audit log, tokens). `DataExportRequest` model tracks requests with async processing. Account deletion endpoint with confirmation flow.

### Phase 14 — Admin Tools + Tests + Documentation
User list/ban/unban, impersonation, role/permission scaffolding, audit log viewer, full test suite, README/docs.

## Key Design Decisions
- **PSR-4 autoloading** — `JarirAhmed\AuthMicroservice\` → `src/`
- **Laravel auto-discovery** via `AuthMicroserviceServiceProvider`
- **Extendable User model** via contract/interface — consuming apps swap in their own model
- **Own packages inlined** — `jarir-ahmed/auth-token-maker` and `jarir-ahmed/user-info-capture` code copied directly into this package's source tree. No Composer dependency bloat.
- **Standalone routes file** — users include or publish them
- **All views publishable** for customization
- **PHPUnit ^9.0** for testing (consistent with existing package)
- **Zero external dependencies** — TOTP, OAuth 2.0, JWT, backup codes all implemented natively with PHP 8.0+ extensions only (`sodium`, `hash`, `json`, `curl`, `mbstring`). Own packages inlined to avoid vendor bloat.
- **QR codes deferred to frontend** — package returns the `otpauth://` URI; client-side JS (or any QR lib) renders it
- **Event-driven architecture** — every auth action fires an event; listeners handle logging, notifications, and tracking decoupled from the main flow
- **JWT implemented natively** — base64url encoding + HMAC-SHA256/RS256 signing via `ext-hash`/`ext-sodium`, no `firebase/php-jwt` dependency
