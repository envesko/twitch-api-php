<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class ContentClassificationLabelsApi extends AbstractResource
{
    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/api/reference/#get-content-classification-labels
     */
    public function getContentClassificationLabels(string $bearer, ?string $locale = null): ResponseInterface
    {
        $queryParamsMap = [];

        if ($locale) {
            $queryParamsMap[] = ['key' => 'locale', 'value' => $locale];
        }

        return $this->getApi('content_classification_labels', $bearer, $queryParamsMap);
    }
}
