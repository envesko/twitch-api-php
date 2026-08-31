<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\SharedChatApi;
use TwitchApi\Tests\ResourceTestCase;

class SharedChatApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return SharedChatApi::class;
    }

    public function testShouldGetASharedChatSession(): void
    {
        $this->api()->getSharedChatSession(self::TOKEN, '123');

        $this->assertSent('GET', 'shared_chat/session', [
            ['broadcaster_id', '123'],
        ]);
    }
}
