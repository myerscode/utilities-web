<?php

namespace Tests\ContentUtility;

use Exception;
use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\UnreachableContentException;
use Myerscode\Utilities\Web\Resource\Dom;
use Tests\BaseContentSuite;
use Tests\TestResponse;

class DomTest extends BaseContentSuite
{
    /**
     * Check that content turns html from a valid url
     */
    public function testContentNotFoundExceptionThrown(): void
    {
        $this->expectException(ContentNotFoundException::class);

        self::$server->setResponseOfPath('', new TestResponse('', [], 404));

        $this->utility(self::serverUrl())->dom();
    }

    /**
     * Check that content turns html from a valid url
     */
    public function testExpectedContent(): void
    {
        self::$server->setResponseOfPath('', new TestResponse('<html><h1>Hello World</h1></html>', [], 200));

        $expected = new Dom('<html><h1>Hello World</h1></html>');
        $dom = $this->utility(self::serverUrl())->dom();
        $this->assertInstanceOf(Dom::class, $dom);
        $this->assertSame($expected->html(), $this->utility(self::serverUrl())->dom()->html());
    }

    /**
     * Check that content turns html from a valid url
     */
    public function testUnreachableContentExceptionThrown(): void
    {
        $this->expectException(UnreachableContentException::class);

        $mock = $this->getMockBuilder(ContentUtility::class)
            ->setConstructorArgs(['http://localhost'])
            ->onlyMethods(['response'])
            ->getMock();

        $mock->expects($this->once())
            ->method('response')
            ->willThrowException(new Exception());

        $mock->dom();
    }
}
