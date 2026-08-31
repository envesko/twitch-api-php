<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class EntitlementsApi extends AbstractResource
{
    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference#get-drops-entitlements
     */
    public function getDropsEntitlements(string $bearer, ?string $id = null, ?string $userId = null, ?string $gameId = null, ?string $after = null, ?int $first = null, ?string $fulfillmentStatus = null): ResponseInterface
    {
        $queryParamsMap = [];

        if ($id) {
            $queryParamsMap[] = ['key' => 'id', 'value' => $id];
        }

        if ($userId) {
            $queryParamsMap[] = ['key' => 'user_id', 'value' => $userId];
        }

        if ($gameId) {
            $queryParamsMap[] = ['key' => 'game_id', 'value' => $gameId];
        }

        if ($after) {
            $queryParamsMap[] = ['key' => 'after', 'value' => $after];
        }

        if ($first) {
            $queryParamsMap[] = ['key' => 'first', 'value' => $first];
        }

        if ($fulfillmentStatus) {
            $queryParamsMap[] = ['key' => 'fulfillment_status', 'value' => $fulfillmentStatus];
        }

        return $this->getApi('entitlements/drops', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference#update-drops-entitlements
     */
    public function updateDropsEntitlements(string $bearer, ?array $entitlement_ids = null, ?string $fulfillment_status = null): ResponseInterface
    {
        $bodyParamsMap = [];

        if ($entitlement_ids) {
            $bodyParamsMap[] = ['key' => 'entitlement_ids', 'value' => $entitlement_ids];
        }

        if ($fulfillment_status) {
            $bodyParamsMap[] = ['key' => 'fulfillment_status', 'value' => $fulfillment_status];
        }

        return $this->patchApi('entitlements/drops', $bearer, [], $bodyParamsMap);
    }
}
