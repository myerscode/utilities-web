# Web Utilities
> A fluent interface for interacting with web sites, page content and URLs.

[![Latest Stable Version](https://poser.pugx.org/myerscode/utilities-web/v/stable)](https://packagist.org/packages/myerscode/utilities-web)
[![Total Downloads](https://poser.pugx.org/myerscode/utilities-web/downloads)](https://packagist.org/packages/myerscode/utilities-web)
[![PHP Version Require](http://poser.pugx.org/myerscode/utilities-web/require/php)](https://packagist.org/packages/myerscode/utilities-web)
[![License](https://poser.pugx.org/myerscode/utilities-web/license)](https://github.com/myerscode/utilities-web/blob/main/LICENSE)
[![Tests](https://github.com/myerscode/utilities-web/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/myerscode/utilities-web/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/myerscode/utilities-web/graph/badge.svg)](https://codecov.io/gh/myerscode/utilities-web)

## Requirements

- PHP >= 8.5
- ext-curl
- ext-dom
- ext-filter
- symfony/http-foundation (installed by Composer)

## Install

```bash
composer require myerscode/utilities-web
```

## Usage

```php
use Myerscode\Utilities\Web\Utility;

$web = new Utility('https://example.com');
// or use the static factory
$web = Utility::make('https://example.com');

// Get content from a URL
$content = $web->content()->content();

// Get a DOM crawler for the page
$dom = $web->content()->dom();

// Decode JSON responses
$data = $web->content()->json();

// Ping a host
$result = $web->ping()->ping();
$alive = $web->ping()->isAlive();

// Quick liveness check
$web->isAlive();

// Work with URLs
$uri = $web->url();

// Check response status
$response = $web->response()->check(\Myerscode\Utilities\Web\Data\ResponseFrom::CURL);
$response->isSuccessful(); // true for 2xx
```

## Available Utilities

### [Content Utility](docs/content-utility.md)
Fetch and interact with web page content.

### [Response Utility](docs/response-utility.md)
Check the status of a URL.

### [Ping Utility](docs/ping-utility.md)
Ping hosts and check latency.

### [URI Utility](docs/uri-utility.md)
Parse, build and manipulate URLs.

## Security

Requests are protected against server side request forgery (SSRF) by default. Only `http` and `https` URLs are allowed, and requests to hosts that resolve to loopback, private (RFC 1918), link-local (including cloud metadata at `169.254.169.254`), CGNAT or otherwise reserved addresses, their IPv6 equivalents and IPv4-mapped IPv6 throw a `BlockedHostException`. Numeric shorthands such as `2130706433`, `0x7f.1` or `127.1` are decoded by the guard itself, so they are caught on every OS. Redirects are checked on every hop, and curl, stream header checks and ping connect to the address that was checked, so DNS rebinding cannot swap it. The ping utility applies the same guard.

To reach internal hosts, opt out explicitly:

```php
$web = Utility::make('http://localhost:8080')->allowPrivateNetworks();
// or per utility: $web->content()->allowPrivateNetworks(), $web->response()->allowPrivateNetworks(), $web->ping()->allowPrivateNetworks()
```

`UriUtility::value()` returns the URL as given. Only `%5B` and `%5D` are unescaped, so escapes such as `%2f`, `%26`, `%0d%0a` and `%00` keep their meaning.

Limit the size of downloaded content with `$web->content()->withMaxBytes(1_048_576)`.

## Exception Handling

All package exceptions extend `Myerscode\Utilities\Web\Exceptions\WebUtilityException`, which extends `RuntimeException`. This allows catching all package exceptions in one go:

```php
use Myerscode\Utilities\Web\Exceptions\WebUtilityException;

try {
    $content = $web->content()->content();
} catch (WebUtilityException $e) {
    // Handle any package exception
}
```

| Exception | Thrown when |
|---|---|
| `InvalidUrlException` | the URL is malformed, or a redirect leaves http(s) |
| `BlockedHostException` | (extends `InvalidUrlException`) the host is blocked by the SSRF guard |
| `EmptyUrlException` | the URL is empty |
| `NetworkErrorException` | no response was received (connection, DNS, timeout) |
| `MaxRedirectsReachedException` | the redirect limit is exceeded |
| `ResponseTooLargeException` | the body exceeds `withMaxBytes()` |
| `FourHundredResponseException` / `FiveHundredResponseException` | `ContentUtility` received a 4xx / 5xx |

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
