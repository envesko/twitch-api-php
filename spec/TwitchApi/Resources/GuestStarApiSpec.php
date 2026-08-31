<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class GuestStarApiSpec extends ObjectBehavior
{
    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_get_channel_guest_star_settings(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'guest_star/channel_settings', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456']], [])->willReturn($request);
        $this->getChannelGuestStarSettings('TEST_TOKEN', '123', '456')->shouldBe($response);
    }

    function it_should_update_channel_guest_star_settings(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('PUT', 'guest_star/channel_settings', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [['key' => 'is_moderator_send_live_enabled', 'value' => true]])->willReturn($request);
        $this->updateChannelGuestStarSettings('TEST_TOKEN', '123', ['is_moderator_send_live_enabled' => true])->shouldBe($response);
    }

    function it_should_get_a_guest_star_session(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'guest_star/session', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456']], [])->willReturn($request);
        $this->getGuestStarSession('TEST_TOKEN', '123', '456')->shouldBe($response);
    }

    function it_should_create_a_guest_star_session(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'guest_star/session', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->createGuestStarSession('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_end_a_guest_star_session(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('DELETE', 'guest_star/session', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'session_id', 'value' => 'sess']], [])->willReturn($request);
        $this->endGuestStarSession('TEST_TOKEN', '123', 'sess')->shouldBe($response);
    }

    function it_should_get_guest_star_invites(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'guest_star/invites', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess']], [])->willReturn($request);
        $this->getGuestStarInvites('TEST_TOKEN', '123', '456', 'sess')->shouldBe($response);
    }

    function it_should_send_a_guest_star_invite(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'guest_star/invites', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess'], ['key' => 'guest_id', 'value' => '789']], [])->willReturn($request);
        $this->sendGuestStarInvite('TEST_TOKEN', '123', '456', 'sess', '789')->shouldBe($response);
    }

    function it_should_delete_a_guest_star_invite(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('DELETE', 'guest_star/invites', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess'], ['key' => 'guest_id', 'value' => '789']], [])->willReturn($request);
        $this->deleteGuestStarInvite('TEST_TOKEN', '123', '456', 'sess', '789')->shouldBe($response);
    }

    function it_should_assign_a_guest_star_slot(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'guest_star/slot', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess'], ['key' => 'guest_id', 'value' => '789'], ['key' => 'slot_id', 'value' => '1']], [])->willReturn($request);
        $this->assignGuestStarSlot('TEST_TOKEN', '123', '456', 'sess', '789', '1')->shouldBe($response);
    }

    function it_should_update_a_guest_star_slot(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('PATCH', 'guest_star/slot', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess'], ['key' => 'source_slot_id', 'value' => '1'], ['key' => 'destination_slot_id', 'value' => '2']], [])->willReturn($request);
        $this->updateGuestStarSlot('TEST_TOKEN', '123', '456', 'sess', '1', '2')->shouldBe($response);
    }

    function it_should_delete_a_guest_star_slot(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('DELETE', 'guest_star/slot', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess'], ['key' => 'guest_id', 'value' => '789'], ['key' => 'slot_id', 'value' => '1']], [])->willReturn($request);
        $this->deleteGuestStarSlot('TEST_TOKEN', '123', '456', 'sess', '789', '1')->shouldBe($response);
    }

    function it_should_update_guest_star_slot_settings(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('PATCH', 'guest_star/slot_settings', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess'], ['key' => 'slot_id', 'value' => '1'], ['key' => 'is_audio_enabled', 'value' => true]], [])->willReturn($request);
        $this->updateGuestStarSlotSettings('TEST_TOKEN', '123', '456', 'sess', '1', ['is_audio_enabled' => true])->shouldBe($response);
    }

    function it_should_delete_a_guest_star_slot_and_reinvite(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('DELETE', 'guest_star/slot', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'moderator_id', 'value' => '456'], ['key' => 'session_id', 'value' => 'sess'], ['key' => 'guest_id', 'value' => '789'], ['key' => 'slot_id', 'value' => '1'], ['key' => 'should_reinvite_guest', 'value' => 'true']], [])->willReturn($request);
        $this->deleteGuestStarSlot('TEST_TOKEN', '123', '456', 'sess', '789', '1', 'true')->shouldBe($response);
    }
}
