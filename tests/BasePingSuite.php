<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Web\PingUtility;

abstract class BasePingSuite extends BaseSuite
{
    public function utility(string $url): PingUtility
    {
        return new PingUtility($url);
    }
}
