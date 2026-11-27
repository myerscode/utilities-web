<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Myerscode\Utilities\Web\ResponseUtility;
use ReflectionMethod;
use ReflectionProperty;
use Tests\BaseResponseSuite;
use Tests\TestResponse;

final class PinningAndEdgeCasesTest extends BaseResponseSuite
{
    public function testCurlRejectsUnparsableRedirectLocation(): void
    {
        self::$server->setResponseOfPath('/rupert-bad', new TestResponse('', ['Location' => 'http://'], 302));

        $this->expectException(InvalidUrlException::class);

        $this->utility(self::serverUrl('/rupert-bad'))->setFollowRedirects(true)->fromCurl();
    }

    public function testEmptyCurrentUrlIsRejected(): void
    {
        $utility = $this->utility(self::serverUrl('/Arthur'));

        (new ReflectionProperty($utility, 'http'))->setValue($utility, null);

        $this->expectException(EmptyUrlException::class);

        $utility->fromCurl();
    }

    public function testGuardedCurlPinsResolvedAddressForNameHosts(): void
    {
        $utility = (new ResponseUtility('http://134744072/Gerald'))->setTimeout(1);

        try {
            $this->assertIsInt($utility->fromCurl()->code());
        } catch (NetworkErrorException $networkErrorException) {
            $this->assertNotSame('', $networkErrorException->getMessage());
        }
    }

    public function testGuardedHeadersPinsNumericHostBeforeRequesting(): void
    {
        // 134744072 is 8.8.8.8; the request is vetted and pinned, then fails fast or succeeds depending on the network
        $utility = (new ResponseUtility('http://134744072/Rupert'))->setTimeout(1);

        try {
            $this->assertIsInt($utility->fromHeaders()->code());
        } catch (NetworkErrorException $networkErrorException) {
            $this->assertStringContainsString('134744072', $networkErrorException->getMessage());
        }
    }

    public function testHeadersRejectUnparsableRedirectLocation(): void
    {
        self::$server->setResponseOfPath('/gerald-bad', new TestResponse('', ['Location' => 'http://'], 302));

        $this->expectException(InvalidUrlException::class);

        $this->utility(self::serverUrl('/gerald-bad'))->setFollowRedirects(true)->fromHeaders();
    }

    public function testPinBracketsIpv6Targets(): void
    {
        $utility = new ResponseUtility('https://[2606:4700:4700::1111]/Gerald');

        [$target, $options] = (new ReflectionMethod($utility, 'pinToVettedIp'))
            ->invoke($utility, 'https://[2606:4700:4700::1111]/Gerald', []);

        $this->assertSame('https://[2606:4700:4700::1111]/Gerald', $target);
        $this->assertSame('2606:4700:4700::1111', $options['ssl']['peer_name']);
    }

    public function testPinFailsForUnresolvableHost(): void
    {
        $utility = new ResponseUtility('http://arthur.invalid/');

        $this->expectException(NetworkErrorException::class);

        (new ReflectionMethod($utility, 'pinToVettedIp'))->invoke($utility, 'http://arthur.invalid/', []);
    }
    public function testPinPointsRequestAtVettedIpAndKeepsHostHeader(): void
    {
        $utility = new ResponseUtility('http://8.8.8.8:8080/Rupert');

        [$target, $options] = (new ReflectionMethod($utility, 'pinToVettedIp'))
            ->invoke($utility, 'http://8.8.8.8:8080/Rupert', ['http' => ['timeout' => 1]]);

        $this->assertSame('http://8.8.8.8:8080/Rupert', $target);
        $this->assertSame('Host: 8.8.8.8:8080', $options['http']['header']);
        $this->assertSame(1, $options['http']['timeout']);
        $this->assertSame(['peer_name' => '8.8.8.8', 'SNI_enabled' => true], $options['ssl']);
    }
}
