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

// Get content from a URL
$content = $web->content()->content();

// Get a DOM crawler for the page
$dom = $web->content()->dom();

// Ping a host
$result = $web->ping()->ping();

// Work with URLs
$uri = $web->url();
```

## Available Utilities

### [Content Utility](docs/content-utility.md)
Fetch and interact with web page content.

### [Ping Utility](docs/ping-utility.md)
Ping hosts and check latency.

### [URI Utility](docs/uri-utility.md)
Parse, build and manipulate URLs.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
