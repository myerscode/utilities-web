<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Iterator;
use Tests\BaseUriSuite;

final class QueryTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'no query' => ['https://example.com', ''];
        yield 'with query' => ['https://example.com?foo=bar', 'foo=bar'];
        yield 'multiple params' => ['https://example.com?foo=bar&baz=qux', 'foo=bar&baz=qux'];
    }

    #[DataProvider('dataProvider')]
    public function testExpectedQuery(string $url, string $expected): void
    {
        $this->assertSame($expected, $this->utility($url)->query());
    }

    public function testGetQueryParametersReturnsArray(): void
    {
        $params = $this->utility('https://example.com?foo=bar&baz=qux')->getQueryParameters();

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $params);
    }

    public function testGetQueryParametersReturnsEmptyForNoQuery(): void
    {
        $params = $this->utility('https://example.com')->getQueryParameters();

        $this->assertSame([], $params);
    }

    public function testGetQueryStringReturnsString(): void
    {
        $queryString = $this->utility('https://example.com?foo=bar&baz=qux')->getQueryString();

        $this->assertSame('foo=bar&baz=qux', $queryString);
    }

    public function testSetQueryByArray(): void
    {
        $utility = $this->utility('https://example.com?old=value');
        $utility->setQuery(['new' => 'value']);

        $this->assertSame('https://example.com?new=value', $utility->value());
    }

    public function testSetQueryByString(): void
    {
        $utility = $this->utility('https://example.com?old=value');
        $utility->setQuery('new=value');

        $this->assertSame('https://example.com?new=value', $utility->value());
    }
}
