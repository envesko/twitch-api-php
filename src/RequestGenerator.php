<?php

namespace TwitchApi;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class RequestGenerator
{
    public function generate(string $httpMethod, string $uriEndpoint, ?string $bearer = null, array $queryParamsMap = [], array $bodyParams = []): RequestInterface
    {
        $headers = [
          'Accept' => 'application/json',
        ];

        if ($bearer) {
            $headers['Authorization'] = sprintf('Bearer %s', $bearer);
        }

        if (count($bodyParams) > 0) {
            $request = new Request(
                $httpMethod,
                sprintf(
                    '%s%s',
                    $uriEndpoint,
                    $this->generateQueryParams($queryParamsMap)
                ),
                $headers,
                $this->generateBodyParams($bodyParams)
            );
        } else {
            $request = new Request(
                $httpMethod,
                sprintf(
                    '%s%s',
                    $uriEndpoint,
                    $this->generateQueryParams($queryParamsMap)
                ),
                $headers
            );
        }

        return $request;
    }

    /**
     * $queryParamsMap should be a mapping of the param key expected in the API call URL,
     * and the value to be sent for that key.
     *
     * [['key' => 'param_key', 'value' => 42],['key' => 'other_key', 'value' => 'asdf']]
     * would result in
     * ?param_key=42&other_key=asdf
     */
    protected function generateQueryParams(array $queryParamsMap): string
    {
        $queryStringParams = '';
        foreach ($queryParamsMap as $paramMap) {
            if ($paramMap['value'] !== null) {
                if (is_bool($paramMap['value'])) {
                    $paramMap['value'] = (int) $paramMap['value'];
                }

                // Values reach here straight from the caller, so they can hold anything a
                // Twitch field allows: a game title containing & or #, a base64 pagination
                // cursor containing + and =, or a search term typed by the caller's own user.
                // Interpolating those raw truncates the value or appends parameters nobody
                // asked for. rawurlencode is used rather than urlencode so a space stays %20,
                // which is what the URI already produced before this was escaped at all.
                $queryStringParams .= sprintf(
                    '&%s=%s',
                    rawurlencode((string) $paramMap['key']),
                    rawurlencode((string) $paramMap['value'])
                );
            }
        }

        return $queryStringParams ? '?'.substr($queryStringParams, 1) : '';
    }

    protected function generateBodyParams(array $bodyParamsMap): string
    {
        $bodyParams = [];
        foreach ($bodyParamsMap as $bodyParam) {
            if ($bodyParam['value'] !== null) {
                $bodyParams[$bodyParam['key']] = $bodyParam['value'];
            }
        }

        return json_encode($bodyParams);
    }
}
