<?php

namespace Myerscode\Utilities\Web;

use League\Uri\Components\Query;
use League\Uri\Http;
use Myerscode\Utilities\Web\Exceptions\CurlInitException;
use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Myerscode\Utilities\Web\Resource\Response;

class UriUtility
{

    const DEFAULT_SCHEME = 'http://';

    /**
     * @var Http $uri
     */
    private $uri;

    /**
     * @var int
     */
    private $ttl = 255;

    /**
     * How long to wait before timing out requests
     *
     * @var int
     */
    private $timeout = 10;

    /**
     * Should follow redirects
     *
     * @var bool
     */
    private $followRedirects = false;

    /**
     * Utility constructor.
     *
     * @param string $uri
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
    private function setUrl($uri)
    {
        $trimmed = trim($uri);

        // check if a scheme is present, if not we need to give it one
        preg_match_all('/(https:\/\/)|(http:\/\/)/', $trimmed, $matches, PREG_SET_ORDER, 0);

        if (empty($matches)) {
            $trimmed = self::DEFAULT_SCHEME . $trimmed;
        }

        $this->uri = Http::createFromString($trimmed);

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
     *
     * @return string
     */
    public function value()
    {
        $scheme = (empty($scheme = $this->scheme())) ? 'http://' : $scheme . '://';

        $query = (empty($query = $this->query())) ? '' : '?' . $query;

        return $scheme . $this->host() . $this->path() . urldecode($query);
    }

    /**
     * Get the URLS scheme
     *
     * @return string
     */
    public function scheme()
    {
        return $this->uri->getScheme();
    }

    /**
     * Get query string of parameters from the URL
     *
     * @return string
     */
    public function query()
    {
        return $this->uri->getQuery();
    }

    /**
     * Retrieve the host component of the URL.
     *
     * @return string
     */
    public function host()
    {
        return $this->uri->getHost();
    }

    /**
     * Get the current URLS path
     *
     * @return string
     */
    public function path()
    {
        return $this->uri->getPath();
    }

    /**
     * Get the ttl.
     *
     * @return int The current ttl for Ping.
     */
    public function ttl()
    {
        return $this->ttl;
    }

    /**
     * Set the ttl (in hops).
     *
     * @param int $ttl TTL in hops.
     *
     * @return $this
     */
    public function setTtl($ttl): UriUtility
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Set the timeout.
     *
     * @param int $timeout Time to wait in seconds.
     *
     * @return $this
     */
    public function setTimeout($timeout): UriUtility
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Set whether or the utility should follow redirects
     *
     * @param bool $followRedirects
     * @return $this
     */
    public function setFollowRedirects(bool $followRedirects): UriUtility
    {
        $this->followRedirects = $followRedirects;

        return $this;
    }

    /**
     * Check the response from the uri
     *
     * @param string $method
     * @return Response
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws UnsupportedCheckMethodException
     * @throws CurlInitException
     */
    public function check(string $method = Utility::METHOD_CURL): Response
    {
        switch ($method) {
            case Utility::METHOD_CURL:
                $response = $this->checkWithCurl();
                break;

            case Utility::METHOD_HEADERS:
                $response = $this->checkWithHeaders();
                break;

            case Utility::METHOD_HTTP:
                $response = $this->checkWithHttpClient();
                break;

            default:
                throw new UnsupportedCheckMethodException();
        }

        return $response;
    }

    /**
     * Get response from the uri
     *
     * @return Response
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    public function response(): Response
    {
        $this->checkUrl();

        $client = Utility::client($this->uri());

        $response = $client->send();

        $response->getStatusCode();

        return new Response(intval($response->getStatusCode()), $response->getBody());
    }

    /**
     * Check the url exists using curl
     *
     * @return Response
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws CurlInitException
     */
    public function checkWithCurl(): Response
    {
        $this->checkUrl();

        $uri = $this->value();

        $curl = @curl_init($uri);

        if ($curl === false) {
            throw new CurlInitException();
        }

        @curl_setopt($curl, CURLOPT_HEADER, true);
        @curl_setopt($curl, CURLOPT_NOBODY, true);
        @curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($this->followRedirects()) {
            @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            @curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        } else {
            @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        }

        @curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout());
        @curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout());

        @curl_exec($curl);

        if (@curl_errno($curl)) {
            // TODO handle curl errors
            $code = 404;
        } else {
            $code = @curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }

        @curl_close($curl);

        return new Response(intval($code));
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
     *
     * @return bool
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
    public function timeout()
    {
        return $this->timeout;
    }

    /**
     * Check the url exists using headers
     *
     * @return Response
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

            if (preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $header, $matches)) {
                $code = $matches[1];
            }
        }

        return new Response(intval($code));
    }

    /**
     * Get the urls query parameters as a string
     *
     * @param null $numeric_prefix
     * @param null $arg_separator
     * @param int $enc_type
     * @return string
     */
    public function getQueryString($numeric_prefix = null, $arg_separator = null, $enc_type = PHP_QUERY_RFC1738)
    {
        return http_build_query($this->getQueryParameters(), $numeric_prefix, $arg_separator, $enc_type);
    }

    /**
     * Get an array of query parameters from the URL
     *
     * @return array
     */
    public function getQueryParameters()
    {
        $parameters = [];

        parse_str(parse_url($this->query(), PHP_URL_QUERY), $parameters);

        return $parameters;
    }

    /**
     * Check if the URL is set to url HTTPS
     *
     * @return bool
     */
    public function isHttps()
    {
        return 'https' === strtolower($this->scheme());
    }

    /**
     * Set the urls query parameters
     *
     * @param $query
     * @return $this
     */
    public function setQuery($query)
    {
        if (is_array($query)) {
            $queryString = Query::createFromPairs($query);
        } else {
            $queryString = new Query(rtrim($query, '?'));
        }

        $this->setUrl($this->uri->withQuery($queryString));

        return $this;
    }

    /**
     * Check the url exists using a http client
     *
     * @return Response
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
