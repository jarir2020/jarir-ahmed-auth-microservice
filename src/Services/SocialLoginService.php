<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Services\OAuth2\OAuth2Client;
use JarirAhmed\AuthMicroservice\Services\OAuth2\Providers\GoogleProvider;
use JarirAhmed\AuthMicroservice\Services\OAuth2\Providers\FacebookProvider;
use JarirAhmed\AuthMicroservice\Services\OAuth2\Providers\GitHubProvider;
use JarirAhmed\AuthMicroservice\Services\OAuth2\Providers\TwitterProvider;
use JarirAhmed\AuthMicroservice\Services\OAuth2\Providers\LinkedInProvider;
use JarirAhmed\AuthMicroservice\Contracts\OAuthProviderContract;
use JarirAhmed\AuthMicroservice\Exceptions\OAuthException;
use JarirAhmed\AuthMicroservice\Support\Hash;

class SocialLoginService
{
    private array $providers = [
        'google'   => GoogleProvider::class,
        'facebook' => FacebookProvider::class,
        'github'   => GitHubProvider::class,
        'twitter'  => TwitterProvider::class,
        'linkedin' => LinkedInProvider::class,
    ];

    public function getRedirectUrl(string $provider, string $state): string
    {
        return $this->makeClient($provider)->getAuthorizationUrl($state);
    }

    public function handleCallback(string $provider, string $code, string $state, string $expectedState): mixed
    {
        $client   = $this->makeClient($provider);
        $result   = $client->handleCallback($code, $state, $expectedState);
        $userInfo = $result['user_info'];

        if (empty($userInfo['email'])) {
            throw new OAuthException("Could not retrieve email from {$provider}.");
        }

        $userModel = config('auth-microservice.user_model');
        return $userModel::firstOrCreate(
            ['email' => $userInfo['email']],
            ['name' => $userInfo['name'] ?? $userInfo['email'], 'password' => Hash::make(bin2hex(random_bytes(16))), 'email_verified_at' => now()]
        );
    }

    private function makeClient(string $provider): OAuth2Client
    {
        if (!isset($this->providers[$provider])) {
            throw new OAuthException("Unsupported provider: {$provider}");
        }
        /** @var OAuthProviderContract $instance */
        $instance = new $this->providers[$provider]();
        return new OAuth2Client($instance);
    }
}
