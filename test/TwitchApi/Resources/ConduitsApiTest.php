<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ConduitsApi;
use TwitchApi\Tests\ResourceTestCase;

class ConduitsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ConduitsApi::class;
    }

    public function testShouldGetConduits(): void
    {
        $this->api()->getConduits(self::TOKEN);

        $this->assertSent('GET', 'eventsub/conduits');
    }

    public function testShouldCreateConduits(): void
    {
        $this->api()->createConduits(self::TOKEN, 5);

        $this->assertSent('POST', 'eventsub/conduits');
        $this->assertSentBody(['shard_count' => 5]);
    }

    public function testShouldUpdateConduits(): void
    {
        $this->api()->updateConduits(self::TOKEN, 'abc', 10);

        $this->assertSent('PATCH', 'eventsub/conduits');
        $this->assertSentBody(['id' => 'abc', 'shard_count' => 10]);
    }

    public function testShouldDeleteAConduit(): void
    {
        $this->api()->deleteConduit(self::TOKEN, 'abc');

        $this->assertSent('DELETE', 'eventsub/conduits', [
            ['id', 'abc'],
        ]);
    }

    public function testShouldGetConduitShards(): void
    {
        $this->api()->getConduitShards(self::TOKEN, 'abc');

        $this->assertSent('GET', 'eventsub/conduits/shards', [
            ['conduit_id', 'abc'],
        ]);
    }

    public function testShouldGetConduitShardsFilteredByStatus(): void
    {
        $this->api()->getConduitShards(self::TOKEN, 'abc', 'enabled', 'cursor');

        $this->assertSent('GET', 'eventsub/conduits/shards', [
            ['conduit_id', 'abc'],
            ['status', 'enabled'],
            ['after', 'cursor'],
        ]);
    }

    public function testShouldUpdateConduitShards(): void
    {
        $shards = [['id' => '0', 'transport' => ['method' => 'webhook', 'callback' => 'https://example.com/cb', 'secret' => 'secret']]];

        $this->api()->updateConduitShards(self::TOKEN, 'abc', $shards);

        $this->assertSent('PATCH', 'eventsub/conduits/shards');
        $this->assertSentBody(['conduit_id' => 'abc', 'shards' => $shards]);
    }
}
