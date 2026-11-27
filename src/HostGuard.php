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

        $numeric = self::numericIpv4($host);

        if ($numeric !== null) {
            $ips = [$numeric];
        } elseif (filter_var($host, FILTER_VALIDATE_IP)) {
            $ips = [$host];
        } else {
            $ips = self::resolve($host);
        }

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
     * Parse the IPv4 shorthands curl and inet_aton accept (2130706433, 0x7f.1, 0177.0.0.1, 127.1)
     * so the guard never depends on the OS resolver understanding them
     */
    private static function numericIpv4(string $host): ?string
    {
        if (!preg_match('/^(0x[0-9a-f]*|[0-9]+)(\.(0x[0-9a-f]*|[0-9]+)){0,3}$/i', $host)) {
            return null;
        }

        $parts = [];

        foreach (explode('.', $host) as $part) {
            $value = match (true) {
                str_starts_with(strtolower($part), '0x') => hexdec(substr($part, 2) ?: '0'),
                strlen($part) > 1 && $part[0] === '0' => ctype_digit($part) && strspn($part, '01234567') === strlen($part) ? octdec($part) : null,
                default => (int) $part,
            };

            if (!is_int($value)) {
                return null;
            }

            $parts[] = $value;
        }

        // the last part fills the remaining bytes, like inet_aton
        $last = array_pop($parts);
        $limit = 256 ** (4 - count($parts));

        foreach ($parts as $part) {
            if ($part > 255) {
                return null;
            }
        }

        if ($last >= $limit) {
            return null;
        }

        $number = $last;

        foreach (array_reverse($parts) as $index => $part) {
            $number += $part * 256 ** (3 - (count($parts) - 1 - $index));
        }

        return long2ip((int) $number);
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
