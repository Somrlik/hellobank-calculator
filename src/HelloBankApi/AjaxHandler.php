<?php

namespace Somrlik\HelloBankApi;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Nette\Utils\Arrays;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class AjaxHandler {

    const REQUEST_DO_BAREMS = 'getBaremy';
    const REQUEST_DO_INSURANCE = 'getPojisteni';
    const REQUEST_DO_CALCULATE = 'calculate';

    /**
     * @var HelloBankApi
     */
    private $api;

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * AjaxHandler constructor.
     *
     * @param HelloBankApi $api
     */
    public function __construct(HelloBankApi $api) {
        $this->api = $api;
        $this->responseFactory = MessageFactoryDiscovery::find();
    }

    /**
     * @param RequestInterface $request
     */
    public function handleRequest(RequestInterface $request) {
        $query = self::parseQuery($request);

        $do = Arrays::get($query, 'do', null);

        if (empty($query)) {
            return $this->getErrorResponse('You must specify the action using the do parameter.');
        }

        switch ($do) {
            case self::REQUEST_DO_CALCULATE:
                $response = $this->calculate($query);
                return $response;

            case self::REQUEST_DO_BAREMS:
                try {
                    $barems = $this->api->getBarems();
                } catch (InvalidRequestException $e) {
                    return $this->getErrorResponse('Failed to get barems from API.');
                }
                $response = $this->responseFactory->createResponse();
                $response = $response->withHeader('content-type', 'application/json; charset=utf-8');
                $response = $response->withBody(StreamFactoryDiscovery::find()->createStream(
                    json_encode($barems)
                ));
                return $response;

            case self::REQUEST_DO_INSURANCE:
                try {
                    $insurance = $this->api->getInsurance();
                } catch (InvalidRequestException $e) {
                    return $this->getErrorResponse('Failed to get insurance from API.');
                }
                $response = $this->responseFactory->createResponse();
                $response = $response->withHeader('content-type', 'application/json; charset=utf-8');
                $response = $response->withBody(StreamFactoryDiscovery::find()->createStream(
                    json_encode($insurance)
                ));
                return $response;

            default:
                return $this->getErrorResponse('You must specify a valid action.');
        }
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    private static function parseQuery(RequestInterface $request): array {
        $method = $request->getMethod();
        $output = [];
        if ($method === 'POST') {
            $params = $request->getBody()->getContents();
            parse_str($params, $output);
        } elseif ($method === 'GET') {
            $params = $request->getUri()->getQuery();
            parse_str($params, $output);
        }

        return $output;
    }

    /**
     * @param null|string $reason
     * @return ResponseInterface
     */
    private function getErrorResponse($reason = null) {
        $response = $this->responseFactory->createResponse();
        $response = $response->withHeader('content-type', 'application/json; charset=utf-8');
        $response = $response->withBody(StreamFactoryDiscovery::find()->createStream(
            json_encode([
                'success' => false,
                'reason' => $reason ?? 'An error occurred with ' . AjaxHandler::class,
            ])
        ));
        return $response;
    }

    /**
     * @param $query
     * @throws \Exception
     * @todo Implement
     */
    private function calculate($query) {
        throw new \Exception('This is not yet implemented.');
    }
}
