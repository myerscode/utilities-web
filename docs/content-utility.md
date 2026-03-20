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

## response(): Response

Get the full Response object containing status code, content and headers.

```php
$response = $utility->response();
$code = $response->code();
$content = $response->content();
$headers = $response->headers();
```

## url(): string

Get the URL the utility is using.

```php
$url = $utility->url();
```
