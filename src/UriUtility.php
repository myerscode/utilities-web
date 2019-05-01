<?php

namespace Myerscode\Utilities\Web;

use Myerscode\Utilities\Web\Exceptions\CurlInitException;
use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Myerscode\Utilities\Web\Resource\Response;

class UriUtility
{
    /**
     * The url ping
     *
     * @var UrlUtility $url
     */
    private $url;

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
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = new UrlUtility($url);
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
     */
    public function response(string $method = WebUtilities::METHOD_CURL): Response
    {
        switch ($method) {
            case WebUtilities::METHOD_CURL:
                $response = $this->checkWithCurl();
                break;

            case WebUtilities::METHOD_HEADERS:
                $response = $this->checkWithHeaders();
                break;

            case WebUtilities::METHOD_HTTP:
                $response = $this->checkWithHeaders();
                break;

            default:
                throw new UnsupportedCheckMethodException();
        }

        return $response;
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

        $uri = $this->url->value();

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
        if (empty($this->url->value())) {
            throw new EmptyUrlException();
        }
        if (filter_var($this->url->value(), FILTER_VALIDATE_URL) === false) {
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

        $uri = $this->url->value();

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
     * Check the url exists using a http client
     *
     * @return Response
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    public function checkWithHttp()
    {
        $this->checkUrl();

        $uri = $this->url->value();

        $client = new ContentUtility($uri);

        return $client->response();
    }
}
