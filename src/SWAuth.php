<?php

namespace SherwebApi;

use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;
use Carbon\Carbon;

class SWAuth
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
     * @var string Scope
     */
    private string $scope;

    //this is a const as SherWeb api does not currently support other types
    private const GRANT_TYPE = "client_credentials";

    /**
     * SWAuth constructor.
     *
     * @param  string $clientId
     * @param  string $clientSecret
     * @param  string $scope
     * @return void
     */
    public function __construct(string $clientId, string $clientSecret, string $scope = 'distributor')
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;
        $this->auth();
    }

    /**
     * This function will call the authentication endpoint
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    private function auth()
    {
        $client = new Client(
            [
            'base_uri' => BASE_URI,
            'verify' => false
            ]
        );
        $postData = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => $this->scope,
            'grant_type' => self::GRANT_TYPE,
        ];

        $request = $client->post(ENDPOINT_AUTH, ['form_params' => $postData]);
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
        if (is_null($this->accessToken)) {
            throw new \Exception("No access token");
        }
        if (!$this->tokenIsValid()) {
            $this->auth();
        }
        return $this->accessToken;
    }
}
