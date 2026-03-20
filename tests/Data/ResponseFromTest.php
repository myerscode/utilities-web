<?php

declare(strict_types=1);

namespace Tests\Data;

use Myerscode\Utilities\Web\Data\ResponseFrom;
use PHPUnit\Framework\TestCase;

final class ResponseFromTest extends TestCase
{
    public function testEnumCases(): void
    {
        $cases = ResponseFrom::cases();

        $this->assertCount(3, $cases);
    }
    public function testEnumValues(): void
    {
        $this->assertSame('curl', ResponseFrom::CURL->value);
        $this->assertSame('headers', ResponseFrom::HEADERS->value);
        $this->assertSame('http', ResponseFrom::HTTP->value);
    }
}
