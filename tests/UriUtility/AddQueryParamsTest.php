<?php

namespace Tests\UriUtility;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseUriSuite;

class AddQueryParamsTest extends BaseUriSuite
{
    public static function dataProvider(): array
    {
        return [
            'add query by string' => ['https://myerscode.com', 'foo=bar', 'https://myerscode.com?foo=bar'],
            'add query by array' => ['https://myerscode.com', ['foo' => 'bar'], 'https://myerscode.com?foo=bar'],
            'add another query by string' =>
                ['https://myerscode.com?hello=world', 'foo=bar', 'https://myerscode.com?hello=world&foo=bar'],
            'add another query by array' =>
                ['https://myerscode.com?hello=world', ['foo' => 'bar'], 'https://myerscode.com?hello=world&foo=bar'],
            'override another query by string' =>
                ['https://myerscode.com?hello=world', 'hello=bar', 'https://myerscode.com?hello=world&hello=bar'],
            'override another query by array' => ['https://myerscode.com?hello=world', ['hello' => 'bar'], 'https://myerscode.com?hello=world&hello=bar'],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testAddQueryParams(string $url, string|array $add, string $expected): void
    {
        $utility = $this->utility($url)->addQueryParameter($add);

        $this->assertEquals($expected, $utility->value());
    }
}
