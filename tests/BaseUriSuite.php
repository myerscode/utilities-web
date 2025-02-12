<?php

namespace Tests;

use Myerscode\Utilities\Web\UriUtility;

abstract class BaseUriSuite extends BaseSuite
{
    public function utility(string $url): UriUtility
    {
        return new UriUtility($url);
    }
}
