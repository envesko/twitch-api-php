<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ChannelPointsApi;
use TwitchApi\Tests\ResourceTestCase;

class ChannelPointsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ChannelPointsApi::class;
    }

    public function testShouldGetCustomReward(): void
    {
        $this->api()->getCustomReward(self::TOKEN, '123');

        $this->assertSent('GET', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetCustomRewardById(): void
    {
        $this->api()->getCustomReward(self::TOKEN, '123', ['321']);

        $this->assertSent('GET', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
        ]);
    }

    public function testShouldGetCustomRewardByIds(): void
    {
        $this->api()->getCustomReward(self::TOKEN, '123', ['321', '456']);

        $this->assertSent('GET', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
            ['id', '456'],
        ]);
    }

    public function testShouldGetCustomRewardByIdWithOpts(): void
    {
        $this->api()->getCustomReward(self::TOKEN, '123', ['321'], true);

        $this->assertSent('GET', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
            ['only_manageable_rewards', '1'],
        ]);
    }

    public function testShouldGetCustomRewardByIdsWithOpts(): void
    {
        $this->api()->getCustomReward(self::TOKEN, '123', ['321', '456'], true);

        $this->assertSent('GET', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
            ['id', '456'],
            ['only_manageable_rewards', '1'],
        ]);
    }

    public function testShouldGetCustomRewardRedemption(): void
    {
        $this->api()->getCustomRewardRedemption(self::TOKEN, '123', '321');

        $this->assertSent('GET', 'channel_points/custom_rewards/redemptions', [
            ['broadcaster_id', '123'],
            ['reward_id', '321'],
        ]);
    }

    public function testShouldGetCustomRewardRedemptionWithOpts(): void
    {
        $this->api()->getCustomRewardRedemption(self::TOKEN, '123', '321', [], 'UNFULFILLED', 'NEWEST', 'abc', '100');

        $this->assertSent('GET', 'channel_points/custom_rewards/redemptions', [
            ['broadcaster_id', '123'],
            ['reward_id', '321'],
            ['status', 'UNFULFILLED'],
            ['sort', 'NEWEST'],
            ['after', 'abc'],
            ['first', '100'],
        ]);
    }

    public function testShouldGetCustomRewardRedemptionById(): void
    {
        $this->api()->getCustomRewardRedemption(self::TOKEN, '123', '321', ['111']);

        $this->assertSent('GET', 'channel_points/custom_rewards/redemptions', [
            ['broadcaster_id', '123'],
            ['reward_id', '321'],
            ['id', '111'],
        ]);
    }

    public function testShouldGetCustomRewardRedemptionByIds(): void
    {
        $this->api()->getCustomRewardRedemption(self::TOKEN, '123', '321', ['111', '222']);

        $this->assertSent('GET', 'channel_points/custom_rewards/redemptions', [
            ['broadcaster_id', '123'],
            ['reward_id', '321'],
            ['id', '111'],
            ['id', '222'],
        ]);
    }

    public function testShouldCreateCustomReward(): void
    {
        $this->api()->createCustomReward(self::TOKEN, '123', 'test 123', 100);

        $this->assertSent('POST', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['title' => 'test 123', 'cost' => 100]);
    }

    public function testShouldCreateCustomRewardWithOneOpt(): void
    {
        $this->api()->createCustomReward(self::TOKEN, '123', 'test 123', 100, ['prompt' => 'What is your name?']);

        $this->assertSent('POST', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['title' => 'test 123', 'cost' => 100, 'prompt' => 'What is your name?']);
    }

    public function testShouldCreateCustomRewardWithMultipleOpts(): void
    {
        $this->api()->createCustomReward(self::TOKEN, '123', 'test 123', 100, ['prompt' => 'What is your name?', 'is_enabled' => 1]);

        $this->assertSent('POST', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['title' => 'test 123', 'cost' => 100, 'prompt' => 'What is your name?', 'is_enabled' => 1]);
    }

    public function testShouldUpdateCustomReward(): void
    {
        $this->api()->updateCustomReward(self::TOKEN, '123', '321');

        $this->assertSent('PATCH', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
        ]);
    }

    public function testShouldUpdateCustomRewardWithOneOpt(): void
    {
        $this->api()->updateCustomReward(self::TOKEN, '123', '321', ['prompt' => 'What is your name?']);

        $this->assertSent('PATCH', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
        ]);
        $this->assertSentBody(['prompt' => 'What is your name?']);
    }

    public function testShouldUpdateCustomRewardWithMultipleOpts(): void
    {
        $this->api()->updateCustomReward(self::TOKEN, '123', '321', ['prompt' => 'What is your name?', 'is_enabled' => 1]);

        $this->assertSent('PATCH', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
        ]);
        $this->assertSentBody(['prompt' => 'What is your name?', 'is_enabled' => 1]);
    }

    public function testShouldDeleteCustomReward(): void
    {
        $this->api()->deleteCustomReward(self::TOKEN, '123', '321');

        $this->assertSent('DELETE', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', '321'],
        ]);
    }

    public function testShouldUpdateRedemptionStatus(): void
    {
        $this->api()->updateRedemptionStatus(self::TOKEN, '123', '456', '789', 'FULFILLED');

        $this->assertSent('PATCH', 'channel_points/custom_rewards/redemptions', [
            ['broadcaster_id', '123'],
            ['reward_id', '456'],
            ['id', '789'],
        ]);
        $this->assertSentBody(['status' => 'FULFILLED']);
    }

    public function testShouldGetACustomRewardById(): void
    {
        $this->api()->getCustomRewardById(self::TOKEN, '123', 'reward');

        $this->assertSent('GET', 'channel_points/custom_rewards', [
            ['broadcaster_id', '123'],
            ['id', 'reward'],
        ]);
    }
}
