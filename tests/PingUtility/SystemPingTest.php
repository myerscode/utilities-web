<?php

namespace Tests\PingUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BasePingSuite;

class SystemPingTest extends BasePingSuite
{
    protected function setUp(): void
    {
        // Ping is not supported on Windows GitHub Actions
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Skipping test on Windows');
        }
    }

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
