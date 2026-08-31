<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\TagsApi;
use TwitchApi\Tests\ResourceTestCase;

class TagsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return TagsApi::class;
    }

    public function testShouldGetAllTags(): void
    {
        $this->api()->getAllStreamTags(self::TOKEN);

        $this->assertSent('GET', 'tags/streams');
    }

    public function testShouldGetAllTagsById(): void
    {
        $this->api()->getAllStreamTags(self::TOKEN, ['123']);

        $this->assertSent('GET', 'tags/streams', [
            ['tag_id', '123'],
        ]);
    }

    public function testShouldGetAllTagsWithFirst(): void
    {
        $this->api()->getAllStreamTags(self::TOKEN, [], 100);

        $this->assertSent('GET', 'tags/streams', [
            ['first', '100'],
        ]);
    }

    public function testShouldGetAllTagsWithAfter(): void
    {
        $this->api()->getAllStreamTags(self::TOKEN, [], null, 'abc');

        $this->assertSent('GET', 'tags/streams', [
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetStreamTags(): void
    {
        $this->api()->getStreamTags(self::TOKEN, '123');

        $this->assertSent('GET', 'streams/tags', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldReplaceStreamTags(): void
    {
        $this->api()->replaceStreamTags(self::TOKEN, '123');

        $this->assertSent('PUT', 'streams/tags', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldReplaceStreamTagsWithOneTag(): void
    {
        $this->api()->replaceStreamTags(self::TOKEN, '123', ['456']);

        $this->assertSent('PUT', 'streams/tags', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['tag_ids' => ['456']]);
    }

    public function testShouldReplaceStreamTagsWithMultipleTags(): void
    {
        $this->api()->replaceStreamTags(self::TOKEN, '123', ['456', '789']);

        $this->assertSent('PUT', 'streams/tags', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['tag_ids' => ['456', '789']]);
    }
}
