<?php

namespace Myerscode\Utilities\Web;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

class ClientUtility
{
    /**
     * Get a http client for utilities to use
     */
    public static function client(): HttpClientInterface
    {
        return HttpClient::create();
    }
}
