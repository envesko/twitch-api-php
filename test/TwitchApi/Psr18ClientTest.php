<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TwitchApi\Exception\NotFoundException;
use TwitchApi\Exception\TwitchApiException;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;
use TwitchApi\Resources\UsersApi;

/**
 * A resource class accepts any PSR-18 client, not only HelixGuzzleClient.
 *
 * The client below is deliberately not Guzzle. It implements the interface and nothing else,
 * which is the point: the library must not require Guzzle to be the transport.
 */
class Psr18ClientTest extends TestCase
{
    public function testAResourceAcceptsAThirdPartyPsr18Client(): void
    {
        $client = $this->psr18Client(200, '{"data":[{"id":"1"}]}');

        $response = (new UsersApi($client, new RequestGenerator()))->getUserById('TOK', '1');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('GET', $client->lastRequest->getMethod());
        $this->assertStringContainsString('users?id=1', (string) $client->lastRequest->getUri());
    }

    public function testAPsr18ClientFailsWithTheSameTypedException(): void
    {
        // PSR-18 says a client returns the response whatever the status, so nothing is thrown
        // at the transport. The library still has to fail the way it always has.
        $client = $this->psr18Client(404, '{"error":"Not Found"}');

        $this->expectException(NotFoundException::class);

        (new UsersApi($client, new RequestGenerator()))->getUserById('TOK', 'missing');
    }

    public function testThatExceptionIsStillATwitchApiException(): void
    {
        $client = $this->psr18Client(503, '{}');

        try {
            (new UsersApi($client, new RequestGenerator()))->getUserById('TOK', '1');
            $this->fail('expected a failure');
        } catch (TwitchApiException $e) {
            $this->assertStringContainsString('503', $e->getMessage());
        }
    }

    public function testHelixGuzzleClientIsItselfAPsr18Client(): void
    {
        $this->assertInstanceOf(ClientInterface::class, new HelixGuzzleClient('TEST_CLIENT_ID'));
    }

    private function psr18Client(int $status, string $body): ClientInterface
    {
        return new class($status, $body) implements ClientInterface {
            public ?RequestInterface $lastRequest = null;

            public function __construct(private int $status, private string $body)
            {
            }

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->lastRequest = $request;

                return new \GuzzleHttp\Psr7\Response($this->status, [], $this->body);
            }
        };
    }
}
