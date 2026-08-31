<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TwitchApi\Auth\AuthGuzzleClient;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;
use TwitchApi\Resources\UsersApi;

/**
 * How the two clients are configured, asserted through the request that leaves them rather
 * than by reading their config back. Guzzle deprecated getConfig() in 7.9 and removed it in 8,
 * so the specs these replace could not survive that upgrade.
 */
class ClientConfigurationTest extends TestCase
{
    /** @var array<int, array<string, mixed>> */
    private array $history = [];

    private function helix(array $config = []): HelixGuzzleClient
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{}')]));
        $stack->push(Middleware::history($this->history));

        return new HelixGuzzleClient('TEST_CLIENT_ID', $config + ['handler' => $stack]);
    }

    private function send(HelixGuzzleClient $client): \Psr\Http\Message\RequestInterface
    {
        (new UsersApi($client, new RequestGenerator()))->getUserById('TOK', '1');

        return $this->history[0]['request'];
    }

    public function testHelixRequestsGoToTheHelixBaseUri(): void
    {
        $uri = $this->send($this->helix())->getUri();

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('api.twitch.tv', $uri->getHost());
        $this->assertStringStartsWith('/helix/', $uri->getPath());
    }

    public function testHelixSendsTheClientIdHeader(): void
    {
        $this->assertSame('TEST_CLIENT_ID', $this->send($this->helix())->getHeaderLine('Client-ID'));
    }

    public function testHelixSendsAJsonContentType(): void
    {
        $this->assertSame('application/json', $this->send($this->helix())->getHeaderLine('Content-Type'));
    }

    public function testCallerConfigurationOverridesTheDefaults(): void
    {
        $uri = $this->send($this->helix(['base_uri' => 'https://different.url']))->getUri();

        $this->assertSame('different.url', $uri->getHost());
    }

    public function testCallerHeadersDoNotDisplaceTheClientId(): void
    {
        // Regression: a caller adding one header of their own used to lose the Client-ID,
        // which Twitch answers with "Client ID and OAuth token do not match".
        $request = $this->send($this->helix(['headers' => ['User-Agent' => 'mine/1.0']]));

        $this->assertSame('TEST_CLIENT_ID', $request->getHeaderLine('Client-ID'));
        $this->assertSame('mine/1.0', $request->getHeaderLine('User-Agent'));
    }

    public function testAuthClientPointsAtTheOauthBaseUri(): void
    {
        $client = AuthGuzzleClient::getClient();

        $this->assertInstanceOf(Client::class, $client);

        $uri = $client->getConfig('base_uri');
        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('id.twitch.tv', $uri->getHost());
        $this->assertSame('/oauth2/', $uri->getPath());
    }

    public function testAuthClientAcceptsCallerConfiguration(): void
    {
        $client = AuthGuzzleClient::getClient(['base_uri' => 'https://different.url']);

        $this->assertSame('different.url', $client->getConfig('base_uri')->getHost());
    }
}
