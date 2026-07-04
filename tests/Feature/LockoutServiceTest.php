<?php

namespace JarirAhmed\AuthMicroservice\Tests\Feature;

use JarirAhmed\AuthMicroservice\Tests\TestCase;
use JarirAhmed\AuthMicroservice\Services\LockoutService;
use JarirAhmed\AuthMicroservice\Models\AccountLockout;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LockoutServiceTest extends TestCase
{
    use RefreshDatabase;

    private LockoutService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LockoutService();
    }

    public function test_not_locked_initially(): void
    {
        $user = $this->mockUser(1);
        $this->assertFalse($this->service->isLocked($user));
    }

    public function test_locks_after_max_attempts(): void
    {
        $user = $this->mockUser(1);
        $max  = config('auth-microservice.lockout.max_attempts', 5);

        for ($i = 0; $i < $max; $i++) {
            $this->service->recordFailure($user);
        }

        $this->assertTrue($this->service->isLocked($user));
    }

    public function test_reset_clears_lockout(): void
    {
        $user = $this->mockUser(1);
        $max  = config('auth-microservice.lockout.max_attempts', 5);

        for ($i = 0; $i < $max; $i++) {
            $this->service->recordFailure($user);
        }

        $this->service->resetFailures($user);
        $this->assertFalse($this->service->isLocked($user));
    }

    private function mockUser(int $id): object
    {
        return new class($id) {
            public function __construct(private int $id) {}
            public function getKey(): int { return $this->id; }
        };
    }
}
