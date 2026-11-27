# Ping Utility

Ping a host and check if it's alive.

```php
use Myerscode\Utilities\Web\PingUtility;

$utility = new PingUtility('https://example.com');
```

## allowPrivateNetworks(bool $allow = true): self

Pinging loopback, private, link-local and reserved addresses (such as `localhost`, `10.0.0.1` or `169.254.169.254`) throws a `BlockedHostException` by default. The host is resolved once, checked, and the checked address is pinged. Call this to allow private addresses, for example when pinging a machine on your own network.

```php
(new PingUtility('192.168.1.10'))->allowPrivateNetworks()->isAlive();
```

## ping(): array

Ping the host and return whether it's alive and the latency in milliseconds. A host that does not resolve is reported as not alive.

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
