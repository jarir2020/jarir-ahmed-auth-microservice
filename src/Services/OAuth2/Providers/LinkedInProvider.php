<?php

namespace JarirAhmed\AuthMicroservice\Services\OAuth2\Providers;

use JarirAhmed\AuthMicroservice\Contracts\OAuthProviderContract;

class LinkedInProvider implements OAuthProviderContract
{
    private array $config;

    public function __construct()
    {
        $this->config = config('auth-microservice.oauth.linkedin');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'scope'         => 'openid profile email',
            'state'         => $state,
        ]);
    }

    public function exchangeCodeForToken(string $code): array
    {
        $ch = curl_init('https://www.linkedin.com/oauth/v2/accessToken');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $this->config['redirect_uri'],
                'client_id'     => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
            ]),
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getUserInfo(string $accessToken): array
    {
        $ch = curl_init('https://api.linkedin.com/v2/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$accessToken}"],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return ['id' => $data['sub'] ?? null, 'name' => $data['name'] ?? null, 'email' => $data['email'] ?? null];
    }
}
