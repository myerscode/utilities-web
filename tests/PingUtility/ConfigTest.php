<?php

declare(strict_types=1);

namespace Tests\PingUtility;

use Tests\BasePingSuite;

final class ConfigTest extends BasePingSuite
{
    public function testIsAliveForValidHost(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Skipping test on Windows');
        }

        $this->assertTrue($this->utility('8.8.8.8')->isAlive());
    }

    public function testIsAliveReturnsBool(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Skipping test on Windows');
        }

        $this->assertIsBool($this->utility('8.8.8.8')->isAlive());
    }

    public function testLatencyForValidHost(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Skipping test on Windows');
        }

        $latency = $this->utility('8.8.8.8')->latency();

        $this->assertIsFloat($latency);
    }

    public function testLatencyReturnsNullableFloat(): void
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Skipping test on Windows');
        }

        $latency = $this->utility('https://not.a.real.domain')->latency();

        $this->assertNull($latency);
    }
    public function testSetTimeoutReturnsSelf(): void
    {
        $utility = $this->utility('https://example.com');
        $result = $utility->setTimeout(5);

        $this->assertSame($utility, $result);
    }

    public function testSetTtlReturnsSelf(): void
    {
        $utility = $this->utility('https://example.com');
        $result = $utility->setTtl(128);

        $this->assertSame($utility, $result);
    }

    public function testUrlReturnsConstructedUrl(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame('https://example.com', $utility->url());
    }
}
