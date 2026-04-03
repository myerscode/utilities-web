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

## isAlive(): bool

Check if the host is alive.

```php
$utility->isAlive(); // true
```

## latency(): ?float

Get the latency in milliseconds, or null if unreachable.

```php
$utility->latency(); // 12.0
```

## setTimeout(int $seconds): self

Set the ping timeout in seconds.

```php
$utility->setTimeout(5)->ping();
```

## setTtl(int $ttl): self

Set the TTL (time to live).

```php
$utility->setTtl(128)->ping();
```

## url(): string

Get the URL the utility is using.

```php
$url = $utility->url();
```
