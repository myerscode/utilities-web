<?php

namespace Tests\PingUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BasePingSuite;

class SystemPingTest extends BasePingSuite
{
    public static function dataProvider(): array
    {
        return [
            'ip' => [true, '8.8.8.8'],
            'valid url' => [true, 'https://myerscode.com'],
            'invalid url' => [false, 'https://not.a.real.domain'],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testPing(bool $expected, string $url): void
    {
        $this->assertEquals($expected, $this->utility($url)->ping()['alive']);
    }

}
