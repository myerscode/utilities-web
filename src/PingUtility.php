<?php

namespace Myerscode\Utilities\Web;

use RuntimeException;

class PingUtility
{
    /**
     * How long to wait in seconds before timing out requests
     */
    private int $timeout = 1;

    private int $ttl = 255;

    /**
     * The url ping
     */
    private readonly UriUtility $uriUtility;

    /**
     * Utility constructor.
     */
    public function __construct(string $url)
    {
        $this->uriUtility = new UriUtility($url);
    }

    /**
     * Check if the host is alive
     */
    public function isAlive(): bool
    {
        return $this->ping()['alive'];
    }

    /**
     * Get the latency in milliseconds, or null if unreachable
     */
    public function latency(): ?float
    {
        return $this->ping()['latency'];
    }

    /**
     * Ping a urls host and return whether it's alive and the latency
     */
    public function ping(): array
    {
        $ping = [
            'alive' => false,
            'latency' => null,
        ];

        $host = $this->uriUtility->host();
        $pingCmd = $this->getPingCommand($host);
        $escapedHost = escapeshellarg($host);

        $exec_string = match ($this->detectOs()) {
            'WIN' => sprintf('%s -n 1 -i %d -w %d %s', $pingCmd, $this->ttl, $this->timeout * 1000, $escapedHost),
            'DARWIN' => sprintf('%s -c 1 -t %d %s', $pingCmd, $this->ttl, $escapedHost),
            default => sprintf('%s -c 1 -t %d -w %d %s', $pingCmd, $this->ttl, $this->timeout, $escapedHost),
        };

        $output = [];

        exec($exec_string . ' 2>&1', $output);

        if ($output === []) {
            return $ping;
        }

        foreach ($output as $line) {
            if (preg_match('/time[=<]?\s?(?<time>\d+(?:\.\d+)?)\s?ms/', $line, $latencyMatches)) {
                $ping['alive'] = true;
                $ping['latency'] = round((float) $latencyMatches['time']);
                break;
            }
        }

        return $ping;
    }

    /**
     * Set the timeout in seconds
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set the TTL (time to live)
     */
    public function setTtl(int $ttl): self
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function url(): string
    {
        return $this->uriUtility->value();
    }

    /**
     * Detect the operating system family
     */
    private function detectOs(): string
    {
        $os = strtoupper(PHP_OS);

        if (str_starts_with($os, 'WIN')) {
            return 'WIN';
        }

        if ($os === 'DARWIN') {
            return 'DARWIN';
        }

        return 'LINUX';
    }

    /**
     * Determines the correct ping command and checks if it's available.
     *
     * @throws RuntimeException if ping is not found
     */
    private function getPingCommand(string $host): string
    {
        $isIPv6 = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        $os = $this->detectOs();

        $pingCmd = ($os !== 'LINUX' || !$isIPv6) ? 'ping' : 'ping6';

        $checkCmd = ($os === 'WIN') ? 'where ' : 'which ';

        exec($checkCmd . $pingCmd . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0 || $output === []) {
            throw new RuntimeException('Ping command not found on this system (' . PHP_OS . ').');
        }

        return $pingCmd;
    }
}
