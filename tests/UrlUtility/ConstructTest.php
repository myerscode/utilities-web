<?php

namespace Tests\UrlUtility;

use Tests\BaseUrlSuite;

/**
 * @coversDefaultClass \Myerscode\Utilities\Web\UrlUtility
 */
class ConstructTest extends BaseUrlSuite
{

    public function dataProvider()
    {
        return [
            ['http://www.foo.bar', 'http://www.foo.bar'],
            ['http://www.foo.bar', 'www.foo.bar'],
            ['https://www.foo.bar', 'https://www.foo.bar'],
            ['http://foo.bar', 'http://foo.bar'],
            ['https://foo.bar', 'https://foo.bar'],
            ['http://www.foo.bar', 'www.foo.bar'],
            ['http://foo.bar', 'foo.bar'],
            ['http://localhost', 'localhost'],
            ['http://www.foo.bar?hello=world', 'www.foo.bar?hello=world'],
            ['http://www.foo.bar?hello[]=world&hello[]=world', 'www.foo.bar?hello[]=world&hello[]=world'],
        ];
    }

    /**
     * Check that the url is correctly assigned in the constructor
     *
     * @param number $expected The value expected to be returned
     * @param number $string The value to pass to the utility
     * @dataProvider dataProvider
     * @covers ::__construct
     */
    public function testExpectedIsAlpha($expected, $string)
    {
        $this->assertEquals($expected, $this->utility($string)->value());
    }

}
