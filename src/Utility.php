<?php

namespace Myerscode\Utilities\Web;

use Zend\Http\Client;

class Utility
{

    const METHOD_CURL = 1;

    const METHOD_HEADERS = 2;

    const METHOD_HTTP = 3;

    const METHOD_SYSTEM = 4;

    /**
     * @var string
     */
    private $url;

    /**
     * Collection of request options to be passed to guzzle
     *
     * @var array
     */
    static $requestOptions = [
        'timeout' => 60,
    ];

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get a http client for utilities to use
     * @param string $url
     * @param array $requestOptions
     * @return Client
     */
    static public function client(string $url, array $requestOptions = [])
    {
        return new Client($url, array_merge([], self::$requestOptions, $requestOptions));
    }

    /**
     * Get the Ping utility
     *
     * @return PingUtility
     */
    public function ping(): PingUtility
    {
        return (new PingUtility($this->url));
    }

    /**
     * Get the URL utility
     *
     * @return UriUtility
     */
    public function url(): UriUtility
    {
        return (new UriUtility($this->url));
    }

    /**
     * Get the content utility
     *
     * @return ContentUtility
     */
    public function content(): ContentUtility
    {
        return (new ContentUtility($this->url));
    }
}
