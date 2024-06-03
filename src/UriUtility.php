<?php

namespace Myerscode\Utilities\Web;

use League\Uri\Components\Query;
use League\Uri\Http;
use League\Uri\QueryString;
use League\Uri\UriString;
use Myerscode\Utilities\Web\Exceptions\InvalidQueryParamsException;
use Myerscode\Utilities\Web\Exceptions\InvalidSchemeException;
use Psr\Http\Message\UriInterface as Psr7UriInterface;

class UriUtility
{
    private ?Http $http = null;
    protected array $defaultFragments = [
            'scheme' => 'http',
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        ];

    /**
     * @throws InvalidSchemeException
     */
    public function __construct(string $uri)
    {
        $this->setUrl($uri);
    }

    /**
     * Set the current URL
     */
    private function setUrl(string|Http|Psr7UriInterface $uri): void
    {
        $trimmed = trim((string)$uri);

        $uriStringParts = UriString::parse($trimmed);

        if (!in_array($uriStringParts['scheme'], ['http', 'https', null])) {
            throw new InvalidSchemeException($uriStringParts['scheme'] . ' scheme given');
        }

        if (is_null($uriStringParts['scheme'])) {
            $trimmed = $this->defaultFragments['scheme'] . '://' . $trimmed;
        }

        $this->http = Http::createFromComponents(UriString::parse($trimmed));
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
    public function scheme(): string
    {
        return $this->http->getScheme();
    }

    /**
     * Get query string of parameters from the URL
     *
     * @return string
     */
    public function query(): string
    {
        return $this->http->getQuery();
    }

    /**
     * Retrieve the host component of the URL.
     *
     * @return string
     */
    public function host(): string
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
     * @return int
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
     * Get the urls query parameters as a string
     *
     * @param  null  $numeric_prefix
     * @param  null  $arg_separator
     * @param  int  $enc_type
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

    /**
     * Get the query string from a given input
     *
     * @throws InvalidQueryParamsException
     */
    private function parseInputQuery(string|array $params): string
    {
        if (is_string($params)) {
            $queryString = Query::createFromPairs(QueryString::parse(ltrim(trim($params), '?')))->toString();
        } else {
            if (is_array($params)) {
                $queryString = Query::createFromParams($params)->toString();
            } else {
                throw new InvalidQueryParamsException();
            }
        }

        return $queryString;
    }

    /**
     * Set the urls query parameters
     *
     * @param  string|array  $query
     *
     * @return $this
     * @throws InvalidQueryParamsException|InvalidSchemeException
     */
    public function setQuery(string|array $query)
    {
        $queryString = $this->parseInputQuery($query);

        return new self((string)$this->http->withQuery($queryString));
    }

    /**
     * Add or override query parameters to the uri
     *
     * @param  string|array  $params
     *
     * @return $this
     * @throws InvalidQueryParamsException|InvalidSchemeException
     */
    public function addQueryParameter(string|array $params): static
    {
        $queryString = $this->parseInputQuery($params);

        if (empty($queryString)) {
            return $this;
        }

        $currentQueryString = urldecode($this->http->getQuery());

        $currentQueryPairs = QueryString::parse($currentQueryString);

        $filteredPairs = array_filter($currentQueryPairs, fn($v) => $v !== ['', null]);

        $currentQuery = Query::createFromPairs($filteredPairs);

        $newQueryString = $currentQuery->append($queryString)->toString();

        return new self($this->http->withQuery($newQueryString));
    }

    /**
     * Add or override query parameters to the uri
     *
     * @param  string|array  $params
     *
     * @return $this
     * @throws InvalidQueryParamsException|InvalidSchemeException
     */
    public function mergeQuery(string|array $params): static
    {
        $queryString = $this->parseInputQuery($params);

        if (empty($queryString)) {
            return $this;
        }

        $currentQueryString = urldecode($this->http->getQuery());

        $currentQueryPairs = QueryString::parse($currentQueryString);

        $filteredPairs = array_filter($currentQueryPairs, fn($v) => $v !== ['', null]);

        $currentQuery = Query::createFromPairs($filteredPairs);

        $newQueryString = $currentQuery->merge($queryString)->toString();

        return new self($this->http->withQuery($newQueryString));
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
