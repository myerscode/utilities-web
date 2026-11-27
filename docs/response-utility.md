# Response Utility

Check the status of a URL. Created with `$web->response()` or `new ResponseUtility($url)`.

```php
use Myerscode\Utilities\Web\ResponseUtility;

$utility = new ResponseUtility('https://example.com');
```

## check(string|ResponseFrom $method): Response

Check the URL using curl, stream headers or the Symfony HTTP client. `fromCurl()`, `fromHeaders()` and `fromHttpClient()` do the same directly. The real status code is returned, including 3xx, 4xx and 5xx.

```php
use Myerscode\Utilities\Web\Data\ResponseFrom;

$utility->check(ResponseFrom::HTTP)->code(); // 200
```

If no response is received (connection refused, DNS failure, timeout) a `NetworkErrorException` is thrown instead of a status code.

## setFollowRedirects(bool $follow): self

Follow redirects (default `false`). When `false`, the status of the first response is returned, for example 302. This applies to all three methods.

## setMaxRedirects(int $max): self

Maximum redirects to follow (default 10). A `MaxRedirectsReachedException` is thrown when it is exceeded. A redirect to a non-http(s) URL throws `InvalidUrlException`.

## setTimeout(int $seconds): self

Timeout in seconds (default 10).

## allowPrivateNetworks(bool $allow = true): self

Loopback, private, link-local, CGNAT and reserved addresses are blocked by default, and every redirect hop is checked. Blocked requests throw `BlockedHostException`. With curl and stream headers the vetted address is pinned to the connection (keeping the original Host header and TLS name) to prevent DNS rebinding. Call this to allow internal targets such as `localhost`.

```php
$utility->allowPrivateNetworks()->check(ResponseFrom::CURL);
```

## head(): Response, isAlive(): bool

`head()` sends a HEAD request using curl. `isAlive()` returns `true` for a 2xx response.

## value(): string

The URL in use. Only `%5B` and `%5D` are unescaped; other percent-escapes are left as they are.
