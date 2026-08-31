<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\TeamsApi;
use TwitchApi\Tests\ResourceTestCase;

class TeamsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return TeamsApi::class;
    }

    public function testShouldGetChannelTeams(): void
    {
        $this->api()->getChannelTeams(self::TOKEN, '123');

        $this->assertSent('GET', 'teams/channel', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetTeams(): void
    {
        $this->api()->getTeams(self::TOKEN);

        $this->assertSent('GET', 'teams');
    }

    public function testShouldGetTeamsByName(): void
    {
        $this->api()->getTeams(self::TOKEN, 'abc');

        $this->assertSent('GET', 'teams', [
            ['name', 'abc'],
        ]);
    }

    public function testShouldGetTeamsByNameWithHelperFunction(): void
    {
        $this->api()->getTeamsByName(self::TOKEN, 'abc');

        $this->assertSent('GET', 'teams', [
            ['name', 'abc'],
        ]);
    }

    public function testShouldGetTeamsById(): void
    {
        $this->api()->getTeams(self::TOKEN, null, '123');

        $this->assertSent('GET', 'teams', [
            ['id', '123'],
        ]);
    }

    public function testShouldGetTeamsByIdWithHelperFunction(): void
    {
        $this->api()->getTeamsById(self::TOKEN, '123');

        $this->assertSent('GET', 'teams', [
            ['id', '123'],
        ]);
    }
}
