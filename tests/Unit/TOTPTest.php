<?php

namespace JarirAhmed\AuthMicroservice\Tests\Unit;

use JarirAhmed\AuthMicroservice\Tests\TestCase;
use JarirAhmed\AuthMicroservice\Services\TOTP\TOTP;

class TOTPTest extends TestCase
{
    private TOTP $totp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->totp = new TOTP();
    }

    public function test_generates_secret(): void
    {
        $secret = $this->totp->generateSecret();
        $this->assertNotEmpty($secret);
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $secret);
    }

    public function test_generates_otpauth_uri(): void
    {
        $secret = $this->totp->generateSecret();
        $uri    = $this->totp->getUri($secret, 'user@example.com', 'TestApp');
        $this->assertStringStartsWith('otpauth://totp/', $uri);
        $this->assertStringContainsString($secret, $uri);
    }

    public function test_verifies_valid_code(): void
    {
        $secret     = $this->totp->generateSecret();
        $reflection = new \ReflectionClass($this->totp);
        $compute    = $reflection->getMethod('compute');
        $compute->setAccessible(true);
        $code = $compute->invoke($this->totp, $secret, (int) floor(time() / 30));
        $this->assertTrue($this->totp->verify($secret, $code));
    }

    public function test_rejects_invalid_code(): void
    {
        $secret = $this->totp->generateSecret();
        $this->assertFalse($this->totp->verify($secret, '000000'));
    }
}
