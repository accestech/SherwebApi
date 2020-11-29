<?php

namespace SherwebApi;

use GuzzleHttp\Client;

class SherwebApi
{
    /**
     * Base URL for sherweb API
     *
     * @var string
     */
    private string $base_uri = 'https://api.sherweb.com';

    /**
     * Will hold the token that needs to be passed to endpoints
     *
     * @var SWToken
     */
    private SWToken $token;

    /**
     * Distributor endpoints
     *
     * @var SWDistributor
     */
    private SWDistributor $distributor;

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
     * Customer API "Susbcription key"
     *
     * @var string
     */
    private string $subscriptionKey;

    /**
     * Scopes for Authentication
     *
     * @var array
     */
    private array $scopes;

    /**
     * @var Client
     */
    private Client $client;

    public function __construct(
        string $clientId,
        string $clientSecret,
        string $subscriptionKey,
        array $scopes = ['distributor']
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->subscriptionKey = $subscriptionKey;
        $this->scopes = $scopes;
        $this->setClient();
    }

    public function distributor()
    {
        if (!isset($this->distributor) || !is_a($this->distributor, SWDistributor::class)) {
            $this->distributor = new SWDistributor($this->client, $this->token(), $this->subscriptionKey);
        }

        return $this->distributor;
    }

    public function token()
    {
        if (!isset($this->token) || !is_a($this->token, SWToken::class)) {
            $this->token = new SWToken($this->client, $this->clientId, $this->clientSecret, $this->scopes);
        }

        return $this->token;
    }

    public function setBaseUri(string $uri)
    {
        $this->base_uri = $uri;
        $this->setClient();
    }

    public function getBaseUri()
    {
        return $this->base_uri;
    }

    private function setClient()
    {
        $this->client = new Client([
            'base_uri' => $this->getBaseUri(),
            'verify' => false
        ]);
    }
}
