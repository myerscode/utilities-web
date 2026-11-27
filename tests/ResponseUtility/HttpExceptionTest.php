<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Tests\BaseResponseSuite;
use Tests\TestResponse;

final class HttpExceptionTest extends BaseResponseSuite
{
    public function testFromHttpClientHandlesClientException(): void
    {
        self::$server->setResponseOfPath('/http-400', new TestResponse('', [], 400));

        $response = $this->utility(self::serverUrl('/http-400'))->fromHttpClient();

        $this->assertSame(400, $response->code());
    }

    public function testFromHttpClientHandlesServerException(): void
    {
        self::$server->setResponseOfPath('/http-500', new TestResponse('', [], 500));

        $response = $this->utility(self::serverUrl('/http-500'))->fromHttpClient();

        $this->assertSame(500, $response->code());
    }

    public function testFromHttpClientReturnsRealClientErrorCode(): void
    {
        self::$server->setResponseOfPath('/http-418', new TestResponse('', [], 418));

        $this->assertSame(418, $this->utility(self::serverUrl('/http-418'))->fromHttpClient()->code());
    }

    public function testFromHttpClientReturnsRealServerErrorCode(): void
    {
        self::$server->setResponseOfPath('/http-503', new TestResponse('', [], 503));

        $this->assertSame(503, $this->utility(self::serverUrl('/http-503'))->fromHttpClient()->code());
    }

    public function testFromHttpClientReportsRedirectWhenNotFollowing(): void
    {
        self::$server->setResponseOfPath('/http-r-start', new TestResponse('', ['Location' => '/http-r-end'], 302));
        self::$server->setResponseOfPath('/http-r-end', new TestResponse('', [], 200));

        $utility = $this->utility(self::serverUrl('/http-r-start'));

        $this->assertSame(302, $utility->fromHttpClient()->code());
        $this->assertSame(200, $utility->setFollowRedirects(true)->fromHttpClient()->code());
    }

    public function testFromHttpClientThrowsNetworkErrorWhenUnreachable(): void
    {
        $this->expectException(NetworkErrorException::class);

        $this->utility('http://127.0.0.1:1/')->fromHttpClient();
    }
}
