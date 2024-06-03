<?php

namespace Tests\UriUtility;

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

    /**
     * @param  number  $expected  The value expected to be returned
     * @param  number  $string  The value to pass to the utility
     *
     * @dataProvider dataProvider
     */
    public function testExpectedPort(string $string, ?int $expected): void
    {
        $this->assertSame($expected, $this->utility($string)->port());
    }

}
