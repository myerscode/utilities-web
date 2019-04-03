<?php

namespace Tests\UriUtility;

use Tests\BaseUrlSuite;

/**
 * @coversDefaultClass Myerscode\Utilities\Web\UrlUtility
 */
class IsHttpsTest extends BaseUrlSuite
{

    public function dataProvider()
    {
        return [
            [false, 'http://www.foo.bar'],
            [false, 'www.foo.bar'],
            [true, 'https://www.foo.bar'],
            [false, 'http://foo.bar'],
            [true, 'https://foo.bar'],
            [false, 'www.foo.bar'],
            [false, 'foo.bar'],
            [false, 'localhost'],
            [false, 'www.foo.bar?hello=world'],
            [false, 'www.foo.bar?hello[]=world&hello[]=world'],
        ];
    }

    /**
     * Check if the url is using https
     *
     * @param number $expected The value expected to be returned
     * @param number $string The value to pass to the utility
     * @dataProvider dataProvider
     * @covers ::isHttps
     */
    public function testExpectedIsHttps($expected, $string)
    {
        $this->assertEquals($expected, $this->utility($string)->isHttps());
    }

}
