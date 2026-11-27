<?php

namespace Myerscode\Utilities\Web;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientUtility
{
    /**
     * Get a http client for utilities to use.
     *
     * Unless private networks are allowed, redirects and DNS results are checked on every hop.
     */
    public static function client(bool $allowPrivateNetworks = false): HttpClientInterface
    {
        $client = HttpClient::create();

        return $allowPrivateNetworks ? $client : new NoPrivateNetworkHttpClient($client);
    }
}
