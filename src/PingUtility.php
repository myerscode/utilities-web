<?php

namespace Myerscode\Utilities\Web;

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

        $isIPv6 = filter_var($this->uri->host(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

        if (str_starts_with(strtoupper(PHP_OS), 'WIN')) {
            $exec_string = sprintf('ping -n 1 -i %d -w %d %s', $ttl, $timeout * 1000, $host);
        } elseif (strtoupper(PHP_OS) === 'DARWIN') {
            $exec_string = sprintf('ping -c 1 -t %d %s', $ttl, $host);
        } else {
            if ($isIPv6) {
                $exec_string = sprintf('ping6 -c 1 -t %d -w %d %s', $ttl, $timeout, $host);
            } else {
                $exec_string = sprintf('ping -c 1 -t %d -w %d %s', $ttl, $timeout, $host);
            }
        }

        $output = [];
        $returnCode = null;

        exec($exec_string . ' 2>&1', $output, $returnCode);

        if (empty($output)) {
            return $ping;
        }

        // Scan all lines to find the latency
        foreach ($output as $line) {
            if (preg_match("/time(?:=|<)(?<time>[0-9]+(?:\.[0-9]+)?)\s?ms/", $line, $matches)) {
                $ping['alive'] = true;
                $ping['latency'] = round((float) $matches['time']);
                break;
            }
        }

        return $ping;
    }
}
