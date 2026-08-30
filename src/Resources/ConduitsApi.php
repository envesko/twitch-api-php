<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class ConduitsApi extends AbstractResource
{
    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-conduits
     */
    public function getConduits(string $bearer): ResponseInterface
    {
        return $this->getApi('eventsub/conduits', $bearer);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#create-conduits
     */
    public function createConduits(string $bearer, int $shardCount): ResponseInterface
    {
        $bodyParamsMap = [];

        $bodyParamsMap[] = ['key' => 'shard_count', 'value' => $shardCount];

        return $this->postApi('eventsub/conduits', $bearer, [], $bodyParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#update-conduits
     */
    public function updateConduits(string $bearer, string $id, int $shardCount): ResponseInterface
    {
        $bodyParamsMap = [];

        $bodyParamsMap[] = ['key' => 'id', 'value' => $id];
        $bodyParamsMap[] = ['key' => 'shard_count', 'value' => $shardCount];

        return $this->patchApi('eventsub/conduits', $bearer, [], $bodyParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#delete-conduit
     */
    public function deleteConduit(string $bearer, string $id): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'id', 'value' => $id];

        return $this->deleteApi('eventsub/conduits', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-conduit-shards
     */
    public function getConduitShards(string $bearer, string $conduitId, ?string $status = null, ?string $after = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'conduit_id', 'value' => $conduitId];

        if ($status) {
            $queryParamsMap[] = ['key' => 'status', 'value' => $status];
        }

        if ($after) {
            $queryParamsMap[] = ['key' => 'after', 'value' => $after];
        }

        return $this->getApi('eventsub/conduits/shards', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#update-conduit-shards
     *
     * $shards is a list of shard definitions, each with an id and a transport, eg.
     * [['id' => '0', 'transport' => ['method' => 'webhook', 'callback' => '...', 'secret' => '...']]]
     */
    public function updateConduitShards(string $bearer, string $conduitId, array $shards): ResponseInterface
    {
        $bodyParamsMap = [];

        $bodyParamsMap[] = ['key' => 'conduit_id', 'value' => $conduitId];
        $bodyParamsMap[] = ['key' => 'shards', 'value' => $shards];

        return $this->patchApi('eventsub/conduits/shards', $bearer, [], $bodyParamsMap);
    }
}
