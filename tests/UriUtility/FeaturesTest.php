<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use Myerscode\Utilities\Web\UriUtility;
use Tests\BaseUriSuite;

final class FeaturesTest extends BaseUriSuite
{
    public function testEqualsReturnsFalseForDifferentUrl(): void
    {
        $a = $this->utility('https://example.com');
        $b = new UriUtility('https://other.com');

        $this->assertFalse($a->equals($b));
    }

    public function testEqualsReturnsTrueForSameUrl(): void
    {
        $a = $this->utility('https://example.com/path');
        $b = new UriUtility('https://example.com/path');

        $this->assertTrue($a->equals($b));
    }

    public function testFragmentReturnsEmptyWhenNone(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame('', $utility->fragment());
    }
    public function testFragmentReturnsFragment(): void
    {
        $utility = $this->utility('https://example.com/page#section');

        $this->assertSame('section', $utility->fragment());
    }

    public function testHasQueryParameterReturnsFalse(): void
    {
        $utility = $this->utility('https://example.com?foo=bar');

        $this->assertFalse($utility->hasQueryParameter('baz'));
    }

    public function testHasQueryParameterReturnsFalseWhenNoQuery(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertFalse($utility->hasQueryParameter('foo'));
    }

    public function testHasQueryParameterReturnsTrue(): void
    {
        $utility = $this->utility('https://example.com?foo=bar');

        $this->assertTrue($utility->hasQueryParameter('foo'));
    }

    public function testIsValidReturnsFalseForInvalidUrl(): void
    {
        $this->assertFalse($this->utility('https://!!!invalid')->isValid());
    }

    public function testIsValidReturnsTrueForValidUrl(): void
    {
        $this->assertTrue($this->utility('https://example.com')->isValid());
    }

    public function testRemoveQueryParameter(): void
    {
        $utility = $this->utility('https://example.com?foo=bar&baz=qux');
        $result = $utility->removeQueryParameter('foo');

        $this->assertSame($utility, $result);
        $this->assertSame('https://example.com?baz=qux', $utility->value());
    }

    public function testRemoveQueryParameterWhenOnly(): void
    {
        $utility = $this->utility('https://example.com?foo=bar');
        $utility->removeQueryParameter('foo');

        $this->assertSame('https://example.com', $utility->value());
    }

    public function testToArrayReturnsAllComponents(): void
    {
        $utility = $this->utility('https://example.com:8080/path?foo=bar#section');

        $this->assertSame([
            'scheme' => 'https',
            'host' => 'example.com',
            'port' => 8080,
            'path' => '/path',
            'query' => 'foo=bar',
            'fragment' => 'section',
        ], $utility->toArray());
    }

    public function testUserInfoReturnsNullWhenNone(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertNull($utility->userInfo());
    }

    public function testWithFragmentSetsFragment(): void
    {
        $utility = $this->utility('https://example.com');
        $result = $utility->withFragment('top');

        $this->assertSame($utility, $result);
        $this->assertSame('top', $utility->fragment());
        $this->assertSame('https://example.com#top', $utility->value());
    }

    public function testWithHostReplacesHost(): void
    {
        $utility = $this->utility('https://example.com/path');
        $utility->withHost('other.com');

        $this->assertSame('other.com', $utility->host());
        $this->assertSame('https://other.com/path', $utility->value());
    }

    public function testWithPathReplacesPath(): void
    {
        $utility = $this->utility('https://example.com/old');
        $utility->withPath('/new/path');

        $this->assertSame('/new/path', $utility->path());
    }

    public function testWithPortNullRemovesPort(): void
    {
        $utility = $this->utility('https://example.com:8080');
        $utility->withPort(null);

        $this->assertSame(443, $utility->port());
    }

    public function testWithPortSetsPort(): void
    {
        $utility = $this->utility('https://example.com');
        $utility->withPort(8080);

        $this->assertSame(8080, $utility->port());
    }

    public function testWithSchemeSwitchesToHttp(): void
    {
        $utility = $this->utility('https://example.com');
        $result = $utility->withScheme('http');

        $this->assertSame($utility, $result);
        $this->assertSame('http', $utility->scheme());
    }
}
