# Ping Utility
The content utility will allow you to ping a given URL.

```php
$utility = new PingUtility('https://google.com');
```

## ping() : array
Ping the URL and return meta of if it is alive and the latency of the ping.

```php
$utility->ping();
```
