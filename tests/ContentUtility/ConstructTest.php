<?php

namespace Tests\ContentUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseContentSuite;

class ConstructTest extends BaseContentSuite
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

    /**
     * Check that the url is correctly assigned in the constructor
     *
     * @param number $expected The value expected to be returned
     * @param number $string The value to pass to the utility
     */
    #[DataProvider('dataProvider')]
    public function testConstructor(string $expected, string $string): void
    {
        $this->assertEquals($expected, $this->utility($string)->url());
    }

}
