<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\GoalsApi;
use TwitchApi\Tests\ResourceTestCase;

class GoalsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return GoalsApi::class;
    }

    public function testShouldGetGoalsByBroadcasterId(): void
    {
        $this->api()->getGoals(self::TOKEN, '123');

        $this->assertSent('GET', 'goals', [
            ['broadcaster_id', '123'],
        ]);
    }
}
