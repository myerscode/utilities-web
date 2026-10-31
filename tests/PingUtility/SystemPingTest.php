<?php

declare(strict_types=1);

namespace Tests\PingUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\BasePingSuite;
use Iterator;

/**
 * Exercises the real system ping command against live hosts, so its result
 * depends on the runner's outbound network/ICMP. Tagged 'network' and excluded
 * from CI (see ci.yml) to avoid nondeterministic failures; run locally or with
 * `--group network`. See the Option B ticket for making PingUtility injectable
 * so the parse logic can be unit-tested without a live network.
 */
#[Group('network')]
final class SystemPingTest extends BasePingSuite
{
    protected function setUp(): void
    {
        // Ping is not supported on Windows GitHub Actions
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Skipping test on Windows');
        }
    }

    public static function dataProvider(): Iterator
    {
        yield 'ip' => [true, '8.8.8.8'];
        yield 'valid url' => [true, 'https://myerscode.com'];
        yield 'invalid url' => [false, 'https://not.a.real.domain'];
    }

    #[DataProvider('dataProvider')]
    public function testPing(bool $expected, string $url): void
    {
        $this->assertEquals($expected, $this->utility($url)->ping()['alive']);
    }

}
