<?php

declare(strict_types=1);

namespace TwitchApi\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class OauthApi
{
    private const BASE_URI = 'https://id.twitch.tv/oauth2/';

    private string $clientId;
    private string $clientSecret;
    private Client $guzzleClient;
    private string $baseUri;

    /**
     * $baseUri is only used to build the absolute authorization URL. It used to be read back
     * off the Guzzle client, which Guzzle 8 no longer allows. Pass it if you also passed a
     * client with a non-default base URI.
     */
    public function __construct(string $clientId, string $clientSecret, ?Client $guzzleClient = null, ?string $baseUri = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->guzzleClient = $guzzleClient ?? AuthGuzzleClient::getClient();
        $this->baseUri = $baseUri ?? self::BASE_URI;
    }

    /**
     * @return string A full authentication URL, including the Guzzle client's base URI.
     */
    public function getAuthUrl(string $redirectUri, string $responseType = 'code', string $scope = '', bool $forceVerify = false, ?string $state = null): string
    {
        return sprintf(
            '%s%s',
            $this->baseUri,
            $this->getPartialAuthUrl($redirectUri, $responseType, $scope, $forceVerify, $state)
        );
    }

    /**
     * @throws GuzzleException
     */
    public function getUserAccessToken(string $code, string $redirectUri, ?string $state = null): ResponseInterface
    {
        return $this->makeRequest(
            new Request('POST', 'token'),
            [
                RequestOptions::JSON => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $redirectUri,
                    'code' => $code,
                    'state' => $state,
                ],
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    public function refreshToken(string $refreshToken, string $scope = ''): ResponseInterface
    {
        $requestOptions = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];
        if ($scope) {
            $requestOptions['scope'] = $scope;
        }

        return $this->makeRequest(
            new Request('POST', 'token'),
            [
                RequestOptions::JSON => $requestOptions,
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    public function validateAccessToken(string $accessToken): ResponseInterface
    {
        return $this->makeRequest(
            new Request(
                'GET',
                'validate',
                [
                    'Authorization' => sprintf('OAuth %s', $accessToken),
                ]
            )
        );
    }

    /**
     * Whether Twitch still accepts this token.
     *
     * Up to 7.x this could not return false. Guzzle raises on a 4xx before the status code
     * can be read, so an invalid token threw rather than answering the question the method
     * exists to answer. A rejected token is now a false, and only a genuine transport or
     * server failure still throws.
     *
     * @throws GuzzleException on a network failure or a 5xx from Twitch
     */
    public function isValidAccessToken(string $accessToken): bool
    {
        try {
            return $this->validateAccessToken($accessToken)->getStatusCode() === 200;
        } catch (ClientException $e) {
            return false;
        }
    }

    /**
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/authentication/revoke-tokens/
     */
    public function revokeToken(string $accessToken): ResponseInterface
    {
        return $this->makeRequest(
            new Request('POST', 'revoke'),
            [
                RequestOptions::JSON => [
                    'client_id' => $this->clientId,
                    'token' => $accessToken,
                ],
            ]
        );
    }

    /**
     * Start the device code flow, for input-constrained clients such as consoles and CLIs.
     *
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/authentication/getting-tokens-device-code-grant-flow/
     */
    public function getDeviceCode(string $scope = ''): ResponseInterface
    {
        return $this->makeRequest(
            new Request('POST', 'device'),
            [
                RequestOptions::JSON => [
                    'client_id' => $this->clientId,
                    'scopes' => $scope,
                ],
            ]
        );
    }

    /**
     * Exchange a device code for a token. Poll this while the user authorises the device;
     * Twitch answers 400 authorization_pending until they do.
     *
     * @throws GuzzleException
     * @link https://dev.twitch.tv/docs/authentication/getting-tokens-device-code-grant-flow/
     */
    public function getDeviceAccessToken(string $deviceCode, string $scope = ''): ResponseInterface
    {
        return $this->makeRequest(
            new Request('POST', 'token'),
            [
                RequestOptions::JSON => [
                    'client_id' => $this->clientId,
                    'scopes' => $scope,
                    'device_code' => $deviceCode,
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:device_code',
                ],
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    public function getAppAccessToken(string $scope = ''): ResponseInterface
    {
        return $this->makeRequest(
            new Request('POST', 'token'),
            [
                RequestOptions::JSON => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => $scope,
                ],
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    private function makeRequest(Request $request, array $options = []): ResponseInterface
    {
        return $this->guzzleClient->send($request, $options);
    }

    /**
     * @return string A partial authentication URL, excluding the Guzzle client's base URI.
     */
    private function getPartialAuthUrl(string $redirectUri, string $responseType = 'code', string $scope = '', bool $forceVerify = false, ?string $state = null): string
    {
        // Built with http_build_query rather than sprintf. A scope list is space separated
        // and a redirect URI often carries a query string of its own, so interpolating
        // these produced a URL Twitch rejected.
        $parameters = [
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => $responseType,
            'scope' => $scope,
        ];

        if ($forceVerify) {
            $parameters['force_verify'] = 'true';
        }

        if ($state) {
            $parameters['state'] = $state;
        }

        return 'authorize?'.http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }
}
