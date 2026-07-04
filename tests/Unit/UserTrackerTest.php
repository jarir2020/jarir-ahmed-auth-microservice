<?php

namespace JarirAhmed\AuthMicroservice\Tests\Unit;

use JarirAhmed\AuthMicroservice\Tests\TestCase;
use JarirAhmed\AuthMicroservice\Services\Tracking\UserTracker;

class UserTrackerTest extends TestCase
{
    private UserTracker $tracker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tracker = new UserTracker();
    }

    public function test_captures_ip_and_user_agent(): void
    {
        $data = $this->tracker->capture('127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0) Chrome/120');
        $this->assertSame('127.0.0.1', $data['ip_address']);
        $this->assertArrayHasKey('device', $data);
        $this->assertArrayHasKey('os', $data);
        $this->assertArrayHasKey('browser', $data);
    }

    public function test_detects_desktop(): void
    {
        $data = $this->tracker->capture('127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0) Chrome/120');
        $this->assertSame('Desktop', $data['device']);
    }

    public function test_detects_mobile(): void
    {
        $data = $this->tracker->capture('127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17) Mobile Safari');
        $this->assertSame('Mobile', $data['device']);
    }

    public function test_detects_chrome_browser(): void
    {
        $data = $this->tracker->capture('127.0.0.1', 'Mozilla/5.0 Chrome/120');
        $this->assertSame('Chrome', $data['browser']);
    }

    public function test_returns_local_geo_for_localhost(): void
    {
        $data = $this->tracker->capture('127.0.0.1', 'test-agent');
        $this->assertSame('Local', $data['geo']['country']);
    }
}
