<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * These endpoints authenticate with an extension JWT rather than a user or app access token.
 * The bearer is still sent in the Authorization header in the same shape, so no separate auth
 * machinery is needed, but the token has to be signed with the extension's shared secret.
 */
class ExtensionsApi extends AbstractResource
{
    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-extensions
     */
    public function getExtensions(string $jwt, string $extensionId, ?string $extensionVersion = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];

        if ($extensionVersion) {
            $queryParamsMap[] = ['key' => 'extension_version', 'value' => $extensionVersion];
        }

        return $this->getApi('extensions', $jwt, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-released-extensions
     */
    public function getReleasedExtensions(string $bearer, string $extensionId, ?string $extensionVersion = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];

        if ($extensionVersion) {
            $queryParamsMap[] = ['key' => 'extension_version', 'value' => $extensionVersion];
        }

        return $this->getApi('extensions/released', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-extension-live-channels
     */
    public function getExtensionLiveChannels(string $bearer, string $extensionId, ?int $first = null, ?string $after = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];

        if ($first) {
            $queryParamsMap[] = ['key' => 'first', 'value' => $first];
        }

        if ($after) {
            $queryParamsMap[] = ['key' => 'after', 'value' => $after];
        }

        return $this->getApi('extensions/live', $bearer, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-extension-configuration-segment
     */
    public function getExtensionConfigurationSegment(string $jwt, string $extensionId, array $segments, ?string $broadcasterId = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];

        foreach ($segments as $segment) {
            $queryParamsMap[] = ['key' => 'segment', 'value' => $segment];
        }

        if ($broadcasterId) {
            $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        }

        return $this->getApi('extensions/configurations', $jwt, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#set-extension-configuration-segment
     */
    public function setExtensionConfigurationSegment(string $jwt, string $extensionId, string $segment, array $optionalBodyParams = []): ResponseInterface
    {
        $bodyParamsMap = [];

        $bodyParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];
        $bodyParamsMap[] = ['key' => 'segment', 'value' => $segment];

        foreach ($optionalBodyParams as $key => $value) {
            $bodyParamsMap[] = ['key' => $key, 'value' => $value];
        }

        return $this->putApi('extensions/configurations', $jwt, [], $bodyParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#set-extension-required-configuration
     */
    public function setExtensionRequiredConfiguration(string $jwt, string $broadcasterId, string $extensionId, string $extensionVersion, string $requiredConfiguration): ResponseInterface
    {
        $queryParamsMap = $bodyParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];

        $bodyParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];
        $bodyParamsMap[] = ['key' => 'extension_version', 'value' => $extensionVersion];
        $bodyParamsMap[] = ['key' => 'required_configuration', 'value' => $requiredConfiguration];

        return $this->putApi('extensions/required_configuration', $jwt, $queryParamsMap, $bodyParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#send-extension-pubsub-message
     */
    public function sendExtensionPubSubMessage(string $jwt, array $target, string $broadcasterId, bool $isGlobalBroadcast, string $message): ResponseInterface
    {
        $bodyParamsMap = [];

        $bodyParamsMap[] = ['key' => 'target', 'value' => $target];
        $bodyParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];
        $bodyParamsMap[] = ['key' => 'is_global_broadcast', 'value' => $isGlobalBroadcast];
        $bodyParamsMap[] = ['key' => 'message', 'value' => $message];

        return $this->postApi('extensions/pubsub', $jwt, [], $bodyParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#send-extension-chat-message
     */
    public function sendExtensionChatMessage(string $jwt, string $broadcasterId, string $text, string $extensionId, string $extensionVersion): ResponseInterface
    {
        $queryParamsMap = $bodyParamsMap = [];

        $queryParamsMap[] = ['key' => 'broadcaster_id', 'value' => $broadcasterId];

        $bodyParamsMap[] = ['key' => 'text', 'value' => $text];
        $bodyParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];
        $bodyParamsMap[] = ['key' => 'extension_version', 'value' => $extensionVersion];

        return $this->postApi('extensions/chat', $jwt, $queryParamsMap, $bodyParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-extension-secrets
     */
    public function getExtensionSecrets(string $jwt, string $extensionId): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];

        return $this->getApi('extensions/jwt/secrets', $jwt, $queryParamsMap);
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#create-extension-secret
     */
    public function createExtensionSecret(string $jwt, string $extensionId, ?int $delay = null): ResponseInterface
    {
        $queryParamsMap = [];

        $queryParamsMap[] = ['key' => 'extension_id', 'value' => $extensionId];

        if ($delay) {
            $queryParamsMap[] = ['key' => 'delay', 'value' => $delay];
        }

        return $this->postApi('extensions/jwt/secrets', $jwt, $queryParamsMap);
    }
}
