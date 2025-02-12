<?php

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Iterator;
use Tests\BaseUriSuite;

class PortTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'http not set' => ['http://example.com', 80];
        yield 'https not set' => ['https://example.com', 443];
        yield '8080 set' => ['https://example.com:8080', 8080];
    }

    #[DataProvider('dataProvider')]
    public function testExpectedPort(string $string, ?int $expected): void
    {
        $this->assertSame($expected, $this->utility($string)->port());
    }
}
