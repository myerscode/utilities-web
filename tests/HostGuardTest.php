<?php

declare(strict_types=1);

namespace Tests;

use Iterator;
use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Exceptions\BlockedHostException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\HostGuard;
use Myerscode\Utilities\Web\ResponseUtility;
use Myerscode\Utilities\Web\UriUtility;
use PHPUnit\Framework\Attributes\DataProvider;

final class HostGuardTest extends BaseSuite
{
    public static function blockedProvider(): Iterator
    {
        yield 'loopback' => ['http://127.0.0.1/'];
        yield 'rfc1918 10/8' => ['http://10.0.0.5/'];
        yield 'rfc1918 172.16/12' => ['http://172.16.0.1/'];
        yield 'rfc1918 192.168/16' => ['http://192.168.1.1/'];
        yield 'cloud metadata' => ['http://169.254.169.254/latest/meta-data'];
        yield 'cgnat' => ['http://100.64.0.1/'];
        yield 'unspecified' => ['http://0.0.0.0/'];
        yield 'ipv6 loopback' => ['http://[::1]/'];
        yield 'ipv6 unique local' => ['http://[fd00::1]/'];
        yield 'ipv6 link local' => ['http://[fe80::1]/'];
        yield 'ipv4 mapped ipv6' => ['http://[::ffff:127.0.0.1]/'];
        yield 'ipv4 mapped ipv6 hex' => ['http://[::ffff:7f00:1]/'];
        yield 'localhost' => ['http://localhost/'];
        yield 'decimal ip' => ['http://2130706433/'];
        yield 'hex ip' => ['http://0x7f000001/'];
        yield 'dotted hex ip' => ['http://0x7f.0.0.1/'];
        yield 'octal ip' => ['http://0177.0.0.1/'];
        yield 'short ip' => ['http://127.1/'];
        yield 'short private ip' => ['http://10.1/'];
    }

    public static function notNumericIpProvider(): Iterator
    {
        yield 'invalid octal digit' => ['http://08.0.0.1/'];
        yield 'octet over 255' => ['http://256.0.0.1/'];
        yield 'last part too large' => ['http://1.16777216/'];
    }

    public function testContentUtilityBlocksByDefault(): void
    {
        $this->expectException(BlockedHostException::class);

        (new ContentUtility(self::serverUrl('/blocked')))->content();
    }

    public function testContentUtilityPostBlocksByDefault(): void
    {
        $this->expectException(BlockedHostException::class);

        (new ContentUtility(self::serverUrl('/blocked')))->post(['name' => 'Gerald']);
    }

    public function testEveryResponseMethodBlocksByDefault(): void
    {
        foreach (['fromCurl', 'fromHeaders', 'fromHttpClient'] as $method) {
            try {
                (new ResponseUtility(self::serverUrl('/blocked')))->$method();
                $this->fail($method . ' did not block a loopback host');
            } catch (BlockedHostException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    #[DataProvider('blockedProvider')]
    public function testGuardBlocksHost(string $url): void
    {
        $this->expectException(BlockedHostException::class);

        HostGuard::assertSafe($url);
    }

    public function testNonHttpSchemeIsBlocked(): void
    {
        $this->expectException(BlockedHostException::class);

        HostGuard::assertSafe('file:///etc/passwd');
    }

    public function testOptOutAllowsPrivateNetworks(): void
    {
        self::$server->setResponseOfPath('/allowed', new TestResponse('Arthur', [], 200));

        $this->assertSame(200, (new ResponseUtility(self::serverUrl('/allowed')))->allowPrivateNetworks()->fromCurl()->code());
        $this->assertSame('Arthur', (new ContentUtility(self::serverUrl('/allowed')))->allowPrivateNetworks()->content());
        $this->assertSame(200, (new UriUtility(self::serverUrl('/allowed')))->allowPrivateNetworks()->check()->code());
    }

    public function testOptOutCanBeReverted(): void
    {
        $this->expectException(BlockedHostException::class);

        (new ResponseUtility(self::serverUrl('/allowed')))->allowPrivateNetworks()->allowPrivateNetworks(false)->fromCurl();
    }

    #[DataProvider('notNumericIpProvider')]
    public function testOutOfRangeNumericHostIsTreatedAsAHostname(string $url): void
    {
        // not a valid IPv4 shorthand, so it is resolved like a name and nothing answers
        $this->assertSame([], HostGuard::assertSafe($url));
    }

    public function testPublicIpIsAllowed(): void
    {
        $this->assertSame(['93.184.216.34'], HostGuard::assertSafe('https://93.184.216.34/'));
        $this->assertSame(['2606:2800:220:1:248:1893:25c8:1946'], HostGuard::assertSafe('https://[2606:2800:220:1:248:1893:25c8:1946]/'));
    }

    public function testPublicIpShorthandIsNormalised(): void
    {
        // 1572395042 is the decimal form of 93.184.216.34
        $this->assertSame(['93.184.216.34'], HostGuard::assertSafe('https://1572395042/'));
    }

    #[DataProvider('blockedProvider')]
    public function testResponseUtilityBlocksByDefault(string $url): void
    {
        $this->expectException(InvalidUrlException::class);

        (new ResponseUtility($url))->fromCurl();
    }

    public function testUriUtilityCheckBlocksByDefault(): void
    {
        $this->expectException(BlockedHostException::class);

        (new UriUtility(self::serverUrl('/blocked')))->check();
    }
}
