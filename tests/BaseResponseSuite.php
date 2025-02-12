<?php

namespace Tests;

use Myerscode\Utilities\Web\ResponseUtility;

abstract class BaseResponseSuite extends BaseSuite
{
    public function utility(string $url): ResponseUtility
    {
        return new ResponseUtility($url);
    }
}
