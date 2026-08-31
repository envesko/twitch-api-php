<?php

declare(strict_types=1);

namespace TwitchApi;

use GuzzleHttp\Client;

class HelixGuzzleClient
{
    private $client;
    private const BASE_URI = 'https://api.twitch.tv/helix/';

    public function __construct(string $clientId, array $config = [], ?string $baseUri = null)
    {
        if ($baseUri == null) {
            $baseUri = self::BASE_URI;
        }

        $headers = [
            'Client-ID' => $clientId,
            'Content-Type' => 'application/json',
        ];

        $client_config = [
            'base_uri' => $baseUri,
            'headers' => $headers,
        ];

        // Anything the caller passes wins, so a custom handler is merged in alongside the
        // base URI and Client-ID rather than replacing them. Discarding those whenever a
        // handler was present left every consumer that wired in retry or logging middleware
        // sending relative URLs with no Client-ID, which Twitch rejects.
        //
        // Headers merge per header rather than wholesale. A caller adding a User-Agent is
        // not asking to drop the Client-ID, but a plain array_merge replaces the whole array
        // and does exactly that, which Twitch answers with "Client ID and OAuth token do not
        // match". Overriding an individual header still works, case-insensitively.
        $callerHeaders = $config['headers'] ?? [];
        unset($config['headers']);

        foreach ($callerHeaders as $name => $value) {
            foreach (array_keys($headers) as $existing) {
                if (strcasecmp($existing, $name) === 0) {
                    unset($headers[$existing]);
                }
            }

            $headers[$name] = $value;
        }

        $client_config['headers'] = $headers;
        $client_config = array_merge($client_config, $config);

        $this->client = new Client($client_config);
    }

    public function getConfig($option = null)
    {
        return $this->client->getConfig($option);
    }

    public function send($request)
    {
        return $this->client->send($request);
    }
}
