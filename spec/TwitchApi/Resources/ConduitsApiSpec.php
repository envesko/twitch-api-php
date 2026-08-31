<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class ConduitsApiSpec extends ObjectBehavior
{
    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_get_conduits(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/conduits', 'TEST_TOKEN', [], [])->willReturn($request);
        $this->getConduits('TEST_TOKEN')->shouldBe($response);
    }

    function it_should_create_conduits(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'eventsub/conduits', 'TEST_TOKEN', [], [['key' => 'shard_count', 'value' => 5]])->willReturn($request);
        $this->createConduits('TEST_TOKEN', 5)->shouldBe($response);
    }

    function it_should_update_conduits(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('PATCH', 'eventsub/conduits', 'TEST_TOKEN', [], [['key' => 'id', 'value' => 'abc'], ['key' => 'shard_count', 'value' => 10]])->willReturn($request);
        $this->updateConduits('TEST_TOKEN', 'abc', 10)->shouldBe($response);
    }

    function it_should_delete_a_conduit(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('DELETE', 'eventsub/conduits', 'TEST_TOKEN', [['key' => 'id', 'value' => 'abc']], [])->willReturn($request);
        $this->deleteConduit('TEST_TOKEN', 'abc')->shouldBe($response);
    }

    function it_should_get_conduit_shards(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/conduits/shards', 'TEST_TOKEN', [['key' => 'conduit_id', 'value' => 'abc']], [])->willReturn($request);
        $this->getConduitShards('TEST_TOKEN', 'abc')->shouldBe($response);
    }

    function it_should_get_conduit_shards_filtered_by_status(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'eventsub/conduits/shards', 'TEST_TOKEN', [['key' => 'conduit_id', 'value' => 'abc'], ['key' => 'status', 'value' => 'enabled'], ['key' => 'after', 'value' => 'cursor']], [])->willReturn($request);
        $this->getConduitShards('TEST_TOKEN', 'abc', 'enabled', 'cursor')->shouldBe($response);
    }

    function it_should_update_conduit_shards(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $shards = [['id' => '0', 'transport' => ['method' => 'webhook', 'callback' => 'https://example.com/cb', 'secret' => 'secret']]];
        $requestGenerator->generate('PATCH', 'eventsub/conduits/shards', 'TEST_TOKEN', [], [['key' => 'conduit_id', 'value' => 'abc'], ['key' => 'shards', 'value' => $shards]])->willReturn($request);
        $this->updateConduitShards('TEST_TOKEN', 'abc', $shards)->shouldBe($response);
    }
}
