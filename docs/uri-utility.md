# URI Utility

Parse, build and manipulate URLs.

```php
use Myerscode\Utilities\Web\UriUtility;

$utility = new UriUtility('https://example.com/path?foo=bar');
```

## host(): string

Get the host component.

```php
$utility->host(); // 'example.com'
```

## scheme(): string

Get the URL scheme.

```php
$utility->scheme(); // 'https'
```

## isHttps(): bool

Check if the URL uses HTTPS.

```php
$utility->isHttps(); // true
```

## port(): int

Get the port (defaults to 80 for HTTP, 443 for HTTPS).

```php
$utility->port(); // 443
```

## path(): string

Get the URL path.

```php
$utility->path(); // '/path'
```

## query(): string

Get the raw query string.

```php
$utility->query(); // 'foo=bar'
```

## getQueryParameters(): array

Get query parameters as an associative array.

```php
$utility->getQueryParameters(); // ['foo' => 'bar']
```

## addQueryParameter(string|array $params): self

Append query parameters (does not override existing keys).

```php
$utility->addQueryParameter(['baz' => 'qux']);
$utility->addQueryParameter('baz=qux');
```

## mergeQuery(string|array $params): self

Merge query parameters (overrides existing keys).

```php
$utility->mergeQuery(['foo' => 'updated']);
```

## setQuery(string|array $params): self

Replace the entire query string.

```php
$utility->setQuery(['new' => 'value']);
```

## check(ResponseFrom $method): Response

Check the URL response using curl, headers, or HTTP client.

```php
use Myerscode\Utilities\Web\Data\ResponseFrom;

$response = $utility->check(ResponseFrom::CURL);
$response->code(); // 200
```

## value(): string

Get the full URL string.

```php
$utility->value(); // 'https://example.com/path?foo=bar'
```
