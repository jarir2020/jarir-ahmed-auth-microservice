<?php

namespace JarirAhmed\AuthMicroservice\Contracts;

interface OAuthProviderContract
{
    public function getAuthorizationUrl(string $state): string;
    public function exchangeCodeForToken(string $code): array;
    public function getUserInfo(string $accessToken): array;
}
