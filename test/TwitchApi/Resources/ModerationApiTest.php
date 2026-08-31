<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ModerationApi;
use TwitchApi\Tests\ResourceTestCase;

class ModerationApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ModerationApi::class;
    }

    public function testShouldCheckAutomodStatus(): void
    {
        $this->api()->checkAutoModStatus(self::TOKEN, '123', '456', 'test 123');

        $this->assertSent('POST', 'moderation/enforcements/status', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['msg_id' => '456', 'msg_text' => 'test 123']);
    }

    public function testShouldReleaseHeldMessage(): void
    {
        $this->api()->manageHeldAutoModMessage(self::TOKEN, '123', '456', 'ALLOW');

        $this->assertSent('POST', 'moderation/automod/message');
        $this->assertSentBody(['user_id' => '123', 'msg_id' => '456', 'action' => 'ALLOW']);
    }

    public function testShouldBanAUser(): void
    {
        $this->api()->banUser(self::TOKEN, '123', '456', '789', 'abc');

        $this->assertSent('POST', 'moderation/bans', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['user_id' => '789', 'reason' => 'abc']);
    }

    public function testShouldBanAUserWithADuration(): void
    {
        $this->api()->banUser(self::TOKEN, '123', '456', '789', 'abc', 300);

        $this->assertSent('POST', 'moderation/bans', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['user_id' => '789', 'reason' => 'abc', 'duration' => 300]);
    }

    public function testShouldUnbanAUser(): void
    {
        $this->api()->unbanUser(self::TOKEN, '123', '456', '789');

        $this->assertSent('DELETE', 'moderation/bans', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['user_id', '789'],
        ]);
    }

    public function testShouldGetAutomodSettings(): void
    {
        $this->api()->getAutoModSettings(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'moderation/automod/settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldUpdateAutomodSettingsWithOneSetting(): void
    {
        $this->api()->updateAutoModSettings(self::TOKEN, '123', '456', ['aggression' => 1]);

        $this->assertSent('PUT', 'moderation/automod/settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['aggression' => 1]);
    }

    public function testShouldUpdateAutomodSettingsWithMultipleSettings(): void
    {
        $this->api()->updateAutoModSettings(self::TOKEN, '123', '456', ['aggression' => 1, 'bullying' => 2]);

        $this->assertSent('PUT', 'moderation/automod/settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['aggression' => 1, 'bullying' => 2]);
    }

    public function testShouldGetBlockedTerms(): void
    {
        $this->api()->getBlockedTerms(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'moderation/blocked_terms', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldGetBlockedTermsWithOpts(): void
    {
        $this->api()->getBlockedTerms(self::TOKEN, '123', '456', 100, 'abc');

        $this->assertSent('GET', 'moderation/blocked_terms', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldAddABlockedTerm(): void
    {
        $this->api()->addBlockedTerm(self::TOKEN, '123', '456', 'abc');

        $this->assertSent('POST', 'moderation/blocked_terms', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['term' => 'abc']);
    }

    public function testShouldRemoveABlockedTerm(): void
    {
        $this->api()->removeBlockedTerm(self::TOKEN, '123', '456', '789');

        $this->assertSent('DELETE', 'moderation/blocked_terms', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['id', '789'],
        ]);
    }

    public function testShouldDeleteChatMessages(): void
    {
        $this->api()->deleteChatMessages(self::TOKEN, '123', '456');

        $this->assertSent('DELETE', 'moderation/chat', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldDeleteChatMessagesWithMessageId(): void
    {
        $this->api()->deleteChatMessages(self::TOKEN, '123', '456', '789');

        $this->assertSent('DELETE', 'moderation/chat', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['message_id', '789'],
        ]);
    }

    public function testShouldAddAChannelModerator(): void
    {
        $this->api()->addChannelModerator(self::TOKEN, '123', '456');

        $this->assertSent('POST', 'moderation/moderators', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
        ]);
    }

    public function testShouldRemoveAChannelModerator(): void
    {
        $this->api()->removeChannelModerator(self::TOKEN, '123', '456');

        $this->assertSent('DELETE', 'moderation/moderators', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
        ]);
    }

    public function testShouldGetVipsForAChannel(): void
    {
        $this->api()->getVips(self::TOKEN, '123');

        $this->assertSent('GET', 'channels/vips', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetVipsForAChannelWithOpts(): void
    {
        $this->api()->getVips(self::TOKEN, '123', [], 100, 'abc');

        $this->assertSent('GET', 'channels/vips', [
            ['broadcaster_id', '123'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetVipsForAChannelWithOneId(): void
    {
        $this->api()->getVips(self::TOKEN, '123', ['456']);

        $this->assertSent('GET', 'channels/vips', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
        ]);
    }

    public function testShouldGetVipsForAChannelWithMultipeIds(): void
    {
        $this->api()->getVips(self::TOKEN, '123', ['456', '789']);

        $this->assertSent('GET', 'channels/vips', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
            ['user_id', '789'],
        ]);
    }

    public function testShouldAddVipForAChannel(): void
    {
        $this->api()->addChannelVip(self::TOKEN, '123', '456');

        $this->assertSent('POST', 'channels/vips', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
        ]);
    }

    public function testShouldRemoveVipForAChannel(): void
    {
        $this->api()->removeChannelVip(self::TOKEN, '123', '456');

        $this->assertSent('DELETE', 'channels/vips', [
            ['broadcaster_id', '123'],
            ['user_id', '456'],
        ]);
    }

    public function testShouldGetBannedUsers(): void
    {
        $this->api()->getBannedUsers(self::TOKEN, '123');

        $this->assertSent('GET', 'moderation/banned', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetBannedUsersWithOpts(): void
    {
        $this->api()->getBannedUsers(self::TOKEN, '123', ['abc', 'def'], 'abc', 'def', '100');

        $this->assertSent('GET', 'moderation/banned', [
            ['broadcaster_id', '123'],
            ['user_id', 'abc'],
            ['user_id', 'def'],
            ['before', 'abc'],
            ['after', 'def'],
            ['first', '100'],
        ]);
    }

    public function testShouldGetModerators(): void
    {
        $this->api()->getModerators(self::TOKEN, '123');

        $this->assertSent('GET', 'moderation/moderators', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetModeratorsWithOpts(): void
    {
        $this->api()->getModerators(self::TOKEN, '123', ['abc', 'def'], 'abc', '100');

        $this->assertSent('GET', 'moderation/moderators', [
            ['broadcaster_id', '123'],
            ['user_id', 'abc'],
            ['user_id', 'def'],
            ['after', 'abc'],
            ['first', '100'],
        ]);
    }

    public function testShouldGetShieldModeStatus(): void
    {
        $this->api()->getShieldModeStatus(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'moderation/shield_mode', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldUpdateShieldModeStatus(): void
    {
        $this->api()->updateShieldModeStatus(self::TOKEN, '123', '456', true);

        $this->assertSent('PUT', 'moderation/shield_mode', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['is_active' => true]);
    }

    public function testShouldGetModeratedChannels(): void
    {
        $this->api()->getModeratedChannels(self::TOKEN, '456');

        $this->assertSent('GET', 'moderation/channels', [
            ['user_id', '456'],
        ]);
    }

    public function testShouldGetUnbanRequests(): void
    {
        $this->api()->getUnbanRequests(self::TOKEN, '123', '456', 'pending');

        $this->assertSent('GET', 'moderation/unban_requests', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['status', 'pending'],
        ]);
    }

    public function testShouldResolveAnUnbanRequest(): void
    {
        $this->api()->resolveUnbanRequest(self::TOKEN, '123', '456', 'req', 'approved');

        $this->assertSent('PATCH', 'moderation/unban_requests', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['unban_request_id', 'req'],
            ['status', 'approved'],
        ]);
    }

    public function testShouldWarnAChatUser(): void
    {
        $this->api()->warnChatUser(self::TOKEN, '123', '456', '789', 'be nice');

        $this->assertSent('POST', 'moderation/warnings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
        $this->assertSentBody(['data' => ['user_id' => '789', 'reason' => 'be nice']]);
    }

    public function testShouldAddASuspiciousUser(): void
    {
        $this->api()->addSuspiciousUser(self::TOKEN, '123', '456', '789', 'restricted');

        $this->assertSent('POST', 'moderation/suspicious_users', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['user_id', '789'],
            ['status', 'restricted'],
        ]);
    }

    public function testShouldRemoveASuspiciousUser(): void
    {
        $this->api()->removeSuspiciousUser(self::TOKEN, '123', '456', '789');

        $this->assertSent('DELETE', 'moderation/suspicious_users', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['user_id', '789'],
        ]);
    }

    public function testShouldGetModeratedChannelsWithPaging(): void
    {
        $this->api()->getModeratedChannels(self::TOKEN, '456', 20, 'cursor');

        $this->assertSent('GET', 'moderation/channels', [
            ['user_id', '456'],
            ['first', '20'],
            ['after', 'cursor'],
        ]);
    }

    public function testShouldGetUnbanRequestsForOneUserWithPaging(): void
    {
        $this->api()->getUnbanRequests(self::TOKEN, '123', '456', 'pending', '789', 'cursor', 20);

        $this->assertSent('GET', 'moderation/unban_requests', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['status', 'pending'],
            ['user_id', '789'],
            ['after', 'cursor'],
            ['first', '20'],
        ]);
    }

    public function testShouldResolveAnUnbanRequestWithResolutionText(): void
    {
        $this->api()->resolveUnbanRequest(self::TOKEN, '123', '456', 'req', 'denied', 'no');

        $this->assertSent('PATCH', 'moderation/unban_requests', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['unban_request_id', 'req'],
            ['status', 'denied'],
            ['resolution_text', 'no'],
        ]);
    }
}
