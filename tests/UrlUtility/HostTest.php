<?php

namespace Tests\UrlUtility;

use Tests\BaseUrlSuite;

/**
 * @coversDefaultClass \Myerscode\Utilities\Web\UrlUtility
 */
class HostTest extends BaseUrlSuite
{

    public function dataProvider()
    {
        return [
            ['www.foo.bar', 'http://www.foo.bar'],
            ['www.foo.bar', 'www.foo.bar'],
            ['www.foo.bar', 'https://www.foo.bar'],
            ['foo.bar', 'http://foo.bar'],
            ['foo.bar', 'https://foo.bar'],
            ['www.foo.bar', 'www.foo.bar'],
            ['8.8.8.8', '8.8.8.8'],
            ['localhost', 'localhost'],
            ['www.foo.bar', 'www.foo.bar?hello=world'],
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
    public function testExpectedHost($expected, $string)
    {
        $this->assertEquals($expected, $this->utility($string)->getHost());
    }

}
