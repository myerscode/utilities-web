<?php

declare(strict_types=1);

namespace Tests\ContentUtility;

use Mockery;
use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\FiveHundredResponseException;
use Myerscode\Utilities\Web\Resource\Response;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\BaseContentSuite;

final class ContentTest extends BaseContentSuite
{
    /**
     * Check that content turns html from a valid url
     */
    public function testContentNotFoundExceptionThrown(): void
    {
        $this->expectException(ContentNotFoundException::class);

        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();

        $legacyMock->shouldReceive('response')->once()->andReturn(new Response(404));

        $legacyMock->content();
    }

    public function testExpectedContent(): void
    {
        $content = '<html lang="en"><h1>Hello World</h1></html>';

        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $legacyMock->shouldReceive('response')->once()->andReturn(new Response(200, $content));

        $this->assertEquals($content, $legacyMock->content());
    }

    /**
     * @throws ContentNotFoundException
     */
    public function testUnreachableContentExceptionThrown(): void
    {
        $this->expectException(FiveHundredResponseException::class);

        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $legacyMock->shouldAllowMockingProtectedMethods();
        $legacyMock->shouldReceive('clientResponse')->once()->andReturn(new MockResponse('Not Found', ['http_code' => 500]));

        $legacyMock->content();
    }
}
