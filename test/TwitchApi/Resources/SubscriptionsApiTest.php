<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\SubscriptionsApi;
use TwitchApi\Tests\ResourceTestCase;

class SubscriptionsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return SubscriptionsApi::class;
    }

    public function testShouldGetBroadcasterSubscriptions(): void
    {
        $this->api()->getBroadcasterSubscriptions(self::TOKEN, '123');

        $this->assertSent('GET', 'subscriptions', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetBroadcasterSubscriptionsWithAll(): void
    {
        $this->api()->getBroadcasterSubscriptions(self::TOKEN, '123', 100, 'abc');

        $this->assertSent('GET', 'subscriptions', [
            ['broadcaster_id', '123'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetBroadcasterSubscribers(): void
    {
        $this->api()->getBroadcasterSubscribers(self::TOKEN, '123');

        $this->assertSent('GET', 'subscriptions', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetBroadcasterSubscribersWithId(): void
    {
        $this->api()->getBroadcasterSubscribers(self::TOKEN, '123', ['321']);

        $this->assertSent('GET', 'subscriptions', [
            ['broadcaster_id', '123'],
            ['user_id', '321'],
        ]);
    }

    public function testShouldGetBroadcasterSubscribersWithIds(): void
    {
        $this->api()->getBroadcasterSubscribers(self::TOKEN, '123', ['321', '456']);

        $this->assertSent('GET', 'subscriptions', [
            ['broadcaster_id', '123'],
            ['user_id', '321'],
            ['user_id', '456'],
        ]);
    }

    public function testShouldCheckUserSubscriptions(): void
    {
        $this->api()->checkUserSubscription(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'subscriptions/user', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
        ]);
    }
}
