<?php

namespace SherwebApi;

use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Carbon\Carbon;

class SWToken
{
    /**
     * The acces token returned by the api
     *
     * @var string Acces Token
     */
    private string $accessToken;

    /**
     * Expiration of the token based on the returned "expires_id"
     *
     * @var Carbon Token Expiration
     */
    private Carbon $tokenExpiration;

    /**
     * Customer API "Client ID"
     *
     * @var string Client ID
     */
    private string $clientId;

    /**
     * Customer API "Client Secret"
     *
     * @var string Client Secret
     */
    private string $clientSecret;

    /**
     * 2020-11-25: Only "distributor" is supported at this time
     *
     * @var array Scope
     */
    private array $scopes;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var string
     */
    private string $endpoint = '/auth/oidc/connect/token';

    //this is a const as SherWeb api does not currently support other types
    private const GRANT_TYPE = "client_credentials";

    /**
     * SWAuth constructor.
     *
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  array  $scopes
     */
    public function __construct(Client $client, string $clientId, string $clientSecret, array $scopes = ['distributor'])
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
    }

    /**
     * This function will call the authentication endpoint
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function auth()
    {
        $postData = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => implode(',', $this->scopes),
            'grant_type' => self::GRANT_TYPE,
        ];

        $request = $this->client->post($this->endpoint, ['form_params' => $postData]);
        $this->parseResponse($request->getBody());
    }

    /**
     * Will parse the response and set the properties
     *
     * @param  StreamInterface $response
     * @throws \JsonException
     */
    private function parseResponse(StreamInterface $response)
    {
        $raw = $response->getContents();
        $json_response = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
        $this->accessToken = $json_response->access_token;
        $this->setTokenExpiration($json_response->expires_in);
    }

    /**
     * Set token expiration
     *
     * @param int $expires_in
     */
    private function setTokenExpiration(int $expires_in)
    {
        $now = Carbon::now();
        $this->tokenExpiration = $now->addSeconds($expires_in);
    }

    /**
     * Verify if the token is expired
     *
     * @return bool
     */
    private function tokenIsValid(): bool
    {
        return $this->tokenExpiration < Carbon::now();
    }

    /**
     * Access Token getter
     *
     * @return string
     * @throws \Exception
     */
    public function getAccessToken(): string
    {
//        if (!isset($this->accessToken)) {
//            throw new \Exception("No access token");
//        }
        if (!isset($this->accessToken) || !$this->tokenIsValid()) {
            $this->auth();
        }
        return $this->accessToken;
    }

    /**
     * Set endpoint URI
     *
     * @param  string  $endpoint
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Get endpoint URI
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
