<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\VideosApi;
use TwitchApi\Tests\ResourceTestCase;

class VideosApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return VideosApi::class;
    }

    public function testShouldGetVideoById(): void
    {
        $this->api()->getVideos(self::TOKEN, ['123']);

        $this->assertSent('GET', 'videos', [
            ['id', '123'],
        ]);
    }

    public function testShouldGetVideosById(): void
    {
        $this->api()->getVideos(self::TOKEN, ['123', '321']);

        $this->assertSent('GET', 'videos', [
            ['id', '123'],
            ['id', '321'],
        ]);
    }

    public function testShouldGetVideosByUserId(): void
    {
        $this->api()->getVideos(self::TOKEN, [], '123');

        $this->assertSent('GET', 'videos', [
            ['user_id', '123'],
        ]);
    }

    public function testShouldGetVideosByGameId(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, '123');

        $this->assertSent('GET', 'videos', [
            ['game_id', '123'],
        ]);
    }

    public function testShouldGetVideosWithFirst(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, null, '100');

        $this->assertSent('GET', 'videos', [
            ['first', '100'],
        ]);
    }

    public function testShouldGetVideosWithBefore(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, null, null, 'abc');

        $this->assertSent('GET', 'videos', [
            ['before', 'abc'],
        ]);
    }

    public function testShouldGetVideosWithAfter(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, null, null, null, 'cba');

        $this->assertSent('GET', 'videos', [
            ['after', 'cba'],
        ]);
    }

    public function testShouldGetVideosWithLanguage(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, null, null, null, null, 'en');

        $this->assertSent('GET', 'videos', [
            ['language', 'en'],
        ]);
    }

    public function testShouldGetVideosWithPeriod(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, null, null, null, null, null, 'all');

        $this->assertSent('GET', 'videos', [
            ['period', 'all'],
        ]);
    }

    public function testShouldGetVideosWithSort(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, null, null, null, null, null, null, 'trending');

        $this->assertSent('GET', 'videos', [
            ['sort', 'trending'],
        ]);
    }

    public function testShouldGetVideosWithType(): void
    {
        $this->api()->getVideos(self::TOKEN, [], null, null, null, null, null, null, null, null, 'all');

        $this->assertSent('GET', 'videos', [
            ['type', 'all'],
        ]);
    }

    public function testShouldGetVideosWithEverything(): void
    {
        $this->api()->getVideos(self::TOKEN, [], '123', '321', '100', 'abc', 'def', 'en', 'all', 'trending', 'all');

        $this->assertSent('GET', 'videos', [
            ['user_id', '123'],
            ['game_id', '321'],
            ['first', '100'],
            ['before', 'abc'],
            ['after', 'def'],
            ['language', 'en'],
            ['period', 'all'],
            ['sort', 'trending'],
            ['type', 'all'],
        ]);
    }

    public function testShouldDeleteVideos(): void
    {
        $this->api()->deleteVideos(self::TOKEN, ['123']);

        $this->assertSent('DELETE', 'videos', [
            ['id', '123'],
        ]);
    }

    public function testShouldDeleteMultipleVideos(): void
    {
        $this->api()->deleteVideos(self::TOKEN, ['123', '321']);

        $this->assertSent('DELETE', 'videos', [
            ['id', '123'],
            ['id', '321'],
        ]);
    }
}
