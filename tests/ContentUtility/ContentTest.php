<?php

namespace Tests\ContentUtility;

use Mockery;
use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\FiveHundredResponseException;
use Myerscode\Utilities\Web\Resource\Response;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\BaseContentSuite;

class ContentTest extends BaseContentSuite
{
    /**
     * Check that content turns html from a valid url
     */
    public function testContentNotFoundExceptionThrown(): void
    {
        $this->expectException(ContentNotFoundException::class);

        $stub = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();

        $stub->shouldReceive('response')->once()->andReturn(new Response(404));

        $stub->content();
    }

    public function testExpectedContent(): void
    {
        $content = '<html lang="en"><h1>Hello World</h1></html>';

        $stub = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $stub->shouldReceive('response')->once()->andReturn(new Response(200, $content));

        $this->assertEquals($content, $stub->content());
    }

    /**
     * @throws ContentNotFoundException
     */
    public function testUnreachableContentExceptionThrown(): void
    {
        $this->expectException(FiveHundredResponseException::class);

        $stub = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $stub->shouldAllowMockingProtectedMethods();
        $stub->shouldReceive('clientResponse')->once()->andReturn(new MockResponse('Not Found', ['http_code' => 500]));

        $stub->content();
    }
}
