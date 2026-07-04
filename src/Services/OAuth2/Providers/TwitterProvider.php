<?php

namespace JarirAhmed\AuthMicroservice\Services\OAuth2\Providers;

use JarirAhmed\AuthMicroservice\Contracts\OAuthProviderContract;

class TwitterProvider implements OAuthProviderContract
{
    private array $config;

    public function __construct()
    {
        $this->config = config('auth-microservice.oauth.twitter');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return 'https://twitter.com/i/oauth2/authorize?' . http_build_query([
            'response_type'         => 'code',
            'client_id'             => $this->config['client_id'],
            'redirect_uri'          => $this->config['redirect_uri'],
            'scope'                 => 'tweet.read users.read offline.access',
            'state'                 => $state,
            'code_challenge'        => $state,
            'code_challenge_method' => 'plain',
        ]);
    }

    public function exchangeCodeForToken(string $code): array
    {
        $ch = curl_init('https://api.twitter.com/2/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => $this->config['redirect_uri'],
                'code_verifier' => $this->config['client_secret'],
            ]),
            CURLOPT_USERPWD    => $this->config['client_id'] . ':' . $this->config['client_secret'],
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getUserInfo(string $accessToken): array
    {
        $ch = curl_init('https://api.twitter.com/2/users/me?user.fields=name,username');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$accessToken}"],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true)['data'] ?? [];
        return ['id' => $data['id'] ?? null, 'name' => $data['name'] ?? null, 'email' => null];
    }
}
