<?php

namespace Tests\PingUtility;

use Iterator;
use Tests\BasePingSuite;

class SystemPingTest extends BasePingSuite
{
    public function testPingValidUrl(): void
    {
        $this->assertTrue($this->utility(self::serverUrl())->ping()['alive']);
    }

    public function testPingInvalidUrl(): void
    {
        $this->assertFalse($this->utility('https://not.a.real.domain')->ping()['alive']);
    }

}
