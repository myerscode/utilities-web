<?php

namespace Tests\UriUtility;

use Iterator;
use Tests\BaseUriSuite;

class HostTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield ['www.foo.bar', 'http://www.foo.bar'];
        yield ['www.foo.bar', 'www.foo.bar'];
        yield ['www.foo.bar', 'https://www.foo.bar'];
        yield ['foo.bar', 'http://foo.bar'];
        yield ['foo.bar', 'https://foo.bar'];
        yield ['www.foo.bar', 'www.foo.bar'];
        yield ['8.8.8.8', '8.8.8.8'];
        yield ['localhost', 'localhost'];
        yield ['www.foo.bar', 'www.foo.bar?hello=world'];
    }

    /**
     * Check if the url is using https
     *
     * @param number $expected The value expected to be returned
     * @param number $string The value to pass to the utility
     * @dataProvider dataProvider
     */
    public function testExpectedHost(string $expected, string $string): void
    {
        $this->assertSame($expected, $this->utility($string)->host());
    }

}
