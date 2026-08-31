<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;
use TwitchApi\TwitchApi;

/**
 * Asserts the request that actually leaves the library.
 *
 * The resource specs mock RequestGenerator and assert the parameter map passed to it, which
 * cannot catch a wrong endpoint path, a wrong verb, or a parameter that never reaches the URL.
 * These drive the real generator and a real client, and read the outgoing request back off a
 * Guzzle history middleware.
 */
class EndpointUrlTest extends TestCase
{
    /** @var array<int, array<string, mixed>> */
    private $history = [];

    private function api(int $responses = 1): TwitchApi
    {
        $this->history = [];

        $queue = array_fill(0, $responses, new Response(200, [], '{"data":[]}'));
        $stack = HandlerStack::create(new MockHandler($queue));
        $stack->push(Middleware::history($this->history));

        $client = new HelixGuzzleClient('TEST_CLIENT_ID', ['handler' => $stack]);

        return new TwitchApi($client, 'TEST_CLIENT_ID', 'TEST_CLIENT_SECRET');
    }

    private function lastRequest(): RequestInterface
    {
        $this->assertNotEmpty($this->history, 'No request was sent.');

        return $this->history[count($this->history) - 1]['request'];
    }

    private function assertSent(string $method, string $url): void
    {
        $request = $this->lastRequest();

        $this->assertSame($method, $request->getMethod());
        $this->assertSame($url, (string) $request->getUri());
    }

    // ------------------------------------------------------- client configuration

    public function testRequestsGoToTheHelixBaseUri(): void
    {
        $this->api()->getUsersApi()->getUserById('TOK', '123');

        $this->assertSame('https://api.twitch.tv/helix/', substr((string) $this->lastRequest()->getUri(), 0, 28));
    }

    public function testClientIdHeaderSurvivesACustomHandler(): void
    {
        // A consumer wiring in retry or logging middleware must still get an authenticated
        // client. Supplying a handler previously discarded the base URI and this header.
        $this->api()->getUsersApi()->getUserById('TOK', '123');

        $this->assertSame('TEST_CLIENT_ID', $this->lastRequest()->getHeaderLine('Client-ID'));
    }

    public function testClientIdSurvivesCallerSuppliedHeaders(): void
    {
        // Reported upstream as "Client ID and OAuth token do not match": adding any header of
        // your own, a User-Agent say, replaced the whole header array and took the Client-ID
        // with it, so Twitch rejected every Helix call with a 401.
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"data":[]}')]));
        $stack->push(Middleware::history($this->history));

        $client = new HelixGuzzleClient('TEST_CLIENT_ID', [
            'handler' => $stack,
            'headers' => ['User-Agent' => 'my-app/1.0'],
        ]);

        (new TwitchApi($client, 'TEST_CLIENT_ID', 'TEST_CLIENT_SECRET'))
            ->getUsersApi()->getUserByAccessToken('USER_TOKEN');

        $this->assertSame('TEST_CLIENT_ID', $this->lastRequest()->getHeaderLine('Client-ID'));
        $this->assertSame('my-app/1.0', $this->lastRequest()->getHeaderLine('User-Agent'));
    }

    public function testCallerCanStillOverrideAHeaderWhateverItsCase(): void
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler([new Response(200, [], '{"data":[]}')]));
        $stack->push(Middleware::history($this->history));

        $client = new HelixGuzzleClient('DEFAULT_ID', [
            'handler' => $stack,
            'headers' => ['client-id' => 'OVERRIDDEN'],
        ]);

        (new TwitchApi($client, 'DEFAULT_ID', 'TEST_CLIENT_SECRET'))
            ->getUsersApi()->getUserByAccessToken('USER_TOKEN');

        // One header, not two differing only by case.
        $this->assertSame(['OVERRIDDEN'], $this->lastRequest()->getHeader('Client-ID'));
    }

    public function testBearerTokenIsSent(): void
    {
        $this->api()->getUsersApi()->getUserById('TOK', '123');

        $this->assertSame('Bearer TOK', $this->lastRequest()->getHeaderLine('Authorization'));
    }

    // ------------------------------------------------------- GET endpoints

    public function testGetUserById(): void
    {
        $this->api()->getUsersApi()->getUserById('TOK', '44322889');

        $this->assertSent('GET', 'https://api.twitch.tv/helix/users?id=44322889');
    }

    public function testGetStreamsWithMultipleUserIds(): void
    {
        $this->api()->getStreamsApi()->getStreams('TOK', ['123', '456']);

        $this->assertSent('GET', 'https://api.twitch.tv/helix/streams?user_id=123&user_id=456');
    }

    public function testGetGamesById(): void
    {
        $this->api()->getGamesApi()->getGames('TOK', ['33214']);

        $this->assertSent('GET', 'https://api.twitch.tv/helix/games?id=33214');
    }

    public function testGetClipsByBroadcaster(): void
    {
        $this->api()->getClipsApi()->getClipsByBroadcasterId('TOK', '1234');

        $this->assertSent('GET', 'https://api.twitch.tv/helix/clips?broadcaster_id=1234');
    }

    public function testGetChannelInformation(): void
    {
        $this->api()->getChannelsApi()->getChannelInfo('TOK', '1234');

        $this->assertSent('GET', 'https://api.twitch.tv/helix/channels?broadcaster_id=1234');
    }

    public function testGetEventSubSubscriptions(): void
    {
        $this->api()->getEventSubApi()->getEventSubSubscription('TOK', 'enabled');

        $this->assertSent('GET', 'https://api.twitch.tv/helix/eventsub/subscriptions?status=enabled');
    }

    public function testSearchCategoriesDoesNotEncodeTheQuery(): void
    {
        // Known defect, fixed in 8.0: reserved characters in a value are not encoded, so
        // this reaches Twitch as three parameters rather than one. Pinned deliberately;
        // see RequestGeneratorTest for why it cannot be fixed in a minor release.
        $this->api()->getSearchApi()->searchCategories('TOK', 'Rock & Roll');

        $this->assertSent('GET', 'https://api.twitch.tv/helix/search/categories?query=Rock%20&%20Roll');
    }

    public function testAPaginationCursorContainingAPlusIsCorrupted(): void
    {
        // Known defect, fixed in 8.0. Cursors are base64, so a + is common, and without
        // encoding the receiving end reads it back as a space.
        $this->api()->getStreamsApi()->getStreams('TOK', [], [], [], [], 20, null, 'eyJiIjpudWxsL+8=');

        parse_str((string) $this->lastRequest()->getUri()->getQuery(), $query);

        $this->assertSame('eyJiIjpudWxsL 8=', $query['after']);
    }

    // ------------------------------------------------------- write endpoints

    public function testModifyChannelInfoUsesPatch(): void
    {
        // This endpoint already takes a plain associative array, which is the shape v8
        // plans to standardise on across every method.
        $this->api()->getChannelsApi()->modifyChannelInfo('TOK', '1234', ['title' => 'New title']);

        $this->assertSent('PATCH', 'https://api.twitch.tv/helix/channels?broadcaster_id=1234');
        $this->assertSame('{"title":"New title"}', (string) $this->lastRequest()->getBody());
    }

    public function testCreateEventSubSubscriptionUsesPost(): void
    {
        $this->api()->getEventSubApi()->subscribeToChannelFollow('TOK', 'secret', 'https://example.com/cb', '1234', '5678');

        $this->assertSent('POST', 'https://api.twitch.tv/helix/eventsub/subscriptions');

        $body = json_decode((string) $this->lastRequest()->getBody(), true);
        $this->assertSame('channel.follow', $body['type']);
        $this->assertSame('webhook', $body['transport']['method']);
        $this->assertSame('https://example.com/cb', $body['transport']['callback']);
    }

    public function testDeleteEventSubSubscriptionUsesDelete(): void
    {
        $this->api()->getEventSubApi()->deleteEventSubSubscription('TOK', 'sub-id');

        $this->assertSent('DELETE', 'https://api.twitch.tv/helix/eventsub/subscriptions?id=sub-id');
    }

    public function testBlockUserUsesPut(): void
    {
        $this->api()->getUsersApi()->blockUser('TOK', '1234');

        $this->assertSent('PUT', 'https://api.twitch.tv/helix/users/blocks?target_user_id=1234');
    }

    // ------------------------------------------------------- laziness is invisible here

    public function testRepeatedCallsReuseTheSameResourceInstance(): void
    {
        $api = $this->api(2);

        $streams = $api->getStreamsApi();
        $api->getStreamsApi()->getStreams('TOK', ['1']);
        $streams->getStreams('TOK', ['2']);

        $this->assertSame($streams, $api->getStreamsApi());
        $this->assertCount(2, $this->history);
    }
}
