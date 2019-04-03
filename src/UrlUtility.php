<?php

namespace Myerscode\Utilities\Web;

use League\Uri\Components\Query;
use League\Uri\Http;

class UrlUtility
{
    /**
     * @var Http $uri
     */
    private $uri;

    public function __construct($uri)
    {
        $this->setUrl($uri);
    }

    /**
     * Retrieve the host component of the URL.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->uri->getHost();
    }

    /**
     * Get the current URLS path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->uri->getPath();
    }

    /**
     * Get query string of parameters from the URL
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->uri->getQuery();
    }

    /**
     * Get an array of query parameters from the URL
     *
     * @return array
     */
    public function getQueryParameters()
    {
        $parameters = [];

        parse_str(parse_url($this->getQuery(), PHP_URL_QUERY), $parameters);

        return $parameters;
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
     * Get the URLS scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->uri->getScheme();
    }

    /**
     * Check if the URL is set to url HTTPS
     *
     * @return bool
     */
    public function isHttps()
    {
        return 'https' === strtolower($this->getScheme());
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
     * Set the current URL
     *
     * @param $uri
     * @return $this
     */
    private function setUrl($uri)
    {
        $this->uri = Http::createFromString(trim($uri));

        return $this;
    }

    /**
     * Get the current URI
     *
     * @return string
     */
    public function value()
    {
        $scheme = (empty($scheme = $this->getScheme())) ? 'http://' : $scheme . '://';

        $query = (empty($query = $this->getQuery())) ? '' : '?' . $query;

        return $scheme . $this->getHost() . $this->getPath() . urldecode($query);
    }
}
