<?php

namespace Tests\ContentUtility;

use Myerscode\Utilities\Web\Exceptions\FourHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\FiveHundredResponseException;
use Myerscode\Utilities\Web\Resource\Dom;
use Tests\BaseContentSuite;
use Tests\TestResponse;

class DomTest extends BaseContentSuite
{
    public function testContentNotFoundExceptionThrown(): void
    {
        $this->expectException(FourHundredResponseException::class);

        self::$server->setResponseOfPath('', new TestResponse('', [], 404));

        $this->utility(self::serverUrl())->dom();
    }

    public function testExpectedContent(): void
    {
        self::$server->setResponseOfPath('', new TestResponse('<html><h1>Hello World</h1></html>', [], 200));

        $expected = new Dom('<html><h1>Hello World</h1></html>');
        $dom = $this->utility(self::serverUrl())->dom();
        $this->assertInstanceOf(Dom::class, $dom);
        $this->assertSame($expected->html(), $this->utility(self::serverUrl())->dom()->html());
    }

    public function testUnreachableContentExceptionThrown(): void
    {
        $this->expectException(FiveHundredResponseException::class);

        self::$server->setResponseOfPath('', new TestResponse('<html><h1>Hello World</h1></html>', [], 500));

        $this->utility(self::serverUrl())->dom();
    }
}
