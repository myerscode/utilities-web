<?php

namespace Tests\ResponseUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Iterator;
use Tests\BaseResponseSuite;
use Tests\TestResponse;

class HeadersTest extends BaseResponseSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'responds with 200' => ['/status/200', 200];
        yield 'responds with 300' => ['/status/300', 300];
        yield 'responds with 400' => ['/status/400', 400];
        yield 'responds with 404' => ['/status/404', 404];
        yield 'responds with 500' => ['/status/500', 500];
    }

    #[DataProvider('dataProvider')]
    public function testCheckWithHeaders(string $path, int $expected): void
    {
        self::$server->setResponseOfPath($path, new TestResponse($path, [], $expected));

        $response = $this->utility(self::serverUrl($path))->fromHeaders();

        $this->assertSame($expected, $response->code());
    }
}
