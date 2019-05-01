<?php

namespace Tests;

use Myerscode\Utilities\Web\UriUtility;
use PHPUnit\Framework\TestCase;

abstract class BaseUriSuite extends TestCase
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
