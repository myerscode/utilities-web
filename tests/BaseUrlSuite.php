<?php

namespace Tests;

use Myerscode\Utilities\Web\UrlUtility;
use PHPUnit\Framework\TestCase;

abstract class BaseUrlSuite extends TestCase
{

    /**
     * @param $url
     * @return UrlUtility
     */
    public function utility($url)
    {
        return new UrlUtility($url);
    }
}
