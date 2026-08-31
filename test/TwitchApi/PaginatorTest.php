<?php

declare(strict_types=1);

namespace TwitchApi\Tests;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use TwitchApi\Paginator;

class PaginatorTest extends TestCase
{
    /**
     * @param list<array<string, mixed>> $pages
     *
     * @return callable(?string): ResponseInterface
     */
    private function server(array $pages, ?array &$cursorsSeen = null): callable
    {
        $cursorsSeen = [];

        return function (?string $after) use ($pages, &$cursorsSeen): ResponseInterface {
            $cursorsSeen[] = $after;
            $index = $after === null ? 0 : (int) $after;

            return new Response(200, [], json_encode($pages[$index] ?? ['data' => []]));
        };
    }

    public function testWalksEveryPageAndStopsAtTheEnd(): void
    {
        $fetch = $this->server([
            ['data' => ['a', 'b'], 'pagination' => ['cursor' => '1']],
            ['data' => ['c'], 'pagination' => ['cursor' => '2']],
            ['data' => ['d'], 'pagination' => []],
        ], $seen);

        $this->assertSame(['a', 'b', 'c', 'd'], iterator_to_array(Paginator::items($fetch), false));
        $this->assertSame([null, '1', '2'], $seen);
    }

    public function testAnEmptyPaginationObjectEndsTheWalk(): void
    {
        // The case a hand-rolled loop usually gets wrong: Twitch sends pagination as an empty
        // object on the last page rather than omitting it, so isset() alone never stops.
        $fetch = $this->server([['data' => ['only'], 'pagination' => []]]);

        $this->assertSame(['only'], iterator_to_array(Paginator::items($fetch), false));
    }

    public function testAMissingPaginationKeyEndsTheWalk(): void
    {
        $fetch = $this->server([['data' => ['only']]]);

        $this->assertSame(['only'], iterator_to_array(Paginator::items($fetch), false));
    }

    public function testARepeatingCursorStopsRatherThanLooping(): void
    {
        $calls = 0;
        $fetch = function (?string $after) use (&$calls): ResponseInterface {
            ++$calls;

            return new Response(200, [], json_encode([
                'data' => ['x'],
                'pagination' => ['cursor' => 'same'],
            ]));
        };

        $this->assertSame(['x', 'x'], iterator_to_array(Paginator::items($fetch), false));
        $this->assertSame(2, $calls, 'stopped once the cursor repeated');
    }

    public function testThePageCapIsHonoured(): void
    {
        $fetch = function (?string $after): ResponseInterface {
            return new Response(200, [], json_encode([
                'data' => ['x'],
                'pagination' => ['cursor' => uniqid('', true)],
            ]));
        };

        $this->assertCount(3, iterator_to_array(Paginator::pages($fetch, 3), false));
    }

    public function testAnUndecodableBodyEndsTheWalk(): void
    {
        $fetch = fn (?string $after): ResponseInterface => new Response(200, [], 'not json');

        $this->assertSame([], iterator_to_array(Paginator::items($fetch), false));
    }

    public function testPagesYieldsWholeBodiesNotJustRows(): void
    {
        $fetch = $this->server([
            ['data' => ['a'], 'total' => 2, 'pagination' => ['cursor' => '1']],
            ['data' => ['b'], 'total' => 2, 'pagination' => []],
        ]);

        $pages = iterator_to_array(Paginator::pages($fetch), false);

        $this->assertCount(2, $pages);
        $this->assertSame(2, $pages[0]['total']);
    }
}
