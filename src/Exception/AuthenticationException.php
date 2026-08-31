<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

use GuzzleHttp\Exception\ClientException;

/**
 * 401. The token is missing, expired, or does not match the client id.
 */
class AuthenticationException extends ClientException implements TwitchApiException
{
}
