<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\RaidsApi;
use TwitchApi\Tests\ResourceTestCase;

class RaidsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return RaidsApi::class;
    }

    public function testShouldStartARaid(): void
    {
        $this->api()->startRaid(self::TOKEN, '123', '456');

        $this->assertSent('POST', 'raids', [
            ['from_broadcaster_id', '123'],
            ['to_broadcaster_id', '456'],
        ]);
    }

    public function testShouldCancelARaid(): void
    {
        $this->api()->cancelRaid(self::TOKEN, '123');

        $this->assertSent('DELETE', 'raids', [
            ['broadcaster_id', '123'],
        ]);
    }
}
