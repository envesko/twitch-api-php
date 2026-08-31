<?php

declare(strict_types=1);

namespace TwitchApi;

use Psr\Http\Message\ResponseInterface;

/**
 * Walks a cursor-paginated Helix endpoint.
 *
 * Twitch returns at most a hundred rows and a cursor, so reading everything means a loop that
 * decodes the body, pulls pagination.cursor, and passes it back as the after argument. Every
 * consumer writes that loop, and most of them get the stop condition subtly wrong: the last
 * page carries an empty pagination object rather than omitting it, so testing isset() alone
 * loops forever against some endpoints.
 *
 * Pass a callable that takes a cursor and returns the response for that page:
 *
 *     foreach (Paginator::pages(fn (?string $after) =>
 *         $api->getStreamsApi()->getStreams($token, [], [], [], [], 100, null, $after)
 *     ) as $page) {
 *         // $page is the decoded body of one page
 *     }
 *
 * Or items(), to iterate the rows and forget pages exist.
 */
final class Paginator
{
    /**
     * A safety net against a malformed or repeating cursor, which would otherwise spin
     * forever against a live API.
     */
    public const MAX_PAGES = 1000;

    /**
     * @param callable(?string): ResponseInterface $fetch
     *
     * @return \Generator<int, array<string, mixed>> the decoded body of each page
     */
    public static function pages(callable $fetch, int $maxPages = self::MAX_PAGES): \Generator
    {
        $after = null;
        $seen = [];

        for ($page = 0; $page < $maxPages; ++$page) {
            $body = json_decode((string) $fetch($after)->getBody(), true);

            if (!is_array($body)) {
                return;
            }

            yield $body;

            $cursor = $body['pagination']['cursor'] ?? null;

            // The last page sends an empty pagination object rather than omitting it.
            if (!is_string($cursor) || $cursor === '') {
                return;
            }

            // A cursor that repeats means the endpoint is not advancing. Stop rather than
            // hammer it.
            if (isset($seen[$cursor])) {
                return;
            }

            $seen[$cursor] = true;
            $after = $cursor;
        }
    }

    /**
     * @param callable(?string): ResponseInterface $fetch
     *
     * @return \Generator<int, mixed> every row across every page
     */
    public static function items(callable $fetch, int $maxPages = self::MAX_PAGES): \Generator
    {
        foreach (self::pages($fetch, $maxPages) as $body) {
            foreach ($body['data'] ?? [] as $item) {
                yield $item;
            }
        }
    }
}
