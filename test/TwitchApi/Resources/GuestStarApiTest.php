<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\GuestStarApi;
use TwitchApi\Tests\ResourceTestCase;

class GuestStarApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return GuestStarApi::class;
    }

    public function testShouldGetChannelGuestStarSettings(): void
    {
        $this->api()->getChannelGuestStarSettings(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'guest_star/channel_settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldUpdateChannelGuestStarSettings(): void
    {
        $this->api()->updateChannelGuestStarSettings(self::TOKEN, '123', ['is_moderator_send_live_enabled' => true]);

        $this->assertSent('PUT', 'guest_star/channel_settings', [
            ['broadcaster_id', '123'],
        ]);
        $this->assertSentBody(['is_moderator_send_live_enabled' => true]);
    }

    public function testShouldGetAGuestStarSession(): void
    {
        $this->api()->getGuestStarSession(self::TOKEN, '123', '456');

        $this->assertSent('GET', 'guest_star/session', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
        ]);
    }

    public function testShouldCreateAGuestStarSession(): void
    {
        $this->api()->createGuestStarSession(self::TOKEN, '123');

        $this->assertSent('POST', 'guest_star/session', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldEndAGuestStarSession(): void
    {
        $this->api()->endGuestStarSession(self::TOKEN, '123', 'sess');

        $this->assertSent('DELETE', 'guest_star/session', [
            ['broadcaster_id', '123'],
            ['session_id', 'sess'],
        ]);
    }

    public function testShouldGetGuestStarInvites(): void
    {
        $this->api()->getGuestStarInvites(self::TOKEN, '123', '456', 'sess');

        $this->assertSent('GET', 'guest_star/invites', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
        ]);
    }

    public function testShouldSendAGuestStarInvite(): void
    {
        $this->api()->sendGuestStarInvite(self::TOKEN, '123', '456', 'sess', '789');

        $this->assertSent('POST', 'guest_star/invites', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
            ['guest_id', '789'],
        ]);
    }

    public function testShouldDeleteAGuestStarInvite(): void
    {
        $this->api()->deleteGuestStarInvite(self::TOKEN, '123', '456', 'sess', '789');

        $this->assertSent('DELETE', 'guest_star/invites', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
            ['guest_id', '789'],
        ]);
    }

    public function testShouldAssignAGuestStarSlot(): void
    {
        $this->api()->assignGuestStarSlot(self::TOKEN, '123', '456', 'sess', '789', '1');

        $this->assertSent('POST', 'guest_star/slot', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
            ['guest_id', '789'],
            ['slot_id', '1'],
        ]);
    }

    public function testShouldUpdateAGuestStarSlot(): void
    {
        $this->api()->updateGuestStarSlot(self::TOKEN, '123', '456', 'sess', '1', '2');

        $this->assertSent('PATCH', 'guest_star/slot', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
            ['source_slot_id', '1'],
            ['destination_slot_id', '2'],
        ]);
    }

    public function testShouldDeleteAGuestStarSlot(): void
    {
        $this->api()->deleteGuestStarSlot(self::TOKEN, '123', '456', 'sess', '789', '1');

        $this->assertSent('DELETE', 'guest_star/slot', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
            ['guest_id', '789'],
            ['slot_id', '1'],
        ]);
    }

    public function testShouldUpdateGuestStarSlotSettings(): void
    {
        $this->api()->updateGuestStarSlotSettings(self::TOKEN, '123', '456', 'sess', '1', ['is_audio_enabled' => true]);

        $this->assertSent('PATCH', 'guest_star/slot_settings', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
            ['slot_id', '1'],
            ['is_audio_enabled', '1'],
        ]);
    }

    public function testShouldDeleteAGuestStarSlotAndReinvite(): void
    {
        $this->api()->deleteGuestStarSlot(self::TOKEN, '123', '456', 'sess', '789', '1', 'true');

        $this->assertSent('DELETE', 'guest_star/slot', [
            ['broadcaster_id', '123'],
            ['moderator_id', '456'],
            ['session_id', 'sess'],
            ['guest_id', '789'],
            ['slot_id', '1'],
            ['should_reinvite_guest', 'true'],
        ]);
    }
}
