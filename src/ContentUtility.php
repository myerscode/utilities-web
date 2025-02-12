<?php

namespace Myerscode\Utilities\Web;

use Exception;
use Myerscode\Utilities\Web\Exceptions\FourHundredResponseException;
use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\MaxRedirectsReachedException;
use Myerscode\Utilities\Web\Exceptions\NetworkErrorException;
use Myerscode\Utilities\Web\Exceptions\FiveHundredResponseException;
use Myerscode\Utilities\Web\Resource\Dom;
use Myerscode\Utilities\Web\Resource\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ContentUtility
{
    /**
     * The url to get content from
     */
    private readonly UriUtility $utility;

    /**
     * Collection of request options to be passed to guzzle
     */
    private array $requestOptions = [
        'timeout' => 60,
    ];

    public function __construct(readonly string $url, array $requestOptions = [])
    {
        $this->utility = new UriUtility($url);
        $this->setRequestOptions($requestOptions);
    }

    /**
     * Create a client to send a http request
     */
    protected function client(): HttpClientInterface
    {
        return Utility::client();
    }

    protected function clientResponse(): ResponseInterface
    {
        return $this->client()->request(
            'GET',
            $this->utility->url()
        );
    }

    /**
     * @throws FourHundredResponseException
     * @throws MaxRedirectsReachedException
     * @throws NetworkErrorException
     * @throws FiveHundredResponseException
     */
    public function response(): Response
    {
        try {
            $response = $this->clientResponse();
            return new Response($response->getStatusCode(), $response->getContent(), $response->getHeaders());
        } catch (TransportExceptionInterface $e) {
            throw new NetworkErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (ClientExceptionInterface $e) {
            throw new FourHundredResponseException($e->getMessage(), $e->getCode(), $e);
        } catch (RedirectionExceptionInterface $e) {
            throw new MaxRedirectsReachedException();
        } catch (ServerExceptionInterface $e) {
            throw new FiveHundredResponseException();
        }
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

        if ($response->code() == 404) {
            throw new ContentNotFoundException();
        }

        return $response->content();
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
     * Set options to use in a request against the url
     *
     * @param $requestOptions
     */
    private function setRequestOptions(array $requestOptions): void
    {
        $this->requestOptions = array_merge($this->requestOptions, $requestOptions);
    }

    /**
     * Get the url that the content is got from
     */
    public function url(): string
    {
        return $this->utility->url();
    }
}
