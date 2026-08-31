<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use NewTwitchApi\HelixGuzzleClient as LegacyHelixGuzzleClient;
use NewTwitchApi\NewTwitchApi;
use PHPUnit\Framework\TestCase;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\Resources\StreamsApi;
use TwitchApi\TwitchApi;

/**
 * The NewTwitchApi namespace was renamed to TwitchApi in 6.0.0 and these aliases were added
 * so the old names kept working. They are two six-line classes, so keeping them costs almost
 * nothing and removing them would break anyone who has not migrated in the years since.
 *
 * These tests exist so that stays a decision rather than an accident.
 */
class LegacyNamespaceTest extends TestCase
{
    public function testTheLegacyClientStillResolves(): void
    {
        $client = new LegacyHelixGuzzleClient('TEST_CLIENT_ID');

        $this->assertInstanceOf(HelixGuzzleClient::class, $client);
    }

    public function testTheLegacyFacadeStillResolves(): void
    {
        $api = new NewTwitchApi(
            new LegacyHelixGuzzleClient('TEST_CLIENT_ID'),
            'TEST_CLIENT_ID',
            'TEST_CLIENT_SECRET'
        );

        $this->assertInstanceOf(TwitchApi::class, $api);
    }

    public function testTheLegacyFacadeStillReachesTheResources(): void
    {
        $api = new NewTwitchApi(
            new LegacyHelixGuzzleClient('TEST_CLIENT_ID'),
            'TEST_CLIENT_ID',
            'TEST_CLIENT_SECRET'
        );

        $this->assertInstanceOf(StreamsApi::class, $api->getStreamsApi());
    }
}
