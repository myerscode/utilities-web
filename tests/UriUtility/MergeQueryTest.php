<?php

namespace Tests\UriUtility;

use Iterator;
use Exception;
use Tests\BaseUriSuite;

class MergeQueryTest extends BaseUriSuite
{
    public static function dataProvider(): Iterator
    {
        yield 'empty query' => ['https://myerscode.com', '', 'https://myerscode.com'];
        yield 'add query by string' => ['https://myerscode.com', 'foo=bar', 'https://myerscode.com?foo=bar'];
        yield 'add query by array' => ['https://myerscode.com', ['foo' => 'bar'], 'https://myerscode.com?foo=bar'];
        yield 'add another query by string' => ['https://myerscode.com?hello=world', 'foo=bar', 'https://myerscode.com?hello=world&foo=bar'];
        yield 'add another query by array' => ['https://myerscode.com?hello=world', ['foo' => 'bar'], 'https://myerscode.com?hello=world&foo=bar'];
        yield 'override another query by string' => ['https://myerscode.com?hello=world', 'hello=bar', 'https://myerscode.com?hello=bar'];
        yield 'override another query by array' => ['https://myerscode.com?hello=world', ['hello' => 'bar'], 'https://myerscode.com?hello=bar'];
    }

    /**
     * Check that the url exists using curl
     *
     * @dataProvider dataProvider
     *
     * @param $url
     * @param $expected
     *
     * @throws Exception
     */
    public function testAddQueryParams(string $url, string|array $add, string $expected): void
    {
        $uriUtility = $this->utility($url)->mergeQuery($add);

        $this->assertSame($expected, $uriUtility->value());
    }
}