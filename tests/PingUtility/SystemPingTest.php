<?php

namespace Tests\PingUtility;

use Iterator;
use Tests\BasePingSuite;

class SystemPingTest extends BasePingSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'ip' => [true, '8.8.8.8'];
        yield 'valid url' => [true, 'https://myerscode.com'];
        yield 'invalid url' => [false, 'https://not.a.real.domain'];
    }

    /**
     * Check that the url is correctly assigned in the constructor
     *
     * @dataProvider dataProvider
     * @param $expected
     * @param $url
     */
    public function testPing(bool $expected, string $url): void
    {
        $this->assertEquals($expected, $this->utility($url)->ping()['alive']);
    }

}
