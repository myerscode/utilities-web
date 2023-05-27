<?php

namespace Myerscode\Utilities\Web;

use Laminas\Http\Client;

class ClientUtility
{
    /**
     * Collection of request options to be passed to guzzle
     *
     * @var array
     */
    public static array $requestOptions = [
        'timeout' => 60,
    ];

    /**
     * Get a http client for utilities to use
     */
    public static function client(string $url, array $requestOptions = []): Client
    {
        return new Client($url, array_merge([], self::$requestOptions, $requestOptions));
    }
}
