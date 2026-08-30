<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class ClipsApiSpec extends ObjectBehavior
{
    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_get_clips_by_broadcaster_id(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->getClips('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_get_clips_by_broadcaster_id_with_helper_function(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->getClipsByBroadcasterId('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_get_clips_by_game_id(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'game_id', 'value' => '123']], [])->willReturn($request);
        $this->getClips('TEST_TOKEN', null, '123')->shouldBe($response);
    }

    function it_should_get_clips_by_game_id_with_helper_function(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'game_id', 'value' => '123']], [])->willReturn($request);
        $this->getClipsByGameId('TEST_TOKEN', '123')->shouldBe($response);
    }
    
    function it_should_get_one_clip_by_id(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'id', 'value' => '123']], [])->willReturn($request);
        $this->getClips('TEST_TOKEN', null, null, '123')->shouldBe($response);
    }

    function it_should_get_one_clip_by_id_with_helper_function(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'id', 'value' => '123']], [])->willReturn($request);
        $this->getClipsByIds('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_get_multiple_clips_by_id(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'id', 'value' => '123,456']], [])->willReturn($request);
        $this->getClips('TEST_TOKEN', null, null, '123,456')->shouldBe($response);
    }

    function it_should_get_multiple_clips_by_id_with_helper_function(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'id', 'value' => '123,456']], [])->willReturn($request);
        $this->getClipsByIds('TEST_TOKEN', '123,456')->shouldBe($response);
    }

    function it_should_get_clips_with_opts(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'first', 'value' => '10'], ['key' => 'before', 'value' => 'abc'], ['key' => 'after', 'value' => 'def'], ['key' => 'started_at', 'value' => '2018-10-12T07:20:50.52Z'], ['key' => 'ended_at', 'value' => '2019-10-12T07:20:50.52Z']], [])->willReturn($request);
        $this->getClips('TEST_TOKEN', '123', null, null, 10, 'abc', 'def', '2018-10-12T07:20:50.52Z', '2019-10-12T07:20:50.52Z')->shouldBe($response);
    }

    function it_should_create_a_clip(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'clips', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'has_delay', 'value' => 'true']], [])->willReturn($request);
        $this->createClip('TEST_TOKEN', '123', true)->shouldBe($response);
    }

    function it_should_get_clips_download(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips/downloads', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->getClipsDownload('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_create_a_clip_from_a_vod(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'videos/clips', 'TEST_TOKEN', [], [['key' => 'video_id', 'value' => 'vid'], ['key' => 'offset_seconds', 'value' => 30]])->willReturn($request);
        $this->createClipFromVod('TEST_TOKEN', 'vid', 30)->shouldBe($response);
    }

    function it_should_get_clips_download_within_a_window(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'clips/downloads', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'started_at', 'value' => '2026-01-01T00:00:00Z'], ['key' => 'ended_at', 'value' => '2026-02-01T00:00:00Z'], ['key' => 'first', 'value' => 20], ['key' => 'after', 'value' => 'cursor']], [])->willReturn($request);
        $this->getClipsDownload('TEST_TOKEN', '123', '2026-01-01T00:00:00Z', '2026-02-01T00:00:00Z', 20, 'cursor')->shouldBe($response);
    }

    function it_should_create_a_clip_from_a_vod_with_a_duration(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'videos/clips', 'TEST_TOKEN', [], [['key' => 'video_id', 'value' => 'vid'], ['key' => 'offset_seconds', 'value' => 30], ['key' => 'duration_seconds', 'value' => 60]])->willReturn($request);
        $this->createClipFromVod('TEST_TOKEN', 'vid', 30, 60)->shouldBe($response);
    }
}
