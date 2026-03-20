<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Iterator;
use Tests\BaseUriSuite;

final class SchemeTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'http scheme' => ['http://example.com', 'http'];
        yield 'https scheme' => ['https://example.com', 'https'];
        yield 'defaults to https' => ['example.com', 'https'];
    }

    #[DataProvider('dataProvider')]
    public function testExpectedScheme(string $url, string $expected): void
    {
        $this->assertSame($expected, $this->utility($url)->scheme());
    }
}
