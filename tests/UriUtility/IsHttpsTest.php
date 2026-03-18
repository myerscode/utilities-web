<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUriSuite;
use Iterator;

final class IsHttpsTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield [false, 'http://www.foo.bar'];
        yield [true, 'www.foo.bar'];
        yield [true, 'https://www.foo.bar'];
        yield [false, 'http://foo.bar'];
        yield [true, 'https://foo.bar'];
        yield [true, 'www.foo.bar'];
        yield [true, 'foo.bar'];
        yield [true, 'localhost'];
        yield [true, 'www.foo.bar?hello=world'];
        yield [true, 'www.foo.bar?hello[]=world&hello[]=world'];
        yield [false, 'http://www.foo.bar?hello[]=world&hello[]=world'];
    }

    #[DataProvider('dataProvider')]
    public function testExpectedIsHttps(bool $expected, string $string): void
    {
        $this->assertSame($expected, $this->utility($string)->isHttps());
    }

}
