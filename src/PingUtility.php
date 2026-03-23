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
     * Ping a urls host
     */
    public function ping(): array
    {
        $ping = [
            'alive' => false,
            'latency' => null,
        ];

        $ttl = $this->ttl;
        $timeout = $this->timeout;
        $host = escapeshellarg($this->uriUtility->host());

        $pingCmd = $this->getPingCommand($host);

        // Construct the ping command
        if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
            $exec_string = sprintf('%s -n 1 -i %d -w %d %s', $pingCmd, $ttl, $timeout * 1000, $host);
        } elseif (strtoupper(PHP_OS) === 'DARWIN') {
            $exec_string = sprintf('%s -c 1 -t %d %s', $pingCmd, $ttl, $host);
        } else {
            $exec_string = sprintf('%s -c 1 -t %d -w %d %s', $pingCmd, $ttl, $timeout, $host);
        }

        $output = [];

        exec($exec_string . ' 2>&1', $output);

        if ($output === []) {
            return $ping;
        }

        foreach ($output as $line) {
            if (preg_match('/time[=<]?\\s?(?<time>\\d+(?:\\.\\d+)?)\\s?ms/', $line, $latencyMatches)) {
                $ping['alive'] = true;
                $ping['latency'] = round((float)$latencyMatches['time']);
                break;
            }
        }

        return $ping;
    }

    public function url(): string
    {
        return $this->uriUtility->value();
    }

    /**
     * Determines the correct ping command and checks if it's available.
     *
     * @throws RuntimeException if ping is not found
     */
    private function getPingCommand(string $host): string
    {
        $isIPv6 = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

        if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
            $pingCmd = 'ping';
        } elseif (strtoupper(PHP_OS) === 'DARWIN') {
            $pingCmd = 'ping';
        } else {
            $pingCmd = $isIPv6 ? 'ping6' : 'ping';
        }

        $checkCmd = (str_starts_with(strtoupper(PHP_OS), 'WIN')) ? 'where ' : 'which ';

        exec($checkCmd . $pingCmd . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0 || $output === []) {
            throw new RuntimeException('Ping command not found on this system (' . PHP_OS . ').');
        }

        return $pingCmd;
    }
}
