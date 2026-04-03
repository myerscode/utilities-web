<?php

declare(strict_types=1);

namespace Tests\Exceptions;

use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\FiveHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\FourHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\MaxRedirectsReachedException;
use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Myerscode\Utilities\Web\Exceptions\WebUtilityException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Iterator;
use RuntimeException;

final class WebUtilityExceptionTest extends TestCase
{
    public static function exceptionProvider(): Iterator
    {
        yield 'ContentNotFoundException' => [new ContentNotFoundException()];
        yield 'EmptyUrlException' => [new EmptyUrlException()];
        yield 'FiveHundredResponseException' => [new FiveHundredResponseException()];
        yield 'FourHundredResponseException' => [new FourHundredResponseException()];
        yield 'InvalidUrlException' => [new InvalidUrlException()];
        yield 'MaxRedirectsReachedException' => [new MaxRedirectsReachedException()];
        yield 'NetworkErrorException' => [new NetworkErrorException()];
        yield 'UnsupportedCheckMethodException' => [new UnsupportedCheckMethodException()];
    }

    #[DataProvider('exceptionProvider')]
    public function testAllExceptionsExtendWebUtilityException(WebUtilityException $exception): void
    {
        $this->assertInstanceOf(WebUtilityException::class, $exception);
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testCanCatchAllPackageExceptions(): void
    {
        $this->expectException(WebUtilityException::class);

        throw new ContentNotFoundException('test');
    }
}
