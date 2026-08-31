<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class SharedChatApi extends AbstractResource
{
    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-shared-chat-session
     */
    public function getSharedChatSession(string $bearer, string $broadcasterId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];

        return $this->getApi('shared_chat/session', $bearer, $queryParamsMap);
    }
}
