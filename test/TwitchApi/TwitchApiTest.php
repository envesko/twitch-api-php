<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\TwitchApi;

/**
 * The facade: every getter hands back the resource it names, and hands back the same one
 * each time. Resources are built on first use rather than in the constructor, so "the same
 * one each time" is the property that keeps that invisible to callers.
 */
class TwitchApiTest extends TestCase
{
    private function api(): TwitchApi
    {
        return new TwitchApi(
            new HelixGuzzleClient('TEST_CLIENT_ID'),
            'TEST_CLIENT_ID',
            'TEST_CLIENT_SECRET'
        );
    }

    #[DataProvider('getters')]
    public function testGetterReturnsItsResource(string $getter, string $class): void
    {
        $this->assertInstanceOf($class, $this->api()->$getter());
    }

    #[DataProvider('getters')]
    public function testGetterReturnsTheSameInstanceEachTime(string $getter, string $class): void
    {
        $api = $this->api();

        $this->assertSame($api->$getter(), $api->$getter());
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function getters(): array
    {
        $cases = [];

        foreach (['Oauth' => 'Auth\\OauthApi'] as $name => $class) {
            $cases[$name] = ['get'.$name.'Api', 'TwitchApi\\'.$class];
        }

        $resources = [
            'Ads', 'Analytics', 'Bits', 'ChannelPoints', 'Channels', 'Charity', 'Chat', 'Clips',
            'Conduits', 'ContentClassificationLabels', 'Entitlements', 'EventSub', 'Extensions',
            'Games', 'Goals', 'GuestStar', 'HypeTrain', 'Moderation', 'Polls', 'Predictions',
            'Raids', 'Schedule', 'Search', 'SharedChat', 'Streams', 'Subscriptions', 'Tags',
            'Teams', 'Users', 'Videos', 'Whispers',
        ];

        foreach ($resources as $name) {
            $cases[$name] = ['get'.$name.'Api', 'TwitchApi\\Resources\\'.$name.'Api'];
        }

        return $cases;
    }

    public function testEveryGetterIsCovered(): void
    {
        $declared = array_filter(
            get_class_methods(TwitchApi::class),
            static fn (string $m): bool => str_starts_with($m, 'get')
        );

        $covered = array_map(static fn (array $c): string => $c[0], array_values(self::getters()));

        sort($declared);
        sort($covered);

        $this->assertSame($declared, $covered, 'a getter was added without a case here');
    }
}
