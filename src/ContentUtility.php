<?php

namespace Myerscode\Utilities\Web;

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
    private readonly UriUtility $uriUtility;

    public function __construct(public readonly string $url)
    {
        $this->uriUtility = new UriUtility($url);
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
            throw new FourHundredResponseException($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
        } catch (RedirectionExceptionInterface) {
            throw new MaxRedirectsReachedException();
        } catch (ServerExceptionInterface) {
            throw new FiveHundredResponseException();
        }
    }

    /**
     * Get the url that the content is got from
     */
    public function url(): string
    {
        return $this->uriUtility->url();
    }

    /**
     * Create a client to send a http request
     */
    protected function client(): HttpClientInterface
    {
        return ClientUtility::client();
    }

    protected function clientResponse(): ResponseInterface
    {
        return $this->client()->request(
            'GET',
            $this->uriUtility->url(),
        );
    }

}
