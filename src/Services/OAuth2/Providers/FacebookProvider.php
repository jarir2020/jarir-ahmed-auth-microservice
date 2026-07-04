<?php

namespace JarirAhmed\AuthMicroservice\Services\OAuth2\Providers;

use JarirAhmed\AuthMicroservice\Contracts\OAuthProviderContract;

class FacebookProvider implements OAuthProviderContract
{
    private array $config;

    public function __construct()
    {
        $this->config = config('auth-microservice.oauth.facebook');
    }

    public function getAuthorizationUrl(string $state): string
    {
        return 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'response_type' => 'code',
            'scope'         => 'email,public_profile',
            'state'         => $state,
        ]);
    }

    public function exchangeCodeForToken(string $code): array
    {
        $url = 'https://graph.facebook.com/v18.0/oauth/access_token?' . http_build_query([
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'code'          => $code,
        ]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    public function getUserInfo(string $accessToken): array
    {
        $url = 'https://graph.facebook.com/me?' . http_build_query([
            'fields'       => 'id,name,email',
            'access_token' => $accessToken,
        ]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        return ['id' => $data['id'] ?? null, 'name' => $data['name'] ?? null, 'email' => $data['email'] ?? null];
    }
}
