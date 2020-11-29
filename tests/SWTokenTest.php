<?php
declare(strict_types=1);

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use SherwebApi\SWToken;

class SWTokenTest extends TestCase
{
    public function testGetTokenReturnToken()
    {
        $response_data = [
            'access_token' => 'TOKEN',
            'expires_in' => '30',
            'token_type' => 'TYPE'
        ];
        $mock = new MockHandler([
            new Response(200, [], json_encode($response_data))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $swToken = new SWToken($client, 'ID', 'Secret', ['scope']);

        $this->assertTrue($swToken->getAccessToken() === $response_data['access_token']);
    }
}
