<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use TwitchApi\Auth\OauthApi;

/**
 * The OAuth endpoints, asserted by the request that leaves the library.
 */
class OauthApiTest extends TestCase
{
    private const CLIENT_ID = 'client-id';
    private const CLIENT_SECRET = 'client-secret';

    /** @var array<int, array<string, mixed>> */
    private array $history = [];

    private function api(int $status = 200, string $body = '{}'): OauthApi
    {
        $this->history = [];
        $stack = HandlerStack::create(new MockHandler([new Response($status, [], $body)]));
        $stack->push(Middleware::history($this->history));

        $client = new Client([
            'base_uri' => 'https://id.twitch.tv/oauth2/',
            'handler' => $stack,
        ]);

        return new OauthApi(self::CLIENT_ID, self::CLIENT_SECRET, $client);
    }

    private function lastRequest(): RequestInterface
    {
        $this->assertNotEmpty($this->history, 'No request was sent.');

        return $this->history[count($this->history) - 1]['request'];
    }

    /**
     * @return array<string, mixed>
     */
    private function sentJson(): array
    {
        return json_decode((string) $this->lastRequest()->getBody(), true) ?? [];
    }

    public function testGetAuthUrl(): void
    {
        $this->assertSame(
            'https://id.twitch.tv/oauth2/authorize?client_id=client-id&redirect_uri=https://redirect.url&response_type=code&scope=',
            $this->api()->getAuthUrl('https://redirect.url')
        );
    }

    public function testGetUserAccessTokenPostsTheAuthorizationCode(): void
    {
        $this->api()->getUserAccessToken('user-code-from-twitch', 'https://redirect.url');

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/oauth2/token', $this->lastRequest()->getUri()->getPath());
        $this->assertSame([
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'grant_type' => 'authorization_code',
            'redirect_uri' => 'https://redirect.url',
            'code' => 'user-code-from-twitch',
            'state' => null,
        ], $this->sentJson());
    }

    public function testRefreshToken(): void
    {
        $this->api()->refreshToken('user-refresh-token');

        $this->assertSame([
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'grant_type' => 'refresh_token',
            'refresh_token' => 'user-refresh-token',
        ], $this->sentJson());
    }

    public function testRefreshTokenWithAScope(): void
    {
        $this->api()->refreshToken('user-refresh-token', 'user:read:email');

        $this->assertSame([
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'grant_type' => 'refresh_token',
            'refresh_token' => 'user-refresh-token',
            'scope' => 'user:read:email',
        ], $this->sentJson());
    }

    public function testGetAppAccessToken(): void
    {
        $this->api()->getAppAccessToken();

        $this->assertSame([
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'grant_type' => 'client_credentials',
            'scope' => '',
        ], $this->sentJson());
    }

    public function testValidateAccessTokenSendsTheOauthHeader(): void
    {
        $this->api()->validateAccessToken('user-access-token');

        $request = $this->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/oauth2/validate', $request->getUri()->getPath());
        $this->assertSame('OAuth user-access-token', $request->getHeaderLine('Authorization'));
    }

    public function testIsValidAccessTokenIsTrueOn200(): void
    {
        $this->assertTrue($this->api(200)->isValidAccessToken('user-access-token'));
    }

    public function testIsValidAccessTokenThrowsOn401RatherThanReturningFalse(): void
    {
        // Documented here as it currently behaves, not as it should. Guzzle raises on a 4xx
        // before the status code can be read, so this method can only ever return true or
        // throw. The spec this replaced mocked the response and never saw it. Fixed in the
        // next commit.
        $this->expectException(\GuzzleHttp\Exception\ClientException::class);

        $this->api(401)->isValidAccessToken('invalid-user-access-token');
    }
}
