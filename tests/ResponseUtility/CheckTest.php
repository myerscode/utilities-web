<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Myerscode\Utilities\Web\Data\ResponseFrom;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Tests\BaseResponseSuite;
use Tests\TestResponse;

final class CheckTest extends BaseResponseSuite
{
    public function testCheckWithCurlEnum(): void
    {
        self::$server->setResponseOfPath('/check-curl', new TestResponse('', [], 200));

        $response = $this->utility(self::serverUrl('/check-curl'))->check(ResponseFrom::CURL);

        $this->assertSame(200, $response->code());
    }

    public function testCheckWithHeadersEnum(): void
    {
        self::$server->setResponseOfPath('/check-headers', new TestResponse('', [], 200));

        $response = $this->utility(self::serverUrl('/check-headers'))->check(ResponseFrom::HEADERS);

        $this->assertSame(200, $response->code());
    }

    public function testCheckWithHttpEnum(): void
    {
        self::$server->setResponseOfPath('/check-http', new TestResponse('', [], 200));

        $response = $this->utility(self::serverUrl('/check-http'))->check(ResponseFrom::HTTP);

        $this->assertSame(200, $response->code());
    }

    public function testCheckWithInvalidStringThrowsException(): void
    {
        $this->expectException(UnsupportedCheckMethodException::class);

        $this->utility(self::serverUrl())->check('invalid');
    }
}
