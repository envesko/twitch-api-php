<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\EntitlementsApi;
use TwitchApi\Tests\ResourceTestCase;

class EntitlementsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return EntitlementsApi::class;
    }

    public function testShouldGetDropEntitlementsById(): void
    {
        $this->api()->getDropsEntitlements(self::TOKEN, '123');

        $this->assertSent('GET', 'entitlements/drops', [
            ['id', '123'],
        ]);
    }

    public function testShouldGetDropEntitlementsByUserId(): void
    {
        $this->api()->getDropsEntitlements(self::TOKEN, null, '123');

        $this->assertSent('GET', 'entitlements/drops', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetDropEntitlementsByUserIdWithOpts(): void
    {
        $this->api()->getDropsEntitlements(self::TOKEN, null, '123', null, 'abc', 100);

        $this->assertSent('GET', 'entitlements/drops', [
            ['user_id', '123'],
            ['after', 'abc'],
            ['first', '100'],
        ]);
    }

    public function testShouldGetDropEntitlementsByGameId(): void
    {
        $this->api()->getDropsEntitlements(self::TOKEN, null, null, '123');

        $this->assertSent('GET', 'entitlements/drops', [
            ['game_id', '123'],
        ]);
    }

    public function testShouldGetDropEntitlementsByGameIdWithOpts(): void
    {
        $this->api()->getDropsEntitlements(self::TOKEN, null, null, '123', 'abc', 100);

        $this->assertSent('GET', 'entitlements/drops', [
            ['game_id', '123'],
            ['after', 'abc'],
            ['first', '100'],
        ]);
    }

    public function testShouldGetDropEntitlementsByStatus(): void
    {
        $this->api()->getDropsEntitlements(self::TOKEN, null, null, null, null, null, 'CLAIMED');

        $this->assertSent('GET', 'entitlements/drops', [
            ['fulfillment_status', 'CLAIMED'],
        ]);
    }

    public function testShouldUpdateDropEntitlements(): void
    {
        $this->api()->updateDropsEntitlements(self::TOKEN);

        $this->assertSent('PATCH', 'entitlements/drops');
    }

    public function testShouldUpdateOneDropEntitlements(): void
    {
        $this->api()->updateDropsEntitlements(self::TOKEN, ['123'], 'FULFILLED');

        $this->assertSent('PATCH', 'entitlements/drops');
        $this->assertSentBody(['entitlement_ids' => ['123'], 'fulfillment_status' => 'FULFILLED']);
    }

    public function testShouldUpdateMultipleDropEntitlements(): void
    {
        $this->api()->updateDropsEntitlements(self::TOKEN, ['123', '456'], 'FULFILLED');

        $this->assertSent('PATCH', 'entitlements/drops');
        $this->assertSentBody(['entitlement_ids' => ['123', '456'], 'fulfillment_status' => 'FULFILLED']);
    }
}
