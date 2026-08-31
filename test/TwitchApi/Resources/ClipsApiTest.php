<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ClipsApi;
use TwitchApi\Tests\ResourceTestCase;

class ClipsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ClipsApi::class;
    }

    public function testShouldGetClipsByBroadcasterId(): void
    {
        $this->api()->getClips(self::TOKEN, '123');

        $this->assertSent('GET', 'clips', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetClipsByBroadcasterIdWithHelperFunction(): void
    {
        $this->api()->getClipsByBroadcasterId(self::TOKEN, '123');

        $this->assertSent('GET', 'clips', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetClipsByGameId(): void
    {
        $this->api()->getClips(self::TOKEN, null, '123');

        $this->assertSent('GET', 'clips', [
            ['game_id', '123'],
        ]);
    }

    public function testShouldGetClipsByGameIdWithHelperFunction(): void
    {
        $this->api()->getClipsByGameId(self::TOKEN, '123');

        $this->assertSent('GET', 'clips', [
            ['game_id', '123'],
        ]);
    }

    public function testShouldGetOneClipById(): void
    {
        $this->api()->getClips(self::TOKEN, null, null, '123');

        $this->assertSent('GET', 'clips', [
            ['id', '123'],
        ]);
    }

    public function testShouldGetOneClipByIdWithHelperFunction(): void
    {
        $this->api()->getClipsByIds(self::TOKEN, '123');

        $this->assertSent('GET', 'clips', [
            ['id', '123'],
        ]);
    }

    public function testShouldGetMultipleClipsById(): void
    {
        $this->api()->getClips(self::TOKEN, null, null, '123,456');

        $this->assertSent('GET', 'clips', [
            ['id', '123,456'],
        ]);
    }

    public function testShouldGetMultipleClipsByIdWithHelperFunction(): void
    {
        $this->api()->getClipsByIds(self::TOKEN, '123,456');

        $this->assertSent('GET', 'clips', [
            ['id', '123,456'],
        ]);
    }

    public function testShouldGetClipsWithOpts(): void
    {
        $this->api()->getClips(self::TOKEN, '123', null, null, 10, 'abc', 'def', '2018-10-12T07:20:50.52Z', '2019-10-12T07:20:50.52Z');

        $this->assertSent('GET', 'clips', [
            ['broadcaster_id', '123'],
            ['first', '10'],
            ['before', 'abc'],
            ['after', 'def'],
            ['started_at', '2018-10-12T07:20:50.52Z'],
            ['ended_at', '2019-10-12T07:20:50.52Z'],
        ]);
    }

    public function testShouldCreateAClip(): void
    {
        $this->api()->createClip(self::TOKEN, '123', true);

        $this->assertSent('POST', 'clips', [
            ['broadcaster_id', '123'],
            ['has_delay', '1'],
        ]);
    }

    public function testShouldGetClipsDownload(): void
    {
        $this->api()->getClipsDownload(self::TOKEN, '123');

        $this->assertSent('GET', 'clips/downloads', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldCreateAClipFromAVod(): void
    {
        $this->api()->createClipFromVod(self::TOKEN, 'vid', 30);

        $this->assertSent('POST', 'videos/clips');
        $this->assertSentBody(['video_id' => 'vid', 'offset_seconds' => 30]);
    }

    public function testShouldGetClipsDownloadWithinAWindow(): void
    {
        $this->api()->getClipsDownload(self::TOKEN, '123', '2026-01-01T00:00:00Z', '2026-02-01T00:00:00Z', 20, 'cursor');

        $this->assertSent('GET', 'clips/downloads', [
            ['broadcaster_id', '123'],
            ['started_at', '2026-01-01T00:00:00Z'],
            ['ended_at', '2026-02-01T00:00:00Z'],
            ['first', '20'],
            ['after', 'cursor'],
        ]);
    }

    public function testShouldCreateAClipFromAVodWithADuration(): void
    {
        $this->api()->createClipFromVod(self::TOKEN, 'vid', 30, 60);

        $this->assertSent('POST', 'videos/clips');
        $this->assertSentBody(['video_id' => 'vid', 'offset_seconds' => 30, 'duration_seconds' => 60]);
    }
}
