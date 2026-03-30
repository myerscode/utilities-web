<?php

declare(strict_types=1);

namespace Tests\UriUtility;

use Myerscode\Utilities\Web\Data\ResponseFrom;
use Myerscode\Utilities\Web\Resource\Response;
use Tests\BaseUriSuite;
use Tests\TestResponse;

final class CheckTest extends BaseUriSuite
{
    public function testCheckDefaultsToCurl(): void
    {
        self::$server->setResponseOfPath('/uri-check-default', new TestResponse('', [], 200));

        $response = $this->utility(self::serverUrl('/uri-check-default'))->check();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->code());
    }

    public function testCheckReturnsResponse(): void
    {
        self::$server->setResponseOfPath('/uri-check', new TestResponse('', [], 200));

        $response = $this->utility(self::serverUrl('/uri-check'))->check(ResponseFrom::HTTP);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->code());
    }
}
