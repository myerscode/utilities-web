<?php


namespace Tests\UriUtility;


use Tests\BaseUriSuite;

class AddQueryParamsTest extends BaseUriSuite
{

    public function dataProvider()
    {
        return [
            'add query by string' => ['https://myerscode.com', 'foo=bar', 'https://myerscode.com?foo=bar'],
            'add query by array' => ['https://myerscode.com', ['foo'=>'bar'], 'https://myerscode.com?foo=bar'],
            'add another query by string' => ['https://myerscode.com?hello=world', 'foo=bar', 'https://myerscode.com?hello=world&foo=bar'],
            'add another query by array' => ['https://myerscode.com?hello=world', ['foo'=>'bar'], 'https://myerscode.com?hello=world&foo=bar'],
            'override another query by string' => ['https://myerscode.com?hello=world', 'hello=bar', 'https://myerscode.com?hello=bar'],
            'override another query by array' => ['https://myerscode.com?hello=world', ['hello'=>'bar'], 'https://myerscode.com?hello=bar'],
        ];
    }

    /**
     * Check that the url exists using curl
     *
     * @dataProvider dataProvider
     * @covers ::addQueryParameter
     * @param $url
     * @param $expected
     * @throws \Exception
     */
    public function testAddQueryParams($url, $add, $expected)
    {
        $utility = $this->utility($url)->addQueryParameter($add);

        $this->assertEquals($expected, $utility->value());
    }
}
