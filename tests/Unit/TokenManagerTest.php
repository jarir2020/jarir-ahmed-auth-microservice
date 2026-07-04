<?php

namespace JarirAhmed\AuthMicroservice\Tests\Unit;

use JarirAhmed\AuthMicroservice\Tests\TestCase;
use JarirAhmed\AuthMicroservice\Services\TokenManager;
use JarirAhmed\AuthMicroservice\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TokenManagerTest extends TestCase
{
    use RefreshDatabase;

    private TokenManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TokenManager();
    }

    public function test_generates_64_char_hex_token(): void
    {
        $user   = $this->mockUser();
        $result = $this->manager->generate($user, 'test');
        $this->assertSame(64, strlen($result['token']));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $result['token']);
    }

    public function test_stores_sha256_hash(): void
    {
        $user   = $this->mockUser();
        $result = $this->manager->generate($user, 'test');
        $stored = PersonalAccessToken::first();
        $this->assertSame(hash('sha256', $result['token']), $stored->token);
    }

    public function test_find_by_plaintext(): void
    {
        $user   = $this->mockUser();
        $result = $this->manager->generate($user, 'test');
        $found  = $this->manager->find($result['token']);
        $this->assertNotNull($found);
    }

    public function test_revoke_sets_revoked_at(): void
    {
        $user   = $this->mockUser();
        $result = $this->manager->generate($user, 'test');
        $this->manager->revoke($result['model']);
        $this->assertNotNull($result['model']->fresh()->revoked_at);
    }

    private function mockUser(): object
    {
        return new class {
            public function getKey(): int { return 1; }
        };
    }
}
