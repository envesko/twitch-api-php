<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;

/**
 * Base for the resource tests.
 *
 * These replace the phpspec suite. The specs asserted that a method handed the right argument
 * map to RequestGenerator, which could not catch a wrong endpoint, a wrong verb, or a value
 * that never reached the URL. These drive the real generator and a real client and read the
 * request back off a Guzzle history middleware.
 *
 * Query expectations are written as plain ordered key and value pairs, and the actual query is
 * decoded before comparison, so the assertion does not restate the encoding logic it is
 * testing.
 */
abstract class ResourceTestCase extends TestCase
{
    protected const TOKEN = 'TEST_TOKEN';
    protected const CLIENT_ID = 'TEST_CLIENT_ID';

    /** @var array<int, array<string, mixed>> */
    private array $history = [];

    /**
     * The resource class under test.
     *
     * @return class-string
     */
    abstract protected function resourceClass(): string;

    protected function api(int $responses = 1): object
    {
        $this->history = [];

        $stack = HandlerStack::create(
            new MockHandler(array_fill(0, max(1, $responses), new Response(200, [], '{"data":[]}')))
        );
        $stack->push(Middleware::history($this->history));

        $class = $this->resourceClass();

        return new $class(
            new HelixGuzzleClient(self::CLIENT_ID, ['handler' => $stack]),
            new RequestGenerator()
        );
    }

    protected function lastRequest(): RequestInterface
    {
        $this->assertNotEmpty($this->history, 'No request was sent.');

        return $this->history[count($this->history) - 1]['request'];
    }

    /**
     * @param list<array{0: string, 1: string}> $expectedQuery ordered key and value pairs
     */
    protected function assertSent(string $method, string $endpoint, array $expectedQuery = []): void
    {
        $request = $this->lastRequest();

        $this->assertSame($method, $request->getMethod(), 'HTTP method');
        $this->assertSame(
            '/helix/'.$endpoint,
            $request->getUri()->getPath(),
            'endpoint path'
        );
        $this->assertSame($expectedQuery, $this->actualQuery($request), 'query parameters');
    }

    /**
     * Decoded, ordered and duplicate-preserving, which parse_str is not.
     *
     * rawurldecode rather than urldecode: the library does not encode values, so a literal +
     * in a value reaches the wire as +, and urldecode would read it back as a space.
     *
     * @return list<array{0: string, 1: string}>
     */
    private function actualQuery(RequestInterface $request): array
    {
        $query = $request->getUri()->getQuery();

        if ($query === '') {
            return [];
        }

        $pairs = [];
        foreach (explode('&', $query) as $pair) {
            $parts = explode('=', $pair, 2);
            $pairs[] = [rawurldecode($parts[0]), rawurldecode($parts[1] ?? '')];
        }

        return $pairs;
    }

    /**
     * @param array<string, mixed> $expected
     */
    protected function assertSentBody(array $expected): void
    {
        $this->assertSame(
            $expected,
            json_decode((string) $this->lastRequest()->getBody(), true),
            'request body'
        );
    }

    protected function assertSentNoBody(): void
    {
        $this->assertSame('', (string) $this->lastRequest()->getBody(), 'request body');
    }
}
