<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\AdsApi;
use TwitchApi\Tests\ResourceTestCase;

class AdsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return AdsApi::class;
    }

    public function testShouldStartCommercial(): void
    {
        $this->api()->startCommercial(self::TOKEN, '123', 30);

        $this->assertSent('POST', 'channels/commercial');
        $this->assertSentBody(['broadcaster_id' => '123', 'length' => 30]);
    }

    public function testShouldGetTheAdSchedule(): void
    {
        $this->api()->getAdSchedule(self::TOKEN, '123');

        $this->assertSent('GET', 'channels/ads', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldSnoozeTheNextAd(): void
    {
        $this->api()->snoozeNextAd(self::TOKEN, '123');

        $this->assertSent('POST', 'channels/ads/schedule/snooze', [
            ['broadcaster_id', '123'],
        ]);
    }
}
