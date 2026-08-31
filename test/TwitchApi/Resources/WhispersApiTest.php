<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\WhispersApi;
use TwitchApi\Tests\ResourceTestCase;

class WhispersApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return WhispersApi::class;
    }

    public function testShouldSendAWhisper(): void
    {
        $this->api()->sendWhisper(self::TOKEN, '123', '456', 'abc');

        $this->assertSent('POST', 'whispers', [
            ['from_user_id', '123'],
            ['to_user_id', '456'],
        ]);
        $this->assertSentBody(['message' => 'abc']);
    }
}
