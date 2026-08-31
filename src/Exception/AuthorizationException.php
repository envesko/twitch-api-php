<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

use GuzzleHttp\Exception\ClientException;

/**
 * 403. The token is valid but lacks the scope the endpoint requires.
 */
class AuthorizationException extends ClientException implements TwitchApiException
{
}
