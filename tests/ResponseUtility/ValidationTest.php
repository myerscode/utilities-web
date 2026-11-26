<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use League\Uri\Exceptions\SyntaxError;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\ResponseUtility;
use Tests\BaseResponseSuite;

final class ValidationTest extends BaseResponseSuite
{
    public function testConstructorRejectsMalformedUrl(): void
    {
        // league/uri < 7.10 throws SyntaxError on spaces; 7.10+ percent-encodes the host and checkUrl() throws InvalidUrlException.
        try {
            new ResponseUtility('https://not a valid url');
            $this->fail('Expected a malformed URL to be rejected');
        } catch (SyntaxError|InvalidUrlException) {
            $this->addToAssertionCount(1);
        }
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

    public function testFromHttpClientThrowsInvalidUrlForUnreachableHost(): void
    {
        $this->expectException(InvalidUrlException::class);

        $this->utility('https://localhost:1')->fromHttpClient();
    }
}
