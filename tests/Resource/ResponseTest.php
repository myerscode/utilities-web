<?php

declare(strict_types=1);

namespace Tests\Resource;

use JsonException;
use Myerscode\Utilities\Web\Resource\Dom;
use Myerscode\Utilities\Web\Resource\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Iterator;

final class ResponseTest extends TestCase
{
    public static function statusCodeProvider(): Iterator
    {
        yield '200 is successful' => [200, true, false, false, false];
        yield '201 is successful' => [201, true, false, false, false];
        yield '299 is successful' => [299, true, false, false, false];
        yield '301 is redirect' => [301, false, true, false, false];
        yield '302 is redirect' => [302, false, true, false, false];
        yield '400 is client error' => [400, false, false, true, false];
        yield '404 is client error' => [404, false, false, true, false];
        yield '429 is client error' => [429, false, false, true, false];
        yield '500 is server error' => [500, false, false, false, true];
        yield '503 is server error' => [503, false, false, false, true];
    }
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

    public function testHeaderIsCaseInsensitive(): void
    {
        $response = new Response(200, '', ['Content-Type' => ['text/html']]);

        $this->assertSame('text/html', $response->header('content-type'));
    }

    public function testHeaderReturnsNullWhenMissing(): void
    {
        $response = new Response(200);

        $this->assertNull($response->header('x-missing'));
    }

    public function testHeaderReturnsStringValueDirectly(): void
    {
        $response = new Response(200, '', ['x-custom' => 'value']);

        $this->assertSame('value', $response->header('x-custom'));
    }

    public function testHeaderReturnsValueByName(): void
    {
        $response = new Response(200, '', ['content-type' => ['application/json']]);

        $this->assertSame('application/json', $response->header('content-type'));
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

    public function testIsJsonReturnsFalseForHtmlContentType(): void
    {
        $response = new Response(200, '<html></html>', ['content-type' => ['text/html']]);

        $this->assertFalse($response->isJson());
    }

    public function testIsJsonReturnsFalseWhenNoContentType(): void
    {
        $response = new Response(200);

        $this->assertFalse($response->isJson());
    }

    public function testIsJsonReturnsTrueForJsonContentType(): void
    {
        $response = new Response(200, '{}', ['content-type' => ['application/json']]);

        $this->assertTrue($response->isJson());
    }

    public function testJsonDecodesContent(): void
    {
        $response = new Response(200, '{"foo":"bar","baz":123}');

        $this->assertSame(['foo' => 'bar', 'baz' => 123], $response->json());
    }

    public function testJsonThrowsExceptionForInvalidJson(): void
    {
        $this->expectException(JsonException::class);

        $response = new Response(200, 'not json');
        $response->json();
    }

    #[DataProvider('statusCodeProvider')]
    public function testStatusHelpers(int $code, bool $successful, bool $redirect, bool $clientError, bool $serverError): void
    {
        $response = new Response($code);

        $this->assertSame($successful, $response->isSuccessful());
        $this->assertSame($redirect, $response->isRedirect());
        $this->assertSame($clientError, $response->isClientError());
        $this->assertSame($serverError, $response->isServerError());
    }

    public function testToArrayReturnsAllComponents(): void
    {
        $headers = ['content-type' => ['text/html']];
        $response = new Response(200, '<html></html>', $headers);

        $this->assertSame([
            'code' => 200,
            'content' => '<html></html>',
            'headers' => $headers,
        ], $response->toArray());
    }
}
