<!DOCTYPE html>
<html>
<body>
<p>Hi {{ $user->name }},</p>
<p>Please verify your email by clicking the link below:</p>
<p><a href="{{ url(config('auth-microservice.routes.prefix', 'auth') . '/email/verify?token=' . $token) }}">Verify Email</a></p>
<p>This link expires in {{ config('auth-microservice.registration.email_verification_expires_minutes', 60) }} minutes.</p>
</body>
</html>
