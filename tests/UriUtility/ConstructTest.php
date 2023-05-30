<?php

namespace Tests\UriUtility;

use Iterator;
use Tests\BaseUriSuite;

class ConstructTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield ['http://www.foo.bar', 'http://www.foo.bar'];
        yield ['http://www.foo.bar', 'www.foo.bar'];
        yield ['https://www.foo.bar', 'https://www.foo.bar'];
        yield ['http://foo.bar', 'http://foo.bar'];
        yield ['https://foo.bar', 'https://foo.bar'];
        yield ['http://www.foo.bar', 'www.foo.bar'];
        yield ['http://foo.bar', 'foo.bar'];
        yield ['http://localhost', 'localhost'];
        yield ['http://www.foo.bar?hello=world', 'www.foo.bar?hello=world'];
        yield ['http://www.foo.bar?hello[]=world&hello[]=world', 'www.foo.bar?hello[]=world&hello[]=world'];
    }

    /**
     * Check that the url is correctly assigned in the constructor
     *
     * @param number $expected The value expected to be returned
     * @param number $string The value to pass to the utility
     * @dataProvider dataProvider
     */
    public function testExpectedIsAlpha(string $expected, string $string): void
    {
        $this->assertSame($expected, $this->utility($string)->value());
    }

}
