<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\SearchApi;
use TwitchApi\Tests\ResourceTestCase;

class SearchApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return SearchApi::class;
    }

    public function testShouldSearchCategories(): void
    {
        $this->api()->searchCategories(self::TOKEN, 'test');

        $this->assertSent('GET', 'search/categories', [
            ['query', 'test'],
        ]);
    }

    public function testShouldSearchCategoriesWithOpts(): void
    {
        $this->api()->searchCategories(self::TOKEN, 'test', '100', 'abc');

        $this->assertSent('GET', 'search/categories', [
            ['query', 'test'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldSearchChannels(): void
    {
        $this->api()->searchChannels(self::TOKEN, 'test');

        $this->assertSent('GET', 'search/channels', [
            ['query', 'test'],
        ]);
    }

    public function testShouldSearchChannelsWithOpts(): void
    {
        $this->api()->searchChannels(self::TOKEN, 'test', true, '100', 'abc');

        $this->assertSent('GET', 'search/channels', [
            ['query', 'test'],
            ['live_only', '1'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }
}
