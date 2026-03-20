<?php

declare(strict_types=1);

namespace Tests;

use Myerscode\Utilities\Web\ClientUtility;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ClientUtilityTest extends TestCase
{
    public function testClientReturnsHttpClientInterface(): void
    {
        $client = ClientUtility::client();

        $this->assertInstanceOf(HttpClientInterface::class, $client);
    }
}
