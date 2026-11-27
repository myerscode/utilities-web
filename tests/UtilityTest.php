<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Exceptions\BlockedHostException;
use Myerscode\Utilities\Web\PingUtility;
use Myerscode\Utilities\Web\ResponseUtility;
use Myerscode\Utilities\Web\UriUtility;
use Myerscode\Utilities\Web\Utility;

final class UtilityTest extends BaseSuite
{
    public function testContentReturnsContentUtility(): void
    {
        $utility = new Utility('https://example.com');

        $this->assertInstanceOf(ContentUtility::class, $utility->content());
    }

    public function testIsAliveReturnsFalseFor500(): void
    {
        self::$server->setResponseOfPath('/utility-dead', new TestResponse('', [], 500));

        $utility = (new Utility(self::serverUrl('/utility-dead')))->allowPrivateNetworks();

        $this->assertFalse($utility->isAlive());
    }

    public function testIsAliveReturnsTrueFor200(): void
    {
        self::$server->setResponseOfPath('/utility-alive', new TestResponse('', [], 200));

        $utility = (new Utility(self::serverUrl('/utility-alive')))->allowPrivateNetworks();

        $this->assertTrue($utility->isAlive());
    }

    public function testMakeReturnsUtilityInstance(): void
    {
        $utility = Utility::make('https://example.com');

        $this->assertInstanceOf(Utility::class, $utility);
    }

    public function testPingReturnsPingUtility(): void
    {
        $utility = new Utility('https://example.com');

        $this->assertInstanceOf(PingUtility::class, $utility->ping());
    }

    public function testResponseReturnsResponseUtility(): void
    {
        $utility = new Utility('https://example.com');

        $this->assertInstanceOf(ResponseUtility::class, $utility->response());
    }

    public function testUrlReturnsUriUtility(): void
    {
        $utility = new Utility('https://example.com');

        $this->assertInstanceOf(UriUtility::class, $utility->url());
    }

    public function testPrivateNetworksAreBlockedByDefault(): void
    {
        $this->expectException(BlockedHostException::class);

        (new Utility(self::serverUrl('/blocked')))->content()->content();
    }

    public function testAllowPrivateNetworksPassesThroughToUtilities(): void
    {
        self::$server->setResponseOfPath('/utility-allowed', new TestResponse('Rupert', [], 200));

        $utility = (new Utility(self::serverUrl('/utility-allowed')))->allowPrivateNetworks();

        $this->assertSame('Rupert', $utility->content()->content());
        $this->assertTrue($utility->url()->check()->isSuccessful());
    }
}
