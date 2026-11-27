# URI Utility

Parse, build and manipulate URLs.

```php
use Myerscode\Utilities\Web\UriUtility;

$utility = new UriUtility('https://example.com/path?foo=bar#section');
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

## fragment(): string

Get the URL fragment (the part after #).

```php
$utility->fragment(); // 'section'
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

## removeQueryParameter(string $key): self

Remove a specific query parameter by key.

```php
$utility->removeQueryParameter('foo');
```

## hasQueryParameter(string $key): bool

Check if a query parameter exists.

```php
$utility->hasQueryParameter('foo'); // true
```

## withScheme(string $scheme): self

Set or replace the URL scheme.

```php
$utility->withScheme('http');
```

## withHost(string $host): self

Set or replace the URL host.

```php
$utility->withHost('other.com');
```

## withPath(string $path): self

Set or replace the URL path.

```php
$utility->withPath('/new/path');
```

## withPort(?int $port): self

Set or replace the URL port. Pass `null` to remove.

```php
$utility->withPort(8080);
```

## withFragment(string $fragment): self

Set or replace the URL fragment.

```php
$utility->withFragment('top');
```

## userInfo(): ?string

Get the user info component of the URI, or null if not present.

```php
$utility->userInfo(); // null
```

## isValid(): bool

Check if the URL is valid without making a request.

```php
$utility->isValid(); // true
```

## toArray(): array

Get all parsed URL components as an associative array.

```php
$utility->toArray();
// ['scheme' => 'https', 'host' => 'example.com', 'port' => 443, 'path' => '/path', 'query' => 'foo=bar', 'fragment' => 'section']
```

## equals(UriUtility $other): bool

Compare two URIs for equality.

```php
$a = new UriUtility('https://example.com');
$b = new UriUtility('https://example.com');
$a->equals($b); // true
```

## check(ResponseFrom $method): Response

Check the URL response using curl, headers, or HTTP client. Throws `NetworkErrorException` when no response is received and `BlockedHostException` for blocked hosts. See [Response Utility](response-utility.md).

```php
use Myerscode\Utilities\Web\Data\ResponseFrom;

$response = $utility->check(ResponseFrom::CURL);
$response->code(); // 200
```

## allowPrivateNetworks(bool $allow = true): self

`check()` blocks loopback, private, link-local, CGNAT and reserved addresses by default (see the [Security](../README.md#security) section). Call this before `check()` to allow them.

```php
$utility->allowPrivateNetworks()->check(); // http://localhost works
```

## value(): string

Get the full URL string. Only `%5B` and `%5D` are unescaped to `[` and `]`; other percent-escapes (`%2f`, `%26`, `%0d%0a`, `%00`) are left as they are.

```php
$utility->value(); // 'https://example.com/path?foo=bar#section'
```
