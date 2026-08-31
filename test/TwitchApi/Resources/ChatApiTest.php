<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ChatApi;
use TwitchApi\Tests\ResourceTestCase;

class ChatApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ChatApi::class;
    }

    public function testShouldGetChannelEmotes(): void
    {
        $this->api()->getChannelEmotes(self::TOKEN, '123');

        $this->assertSent('GET', 'chat/emotes', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetGlobalEmotes(): void
    {
        $this->api()->getGlobalEmotes(self::TOKEN);

        $this->assertSent('GET', 'chat/emotes/global');
    }

    public function testShouldGetOneEmoteSet(): void
    {
        $this->api()->getEmoteSets(self::TOKEN, ['123']);

        $this->assertSent('GET', 'chat/emotes/set', [
            ['emote_set_id', '123'],
        ]);
    }

    public function testShouldGetOneEmoteSetWithHelperFunction(): void
    {
        $this->api()->getEmoteSet(self::TOKEN, '123');

        $this->assertSent('GET', 'chat/emotes/set', [
            ['emote_set_id', '123'],
        ]);
    }

    public function testShouldGetMultipleEmoteSets(): void
    {
        $this->api()->getEmoteSets(self::TOKEN, ['123', '456']);

        $this->assertSent('GET', 'chat/emotes/set', [
            ['emote_set_id', '123'],
            ['emote_set_id', '456'],
        ]);
    }

    public function testShouldGetChannelChatBadges(): void
    {
        $this->api()->getChannelChatBadges(self::TOKEN, '123');

        $this->assertSent('GET', 'chat/badges', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetGlobalChatBadges(): void
    {
        $this->api()->getGlobalChatBadges(self::TOKEN);

        $this->assertSent('GET', 'chat/badges/global');
    }

    public function testShouldGetChatSettings(): void
    {
        $this->api()->getChatSettings(self::TOKEN, '123');

        $this->assertSent('GET', 'chat/settings', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetChatSettingsWithModeratorId(): void
    {
        $this->api()->getChatSettings(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'chat/settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldUpdateChatSettingsWithOneSetting(): void
    {
        $this->api()->updateChatSettings(self::TOKEN, '123', '456', ['emote_mode' => true]);

        $this->assertSent('PATCH', 'chat/settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['emote_mode' => true]);
    }

    public function testShouldUpdateChatSettingsWithMultipleSettings(): void
    {
        $this->api()->updateChatSettings(self::TOKEN, '123', '456', ['emote_mode' => true, 'slow_mode_wait_time' => 10]);

        $this->assertSent('PATCH', 'chat/settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['emote_mode' => true, 'slow_mode_wait_time' => 10]);
    }

    public function testShouldSendAChatAnnouncement(): void
    {
        $this->api()->sendChatAnnouncement(self::TOKEN, '123', '456', 'Hello World');

        $this->assertSent('POST', 'chat/announcements', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['message' => 'Hello World']);
    }

    public function testShouldSendAChatAnnouncementWithAColor(): void
    {
        $this->api()->sendChatAnnouncement(self::TOKEN, '123', '456', 'Hello World', 'red');

        $this->assertSent('POST', 'chat/announcements', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['message' => 'Hello World', 'color' => 'red']);
    }

    public function testShouldGetAUsersChatColor(): void
    {
        $this->api()->getUserChatColor(self::TOKEN, '123');

        $this->assertSent('GET', 'chat/color', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldUpdateAUsersChatColor(): void
    {
        $this->api()->updateUserChatColor(self::TOKEN, '123', 'red');

        $this->assertSent('PUT', 'chat/color', [
            ['user_id', '123'],
            ['color', 'red'],
        ]);
    }

    public function testShouldGetChatters(): void
    {
        $this->api()->getChatters(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'chat/chatters', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldGetChattersWithOpts(): void
    {
        $this->api()->getChatters(self::TOKEN, '123', '456', 100, 'abc');

        $this->assertSent('GET', 'chat/chatters', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldSendAShoutout(): void
    {
        $this->api()->sendShoutout(self::TOKEN, '123', '456', '789');

        $this->assertSent('POST', 'chat/shoutouts', [
            ['from_broadcaster_id', '123'],
            ['to_broadcaster_id', '456'],
            ['moderator_id', '789'],
        ]);
    }

    public function testShouldSendAChatMessage(): void
    {
        $this->api()->sendChatMessage(self::TOKEN, '123', '456', 'hello');

        $this->assertSent('POST', 'chat/messages');
        $this->assertSentBody(['broadcaster_id' => '123', 'sender_id' => '456', 'message' => 'hello']);
    }

    public function testShouldSendAChatMessageAsAReply(): void
    {
        $this->api()->sendChatMessage(self::TOKEN, '123', '456', 'hello', 'msg');

        $this->assertSent('POST', 'chat/messages');
        $this->assertSentBody(['broadcaster_id' => '123', 'sender_id' => '456', 'message' => 'hello', 'reply_parent_message_id' => 'msg']);
    }

    public function testShouldGetUserEmotes(): void
    {
        $this->api()->getUserEmotes(self::TOKEN, '456');

        $this->assertSent('GET', 'chat/emotes/user', [
            ['user_id', '456'],
        ]);
    }

    public function testShouldGetThePinnedChatMessage(): void
    {
        $this->api()->getPinnedChatMessage(self::TOKEN, '123');

        $this->assertSent('GET', 'chat/pins', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldPinAChatMessage(): void
    {
        $this->api()->pinChatMessage(self::TOKEN, '123', 'msg', 60);

        $this->assertSent('PUT', 'chat/pins', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['message_id' => 'msg', 'duration_seconds' => 60]);
    }

    public function testShouldUpdateAPinnedChatMessage(): void
    {
        $this->api()->updatePinnedChatMessage(self::TOKEN, '123', 'pin', 120);

        $this->assertSent('PATCH', 'chat/pins', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['pinned_message_id' => 'pin', 'duration_seconds' => 120]);
    }

    public function testShouldUnpinAChatMessage(): void
    {
        $this->api()->unpinChatMessage(self::TOKEN, '123', 'pin');

        $this->assertSent('DELETE', 'chat/pins', [
            ['broadcaster_id', '123'],
            ['pinned_message_id', 'pin'],
        ]);
    }

    public function testShouldSendAChatMessageToTheSourceChannelOnly(): void
    {
        $this->api()->sendChatMessage(self::TOKEN, '123', '456', 'hello', null, 'true');

        $this->assertSent('POST', 'chat/messages');
        $this->assertSentBody(['broadcaster_id' => '123', 'sender_id' => '456', 'message' => 'hello', 'for_source_only' => 'true']);
    }

    public function testShouldGetUserEmotesForABroadcasterWithPaging(): void
    {
        $this->api()->getUserEmotes(self::TOKEN, '456', '123', 'cursor');

        $this->assertSent('GET', 'chat/emotes/user', [
            ['user_id', '456'],
            ['broadcaster_id', '123'],
            ['after', 'cursor'],
        ]);
    }
}
