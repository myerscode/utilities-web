<?php

namespace Myerscode\Utilities\Web\Resource;

class Response
{
    public function __construct(private readonly int $code, private readonly string $content = '')
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

    public function dom(): Dom
    {
        return new Dom($this->content());
    }
}
