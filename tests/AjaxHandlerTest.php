<?php

namespace Somrlik\Tests;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Somrlik\HelloBankApi\AjaxHandler;
use Somrlik\HelloBankApi\HelloBankApi;

class AjaxHandlerTest extends TestCase {

    /** @var HelloBankApi */
    private $api;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    public function testGetBaremy() {
        $body = http_build_query([
            'do' => AjaxHandler::REQUEST_DO_BAREMS,
        ]);
        $streamFactory = StreamFactoryDiscovery::find();
        $stream = $streamFactory->createStream($body);

        $request = $this->requestFactory->createRequest('POST', '');
        $request = $request->withBody($stream);

        $response = $this->api->getAjaxHandler()->handleRequest($request);

        $request = $this->requestFactory->createRequest('GET', '');
        $request = $request->withUri($request->getUri()->withQuery($body));

        $response = $this->api->getAjaxHandler()->handleRequest($request);

        $this->assertNotFalse(true);
    }

    protected function setUp() {
        parent::setUp();
        $this->api = new HelloBankApi(
            $_ENV['PRODUCTION_MERCHANT_ID'],
            HelloBankApi::ENVIRONMENT_PRODUCTION

        );
        $this->requestFactory = MessageFactoryDiscovery::find();
    }

}
