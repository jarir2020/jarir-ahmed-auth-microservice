<?php

namespace JarirAhmed\AuthMicroservice\Services;

use Illuminate\Support\Facades\DB;

class MagicLinkService
{
    public function generate(mixed $user): string
    {
        $plain   = bin2hex(random_bytes(32));
        $expires = config('auth-microservice.magic_link.expires_minutes', 15);

        DB::table('magic_links')->where('user_id', $user->getKey())->delete();
        DB::table('magic_links')->insert([
            'user_id'    => $user->getKey(),
            'token'      => hash('sha256', $plain),
            'expires_at' => now()->addMinutes($expires),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $plain;
    }

    public function verify(string $token): mixed
    {
        $record = DB::table('magic_links')
            ->where('token', hash('sha256', $token))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) return null;

        DB::table('magic_links')->where('id', $record->id)->update(['used_at' => now()]);

        $userModel = config('auth-microservice.user_model');
        return $userModel::find($record->user_id);
    }
}
