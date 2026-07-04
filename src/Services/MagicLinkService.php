<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Config;
use JarirAhmed\AuthMicroservice\Database\Connection;

class MagicLinkService
{
    public function generate(mixed $user): string
    {
        $plain   = bin2hex(random_bytes(32));
        $expires = Config::get('auth-microservice.magic_link.expires_minutes', 15);

        Connection::getInstance()->table('magic_links')
            ->where('user_id', $user->getKey())
            ->delete();

        Connection::getInstance()->table('magic_links')->insert([
            'user_id'    => $user->getKey(),
            'token'      => hash('sha256', $plain),
            'expires_at' => date('Y-m-d H:i:s', time() + ($expires * 60)),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $plain;
    }

    public function verify(string $token): mixed
    {
        $record = Connection::getInstance()->first(
            'SELECT * FROM magic_links WHERE token = ? AND used_at IS NULL AND expires_at > ?',
            [hash('sha256', $token), date('Y-m-d H:i:s')]
        );

        if (!$record) return null;

        Connection::getInstance()->query(
            'UPDATE magic_links SET used_at = ? WHERE id = ?',
            [date('Y-m-d H:i:s'), $record['id']]
        );

        $userModel = Config::get('auth-microservice.user_model');
        return $userModel::find($record['user_id']);
    }
}
