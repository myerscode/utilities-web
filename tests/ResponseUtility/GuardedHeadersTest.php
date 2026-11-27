<?php

declare(strict_types=1);

namespace Tests\ResponseUtility;

use Myerscode\Utilities\Web\ResponseUtility;
use PHPUnit\Framework\Attributes\Group;
use Tests\BaseSuite;

/**
 * Guarded header checks pin the request to the vetted IP, so these need a live public host.
 * Excluded from CI with the rest of the 'network' group.
 */
#[Group('network')]
final class GuardedHeadersTest extends BaseSuite
{
    public function testGuardedHttpRequestKeepsHostHeaderWhenPinned(): void
    {
        $this->assertSame(200, (new ResponseUtility('http://example.com'))->fromHeaders()->code());
    }
    public function testGuardedHttpsRequestKeepsHostAndTlsNameWhenPinned(): void
    {
        $this->assertSame(200, (new ResponseUtility('https://example.com'))->fromHeaders()->code());
    }
}
