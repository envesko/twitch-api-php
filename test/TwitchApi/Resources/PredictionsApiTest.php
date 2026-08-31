<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\PredictionsApi;
use TwitchApi\Tests\ResourceTestCase;

class PredictionsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return PredictionsApi::class;
    }

    public function testShouldGetPredictions(): void
    {
        $this->api()->getPredictions(self::TOKEN, '123');

        $this->assertSent('GET', 'predictions', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetPredictionsById(): void
    {
        $this->api()->getPredictions(self::TOKEN, '123', ['321']);

        $this->assertSent('GET', 'predictions', [
            ['broadcaster_id', '123'],
            ['id', '321'],
        ]);
    }

    public function testShouldGetPredictionsByIds(): void
    {
        $this->api()->getPredictions(self::TOKEN, '123', ['321', '456']);

        $this->assertSent('GET', 'predictions', [
            ['broadcaster_id', '123'],
            ['id', '321'],
            ['id', '456'],
        ]);
    }

    public function testShouldGetPredictionsWithOpts(): void
    {
        $this->api()->getPredictions(self::TOKEN, '123', [], 'abc', 100);

        $this->assertSent('GET', 'predictions', [
            ['broadcaster_id', '123'],
            ['after', 'abc'],
            ['first', '100'],
        ]);
    }

    public function testShouldCreateAPrediction(): void
    {
        $this->api()->createPrediction(self::TOKEN, '123', 'Will the coin land on heads or tails?', [['title' => 'Heads'], ['title' => 'Tails']], 15);

        $this->assertSent('POST', 'predictions');
        $this->assertSentBody(['broadcaster_id' => '123', 'title' => 'Will the coin land on heads or tails?', 'outcomes' => [['title' => 'Heads'], ['title' => 'Tails']], 'prediction_window' => 15]);
    }

    public function testShouldEndAPrediction(): void
    {
        $this->api()->endPrediction(self::TOKEN, '123', '456', 'CANCELLED');

        $this->assertSent('PATCH', 'predictions');
        $this->assertSentBody(['broadcaster_id' => '123', 'id' => '456', 'status' => 'CANCELLED']);
    }

    public function testShouldResolveAPrediction(): void
    {
        $this->api()->endPrediction(self::TOKEN, '123', '456', 'RESOLVED', '1');

        $this->assertSent('PATCH', 'predictions');
        $this->assertSentBody(['broadcaster_id' => '123', 'id' => '456', 'status' => 'RESOLVED', 'winning_outcome_id' => '1']);
    }
}
