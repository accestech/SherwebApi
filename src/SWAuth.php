<?php
namespace SherwebApi;

use GuzzleHttp\Client;
use Psr\Http\Message\StreamInterface;

class SWAuth
{
    /**
     * @var string Acces Token
     */
    private $accessToken;

    /**
     * @var string Expires In
     */
    private $expiresIn;

    /**
     * @var string Client ID
     */
    private $clientId;

    /**
     * @var string Client Secret
     */
    private $clientSecret;

    /**
     * 2020-11-25: Only "distributor" is supported at this time
     * @var string Scope
     */
    private $scope;

    const GRANT_TYPE = "client_credentials"; //this is a const as SherWeb api does not currently support other types

    public function __construct(string $clientId, string $clientSecret, string $scope = 'distributor')
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;
        $this->auth();
    }

    private function auth()
    {
        $client = new Client([
            'base_uri' => BASE_URI,
            'verify' => false
        ]);
        $postData = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => $this->scope,
            'grant_type' => self::GRANT_TYPE,
        ];

        $request = $client->post(AUTH_ENDPOINT, ['form_params' => $postData] );
        $this->parseResponse($request->getBody());
    }

    private function parseResponse(StreamInterface $response)
    {
        $raw = $response->getContents();
        $json_response = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
        $this->accessToken = $json_response->access_token;
        $this->expiresIn = $json_response->expires_in;
    }

    public function getAccessToken()
    {
        if (is_null($this->accessToken)) {
            throw new \Exception("No access token");
        }
        return $this->accessToken;
    }
}