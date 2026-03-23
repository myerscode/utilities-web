<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Tests\BaseResponseSuite;
use Tests\TestResponse;

final class FollowRedirectsTest extends BaseResponseSuite
{
    public function testFromCurlWithFollowRedirects(): void
    {
        self::$server->setResponseOfPath('/redirect-curl', new TestResponse('', [], 200));

        $utility = $this->utility(self::serverUrl('/redirect-curl'));
        $utility->setFollowRedirects(true);

        $response = $utility->fromCurl();

        $this->assertSame(200, $response->code());
    }

    public function testFromCurlWithoutFollowRedirects(): void
    {
        self::$server->setResponseOfPath('/no-redirect-curl', new TestResponse('', [], 200));

        $utility = $this->utility(self::serverUrl('/no-redirect-curl'));
        $utility->setFollowRedirects(false);

        $response = $utility->fromCurl();

        $this->assertSame(200, $response->code());
    }

    public function testFromHeadersWithFollowRedirects(): void
    {
        self::$server->setResponseOfPath('/redirect-headers', new TestResponse('', [], 200));

        $utility = $this->utility(self::serverUrl('/redirect-headers'));
        $utility->setFollowRedirects(true);

        $response = $utility->fromHeaders();

        $this->assertIsInt($response->code());
    }
}
