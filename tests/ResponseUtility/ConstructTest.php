<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use League\Uri\Exceptions\SyntaxError;
use Myerscode\Utilities\Web\UriUtility;
use Tests\BaseResponseSuite;

final class ConstructTest extends BaseResponseSuite
{
    public function testConstructAddsSchemeWhenMissing(): void
    {
        $utility = $this->utility('example.com');

        $this->assertSame('https://example.com', $utility->value());
    }

    public function testConstructWithString(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame('https://example.com', $utility->value());
    }

    public function testConstructWithUriUtility(): void
    {
        $uriUtility = new UriUtility('https://example.com');
        $utility = $this->utility($uriUtility->value());

        $this->assertSame('https://example.com', $utility->value());
    }

    public function testDefaultTimeout(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame(10, $utility->timeout());
    }

    public function testEmptyUrlThrowsException(): void
    {
        $this->expectException(SyntaxError::class);

        $this->utility('');
    }

    public function testSetFollowRedirects(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertFalse($utility->followRedirects());

        $responseUtility = $utility->setFollowRedirects(true);

        $this->assertSame($utility, $responseUtility);
        $this->assertTrue($utility->followRedirects());
    }

    public function testSetTimeoutReturnsInstance(): void
    {
        $utility = $this->utility('https://example.com');
        $responseUtility = $utility->setTimeout(30);

        $this->assertSame($utility, $responseUtility);
        $this->assertSame(30, $utility->timeout());
    }

    public function testUriReturnsValue(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame($utility->value(), $utility->uri());
    }
}
