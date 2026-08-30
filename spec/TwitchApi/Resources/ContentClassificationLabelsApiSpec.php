<?php

namespace spec\TwitchApi\Resources;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use TwitchApi\RequestGenerator;
use TwitchApi\HelixGuzzleClient;
use PhpSpec\ObjectBehavior;

class ContentClassificationLabelsApiSpec extends ObjectBehavior
{
    function let(HelixGuzzleClient $guzzleClient, RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $this->beConstructedWith($guzzleClient, $requestGenerator);
        $guzzleClient->send($request)->willReturn($response);
    }

    function it_should_get_content_classification_labels(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'content_classification_labels', 'TEST_TOKEN', [], [])->willReturn($request);
        $this->getContentClassificationLabels('TEST_TOKEN')->shouldBe($response);
    }

    function it_should_get_content_classification_labels_for_a_locale(RequestGenerator $requestGenerator, Request $request, Response $response)
    {
        $requestGenerator->generate('GET', 'content_classification_labels', 'TEST_TOKEN', [['key' => 'locale', 'value' => 'en-US']], [])->willReturn($request);
        $this->getContentClassificationLabels('TEST_TOKEN', 'en-US')->shouldBe($response);
    }
}
