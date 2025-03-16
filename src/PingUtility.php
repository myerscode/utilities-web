<?php

namespace Myerscode\Utilities\Web;

use RuntimeException;

class PingUtility
{
    /**
     * The url ping
     *
     * @var UriUtility $uri
     */
    private UriUtility $uri;

    /**
     * @var int
     */
    private int $ttl = 255;

    /**
     * How long to wait in seconds before timing out requests
     *
     * @var int
     */
    private int $timeout = 1;

    /**
     * Utility constructor.
     */
    public function __construct(string $url)
    {
        $this->uri = new UriUtility($url);
    }

    public function url(): string
    {
        return $this->uri->value();
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

        $ttl = (int) $this->ttl;
        $timeout = (int) $this->timeout;
        $host = escapeshellarg($this->uri->host());

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
        $returnCode = null;

        exec($exec_string . ' 2>&1', $output, $returnCode);

        if (empty($output)) {
            return $ping;
        }

        foreach ($output as $line) {
            if (preg_match("/time[=<]?\s?(?<time>[0-9]+(?:\.[0-9]+)?)\s?ms/", $line, $latencyMatches)) {
                $ping['alive'] = true;
                $ping['latency'] = round((float) $latencyMatches['time']);
                break;
            }
        }

        return $ping;
    }

    /**
     * Determines the correct ping command and checks if it's available.
     *
     * @param string $host
     * @return string
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

        // Check if ping command exists
        if (!shell_exec(sprintf('command -v %s 2>/dev/null', escapeshellarg($pingCmd)))) {
            throw new RuntimeException("Ping command ($pingCmd) not found on this system.");
        }

        return $pingCmd;
    }
}
