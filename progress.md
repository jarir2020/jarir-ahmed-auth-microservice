# Progress Report

**Goal**: Make the package framework-agnostic by removing Laravel dependencies.

## Completed Tasks & Files

- [x] src/AuthMicroserviceServiceProvider.php modified to be framework-agnostic
- [x] src/Controllers/Auth/LoginController.php
- [x] src/Controllers/Auth/LogoutController.php
- [x] src/Controllers/Auth/MagicLinkController.php
- [x] src/Controllers/Auth/PasswordResetController.php
- [x] src/Controllers/Auth/RegisterController.php
- [x] src/Middleware/TokenAuthMiddleware.php
- [x] src/Models/AccountLockout.php
- [x] src/Models/AuditLog.php
- [x] src/Models/DataExportRequest.php
- [x] src/Models/LoginHistory.php
- [x] src/Models/PasswordReset.php
- [x] src/Models/PersonalAccessToken.php
- [x] src/Models/TwoFactorBackupCode.php
- [x] src/Models/User.php
- [x] src/Services/AuthService.php
- [x] src/Services/LockoutService.php
- [x] src/Services/MagicLinkService.php
- [x] src/Services/PasswordResetService.php
- [x] Added standalone classes: Container, Database, Http, Mailer, RateLimiter
- [x] src/Mail/EmailVerificationMail.php
- [x] src/Mail/PasswordResetMail.php
- [x] src/Mail/SecurityAlertMail.php
- [x] src/Mail/MagicLinkMail.php
- [x] src/Mail/WelcomeMail.php
- [x] src/Middleware/LastOnlineMiddleware.php
- [x] src/Middleware/TwoFactorMiddleware.php
- [x] src/Middleware/TrackAuthMiddleware.php
- [x] src/Middleware/AccountLockoutMiddleware.php
- [x] src/Middleware/ThrottleMiddleware.php
- [x] src/Middleware/EmailVerifiedMiddleware.php
- [x] src/Controllers/ProfileController.php
- [x] src/Controllers/TokenController.php
- [x] src/Controllers/Auth/TwoFactorController.php
- [x] src/Controllers/Auth/SocialLoginController.php
- [x] src/Controllers/AdminController.php
- [x] src/Controllers/AuditController.php
- [x] src/Controllers/ExportController.php
- [x] src/Services/SocialLoginService.php
- [x] src/Services/NotificationService.php

## Notes

- All Laravel/Illuminate dependencies have been removed.
- Tests were updated to not rely on `orchestra/testbench` and use standard `PHPUnit\Framework\TestCase`.
