<?php

namespace Myerscode\Utilities\Web\Resource;

readonly class Response
{
    public function __construct(private int $code, private string $content = '', private array $headers = [])
    {
    }

    public function code(): int
    {
        return $this->code;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function dom(): Dom
    {
        return new Dom($this->content());
    }
}
