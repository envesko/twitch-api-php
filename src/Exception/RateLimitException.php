<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

use GuzzleHttp\Exception\ClientException;
use TwitchApi\RateLimit;

/**
 * 429. The client has spent its request allowance.
 *
 * Exposes the rate limit headers Twitch sends back, so a caller can wait exactly as long as it
 * needs to rather than guessing.
 *
 * The rate limit is read on demand rather than in a constructor, because Guzzle marks the
 * constructor on its response exceptions final.
 */
class RateLimitException extends ClientException implements TwitchApiException
{
    private bool $rateLimitResolved = false;

    private ?RateLimit $rateLimit = null;

    public function getRateLimit(): ?RateLimit
    {
        if (!$this->rateLimitResolved) {
            $this->rateLimit = RateLimit::fromResponse($this->getResponse());
            $this->rateLimitResolved = true;
        }

        return $this->rateLimit;
    }

    /**
     * Seconds to wait before retrying, or null when Twitch did not say.
     */
    public function getRetryAfter(): ?int
    {
        return $this->getRateLimit()?->getSecondsUntilReset();
    }
}
