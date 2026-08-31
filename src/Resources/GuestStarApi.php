<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Guest Star is published by Twitch as a beta API. Its request and response shapes can change
 * without going through a deprecation cycle, so treat anything here as unstable.
 */
class GuestStarApi extends AbstractResource
{
    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-channel-guest-star-settings
     */
    public function getChannelGuestStarSettings(string $bearer, string $broadcasterId, string $moderatorId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];

        return $this->getApi('guest_star/channel_settings', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#update-channel-guest-star-settings
     */
    public function updateChannelGuestStarSettings(string $bearer, string $broadcasterId, array $optionalBodyParams = []): ResponseInterface
    {
        $queryParamsMap = $bodyParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];

        foreach ($optionalBodyParams as $key => $value) {
            $bodyParamsMap[] = ['key' => $key, 'value' => $value];
        }

        return $this->putApi('guest_star/channel_settings', $bearer, $queryParamsMap, $bodyParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-guest-star-session
     */
    public function getGuestStarSession(string $bearer, string $broadcasterId, string $moderatorId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];

        return $this->getApi('guest_star/session', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#create-guest-star-session
     */
    public function createGuestStarSession(string $bearer, string $broadcasterId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];

        return $this->postApi('guest_star/session', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#end-guest-star-session
     */
    public function endGuestStarSession(string $bearer, string $broadcasterId, string $sessionId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];

        return $this->deleteApi('guest_star/session', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-guest-star-invites
     */
    public function getGuestStarInvites(string $bearer, string $broadcasterId, string $moderatorId, string $sessionId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];

        return $this->getApi('guest_star/invites', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#send-guest-star-invite
     */
    public function sendGuestStarInvite(string $bearer, string $broadcasterId, string $moderatorId, string $sessionId, string $guestId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];
        $queryParamsMap[] = ['key' => 'guest_id', 'value' => $guestId];

        return $this->postApi('guest_star/invites', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#delete-guest-star-invite
     */
    public function deleteGuestStarInvite(string $bearer, string $broadcasterId, string $moderatorId, string $sessionId, string $guestId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];
        $queryParamsMap[] = ['key' => 'guest_id', 'value' => $guestId];

        return $this->deleteApi('guest_star/invites', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#assign-guest-star-slot
     */
    public function assignGuestStarSlot(string $bearer, string $broadcasterId, string $moderatorId, string $sessionId, string $guestId, string $slotId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];
        $queryParamsMap[] = ['key' => 'guest_id', 'value' => $guestId];
        $queryParamsMap[] = ['key' => 'slot_id', 'value' => $slotId];

        return $this->postApi('guest_star/slot', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#update-guest-star-slot
     */
    public function updateGuestStarSlot(string $bearer, string $broadcasterId, string $moderatorId, string $sessionId, string $sourceSlotId, ?string $destinationSlotId = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];
        $queryParamsMap[] = ['key' => 'source_slot_id', 'value' => $sourceSlotId];

        if ($destinationSlotId) {
            $queryParamsMap[] = ['key' => 'destination_slot_id', 'value' => $destinationSlotId];
        }

        return $this->patchApi('guest_star/slot', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#delete-guest-star-slot
     */
    public function deleteGuestStarSlot(string $bearer, string $broadcasterId, string $moderatorId, string $sessionId, string $guestId, string $slotId, ?string $shouldReinviteGuest = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];
        $queryParamsMap[] = ['key' => 'guest_id', 'value' => $guestId];
        $queryParamsMap[] = ['key' => 'slot_id', 'value' => $slotId];

        if ($shouldReinviteGuest) {
            $queryParamsMap[] = ['key' => 'should_reinvite_guest', 'value' => $shouldReinviteGuest];
        }

        return $this->deleteApi('guest_star/slot', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#update-guest-star-slot-settings
     */
    public function updateGuestStarSlotSettings(string $bearer, string $broadcasterId, string $moderatorId, string $sessionId, string $slotId, array $optionalQueryParams = []): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $queryParamsMap[] = ['key' => 'moderator_id', 'value' => $moderatorId];
        $queryParamsMap[] = ['key' => 'session_id', 'value' => $sessionId];
        $queryParamsMap[] = ['key' => 'slot_id', 'value' => $slotId];

        foreach ($optionalQueryParams as $key => $value) {
            $queryParamsMap[] = ['key' => $key, 'value' => $value];
        }

        return $this->patchApi('guest_star/slot_settings', $bearer, $queryParamsMap);
    }
}
