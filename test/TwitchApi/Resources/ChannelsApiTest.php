<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ChannelsApi;
use TwitchApi\Tests\ResourceTestCase;

class ChannelsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ChannelsApi::class;
    }

    public function testShouldGetChannelInfo(): void
    {
        $this->api()->getChannelInfo(self::TOKEN, '123');

        $this->assertSent('GET', 'channels', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetChannelEditors(): void
    {
        $this->api()->getChannelEditors(self::TOKEN, '123');

        $this->assertSent('GET', 'channels/editors', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldModifyChannelWithGameId(): void
    {
        $this->api()->modifyChannelInfo(self::TOKEN, '123', ['game_id' => '0']);

        $this->assertSent('PATCH', 'channels', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['game_id' => '0']);
    }

    public function testShouldModifyChannelWithLanguage(): void
    {
        $this->api()->modifyChannelInfo(self::TOKEN, '123', ['broadcaster_language' => 'en']);

        $this->assertSent('PATCH', 'channels', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['broadcaster_language' => 'en']);
    }

    public function testShouldModifyChannelWithTitle(): void
    {
        $this->api()->modifyChannelInfo(self::TOKEN, '123', ['title' => 'test 123']);

        $this->assertSent('PATCH', 'channels', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['title' => 'test 123']);
    }

    public function testShouldModifyChannelWithDelay(): void
    {
        $this->api()->modifyChannelInfo(self::TOKEN, '123', ['delay' => 5]);

        $this->assertSent('PATCH', 'channels', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['delay' => 5]);
    }

    public function testShouldModifyChannelWithOpts(): void
    {
        $this->api()->modifyChannelInfo(self::TOKEN, '123', ['game_id' => '0', 'broadcaster_language' => 'en', 'title' => 'test 123', 'delay' => 5]);

        $this->assertSent('PATCH', 'channels', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['game_id' => '0', 'broadcaster_language' => 'en', 'title' => 'test 123', 'delay' => 5]);
    }

    public function testShouldGetFollowedChannels(): void
    {
        $this->api()->getFollowedChannels(self::TOKEN, '123');

        $this->assertSent('GET', 'channels/followed', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetFollowedChannelsWithOpts(): void
    {
        $this->api()->getFollowedChannels(self::TOKEN, '123', '456', 100, 'abc');

        $this->assertSent('GET', 'channels/followed', [
            ['user_id', '123'],
            ['broadcaster_id', '456'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetChannelFollowers(): void
    {
        $this->api()->getChannelFollowers(self::TOKEN, '123');

        $this->assertSent('GET', 'channels/followers', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetChannelFollowersWithOpts(): void
    {
        $this->api()->getChannelFollowers(self::TOKEN, '123', '456', 100, 'abc');

        $this->assertSent('GET', 'channels/followers', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }
}
