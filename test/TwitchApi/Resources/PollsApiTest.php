<?php

declare(strict_types=1);

namespace TwitchApi\Tests\Resources;

use TwitchApi\Resources\PollsApi;
use TwitchApi\Tests\ResourceTestCase;

class PollsApiTest extends ResourceTestCase
{
    protected function resourceClass(): string
    {
        return PollsApi::class;
    }

    public function testShouldGetPolls(): void
    {
        $this->api()->getPolls(self::TOKEN, '123');

        $this->assertSent('GET', 'polls', [
            ['broadcaster_id', '123'],
        ]);
    }

    public function testShouldGetPollsById(): void
    {
        $this->api()->getPolls(self::TOKEN, '123', ['321']);

        $this->assertSent('GET', 'polls', [
            ['broadcaster_id', '123'],
            ['id', '321'],
        ]);
    }

    public function testShouldGetPollsByIds(): void
    {
        $this->api()->getPolls(self::TOKEN, '123', ['321', '456']);

        $this->assertSent('GET', 'polls', [
            ['broadcaster_id', '123'],
            ['id', '321'],
            ['id', '456'],
        ]);
    }

    public function testShouldGetPollsWithOpts(): void
    {
        $this->api()->getPolls(self::TOKEN, '123', [], 'abc', 100);

        $this->assertSent('GET', 'polls', [
            ['broadcaster_id', '123'],
            ['after', 'abc'],
            ['first', '100'],
        ]);
    }

    public function testShouldCreateAPoll(): void
    {
        $this->api()->createPoll(self::TOKEN, '123', 'What is my name?', [['title' => 'John'], ['title' => 'Doe']], 15);

        $this->assertSent('POST', 'polls');
        $this->assertSentBody(['broadcaster_id' => '123', 'title' => 'What is my name?', 'choices' => [['title' => 'John'], ['title' => 'Doe']], 'duration' => 15]);
    }

    public function testShouldCreateAPollWithOpts(): void
    {
        $this->api()->createPoll(self::TOKEN, '123', 'What is my name?', [['title' => 'John'], ['title' => 'Doe']], 15, ['bits_voting_enabled' => 1]);

        $this->assertSent('POST', 'polls');
        $this->assertSentBody(['broadcaster_id' => '123', 'title' => 'What is my name?', 'choices' => [['title' => 'John'], ['title' => 'Doe']], 'duration' => 15, 'bits_voting_enabled' => 1]);
    }

    public function testShouldEndAPoll(): void
    {
        $this->api()->endPoll(self::TOKEN, '123', '456', 'TERMINATED');

        $this->assertSent('PATCH', 'polls');
        $this->assertSentBody(['broadcaster_id' => '123', 'id' => '456', 'status' => 'TERMINATED']);
    }
}
