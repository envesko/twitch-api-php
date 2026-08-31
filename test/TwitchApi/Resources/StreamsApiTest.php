<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\StreamsApi;
use TwitchApi\Tests\ResourceTestCase;

class StreamsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return StreamsApi::class;
    }

    public function testShouldGetStreamsById(): void
    {
        $this->api()->getStreams(self::TOKEN, ['123']);

        $this->assertSent('GET', 'streams', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetStreamsByIdWithHelper(): void
    {
        $this->api()->getStreamForUserId(self::TOKEN, '123');

        $this->assertSent('GET', 'streams', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetStreamsByIds(): void
    {
        $this->api()->getStreams(self::TOKEN, ['123', '321']);

        $this->assertSent('GET', 'streams', [
            ['user_id', '123'],
            ['user_id', '321'],
        ]);
    }

    public function testShouldGetStreamsByUsername(): void
    {
        $this->api()->getStreams(self::TOKEN, [], ['test']);

        $this->assertSent('GET', 'streams', [
            ['user_login', 'test'],
        ]);
    }

    public function testShouldGetStreamsByUsernameWithHelper(): void
    {
        $this->api()->getStreamForUsername(self::TOKEN, 'test');

        $this->assertSent('GET', 'streams', [
            ['user_login', 'test'],
        ]);
    }

    public function testShouldGetStreamsByUsernames(): void
    {
        $this->api()->getStreams(self::TOKEN, [], ['test', 'user']);

        $this->assertSent('GET', 'streams', [
            ['user_login', 'test'],
            ['user_login', 'user'],
        ]);
    }

    public function testShouldGetStreamsByIdAndUsername(): void
    {
        $this->api()->getStreams(self::TOKEN, ['123'], ['test']);

        $this->assertSent('GET', 'streams', [
            ['user_id', '123'],
            ['user_login', 'test'],
        ]);
    }

    public function testShouldGetStreamsByGameId(): void
    {
        $this->api()->getStreams(self::TOKEN, [], [], ['123']);

        $this->assertSent('GET', 'streams', [
            ['game_id', '123'],
        ]);
    }

    public function testShouldGetStreamsByGameIdWithHelper(): void
    {
        $this->api()->getStreamsByGameId(self::TOKEN, '123');

        $this->assertSent('GET', 'streams', [
            ['game_id', '123'],
        ]);
    }

    public function testShouldGetStreamsByGameIds(): void
    {
        $this->api()->getStreams(self::TOKEN, [], [], ['123', '456']);

        $this->assertSent('GET', 'streams', [
            ['game_id', '123'],
            ['game_id', '456'],
        ]);
    }

    public function testShouldGetStreamsByLanguage(): void
    {
        $this->api()->getStreams(self::TOKEN, [], [], [], ['en']);

        $this->assertSent('GET', 'streams', [
            ['language', 'en'],
        ]);
    }

    public function testShouldGetStreamsByLanguageWithHelper(): void
    {
        $this->api()->getStreamsByLanguage(self::TOKEN, 'en');

        $this->assertSent('GET', 'streams', [
            ['language', 'en'],
        ]);
    }

    public function testShouldGetStreamsByLanguages(): void
    {
        $this->api()->getStreams(self::TOKEN, [], [], [], ['en', 'fr']);

        $this->assertSent('GET', 'streams', [
            ['language', 'en'],
            ['language', 'fr'],
        ]);
    }

    public function testShouldGetStreamsWithOpts(): void
    {
        $this->api()->getStreams(self::TOKEN, [], [], [], [], 100, 'abc', 'def');

        $this->assertSent('GET', 'streams', [
            ['first', '100'],
            ['before', 'abc'],
            ['after', 'def'],
        ]);
    }

    public function testShouldGetStreamKey(): void
    {
        $this->api()->getStreamKey(self::TOKEN, '123');

        $this->assertSent('GET', 'streams/key', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetStreamMarkersByUserId(): void
    {
        $this->api()->getStreamMarkers(self::TOKEN, '123');

        $this->assertSent('GET', 'streams/markers', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetStreamMarkersByUserIdWithOpts(): void
    {
        $this->api()->getStreamMarkers(self::TOKEN, '123', null, '100', 'abc', 'def');

        $this->assertSent('GET', 'streams/markers', [
            ['user_id', '123'],
            ['first', '100'],
            ['before', 'abc'],
            ['after', 'def'],
        ]);
    }

    public function testShouldGetStreamMarkersByVideoId(): void
    {
        $this->api()->getStreamMarkers(self::TOKEN, null, '123');

        $this->assertSent('GET', 'streams/markers', [
            ['video_id', '123'],
        ]);
    }

    public function testShouldGetStreamMarkersByVideoIdWithOpts(): void
    {
        $this->api()->getStreamMarkers(self::TOKEN, null, '123', '100', 'abc', 'def');

        $this->assertSent('GET', 'streams/markers', [
            ['video_id', '123'],
            ['first', '100'],
            ['before', 'abc'],
            ['after', 'def'],
        ]);
    }

    public function testShouldGetFollowedStreams(): void
    {
        $this->api()->getFollowedStreams(self::TOKEN, '123');

        $this->assertSent('GET', 'streams/followed', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetFollowedStreamsWithOpts(): void
    {
        $this->api()->getFollowedStreams(self::TOKEN, '123', 100, 'abc');

        $this->assertSent('GET', 'streams/followed', [
            ['user_id', '123'],
            ['first', '100'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldCreateStreamMarker(): void
    {
        $this->api()->createStreamMarker(self::TOKEN, '123');

        $this->assertSent('POST', 'streams/markers');
        $this->assertSentBody(['user_id' => '123']);
    }

    public function testShouldCreateStreamMarkerWithDescription(): void
    {
        $this->api()->createStreamMarker(self::TOKEN, '123', 'This is a marker');

        $this->assertSent('POST', 'streams/markers');
        $this->assertSentBody(['user_id' => '123', 'description' => 'This is a marker']);
    }
}
