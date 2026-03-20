<?php

namespace Myerscode\Utilities\Web;

use Exception;
use League\Uri\Components\Query;
use League\Uri\Http;
use Myerscode\Utilities\Web\Exceptions\CurlInitException;
use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Myerscode\Utilities\Web\Resource\Response;

class UriUtility
{
    public const DEFAULT_SCHEME = 'http://';

    private Http $http;

    /**
     * How long to wait before timing out requests
     */
    private int $timeout = 10;


    /**
     * Utility constructor.
     */
    public function __construct(string $uri)
    {
        $this->setUrl($uri);
    }

    /**
     * Add or override query parameters to the uri
     *
     * @throws Exception
     */
    public function addQueryParameter(string|array $params): self
    {
        if (is_array($params)) {
            $queryParams = Query::fromVariable($params);
        } else {
            $queryParams = Query::new($params);
        }

        $query = Query::new($this->http->getQuery());

        $newQuery = $query->append($queryParams);

        return $this->setQuery(ltrim($newQuery, '&'));
    }

    /**
     * Check the response from the uri
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws UnsupportedCheckMethodException
     * @throws CurlInitException
     */
    public function check(int $method = Utility::METHOD_CURL): Response
    {
        $responseUtility = new ResponseUtility($this->value());

        return $responseUtility->check(match ($method) {
            Utility::METHOD_CURL => Data\ResponseFrom::CURL,
            Utility::METHOD_HEADERS => Data\ResponseFrom::HEADERS,
            Utility::METHOD_HTTP => Data\ResponseFrom::HTTP,
            default => throw new UnsupportedCheckMethodException(),
        });
    }

    /**
     * Get an array of query parameters from the URL
     */
    public function getQueryParameters(): array
    {
        $parameters = [];
        $queryString = parse_url($this->query(), PHP_URL_QUERY);

        if (is_string($queryString)) {
            parse_str($queryString, $parameters);
        }

        return $parameters;
    }

    /**
     * Get the urls query parameters as a string
     */
    public function getQueryString(string $numeric_prefix = '', string $arg_separator = '&', int $enc_type = PHP_QUERY_RFC1738): string
    {
        return http_build_query($this->getQueryParameters(), $numeric_prefix, $arg_separator, $enc_type);
    }

    /**
     * Retrieve the host component of the URL.
     */
    public function host(): string
    {
        return $this->http->getHost();
    }

    /**
     * Check if the URL is set to url HTTPS
     */
    public function isHttps(): bool
    {
        return 'https' === strtolower($this->scheme());
    }


    /**
     * Add or override query parameters to the uri
     */
    public function mergeQuery(string|array $params): self
    {
        if (is_array($params)) {
            $queryParams = Query::fromVariable($params);
        } else {
            $queryParams = Query::new($params);
        }

        $query = Query::new($this->http->getQuery());

        $newQuery = $query->merge($queryParams);

        return $this->setQuery(ltrim($newQuery, '&'));
    }

    /**
     * Get the current URLS path
     */
    public function path(): string
    {
        return $this->http->getPath();
    }

    /**
     * Get the port of the URI
     */
    public function port(): int
    {
        $port = $this->http->getPort();

        if (is_null($port)) {
            return $this->isHttps() ? 443 : 80;
        }

        return $port;
    }

    /**
     * Get query string of parameters from the URL
     */
    public function query(): string
    {
        return $this->http->getQuery();
    }

    /**
     * Get the URLS scheme
     */
    public function scheme(): string
    {
        return $this->http->getScheme();
    }

    /**
     * Set the urls query parameters
     */
    public function setQuery(string|array $params): self
    {
        if (is_array($params)) {
            $queryParams = Query::fromVariable($params);
        } else {
            $queryParams = Query::new($params);
        }

        $this->setUrl($this->http->withQuery(ltrim($queryParams, '&')));

        return $this;
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
     * Get the url the utility is using.
     *
     * @return string The url
     */
    public function uri(): string
    {
        return $this->value();
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

    /**
     * Set the current URL
     */
    private function setUrl(string|Http $uri): void
    {
        $trimmed = trim($uri);

        // check if a scheme is present, if not we need to give it one
        preg_match_all('/(https:\/\/)|(http:\/\/)/', $trimmed, $matches, PREG_SET_ORDER, 0);

        if ($matches === []) {
            $trimmed = 'https://' . $trimmed;
        }

        $this->http = Http::new($trimmed);
    }
}
