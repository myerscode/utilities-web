<?php

namespace Myerscode\Utilities\Web;

class Utility
{

    const METHOD_CURL = 1;

    const METHOD_HEADERS = 2;

    const METHOD_HTTP = 3;

    const METHOD_SYSTEM = 4;

    /**
     * @var string
     */
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Get the Ping utility
     *
     * @return PingUtility
     */
    public function ping(): PingUtility
    {
        return (new PingUtility($this->url));
    }

    /**
     * Get the URL utility
     *
     * @return UrlUtility
     */
    public function url(): UrlUtility
    {
        return (new UrlUtility($this->url));
    }

    /**
     * Get the content utility
     *
     * @return ContentUtility
     */
    public function content(): ContentUtility
    {
        return (new ContentUtility($this->url));
    }
}
