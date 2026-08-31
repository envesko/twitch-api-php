<?php

declare(strict_types=1);

namespace TwitchApi\Exception;

use GuzzleHttp\Exception\ServerException as GuzzleServerException;

/**
 * 5xx. A failure at Twitch rather than in the request, so worth retrying.
 */
class ServerException extends GuzzleServerException implements TwitchApiException
{
}
