<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\MaxRedirectsReachedException;
use Tests\BaseResponseSuite;
use Tests\TestResponse;

final class FollowRedirectsTest extends BaseResponseSuite
{
    public function testCurlFollowsRedirectsToFinalResponse(): void
    {
        self::$server->setResponseOfPath('/hop-start', new TestResponse('', ['Location' => '/hop-end'], 302));
        self::$server->setResponseOfPath('/hop-end', new TestResponse('', [], 200));

        $utility = $this->utility(self::serverUrl('/hop-start'))->setFollowRedirects(true);

        $this->assertSame(200, $utility->fromCurl()->code());
    }

    public function testCurlRefusesRedirectToOtherProtocol(): void
    {
        self::$server->setResponseOfPath('/to-ftp', new TestResponse('', ['Location' => 'ftp://example.com/file'], 302));

        $this->expectException(InvalidUrlException::class);

        $this->utility(self::serverUrl('/to-ftp'))->setFollowRedirects(true)->fromCurl();
    }

    public function testCurlReportsRedirectWhenNotFollowing(): void
    {
        self::$server->setResponseOfPath('/stay-start', new TestResponse('', ['Location' => '/stay-end'], 302));

        $this->assertSame(302, $this->utility(self::serverUrl('/stay-start'))->fromCurl()->code());
    }

    public function testCurlStopsAtMaxRedirects(): void
    {
        self::$server->setResponseOfPath('/loop', new TestResponse('', ['Location' => '/loop'], 302));

        $this->expectException(MaxRedirectsReachedException::class);

        $this->utility(self::serverUrl('/loop'))->setFollowRedirects(true)->setMaxRedirects(2)->fromCurl();
    }
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

    public function testHeadersFollowRedirectsToFinalResponse(): void
    {
        self::$server->setResponseOfPath('/h-start', new TestResponse('', ['Location' => '/h-end'], 302));
        self::$server->setResponseOfPath('/h-end', new TestResponse('', [], 200));

        $utility = $this->utility(self::serverUrl('/h-start'))->setFollowRedirects(true);

        $this->assertSame(200, $utility->fromHeaders()->code());
    }

    public function testHeadersReportFirstResponseWhenNotFollowing(): void
    {
        self::$server->setResponseOfPath('/h-stay', new TestResponse('', ['Location' => '/h-end'], 302));

        $this->assertSame(302, $this->utility(self::serverUrl('/h-stay'))->fromHeaders()->code());
    }
}
