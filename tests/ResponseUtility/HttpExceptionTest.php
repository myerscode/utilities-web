<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

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
}
