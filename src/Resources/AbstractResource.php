<?php

declare(strict_types=1);

namespace TwitchApi\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use TwitchApi\Exception\ExceptionFactory;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\RequestGenerator;

abstract class AbstractResource
{
    /**
     * Typed as the union rather than HelixGuzzleClient alone so any PSR-18 client can be
     * injected. Widening a parameter accepts everything it accepted before, so this is not a
     * change for existing callers.
     */
    protected HelixGuzzleClient|ClientInterface $guzzleClient;

    private RequestGenerator $requestGenerator;

    public function __construct(HelixGuzzleClient|ClientInterface $guzzleClient, RequestGenerator $requestGenerator)
    {
        $this->guzzleClient = $guzzleClient;
        $this->requestGenerator = $requestGenerator;
    }

    /**
     * @throws GuzzleException
     */
    protected function getApi(string $uriEndpoint, string $bearer, array $queryParamsMap = [], array $bodyParams = []): ResponseInterface
    {
        return $this->sendToApi('GET', $uriEndpoint, $bearer, $queryParamsMap, $bodyParams);
    }

    /**
     * @throws GuzzleException
     */
    protected function getApiWithOptionalAuth(string $uriEndpoint, ?string $bearer = null, array $queryParamsMap = [], array $bodyParams = []): ResponseInterface
    {
        return $this->sendToApi('GET', $uriEndpoint, $bearer, $queryParamsMap, $bodyParams);
    }

    /**
     * @throws GuzzleException
     */
    protected function deleteApi(string $uriEndpoint, string $bearer, array $queryParamsMap = [], array $bodyParams = []): ResponseInterface
    {
        return $this->sendToApi('DELETE', $uriEndpoint, $bearer, $queryParamsMap, $bodyParams);
    }

    /**
     * @throws GuzzleException
     */
    protected function patchApi(string $uriEndpoint, string $bearer, array $queryParamsMap = [], array $bodyParams = []): ResponseInterface
    {
        return $this->sendToApi('PATCH', $uriEndpoint, $bearer, $queryParamsMap, $bodyParams);
    }

    /**
     * @throws GuzzleException
     */
    protected function postApi(string $uriEndpoint, string $bearer, array $queryParamsMap = [], array $bodyParams = []): ResponseInterface
    {
        return $this->sendToApi('POST', $uriEndpoint, $bearer, $queryParamsMap, $bodyParams);
    }

    /**
     * @throws GuzzleException
     */
    protected function putApi(string $uriEndpoint, string $bearer, array $queryParamsMap = [], array $bodyParams = []): ResponseInterface
    {
        return $this->sendToApi('PUT', $uriEndpoint, $bearer, $queryParamsMap, $bodyParams);
    }

    private function sendToApi(string $httpMethod, string $uriEndpoint, ?string $bearer = null, array $queryParamsMap = [], array $bodyParams = []): ResponseInterface
    {
        $request = $this->requestGenerator->generate($httpMethod, $uriEndpoint, $bearer, $queryParamsMap, $bodyParams);

        if ($this->guzzleClient instanceof HelixGuzzleClient) {
            return $this->guzzleClient->send($request);
        }

        // A PSR-18 client returns the response whatever its status, because the standard
        // says so. The resource classes have always signalled failure by throwing, so the
        // status is turned back into the same typed exception a HelixGuzzleClient would
        // have raised. Without this an injected PSR-18 client would silently hand back
        // error bodies where the library used to throw.
        $response = $this->guzzleClient->sendRequest($request);

        if ($response->getStatusCode() >= 400) {
            throw ExceptionFactory::fromResponse($request, $response);
        }

        return $response;
    }
}
