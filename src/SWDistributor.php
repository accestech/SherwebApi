<?php

namespace SherwebApi;

use GuzzleHttp\Client;

class SWDistributor
{
    /**
     * @var SWToken
     */
    private $token;

    /**
     * @var string
     */
    private $subscriptionKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $distributor_endpoint = '/distributor/v1/billing/payable-charges';

    /**
     * SWDistributor constructor.
     * @param  Client  $client
     * @param  SWToken  $token
     * @param  string  $subscriptionKey
     */
    public function __construct(Client $client, SWToken $token, string $subscriptionKey)
    {
        $this->token = $token;
        $this->subscriptionKey = $subscriptionKey;
        $this->setClient($client);
    }

    public function getPayableCharges(string $date = '')
    {
        $url = $this->distributor_endpoint . '?subscription-key=' . $this->subscriptionKey;
        if ($date != '') {
            $url .= '&date=' . $date;
        }
        $request = $this->client->get($url);
        return json_decode($request->getBody()->getContents());
    }

    private function setClient(Client $client)
    {
        $config = $client->getConfig();
        $config['headers'] = ['Authorization' => 'Bearer ' . $this->token->getAccessToken()];
        $this->client = new Client($config);
    }
}
