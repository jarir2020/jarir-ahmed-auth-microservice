<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;

class AuditLog extends Model
{
    protected static string $table = 'audit_logs';

    protected static array $casts = [
        'before' => 'json',
        'after'  => 'json',
    ];
}
