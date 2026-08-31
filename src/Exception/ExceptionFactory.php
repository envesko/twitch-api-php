<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Maps a Twitch error response onto the matching typed exception.
 *
 * Two entry points, because the two supported client kinds fail differently: a Guzzle client
 * throws, and a PSR-18 client returns the response whatever its status. Both end up here, so
 * they fail identically from a caller's point of view.
 *
 * Anything without a recognised status is handed back untouched, so behaviour only ever gets
 * more specific, never different.
 */
final class ExceptionFactory
{
    /**
     * Rethrows a Guzzle response exception as its typed equivalent.
     */
    public static function from(\Throwable $e): \Throwable
    {
        if (!$e instanceof BadResponseException) {
            return $e;
        }

        return self::build($e->getMessage(), $e->getRequest(), $e->getResponse(), $e) ?? $e;
    }

    /**
     * Builds the same exception from a response a PSR-18 client returned rather than threw.
     */
    public static function fromResponse(RequestInterface $request, ResponseInterface $response): \Throwable
    {
        $message = sprintf(
            '%s error: `%s %s` resulted in a `%s %s` response',
            $response->getStatusCode() >= 500 ? 'Server' : 'Client',
            $request->getMethod(),
            $request->getUri(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        return self::build($message, $request, $response)
            ?? new BadResponseException($message, $request, $response);
    }

    private static function build(
        string $message,
        RequestInterface $request,
        ResponseInterface $response,
        ?\Throwable $previous = null,
    ): ?\Throwable {
        $status = $response->getStatusCode();

        $class = match (true) {
            $status === 401 => AuthenticationException::class,
            $status === 403 => AuthorizationException::class,
            $status === 404 => NotFoundException::class,
            $status === 429 => RateLimitException::class,
            $status >= 500 => ServerException::class,
            default => null,
        };

        if ($class === null) {
            return null;
        }

        return new $class($message, $request, $response, $previous);
    }
}
