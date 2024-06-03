<?php

namespace Tests;

use donatj\MockWebServer\MockWebServer;
use PHPUnit\Framework\TestCase;

class BaseSuite extends TestCase
{
    protected static MockWebServer $server;

    public static function serverUrl(string $path = ''): string
    {
        return self::$server->getServerRoot() . $path;
    }
    public static function serverIP(string $path = ''): string
    {
        return self::$server->getServerRoot() . $path;
    }

    public static function setUpBeforeClass(): void
    {
        self::$server = new MockWebServer();

        self::$server->start();
    }

    public static function tearDownAfterClass(): void
    {
        self::$server->stop();
    }
}
