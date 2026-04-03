<?php

namespace Myerscode\Utilities\Web;

use League\Uri\Components\Query;
use League\Uri\Http;
use Myerscode\Utilities\Web\Data\ResponseFrom;
use Myerscode\Utilities\Web\Resource\Response;

class UriUtility
{
    public const DEFAULT_SCHEME = 'http://';

    private Http $http;

    /**
     * How long to wait before timing out requests
     */
    private int $timeout = 10;


    public function __construct(string $uri)
    {
        $this->setUrl($uri);
    }

    /**
     * Add or override query parameters to the uri
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
     */
    public function check(ResponseFrom $responseFrom = ResponseFrom::CURL): Response
    {
        $responseUtility = new ResponseUtility($this->value());

        return $responseUtility->check($responseFrom);
    }

    /**
     * Get an array of query parameters from the URL
     */
    public function getQueryParameters(): array
    {
        $parameters = [];
        $query = $this->query();

        if ($query !== '') {
            parse_str($query, $parameters);
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

        if ($port === null) {
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
     * Get the url the utility is using.
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
     * Set the current URL
     */
    private function setUrl(string|Http $uri): void
    {
        $trimmed = trim($uri);

        // check if a scheme is present at the start, if not we need to give it one
        if (!preg_match('#^https?://#i', $trimmed)) {
            $trimmed = 'https://' . $trimmed;
        }

        $this->http = Http::new($trimmed);
    }
}
