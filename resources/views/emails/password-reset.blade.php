<!DOCTYPE html>
<html>
<body>
<p>Hi {{ $user->name }},</p>
<p>Click the link below to reset your password:</p>
<p><a href="{{ url(config('auth-microservice.routes.prefix', 'auth') . '/password/reset?token=' . $token) }}">Reset Password</a></p>
<p>This link expires in {{ config('auth-microservice.password_reset.expires_minutes', 60) }} minutes.</p>
</body>
</html>
