<?php

declare(strict_types=1);

namespace Tests\ContentUtility;

use Myerscode\Utilities\Web\Exceptions\ResponseTooLargeException;
use Tests\BaseContentSuite;
use Tests\TestResponse;

final class MaxBytesTest extends BaseContentSuite
{
    public function testUnlimitedByDefault(): void
    {
        self::$server->setResponseOfPath('/big-default', new TestResponse(str_repeat('Rupert', 50000), [], 200));

        $this->assertSame(300000, strlen($this->utility(self::serverUrl('/big-default'))->content()));
    }

    public function testBodyWithinLimitIsReturned(): void
    {
        self::$server->setResponseOfPath('/small', new TestResponse('Gerald', [], 200));

        $this->assertSame('Gerald', $this->utility(self::serverUrl('/small'))->withMaxBytes(1024)->content());
    }

    public function testGetAbortsWhenBodyExceedsLimit(): void
    {
        self::$server->setResponseOfPath('/big-get', new TestResponse(str_repeat('Arthur', 50000), [], 200));

        $this->expectException(ResponseTooLargeException::class);

        $this->utility(self::serverUrl('/big-get'))->withMaxBytes(1024)->content();
    }

    public function testPostAbortsWhenBodyExceedsLimit(): void
    {
        self::$server->setResponseOfPath('/big-post', new TestResponse(str_repeat('Arthur', 50000), [], 200));

        $this->expectException(ResponseTooLargeException::class);

        $this->utility(self::serverUrl('/big-post'))->withMaxBytes(1024)->post(['name' => 'Gerald']);
    }

    public function testLimitCanBeRemoved(): void
    {
        self::$server->setResponseOfPath('/big-reset', new TestResponse(str_repeat('Rupert', 5000), [], 200));

        $utility = $this->utility(self::serverUrl('/big-reset'))->withMaxBytes(1024)->withMaxBytes(null);

        $this->assertSame(30000, strlen($utility->content()));
    }
}
