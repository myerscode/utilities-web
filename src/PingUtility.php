<?php

namespace Myerscode\Utilities\Web;

class PingUtility
{
    /**
     * The url ping
     */
    private readonly UriUtility $uriUtility;

    private int $ttl = 255;

    /**
     * How long to wait in seconds before timing out requests
     */
    private int $timeout = 1;

    /**
     * ClientUtility constructor.
     */
    public function __construct(string $url)
    {
        $this->uriUtility = new UriUtility($url);
    }

    /**
     * Ping a urls host
     *
     * @return array{alive: false, latency: null}
     */
    public function ping(): array
    {
        $ping = [
            'alive' => false,
            'latency' => null,
        ];

        $ttl = escapeshellcmd($this->ttl);

        $timeout = escapeshellcmd($this->timeout);

        $host = escapeshellcmd($this->uriUtility->host());


        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Exec string for Windows-based systems.
            // -n = number of pings; -i = ttl; -w = timeout (in milliseconds).
            $exec_string = 'ping -n 1 -i ' . $ttl . ' -w ' . ($timeout * 1000) . ' ' . $host;
        } elseif (strtoupper(PHP_OS) === 'DARWIN') {
            // Exec string for Darwin based systems (OS X).
            // -n = numeric output; -c = number of pings; -m = ttl; -t = timeout.
            $exec_string = 'ping -n -c 1 -m ' . $ttl . ' -t ' . $timeout . ' ' . $host;
        } else {
            // Exec string for other UNIX-based systems (Linux).
            // -n = numeric output; -c = number of pings; -t = ttl; -W = timeout
            $exec_string = 'ping -n -c 1 -t ' . $ttl . ' -W ' . $timeout . ' ' . $host;
        }

        $output = [];

        $return = [];

        $return = exec($exec_string . ' 2>&1', $output, $return);

        $output = array_values(array_filter($output));

        if (!empty($output[1])) {
            $response = preg_match("#time(?:=|<)(?<time>[\.0-9]+)(?:|\s)ms#", $output[1], $matches);
            if ($response > 0 && isset($matches['time'])) {
                $ping['alive'] = true;
                $ping['latency'] = round($matches['time']);
            }
        }

        return $ping;
    }
}
