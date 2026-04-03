<?php

declare(strict_types=1);

namespace Tests\ContentUtility;

use Mockery;
use Myerscode\Utilities\Web\ContentUtility;
use Myerscode\Utilities\Web\Resource\Response;
use Tests\BaseContentSuite;

final class FeaturesTest extends BaseContentSuite
{
    public function testContentTypeReturnsHeader(): void
    {
        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $legacyMock->shouldReceive('response')->once()->andReturn(
            new Response(200, '', ['content-type' => ['text/html; charset=utf-8']]),
        );

        $this->assertSame('text/html; charset=utf-8', $legacyMock->contentType());
    }

    public function testContentTypeReturnsNullWhenMissing(): void
    {
        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $legacyMock->shouldReceive('response')->once()->andReturn(
            new Response(200, ''),
        );

        $this->assertNull($legacyMock->contentType());
    }

    public function testHeadersReturnsResponseHeaders(): void
    {
        $headers = ['content-type' => ['text/html'], 'x-custom' => ['value']];

        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $legacyMock->shouldReceive('response')->once()->andReturn(
            new Response(200, '', $headers),
        );

        $this->assertSame($headers, $legacyMock->headers());
    }
    public function testJsonDecodesResponseContent(): void
    {
        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $legacyMock->shouldReceive('response')->once()->andReturn(
            new Response(200, '{"foo":"bar"}', ['content-type' => ['application/json']]),
        );

        $this->assertSame(['foo' => 'bar'], $legacyMock->json());
    }

    public function testStatusCodeReturnsCode(): void
    {
        $legacyMock = Mockery::mock(ContentUtility::class, ['https://localhost'])->makePartial();
        $legacyMock->shouldReceive('response')->once()->andReturn(
            new Response(201, ''),
        );

        $this->assertSame(201, $legacyMock->statusCode());
    }

    public function testWithHeadersReturnsSelf(): void
    {
        $utility = $this->utility('https://example.com');
        $result = $utility->withHeaders(['Authorization' => 'Bearer token']);

        $this->assertSame($utility, $result);
    }

    public function testWithTimeoutReturnsSelf(): void
    {
        $utility = $this->utility('https://example.com');
        $result = $utility->withTimeout(60);

        $this->assertSame($utility, $result);
    }
}
