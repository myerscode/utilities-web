<?php

namespace Tests;

use Myerscode\Utilities\Web\UriUtility;

abstract class BaseUriSuite extends BaseSuite
{
    /**
     * @param $url
     * @return UriUtility
     */
    public function utility($url)
    {
        return new UriUtility($url);
    }
}
