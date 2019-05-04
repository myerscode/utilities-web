<?php

namespace Myerscode\Utilities\Web\Resource;


class Response
{

    /**
     * @var int
     */
    private $code;

    /**
     * @var string
     */
    private $content;


    public function __construct(int $code, string $content = '')
    {
        $this->code = $code;
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function code(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * @return Dom
     */
    public function dom(): Dom
    {
        return new Dom($this->content());
    }
}
