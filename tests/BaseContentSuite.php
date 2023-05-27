<?php

namespace Tests;

use Myerscode\Utilities\Web\ContentUtility;

abstract class BaseContentSuite extends BaseSuite
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
