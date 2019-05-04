<?php

namespace Myerscode\Utilities\Web;

use Myerscode\Utilities\Web\Exceptions\ContentNotFoundException;
use Myerscode\Utilities\Web\Exceptions\UnreachableContentException;
use Myerscode\Utilities\Web\Resource\Dom;
use Myerscode\Utilities\Web\Resource\Response;
use Zend\Http\Client;

/**
 * Class Utility
 *
 * @package Myerscode\Utilities\Web
 */
class ContentUtility
{

    /**
     * The url to get content from
     *
     * @var UriUtility $url
     */
    private $url;

    /**
     * Collection of request options to be passed to guzzle
     *
     * @var array
     */
    private $requestOptions = [
        'timeout' => 60,
    ];

    /**
     * Utility constructor.
     *
     * @param $url
     * @param array $requestOptions
     */
    public function __construct($url, array $requestOptions = [])
    {
        $this->url = new UriUtility($url);
        $this->setRequestOptions($requestOptions);
    }

    /**
     * Create a client to send a http request
     *
     * @return Client
     */
    protected function client()
    {
        return Utility::client($this->url(), $this->requestOptions);
    }

    /**
     * @return Response
     */
    public function response()
    {
        $response = $this->client()->send();

        return new Response(intval($response->getStatusCode()), $response->getBody());
    }

    /**
     * Get the content from the url
     *
     * @return string
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
        } catch (ContentNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new UnreachableContentException();
        }
    }

    /**
     * Get the content as a content dom
     *
     * @return Dom
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
     * @return void
     */
    private function setRequestOptions(array $requestOptions): void
    {
        $this->requestOptions = array_merge($this->requestOptions, $requestOptions);
    }

    /**
     * Get the url that the the content is got from
     *
     * @return string
     */
    public function url(): string
    {
        return $this->url->value();
    }
}
