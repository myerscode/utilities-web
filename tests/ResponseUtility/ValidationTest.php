<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Myerscode\Utilities\Web\ResponseUtility;
use Tests\BaseResponseSuite;

final class ValidationTest extends BaseResponseSuite
{
    public function testConstructorRejectsMalformedUrl(): void
    {
        // league/uri errors are wrapped, so every league version surfaces InvalidUrlException
        $this->expectException(InvalidUrlException::class);

        new ResponseUtility('https://not a valid url');
    }
    public function testConstructorThrowsInvalidUrlForUnderscoreHost(): void
    {
        $this->expectException(InvalidUrlException::class);

        new ResponseUtility('foo_bar');
    }

    public function testFromCurlThrowsInvalidUrlException(): void
    {
        $this->expectException(InvalidUrlException::class);

        $this->utility('https://!!!invalid')->fromCurl();
    }

    public function testFromHttpClientThrowsInvalidUrlForBadHost(): void
    {
        $this->expectException(InvalidUrlException::class);

        $this->utility('https://!!!invalid')->fromHttpClient();
    }

    public function testFromHttpClientThrowsNetworkErrorForUnreachableHost(): void
    {
        $this->expectException(NetworkErrorException::class);

        $this->utility('https://localhost:1')->fromHttpClient();
    }
}
