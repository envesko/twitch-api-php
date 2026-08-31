<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

/**
 * Marker for every exception this library raises for a Twitch response.
 *
 * The concrete classes extend Guzzle's own exceptions rather than replacing them, so code
 * written against 7.x that catches GuzzleException, or the PSR-18 ClientExceptionInterface,
 * keeps working unchanged. Catch this instead when you want only Twitch failures, or one of
 * the specific classes when you want to branch on why.
 */
interface TwitchApiException extends \Throwable
{
}
