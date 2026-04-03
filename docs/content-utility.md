# Content Utility

Fetch and interact with a URL's content.

```php
use Myerscode\Utilities\Web\ContentUtility;

$utility = new ContentUtility('https://example.com');
```

## content(): string

Get the string content of the URL's response.

```php
$html = $utility->content();
```

Throws `ContentNotFoundException` if the response is 404.

## dom(): Dom

Get a Symfony DomCrawler instance of the content.

```php
$dom = $utility->dom();
$title = $dom->filterXPath('//title')->text();
```

## json(): array

Decode the response content as JSON.

```php
$data = $utility->json();
```

Throws `\JsonException` if the content is not valid JSON.

## contentType(): ?string

Get the Content-Type header from the response.

```php
$type = $utility->contentType(); // 'text/html; charset=utf-8'
```

## headers(): array

Get all response headers.

```php
$headers = $utility->headers();
```

## statusCode(): int

Get the HTTP status code.

```php
$code = $utility->statusCode(); // 200
```

## response(): Response

Get the full Response object containing status code, content and headers.

```php
$response = $utility->response();
$code = $response->code();
$content = $response->content();
$headers = $response->headers();
```

## post(array $data): Response

Send a POST request with form data.

```php
$response = $utility->post(['name' => 'value']);
$code = $response->code();
```

## withHeaders(array $headers): self

Set custom request headers.

```php
$utility->withHeaders(['Authorization' => 'Bearer token']);
$content = $utility->content();
```

## withTimeout(int $seconds): self

Set the request timeout in seconds.

```php
$utility->withTimeout(60);
$content = $utility->content();
```

## url(): string

Get the URL the utility is using.

```php
$url = $utility->url();
```
