<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\HypeTrainApi;
use TwitchApi\Tests\ResourceTestCase;

class HypeTrainApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return HypeTrainApi::class;
    }

    public function testShouldGetHypeTrainStatus(): void
    {
        $this->api()->getHypeTrainStatus(self::TOKEN, '123');

        $this->assertSent('GET', 'hypetrain/status', [
            ['broadcaster_id', '123'],
        ]);
    }
}
