<?php

declare(strict_types=1);

namespace NewTwitchApi;

use TwitchApi\HelixGuzzleClient as TwitchApiGuzzleClient;

/**
 * @deprecated since 6.0.0, use TwitchApi\HelixGuzzleClient. Still working, and 8.0.0 is
 *             the last release guaranteed to carry it. Changing the import is the whole
 *             migration; nothing else differs.
 */
class HelixGuzzleClient extends TwitchApiGuzzleClient
{
}
