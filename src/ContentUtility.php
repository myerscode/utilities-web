<?php

namespace Myerscode\Utilities\Web;

use Exception;
use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\UnreachableContentException;
use Myerscode\Utilities\Web\Resource\Dom;
use Myerscode\Utilities\Web\Resource\Response;
use Laminas\Http\Client;

/**
 * Class ClientUtility
 *
 * @package Myerscode\Utilities\Web
 */
class ContentUtility
{
    /**
     * The url to get content from
     */
    private readonly UriUtility $uriUtility;

    /**
     * Collection of request options to be passed to guzzle
     */
    private array $requestOptions = [
        'timeout' => 60,
    ];

    /**
     * ClientUtility constructor.
     *
     * @param $url
     */
    public function __construct($url, array $requestOptions = [])
    {
        $this->uriUtility = new UriUtility($url);
        $this->setRequestOptions($requestOptions);
    }

    /**
     * Create a client to send a http request
     *
     * @return Client
     */
    protected function client(): Client
    {
        return ClientUtility::client($this->url(), $this->requestOptions);
    }

    public function response(): Response
    {
        $response = $this->client()->send();

        return new Response((int) $response->getStatusCode(), $response->getBody());
    }

    /**
     * Get the content from the url
     *
     * @throws UnreachableContentException
     * @throws ContentNotFoundException
     */
    public function content(): string
    {
        try {
            $response = $this->response();

            if ($response->code() == 404) {
                throw new ContentNotFoundException();
            }

            return $response->content();
        } catch (ContentNotFoundException $contentNotFoundException) {
            throw $contentNotFoundException;
        } catch (Exception) {
            throw new UnreachableContentException();
        }
    }

    /**
     * Get the content as a content dom
     *
     * @throws ContentNotFoundException
     * @throws UnreachableContentException
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
     * Get the url that the the content is got from
     */
    public function url(): string
    {
        return $this->uriUtility->value();
    }
}
