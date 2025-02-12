<?php

namespace Myerscode\Utilities\Web;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

class ClientUtility
{
    /**
     * Collection of request options to be passed to guzzle
     */
    public static array $requestOptions = [
        'timeout' => 60,
    ];

    /**
     * Get a http client for utilities to use
     */
    public static function client(array $requestOptions = []): HttpClientInterface
    {
        return HttpClient::create();
    }
}
