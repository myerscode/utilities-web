<?php

namespace Tests;

use Myerscode\Utilities\Web\PingUtility;
use PHPUnit\Framework\TestCase;

abstract class BasePingSuite extends TestCase
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
