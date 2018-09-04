<?php

namespace Somrlik\Tests;

use Http\Discovery\MessageFactoryDiscovery;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestFactoryInterface;
use Somrlik\HelloBankApi\HelloBankApi;

class LoanApplicationGeneratorTest extends TestCase {

    /** @var HelloBankApi */
    private $api;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    public function testLoanCalculator() {
        try {
            $loanCalculator = $this->api->getLoanCalculator();

            $barems = $this->api->getBarems();
            $insurance = $this->api->getInsurance();
        } catch (\Exception $e) {
            throw new \AssertionError('Failed to get loan calculator');
        }

        $baremId = key($barems);
        $insuranceId = key($insurance);

        $loanCalculator
            ->setBaremCode($baremId)
            ->setInsuranceCode($insuranceId)
            ->setTotalPrice(10000)
            ->setInstallmentCount(30);
        $resolved = $loanCalculator->resolve();

        $this->assertTrue(!! $resolved);

        $loanApplicationGenerator = $this->api->getLoanApplicationGenerator();

        $request = $loanApplicationGenerator
            ->generateRequestFromCalculator($loanCalculator, 'url_ok', 'url_ko');

        $postData = [];
        parse_str($request->getBody(), $postData);
        Assert::assertNotEmpty($postData);
        Assert::assertEquals(10000, $postData['cenaZbozi']);
        Assert::assertEquals('url_ok', $postData['url_back_ok']);
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
