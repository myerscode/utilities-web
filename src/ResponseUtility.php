<?php

namespace Myerscode\Utilities\Web;

use Curl\Curl;
use League\Uri\Http;
use Myerscode\Utilities\Web\Data\ResponseFrom;
use Myerscode\Utilities\Web\Exceptions\EmptyUrlException;
use Myerscode\Utilities\Web\Exceptions\InvalidUrlException;
use Myerscode\Utilities\Web\Exceptions\UnsupportedCheckMethodException;
use Myerscode\Utilities\Web\Resource\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ResponseUtility
{
    public const DEFAULT_SCHEME = 'https://';

    protected int $maxRedirects = 10;

    /**
     * Should follow redirects
     */
    private bool $followRedirects = false;

    private ?Http $http = null;

    /**
     * How long to wait before timing out requests
     */
    private int $timeout = 10;

    public function __construct(string|UriUtility $uri)
    {
        $this->setUrl($uri);
    }

    /**
     * Check the response from the uri
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws UnsupportedCheckMethodException
     */
    public function check(string|ResponseFrom $method): Response
    {
        return match ($method) {
            ResponseFrom::CURL => $this->fromCurl(),
            ResponseFrom::HEADERS => $this->fromHeaders(),
            ResponseFrom::HTTP => $this->fromHttpClient(),
            default => throw new UnsupportedCheckMethodException(),
        };
    }

    /**
     * Should follow redirects
     */
    public function followRedirects(): bool
    {
        return $this->followRedirects;
    }

    /**
     * Check the url exists using curl
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    public function fromCurl(): Response
    {
        $this->checkUrl();
        $curl = new Curl();
        $curl->setUrl($this->value());

        $curl->setOpts([
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        if ($this->followRedirects()) {
            $curl->setFollowLocation();
            $curl->setMaximumRedirects($this->maxRedirects);
        } else {
            $curl->setFollowLocation(false);
        }

        $curl->setTimeout($this->timeout());
        $curl->setConnectTimeout($this->timeout());
        $curl->exec();

        return new Response($curl->getHttpStatusCode());
    }

    /**
     * Check the url exists using headers
     */
    public function fromHeaders(): Response
    {
        $streamContextDefaults = stream_context_get_options(stream_context_get_default());

        stream_context_set_default(
            [
                'http' => [
                    'timeout' => $this->timeout(),
                ],
            ],
        );

        $headers = @get_headers($this->value());

        stream_context_set_default($streamContextDefaults);

        $code = 404;

        if ($headers) {
            if ($this->followRedirects()) {
                $headers = array_reverse($headers);
            }

            $header = $headers[0];

            if (preg_match('#^HTTP/\S+\s+([1-9]\d\d)\s+.*#', (string)$header, $matches)) {
                $code = $matches[1];
            }
        }

        return new Response((int)$code);
    }


    /**
     * Check the url exists using a http client
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     * @throws TransportExceptionInterface
     */
    public function fromHttpClient(): Response
    {
        $this->checkUrl();

        $client = ClientUtility::client();

        try {
            $response = $client->request('GET', $this->value());
            $statusCode = $response->getStatusCode();
        } catch (ClientExceptionInterface) {
            $statusCode = 400;
        } catch (ServerExceptionInterface) {
            $statusCode = 500;
        } catch (\Exception) {
            throw new InvalidUrlException();
        }

        return new Response($statusCode);
    }

    /**
     * Set whether or the utility should follow redirects
     */
    public function setFollowRedirects(bool $followRedirects): ResponseUtility
    {
        $this->followRedirects = $followRedirects;

        return $this;
    }

    /**
     * Set the timeout in seconds.
     */
    public function setTimeout(int $timeout): ResponseUtility
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the timeout.
     */
    public function timeout(): int
    {
        return $this->timeout;
    }


    /**
     * Get the url the utility is using.
     */
    public function uri(): string
    {
        return $this->value();
    }

    /**
     * Get the current URL
     */
    public function value(): string
    {
        return urldecode((string)$this->http);
    }

    /**
     * Check the URL that will be used
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    protected function checkUrl(): void
    {
        if (in_array($this->value(), ['', '0'], true)) {
            throw new EmptyUrlException();
        }

        if (filter_var($this->value(), FILTER_VALIDATE_URL) === false) {
            throw new InvalidUrlException();
        }
    }

    /**
     * Set the current URL
     *
     * @throws EmptyUrlException
     * @throws InvalidUrlException
     */
    private function setUrl(string|UriUtility $uri): void
    {
        $trimmed = trim($uri instanceof UriUtility ? $uri->value() : $uri);

        // check if a scheme is present, if not we need to give it one
        preg_match_all('#(https:\/\/)|(http:\/\/)#', $trimmed, $matches, PREG_SET_ORDER, 0);

        if ($matches === []) {
            $trimmed = self::DEFAULT_SCHEME . $trimmed;
        }

        $this->http = Http::new($trimmed);

        $this->checkUrl();
    }
}
