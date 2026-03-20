<?php

namespace Myerscode\Utilities\Web;

class Utility
{
    public function __construct(private readonly string $url)
    {
    }

    /**
     * Get the content utility
     */
    public function content(): ContentUtility
    {
        return new ContentUtility($this->url);
    }

    /**
     * Get the Ping utility
     */
    public function ping(): PingUtility
    {
        return new PingUtility($this->url);
    }

    /**
     * Get the URL utility
     */
    public function url(): UriUtility
    {
        return new UriUtility($this->url);
    }
}
