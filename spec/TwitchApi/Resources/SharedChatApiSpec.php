<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class SharedChatApiSpec extends ObjectBehavior
{
    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_get_a_shared_chat_session(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'shared_chat/session', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->getSharedChatSession('TEST_TOKEN', '123')->shouldBe($response);
    }
}
