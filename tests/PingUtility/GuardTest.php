<?php

declare(strict_types=1);

namespace Tests\PingUtility;

use Iterator;
use Myerscode\Utilities\Web\Exceptions\BlockedHostException;
use Myerscode\Utilities\Web\Utility;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BasePingSuite;

final class GuardTest extends BasePingSuite
{
    public static function blockedHosts(): Iterator
    {
        yield 'loopback' => ['127.0.0.1'];
        yield 'localhost' => ['localhost'];
        yield 'private' => ['10.0.0.1'];
        yield 'metadata' => ['169.254.169.254'];
        yield 'ipv6 loopback' => ['[::1]'];
    }

    #[DataProvider('blockedHosts')]
    public function testPingBlocksPrivateHostsByDefault(string $host): void
    {
        $this->expectException(BlockedHostException::class);

        $this->utility($host)->ping();
    }

    public function testUnresolvableHostIsNotAlive(): void
    {
        $this->assertFalse($this->utility('rupert.invalid')->isAlive());
    }

    public function testUtilityKeepsTheGuardOnPingByDefault(): void
    {
        $this->expectException(BlockedHostException::class);

        Utility::make('127.0.0.1')->ping()->ping();
    }

    public function testUtilityPassesTheOptOutToPing(): void
    {
        $this->expectNotToPerformAssertions();

        // allowed through the guard, so ping runs instead of throwing
        Utility::make('127.0.0.1')->allowPrivateNetworks()->ping()->ping();
    }
}
