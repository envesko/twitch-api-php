<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\AnalyticsApi;
use TwitchApi\Tests\ResourceTestCase;

class AnalyticsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return AnalyticsApi::class;
    }

    public function testShouldGetExtensionAnalytics(): void
    {
        $this->api()->getExtensionAnalytics(self::TOKEN);

        $this->assertSent('GET', 'analytics/extensions');
    }

    public function testShouldGetExtensionAnalyticsById(): void
    {
        $this->api()->getExtensionAnalytics(self::TOKEN, '1');

        $this->assertSent('GET', 'analytics/extensions', [
            ['extension_id', '1'],
        ]);
    }

    public function testShouldGetExtensionAnalyticsWithType(): void
    {
        $this->api()->getExtensionAnalytics(self::TOKEN, null, 'overview_v1');

        $this->assertSent('GET', 'analytics/extensions', [
            ['type', 'overview_v1'],
        ]);
    }

    public function testShouldGetExtensionAnalyticsWithFirst(): void
    {
        $this->api()->getExtensionAnalytics(self::TOKEN, null, null, 100);

        $this->assertSent('GET', 'analytics/extensions', [
            ['first', '100'],
        ]);
    }

    public function testShouldGetExtensionAnalyticsWithAfter(): void
    {
        $this->api()->getExtensionAnalytics(self::TOKEN, null, null, null, 'abc');

        $this->assertSent('GET', 'analytics/extensions', [
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetExtensionAnalyticsWithStartedAt(): void
    {
        $this->api()->getExtensionAnalytics(self::TOKEN, null, null, null, null, '2020-01-01T00:00:00Z');

        $this->assertSent('GET', 'analytics/extensions', [
            ['started_at', '2020-01-01T00:00:00Z'],
        ]);
    }

    public function testShouldGetExtensionAnalyticsWithEndedAt(): void
    {
        $this->api()->getExtensionAnalytics(self::TOKEN, null, null, null, null, null, '2020-01-01T00:00:00Z');

        $this->assertSent('GET', 'analytics/extensions', [
            ['ended_at', '2020-01-01T00:00:00Z'],
        ]);
    }

    public function testShouldGetGameAnalytics(): void
    {
        $this->api()->getGameAnalytics(self::TOKEN);

        $this->assertSent('GET', 'analytics/games');
    }

    public function testShouldGetGameAnalyticsById(): void
    {
        $this->api()->getGameAnalytics(self::TOKEN, '1');

        $this->assertSent('GET', 'analytics/games', [
            ['game_id', '1'],
        ]);
    }

    public function testShouldGetGameAnalyticsWithType(): void
    {
        $this->api()->getGameAnalytics(self::TOKEN, null, 'overview_v1');

        $this->assertSent('GET', 'analytics/games', [
            ['type', 'overview_v1'],
        ]);
    }

    public function testShouldGetGameAnalyticsWithFirst(): void
    {
        $this->api()->getGameAnalytics(self::TOKEN, null, null, 100);

        $this->assertSent('GET', 'analytics/games', [
            ['first', '100'],
        ]);
    }

    public function testShouldGetGameAnalyticsWithAfter(): void
    {
        $this->api()->getGameAnalytics(self::TOKEN, null, null, null, 'abc');

        $this->assertSent('GET', 'analytics/games', [
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetGameAnalyticsWithStartedAt(): void
    {
        $this->api()->getGameAnalytics(self::TOKEN, null, null, null, null, '2020-01-01T00:00:00Z');

        $this->assertSent('GET', 'analytics/games', [
            ['started_at', '2020-01-01T00:00:00Z'],
        ]);
    }

    public function testShouldGetGameAnalyticsWithEndedAt(): void
    {
        $this->api()->getGameAnalytics(self::TOKEN, null, null, null, null, null, '2020-01-01T00:00:00Z');

        $this->assertSent('GET', 'analytics/games', [
            ['ended_at', '2020-01-01T00:00:00Z'],
        ]);
    }
}
