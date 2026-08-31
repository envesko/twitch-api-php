<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

use GuzzleHttp\Exception\BadResponseException;

/**
 * Turns a Guzzle response exception into the matching typed one.
 *
 * Anything without a recognised status is handed back untouched, so behaviour only ever gets
 * more specific, never different.
 */
final class ExceptionFactory
{
    public static function from(\Throwable $e): \Throwable
    {
        if (!$e instanceof BadResponseException) {
            return $e;
        }

        $response = $e->getResponse();
        $request = $e->getRequest();
        $message = $e->getMessage();

        $class = match (true) {
            $response->getStatusCode() === 401 => AuthenticationException::class,
            $response->getStatusCode() === 403 => AuthorizationException::class,
            $response->getStatusCode() === 404 => NotFoundException::class,
            $response->getStatusCode() === 429 => RateLimitException::class,
            $response->getStatusCode() >= 500 => ServerException::class,
            default => null,
        };

        if ($class === null) {
            return $e;
        }

        return new $class($message, $request, $response, $e);
    }
}
