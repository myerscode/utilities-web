<?php

namespace Myerscode\Utilities\Web;

use Curl\Curl;
use League\Uri\Contracts\UriException;
use League\Uri\Http;
use League\Uri\Uri;
use Myerscode\Utilities\Web\Data\ResponseFrom;
use Myerscode\Utilities\Web\Exceptions\BlockedHostException;
use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\MaxRedirectsReachedException;
use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Myerscode\Utilities\Web\Resource\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

class ResponseUtility
{
    public const DEFAULT_SCHEME = 'https://';

    protected int $maxRedirects = 10;

    /**
     * Allow requests to loopback, private, link-local and reserved addresses
     */
    private bool $allowPrivateNetworks = false;

    /**
     * Should follow redirects
     */
    private bool $followRedirects = false;

    private ?Http $http = null;

    /**
     * How long to wait before timing out requests
     */
    private int $timeout = 10;

    public function __construct(string|UriUtility $uri)
    {
        $this->setUrl($uri);
    }

    /**
     * Allow (or block again) requests to loopback, private, link-local and reserved addresses.
     *
     * Blocked by default to prevent server side request forgery.
     */
    public function allowPrivateNetworks(bool $allow = true): self
    {
        $this->allowPrivateNetworks = $allow;

        return $this;
    }

    /**
     * Check the response from the uri
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws UnsupportedCheckMethodException
     */
    public function check(string|ResponseFrom $method): Response
    {
        return match ($method) {
            ResponseFrom::CURL => $this->fromCurl(),
            ResponseFrom::HEADERS => $this->fromHeaders(),
            ResponseFrom::HTTP => $this->fromHttpClient(),
            default => throw new UnsupportedCheckMethodException(),
        };
    }

    /**
     * Should follow redirects
     */
    public function followRedirects(): bool
    {
        return $this->followRedirects;
    }

    /**
     * Check the url exists using curl
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     */
    public function fromCurl(): Response
    {
        $this->checkUrl();

        $url = $this->value();

        // redirects are followed here, not by curl, so every Location can be vetted
        for ($hop = 0; ; $hop++) {
            $ips = $this->allowPrivateNetworks ? [] : HostGuard::assertSafe($url);

            // an unvetted address could not be pinned, so curl must not resolve it itself
            if (!$this->allowPrivateNetworks && $ips === []) {
                throw new NetworkErrorException('Could not resolve ' . $url);
            }

            $curl = new Curl();
            $curl->setUrl($url);

            $curl->setOpts([
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            ]);

            $host = (string)parse_url($url, PHP_URL_HOST);

            // pin the vetted addresses so the connection cannot be redirected by a second DNS lookup
            if ($ips !== [] && !filter_var(trim($host, '[]'), FILTER_VALIDATE_IP)) {
                $port = parse_url($url, PHP_URL_PORT) ?? (strtolower((string)parse_url($url, PHP_URL_SCHEME)) === 'https' ? 443 : 80);
                $addresses = implode(',', array_map(static fn (string $ip): string => str_contains($ip, ':') ? '[' . $ip . ']' : $ip, $ips));
                $curl->setOpt(CURLOPT_RESOLVE, [sprintf('%s:%d:%s', $host, $port, $addresses)]);
            }

            $curl->setFollowLocation(false);
            $curl->setTimeout($this->timeout());
            $curl->setConnectTimeout($this->timeout());
            $curl->exec();

            if ($curl->curlError) {
                throw new NetworkErrorException((string)$curl->curlErrorMessage, (int)$curl->curlErrorCode);
            }

            $code = (int)$curl->getHttpStatusCode();
            $location = $curl->responseHeaders['Location'] ?? null;

            if (!$this->followRedirects() || !is_string($location) || !$this->isRedirect($code)) {
                return new Response($code);
            }

            $url = $this->nextUrl($url, $location, $hop);
        }
    }

    /**
     * Check the url exists using headers
     *
     * @throws InvalidUrlException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     */
    public function fromHeaders(): Response
    {
        $url = $this->value();

        // redirects are followed here, not by the stream wrapper, so every Location can be vetted
        for ($hop = 0; ; $hop++) {
            $options = [
                'http' => [
                    'timeout' => $this->timeout(),
                    'follow_location' => 0,
                ],
            ];
            $target = $url;

            if (!$this->allowPrivateNetworks) {
                [$target, $options] = $this->pinToVettedIp($url, $options);
            }

            $headers = @get_headers($target, false, stream_context_create($options));

            if (!$headers || !preg_match('#^HTTP/\S+\s+([1-9]\d\d)\s+#', (string)$headers[0], $matches)) {
                throw new NetworkErrorException('Could not get a response from ' . $url);
            }

            $code = (int)$matches[1];
            $location = null;

            foreach ($headers as $header) {
                if (preg_match('#^Location:\s*(\S+)#i', (string)$header, $matches)) {
                    $location = $matches[1];
                }
            }

            if (!$this->followRedirects() || $location === null || !$this->isRedirect($code)) {
                return new Response($code);
            }

            $url = $this->nextUrl($url, $location, $hop);
        }
    }

    /**
     * Check the url exists using a http client
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws NetworkErrorException
     */
    public function fromHttpClient(): Response
    {
        $this->checkUrl();

        if (!$this->allowPrivateNetworks) {
            HostGuard::assertSafe($this->value());
        }

        $client = ClientUtility::client($this->allowPrivateNetworks);

        try {
            $response = $client->request('GET', $this->value(), [
                'timeout' => $this->timeout(),
                'max_redirects' => $this->followRedirects() ? $this->maxRedirects : 0,
            ]);

            // getStatusCode() does not throw on 3xx, 4xx or 5xx
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            throw new NetworkErrorException($e->getMessage(), (int)$e->getCode(), $e);
        }

        return new Response($statusCode);
    }

    /**
     * Send a HEAD request using curl (lighter than GET for status checks)
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    public function head(): Response
    {
        return $this->fromCurl();
    }

    /**
     * Check if the URL responds with a 2xx status code
     */
    public function isAlive(ResponseFrom $method = ResponseFrom::CURL): bool
    {
        return $this->check($method)->isSuccessful();
    }

    /**
     * Set whether or the utility should follow redirects
     */
    public function setFollowRedirects(bool $followRedirects): self
    {
        $this->followRedirects = $followRedirects;

        return $this;
    }

    /**
     * Set the maximum number of redirects to follow
     */
    public function setMaxRedirects(int $maxRedirects): self
    {
        $this->maxRedirects = $maxRedirects;

        return $this;
    }

    /**
     * Set the timeout in seconds.
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout.
     */
    public function timeout(): int
    {
        return $this->timeout;
    }


    /**
     * Get the url the utility is using.
     */
    public function uri(): string
    {
        return $this->value();
    }

    /**
     * Get the current URL
     */
    public function value(): string
    {
        // only brackets are unescaped; decoding anything else would change the URL's meaning (%2f, %26, %0d%0a, %00)
        return str_ireplace(['%5B', '%5D'], ['[', ']'], (string)$this->http);
    }

    /**
     * Check the URL that will be used
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    protected function checkUrl(): void
    {
        if (in_array($this->value(), ['', '0'], true)) {
            throw new EmptyUrlException();
        }

        if (filter_var($this->value(), FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException();
        }
    }

    private function isRedirect(int $code): bool
    {
        return in_array($code, [301, 302, 303, 307, 308], true);
    }

    /**
     * Resolve the next hop of a redirect, enforcing the redirect limit and http(s) only
     *
     * @throws InvalidUrlException
     * @throws MaxRedirectsReachedException
     */
    private function nextUrl(string $current, string $location, int $hop): string
    {
        if ($hop >= $this->maxRedirects) {
            throw new MaxRedirectsReachedException();
        }

        try {
            $next = (string)Uri::new($current)->resolve($location);
        } catch (Throwable) {
            throw new InvalidUrlException();
        }

        if (!preg_match('#^https?://#i', $next)) {
            throw new InvalidUrlException();
        }

        return $next;
    }

    /**
     * Point a stream request at the vetted IP while keeping the original Host header and TLS name,
     * so a second DNS lookup by the stream wrapper cannot reach a different address
     *
     * @param  array<string, array<string, mixed>>  $options
     * @return array{0: string, 1: array<string, array<string, mixed>>}
     *
     * @throws BlockedHostException
     * @throws NetworkErrorException
     */
    private function pinToVettedIp(string $url, array $options): array
    {
        $ips = HostGuard::assertSafe($url);

        if ($ips === []) {
            throw new NetworkErrorException('Could not resolve ' . $url);
        }

        $http = Http::new($url);
        $host = (string) $http->getHost();
        $port = $http->getPort();
        $ip = str_contains($ips[0], ':') ? '[' . $ips[0] . ']' : $ips[0];

        $options['http']['header'] = 'Host: ' . $host . ($port !== null ? ':' . $port : '');
        $options['ssl'] = ['peer_name' => trim($host, '[]'), 'SNI_enabled' => true];

        return [(string) $http->withHost($ip), $options];
    }

    /**
     * Set the current URL
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    private function setUrl(string|UriUtility $uri): void
    {
        $trimmed = trim($uri instanceof UriUtility ? $uri->value() : $uri);

        if ($trimmed === '') {
            throw new EmptyUrlException();
        }

        // check if a scheme is present at the start, if not we need to give it one
        if (!preg_match('#^https?://#i', $trimmed)) {
            $trimmed = self::DEFAULT_SCHEME . $trimmed;
        }

        try {
            $this->http = Http::new($trimmed);
        } catch (UriException $exception) {
            throw new InvalidUrlException($exception->getMessage(), 0, $exception);
        }

        $this->checkUrl();
    }
}
