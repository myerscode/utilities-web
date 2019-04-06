<?php

namespace Tests\PingUtility;

use Tests\BasePingSuite;

/**
 * @coversDefaultClass \Myerscode\Utilities\Web\PingUtility
 */
class SystemPingTest extends BasePingSuite
{

    public function dataProvider()
    {
        return [
            'ip' => [true, '8.8.8.8'],
            'valid url' => [true, 'https://myerscode.com'],
            'invalid url' => [false, 'https://not.a.real.domain'],
        ];
    }

    /**
     * Check that the url is correctly assigned in the constructor
     *
     * @dataProvider dataProvider
     * @covers ::__construct
     * @param $expected
     * @param $url
     */
    public function testPing($expected, $url)
    {
        $this->assertEquals($expected, $this->utility($url)->ping()['alive']);
    }

}
