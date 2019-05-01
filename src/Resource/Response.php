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

    public function __construct($code, $content = '')
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
    public function content()
    {
        return $this->content;
    }


}
