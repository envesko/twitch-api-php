<?php

declare(strict_types=1);

namespace TwitchApi;

use Psr\Http\Message\ResponseInterface;

/**
 * The rate limit headers Twitch returns on every Helix response.
 *
 * Up to 7.x these were discarded, so a caller had no way to pace itself except by waiting for
 * a 429 and guessing. Read one off any response with fromResponse().
 *
 * @link https://dev.twitch.tv/docs/api/guide/#twitch-rate-limits
 */
final class RateLimit
{
    private function __construct(
        private readonly ?int $limit,
        private readonly ?int $remaining,
        private readonly ?int $resetsAt,
    ) {
    }

    /**
     * Null when the response carried none of the headers, which is the case for the OAuth
     * endpoints and for a response served from a cache.
     */
    public static function fromResponse(ResponseInterface $response): ?self
    {
        $limit = self::header($response, 'Ratelimit-Limit');
        $remaining = self::header($response, 'Ratelimit-Remaining');
        $reset = self::header($response, 'Ratelimit-Reset');

        if ($limit === null && $remaining === null && $reset === null) {
            return null;
        }

        return new self($limit, $remaining, $reset);
    }

    /**
     * Requests allowed in the current window.
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Requests still available in the current window.
     */
    public function getRemaining(): ?int
    {
        return $this->remaining;
    }

    /**
     * Unix timestamp at which the allowance refills.
     */
    public function getResetsAt(): ?int
    {
        return $this->resetsAt;
    }

    /**
     * Seconds until the allowance refills, never negative. Null when Twitch did not say.
     */
    public function getSecondsUntilReset(): ?int
    {
        if ($this->resetsAt === null) {
            return null;
        }

        return max(0, $this->resetsAt - time());
    }

    public function isExhausted(): bool
    {
        return $this->remaining !== null && $this->remaining <= 0;
    }

    private static function header(ResponseInterface $response, string $name): ?int
    {
        $value = $response->getHeaderLine($name);

        return $value === '' ? null : (int) $value;
    }
}
