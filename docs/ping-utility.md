# Ping Utility

Ping a host and check if it's alive.

```php
use Myerscode\Utilities\Web\PingUtility;

$utility = new PingUtility('https://example.com');
```

## ping(): array

Ping the host and return whether it's alive and the latency in milliseconds.

```php
$result = $utility->ping();
// ['alive' => true, 'latency' => 12.0]
```

## url(): string

Get the URL the utility is using.

```php
$url = $utility->url();
```
