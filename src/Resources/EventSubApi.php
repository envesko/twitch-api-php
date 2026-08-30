<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class EventSubApi extends AbstractResource
{
    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference#get-eventsub-subscriptions
     */
    public function getEventSubSubscription(string $bearer, ?string $status = null, ?string $type = null, ?string $after = null, ?string $userId = null): ResponseInterface
    {
        $queryParamsMap = [];

        if ($status) {
            $queryParamsMap[] = ['key' => 'status', 'value' => $status];
        }

        if ($type) {
            $queryParamsMap[] = ['key' => 'type', 'value' => $type];
        }

        if ($after) {
            $queryParamsMap[] = ['key' => 'after', 'value' => $after];
        }

        if ($userId) {
            $queryParamsMap[] = ['key' => 'user_id', 'value' => $userId];
        }

        return $this->getApi('eventsub/subscriptions', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference#create-eventsub-subscription
     */
    public function createEventSubSubscription(string $bearer, string $secret, string $callback, string $type, string $version, array $condition, ?bool $isBatchingEnabled = null): ResponseInterface
    {
        return $this->createSubscriptionWithTransport(
            $bearer,
            [
                'method' => 'webhook',
                'callback' => $callback,
                'secret' => $secret,
            ],
            $type,
            $version,
            $condition,
            $isBatchingEnabled
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference#delete-eventsub-subscription
     */
    public function deleteEventSubSubscription(string $bearer, string $id): ResponseInterface
    {
        $queryParamsMap = [];
        $queryParamsMap[] = ['key' => 'id', 'value' => $id];

        return $this->deleteApi('eventsub/subscriptions', $bearer, $queryParamsMap);
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelupdate
     */
    public function subscribeToChannelUpdate(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.update',
            '2',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types/#channelfollow
     */
    public function subscribeToChannelFollow(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.follow',
            '2',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelsubscribe
     */
    public function subscribeToChannelSubscribe(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.subscribe',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types/#channelsubscriptionend
     */
    public function subscribeToChannelSubscriptionEnd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.subscription.end',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types/#channelsubscriptiongift
     */
    public function subscribeToChannelSubscriptionGift(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.subscription.gift',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelsubscriptionmessage
     */
    public function subscribeToChannelSubscriptionMessage(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.subscription.message',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelcheer
     */
    public function subscribeToChannelCheer(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.cheer',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelraid
     */
    public function subscribeToChannelRaid(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.raid',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelban
     */
    public function subscribeToChannelBan(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.ban',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelunban
     */
    public function subscribeToChannelUnban(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.unban',
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelmoderatoradd
     */
    public function subscribeToChannelModeratorAdd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelModerator($bearer, $secret, $callback, $twitchId, 'add');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelmoderatorremove
     */
    public function subscribeToChannelModeratorRemove(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelModerator($bearer, $secret, $callback, $twitchId, 'remove');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchannel_points_custom_rewardadd
     */
    public function subscribeToChannelPointsCustomRewardAdd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPointsCustomReward($bearer, $secret, $callback, $twitchId, null, 'add');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchannel_points_custom_rewardupdate
     */
    public function subscribeToChannelPointsCustomRewardUpdate(string $bearer, string $secret, string $callback, string $twitchId, ?string $rewardId = null): ResponseInterface
    {
        return $this->subscribeToChannelPointsCustomReward($bearer, $secret, $callback, $twitchId, $rewardId, 'update');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchannel_points_custom_rewardremove
     */
    public function subscribeToChannelPointsCustomRewardRemove(string $bearer, string $secret, string $callback, string $twitchId, ?string $rewardId = null): ResponseInterface
    {
        return $this->subscribeToChannelPointsCustomReward($bearer, $secret, $callback, $twitchId, $rewardId, 'remove');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchannel_points_custom_reward_redemptionadd
     */
    public function subscribeToChannelPointsCustomRewardRedemptionAdd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPointsCustomRewardRedemption($bearer, $secret, $callback, $twitchId, null, 'add');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchannel_points_custom_reward_redemptionupdate
     */
    public function subscribeToChannelPointsCustomRewardRedemptionUpdate(string $bearer, string $secret, string $callback, string $twitchId, ?string $rewardId = null): ResponseInterface
    {
        return $this->subscribeToChannelPointsCustomRewardRedemption($bearer, $secret, $callback, $twitchId, $rewardId, 'update');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelpollbegin
     */
    public function subscribeToChannelPollBegin(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPoll($bearer, $secret, $callback, $twitchId, 'begin');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelpollprogress
     */
    public function subscribeToChannelPollProgress(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPoll($bearer, $secret, $callback, $twitchId, 'progress');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelpollend
     */
    public function subscribeToChannelPollEnd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPoll($bearer, $secret, $callback, $twitchId, 'end');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelpredictionbegin
     */
    public function subscribeToChannelPredictionBegin(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPrediction($bearer, $secret, $callback, $twitchId, 'begin');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelpredictionprogress
     */
    public function subscribeToChannelPredictionProgress(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPrediction($bearer, $secret, $callback, $twitchId, 'progress');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelpredictionprogress
     */
    public function subscribeToChannelPredictionLock(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPrediction($bearer, $secret, $callback, $twitchId, 'lock');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelpredictionend
     */
    public function subscribeToChannelPredictionEnd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelPrediction($bearer, $secret, $callback, $twitchId, 'end');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelhype_trainbegin
     */
    public function subscribeToChannelHypeTrainBegin(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelHypeTrain($bearer, $secret, $callback, $twitchId, 'begin');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelhype_trainprogress
     */
    public function subscribeToChannelHypeTrainProgress(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelHypeTrain($bearer, $secret, $callback, $twitchId, 'progress');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelhype_trainend
     */
    public function subscribeToChannelHypeTrainEnd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelHypeTrain($bearer, $secret, $callback, $twitchId, 'end');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#streamonline
     */
    public function subscribeToStreamOnline(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToStream($bearer, $secret, $callback, $twitchId, 'online');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#streamoffline
     */
    public function subscribeToStreamOffline(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToStream($bearer, $secret, $callback, $twitchId, 'offline');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#userauthorizationrevoke
     */
    public function subscribeToUserAuthorizationRevoke(string $bearer, string $secret, string $callback, string $clientId): ResponseInterface
    {
        return $this->subscribeToUserAuthorization($bearer, $secret, $callback, $clientId, 'revoke');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#userauthorizationgrant
     */
    public function subscribeToUserAuthorizationGrant(string $bearer, string $secret, string $callback, string $clientId): ResponseInterface
    {
        return $this->subscribeToUserAuthorization($bearer, $secret, $callback, $clientId, 'grant');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#userupdate
     */
    public function subscribeToUserUpdate(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'user.update',
            '1',
            ['user_id' => $twitchId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#extensionbits_transactioncreate
     */
    public function subscribeToExtensionBitsTransactionCreate(string $bearer, string $secret, string $callback, string $extensionClientId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'extension.bits_transaction.create',
            '1',
            ['extension_client_id' => $extensionClientId],
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelgoalbegin
     */
    public function subscribeToChannelGoalBegin(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelGoal($bearer, $secret, $callback, $twitchId, 'begin');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelgoalprogress
     */
    public function subscribeToChannelGoalProgress(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelGoal($bearer, $secret, $callback, $twitchId, 'progress');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelgoalend
     */
    public function subscribeToChannelGoalEnd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelGoal($bearer, $secret, $callback, $twitchId, 'end');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelcharity_campaignstart
     */
    public function subscribeToChannelCharityCampaignStart(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelCharityCampaign($bearer, $secret, $callback, $twitchId, 'start');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelcharity_campaignprogress
     */
    public function subscribeToChannelCharityCampaignProgress(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelCharityCampaign($bearer, $secret, $callback, $twitchId, 'progress');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelcharity_campaignstop
     */
    public function subscribeToChannelCharityCampaignStop(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelCharityCampaign($bearer, $secret, $callback, $twitchId, 'stop');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelcharity_campaigndonate
     */
    public function subscribeToChannelCharityCampaignDonate(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->subscribeToChannelCharityCampaign($bearer, $secret, $callback, $twitchId, 'donate');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelshield_modebegin
     */
    public function subscribeToChannelShieldModeBegin(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->subscribeToChannelShieldMode($bearer, $secret, $callback, $twitchId, $moderatorId, 'begin');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelshield_modeend
     */
    public function subscribeToChannelShieldModeEnd(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->subscribeToChannelShieldMode($bearer, $secret, $callback, $twitchId, $moderatorId, 'end');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types/#channelshoutoutcreate
     */
    public function subscribeToChannelShoutoutCreate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->subscribeToChannelShoutout($bearer, $secret, $callback, $twitchId, $moderatorId, 'create');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types/#channelshoutoutreceive
     */
    public function subscribeToChannelShoutoutReceive(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->subscribeToChannelShoutout($bearer, $secret, $callback, $twitchId, $moderatorId, 'receive');
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types/#dropentitlementgrant
     */
    public function subscribeToDropEntitlementGrant(string $bearer, string $secret, string $callback, string $organizationId, ?string $categoryId = null, ?string $campaign_id = null): ResponseInterface
    {
        $condition = ['organization_id' => $organizationId];
        if ($categoryId) {
            $condition['category_id'] = $categoryId;
        }
        if ($campaign_id) {
            $condition['campaign_id'] = $campaign_id;
        }

        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'drop.entitlement.grant',
            '1',
            $condition,
            true
        );
    }

    /**
     * @link https://dev.twitch.tv/docs/eventsub#verify-a-signature
     */
    public function verifySignature(string $signature, string $secret, string $messageId, string $timestamp, string $body): bool
    {
        [$hashAlgorithm, $expectedHash] = explode('=', $signature);
        $generatedHash = hash_hmac($hashAlgorithm, $messageId.$timestamp.$body, $secret);

        return $expectedHash === $generatedHash;
    }

    private function subscribeToChannelModerator(string $bearer, string $secret, string $callback, string $twitchId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.moderator.%s', $eventType),
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    private function subscribeToChannelPointsCustomReward(string $bearer, string $secret, string $callback, string $twitchId, ?string $rewardId = null, string $eventType): ResponseInterface
    {
        $condition = ['broadcaster_user_id' => $twitchId];

        if ($rewardId) {
            $condition['reward_id'] = $rewardId;
        }

        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.channel_points_custom_reward.%s', $eventType),
            '1',
            $condition,
        );
    }

    private function subscribeToChannelPointsCustomRewardRedemption(string $bearer, string $secret, string $callback, string $twitchId, ?string $rewardId = null, string $eventType): ResponseInterface
    {
        $condition = ['broadcaster_user_id' => $twitchId];

        if ($rewardId) {
            $condition['reward_id'] = $rewardId;
        }

        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.channel_points_custom_reward_redemption.%s', $eventType),
            '1',
            $condition,
        );
    }

    private function subscribeToChannelPoll(string $bearer, string $secret, string $callback, string $twitchId, string $eventType): ResponseInterface
    {
        $condition = ['broadcaster_user_id' => $twitchId];

        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.poll.%s', $eventType),
            '1',
            $condition,
        );
    }

    private function subscribeToChannelPrediction(string $bearer, string $secret, string $callback, string $twitchId, string $eventType): ResponseInterface
    {
        $condition = ['broadcaster_user_id' => $twitchId];

        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.prediction.%s', $eventType),
            '1',
            $condition,
        );
    }

    private function subscribeToChannelHypeTrain(string $bearer, string $secret, string $callback, string $twitchId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.hype_train.%s', $eventType),
            '2',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    private function subscribeToStream(string $bearer, string $secret, string $callback, string $twitchId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('stream.%s', $eventType),
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    private function subscribeToUserAuthorization(string $bearer, string $secret, string $callback, string $clientId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('user.authorization.%s', $eventType),
            '1',
            ['client_id' => $clientId],
        );
    }

    private function subscribeToChannelGoal(string $bearer, string $secret, string $callback, string $twitchId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.goal.%s', $eventType),
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    private function subscribeToChannelCharityCampaign(string $bearer, string $secret, string $callback, string $twitchId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.charity_campaign.%s', $eventType),
            '1',
            ['broadcaster_user_id' => $twitchId],
        );
    }

    private function subscribeToChannelShieldMode(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.shield_mode.%s', $eventType),
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    private function subscribeToChannelShoutout(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId, string $eventType): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            sprintf('channel.shoutout.%s', $eventType),
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/handling-websocket-events
     *
     * WebSocket subscriptions carry a session id from the welcome message instead of a
     * callback and secret, so they cannot reuse createEventSubSubscription's signature.
     */
    public function createEventSubSubscriptionViaWebSocket(string $bearer, string $sessionId, string $type, string $version, array $condition, ?bool $isBatchingEnabled = null): ResponseInterface
    {
        return $this->createSubscriptionWithTransport(
            $bearer,
            ['method' => 'websocket', 'session_id' => $sessionId],
            $type,
            $version,
            $condition,
            $isBatchingEnabled
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/handling-conduit-events
     *
     * Create the conduit first with ConduitsApi, then pass its id here.
     */
    public function createEventSubSubscriptionViaConduit(string $bearer, string $conduitId, string $type, string $version, array $condition, ?bool $isBatchingEnabled = null): ResponseInterface
    {
        return $this->createSubscriptionWithTransport(
            $bearer,
            ['method' => 'conduit', 'conduit_id' => $conduitId],
            $type,
            $version,
            $condition,
            $isBatchingEnabled
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#conduitsharddisabled
     *
     * The condition takes the client id that owns the conduit. A conduit id narrows it to one
     * conduit; without it the subscription covers every conduit the client owns.
     */
    public function subscribeToConduitShardDisabled(string $bearer, string $secret, string $callback, string $clientId, ?string $conduitId = null): ResponseInterface
    {
        $condition = ['client_id' => $clientId];

        if ($conduitId) {
            $condition['conduit_id'] = $conduitId;
        }

        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'conduit.shard.disabled',
            '1',
            $condition,
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#automodmessagehold
     */
    public function subscribeToAutomodMessageHold(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'automod.message.hold',
            '2',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#automodmessageupdate
     */
    public function subscribeToAutomodMessageUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'automod.message.update',
            '2',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#automodsettingsupdate
     */
    public function subscribeToAutomodSettingsUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'automod.settings.update',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#automodtermsupdate
     */
    public function subscribeToAutomodTermsUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'automod.terms.update',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channeladbreakbegin
     */
    public function subscribeToChannelAdBreakBegin(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.ad_break.begin',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelbitsuse
     */
    public function subscribeToChannelBitsUse(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.bits.use',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchannelpointsautomaticrewardredemptionadd
     */
    public function subscribeToChannelPointsAutomaticRewardRedemptionAdd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.channel_points_automatic_reward_redemption.add',
            '2',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatclear
     */
    public function subscribeToChannelChatClear(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat.clear',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatclearusermessages
     */
    public function subscribeToChannelChatClearUserMessages(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat.clear_user_messages',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatmessage
     */
    public function subscribeToChannelChatMessage(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat.message',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatmessagedelete
     */
    public function subscribeToChannelChatMessageDelete(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat.message_delete',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatnotification
     */
    public function subscribeToChannelChatNotification(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat.notification',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatusermessagehold
     */
    public function subscribeToChannelChatUserMessageHold(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat.user_message_hold',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatusermessageupdate
     */
    public function subscribeToChannelChatUserMessageUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat.user_message_update',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelchatsettingsupdate
     */
    public function subscribeToChannelChatSettingsUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.chat_settings.update',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'user_id' => $userId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelcustompowerupredemptionadd
     */
    public function subscribeToChannelCustomPowerUpRedemptionAdd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.custom_power_up_redemption.add',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelgueststarguestupdate
     */
    public function subscribeToChannelGuestStarGuestUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.guest_star_guest.update',
            'beta',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelgueststarsessionbegin
     */
    public function subscribeToChannelGuestStarSessionBegin(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.guest_star_session.begin',
            'beta',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelgueststarsessionend
     */
    public function subscribeToChannelGuestStarSessionEnd(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.guest_star_session.end',
            'beta',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelgueststarsettingsupdate
     */
    public function subscribeToChannelGuestStarSettingsUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.guest_star_settings.update',
            'beta',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelmoderate
     */
    public function subscribeToChannelModerate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.moderate',
            '2',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelsharedchatbegin
     */
    public function subscribeToChannelSharedChatBegin(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.shared_chat.begin',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelsharedchatupdate
     */
    public function subscribeToChannelSharedChatUpdate(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.shared_chat.update',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelsharedchatend
     */
    public function subscribeToChannelSharedChatEnd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.shared_chat.end',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelsuspicioususermessage
     */
    public function subscribeToChannelSuspiciousUserMessage(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.suspicious_user.message',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelsuspicioususerupdate
     */
    public function subscribeToChannelSuspiciousUserUpdate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.suspicious_user.update',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelunbanrequestcreate
     */
    public function subscribeToChannelUnbanRequestCreate(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.unban_request.create',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelunbanrequestresolve
     */
    public function subscribeToChannelUnbanRequestResolve(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.unban_request.resolve',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelvipadd
     */
    public function subscribeToChannelVipAdd(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.vip.add',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelvipremove
     */
    public function subscribeToChannelVipRemove(string $bearer, string $secret, string $callback, string $twitchId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.vip.remove',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelwarningacknowledge
     */
    public function subscribeToChannelWarningAcknowledge(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.warning.acknowledge',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#channelwarningsend
     */
    public function subscribeToChannelWarningSend(string $bearer, string $secret, string $callback, string $twitchId, string $moderatorId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'channel.warning.send',
            '1',
            [
                'broadcaster_user_id' => $twitchId,
                'moderator_user_id' => $moderatorId,
            ],
        );
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/eventsub/eventsub-subscription-types#userwhispermessage
     */
    public function subscribeToUserWhisperMessage(string $bearer, string $secret, string $callback, string $userId): ResponseInterface
    {
        return $this->createEventSubSubscription(
            $bearer,
            $secret,
            $callback,
            'user.whisper.message',
            '1',
            [
                'user_id' => $userId,
            ],
        );
    }

    private function createSubscriptionWithTransport(string $bearer, array $transport, string $type, string $version, array $condition, ?bool $isBatchingEnabled = null): ResponseInterface
    {
        $bodyParams = [];

        $bodyParams[] = ['key' => 'type', 'value' => $type];
        $bodyParams[] = ['key' => 'version', 'value' => $version];
        $bodyParams[] = ['key' => 'condition', 'value' => $condition];
        $bodyParams[] = ['key' => 'transport', 'value' => $transport];

        if (null !== $isBatchingEnabled) {
            $bodyParams[] = ['key' => 'is_batching_enabled', 'value' => $isBatchingEnabled];
        }

        return $this->postApi('eventsub/subscriptions', $bearer, [], $bodyParams);
    }
}
