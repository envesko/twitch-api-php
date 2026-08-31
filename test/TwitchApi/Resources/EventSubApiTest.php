<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\EventSubApi;
use TwitchApi\Tests\ResourceTestCase;

/**
 * Every subscription helper, asserted by the request it actually sends rather than by the
 * arguments it hands to the request builder. That is what catches a wrong subscription type,
 * a stale version, or a condition key Twitch does not accept.
 */
class EventSubApiTest extends ResourceTestCase
{
    private const SECRET = 'SECRET';
    private const CALLBACK = 'https://example.com/';

    protected function resourceClass(): string
    {
        return EventSubApi::class;
    }

    /**
     * @param array<string, mixed> $condition
     */
    private function assertSubscribed(string $type, string $version, array $condition, ?bool $batching = null): void
    {
        $this->assertSent('POST', 'eventsub/subscriptions');

        $expected = [
            'type' => $type,
            'version' => $version,
            'condition' => $condition,
            'transport' => [
                'method' => 'webhook',
                'callback' => self::CALLBACK,
                'secret' => self::SECRET,
            ],
        ];

        if ($batching !== null) {
            $expected['is_batching_enabled'] = $batching;
        }

        $this->assertSentBody($expected);
    }

    public function testShouldGetEventSubSubscription(): void
    {
        $this->api()->getEventSubSubscription(self::TOKEN);

        // Hand-check this one against the spec it replaced.
        $this->assertSent('GET', 'eventsub/subscriptions');
    }

    public function testShouldGetEventSubSubscriptionWithStatus(): void
    {
        $this->api()->getEventSubSubscription(self::TOKEN, 'enabled');

        $this->assertSent('GET', 'eventsub/subscriptions', [
            ['status', 'enabled'],
        ]);
    }

    public function testShouldGetEventSubSubscriptionWithType(): void
    {
        $this->api()->getEventSubSubscription(self::TOKEN, null, 'channel.update');

        $this->assertSent('GET', 'eventsub/subscriptions', [
            ['type', 'channel.update'],
        ]);
    }

    public function testShouldGetEventSubSubscriptionWithUserId(): void
    {
        $this->api()->getEventSubSubscription(self::TOKEN, null, null, null, '789');

        $this->assertSent('GET', 'eventsub/subscriptions', [
            ['user_id', '789'],
        ]);
    }

    public function testShouldGetEventSubSubscriptionWithAfter(): void
    {
        $this->api()->getEventSubSubscription(self::TOKEN, null, null, 'abc');

        $this->assertSent('GET', 'eventsub/subscriptions', [
            ['after', 'abc'],
        ]);
    }

    public function testShouldDeleteEventSubSubscription(): void
    {
        $this->api()->deleteEventSubSubscription(self::TOKEN, '123');

        $this->assertSent('DELETE', 'eventsub/subscriptions', [
            ['id', '123'],
        ]);
    }

    public function testShouldSubscribeToChannelUpdate(): void
    {
        $this->api()->subscribeToChannelUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.update', '2', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelFollow(): void
    {
        $this->api()->subscribeToChannelFollow(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.follow', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelSubscribe(): void
    {
        $this->api()->subscribeToChannelSubscribe(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.subscribe', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelSubscriptionEnd(): void
    {
        $this->api()->subscribeToChannelSubscriptionEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.subscription.end', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelSubscriptionGift(): void
    {
        $this->api()->subscribeToChannelSubscriptionGift(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.subscription.gift', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelSubscriptionMessage(): void
    {
        $this->api()->subscribeToChannelSubscriptionMessage(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.subscription.message', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelCheer(): void
    {
        $this->api()->subscribeToChannelCheer(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.cheer', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelRaid(): void
    {
        $this->api()->subscribeToChannelRaid(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.raid', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelBan(): void
    {
        $this->api()->subscribeToChannelBan(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.ban', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelUnban(): void
    {
        $this->api()->subscribeToChannelUnban(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.unban', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelModeratorAdd(): void
    {
        $this->api()->subscribeToChannelModeratorAdd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.moderator.add', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelModeratorRemove(): void
    {
        $this->api()->subscribeToChannelModeratorRemove(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.moderator.remove', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPointsCustomRewardAdd(): void
    {
        $this->api()->subscribeToChannelPointsCustomRewardAdd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.channel_points_custom_reward.add', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPointsCustomRewardUpdate(): void
    {
        $this->api()->subscribeToChannelPointsCustomRewardUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.channel_points_custom_reward.update', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPointsCustomRewardRemove(): void
    {
        $this->api()->subscribeToChannelPointsCustomRewardRemove(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.channel_points_custom_reward.remove', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPointsCustomRewardRedemptionAdd(): void
    {
        $this->api()->subscribeToChannelPointsCustomRewardRedemptionAdd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.channel_points_custom_reward_redemption.add', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPointsCustomRewardRedemptionUpdate(): void
    {
        $this->api()->subscribeToChannelPointsCustomRewardRedemptionUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.channel_points_custom_reward_redemption.update', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPollBegin(): void
    {
        $this->api()->subscribeToChannelPollBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.poll.begin', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPollProgress(): void
    {
        $this->api()->subscribeToChannelPollProgress(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.poll.progress', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPollEndn(): void
    {
        $this->api()->subscribeToChannelPollEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.poll.end', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPredictionBegin(): void
    {
        $this->api()->subscribeToChannelPredictionBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.prediction.begin', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPredictionProgress(): void
    {
        $this->api()->subscribeToChannelPredictionProgress(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.prediction.progress', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPredictionLock(): void
    {
        $this->api()->subscribeToChannelPredictionLock(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.prediction.lock', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPredictionEndn(): void
    {
        $this->api()->subscribeToChannelPredictionEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.prediction.end', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelHypeTrainBegin(): void
    {
        $this->api()->subscribeToChannelHypeTrainBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.hype_train.begin', '2', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelHypeTrainProgress(): void
    {
        $this->api()->subscribeToChannelHypeTrainProgress(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.hype_train.progress', '2', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelHypeTrainEnd(): void
    {
        $this->api()->subscribeToChannelHypeTrainEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.hype_train.end', '2', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToStreamOnline(): void
    {
        $this->api()->subscribeToStreamOnline(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('stream.online', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToStreamOffline(): void
    {
        $this->api()->subscribeToStreamOffline(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('stream.offline', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToUserUpdate(): void
    {
        $this->api()->subscribeToUserUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('user.update', '1', ['user_id' => '12345']);
    }

    public function testShouldSubscribeToExtensionBitsTransactionCreate(): void
    {
        $this->api()->subscribeToExtensionBitsTransactionCreate(self::TOKEN, self::SECRET, self::CALLBACK, 'deadbeef');

        $this->assertSubscribed('extension.bits_transaction.create', '1', ['extension_client_id' => 'deadbeef']);
    }

    public function testShouldSubscribeToUserAuthorizationRevoke(): void
    {
        $this->api()->subscribeToUserAuthorizationRevoke(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('user.authorization.revoke', '1', ['client_id' => '12345']);
    }

    public function testShouldSubscribeToUserAuthorizationGrant(): void
    {
        $this->api()->subscribeToUserAuthorizationGrant(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('user.authorization.grant', '1', ['client_id' => '12345']);
    }

    public function testShouldSubscribeToChannelGoalBegin(): void
    {
        $this->api()->subscribeToChannelGoalBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.goal.begin', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelGoalProgress(): void
    {
        $this->api()->subscribeToChannelGoalProgress(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.goal.progress', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelGoalEnd(): void
    {
        $this->api()->subscribeToChannelGoalEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.goal.end', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToDropEntitelementGrant(): void
    {
        $this->api()->subscribeToDropEntitlementGrant(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('drop.entitlement.grant', '1', ['organization_id' => '12345'], true);
    }

    public function testShouldSubscribeToDropEntitelementGrantWithOpts(): void
    {
        $this->api()->subscribeToDropEntitlementGrant(self::TOKEN, self::SECRET, self::CALLBACK, '123', '456', '789');

        $this->assertSubscribed('drop.entitlement.grant', '1', ['organization_id' => '123', 'category_id' => '456', 'campaign_id' => '789'], true);
    }

    public function testShouldSubscribeToChannelCharityCampaignStart(): void
    {
        $this->api()->subscribeToChannelCharityCampaignStart(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.charity_campaign.start', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelCharityCampaignProgress(): void
    {
        $this->api()->subscribeToChannelCharityCampaignProgress(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.charity_campaign.progress', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelCharityCampaignStop(): void
    {
        $this->api()->subscribeToChannelCharityCampaignStop(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.charity_campaign.stop', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelCharityCampaignDonate(): void
    {
        $this->api()->subscribeToChannelCharityCampaignDonate(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.charity_campaign.donate', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelShieldModeBegin(): void
    {
        $this->api()->subscribeToChannelShieldModeBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.shield_mode.begin', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelShieldModeEnd(): void
    {
        $this->api()->subscribeToChannelShieldModeEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.shield_mode.end', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelShoutoutCreate(): void
    {
        $this->api()->subscribeToChannelShoutoutCreate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.shoutout.create', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelShoutoutReceive(): void
    {
        $this->api()->subscribeToChannelShoutoutReceive(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.shoutout.receive', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToAutomodMessageHold(): void
    {
        $this->api()->subscribeToAutomodMessageHold(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('automod.message.hold', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToAutomodMessageUpdate(): void
    {
        $this->api()->subscribeToAutomodMessageUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('automod.message.update', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToAutomodSettingsUpdate(): void
    {
        $this->api()->subscribeToAutomodSettingsUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('automod.settings.update', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToAutomodTermsUpdate(): void
    {
        $this->api()->subscribeToAutomodTermsUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('automod.terms.update', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelAdBreakBegin(): void
    {
        $this->api()->subscribeToChannelAdBreakBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.ad_break.begin', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelBitsUse(): void
    {
        $this->api()->subscribeToChannelBitsUse(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.bits.use', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelPointsAutomaticRewardRedemptionAdd(): void
    {
        $this->api()->subscribeToChannelPointsAutomaticRewardRedemptionAdd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.channel_points_automatic_reward_redemption.add', '2', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelChatClear(): void
    {
        $this->api()->subscribeToChannelChatClear(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat.clear', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelChatClearUserMessages(): void
    {
        $this->api()->subscribeToChannelChatClearUserMessages(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat.clear_user_messages', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelChatMessage(): void
    {
        $this->api()->subscribeToChannelChatMessage(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat.message', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelChatMessageDelete(): void
    {
        $this->api()->subscribeToChannelChatMessageDelete(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat.message_delete', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelChatNotification(): void
    {
        $this->api()->subscribeToChannelChatNotification(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat.notification', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelChatUserMessageHold(): void
    {
        $this->api()->subscribeToChannelChatUserMessageHold(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat.user_message_hold', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelChatUserMessageUpdate(): void
    {
        $this->api()->subscribeToChannelChatUserMessageUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat.user_message_update', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelChatSettingsUpdate(): void
    {
        $this->api()->subscribeToChannelChatSettingsUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '99999');

        $this->assertSubscribed('channel.chat_settings.update', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);
    }

    public function testShouldSubscribeToChannelCustomPowerUpRedemptionAdd(): void
    {
        $this->api()->subscribeToChannelCustomPowerUpRedemptionAdd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.custom_power_up_redemption.add', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelGuestStarGuestUpdate(): void
    {
        $this->api()->subscribeToChannelGuestStarGuestUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.guest_star_guest.update', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelGuestStarSessionBegin(): void
    {
        $this->api()->subscribeToChannelGuestStarSessionBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.guest_star_session.begin', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelGuestStarSessionEnd(): void
    {
        $this->api()->subscribeToChannelGuestStarSessionEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.guest_star_session.end', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelGuestStarSettingsUpdate(): void
    {
        $this->api()->subscribeToChannelGuestStarSettingsUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.guest_star_settings.update', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelModerate(): void
    {
        $this->api()->subscribeToChannelModerate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.moderate', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelSharedChatBegin(): void
    {
        $this->api()->subscribeToChannelSharedChatBegin(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.shared_chat.begin', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelSharedChatUpdate(): void
    {
        $this->api()->subscribeToChannelSharedChatUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.shared_chat.update', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelSharedChatEnd(): void
    {
        $this->api()->subscribeToChannelSharedChatEnd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.shared_chat.end', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelSuspiciousUserMessage(): void
    {
        $this->api()->subscribeToChannelSuspiciousUserMessage(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.suspicious_user.message', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelSuspiciousUserUpdate(): void
    {
        $this->api()->subscribeToChannelSuspiciousUserUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.suspicious_user.update', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelUnbanRequestCreate(): void
    {
        $this->api()->subscribeToChannelUnbanRequestCreate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.unban_request.create', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelUnbanRequestResolve(): void
    {
        $this->api()->subscribeToChannelUnbanRequestResolve(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.unban_request.resolve', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelVipAdd(): void
    {
        $this->api()->subscribeToChannelVipAdd(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.vip.add', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelVipRemove(): void
    {
        $this->api()->subscribeToChannelVipRemove(self::TOKEN, self::SECRET, self::CALLBACK, '12345');

        $this->assertSubscribed('channel.vip.remove', '1', ['broadcaster_user_id' => '12345']);
    }

    public function testShouldSubscribeToChannelWarningAcknowledge(): void
    {
        $this->api()->subscribeToChannelWarningAcknowledge(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.warning.acknowledge', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToChannelWarningSend(): void
    {
        $this->api()->subscribeToChannelWarningSend(self::TOKEN, self::SECRET, self::CALLBACK, '12345', '54321');

        $this->assertSubscribed('channel.warning.send', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321']);
    }

    public function testShouldSubscribeToUserWhisperMessage(): void
    {
        $this->api()->subscribeToUserWhisperMessage(self::TOKEN, self::SECRET, self::CALLBACK, '99999');

        $this->assertSubscribed('user.whisper.message', '1', ['user_id' => '99999']);
    }

    public function testShouldSubscribeToConduitShardDisabled(): void
    {
        $this->api()->subscribeToConduitShardDisabled(self::TOKEN, self::SECRET, self::CALLBACK, 'CLIENT');

        $this->assertSubscribed('conduit.shard.disabled', '1', ['client_id' => 'CLIENT']);
    }

    public function testShouldSubscribeToConduitShardDisabledForOneConduit(): void
    {
        $this->api()->subscribeToConduitShardDisabled(self::TOKEN, self::SECRET, self::CALLBACK, 'CLIENT', 'CONDUIT');

        $this->assertSubscribed('conduit.shard.disabled', '1', ['client_id' => 'CLIENT', 'conduit_id' => 'CONDUIT']);
    }

    public function testShouldCreateASubscriptionOverWebsocket(): void
    {
        $this->api()->createEventSubSubscriptionViaWebSocket(self::TOKEN, 'SESSION', 'channel.chat.message', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999']);

        // Hand-check this one against the spec it replaced.
        $this->assertSent('POST', 'eventsub/subscriptions');
    }

    public function testShouldCreateASubscriptionOverAConduit(): void
    {
        $this->api()->createEventSubSubscriptionViaConduit(self::TOKEN, 'CONDUIT', 'stream.online', '1', ['broadcaster_user_id' => '12345']);

        // Hand-check this one against the spec it replaced.
        $this->assertSent('POST', 'eventsub/subscriptions');
    }

    public function testShouldPassBatchingThroughOnAConduitSubscription(): void
    {
        $this->api()->createEventSubSubscriptionViaConduit(self::TOKEN, 'CONDUIT', 'drop.entitlement.grant', '1', ['organization_id' => 'ORG'], true);

        // Hand-check this one against the spec it replaced.
        $this->assertSent('POST', 'eventsub/subscriptions');
    }

    public function testShouldSubscribeToASingleCustomReward(): void
    {
        $this->api()->subscribeToChannelPointsCustomRewardUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', 'REWARD');

        $this->assertSubscribed('channel.channel_points_custom_reward.update', '1', ['broadcaster_user_id' => '12345', 'reward_id' => 'REWARD']);
    }

    public function testShouldSubscribeToASingleCustomRewardRedemption(): void
    {
        $this->api()->subscribeToChannelPointsCustomRewardRedemptionUpdate(self::TOKEN, self::SECRET, self::CALLBACK, '12345', 'REWARD');

        $this->assertSubscribed('channel.channel_points_custom_reward_redemption.update', '1', ['broadcaster_user_id' => '12345', 'reward_id' => 'REWARD']);
    }

    public function testGetEventSubSubscriptionFiltersBySubscriptionId(): void
    {
        // Added by Twitch on 2025-05-09.
        $this->api()->getEventSubSubscription(self::TOKEN, null, null, null, null, 'sub-1');

        $this->assertSent('GET', 'eventsub/subscriptions', [
            ['subscription_id', 'sub-1'],
        ]);
    }

    public function testGetEventSubSubscriptionFiltersByConduitId(): void
    {
        // Added by Twitch on 2026-04-17.
        $this->api()->getEventSubSubscription(self::TOKEN, null, null, null, null, null, 'conduit-1');

        $this->assertSent('GET', 'eventsub/subscriptions', [
            ['conduit_id', 'conduit-1'],
        ]);
    }
}
