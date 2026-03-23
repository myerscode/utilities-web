<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use League\Uri\Exceptions\SyntaxError;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\ResponseUtility;
use Tests\BaseResponseSuite;

final class ValidationTest extends BaseResponseSuite
{
    public function testConstructorThrowsInvalidUrlForUnderscoreHost(): void
    {
        $this->expectException(InvalidUrlException::class);

        new ResponseUtility('foo_bar');
    }
    public function testConstructorThrowsSyntaxErrorForMalformedUrl(): void
    {
        $this->expectException(SyntaxError::class);

        new ResponseUtility('https://not a valid url');
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

    public function testFromHttpClientThrowsInvalidUrlForUnreachableHost(): void
    {
        $this->expectException(InvalidUrlException::class);

        $this->utility('https://localhost:1')->fromHttpClient();
    }
}
