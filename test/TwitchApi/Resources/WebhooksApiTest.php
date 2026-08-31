<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\WebhooksApi;
use TwitchApi\Tests\ResourceTestCase;

class WebhooksApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return WebhooksApi::class;
    }

    public function testShouldGetWebhooksSubscriptions(): void
    {
        $this->api()->getWebhookSubscriptions(self::TOKEN);

        $this->assertSent('GET', 'webhooks/subscriptions');
    }

    public function testShouldGetWebhooksSubscriptionsWithFirst(): void
    {
        $this->api()->getWebhookSubscriptions(self::TOKEN, 100);

        $this->assertSent('GET', 'webhooks/subscriptions', [
            ['first', '100'],
        ]);
    }

    public function testShouldGetWebhooksSubscriptionsWithAfter(): void
    {
        $this->api()->getWebhookSubscriptions(self::TOKEN, null, 'abc');

        $this->assertSent('GET', 'webhooks/subscriptions', [
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetWebhooksSubscriptionsWithEverything(): void
    {
        $this->api()->getWebhookSubscriptions(self::TOKEN, 100, 'abc');

        $this->assertSent('GET', 'webhooks/subscriptions', [
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }
}
