<?php

namespace Tests\ContentUtility;

use Exception;
use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\UnreachableContentException;
use Tests\BaseContentSuite;
use Tests\TestResponse;

class ContentTest extends BaseContentSuite
{
    /**
     * Check that content turns html from a valid url
     */
    public function testContentNotFoundExceptionThrown(): void
    {
        $this->expectException(ContentNotFoundException::class);

        self::$server->setResponseOfPath('', new TestResponse('', [], 404));

        $this->utility(self::serverUrl())->content();
    }

    /**
     * Check that content turns html from a valid url
     */
    public function testExpectedContent(): void
    {
        self::$server->setResponseOfPath('', new TestResponse('<html><h1>Hello World</h1></html>', [], 200));

        $this->assertSame('<html><h1>Hello World</h1></html>', $this->utility(self::serverUrl())->content());
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

        $mock->content();
    }
}
