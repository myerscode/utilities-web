<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use Tests\BaseUriSuite;

final class AliasTest extends BaseUriSuite
{
    public function testTimeoutReturnsDefault(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame(10, $utility->timeout());
    }

    public function testUriReturnsValue(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame($utility->value(), $utility->uri());
    }

    public function testUrlReturnsValue(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame($utility->value(), $utility->url());
    }
}
