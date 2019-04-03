<?php

namespace Tests;

use Myerscode\Utilities\Web\ContentUtility;
use PHPUnit\Framework\TestCase;

abstract class BaseContentSuite extends TestCase
{

    /**
     * @param $url
     * @param $requestOptions
     * @return ContentUtility
     */
    public function utility($url, $requestOptions = [])
    {
        return new ContentUtility($url, $requestOptions);
    }
}
