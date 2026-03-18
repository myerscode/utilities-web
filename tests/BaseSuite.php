<?php

declare(strict_types=1);

namespace Tests;

use donatj\MockWebServer\MockWebServer;
use PHPUnit\Framework\TestCase;

abstract class BaseSuite extends TestCase
{
    protected static MockWebServer $server;

    public static function setUpBeforeClass(): void
    {
        self::$server = new MockWebServer();

        self::$server->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }

    public static function serverIP(string $path = ''): string
    {
        return self::$server->getServerRoot() . $path;
    }

    public static function serverUrl(string $path = ''): string
    {
        return self::$server->getServerRoot() . $path;
    }

    public function server(): MockWebServer
    {
        if (!isset(self::$server)) {
            self::$server = new MockWebServer();
        }

        return self::$server;
    }
}
