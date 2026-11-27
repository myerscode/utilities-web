<?php

namespace Myerscode\Utilities\Web;

use Myerscode\Utilities\Web\Exceptions\BlockedHostException;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * SSRF guard: only http(s) URLs whose host resolves to public addresses are allowed.
 */
final class HostGuard
{
    /**
     * Ranges not covered by FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
     */
    private const EXTRA_BLOCKED = [
        '100.64.0.0/10', // CGNAT
        '192.0.0.0/24', // IETF protocol assignments
        '198.18.0.0/15', // benchmarking
        '64:ff9b::/96', // NAT64, embeds an IPv4 address
        '2002::/16', // 6to4, embeds an IPv4 address
    ];

    /**
     * Check the scheme and every address the host resolves to, and return those addresses.
     *
     * An unresolvable host returns an empty list so the caller can report the network failure.
     *
     * @return list<string>
     *
     * @throws BlockedHostException
     */
    public static function assertSafe(string $url): array
    {
        $parts = parse_url($url);

        if (!is_array($parts) || !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || empty($parts['host'])) {
            throw new BlockedHostException('Only http and https URLs with a host are allowed');
        }

        $host = trim($parts['host'], '[]');

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : self::resolve($host);

        foreach ($ips as $ip) {
            if (!self::isPublic($ip)) {
                throw new BlockedHostException(sprintf('Requests to %s (%s) are not allowed', $host, $ip));
            }
        }

        return $ips;
    }

    public static function isPublic(string $ip): bool
    {
        // unwrap IPv4-mapped IPv6 in any notation (::ffff:127.0.0.1, ::ffff:7f00:1)
        $packed = @inet_pton($ip);

        if ($packed !== false && strlen($packed) === 16 && str_starts_with($packed, str_repeat("\0", 10) . "\xff\xff")) {
            $ip = inet_ntop(substr($packed, 12)) ?: $ip;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }

        return !IpUtils::checkIp($ip, self::EXTRA_BLOCKED);
    }

    /**
     * @return list<string>
     */
    private static function resolve(string $host): array
    {
        $ips = gethostbynamel($host) ?: [];

        foreach (@dns_get_record($host, DNS_AAAA) ?: [] as $record) {
            $ips[] = $record['ipv6'];
        }

        return $ips;
    }
}
