<?php

namespace JarirAhmed\AuthMicroservice\Services\OAuth2\Providers;

use JarirAhmed\AuthMicroservice\Contracts\OAuthProviderContract;

class GitHubProvider implements OAuthProviderContract
{
    private array $config;

    public function __construct()
    {
        $this->config = config('auth-microservice.oauth.github');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return 'https://github.com/login/oauth/authorize?' . http_build_query([
            'client_id'    => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope'        => 'user:email',
            'state'        => $state,
        ]);
    }

    public function exchangeCodeForToken(string $code): array
    {
        $ch = curl_init('https://github.com/login/oauth/access_token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'client_id'     => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'code'          => $code,
                'redirect_uri'  => $this->config['redirect_uri'],
            ]),
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getUserInfo(string $accessToken): array
    {
        $ch = curl_init('https://api.github.com/user');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer {$accessToken}",
                'User-Agent: AuthMicroservice',
            ],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return ['id' => $data['id'] ?? null, 'name' => $data['name'] ?? null, 'email' => $data['email'] ?? null];
    }
}
