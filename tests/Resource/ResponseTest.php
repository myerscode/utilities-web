<?php

declare(strict_types=1);

namespace Tests\Resource;

use Myerscode\Utilities\Web\Resource\Dom;
use Myerscode\Utilities\Web\Resource\Response;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testCodeReturnsStatusCode(): void
    {
        $response = new Response(200);

        $this->assertSame(200, $response->code());
    }

    public function testContentDefaultsToEmptyString(): void
    {
        $response = new Response(404);

        $this->assertSame('', $response->content());
    }

    public function testContentReturnsBody(): void
    {
        $response = new Response(200, '<html></html>');

        $this->assertSame('<html></html>', $response->content());
    }

    public function testDomReturnsDomInstance(): void
    {
        $response = new Response(200, '<html><body><h1>Test</h1></body></html>');

        $dom = $response->dom();

        $this->assertInstanceOf(Dom::class, $dom);
        $this->assertSame('Test', $dom->filterXPath('//h1')->text());
    }

    public function testHeadersDefaultsToEmptyArray(): void
    {
        $response = new Response(200);

        $this->assertSame([], $response->headers());
    }

    public function testHeadersReturnsHeaders(): void
    {
        $headers = ['content-type' => ['text/html']];
        $response = new Response(200, '', $headers);

        $this->assertSame($headers, $response->headers());
    }
}
