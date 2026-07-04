<?php

namespace JarirAhmed\AuthMicroservice\Services\OAuth2;

use JarirAhmed\AuthMicroservice\Contracts\OAuthProviderContract;
use JarirAhmed\AuthMicroservice\Exceptions\OAuthException;

class OAuth2Client
{
    public function __construct(private OAuthProviderContract $provider) {}

    public function generateState(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function getAuthorizationUrl(string $state): string
    {
        return $this->provider->getAuthorizationUrl($state);
    }

    public function handleCallback(string $code, string $state, string $expectedState): array
    {
        if (!hash_equals($expectedState, $state)) {
            throw new OAuthException('Invalid OAuth state parameter.');
        }

        $tokenData = $this->provider->exchangeCodeForToken($code);
        $userInfo  = $this->provider->getUserInfo($tokenData['access_token']);

        return [
            'token_data' => $tokenData,
            'user_info'  => $userInfo,
        ];
    }
}
