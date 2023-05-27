<?php

namespace Tests\UriUtility;

use Iterator;
use Tests\BaseUriSuite;

class IsHttpsTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield [false, 'http://www.foo.bar'];
        yield [false, 'www.foo.bar'];
        yield [true, 'https://www.foo.bar'];
        yield [false, 'http://foo.bar'];
        yield [true, 'https://foo.bar'];
        yield [false, 'www.foo.bar'];
        yield [false, 'foo.bar'];
        yield [false, 'localhost'];
        yield [false, 'www.foo.bar?hello=world'];
        yield [false, 'www.foo.bar?hello[]=world&hello[]=world'];
    }

    /**
     * Check if the url is using https
     *
     * @param number $expected The value expected to be returned
     * @param number $string The value to pass to the utility
     * @dataProvider dataProvider
     */
    public function testExpectedIsHttps(bool $expected, string $string): void
    {
        $this->assertEquals($expected, $this->utility($string)->isHttps());
    }

}
