<?php

namespace SherwebApi;

use GuzzleHttp\Client;

class SWDistributor
{
    /**
     * @var SWAuth
     */
    private $auth;

    /**
     * @var string
     */
    private $subscriptionKey;

    /**
     * @var Client
     */
    private $client;


    public function __construct(SWAuth $auth, string $subscriptionKey)
    {
        $this->auth = $auth;
        $this->subscriptionKey = $subscriptionKey;
        $this->client = new Client(
            [
            'base_uri' => BASE_URI,
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $auth->getAccessToken()
            ]
            ]
        );
    }

    public function getPayableCharges(string $date = '')
    {
        $url = ENDPOINT_PAYABLE_CHARGES . '?subscription-key=' . $this->subscriptionKey;
        if ($date != '') {
            $url .= '&date=' . $date;
        }
        $request = $this->client->get($url);
        return json_decode($request->getBody()->getContents());
    }
}
