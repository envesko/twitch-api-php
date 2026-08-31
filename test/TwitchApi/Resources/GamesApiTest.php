<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\GamesApi;
use TwitchApi\Tests\ResourceTestCase;

class GamesApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return GamesApi::class;
    }

    public function testShouldGetGamesById(): void
    {
        $this->api()->getGames(self::TOKEN, ['123']);

        $this->assertSent('GET', 'games', [
            ['id', '123'],
        ]);
    }

    public function testShouldGetGamesByIds(): void
    {
        $this->api()->getGames(self::TOKEN, ['123', '456']);

        $this->assertSent('GET', 'games', [
            ['id', '123'],
            ['id', '456'],
        ]);
    }

    public function testShouldGetGamesByName(): void
    {
        $this->api()->getGames(self::TOKEN, [], ['abc']);

        $this->assertSent('GET', 'games', [
            ['name', 'abc'],
        ]);
    }

    public function testShouldGetGamesByNames(): void
    {
        $this->api()->getGames(self::TOKEN, [], ['abc', 'def']);

        $this->assertSent('GET', 'games', [
            ['name', 'abc'],
            ['name', 'def'],
        ]);
    }

    public function testShouldGetGamesByIdsAndNames(): void
    {
        $this->api()->getGames(self::TOKEN, ['123', '456'], ['abc', 'def']);

        $this->assertSent('GET', 'games', [
            ['id', '123'],
            ['id', '456'],
            ['name', 'abc'],
            ['name', 'def'],
        ]);
    }

    public function testShouldGetTopGames(): void
    {
        $this->api()->getTopGames(self::TOKEN);

        $this->assertSent('GET', 'games/top');
    }

    public function testShouldGetTopGamesWithOpts(): void
    {
        $this->api()->getTopGames(self::TOKEN, 100, 'abc', 'def');

        $this->assertSent('GET', 'games/top', [
            ['first', '100'],
            ['before', 'abc'],
            ['after', 'def'],
        ]);
    }
}
