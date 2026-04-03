# Web Utilities
> A fluent interface for interacting with web sites, page content and URLs.

[![Latest Stable Version](https://poser.pugx.org/myerscode/utilities-web/v/stable)](https://packagist.org/packages/myerscode/utilities-web)
[![Total Downloads](https://poser.pugx.org/myerscode/utilities-web/downloads)](https://packagist.org/packages/myerscode/utilities-web)
[![PHP Version Require](http://poser.pugx.org/myerscode/utilities-web/require/php)](https://packagist.org/packages/myerscode/utilities-web)
[![License](https://poser.pugx.org/myerscode/utilities-web/license)](https://github.com/myerscode/utilities-web/blob/main/LICENSE)
[![Tests](https://github.com/myerscode/utilities-web/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/myerscode/utilities-web/actions/workflows/tests.yml)
[![codecov](https://codecov.io/gh/myerscode/utilities-web/graph/badge.svg)](https://codecov.io/gh/myerscode/utilities-web)

## Requirements

- PHP >= 8.5
- ext-curl
- ext-dom
- ext-filter

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

### [Ping Utility](docs/ping-utility.md)
Ping hosts and check latency.

### [URI Utility](docs/uri-utility.md)
Parse, build and manipulate URLs.

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

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
