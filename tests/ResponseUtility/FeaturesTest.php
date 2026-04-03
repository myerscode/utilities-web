<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Myerscode\Utilities\Web\Data\ResponseFrom;
use Tests\BaseResponseSuite;
use Tests\TestResponse;

final class FeaturesTest extends BaseResponseSuite
{
    public function testHeadReturnsResponse(): void
    {
        self::$server->setResponseOfPath('/head-test', new TestResponse('', [], 200));

        $response = $this->utility(self::serverUrl('/head-test'))->head();

        $this->assertSame(200, $response->code());
    }

    public function testIsAliveReturnsFalseFor500(): void
    {
        self::$server->setResponseOfPath('/dead-test', new TestResponse('', [], 500));

        $this->assertFalse($this->utility(self::serverUrl('/dead-test'))->isAlive());
    }

    public function testIsAliveReturnsTrueFor200(): void
    {
        self::$server->setResponseOfPath('/alive-test', new TestResponse('', [], 200));

        $this->assertTrue($this->utility(self::serverUrl('/alive-test'))->isAlive());
    }

    public function testIsAliveWithHttpMethod(): void
    {
        self::$server->setResponseOfPath('/alive-http', new TestResponse('', [], 200));

        $this->assertTrue($this->utility(self::serverUrl('/alive-http'))->isAlive(ResponseFrom::HTTP));
    }
    public function testSetMaxRedirectsReturnsSelf(): void
    {
        $utility = $this->utility('https://example.com');
        $result = $utility->setMaxRedirects(5);

        $this->assertSame($utility, $result);
    }
}
