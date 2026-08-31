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

    public function testShouldOmitLiveOnlyWhenFalse(): void
    {
        // $liveOnly gained a ?bool type in 8.0.0. Passing a string there coerces to true
        // in a caller that is not under strict_types, which inverts the filter without
        // raising anything, so UPGRADING.md documents it. These two pin the behaviour a
        // caller passing real booleans gets, which is unchanged from 7.x.
        $this->api()->searchChannels(self::TOKEN, 'test', false);

        $this->assertSent('GET', 'search/channels', [
            ['query', 'test'],
        ]);
    }

    public function testShouldOmitLiveOnlyWhenNull(): void
    {
        $this->api()->searchChannels(self::TOKEN, 'test', null);

        $this->assertSent('GET', 'search/channels', [
            ['query', 'test'],
        ]);
    }
}
