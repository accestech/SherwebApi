<?php


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SherwebApi\SWDistributor;
use SherwebApi\SWToken;

class SWDistributorTest extends TestCase
{
    public function testGetPayableChargesReturnRequest()
    {
        $response_data = [
            'charges' => 'charges'
        ];
        $mock = new MockHandler([
            new Response(200, [], json_encode($response_data))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $token = $this->createMock(SWToken::class);
        $token->method('getAccessToken')->willReturn('TOKEN');

        $distributor = new SWDistributor($client, $token, 'SUB_KEY');

        $this->assertTrue($distributor->getPayableCharges()->charges === $response_data['charges']);
    }
}