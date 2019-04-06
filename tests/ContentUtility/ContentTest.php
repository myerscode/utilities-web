<?php

namespace Tests\ContentUtility;

use Myerscode\Utilities\Web\ContentUtility;
use Tests\BaseContentSuite;

/**
 * @coversDefaultClass Myerscode\Utilities\Web\ContentUtility
 */
class ContentTest extends BaseContentSuite
{

    /**
     * Check that content turns html from a valid url
     * @covers ::content
     *
     * @expectedException \Myerscode\Utilities\Web\Exceptions\ContentNotFoundException
     */
    public function testContentNotFoundExceptionThrown()
    {
        $clientStub = $this->getMockBuilder('Client')->setMethods(['send'])->getMock();

        $responseStub = $this->getMockBuilder('Response')->setMethods(['getStatusCode', 'getBody'])->getMock();

        $clientStub->expects($this->once())
            ->method('send')
            ->will($this->returnValue($responseStub));

        $responseStub->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(404));

        $stub = $this->getMockBuilder(ContentUtility::class)
            ->setConstructorArgs(['http://localhost/foo-bar'])
            ->setMethods(['client'])
            ->getMock();

        $stub->expects($this->once())
            ->method('client')
            ->will($this->returnValue($clientStub));


        $stub->content();
    }

    /**
     * Check that content turns html from a valid url
     * @covers ::content
     */
    public function testExpectedContent()
    {
        $clientStub = $this->getMockBuilder('Client')->setMethods(['send'])->getMock();

        $responseStub = $this->getMockBuilder('Response')->setMethods(['getStatusCode', 'getBody'])->getMock();

        $clientStub->expects($this->once())
            ->method('send')
            ->will($this->returnValue($responseStub));

        $responseStub->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $responseStub->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('<html><h1>Hello World</h1></html>'));

        $stub = $this->getMockBuilder(ContentUtility::class)
            ->setConstructorArgs(['http://localhost'])
            ->setMethods(['client'])
            ->getMock();

        $stub->expects($this->once())
            ->method('client')
            ->will($this->returnValue($clientStub));


        $this->assertEquals('<html><h1>Hello World</h1></html>', $stub->content());
    }

    /**
     * Check that content turns html from a valid url
     * @covers ::content
     *
     * @expectedException \Myerscode\Utilities\Web\Exceptions\UnreachableContentException
     */
    public function testUnreachableContentExceptionThrown()
    {
        $clientStub = $this->getMockBuilder('Client')->setMethods(['send'])->getMock();

        $clientStub->expects($this->once())
            ->method('send')
            ->will($this->throwException(new \Exception()));

        $stub = $this->getMockBuilder(ContentUtility::class)
            ->setConstructorArgs(['http://localhost'])
            ->setMethods(['client'])
            ->getMock();

        $stub->expects($this->once())
            ->method('client')
            ->will($this->returnValue($clientStub));

        $stub->content();
    }
}