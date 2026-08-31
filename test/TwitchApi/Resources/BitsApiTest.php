<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\BitsApi;
use TwitchApi\Tests\ResourceTestCase;

class BitsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return BitsApi::class;
    }

    public function testShouldGetcheermotes(): void
    {
        $this->api()->getCheermotes(self::TOKEN);

        $this->assertSent('GET', 'bits/cheermotes');
    }

    public function testShouldGetcheermotesByBroadcasterId(): void
    {
        $this->api()->getCheermotes(self::TOKEN, '123');

        $this->assertSent('GET', 'bits/cheermotes', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldExtensionTransactions(): void
    {
        $this->api()->getExtensionTransactions(self::TOKEN, '1');

        $this->assertSent('GET', 'extensions/transactions', [
            ['extension_id', '1'],
        ]);
    }

    public function testShouldExtensionTransactionsWithTransactionId(): void
    {
        $this->api()->getExtensionTransactions(self::TOKEN, '1', ['321']);

        $this->assertSent('GET', 'extensions/transactions', [
            ['extension_id', '1'],
            ['id', '321'],
        ]);
    }

    public function testShouldExtensionTransactionsWithFirst(): void
    {
        $this->api()->getExtensionTransactions(self::TOKEN, '1', [], 100);

        $this->assertSent('GET', 'extensions/transactions', [
            ['extension_id', '1'],
            ['first', '100'],
        ]);
    }

    public function testShouldExtensionTransactionsWithAfter(): void
    {
        $this->api()->getExtensionTransactions(self::TOKEN, '1', [], null, '100');

        $this->assertSent('GET', 'extensions/transactions', [
            ['extension_id', '1'],
            ['after', '100'],
        ]);
    }

    public function testShouldGetBitsLeaderboard(): void
    {
        $this->api()->getBitsLeaderboard(self::TOKEN);

        $this->assertSent('GET', 'bits/leaderboard');
    }

    public function testShouldGetBitsLeaderboardWithOpts(): void
    {
        $this->api()->getBitsLeaderboard(self::TOKEN, 100, 'all', '2019-10-12T07:20:50.52Z', '123');

        $this->assertSent('GET', 'bits/leaderboard', [
            ['count', '100'],
            ['period', 'all'],
            ['started_at', '2019-10-12T07:20:50.52Z'],
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetACustomPowerUp(): void
    {
        $this->api()->getCustomPowerUp(self::TOKEN, '123');

        $this->assertSent('GET', 'bits/custom_power_ups', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetExtensionBitsProducts(): void
    {
        $this->api()->getExtensionBitsProducts(self::TOKEN);

        $this->assertSent('GET', 'bits/extensions');
    }

    public function testShouldGetAllExtensionBitsProducts(): void
    {
        $this->api()->getExtensionBitsProducts(self::TOKEN, true);

        $this->assertSent('GET', 'bits/extensions', [
            ['should_include_all', '1'],
        ]);
    }

    public function testShouldUpdateAnExtensionBitsProduct(): void
    {
        $cost = ['amount' => 100, 'type' => 'bits'];

        $this->api()->updateExtensionBitsProduct(self::TOKEN, 'SKU', $cost, 'Name', ['in_development' => true]);

        $this->assertSent('PUT', 'bits/extensions');
        $this->assertSentBody(['sku' => 'SKU', 'cost' => $cost, 'display_name' => 'Name', 'in_development' => true]);
    }
}
