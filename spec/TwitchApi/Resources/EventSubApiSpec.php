<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class EventSubApiSpec extends ObjectBehavior
{
    private string $bearer = 'TEST_TOKEN';
    private string $secret = 'SECRET';
    private string $callback = 'https://example.com/';

    private function createEventSubSubscription(string $type, string $version, array $condition, RequestGenerator $requestGenerator, bool $isBatchingEnabled = null)
    {
        $bodyParams = [];

        $bodyParams[] = ['key' => 'type', 'value' => $type];
        $bodyParams[] = ['key' => 'version', 'value' => $version];
        $bodyParams[] = ['key' => 'condition', 'value' => $condition];
        $bodyParams[] = [
            'key' => 'transport',
            'value' => [
                'method' => 'webhook',
                'callback' => $this->callback,
                'secret' => $this->secret,
            ],
        ];

        if (null !== $isBatchingEnabled) {
            $bodyParams[] = ['key' => 'is_batching_enabled', 'value' => $isBatchingEnabled];
        }

        return $requestGenerator->generate('POST', 'eventsub/subscriptions', $this->bearer, [], $bodyParams);
    }

    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_get_event_sub_subscription(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/subscriptions', 'TEST_TOKEN', [], [])->willReturn($request);
        $this->getEventSubSubscription('TEST_TOKEN')->shouldBe($response);
    }

    function it_should_get_event_sub_subscription_with_status(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/subscriptions', 'TEST_TOKEN', [['key' => 'status', 'value' => 'enabled']], [])->willReturn($request);
        $this->getEventSubSubscription('TEST_TOKEN', 'enabled')->shouldBe($response);
    }

    function it_should_get_event_sub_subscription_with_type(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/subscriptions', 'TEST_TOKEN', [['key' => 'type', 'value' => 'channel.update']], [])->willReturn($request);
        $this->getEventSubSubscription('TEST_TOKEN', null, 'channel.update')->shouldBe($response);
    }

    function it_should_get_event_sub_subscription_with_user_id(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/subscriptions', 'TEST_TOKEN', [['key' => 'user_id', 'value' => '789']], [])->willReturn($request);
        $this->getEventSubSubscription('TEST_TOKEN', null, null, null, '789')->shouldBe($response);
    }

    function it_should_get_event_sub_subscription_with_after(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/subscriptions', 'TEST_TOKEN', [['key' => 'after', 'value' => 'abc']], [])->willReturn($request);
        $this->getEventSubSubscription('TEST_TOKEN', null, null, 'abc')->shouldBe($response);
    }

    function it_should_delete_event_sub_subscription(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('DELETE', 'eventsub/subscriptions', 'TEST_TOKEN', [['key' => 'id', 'value' => '123']], [])->willReturn($request);
        $this->deleteEventSubSubscription('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.update', '2', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelUpdate($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_follow(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.follow', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelFollow($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_subscribe(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.subscribe', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSubscribe($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_subscription_end(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.subscription.end', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSubscriptionEnd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_subscription_gift(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.subscription.gift', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSubscriptionGift($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_subscription_message(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.subscription.message', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSubscriptionMessage($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_cheer(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.cheer', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelCheer($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_raid(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.raid', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelRaid($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_ban(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.ban', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelBan($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_unban(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.unban', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelUnban($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_moderator_add(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.moderator.add', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelModeratorAdd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_moderator_remove(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.moderator.remove', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelModeratorRemove($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_points_custom_reward_add(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.channel_points_custom_reward.add', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPointsCustomRewardAdd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_points_custom_reward_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.channel_points_custom_reward.update', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPointsCustomRewardUpdate($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_points_custom_reward_remove(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.channel_points_custom_reward.remove', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPointsCustomRewardRemove($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_points_custom_reward_redemption_add(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.channel_points_custom_reward_redemption.add', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPointsCustomRewardRedemptionAdd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_points_custom_reward_redemption_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.channel_points_custom_reward_redemption.update', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPointsCustomRewardRedemptionUpdate($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_poll_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.poll.begin', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPollBegin($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_poll_progress(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.poll.progress', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPollProgress($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_poll_endn(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.poll.end', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPollEnd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_prediction_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.prediction.begin', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPredictionBegin($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_prediction_progress(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.prediction.progress', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPredictionProgress($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_prediction_lock(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.prediction.lock', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPredictionLock($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_prediction_endn(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.prediction.end', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPredictionEnd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_hype_train_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.hype_train.begin', '2', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelHypeTrainBegin($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_hype_train_progress(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.hype_train.progress', '2', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelHypeTrainProgress($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_hype_train_end(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.hype_train.end', '2', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelHypeTrainEnd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_stream_online(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('stream.online', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToStreamOnline($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_stream_offline(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('stream.offline', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToStreamOffline($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_user_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('user.update', '1', ['user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToUserUpdate($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_extension_bits_transaction_create(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('extension.bits_transaction.create', '1', ['extension_client_id' => 'deadbeef'], $requestGenerator)->willReturn($request);
        $this->subscribeToExtensionBitsTransactionCreate($this->bearer, $this->secret, $this->callback, 'deadbeef')->shouldBe($response);
    }

    function it_should_subscribe_to_user_authorization_revoke(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('user.authorization.revoke', '1', ['client_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToUserAuthorizationRevoke($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_user_authorization_grant(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('user.authorization.grant', '1', ['client_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToUserAuthorizationGrant($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_goal_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.goal.begin', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelGoalBegin($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_goal_progress(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.goal.progress', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelGoalProgress($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_goal_end(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.goal.end', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelGoalEnd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_drop_entitelement_grant(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('drop.entitlement.grant', '1', ['organization_id' => '12345'], $requestGenerator, true)->willReturn($request);
        $this->subscribeToDropEntitlementGrant($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_drop_entitelement_grant_with_opts(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('drop.entitlement.grant', '1', ['organization_id' => '123', 'category_id' => '456', 'campaign_id' => '789'], $requestGenerator, true)->willReturn($request);
        $this->subscribeToDropEntitlementGrant($this->bearer, $this->secret, $this->callback, '123', '456', '789')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_charity_campaign_start(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.charity_campaign.start', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelCharityCampaignStart($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_charity_campaign_progress(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.charity_campaign.progress', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelCharityCampaignProgress($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_charity_campaign_stop(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.charity_campaign.stop', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelCharityCampaignStop($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_charity_campaign_donate(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.charity_campaign.donate', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelCharityCampaignDonate($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_shield_mode_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.shield_mode.begin', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelShieldModeBegin($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_shield_mode_end(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.shield_mode.end', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelShieldModeEnd($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_shoutout_create(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.shoutout.create', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelShoutoutCreate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_shoutout_receive(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.shoutout.receive', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelShoutoutReceive($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_automod_message_hold(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('automod.message.hold', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToAutomodMessageHold($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_automod_message_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('automod.message.update', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToAutomodMessageUpdate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_automod_settings_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('automod.settings.update', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToAutomodSettingsUpdate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_automod_terms_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('automod.terms.update', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToAutomodTermsUpdate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_ad_break_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.ad_break.begin', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelAdBreakBegin($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_bits_use(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.bits.use', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelBitsUse($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_points_automatic_reward_redemption_add(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.channel_points_automatic_reward_redemption.add', '2', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelPointsAutomaticRewardRedemptionAdd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_clear(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat.clear', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatClear($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_clear_user_messages(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat.clear_user_messages', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatClearUserMessages($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_message(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat.message', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatMessage($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_message_delete(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat.message_delete', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatMessageDelete($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_notification(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat.notification', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatNotification($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_user_message_hold(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat.user_message_hold', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatUserMessageHold($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_user_message_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat.user_message_update', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatUserMessageUpdate($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_chat_settings_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.chat_settings.update', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelChatSettingsUpdate($this->bearer, $this->secret, $this->callback, '12345', '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_custom_power_up_redemption_add(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.custom_power_up_redemption.add', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelCustomPowerUpRedemptionAdd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_guest_star_guest_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.guest_star_guest.update', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelGuestStarGuestUpdate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_guest_star_session_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.guest_star_session.begin', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelGuestStarSessionBegin($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_guest_star_session_end(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.guest_star_session.end', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelGuestStarSessionEnd($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_guest_star_settings_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.guest_star_settings.update', 'beta', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelGuestStarSettingsUpdate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_moderate(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.moderate', '2', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelModerate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_shared_chat_begin(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.shared_chat.begin', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSharedChatBegin($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_shared_chat_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.shared_chat.update', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSharedChatUpdate($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_shared_chat_end(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.shared_chat.end', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSharedChatEnd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_suspicious_user_message(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.suspicious_user.message', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSuspiciousUserMessage($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_suspicious_user_update(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.suspicious_user.update', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelSuspiciousUserUpdate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_unban_request_create(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.unban_request.create', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelUnbanRequestCreate($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_unban_request_resolve(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.unban_request.resolve', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelUnbanRequestResolve($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_vip_add(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.vip.add', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelVipAdd($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_vip_remove(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.vip.remove', '1', ['broadcaster_user_id' => '12345'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelVipRemove($this->bearer, $this->secret, $this->callback, '12345')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_warning_acknowledge(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.warning.acknowledge', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelWarningAcknowledge($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_channel_warning_send(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('channel.warning.send', '1', ['broadcaster_user_id' => '12345', 'moderator_user_id' => '54321'], $requestGenerator)->willReturn($request);
        $this->subscribeToChannelWarningSend($this->bearer, $this->secret, $this->callback, '12345', '54321')->shouldBe($response);
    }

    function it_should_subscribe_to_user_whisper_message(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('user.whisper.message', '1', ['user_id' => '99999'], $requestGenerator)->willReturn($request);
        $this->subscribeToUserWhisperMessage($this->bearer, $this->secret, $this->callback, '99999')->shouldBe($response);
    }

    function it_should_subscribe_to_conduit_shard_disabled(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('conduit.shard.disabled', '1', ['client_id' => 'CLIENT'], $requestGenerator)->willReturn($request);
        $this->subscribeToConduitShardDisabled($this->bearer, $this->secret, $this->callback, 'CLIENT')->shouldBe($response);
    }

    function it_should_subscribe_to_conduit_shard_disabled_for_one_conduit(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->createEventSubSubscription('conduit.shard.disabled', '1', ['client_id' => 'CLIENT', 'conduit_id' => 'CONDUIT'], $requestGenerator)->willReturn($request);
        $this->subscribeToConduitShardDisabled($this->bearer, $this->secret, $this->callback, 'CLIENT', 'CONDUIT')->shouldBe($response);
    }

    function it_should_create_a_subscription_over_websocket(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $bodyParams = [];
        $bodyParams[] = ['key' => 'type', 'value' => 'channel.chat.message'];
        $bodyParams[] = ['key' => 'version', 'value' => '1'];
        $bodyParams[] = ['key' => 'condition', 'value' => ['broadcaster_user_id' => '12345', 'user_id' => '99999']];
        $bodyParams[] = ['key' => 'transport', 'value' => ['method' => 'websocket', 'session_id' => 'SESSION']];

        $requestGenerator->generate('POST', 'eventsub/subscriptions', $this->bearer, [], $bodyParams)->willReturn($request);
        $this->createEventSubSubscriptionViaWebSocket($this->bearer, 'SESSION', 'channel.chat.message', '1', ['broadcaster_user_id' => '12345', 'user_id' => '99999'])->shouldBe($response);
    }

    function it_should_create_a_subscription_over_a_conduit(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $bodyParams = [];
        $bodyParams[] = ['key' => 'type', 'value' => 'stream.online'];
        $bodyParams[] = ['key' => 'version', 'value' => '1'];
        $bodyParams[] = ['key' => 'condition', 'value' => ['broadcaster_user_id' => '12345']];
        $bodyParams[] = ['key' => 'transport', 'value' => ['method' => 'conduit', 'conduit_id' => 'CONDUIT']];

        $requestGenerator->generate('POST', 'eventsub/subscriptions', $this->bearer, [], $bodyParams)->willReturn($request);
        $this->createEventSubSubscriptionViaConduit($this->bearer, 'CONDUIT', 'stream.online', '1', ['broadcaster_user_id' => '12345'])->shouldBe($response);
    }

    function it_should_pass_batching_through_on_a_conduit_subscription(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $bodyParams = [];
        $bodyParams[] = ['key' => 'type', 'value' => 'drop.entitlement.grant'];
        $bodyParams[] = ['key' => 'version', 'value' => '1'];
        $bodyParams[] = ['key' => 'condition', 'value' => ['organization_id' => 'ORG']];
        $bodyParams[] = ['key' => 'transport', 'value' => ['method' => 'conduit', 'conduit_id' => 'CONDUIT']];
        $bodyParams[] = ['key' => 'is_batching_enabled', 'value' => true];

        $requestGenerator->generate('POST', 'eventsub/subscriptions', $this->bearer, [], $bodyParams)->willReturn($request);
        $this->createEventSubSubscriptionViaConduit($this->bearer, 'CONDUIT', 'drop.entitlement.grant', '1', ['organization_id' => 'ORG'], true)->shouldBe($response);
    }
}
