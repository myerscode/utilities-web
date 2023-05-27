<?php

namespace Myerscode\Utilities\Web;

use Curl\Curl;
use Exception;
use League\Uri\Components\Query;
use League\Uri\Http;
use League\Uri\QueryString;
use Myerscode\Utilities\Web\Data\CheckWith;
use Myerscode\Utilities\Web\Exceptions\CurlInitException;
use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Myerscode\Utilities\Web\Resource\Response;
use Psr\Http\Message\UriInterface as Psr7UriInterface;

class UriUtility
{
    /**
     * @var string
     */
    final public const DEFAULT_SCHEME = 'http://';

    private ?Http $http = null;

    private int $ttl = 255;

    /**
     * How long to wait before timing out requests
     */
    private int $timeout = 10;

    /**
     * Should follow redirects
     */
    private bool $followRedirects = false;

    protected int $maxRedirects = 10;

    /**
     * ClientUtility constructor.
     */
    public function __construct(string $uri)
    {
        $this->setUrl($uri);
    }

    /**
     * Set the current URL
     *
     * @param $uri
     * @return $this
     */
    private function setUrl(string|Http|Psr7UriInterface $uri)
    {
        $trimmed = trim((string) $uri);

        // check if a scheme is present, if not we need to give it one
        preg_match_all('#(https:\/\/)|(http:\/\/)#', $trimmed, $matches, PREG_SET_ORDER, 0);

        if ($matches === []) {
            $trimmed = self::DEFAULT_SCHEME . $trimmed;
        }

        $this->http = Http::createFromString($trimmed);

        return $this;
    }

    /**
     * Get the url the utility is using.
     *
     * @return string The url
     */
    public function url(): string
    {
        return $this->value();
    }

    /**
     * Get the current URI
     */
    public function value(): string
    {
        return urldecode((string)$this->http);
    }

    /**
     * Get the URLS scheme
     *
     * @return string
     */
    public function scheme()
    {
        return $this->http->getScheme();
    }

    /**
     * Get query string of parameters from the URL
     *
     * @return string
     */
    public function query()
    {
        return $this->http->getQuery();
    }

    /**
     * Retrieve the host component of the URL.
     *
     * @return string
     */
    public function host()
    {
        return $this->http->getHost();
    }

    /**
     * Get the current URLS path
     *
     * @return string
     */
    public function path()
    {
        return $this->http->getPath();
    }

    /**
     * Get the port of the URI
     *
     * @return int|null
     */
    public function port(): int|null
    {
        return $this->http->getPort();
    }

    /**
     * Get the ttl.
     *
     * @return int The current ttl for Ping.
     */
    public function ttl(): int
    {
        return $this->ttl;
    }

    /**
     * Set the ttl (in hops).
     *
     * @param int $ttl TTL in hops.
     */
    public function setTtl(int $ttl): UriUtility
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Set the timeout.
     *
     * @param int $timeout Time to wait in seconds.
     */
    public function setTimeout(int $timeout): UriUtility
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set whether or the utility should follow redirects
     */
    public function setFollowRedirects(bool $followRedirects): UriUtility
    {
        $this->followRedirects = $followRedirects;

        return $this;
    }

    /**
     * Check the response from the uri
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws UnsupportedCheckMethodException
     * @throws CurlInitException
     */
    public function check(string|CheckWith $method): Response
    {
        return match ($method) {
            CheckWith::CURL => $this->checkWithCurl(),
            CheckWith::HEADERS => $this->checkWithHeaders(),
            CheckWith::HTTP => $this->checkWithHttpClient(),
            default => throw new UnsupportedCheckMethodException(),
        };
    }

    /**
     * Get response from the uri
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    public function response(): Response
    {
        $this->checkUrl();

        $client = ClientUtility::client($this->uri());

        $response = $client->send();

        return new Response($response->getStatusCode(), $response->getBody());
    }

    /**
     * Check the url exists using curl
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws CurlInitException
     */
    public function checkWithCurl(): Response
    {
        $this->checkUrl();
        $curl = new Curl();
        $curl->setUrl($this->value());

        $curl->setOpts([
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        if ($this->followRedirects()) {
            $curl->setFollowLocation();
            $curl->setMaximumRedirects($this->maxRedirects);
        } else {
            $curl->setFollowLocation(false);
        }

        $curl->setTimeout($this->timeout());
        $curl->setConnectTimeout($this->timeout());
        $curl->exec();

        return new Response($curl->getHttpStatusCode());
    }

    /**
     * Check the URL that will be used
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    protected function checkUrl()
    {
        if (empty($this->value())) {
            throw new EmptyUrlException();
        }

        if (filter_var($this->value(), FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException();
        }
    }

    /**
     * Should follow redirects
     */
    public function followRedirects(): bool
    {
        return $this->followRedirects;
    }

    /**
     * Get the timeout.
     *
     * @return int Current timeout for Ping.
     */
    public function timeout(): int
    {
        return $this->timeout;
    }

    /**
     * Check the url exists using headers
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    public function checkWithHeaders(): Response
    {
        $this->checkUrl();

        $uri = $this->value();

        $streamContextDefaults = stream_context_get_options(stream_context_get_default());

        stream_context_set_default(
            [
                'http' => [
                    'timeout' => $this->timeout()
                ]
            ]
        );

        $headers = @get_headers($uri);

        stream_context_set_default($streamContextDefaults);

        $code = 404;

        if ($headers && is_array($headers)) {
            if ($this->followRedirects()) {
                $headers = array_reverse($headers);
            }

            $header = $headers[0];

            if (preg_match('#^HTTP\/\S+\s+([1-9]\d\d)\s+.*#', (string) $header, $matches)) {
                $code = $matches[1];
            }
        }

        return new Response((int) $code);
    }

    /**
     * Get the urls query parameters as a string
     *
     * @param null $numeric_prefix
     * @param null $arg_separator
     * @param int $enc_type
     */
    public function getQueryString($numeric_prefix = null, $arg_separator = null, $enc_type = PHP_QUERY_RFC1738): string
    {
        return http_build_query($this->getQueryParameters(), $numeric_prefix, $arg_separator, $enc_type);
    }

    /**
     * Get an array of query parameters from the URL
     *
     * @return string[]|mixed[][]
     */
    public function getQueryParameters(): array
    {
        $parameters = [];

        parse_str(parse_url($this->query(), PHP_URL_QUERY), $parameters);

        return $parameters;
    }

    /**
     * Check if the URL is set to url HTTPS
     */
    public function isHttps(): bool
    {
        return 'https' === strtolower($this->scheme());
    }

    private function parseInputQuery(string|array $params): string
    {
        if (is_string($params)) {
            $queryString = Query::createFromPairs(QueryString::parse(ltrim(trim($params), '?')))->toString();
        } else {
            if (is_array($params)) {
                $queryString = Query::createFromParams($params)->toString();
            } else {
                throw new \Exception();
            }
        }

        return $queryString;
    }

    /**
     * Set the urls query parameters
     *
     * @param $query
     * @return $this
     */
    public function setQuery(string|array $query)
    {
        $queryString = $this->parseInputQuery($query);

        $this->setUrl($this->http->withQuery($queryString));

        return new self();
    }

    /**
     * Add or override query parameters to the uri
     *
     * @param $params
     * @return $this
     * @throws Exception
     */
    public function addQueryParameter(string|array $params): static
    {
        $queryString = $this->parseInputQuery($params);

        if (empty($queryString)) {
            return $this;
        }

        $currentQueryString = urldecode($this->http->getQuery());

        $currentQueryPairs = QueryString::parse($currentQueryString);

        $filteredPairs = array_filter($currentQueryPairs, fn ($v) => $v !== ['', null]);

        $currentQuery = Query::createFromPairs($filteredPairs);

        $newQueryString = $currentQuery->append($queryString)->toString();

        return new self($this->http->withQuery($newQueryString));
    }


    public function mergeQuery(string|array $params): static
    {
        $queryString = $this->parseInputQuery($params);

        if (empty($queryString)) {
            return $this;
        }

        $currentQueryString = urldecode($this->http->getQuery());

        $currentQueryPairs = QueryString::parse($currentQueryString);

        $filteredPairs = array_filter($currentQueryPairs, fn ($v) => $v !== ['', null]);

        $currentQuery = Query::createFromPairs($filteredPairs);

        $newQueryString = $currentQuery->merge($queryString)->toString();

        return new self($this->http->withQuery($newQueryString));
    }



    /**
     * Check the url exists using a http client
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    public function checkWithHttpClient(): Response
    {
        return $this->response();
    }

    /**
     * Get the url the utility is using.
     *
     * @return string The url
     */
    public function uri(): string
    {
        return $this->value();
    }
}
