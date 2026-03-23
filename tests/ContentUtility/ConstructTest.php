<?php

declare(strict_types=1);

namespace Tests\ContentUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseContentSuite;
use Iterator;

final class ConstructTest extends BaseContentSuite
{
    public static function dataProvider(): Iterator
    {
        yield ['http://www.foo.bar', 'http://www.foo.bar'];
        yield ['https://www.foo.bar', 'www.foo.bar'];
        yield ['https://www.foo.bar', 'https://www.foo.bar'];
        yield ['http://foo.bar', 'http://foo.bar'];
        yield ['https://foo.bar', 'https://foo.bar'];
        yield ['https://www.foo.bar', 'www.foo.bar'];
        yield ['https://foo.bar', 'foo.bar'];
        yield ['https://localhost', 'localhost'];
        yield ['https://www.foo.bar?hello=world', 'www.foo.bar?hello=world'];
        yield ['https://www.foo.bar?hello[]=world&hello[]=world', 'www.foo.bar?hello[]=world&hello[]=world'];
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
        $this->assertSame($expected, $this->utility($string)->url());
    }

}
