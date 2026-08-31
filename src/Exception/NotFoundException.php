<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

use GuzzleHttp\Exception\ClientException;

/**
 * 404. The endpoint exists but the thing asked for does not.
 */
class NotFoundException extends ClientException implements TwitchApiException
{
}
