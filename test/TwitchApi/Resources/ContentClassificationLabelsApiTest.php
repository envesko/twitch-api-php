<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ContentClassificationLabelsApi;
use TwitchApi\Tests\ResourceTestCase;

class ContentClassificationLabelsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ContentClassificationLabelsApi::class;
    }

    public function testShouldGetContentClassificationLabels(): void
    {
        $this->api()->getContentClassificationLabels(self::TOKEN);

        $this->assertSent('GET', 'content_classification_labels');
    }

    public function testShouldGetContentClassificationLabelsForALocale(): void
    {
        $this->api()->getContentClassificationLabels(self::TOKEN, 'en-US');

        $this->assertSent('GET', 'content_classification_labels', [
            ['locale', 'en-US'],
        ]);
    }
}
