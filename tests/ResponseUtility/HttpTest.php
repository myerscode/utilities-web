<?php

namespace Tests\ResponseUtility;

use Iterator;
use Tests\BaseResponseSuite;
use Tests\TestResponse;

class HttpTest extends BaseResponseSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'responds with 200' => ['/status/200', 200];
        yield 'responds with 300' => ['/status/300', 300];
        yield 'responds with 400' => ['/status/400', 400];
        yield 'responds with 404' => ['/status/404', 404];
        yield 'responds with 500' => ['/status/500', 500];
    }

    /**
     * Check that the url exists using http
     *
     * @dataProvider dataProvider
     *
     * @param $url
     * @param $expected
     */
    public function testCheckWithHttp(string $path, int $expected): void
    {
        self::$server->setResponseOfPath($path, new TestResponse($path, [], $expected));

        $response = $this->utility(self::serverUrl($path))->fromHttpClient();

        $this->assertSame($expected, $response->code());
    }
}
