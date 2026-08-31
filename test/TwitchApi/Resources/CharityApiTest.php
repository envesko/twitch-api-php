<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\CharityApi;
use TwitchApi\Tests\ResourceTestCase;

class CharityApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return CharityApi::class;
    }

    public function testShouldGetCharityCampaigns(): void
    {
        $this->api()->getCharityCampaign(self::TOKEN, '123');

        $this->assertSent('GET', 'charity/campaigns', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetCharityCampaignDonations(): void
    {
        $this->api()->getCharityCampaignDonations(self::TOKEN, '123');

        $this->assertSent('GET', 'charity/donations', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetCharityCampaignDonationsWithOpts(): void
    {
        $this->api()->getCharityCampaignDonations(self::TOKEN, '123', 100, 'abc');

        $this->assertSent('GET', 'charity/donations', [
            ['broadcaster_id', '123'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }
}
