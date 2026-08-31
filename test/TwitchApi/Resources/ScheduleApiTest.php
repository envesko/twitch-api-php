<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\ScheduleApi;
use TwitchApi\Tests\ResourceTestCase;

class ScheduleApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return ScheduleApi::class;
    }

    public function testShouldGetChannelStreamSchedule(): void
    {
        $this->api()->getChannelStreamSchedule(self::TOKEN, '123');

        $this->assertSent('GET', 'schedule', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetChannelStreamScheduleWithOpts(): void
    {
        $this->api()->getChannelStreamSchedule(self::TOKEN, '123', [], '2021-06-15T23:08:20+00:00', '240', 25, 'abc');

        $this->assertSent('GET', 'schedule', [
            ['broadcaster_id', '123'],
            ['start_time', '2021-06-15T23:08:20+00:00'],
            ['utc_offset', '240'],
            ['first', '25'],
            ['after', 'abc'],
        ]);
    }

    public function testShouldGetAChannelStreamSchedule(): void
    {
        $this->api()->getChannelStreamSchedule(self::TOKEN, '123', ['456']);

        $this->assertSent('GET', 'schedule', [
            ['broadcaster_id', '123'],
            ['id', '456'],
        ]);
    }

    public function testShouldGetMultipleChannelStreamSchedules(): void
    {
        $this->api()->getChannelStreamSchedule(self::TOKEN, '123', ['456', '789']);

        $this->assertSent('GET', 'schedule', [
            ['broadcaster_id', '123'],
            ['id', '456'],
            ['id', '789'],
        ]);
    }

    public function testShouldGetChannelIcalendarWithNoAuth(): void
    {
        $this->api()->getChanneliCalendar(null, '123');

        $this->assertSent('GET', 'schedule/icalendar', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetChannelIcalendarWithAuth(): void
    {
        $this->api()->getChanneliCalendar(self::TOKEN, '123');

        $this->assertSent('GET', 'schedule/icalendar', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldUpdateChannelStreamSchedule(): void
    {
        $this->api()->updateChannelStreamSchedule(self::TOKEN, '123', true, '2021-06-15T23:08:20+00:00', '2021-06-22T23:08:20+00:00', 'America/New_York');

        $this->assertSent('PATCH', 'schedule/settings', [
            ['broadcaster_id', '123'],
            ['is_vacation_enabled', '1'],
            ['vacation_start_time', '2021-06-15T23:08:20+00:00'],
            ['vacation_end_time', '2021-06-22T23:08:20+00:00'],
            ['timezone', 'America/New_York'],
        ]);
    }

    public function testShouldCreateChannelStreamScheduleSegment(): void
    {
        $this->api()->createChannelStreamScheduleSegment(self::TOKEN, '123', '2021-06-15T23:08:20+00:00', 'America/New_York', true);

        $this->assertSent('POST', 'schedule/segment', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['start_time' => '2021-06-15T23:08:20+00:00', 'timezone' => 'America/New_York', 'is_recurring' => true]);
    }

    public function testShouldCreateChannelStreamScheduleSegmentWithOpts(): void
    {
        $this->api()->createChannelStreamScheduleSegment(self::TOKEN, '123', '2021-06-15T23:08:20+00:00', 'America/New_York', true, ['duration' => '240']);

        $this->assertSent('POST', 'schedule/segment', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['start_time' => '2021-06-15T23:08:20+00:00', 'timezone' => 'America/New_York', 'is_recurring' => true, 'duration' => '240']);
    }

    public function testShouldUpdateChannelStreamScheduleSegment(): void
    {
        $this->api()->updateChannelStreamScheduleSegment(self::TOKEN, '123', '456', ['start_time' => '2021-06-15T23:08:20+00:00', 'timezone' => 'America/New_York', 'is_canceled' => true, 'duration' => '240']);

        $this->assertSent('PATCH', 'schedule/segment', [
            ['broadcaster_id', '123'],
            ['id', '456'],
        ]);
        $this->assertSentBody(['start_time' => '2021-06-15T23:08:20+00:00', 'timezone' => 'America/New_York', 'is_canceled' => true, 'duration' => '240']);
    }

    public function testShouldDeleteChannelStreamScheduleSegment(): void
    {
        $this->api()->deleteChannelStreamScheduleSegment(self::TOKEN, '123', '456');

        $this->assertSent('DELETE', 'schedule/segment', [
            ['broadcaster_id', '123'],
            ['id', '456'],
        ]);
    }
}
