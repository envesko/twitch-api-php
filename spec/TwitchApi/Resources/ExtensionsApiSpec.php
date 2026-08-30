<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class ExtensionsApiSpec extends ObjectBehavior
{
    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_get_extensions(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions', 'TEST_JWT', [['key' => 'extension_id', 'value' => 'ext']], [])->willReturn($request);
        $this->getExtensions('TEST_JWT', 'ext')->shouldBe($response);
    }

    function it_should_get_extensions_for_a_version(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions', 'TEST_JWT', [['key' => 'extension_id', 'value' => 'ext'], ['key' => 'extension_version', 'value' => '1.0.0']], [])->willReturn($request);
        $this->getExtensions('TEST_JWT', 'ext', '1.0.0')->shouldBe($response);
    }

    function it_should_get_released_extensions(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/released', 'TEST_TOKEN', [['key' => 'extension_id', 'value' => 'ext']], [])->willReturn($request);
        $this->getReleasedExtensions('TEST_TOKEN', 'ext')->shouldBe($response);
    }

    function it_should_get_extension_live_channels(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/live', 'TEST_TOKEN', [['key' => 'extension_id', 'value' => 'ext'], ['key' => 'first', 'value' => 20], ['key' => 'after', 'value' => 'cursor']], [])->willReturn($request);
        $this->getExtensionLiveChannels('TEST_TOKEN', 'ext', 20, 'cursor')->shouldBe($response);
    }

    function it_should_get_an_extension_configuration_segment(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/configurations', 'TEST_JWT', [['key' => 'extension_id', 'value' => 'ext'], ['key' => 'segment', 'value' => 'broadcaster'], ['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->getExtensionConfigurationSegment('TEST_JWT', 'ext', ['broadcaster'], '123')->shouldBe($response);
    }

    function it_should_set_an_extension_configuration_segment(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('PUT', 'extensions/configurations', 'TEST_JWT', [], [['key' => 'extension_id', 'value' => 'ext'], ['key' => 'segment', 'value' => 'global'], ['key' => 'content', 'value' => '{}']])->willReturn($request);
        $this->setExtensionConfigurationSegment('TEST_JWT', 'ext', 'global', ['content' => '{}'])->shouldBe($response);
    }

    function it_should_set_extension_required_configuration(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('PUT', 'extensions/required_configuration', 'TEST_JWT', [['key' => 'broadcaster_id', 'value' => '123']], [['key' => 'extension_id', 'value' => 'ext'], ['key' => 'extension_version', 'value' => '1.0.0'], ['key' => 'required_configuration', 'value' => 'config']])->willReturn($request);
        $this->setExtensionRequiredConfiguration('TEST_JWT', '123', 'ext', '1.0.0', 'config')->shouldBe($response);
    }

    function it_should_send_an_extension_pubsub_message(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'extensions/pubsub', 'TEST_JWT', [], [['key' => 'target', 'value' => ['broadcast']], ['key' => 'broadcaster_id', 'value' => '123'], ['key' => 'is_global_broadcast', 'value' => false], ['key' => 'message', 'value' => 'hello']])->willReturn($request);
        $this->sendExtensionPubSubMessage('TEST_JWT', ['broadcast'], '123', false, 'hello')->shouldBe($response);
    }

    function it_should_send_an_extension_chat_message(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'extensions/chat', 'TEST_JWT', [['key' => 'broadcaster_id', 'value' => '123']], [['key' => 'text', 'value' => 'hello'], ['key' => 'extension_id', 'value' => 'ext'], ['key' => 'extension_version', 'value' => '1.0.0']])->willReturn($request);
        $this->sendExtensionChatMessage('TEST_JWT', '123', 'hello', 'ext', '1.0.0')->shouldBe($response);
    }

    function it_should_get_extension_secrets(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/jwt/secrets', 'TEST_JWT', [['key' => 'extension_id', 'value' => 'ext']], [])->willReturn($request);
        $this->getExtensionSecrets('TEST_JWT', 'ext')->shouldBe($response);
    }

    function it_should_create_an_extension_secret(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('POST', 'extensions/jwt/secrets', 'TEST_JWT', [['key' => 'extension_id', 'value' => 'ext'], ['key' => 'delay', 'value' => 300]], [])->willReturn($request);
        $this->createExtensionSecret('TEST_JWT', 'ext', 300)->shouldBe($response);
    }

    function it_should_get_released_extensions_for_a_version(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/released', 'TEST_TOKEN', [['key' => 'extension_id', 'value' => 'ext'], ['key' => 'extension_version', 'value' => '1.0.0']], [])->willReturn($request);
        $this->getReleasedExtensions('TEST_TOKEN', 'ext', '1.0.0')->shouldBe($response);
    }
}
