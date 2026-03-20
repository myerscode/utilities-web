<?php

declare(strict_types=1);

namespace Tests\ContentUtility;

use Myerscode\Utilities\Web\Exceptions\FiveHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\FourHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Myerscode\Utilities\Web\Resource\Response;
use Tests\BaseContentSuite;
use Tests\TestResponse;

final class ResponseTest extends BaseContentSuite
{
    public function testResponseReturnsResponseObject(): void
    {
        self::$server->setResponseOfPath('/response-ok', new TestResponse('Hello', [], 200));

        $response = $this->utility(self::serverUrl('/response-ok'))->response();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->code());
    }

    public function testResponseThrowsFiveHundredException(): void
    {
        $this->expectException(FiveHundredResponseException::class);

        self::$server->setResponseOfPath('/response-500', new TestResponse('', [], 500));

        $this->utility(self::serverUrl('/response-500'))->response();
    }

    public function testResponseThrowsFourHundredException(): void
    {
        $this->expectException(FourHundredResponseException::class);

        self::$server->setResponseOfPath('/response-400', new TestResponse('', [], 400));

        $this->utility(self::serverUrl('/response-400'))->response();
    }

    public function testResponseThrowsNetworkErrorForUnreachableHost(): void
    {
        $this->expectException(NetworkErrorException::class);

        $this->utility('http://0.0.0.0:1')->response();
    }

    public function testUrlReturnsConstructedUrl(): void
    {
        $utility = $this->utility('https://example.com');

        $this->assertSame('https://example.com', $utility->url());
    }
}
