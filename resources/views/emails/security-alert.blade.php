<!DOCTYPE html>
<html>
<body>
<p>Hi {{ $user->name }},</p>
<p>A security event occurred on your account: <strong>{{ str_replace('_', ' ', $alertType) }}</strong></p>
@if(!empty($context))
<ul>
@foreach($context as $key => $value)
    <li>{{ $key }}: {{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</li>
@endforeach
</ul>
@endif
<p>If this wasn't you, please secure your account immediately.</p>
</body>
</html>
