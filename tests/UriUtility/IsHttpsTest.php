<?php

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUriSuite;

class IsHttpsTest extends BaseUriSuite
{
    public static function dataProvider(): array
    {
        return [
            [false, 'http://www.foo.bar'],
            [true, 'www.foo.bar'],
            [true, 'https://www.foo.bar'],
            [false, 'http://foo.bar'],
            [true, 'https://foo.bar'],
            [true, 'www.foo.bar'],
            [true, 'foo.bar'],
            [true, 'localhost'],
            [true, 'www.foo.bar?hello=world'],
            [true, 'www.foo.bar?hello[]=world&hello[]=world'],
            [false, 'http://www.foo.bar?hello[]=world&hello[]=world'],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testExpectedIsHttps(bool $expected, string $string): void
    {
        $this->assertEquals($expected, $this->utility($string)->isHttps());
    }

}
