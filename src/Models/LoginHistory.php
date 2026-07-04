<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;

class LoginHistory extends Model
{
    protected static string $table = 'login_histories';

    protected static array $casts = ['is_suspicious' => 'boolean'];
}
