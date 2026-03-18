<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUriSuite;
use Iterator;

final class HostTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield ['www.foo.bar', 'http://www.foo.bar'];
        yield ['www.foo.bar', 'www.foo.bar'];
        yield ['www.foo.bar', 'https://www.foo.bar'];
        yield ['foo.bar', 'http://foo.bar'];
        yield ['foo.bar', 'https://foo.bar'];
        yield ['www.foo.bar', 'www.foo.bar'];
        yield ['8.8.8.8', '8.8.8.8'];
        yield ['localhost', 'localhost'];
        yield ['www.foo.bar', 'www.foo.bar?hello=world'];
    }

    #[DataProvider('dataProvider')]
    public function testExpectedHost(string $expected, string $string): void
    {
        $this->assertSame($expected, $this->utility($string)->host());
    }
}
