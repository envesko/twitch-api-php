<?php

declare(strict_types=1);

namespace TwitchApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TwitchApi\Exception\ExceptionFactory;

class HelixGuzzleClient
{
    private Client $client;

    /** @var array<string, mixed> */
    private array $config;

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

        // Kept so getConfig() can answer without asking Guzzle. Guzzle deprecated
        // Client::getConfig() in 7.9 and removed it in 8, and this class supports both.
        $this->config = $client_config;
        $this->client = new Client($client_config);
    }

    /**
     * @return mixed the whole configuration, or one option from it
     */
    public function getConfig(?string $option = null)
    {
        if ($option === null) {
            return $this->config;
        }

        return $this->config[$option] ?? null;
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->client->send($request);
        } catch (BadResponseException $e) {
            // Rethrown as the typed equivalent, which extends the Guzzle class it replaces,
            // so an existing catch for GuzzleException still matches.
            throw ExceptionFactory::from($e);
        }
    }
}
