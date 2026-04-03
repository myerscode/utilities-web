<?php

namespace Myerscode\Utilities\Web;

class Utility
{
    public function __construct(private readonly string $url)
    {
    }

    /**
     * Create a new utility instance
     */
    public static function make(string $url): self
    {
        return new self($url);
    }

    /**
     * Get the content utility
     */
    public function content(): ContentUtility
    {
        return new ContentUtility($this->url);
    }

    /**
     * Quick liveness check — is the URL responding with 2xx?
     */
    public function isAlive(): bool
    {
        return $this->response()->isAlive();
    }

    /**
     * Get the Ping utility
     */
    public function ping(): PingUtility
    {
        return new PingUtility($this->url);
    }

    /**
     * Get the response utility
     */
    public function response(): ResponseUtility
    {
        return new ResponseUtility($this->url);
    }

    /**
     * Get the URL utility
     */
    public function url(): UriUtility
    {
        return new UriUtility($this->url);
    }
}
