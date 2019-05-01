<?php

namespace Tests\UriUtility;

use Tests\BaseUriSuite;

/**
 * @coversDefaultClass \Myerscode\Utilities\Web\UriUtility
 */
class HttpTest extends BaseUriSuite
{

    public function dataProvider()
    {
        return [
            'responds with 200' => ['https://httpbin.org/status/200', 200],
            'responds with 300' => ['https://httpbin.org/status/300', 300],
            'responds with 400' => ['https://httpbin.org/status/400', 400],
            'responds with 404' => ['https://httpbin.org/status/404', 404],
            'responds with 500' => ['https://httpbin.org/status/500', 500],
        ];
    }

    /**
     * Check that the url exists using http
     *
     * @dataProvider dataProvider
     * @covers ::checkWithHttp
     * @param $url
     * @param $expected
     */
    public function testCheckWithHttp($url, $expected)
    {
        $response = $this->utility($url)->checkWithHttp();

        $this->assertEquals($expected, $response->code());
    }
}
