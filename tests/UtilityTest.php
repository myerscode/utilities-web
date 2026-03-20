<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\PingUtility;
use Myerscode\Utilities\Web\UriUtility;
use Myerscode\Utilities\Web\Utility;
use PHPUnit\Framework\TestCase;

final class UtilityTest extends TestCase
{
    public function testContentReturnsContentUtility(): void
    {
        $utility = new Utility('https://example.com');

        $this->assertInstanceOf(ContentUtility::class, $utility->content());
    }

    public function testPingReturnsPingUtility(): void
    {
        $utility = new Utility('https://example.com');

        $this->assertInstanceOf(PingUtility::class, $utility->ping());
    }

    public function testUrlReturnsUriUtility(): void
    {
        $utility = new Utility('https://example.com');

        $this->assertInstanceOf(UriUtility::class, $utility->url());
    }
}
