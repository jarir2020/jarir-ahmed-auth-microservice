<?php

namespace JarirAhmed\AuthMicroservice\Models;

use Illuminate\Database\Eloquent\Model;

class DataExportRequest extends Model
{
    protected $fillable = ['user_id', 'format', 'status', 'file_path', 'completed_at'];

    protected $casts = ['completed_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(config('auth-microservice.user_model'));
    }
}
