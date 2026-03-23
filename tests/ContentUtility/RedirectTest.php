<?php

declare(strict_types=1);

namespace Tests\ContentUtility;

use Mockery;
use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Exceptions\MaxRedirectsReachedException;
use Symfony\Component\HttpClient\Exception\RedirectionException;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\BaseContentSuite;

final class RedirectTest extends BaseContentSuite
{
    public function testResponseThrowsMaxRedirectsReachedException(): void
    {
        $this->expectException(MaxRedirectsReachedException::class);

        $mock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $mock->shouldAllowMockingProtectedMethods();

        $mockResponse = new MockResponse('', ['http_code' => 301]);
        // Force the mock response to resolve so we can trigger the redirect exception
        $mockResponse->getStatusCode();

        $mock->shouldReceive('clientResponse')->once()->andReturnUsing(function () {
            throw new RedirectionException(new MockResponse('', [
                'http_code' => 301,
                'response_headers' => ['location' => 'https://example.com'],
            ]));
        });

        $mock->response();
    }
}
