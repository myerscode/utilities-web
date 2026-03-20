<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use PHPUnit\Framework\Attributes\DataProvider;
use Iterator;
use Tests\BaseUriSuite;

final class PathTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'root path' => ['https://example.com', ''];
        yield 'with path' => ['https://example.com/foo/bar', '/foo/bar'];
        yield 'with trailing slash' => ['https://example.com/foo/', '/foo/'];
    }

    #[DataProvider('dataProvider')]
    public function testExpectedPath(string $url, string $expected): void
    {
        $this->assertSame($expected, $this->utility($url)->path());
    }
}
