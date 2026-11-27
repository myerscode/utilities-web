<?php

namespace Myerscode\Utilities\Web;

class Utility
{
    private bool $allowPrivateNetworks = false;

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
     * Allow (or block again) requests to loopback, private, link-local and reserved addresses
     * for the utilities created from here. Blocked by default.
     */
    public function allowPrivateNetworks(bool $allow = true): self
    {
        $this->allowPrivateNetworks = $allow;

        return $this;
    }

    /**
     * Get the content utility
     */
    public function content(): ContentUtility
    {
        return (new ContentUtility($this->url))->allowPrivateNetworks($this->allowPrivateNetworks);
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
        return (new ResponseUtility($this->url))->allowPrivateNetworks($this->allowPrivateNetworks);
    }

    /**
     * Get the URL utility
     */
    public function url(): UriUtility
    {
        return (new UriUtility($this->url))->allowPrivateNetworks($this->allowPrivateNetworks);
    }
}
