<?php

namespace Myerscode\Utilities\Web;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Utility
{
    public const METHOD_CURL = 1;

    public const METHOD_HEADERS = 2;

    public const METHOD_HTTP = 3;

    public const METHOD_SYSTEM = 4;

    /**
     * Collection of request options to be passed to guzzle
     *
     * @var array
     */
    protected static $requestOptions = [
        'timeout' => 60,
    ];

    public function __construct(private readonly string $url)
    {
    }

    /**
     * Get a http client for utilities to use
     */
    public static function client(): HttpClientInterface
    {
        return HttpClient::create();
//        return $client->request($url, array_merge([], self::$requestOptions, $requestOptions));
    }

    /**
     * Get the Ping utility
     */
    public function ping(): PingUtility
    {
        return (new PingUtility($this->url));
    }

    /**
     * Get the URL utility
     */
    public function url(): UriUtility
    {
        return (new UriUtility($this->url));
    }

    /**
     * Get the content utility
     */
    public function content(): ContentUtility
    {
        return (new ContentUtility($this->url));
    }
}
