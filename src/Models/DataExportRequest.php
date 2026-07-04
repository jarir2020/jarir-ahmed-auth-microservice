<?php

namespace JarirAhmed\AuthMicroservice\Models;

use JarirAhmed\AuthMicroservice\Database\Model;

class DataExportRequest extends Model
{
    protected static string $table = 'data_export_requests';

    protected static array $casts = ['completed_at' => 'datetime'];

    public function user(): ?User
    {
        return User::find($this->user_id);
    }
}
