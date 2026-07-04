<!DOCTYPE html>
<html>
<body>
<p>Hi {{ $user->name }},</p>
<p>Click the link below to sign in instantly:</p>
<p><a href="{{ url(config('auth-microservice.routes.prefix', 'auth') . '/magic-link/verify?token=' . $token) }}">Sign In</a></p>
<p>This link expires in {{ config('auth-microservice.magic_link.expires_minutes', 15) }} minutes and can only be used once.</p>
</body>
</html>
