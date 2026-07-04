<?php

namespace JarirAhmed\AuthMicroservice\Services\OAuth2\Providers;

use JarirAhmed\AuthMicroservice\Contracts\OAuthProviderContract;

class GoogleProvider implements OAuthProviderContract
{
    private array $config;

    public function __construct()
    {
        $this->config = config('auth-microservice.oauth.google');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $state,
        ]);
    }

    public function exchangeCodeForToken(string $code): array
    {
        return $this->post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'grant_type'    => 'authorization_code',
        ]);
    }

    public function getUserInfo(string $accessToken): array
    {
        return $this->get('https://www.googleapis.com/oauth2/v3/userinfo', $accessToken);
    }

    private function post(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    private function get(string $url, string $token): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$token}"],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
