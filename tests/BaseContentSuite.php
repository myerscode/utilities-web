<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Web\ContentUtility;

abstract class BaseContentSuite extends BaseSuite
{
    public function utility(string $url): ContentUtility
    {
        return new ContentUtility($url);
    }
}
