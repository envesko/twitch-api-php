<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use TwitchApi\Exception\AuthenticationException;
use TwitchApi\Exception\AuthorizationException;
use TwitchApi\Exception\NotFoundException;
use TwitchApi\Exception\RateLimitException;
use TwitchApi\Exception\ServerException;
use TwitchApi\Exception\TwitchApiException;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;
use TwitchApi\Resources\UsersApi;

/**
 * The typed exceptions, and the compatibility that lets them be introduced without a break.
 */
class ExceptionTest extends TestCase
{
    /**
     * @param array<string, string> $headers
     */
    private function failWith(int $status, array $headers = []): \Throwable
    {
        $stack = HandlerStack::create(new MockHandler([new Response($status, $headers, '{}')]));
        $api = new UsersApi(
            new HelixGuzzleClient('TEST_CLIENT_ID', ['handler' => $stack]),
            new RequestGenerator()
        );

        try {
            $api->getUserById('TOK', '1');
        } catch (\Throwable $e) {
            return $e;
        }

        $this->fail('expected a failure for status '.$status);
    }

    /**
     * @return array<string, array{0: int, 1: class-string}>
     */
    public static function statuses(): array
    {
        return [
            '401 authentication' => [401, AuthenticationException::class],
            '403 authorization' => [403, AuthorizationException::class],
            '404 not found' => [404, NotFoundException::class],
            '429 rate limit' => [429, RateLimitException::class],
            '500 server' => [500, ServerException::class],
            '503 server' => [503, ServerException::class],
        ];
    }

    #[DataProvider('statuses')]
    public function testStatusMapsToItsException(int $status, string $expected): void
    {
        $this->assertInstanceOf($expected, $this->failWith($status));
    }

    #[DataProvider('statuses')]
    public function testEveryTypedExceptionIsATwitchApiException(int $status, string $expected): void
    {
        $this->assertInstanceOf(TwitchApiException::class, $this->failWith($status));
    }

    /**
     * The whole point of extending Guzzle's classes rather than replacing them.
     */
    #[DataProvider('statuses')]
    public function testCodeWrittenAgainstGuzzleStillCatches(int $status, string $expected): void
    {
        $e = $this->failWith($status);

        $this->assertInstanceOf(GuzzleException::class, $e, 'a 7.x catch for GuzzleException must still match');
        $this->assertInstanceOf(ClientExceptionInterface::class, $e, 'the PSR-18 catch must still match');
    }

    public function testAStatusWithNoTypedEquivalentIsLeftAlone(): void
    {
        // 418 has no mapping, so the original Guzzle exception passes through untouched.
        $e = $this->failWith(418);

        $this->assertInstanceOf(GuzzleException::class, $e);
        $this->assertNotInstanceOf(TwitchApiException::class, $e);
    }

    public function testRateLimitExceptionCarriesTheHeaders(): void
    {
        $resetsAt = time() + 45;

        $e = $this->failWith(429, [
            'Ratelimit-Limit' => '800',
            'Ratelimit-Remaining' => '0',
            'Ratelimit-Reset' => (string) $resetsAt,
        ]);

        $this->assertInstanceOf(RateLimitException::class, $e);

        $rateLimit = $e->getRateLimit();
        $this->assertNotNull($rateLimit);
        $this->assertSame(800, $rateLimit->getLimit());
        $this->assertSame(0, $rateLimit->getRemaining());
        $this->assertSame($resetsAt, $rateLimit->getResetsAt());
        $this->assertTrue($rateLimit->isExhausted());

        // Whole seconds, so allow for the clock ticking during the test.
        $this->assertLessThanOrEqual(45, $e->getRetryAfter());
        $this->assertGreaterThan(40, $e->getRetryAfter());
    }

    public function testRateLimitExceptionWithoutHeadersHasNoRateLimit(): void
    {
        $e = $this->failWith(429);

        $this->assertInstanceOf(RateLimitException::class, $e);
        $this->assertNull($e->getRateLimit());
        $this->assertNull($e->getRetryAfter());
    }

    public function testTheOriginalExceptionIsKeptAsThePrevious(): void
    {
        $this->assertInstanceOf(GuzzleException::class, $this->failWith(404)->getPrevious());
    }
}
