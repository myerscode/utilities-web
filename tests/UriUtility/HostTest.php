<?php

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUriSuite;

class HostTest extends BaseUriSuite
{
    public static function dataProvider(): array
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

    #[DataProvider('dataProvider')]
    public function testExpectedHost(string $expected, string $string): void
    {
        $this->assertEquals($expected, $this->utility($string)->host());
    }
}
