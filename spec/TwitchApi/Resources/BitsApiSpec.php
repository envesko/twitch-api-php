<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class BitsApiSpec extends ObjectBehavior
{
    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_getcheermotes(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'bits/cheermotes', 'TEST_TOKEN', [], [])->willReturn($request);
        $this->getCheermotes('TEST_TOKEN')->shouldBe($response);
    }

    function it_should_getcheermotes_by_broadcaster_id(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'bits/cheermotes', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->getCheermotes('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_extension_transactions(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/transactions', 'TEST_TOKEN', [['key' => 'extension_id', 'value' => '1']], [])->willReturn($request);
        $this->getExtensionTransactions('TEST_TOKEN', '1')->shouldBe($response);
    }

    function it_should_extension_transactions_with_transaction_id(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/transactions', 'TEST_TOKEN', [['key' => 'extension_id', 'value' => '1'], ['key' => 'id', 'value' => '321']], [])->willReturn($request);
        $this->getExtensionTransactions('TEST_TOKEN', '1', ['321'])->shouldBe($response);
    }

    function it_should_extension_transactions_with_first(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/transactions', 'TEST_TOKEN', [['key' => 'extension_id', 'value' => '1'], ['key' => 'first', 'value' => '100']], [])->willReturn($request);
        $this->getExtensionTransactions('TEST_TOKEN', '1', [], 100)->shouldBe($response);
    }

    function it_should_extension_transactions_with_after(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'extensions/transactions', 'TEST_TOKEN', [['key' => 'extension_id', 'value' => '1'], ['key' => 'after', 'value' => '100']], [])->willReturn($request);
        $this->getExtensionTransactions('TEST_TOKEN', '1', [], null, 100)->shouldBe($response);
    }

    function it_should_get_bits_leaderboard(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'bits/leaderboard', 'TEST_TOKEN', [], [])->willReturn($request);
        $this->getBitsLeaderboard('TEST_TOKEN')->shouldBe($response);
    }

    function it_should_get_bits_leaderboard_with_opts(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'bits/leaderboard', 'TEST_TOKEN', [['key' => 'count', 'value' => '100'], ['key' => 'period', 'value' => 'all'], ['key' => 'started_at', 'value' => '2019-10-12T07:20:50.52Z'], ['key' => 'user_id', 'value' => '123']], [])->willReturn($request);
        $this->getBitsLeaderboard('TEST_TOKEN', 100, 'all', '2019-10-12T07:20:50.52Z', '123')->shouldBe($response);
    }

    function it_should_get_a_custom_power_up(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'bits/custom_power_ups', 'TEST_TOKEN', [['key' => 'broadcaster_id', 'value' => '123']], [])->willReturn($request);
        $this->getCustomPowerUp('TEST_TOKEN', '123')->shouldBe($response);
    }

    function it_should_get_extension_bits_products(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'bits/extensions', 'TEST_TOKEN', [], [])->willReturn($request);
        $this->getExtensionBitsProducts('TEST_TOKEN')->shouldBe($response);
    }

    function it_should_get_all_extension_bits_products(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'bits/extensions', 'TEST_TOKEN', [['key' => 'should_include_all', 'value' => true]], [])->willReturn($request);
        $this->getExtensionBitsProducts('TEST_TOKEN', true)->shouldBe($response);
    }

    function it_should_update_an_extension_bits_product(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $cost = ['amount' => 100, 'type' => 'bits'];
        $requestGenerator->generate('PUT', 'bits/extensions', 'TEST_TOKEN', [], [['key' => 'sku', 'value' => 'SKU'], ['key' => 'cost', 'value' => $cost], ['key' => 'display_name', 'value' => 'Name'], ['key' => 'in_development', 'value' => true]])->willReturn($request);
        $this->updateExtensionBitsProduct('TEST_TOKEN', 'SKU', $cost, 'Name', ['in_development' => true])->shouldBe($response);
    }
}
