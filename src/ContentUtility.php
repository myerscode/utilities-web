<?php

namespace Myerscode\Utilities\Web;

use Myerscode\Utilities\Web\Exceptions\BlockedHostException;
use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\FiveHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\FourHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\MaxRedirectsReachedException;
use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Myerscode\Utilities\Web\Exceptions\ResponseTooLargeException;
use Myerscode\Utilities\Web\Resource\Dom;
use Myerscode\Utilities\Web\Resource\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use JsonException;

class ContentUtility
{
    /**
     * Custom request headers
     *
     * @var array<string, string>
     */
    private array $headers = [];

    /**
     * Request timeout in seconds
     */
    private int $timeout = 30;

    /**
     * Allow requests to loopback, private, link-local and reserved addresses
     */
    private bool $allowPrivateNetworks = false;

    /**
     * Maximum response body size in bytes, null for unlimited
     */
    private ?int $maxBytes = null;

    /**
     * The url to get content from
     */
    private readonly UriUtility $uriUtility;

    public function __construct(public readonly string $url)
    {
        $this->uriUtility = new UriUtility($url);
    }

    /**
     * Allow (or block again) requests to loopback, private, link-local and reserved addresses.
     *
     * Blocked by default to prevent server side request forgery.
     */
    public function allowPrivateNetworks(bool $allow = true): self
    {
        $this->allowPrivateNetworks = $allow;

        return $this;
    }

    /**
     * Get the content from the url
     *
     * @throws FiveHundredResponseException
     * @throws ContentNotFoundException
     */
    public function content(): string
    {
        $response = $this->response();

        if ($response->code() === 404) {
            throw new ContentNotFoundException();
        }

        return $response->content();
    }

    /**
     * Get the Content-Type header from the response
     *
     * @throws FourHundredResponseException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     * @throws FiveHundredResponseException
     */
    public function contentType(): ?string
    {
        return $this->response()->header('content-type');
    }

    /**
     * Get the content as a content dom
     *
     * @throws ContentNotFoundException
     * @throws FiveHundredResponseException
     */
    public function dom(): Dom
    {
        return new Dom($this->content());
    }

    /**
     * Get the response headers
     *
     * @return array<mixed>
     *
     * @throws FourHundredResponseException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     * @throws FiveHundredResponseException
     */
    public function headers(): array
    {
        return $this->response()->headers();
    }

    /**
     * Decode the response content as JSON
     *
     * @return array<mixed>
     *
     * @throws ContentNotFoundException
     * @throws FiveHundredResponseException
     * @throws JsonException
     */
    public function json(): array
    {
        return $this->response()->json();
    }

    /**
     * Send a POST request with data
     *
     * @param array<mixed> $data
     *
     * @throws FourHundredResponseException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     * @throws FiveHundredResponseException
     * @throws ResponseTooLargeException
     */
    public function post(array $data = []): Response
    {
        try {
            $options = $this->requestOptions();
            $options['body'] = $data;

            $this->guard();

            $response = $this->client()->request('POST', $this->uriUtility->url(), $options);

            return new Response($response->getStatusCode(), $response->getContent(), $response->getHeaders());
        } catch (TransportExceptionInterface $e) {
            // the client wraps whatever on_progress throws
            if ($e->getPrevious() instanceof ResponseTooLargeException) {
                throw $e->getPrevious();
            }

            throw new NetworkErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (ClientExceptionInterface $e) {
            throw new FourHundredResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        } catch (RedirectionExceptionInterface) {
            throw new MaxRedirectsReachedException();
        } catch (ServerExceptionInterface) {
            throw new FiveHundredResponseException();
        }
    }

    /**
     * @throws FourHundredResponseException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     * @throws FiveHundredResponseException
     * @throws ResponseTooLargeException
     */
    public function response(): Response
    {
        try {
            $response = $this->clientResponse();

            return new Response($response->getStatusCode(), $response->getContent(), $response->getHeaders());
        } catch (TransportExceptionInterface $e) {
            // the client wraps whatever on_progress throws
            if ($e->getPrevious() instanceof ResponseTooLargeException) {
                throw $e->getPrevious();
            }

            throw new NetworkErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (ClientExceptionInterface $e) {
            throw new FourHundredResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        } catch (RedirectionExceptionInterface) {
            throw new MaxRedirectsReachedException();
        } catch (ServerExceptionInterface) {
            throw new FiveHundredResponseException();
        }
    }

    /**
     * Get the HTTP status code
     *
     * @throws FourHundredResponseException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     * @throws FiveHundredResponseException
     */
    public function statusCode(): int
    {
        return $this->response()->code();
    }

    /**
     * Get the url that the content is got from
     */
    public function url(): string
    {
        return $this->uriUtility->url();
    }

    /**
     * Set custom request headers
     *
     * @param array<string, string> $headers
     */
    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Set the request timeout in seconds
     */
    public function withTimeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Set the maximum response body size in bytes, the request is aborted once it is exceeded.
     *
     * Null (the default) means unlimited.
     */
    public function withMaxBytes(?int $bytes): self
    {
        $this->maxBytes = $bytes;

        return $this;
    }

    /**
     * Create a client to send a http request
     */
    protected function client(): HttpClientInterface
    {
        return ClientUtility::client($this->allowPrivateNetworks);
    }

    /**
     * @return array<string, mixed>
     */
    private function requestOptions(): array
    {
        $options = ['timeout' => $this->timeout];

        if ($this->headers !== []) {
            $options['headers'] = $this->headers;
        }

        if ($this->maxBytes !== null) {
            $max = $this->maxBytes;

            $options['on_progress'] = static function (int $downloaded, int $total) use ($max): void {
                if ($downloaded > $max || $total > $max) {
                    throw new ResponseTooLargeException('Response is larger than ' . $max . ' bytes');
                }
            };
        }

        return $options;
    }

    /**
     * @throws BlockedHostException
     */
    private function guard(): void
    {
        if (!$this->allowPrivateNetworks) {
            HostGuard::assertSafe($this->uriUtility->url());
        }
    }

    protected function clientResponse(): ResponseInterface
    {
        $this->guard();

        return $this->client()->request(
            'GET',
            $this->uriUtility->url(),
            $this->requestOptions(),
        );
    }
}
