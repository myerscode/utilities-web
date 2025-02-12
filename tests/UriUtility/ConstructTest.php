<?php

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUriSuite;

class ConstructTest extends BaseUriSuite
{
    public static function dataProvider(): array
    {
        return [
            ['http://www.foo.bar', 'http://www.foo.bar'],
            ['https://www.foo.bar', 'www.foo.bar'],
            ['https://www.foo.bar', 'https://www.foo.bar'],
            ['http://foo.bar', 'http://foo.bar'],
            ['https://foo.bar', 'https://foo.bar'],
            ['https://www.foo.bar', 'www.foo.bar'],
            ['https://foo.bar', 'foo.bar'],
            ['https://localhost', 'localhost'],
            ['https://www.foo.bar?hello=world', 'www.foo.bar?hello=world'],
            ['https://www.foo.bar?hello[]=world&hello[]=world', 'www.foo.bar?hello[]=world&hello[]=world'],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testConstructor(string $expected, string $string): void
    {
        $this->assertEquals($expected, $this->utility($string)->value());
    }

}
