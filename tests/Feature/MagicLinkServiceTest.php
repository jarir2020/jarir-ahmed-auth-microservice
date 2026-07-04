<?php

namespace JarirAhmed\AuthMicroservice\Tests\Feature;

use JarirAhmed\AuthMicroservice\Tests\TestCase;
use JarirAhmed\AuthMicroservice\Services\MagicLinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class MagicLinkServiceTest extends TestCase
{
    use RefreshDatabase;

    private MagicLinkService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MagicLinkService();
    }

    public function test_generates_token_and_stores_hash(): void
    {
        $user  = $this->mockUser(1);
        $plain = $this->service->generate($user);

        $this->assertSame(64, strlen($plain));
        $record = DB::table('magic_links')->where('user_id', 1)->first();
        $this->assertNotNull($record);
        $this->assertSame(hash('sha256', $plain), $record->token);
    }

    public function test_verify_returns_null_for_invalid_token(): void
    {
        $result = $this->service->verify('invalidtoken');
        $this->assertNull($result);
    }

    private function mockUser(int $id): object
    {
        return new class($id) {
            public function __construct(private int $id) {}
            public function getKey(): int { return $this->id; }
        };
    }
}
