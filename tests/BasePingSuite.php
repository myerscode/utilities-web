<?php

namespace Tests;

use Myerscode\Utilities\Web\PingUtility;

abstract class BasePingSuite extends BaseSuite
{
    /**
     * @param $url
     * @return PingUtility
     */
    public function utility($url)
    {
        return new PingUtility($url);
    }
}
